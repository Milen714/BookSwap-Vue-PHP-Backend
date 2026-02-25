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
use App\Models\PaginatedList;

class HomeController extends Controller
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

    public function home($vars = [])
    {
        $search = $_GET['search'] ?? null;
        $genre = $_GET['genre'] ?? null;
        // $_SESSION['toast'] = [
        // 'type' => 'success',
        // 'title' => 'Welcome back!',
        // 'description' => 'You are now logged in'
        // ];

        $param = $vars['id'] ?? null;  // Gets 'value'
        $books = $this->bookService->getAllBooks($genre,$search);
        $genres = $this->bookService->getBooksGenres();
        $paginatedBooks = new PaginatedList($books, 1, 5, count($books));
        $paginatedBooks = $paginatedBooks->createPaginatedList($books, $_GET['page'] ?? 1, 10);
        

        $this->view('Home/Landing', ['message' => "Please log in. now :)", 'title' => 'Login Page', 
        'param' => $param ?? 'noParam', 'paginatedBooks' => $paginatedBooks, 'genres' => $genres] );
    }
    public function signup($vars = [])
    {
        $this->view('Home/Signup', ['message' => "Please sign up.", 'title' => 'Signup Page'] );
    }
    public function signupPost($vars = [])
    {
        $user = new User();
        $user = $user->fromPost();
        try {
            $this->userService->createUser($user);
        echo "Signup successful for " . htmlspecialchars($user->fname) . " with email " . htmlspecialchars($user->email) . ".";
        $this->view('Home/Login', ['success' => "Signup successful for " . htmlspecialchars($user->fname) . " with email " . htmlspecialchars($user->email) . ".", 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
            } catch (\Exception $e) {
                if(str_contains($e->getMessage(), 'email')) {
                    $errorMsg = "This email is already in use.";
                } else {
                 $errorMsg = $e->getMessage();
                }
                $this->view('Home/Signup', ['message' => "Please sign up.", 'title' => 'Signup Page', 'userModel' => $user, 'error' => $errorMsg] );
        }
    }
    
    public function login($vars = [])
    {
        $error = null;
        if (isset($vars['error'])) {
            $error =  urldecode($vars['error']);
        }
        $this->view('Home/Login', ['error' => $error, 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
    }
    public function loginPost($vars = [])
    {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = $this->userService->authenticateUser($email, $password);
        if ($user) {
            // Successful login
            $_SESSION['loggedInUser'] = $user;
            header("Location: /");
            exit();
            
        } else {
            // Failed login
            throw new \Exception("Invalid email or password.");
        }
        } catch (\Exception $e) {
            //header("Location: /login/" . urlencode($e->getMessage()));
            $this->view('Home/Login', ['error' => $e->getMessage(), 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
            exit();
        }
    }
    public function logout($vars = [])
    {
        // Clear the session to log out the user
        session_unset();
        session_destroy();
        header("Location: /");
        
        exit();
    }
    public function setTheme($vars = [])
    {
        header('Content-Type: application/json');
        if (isset($_POST['theme'])) {
            $theme = $_POST['theme'];
            // Set a cookie wit 30 day expiry for the selected theme
            setcookie('theme', $theme, time() + (86400 * 30), '/');
            
            echo json_encode(['success' => true, 'theme' => $theme]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No theme selected']);
        }
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function dashboard() {
        echo "Welcome Admin!";
    }
    public function notFound() {
        $this->view('Shared/NotFound', ['title' => 'Page Not Found']);
    }
    public function notAuthorized() {
        $this->view('Shared/NotAuthorized', ['title' => 'Not Authorized']);
    }
}