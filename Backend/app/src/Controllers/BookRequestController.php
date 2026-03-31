<?php
namespace App\Controllers;
use App\Framework\Controller;
use App\Models\BookSwapRequest;
use App\Models\Book;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Services\BookService;
use App\Repositories\BookRepository;
use App\Middleware\RequireRole;
use App\Middleware\JWTMiddleware;
use App\Models\Enums\UserRole;
use App\Repositories\BookAPI;
use App\Models\Enums\BookSwapStatus;
use App\Services\AuthService;
use App\Repositories\BookSwapRequestRepository;
use App\Services\BookRequestService;
use App\Services\MockPostNlService;

class BookRequestController extends Controller{

     private UserService $userService;
     private UserRepository $userRepository;
    private BookService $bookService;
    private BookRepository $bookRepository;
    private AuthService $authService;
    private BookRequestService $bookRequestService;
    private BookSwapRequestRepository $bookSwapRequestRepository;
    private MockPostNlService $mockPostNlService;
    
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->bookRepository = new BookRepository();
        $this->bookService = new BookService($this->bookRepository);
        $this->authService = new AuthService();
        $this->bookSwapRequestRepository = new BookSwapRequestRepository();
        $this->bookRequestService = new BookRequestService();
        $this->mockPostNlService = new MockPostNlService();
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function requestBookSwap($vars = []) 
    {
        try {
            $data = $this->getPostData();

            if (!$data) {
                $this->sendErrorResponse(['error' => 'Invalid JSON'], 400);
                return;
            }

            $bookId      = $data['bookId'] ?? null;
            $ownerId     = $data['ownerId'] ?? null;
            $requesterId = $data['requesterId'] ?? null;
            $street      = $data['street'] ?? null;
            $post_code   = $data['zip'] ?? null;
            $state       = $data['state'] ?? null;
            $country     = $data['country'] ?? null;

            if (!$bookId || !$ownerId || !$requesterId || !$street || !$post_code || !$state || !$country) {
                $this->sendErrorResponse(['error' => 'All fields are required'], 400);
                return;
            }

            $owner = $this->userService->getUserById($ownerId);
            $requester = $this->userService->getUserById($requesterId);
            $book= $this->bookService->getBookById($bookId);

            if (!$owner || !$requester || !$book) {
                $this->sendErrorResponse(['error' => 'Invalid book, owner, or requester'], 400);
                return;
            }

            if ($book->shared_by->id !== $owner->id) {
                $this->sendErrorResponse(['error' => 'Owner does not match the book owner'], 400);
                return;
            }

            $ownerActionToken= $this->authService->generateActionToken();
            $requesterActionToken = $this->authService->generateActionToken();

            $bookSwapRequest = new BookSwapRequest()->map(
                $requester,
                $owner,
                $book,
                $ownerActionToken,
                $requesterActionToken,
                $street,
                $post_code,
                $state,
                $country
            );
            $bookSwapRequest->shipping_cost = (float)$this->mockPostNlService->calculateShippingCost($bookSwapRequest->book);
            $requestId = (int)$this->bookRequestService->getRequestIdByBookIdAndOwnerId($book, $owner);
            $bookSwapRequest->id = $requestId;
            $this->bookRequestService->updateRequest($bookSwapRequest);
            

            //$_SESSION['currentBookRequest'] = $bookSwapRequest;
            $_SESSION['currentBookRequestId'] = $bookSwapRequest->id;

            $this->sendSuccessResponse([
                'success' => true,
                'message' => 'Book request created successfully'. $data['bookId'] . "-" . $data['ownerId'] . "-" . $data['requesterId'],
                'redirectUrl' => '/checkout?requestId=' . $requestId
            ], 201);
            } catch (\Throwable $e) {
            $this->sendErrorResponse([
                'success' => false,
                'message' => 'An error occurred while creating the book request: ' . $e->getMessage()
            ], 500);
        }
    }
    
