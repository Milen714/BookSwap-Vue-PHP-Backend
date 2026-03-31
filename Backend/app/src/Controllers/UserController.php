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
use App\Services\AuthService;
use App\Services\Interfaces\IAuthService;
use App\Exceptions\PasswordStrengthException;
use App\Models\DTOs\UserDTO;

class UserController extends Controller
{
    private UserService $userService;
    private UserRepository $userRepository;
    private IAuthService $authService;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->authService = new AuthService();
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

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function updateProfile($vars = [])
    {
        $data = $this->getPostData();
        $userId = $data['userId'] ?? null;
        try {
            // Verify user is authenticated and owns this profile
            $authUserId = JWTMiddleware::getUserIdFromToken();
            if (!$this->authService->validateUserId((int)$userId, $authUserId)) {
                $this->sendErrorResponse(['error' => 'Unauthorized'], 403);
                return;
            }
            
            if (!$data) {
                $this->sendErrorResponse(['error' => 'Invalid JSON'], 400);
                return;
            }

            // Get existing user
            $user = $this->userService->getUserById($userId);
            if (!$user) {
                $this->sendErrorResponse(['error' => 'User not found'], 404);
                return;
            }

            // Update user profile with new data
            $user->updateProfile($data);

            // Save to database
            $this->userService->updateUser($user);

            $this->sendSuccessResponse([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'phone_number' => $user->phone_number,
                    'bio' => $user->bio
                ]
            ], 200);

        } catch (\Exception $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function updateAddress($vars = [])
    {
        $data = $this->getPostData();
        $userId = $data['userId'] ?? null;
        try {
            // Verify user is authenticated and owns this profile
            $authUserId = JWTMiddleware::getUserIdFromToken();
            if (!$this->authService->validateUserId((int)$userId, $authUserId)) {
                $this->sendErrorResponse(['error' => 'Unauthorized'], 403);
                return;
            }
            
            if (!$data) {
                $this->sendErrorResponse(['error' => 'Invalid JSON'], 400);
                return;
            }

            // Verify user is authenticated and owns this profile
            $authUserId = JWTMiddleware::getUserIdFromToken();
            if ($authUserId !== $userId) {
                $this->sendErrorResponse(['error' => 'Unauthorized'], 403);
                return;
            }

            // Get existing user
            $user = $this->userService->getUserById($userId);
            if (!$user) {
                $this->sendErrorResponse(['error' => 'User not found'], 404);
                return;
            }

            // Update address fields
            $user->updateAddress($data);

            // Save to database
            $this->userService->updateUser($user);

            $this->sendSuccessResponse([
                'success' => true,
                'message' => 'Address updated successfully',
                'user' => [
                    'id' => $user->id,
                    'address' => $user->address,
                    'state' => $user->state,
                    'country' => $user->country,
                    'post_code' => $user->post_code
                ]
            ], 200);

        } catch (\Exception $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function changePassword($vars = [])
    {
        $data = $this->getPostData();
        $userId = $data['userId'] ?? null;
        try {
            // Verify user is authenticated and owns this profile
            $authUserId = JWTMiddleware::getUserIdFromToken();
            if (!$this->authService->validateUserId((int)$userId, $authUserId)) {
                $this->sendErrorResponse(['error' => 'Unauthorized'], 403);
                return;
            }

            $oldPassword = $data['old_password'] ?? null;
            $newPassword = $data['new_password'] ?? null;
            $confirmPassword = $data['confirm_password'] ?? null;

            if (!$oldPassword || !$newPassword || !$confirmPassword) {
                $this->sendErrorResponse(['error' => 'All password fields are required'], 400);
                return;
            }

            // Get existing user
            $user = $this->userService->getUserById($userId);
            if (!$user) {
                $this->sendErrorResponse(['error' => 'User not found'], 404);
                return;
            }

            // Verify old password
            if (!password_verify($oldPassword, $user->password_hash)) {
                $this->sendErrorResponse(['error' => 'Old password is incorrect'], 401);
                return;
            }
            if ($newPassword !== $confirmPassword) {
                $this->sendErrorResponse(['error' => 'New password and confirm password do not match'], 400);
                return;
            }
            $passwordValidation = $this->authService->validatePassword($data['new_password']);
            if (!$passwordValidation['valid']) {
                $errorMsg = "Password does not meet the following criteria: " . implode(", ", $passwordValidation['errors']);
                throw new PasswordStrengthException($errorMsg);
                }
            // Update password hash
            $user->password_hash = password_hash($newPassword, PASSWORD_BCRYPT);

            // Save to database
            $this->userService->updateUser($user);

            $this->sendSuccessResponse([
                'success' => true,
                'message' => 'Password changed successfully'
            ], 200);

        } catch (PasswordStrengthException $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], 500);
        }
    }
    public function getUserInfoById($vars = [])
    {
        $userId = $vars['userId'] ?? null;
        if (!$userId) {
            $this->sendErrorResponse(['error' => 'User ID is required'], 400);
            return;
        }
        try {
            $user = $this->userService->getUserById((int)$userId);
            if (!$user) {
                $this->sendErrorResponse(['error' => 'User not found'], 404);
                return;
            }
            $userDto = new UserDTO($user);
            $this->sendSuccessResponse([
                'success' => true,
                'user' => $userDto
            ], 200);
        } catch (\Exception $e) {
            $this->sendErrorResponse(['error' => 'An error occurred while fetching user info.'], 500);
        }
    }
}