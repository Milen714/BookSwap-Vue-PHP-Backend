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

/**
 * HomeController
 * 
 * Manages home page and general site operations including theme preference management.
 */
class HomeController extends Controller
{
    private UserService $userService;
    private UserRepository $userRepository;
    private BookService $bookService;
    private BookRepository $bookRepository;
    
    /**
     * Initialize home controller services
     */
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->bookRepository = new BookRepository();
        $this->bookService = new BookService($this->bookRepository);
    }

   
    /**
     * Set user theme preference with 30-day cookie persistence
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
    
}