<?php
namespace App\Controllers;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Middleware\RequireRole;
use App\Models\UserRole;
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
        $this->userService = new UserService($this->userRepository);
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
        $senderId= null;
        // Security check to ensure users can only access their own messages
         if ($_SESSION['loggedInUser']->id !== (int)$_GET['senderId']) {
            http_response_code(403);
            echo json_encode(['error' => 'You do not have permission to access this resource.']);
            return;
        }
        $senderId = (int)$_GET['senderId'];
        header('Content-Type: application/json');
        try {
        
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
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $recipientId = $data['recipientId'] ?? null;
        $senderId = $this->validateSender((int)($data['senderId'] ?? 0));
        $message = $data['message'] ?? null;
        if (!$recipientId || !$message) {
            http_response_code(400);
            echo json_encode(['error' => 'Recipient ID and message are required']);
            return;
        }
        try {
            $directMessage = DirectMessage::fromArray($data, $senderId);
            
            // Save to database
            $this->directMessageService->saveDirectMessage($directMessage);
            // Publish to Redis for real-time delivery
            $this->redisClient->publish('chat-channel', json_encode([
                'senderId' =>  $senderId,
                'recipientId' => $recipientId,
                'message' => $directMessage->message,
            ]));
            // Return success response
            echo json_encode(['success' => true]);
            exit;
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Failed to send message: ' . $e->getMessage()]);
        }
    }
    private function validateSender(int $senderId): int {
        if(!$_SESSION['loggedInUser'] || $_SESSION['loggedInUser']->id !== $senderId) {
            throw new \Exception("You do not have permission to access this resource.");
             http_response_code(403);
            exit();
        }
        return $_SESSION['loggedInUser']->id;
    }
}