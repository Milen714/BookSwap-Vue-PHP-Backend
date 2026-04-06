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
use App\Exceptions\ApplicationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;

/**
 * BookRequestController
 * 
 * Handles all book swap request operations including creation, 
 * status updates, and retrieval of requests and listings.
 */
class BookRequestController extends Controller{

     private UserService $userService;
     private UserRepository $userRepository;
    private BookService $bookService;
    private BookRepository $bookRepository;
    private AuthService $authService;
    private BookRequestService $bookRequestService;
    private BookSwapRequestRepository $bookSwapRequestRepository;
    private MockPostNlService $mockPostNlService;
    
    /**
     * Initialize all required services and repositories
     */
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

    /**
     * Create a new book swap request between a requester and book owner
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
            } catch (ApplicationException $e) {
            $this->sendErrorResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse([
                'success' => false,
                'message' => 'An error occurred while creating the book request.'
            ], 500);
        }
    }
    
    /**
     * Retrieve book swap requests where the user is the requester
     * Supports filtering by status: inProgress, completed, all
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
        } catch(ApplicationException $e){
            $this->sendErrorResponse(['success' => false, 'error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch(\Throwable $e){
            $this->sendErrorResponse(['success' => false, 'error' => 'Failed to fetch your book requests.'], 500);
        }
    }

    /**
     * Retrieve book listings where the user is the owner
     * Supports filtering by status: listed, completed, takenDown, all
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
        } catch(ApplicationException $e){
            $this->sendErrorResponse(['success' => false, 'error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch(\Throwable $e){
            $this->sendErrorResponse(['success' => false, 'error' => 'Failed to fetch your listings.'], 500);
        }
    }
    
    /**
     * Update the status of a book swap request
     * Awards owner 1 swap token upon completion
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
            $newStatusRaw = $data['status'] ?? null;

            try {
                $newStatus = $newStatusRaw ? BookSwapStatus::from($newStatusRaw) : null;
            } catch (\ValueError $e) {
                throw new ValidationException('Invalid status provided.');
            }

            if (!$requestId || !$newStatus) {
                $this->sendErrorResponse(['success' => false, 'error' => 'Request ID and valid status are required.'], 400);
                return;
            }

            $bookRequest = $this->bookRequestService->getRequestById((int)$requestId);

            if (!$bookRequest) {
                throw new NotFoundException('Book request not found');
            }
            

            $bookRequest->status = $newStatus;
            if ($bookRequest->status === BookSwapStatus::TAKENDOWN && $bookRequest->owner->id === $userId) {
                $this->bookService->deactivateBookPost($bookRequest->book->id);
            }
            elseif ($bookRequest->status === BookSwapStatus::TAKENDOWN &&$bookRequest->owner->id !== $userId) {
                $this->sendErrorResponse(['success' => false, 'error' => 'Unauthorized to take down the book post.'], 403);
                return;
            }
            if ($bookRequest->status === BookSwapStatus::COMPLETED) {
                $bookRequest->closed_at = new \DateTime();
                $this->userService->addSwapTokens($bookRequest->owner->id, 1);
            }

            $this->bookRequestService->updateRequest($bookRequest);

            $this->sendSuccessResponse(['success' => true, 'message' => 'Book request status updated successfully'], 200);
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'error' => 'An error occurred while updating the book request status.'], 500);
        }
    }
    /**
     * Get all available book swap statuses
     * 
     * @param array $vars URL parameters
     * @return void
     */
    public function getBookSwapStatusses($vars = []) {
        try {
            $statuses = array_map(fn($status) => $status->value, BookSwapStatus::cases());
            $this->sendSuccessResponse(['success' => true, 'statuses' => $statuses], 200);
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'error' => 'An error occurred while fetching book swap statuses.'], 500);
        }
    }
    /**
     * Retrieve a specific book request by ID
     * Only accessible by the requester
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'error' => 'An error occurred while fetching the book request.'], 500);
        }
    }


}
           