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
use App\Exceptions\ApplicationException;
use App\Clients\OllamaClient;

/**
 * BookController
 * 
 * Manages book catalog operations including listing, searching, previewing books,
 * posting new books for swap, and retrieving user book collections.
 */
class BookController extends Controller
{
    private UserService $userService;
     private UserRepository $userRepository;
    private BookService $bookService;
    private BookRepository $bookRepository;
    private RedisClient $redisClient;
    private OllamaClient $ollamaClient;
    
    /**
     * Initialize book services and repository dependencies
     */
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->bookRepository = new BookRepository();
        $this->ollamaClient = new OllamaClient();
        $this->bookService = new BookService($this->bookRepository, $this->ollamaClient);
        $this->redisClient = new RedisClient([
            'scheme' => getenv('REDIS_SCHEME'),
            'host'   => getenv('REDIS_HOST'),
            'port'   => (int)(getenv('REDIS_PORT'))
        ]);
    }

    /**
     * Fetch book preview data from Google Books API using ISBN
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['error' => 'Failed to fetch book preview.'], 500);
        }
    }
    /**
     * Add a book post to the platform for swap. Awards first listing token to user.
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
        catch(ApplicationException $e){
            $this->sendErrorResponse(['error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['error' => 'Failed to add book post.'], 500);
        }
    }
    /**
     * Scan book ISBN via barcode and retrieve book data
     * 
     * @param array $vars URL parameters
     * @return void
     */
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function scanBook($vars = [])
{
    $isbn = $vars['isbn'] ?? null;
    if (!$isbn) {
        $this->sendErrorResponse(['error' => 'ISBN is required'], 400);
        return;
    }
    
    try {
        $book = $this->bookService->getBookByISBNFromGoogleApi($isbn);
        $this->sendSuccessResponse(['success' => true, 'book' => $book], 200);
    } catch (ApplicationException $e) {
        $this->sendErrorResponse(['error' => $e->getMessage()], $e->getHttpStatusCode());
    } catch (\Throwable $e) {
        $this->sendErrorResponse(['error' => 'Failed to scan book.'], 500);
    }
}
    
    /**
     * Get book details via JSON API response
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
        
        $this->sendSuccessResponse(['success' => true, 'book' => $book], 200);
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['error' => 'Failed to fetch book details.'], 500);
        }
    }
    
    /**
     * Display book post confirmation page
     * 
     * @param array $vars URL parameters
     * @return void
     */
    
    
    /**
     * Retrieve paginated list of all books with filtering options
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => 'Error fetching books.'], 500);
        }
    }

    /**
     * Retrieve list of all book genres in the catalog
     * 
     * @param array $vars URL parameters
     * @return void
     */
    public function getAllGenres($vars = [])
    {
        try {
            $genres = $this->bookService->getBooksGenres();
            $this->sendSuccessResponse(['success' => true, 'genres' => $genres], 200);
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => 'Error fetching genres.' . $e->getMessage()], 500);
        }
    }
    /**
     * Get all books posted by a specific user for swap
     * 
     * @param array $vars URL parameters
     * @return void
     */
    public function getUserBooks($vars = [])
    {
        $userId = $vars['userId'] ?? null;
        if ($userId === null) {
            $this->sendErrorResponse(['error' => 'User ID is required.'], 400);
            return;
        }
        try {
            $books = $this->bookService->getBooksByUserId((int)$userId);
            if ($books) {
                $this->sendSuccessResponse(['success' => true, 'books' => $books], 200);
            } else {
                $this->sendErrorResponse(['error' => 'No books found for this user.'], 404);
            }
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['error' => 'Error fetching user books.'], 500);
        }
    }

    public function embedBooks($vars = [])
    {
        try {
            $books = $this->bookService->getAllBooks(null, null, null);
            foreach ($books as $book) {
                $this->bookService->updateBook($book);
            }
            $this->sendSuccessResponse(['success' => true, 'message' => 'Embeddings generated for all books.'], 200);
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['error' => 'Error generating embeddings for books.'. $e->getMessage()], 500);
        }
    }
}