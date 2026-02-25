<?
namespace App\Controllers;
use App\Models\BookSwapRequest;
use App\Models\Book;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Services\BookService;
use App\Repositories\BookRepository;
use App\Middleware\RequireRole;
use App\Models\UserRole;
use App\Repositories\BookAPI;
use App\Models\BookSwapStatus;
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
        $this->userService = new UserService($this->userRepository);
        $this->bookRepository = new BookRepository();
        $this->bookService = new BookService($this->bookRepository);
        $this->authService = new AuthService();
        $this->bookSwapRequestRepository = new BookSwapRequestRepository();
        $this->bookRequestService = new BookRequestService($this->bookSwapRequestRepository);
        $this->mockPostNlService = new MockPostNlService();
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function requestBookSwap($vars = []){

            header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                throw new \Exception('Invalid JSON payload');
            }

            $bookId      = $data['bookId'] ?? null;
            $ownerId     = $data['ownerId'] ?? null;
            $requesterId = $data['requesterId'] ?? null;
            $street      = $data['street'] ?? null;
            $post_code   = $data['zip'] ?? null;
            $state       = $data['state'] ?? null;
            $country     = $data['country'] ?? null;

            if (!$bookId || !$ownerId || !$requesterId || !$street || !$post_code || !$state || !$country) {
                throw new \Exception('All fields are required');
            }

            $owner = $this->userService->getUserById($ownerId);
            $requester = $this->userService->getUserById($requesterId);
            $book= $this->bookService->getBookById($bookId);

            if (!$owner || !$requester || !$book) {
                throw new \Exception('Invalid book, owner, or requester');
            }

            if ($book->shared_by->id !== $owner->id) {
                throw new \Exception('Owner does not match the book owner');
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

            header('Content-Type: application/json');
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Book request created successfully'. $data['bookId'] . "-" . $data['ownerId'] . "-" . $data['requesterId'],
                'redirectUrl' => '/checkout?requestId=' . $requestId
        ]);

        } catch (\Throwable $e) {
            header('Content-Type: application/json');
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function viewBookMyRequests($vars = []){
        $userId = $vars['id'] ?? null;
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
        
        try{
        if ((int)$userId !== $_SESSION['loggedInUser']->id) {
            $this->authService->logout('Unauthorized access to book requests.');
        }
        $user = $this->userService->getUserById($userId);
        $bookRequests = $this->bookRequestService->getRequestsByUserId($user, $includeClosed, false, $statusFilter);
        $this->view('BookRequest/MyRequests', ['message' => "My Book Requests", 'title' => 'My Requests Page', 'user' => $user, 'bookRequests' => $bookRequests] );
        }catch(\Exception $e){
            http_response_code(400);
            echo "Error: " . $e->getMessage();

        }
    }
     #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getMyBookRequests($vars = []){
        header('Content-Type: application/json');
        $userId = $_GET['id'] ?? null;
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
        
        try{
        if ((int)$userId !== $_SESSION['loggedInUser']->id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized access to book requests.'. $_SESSION['loggedInUser']->id . "-" . $userId]);
            return;
        }
        $user = $this->userService->getUserById($userId);
        $bookRequests = $this->bookRequestService->getRequestsByUserId($user, $includeClosed, false, $statusFilter);
        echo json_encode([
            'success' => true,
            'bookRequests' => $bookRequests
        ]);
        }catch(\Exception $e){
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function viewRequesteeDetails($vars = [])
    {
        $requestId = $vars['requestId'] ?? null;
        $user = $_SESSION['loggedInUser'];
        if ($requestId === null) {
            die("Request ID is required.");
        }
        $request = $this->bookRequestService->getRequestByUserIdAndRequestId($user, (int)$requestId, true, false);
        if ($request->requester->id !== $user->id) {
            $this->authService->logout('Unauthorized access to requestee details.');
        }
        $book = $request->book;
        //require_once '/app/Views/BookRequest/RequesteeDetailsModal.php';
        require_once '/app/Views/Book/BookDetailsModal.php';
    }

     #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function viewMySwapRequests($vars = []){
        $userId = $vars['id'] ?? null;
        
        
        if ((int)$userId !== $_SESSION['loggedInUser']->id) {
            $this->authService->logout('Unauthorized access to book requests.');
        }
        $user = $this->userService->getUserById($userId);
        $bookRequests = $this->bookRequestService->getRequestsByUserId($user, false, true, null);
        $this->view('BookRequest/MyRequests', ['message' => "My Book Requests", 'title' => 'My Requests Page', 'user' => $user, 'bookRequests' => $bookRequests] );
    }
     #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function viewMyListings($vars = []){
        $userId = $vars['id'] ?? null;
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
        try{
        
        if ((int)$userId !== $_SESSION['loggedInUser']->id) {
            $this->authService->logout('Unauthorized access to book requests.');
        }
        $user = $this->userService->getUserById($userId);
        //throw new \Exception("Test exception for empty requests.". $user->id . "-" . $user->fname);
        $bookRequests = $this->bookRequestService->getRequestsByUserId($user, $includeClosed, true, $statusFilter);
        $this->view('BookRequest/MyListings', ['message' => "My Book Listings", 'title' => 'My Listings Page', 'user' => $user, 'bookRequests' => $bookRequests] );
        }catch(\Exception $e){
            http_response_code(400);
            echo "Error: " . $e->getMessage();

        }
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getMyListings($vars = []){
        header('Content-Type: application/json');
        $userId = $_GET['id'] ?? null;
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
        try{
        
        if ((int)$userId !== $_SESSION['loggedInUser']->id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized access to book listings.']);
            return;
        }
        $user = $this->userService->getUserById($userId);
        
        $bookRequests = $this->bookRequestService->getRequestsByUserId($user, $includeClosed, true, $statusFilter);
        http_response_code(200);
        echo json_encode(['message' => "My Book Listings", 'title' => 'My Listings Page', 'user' => $user, 'bookRequests' => $bookRequests]);
        }catch(\Exception $e){
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function viewMyRequest($vars = []){
        try{
            $requestId = $_GET['requestId'] ?? null;
            $requesterId = $_GET['requesterId'] ?? null;
            $requesterToken = $_GET['requesterToken'] ?? null;

            if (!$requestId || !$requesterId || !$requesterToken) {
                throw new \Exception('Invalid request parameters.');
            }
    
            $bookRequest = $this->bookRequestService->getRequestById((int)$requestId);

            if (!$bookRequest) {
                throw new \Exception('Book request not found.');
            }

            if ($bookRequest->requester->id !== (int)$requesterId || $bookRequest->requester_action_token !== $requesterToken) {
                throw new \Exception('Unauthorized access to the book request.');
            }
            $this->view('BookRequest/MyRequests', ['message' => "My Book Requests", 'title' => 'My Requests Page', 'bookRequests' => [$bookRequest]] );

        }catch(\Exception $e){
            http_response_code(400);
            echo "Error: " . $e->getMessage();

        }
    }
    public function updateRequestStatus($vars = []){
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                throw new \Exception('Invalid JSON');
            }

            $requestId = $data['requestId'] ?? null;
            $newStatus = BookSwapStatus::from($data['status'] ?? null) ?? null;

            if (!$requestId || !$newStatus) {
                throw new \Exception('Request ID and new status are required');
            }

            $bookRequest = $this->bookRequestService->getRequestById((int)$requestId);

            if (!$bookRequest) {
                throw new \Exception('Book request not found');
            }
            

            $bookRequest->status = $newStatus;
            if ($bookRequest->status === BookSwapStatus::TAKENDOWN && $bookRequest->owner->id === $_SESSION['loggedInUser']->id) {
                $this->bookService->deactivateBookPost($bookRequest->book->id);
            }
            elseif ($bookRequest->status === BookSwapStatus::TAKENDOWN &&$bookRequest->owner->id !== $_SESSION['loggedInUser']->id) {
                throw new \Exception('Unauthorized to take down the book post.');
            }
            if ($bookRequest->status === BookSwapStatus::COMPLETED) {
                $bookRequest->closed_at = new \DateTime();
            }

            $this->bookRequestService->updateRequest($bookRequest);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Book request status updated successfully'
            ]);

        } catch (\Throwable $e) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getBookSwapStatusses($vars = []){
        header('Content-Type: application/json');
        try {
            $statuses = array_map(fn($status) => $status->value, BookSwapStatus::cases());
            echo json_encode($statuses);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


}