     #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getMyBookRequests($vars = []){
        try {
            $userId = JWTMiddleware::getUserIdFromToken();
            $filterStatus = $_GET['status'] ?? 'all';
            switch($filterStatus){
                case 'inProgress':
                    $includeClosed = false;
                    $statusFilter = BookSwapStatus::ALL;
                    break;
                case 'completed':
                    $includeClosed = true;
                    $statusFilter = BookSwapStatus::COMPLETED;
                    break;
                case 'all':
                    $includeClosed = true;
                    $statusFilter = null;
                    break;
                default:
                    $includeClosed = true;
                    $statusFilter = null;
                    break;
            }
            
            $user = $this->userService->getUserById($userId);
            $bookRequests = $this->bookRequestService->getRequestsByUserId($user, $includeClosed, false, $statusFilter);
            $this->sendSuccessResponse(['success' => true, 'bookRequests' => $bookRequests], 200);
        } catch(\Exception $e){
            $this->sendErrorResponse(['success' => false, 'error' => $e->getMessage()], 401);
        }
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getMyListings($vars = []){
        header('Content-Type: application/json');
        try {
            $userId = JWTMiddleware::getUserIdFromToken();
            $filterStatus = $_GET['status'] ?? 'all';
            switch($filterStatus){
                case 'listed':
                    $includeClosed = false;
                    $statusFilter = BookSwapStatus::ALL;
                    break;
                case 'completed':
                    $includeClosed = true;
                    $statusFilter = BookSwapStatus::COMPLETED;
                    break;
                case 'takenDown':
                    $includeClosed = true;
                    $statusFilter = BookSwapStatus::TAKENDOWN;
                    break;
                case 'all':
                    $includeClosed = true;
                    $statusFilter = null;
                default:
                    $includeClosed = true;
                    $statusFilter = null;
                    break;
            }
            
            $user = $this->userService->getUserById($userId);
            
            $bookRequests = $this->bookRequestService->getRequestsByUserId($user, $includeClosed, true, $statusFilter);
            $this->sendSuccessResponse(['success' => true, 'bookRequests' => $bookRequests], 200);
        } catch(\Exception $e){
            $this->sendErrorResponse(['success' => false, 'error' => $e->getMessage()], 401);
        }
    }
    
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function updateRequestStatus($vars = []){

        try {
            $userId = JWTMiddleware::getUserIdFromToken();
            $data = $this->getPostData();

             if (!$data) {
                $this->sendErrorResponse(['success' => false, 'error' => 'Invalid JSON'], 400);
                return;
            }
            $requestId = $data['requestId'] ?? null;
            $newStatus = BookSwapStatus::from($data['status'] ?? null) ?? null;

            if (!$requestId || !$newStatus) {
                $this->sendErrorResponse(['success' => false, 'error' => 'Request ID and valid status are required.'], 400);
            }

            $bookRequest = $this->bookRequestService->getRequestById((int)$requestId);

            if (!$bookRequest) {
                throw new \Exception('Book request not found');
            }
            

            $bookRequest->status = $newStatus;
            if ($bookRequest->status === BookSwapStatus::TAKENDOWN && $bookRequest->owner->id === $userId) {
                $this->bookService->deactivateBookPost($bookRequest->book->id);
            }
            elseif ($bookRequest->status === BookSwapStatus::TAKENDOWN &&$bookRequest->owner->id !== $userId) {
                $this->sendErrorResponse(['success' => false, 'error' => 'Unauthorized to take down the book post.'], 403);
            }
            if ($bookRequest->status === BookSwapStatus::COMPLETED) {
                $bookRequest->closed_at = new \DateTime();
            }

            $this->bookRequestService->updateRequest($bookRequest);

            $this->sendSuccessResponse(['success' => true, 'message' => 'Book request status updated successfully'], 200);
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'error' => 'An error occurred while updating the book request status: ' . $e->getMessage()], 500);
        }
    }
    public function getBookSwapStatusses($vars = []){
        try {
            $statuses = array_map(fn($status) => $status->value, BookSwapStatus::cases());
            $this->sendSuccessResponse(['success' => true, 'statuses' => $statuses], 200);
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'error' => 'An error occurred while fetching book swap statuses: ' . $e->getMessage()], 500);
        }
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getRequestById($vars = []){
        try {
            $requestId = $_GET['requestId'] ?? null;
            if (!$requestId) {
                $this->sendErrorResponse(['success' => false, 'error' => 'Request ID is required'], 400);
                return;
            }
            $bookRequest = $this->bookRequestService->getRequestById((int)$requestId);
            if (!$bookRequest) {
                $this->sendErrorResponse(['success' => false, 'error' => 'Book request not found'], 404);
                return;
            }

            if ($bookRequest->requester->id !== JWTMiddleware::getUserIdFromToken()) {
                $this->sendErrorResponse(['success' => false, 'error' => 'Unauthorized access to the book request.'], 403);
                return;
            }
            $this->sendSuccessResponse(['success' => true, 'bookRequest' => $bookRequest], 200);
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'error' => 'An error occurred while fetching the book request: ' . $e->getMessage()], 500);
        }
    }


}
           