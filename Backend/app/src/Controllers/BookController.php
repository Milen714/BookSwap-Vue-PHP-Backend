<?php

namespace App\Controllers;
use App\Framework\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Models\Enums\BookCondition;
use App\Models\Book;
use App\Services\BookService;
use App\Repositories\BookRepository;
use App\Middleware\RequireRole;
use App\Models\Enums\UserRole;
use App\Repositories\BookAPI;
use App\Middleware\JWTMiddleware;
use Predis\Client as RedisClient;

class BookController extends Controller
{
    private UserService $userService;
     private UserRepository $userRepository;
    private BookService $bookService;
    private BookRepository $bookRepository;
    private RedisClient $redisClient;
    
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->bookRepository = new BookRepository();
        $this->bookService = new BookService($this->bookRepository);
        $this->redisClient = new RedisClient([
            'scheme' => getenv('REDIS_SCHEME'),
            'host'   => getenv('REDIS_HOST'),
            'port'   => (int)(getenv('REDIS_PORT'))
        ]);
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function fetchBookPreview($vars = [])
    {
        $data = $this->getPostData();
        $isbn = $data['isbn'] ?? null;
        if (!$isbn) {
            $this->sendErrorResponse(['error' => 'ISBN required'], 400);
            return;
        }
        
        try {
            $book = $this->bookService->getBookByISBNFromGoogleApi($isbn);
            $this->sendSuccessResponse(['success' => true, 'book' => $book], 200);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function addBookPost($vars = [])
    {
        
        try{
            // Get JSON data from request body
            $data = $this->getPostData();
            
            $isbn = $data['isbn'] ?? null;
            $condition = $data['condition'] ?? 'Unknown';
            $userReview = $data['userReview'] ?? null;
            $userId = JWTMiddleware::getUserIdFromToken();
            
            if (!$isbn) {
                $this->sendErrorResponse(['error' => 'ISBN is required'], 400);
                return;
            }
            
            $book = $this->bookService->getBookByISBNFromGoogleApi($isbn);
            $book->condition = BookCondition::from($condition);
            $book->owner_review = $userReview;
            $sharedBy = $this->userService->getUserById($userId);
            $book->shared_by = $sharedBy;

            $numberOfListings = $this->userService->numberOfListedBooks($sharedBy->id);
            
            $this->bookService->saveBook($book);
            
            
            if ($numberOfListings === 0) {
                $this->userService->addSwapTokens($sharedBy->id, 1);
            }

            $this->sendSuccessResponse(['success' => true, 'message' => 'Book added successfully'], 200);
        }
        catch(\Exception $e){
            $this->sendErrorResponse(['error' => $e->getMessage()], 400);
        }
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function scanBook($vars = [])
{
    $isbn = $vars['isbn'] ?? null;
    if (!$isbn) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'ISBN required']);
        return;
    }
    
    try {
        $book = $this->bookService->getBookByISBNFromGoogleApi($isbn);
        $this->sendSuccessResponse($book, 200);
    } catch (\Exception $e) {
        $this->sendErrorResponse(['error' => $e->getMessage()], 400);
    }
}
    public function viewBookDetails($vars = [])
    {
        $bookId = $vars['id'] ?? null;
        if ($bookId === null) {
            die("Book ID is required.");
        }
        $book = $this->bookService->getBookById((int)$bookId);
        if ($book === null) {
            die("Book not found.");
        }
        
        echo require_once '/app/Views/Book/BookDetailsModal.php';
    }
    public function getBookDetails($vars = [])
    {
        try {
        $bookId = $_GET['id'] ?? null;
        if ($bookId === null) {
            $this->sendErrorResponse(['error' => 'Book ID is required.'], 400);
            return;

        }
        $book = $this->bookService->getBookById((int)$bookId);
        if ($book === null) {
            $this->sendErrorResponse(['error' => 'Book not found.'], 404);
            return;
        }
        
        $this->sendSuccessResponse($book, 200);
        } catch (\Exception $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function bookPostConfirmation($vars = [])
    {
        echo require_once '/app/Views/Book/BookPostConfimation.php';
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function takeDownBookPost($vars = [])
    {
        $bookId = $vars['id'] ?? null;
        if ($bookId === null) {
            die("Book ID is required.");
        }
        $this->bookService->deactivateBookPost((int)$bookId);
        header("Location: /myListings/" . $_SESSION['loggedInUser']->id);
        exit();
    }
    public function searchBooks($vars = [])
    {
        $genreFilter = $_GET['genre'] ?? null;
        $generalFilter = $_GET['search'] ?? null;

        $books = $this->bookService->getAllBooks($genreFilter, $generalFilter);

        require_once '/app/Views/Book/BooksSection.php';
    }
    public function getAllBooks($vars = [])
    {
        $genreFilter = $_GET['genre'] ?? null;
        $generalFilter = $_GET['search'] ?? null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : null;

        try {
            $books = $this->bookService->getAllBooks($genreFilter, $generalFilter, $page);
            $hasNextPage = count($books) > BookService::ITEMS_PER_PAGE - 1;

            // Remove the extra book used to check for next page
            if ($hasNextPage) {
                array_pop($books); 
            }
            
            $this->redisClient->publish('book-search', json_encode(['message' => 'Books searched', 'test' => getenv('REDIS_HOST')]));
            
            $this->sendSuccessResponse(['success' => true, 'books' => $books, 'hasNextPage' => $hasNextPage, 'currentPage' => $page], 200);
        } catch (\Exception $e) {
            $this->sendErrorResponse(['success' => false, 'message' => 'Error fetching books: ' . $e->getMessage()], 500);
        }
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getAllGenres($vars = [])
    {
        header('Content-Type: application/json');
        try {
            $genres = $this->bookService->getBooksGenres();
            $this->sendSuccessResponse(['success' => true, 'genres' => $genres], 200);
        } catch (\Exception $e) {
            $this->sendErrorResponse(['success' => false, 'message' => 'Error fetching genres: ' . $e->getMessage()], 500);
        }
    }
}