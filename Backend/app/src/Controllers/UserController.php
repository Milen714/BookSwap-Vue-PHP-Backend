<?php
namespace App\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Middleware\RequireRole;
use App\Models\UserRole;
use Stripe\Terminal\Location;

class UserController extends Controller
{
    private UserService $userService;
    private UserRepository $userRepository;
    
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
    }

    public function profile($vars = [])
    {
        $userId = $_SESSION['loggedInUser']->id ?? null;
        if (!$userId) {
            header("Location: /login");
            exit();
        }

        $user = $this->userService->getUserById($userId);
        $this->view('User/Profile', ['message' => "User Profile", 'title' => 'Profile Page', 'user' => $user] );
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getProfileAddress($vars = [])
    {
        header('Content-Type: application/json');
        try{
        $userId = $vars['id'] ?? null;
        if (!$userId || $_SESSION['loggedInUser']->id != $userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Nice try! Unauthorized access.']);
            
            return;
        }
        $user = $this->userService->getUserById($userId);
        $address = [
            'address' => $user->address,
            'post_code' => $user->post_code,
            'state' => $user->state,
            'country' => $user->country
        ];
        http_response_code(200);
        echo json_encode($address);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred while fetching the address.']);
        }
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getUserTokens($vars = []){
        $userId = isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : null;
        
        $user = $this->userService->getUserById($userId);
        $token = $user->swapTokens;

        header('Content-Type: application/json');
        echo json_encode(['tokens' => $token]);
    }
}