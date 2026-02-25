<?php

namespace App\Controllers;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Models\BookCondition;
use App\Models\Book;
use App\Services\BookService;
use App\Repositories\BookRepository;
use App\Middleware\RequireRole;
use App\Models\UserRole;
use App\Repositories\BookAPI;

class BookController extends Controller
{
    private UserService $userService;
     private UserRepository $userRepository;
    private BookService $bookService;
    private BookRepository $bookRepository;
    
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
        $this->bookRepository = new BookRepository();
        $this->bookService = new BookService($this->bookRepository);
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function addBook($vars = [])
    {
        $error = isset($vars['error']) ? urldecode($vars['error']) : null;
        
        $this->view('Book/AddBook', ['message' => "Add a new book.", 'title' => 'Add Book Page', 'error' => $error] );
    }
    public function fetchBookPreview($vars = [])
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $isbn = $data['isbn'] ?? null;
        if (!$isbn) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ISBN required']);
            return;
        }
        
        try {
            $book = $this->bookService->getBookByISBNFromGoogleApi($isbn);
            header('Content-Type: application/json');
            echo json_encode($book);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function addBookPost($vars = [])
    {
        
        try{
            $book = $this->bookService->getBookByISBNFromGoogleApi($_POST['isbn']);
            $book->condition = BookCondition::from($_POST['condition'] ?? 'Unknown');
            $book->owner_review = $_POST['userReview'] ?? null;
            $sharedBy = $this->userService->getUserById($_SESSION['loggedInUser']->id);
            $book->shared_by = $sharedBy;

            $numberOfListings = $this->userService->numberOfListedBooks($sharedBy->id);
            
            $this->bookService->saveBook($book);
            
            
            if ($numberOfListings === 0) {
                $this->userService->addSwapTokens($sharedBy->id, 1);
            }

            header("Location: /");
            exit();
        }
        catch(\Exception $e){
            //echo "Error: " . htmlspecialchars($e->getMessage());
            header("Location: /addBook/" . urlencode($e->getMessage()));
            exit();
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
        header('Content-Type: application/json');
        echo json_encode($book);
    } catch (\Exception $e) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
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
        header('Content-Type: application/json');
        try {
        $bookId = $_GET['id'] ?? null;
        if ($bookId === null) {
            die("Book ID is required.");
        }
        $book = $this->bookService->getBookById((int)$bookId);
        if ($book === null) {
            die("Book not found.");
        }
        
        echo json_encode($book);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function myListings($vars = [])
    {
        $loggedInUser = $this->userService->getUserById($_SESSION['loggedInUser']->id);
        //$books = $this->bookService->getBooksByUser($loggedInUser);
        
        $this->view('Book/MyListings', ['message' => "My Book Listings.", 'title' => 'My Listings Page'] );
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
        header('Content-Type: application/json');
        $genreFilter = $_GET['genre'] ?? null;
        $generalFilter = $_GET['search'] ?? null;

        try {
            $books = $this->bookService->getAllBooks($genreFilter, $generalFilter);
            echo json_encode(['success' => true, 'books' => $books]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching books: ' . $e->getMessage()]);
        }
    }
}