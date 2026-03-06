<?php
namespace App\Controllers;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Middleware\RequireRole;
use App\Models\UserRole;
use Predis\Client as RedisClient;
use App\Repositories\Interfaces\IDirectMessageRepository;
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
            'scheme' => getenv('REDIS_SCHEME'),
            'host'   => getenv('REDIS_HOST'),
            'port'   => (int)(getenv('REDIS_PORT'))
        ]);
        $this->directMessageService = new DirectMessageService($this->directMessageRepository);
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getChatMessages($vars = [])
    {
        header('Content-Type: application/json');
        $senderId = $_SESSION['loggedInUser']->id ?? null;
        $recipientId = $_GET['recipientId'] ?? null;
        if (!$senderId || !$recipientId) {
            echo json_encode(['error' => 'Both user IDs are required']);
            return;
        }
        $messages = $this->directMessageService->getDirectMessages($senderId, $recipientId);
        try {
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
        $message = $data['message'] ?? null;
        if (!$recipientId || !$message) {
            http_response_code(400);
            echo json_encode(['error' => 'Recipient ID and message are required']);
            return;
        }
        try {
            $directMessage = new DirectMessage();
            $directMessage->fromArray($data, $_SESSION['loggedInUser']->id);
            // Save to database
            $this->directMessageService->saveDirectMessage($directMessage);
            // Publish to Redis for real-time delivery
            $this->redisClient->publish('direct_messages', json_encode([
                'senderId' => $_SESSION['loggedInUser']->id,
                'recipientId' => $recipientId,
                'message' => $message
            ]));
            // Return success response
            echo json_encode(['success' => true]);
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send message: ' . $e->getMessage()]);
        }
    }
}