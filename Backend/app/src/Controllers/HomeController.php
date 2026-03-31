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
use App\Models\PaginatedList;

class HomeController extends Controller
{
    private UserService $userService;
    private UserRepository $userRepository;
    private BookService $bookService;
    private BookRepository $bookRepository;
    
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->bookRepository = new BookRepository();
        $this->bookService = new BookService($this->bookRepository);
    }

    public function home($vars = [])
    {
        $search = $_GET['search'] ?? null;
        $genre = $_GET['genre'] ?? null;
        

        $param = $vars['id'] ?? null;  // Gets 'value'
        $books = $this->bookService->getAllBooks($genre,$search);
        $genres = $this->bookService->getBooksGenres();
        $paginatedBooks = new PaginatedList($books, 1, 5, count($books));
        $paginatedBooks = $paginatedBooks->createPaginatedList($books, $_GET['page'] ?? 1, 10);
        

        $this->view('Home/Landing', ['message' => "Please log in. now :)", 'title' => 'Login Page', 
        'param' => $param ?? 'noParam', 'paginatedBooks' => $paginatedBooks, 'genres' => $genres] );
    }
    
    public function setTheme($vars = [])
    {
        if (isset($_POST['theme'])) {
            $theme = $_POST['theme'];
            // Set a cookie wit 30 day expiry for the selected theme
            setcookie('theme', $theme, time() + (86400 * 30), '/');
            
            $this->sendSuccessResponse(['success' => true, 'message' => 'Theme updated successfully.', 'theme' => $theme], 200);
            echo json_encode(['success' => true, 'theme' => $theme]);
        } else {
            $this->sendErrorResponse(['success' => false, 'message' => 'No theme selected'], 400);
        }
    }
    
    public function notFound() {
        $this->view('Shared/NotFound', ['title' => 'Page Not Found']);
    }
    public function notAuthorized() {
        $this->view('Shared/NotAuthorized', ['title' => 'Not Authorized']);
    }
}