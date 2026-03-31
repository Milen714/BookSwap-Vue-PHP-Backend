<?php
namespace App\Controllers;
use App\Framework\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Middleware\RequireRole;
use App\Middleware\JWTMiddleware;
use App\Models\Enums\UserRole;
use Predis\Client as RedisClient;
use App\Repositories\Interfaces\IDirectMessageRepository;
use App\Repositories\DirectMessageRepository;
use App\Services\DirectMessageService;
use App\Models\DirectMessage;

class ChatController extends Controller
{
    private UserService $userService;
    private UserRepository $userRepository;
    private RedisClient $redisClient;
    private IDirectMessageRepository $directMessageRepository;
    private DirectMessageService $directMessageService;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->redisClient = new RedisClient([
            'scheme' => getenv('REDIS_SCHEME') ?: 'tcp',
            'host'   => getenv('REDIS_HOST') ?: 'redis',
            'port'   => (int)(getenv('REDIS_PORT') ?: 6379)
        ]);
        $this->directMessageRepository = new DirectMessageRepository();
        $this->directMessageService = new DirectMessageService($this->directMessageRepository);
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getChatMessages($vars = [])
    {
        header('Content-Type: application/json');
        try {
            $senderId = JWTMiddleware::getUserIdFromToken();
            
            // Security check to ensure users can only access their own messages
            $requestSenderId = $_GET['senderId'] ?? null;
            if ($senderId !== (int)$requestSenderId) {
                http_response_code(403);
                echo json_encode(['error' => 'You do not have permission to access this resource.']);
                return;
            }
            
            $recipientId = $_GET['recipientId'] ?? null;
        
            $messages = $this->directMessageService->getDirectMessages($senderId, $recipientId);
            
            echo json_encode(['success' => true, 'messages' => $messages]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve messages']);
        }
    }
    
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function sendDirectMessage($vars = [])
    {
        $data = $this->getPostData();
        $recipientId = $data['recipientId'] ?? null;
        $senderId = $this->validateSender((int)($data['senderId'] ?? 0));
        $message = $data['message'] ?? null;
        if (!$recipientId || !$message) {
            $this->sendErrorResponse('Recipient ID and message are required', 400);
            return;
        }
        try {
            $directMessage = DirectMessage::fromArray($data, $senderId);
            
            // Save to database
            $this->directMessageService->saveDirectMessage($directMessage);
            
            // Get the current timestamp (same as what DB sets)
            $createdAt = date('Y-m-d H:i:s');
            
            // Publish to Redis for real-time delivery
            $this->redisClient->publish('chat-channel', json_encode([
                'senderId' =>  $senderId,
                'recipientId' => $recipientId,
                'message' => $directMessage->message,
                'created_at' => $createdAt,
            ]));
            // Return success response
            echo json_encode(['success' => true]);
            exit;
        } catch (\Exception $e) {
            $this->sendErrorResponse('Failed to send message: ' . $e->getMessage(), 500);
        }
    }
    private function validateSender(int $senderId): int {
        try {
            $userId = JWTMiddleware::getUserIdFromToken();
            if ($userId !== $senderId) {
                throw new \Exception("You do not have permission to access this resource.");
            }
            return $userId;
        } catch (\Exception $e) {
            $this->sendErrorResponse('Unauthorized', 401);
            exit();
        }
    }
    public function getChatPartners($vars = []) {
        try {
            $userId = JWTMiddleware::getUserIdFromToken();
            $partners = $this->directMessageService->getMyChatPartners($userId);
            echo json_encode(['success' => true, 'partners' => $partners]);
        } catch (\Exception $e) {
            $this->sendErrorResponse('Failed to retrieve chat partners', 500);
        }
    }
}