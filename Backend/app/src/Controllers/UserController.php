<?php
namespace App\Controllers;
use App\Framework\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Middleware\RequireRole;
use App\Middleware\JWTMiddleware;
use App\Models\Enums\UserRole;
use Stripe\Terminal\Location;

class UserController extends Controller
{
    private UserService $userService;
    private UserRepository $userRepository;
    
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
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
        try{
        $userId = JWTMiddleware::getUserIdFromToken();
        
        $user = $this->userService->getUserById($userId);
        $address = [
            'address' => $user->address,
            'post_code' => $user->post_code,
            'state' => $user->state,
            'country' => $user->country
        ];
        $this->sendSuccessResponse($address, 200);
         } catch (\Exception $e) {
            $this->sendErrorResponse(['error' => 'An error occurred while fetching the address.'], 500);
        } 
    }
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getUserTokens($vars = []){
        header('Content-Type: application/json');
        try {
            $userId = JWTMiddleware::getUserIdFromToken();
            $user = $this->userService->getUserById($userId);
            $token = $user->swapTokens;

            $this->sendSuccessResponse(['success' => true, 'tokens' => $token], 200);
        } catch (\Exception $e) {
            $this->sendErrorResponse(['success' => false, 'error' => 'An error occurred while fetching user tokens.'], 401);
        }
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function getUserInfo($vars = [])
    {
        header('Content-Type: application/json');
        try {
            $userId = $_GET['userId'] ?? null;
            if (!$userId) {
                $this->sendErrorResponse(['error' => 'User ID is required'], 400);
                return;
            }

            $user = $this->userService->getUserById((int)$userId);
            if (!$user) {
                $this->sendErrorResponse(['error' => 'User not found'], 404);
                return;
            }

            $this->sendSuccessResponse([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'firstName' => $user->fname,
                    'lastName' => $user->lname,
                    'email' => $user->email,
                    'profilePic' => $user->profilePic ?? null
                ]
            ], 200);
        } catch (\Exception $e) {
            $this->sendErrorResponse(['error' => 'An error occurred while fetching user info.'], 500);
        }
    }
}