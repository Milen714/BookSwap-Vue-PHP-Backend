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
use App\Exceptions\ApplicationException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;

/**
 * ChatController
 * 
 * Manages direct messaging between users including retrieving chat history,
 * sending messages, and managing chat partner lists via Redis pub/sub.
 */
class ChatController extends Controller
{
    private UserService $userService;
    private UserRepository $userRepository;
    private RedisClient $redisClient;
    private IDirectMessageRepository $directMessageRepository;
    private DirectMessageService $directMessageService;

    /**
     * Initialize chat services and Redis client for real-time messaging
     */
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

    /**
     * Retrieve chat message history between two users
     * 
     * @param array $vars URL parameters
     * @return void
     */
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getChatMessages($vars = [])
    {
        header('Content-Type: application/json');
        try {
            $senderId = JWTMiddleware::getUserIdFromToken();
            
            // Security check to ensure users can only access their own messages
            $requestSenderId = $_GET['senderId'] ?? null;
            if ($senderId !== (int)$requestSenderId) {
                $this->sendErrorResponse('You do not have permission to access this resource.', 403);
                return;
            }
            
            $recipientId = $_GET['recipientId'] ?? null;
        
            $messages = $this->directMessageService->getDirectMessages($senderId, $recipientId);
            
            echo json_encode(['success' => true, 'messages' => $messages]);
        } catch (ApplicationException $e) {
            $this->sendErrorResponse($e->getMessage(), $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse('Failed to retrieve messages', 500);
        }
    }
    
    /**
     * Send a direct message to another user via Redis pub/sub
     * 
     * @param array $vars URL parameters
     * @return void
     */
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function sendDirectMessage($vars = [])
    {
        $data = $this->getPostData();
        $recipientId = $data['recipientId'] ?? null;
        $message = $data['message'] ?? null;
        if (!$recipientId || !$message) {
            $this->sendErrorResponse('Recipient ID and message are required', 400);
            return;
        }
        try {
            $senderId = $this->validateSender((int)($data['senderId'] ?? 0));
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
            $this->sendSuccessResponse(['success' => true], 200);
            exit;
        } catch (ApplicationException $e) {
            $this->sendErrorResponse($e->getMessage(), $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse('Failed to send message', 500);
        }
    }
    /**
     * Validate that the sender matches the authenticated user
     * 
     * @param int $senderId The sender ID to validate
     * @return int The validated sender ID
     */
    private function validateSender(int $senderId): int {
        $userId = JWTMiddleware::getUserIdFromToken();
        if ($senderId <= 0) {
            throw new UnauthorizedException('Unauthorized');
        }
        if ($userId !== $senderId) {
            throw new ForbiddenException('You do not have permission to access this resource.');
        }
        return $userId;
    }
    /**
     * Get list of all users the current user has messaged with
     * 
     * @param array $vars URL parameters
     * @return void
     */
    public function getChatPartners($vars = []) {
        try {
            $userId = JWTMiddleware::getUserIdFromToken();
            $partners = $this->directMessageService->getMyChatPartners($userId);
            $this->sendSuccessResponse(['success' => true, 'partners' => $partners], 200);
        } catch (ApplicationException $e) {
            $this->sendErrorResponse($e->getMessage(), $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse('Failed to retrieve chat partners', 500);
        }
    }
}