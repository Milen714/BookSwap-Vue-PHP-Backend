<?php
namespace App\Controllers;
use App\Framework\Controller;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\RequiredFieldException;
use App\Exceptions\PasswordStrengthException;
use App\Models\DTOs\UserDTO;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Services\MailService;
use App\Services\AuthService;
use App\Services\Interfaces\IAuthService;
use App\Middleware\JWTMiddleware;
use App\config\Secrets;

class AuthController extends Controller {
    private UserService $userService;
    private UserRepository $userRepository;
    private MailService $mailService;
    private IAuthService $authService;
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->mailService = new MailService();
        $this->authService = new AuthService();
    }

    public function login($vars = []) {
        try {
            $data = $this->getPostData();
            $email = $data['email'] ?? $_POST['email'] ?? '';
            $password = $data['password'] ?? $_POST['password'] ?? '';
            if (empty($email) || empty($password)) {
                throw new RequiredFieldException();
            }
            $user = $this->userService->authenticateUser($email, $password);
            if ($user) {
            // Successful login - generate JWT token
            $userDTO = new UserDTO($user);
            $token = $this->authService->generateJWTToken($user);
            
            $this->sendSuccessResponse(
                ['success' => true, 'message' => "Login successful. Welcome back, " . htmlspecialchars($user->fname) . "!", 'token' => $token, 'user' => $userDTO],
                200);
        } else {
            // Failed login
            $this->sendErrorResponse(['success' => false, 'message' => "Invalid email or password. Please try again."], 401);
             return;
        }
        } catch (\Exception $e) {
            $this->sendErrorResponse($e->getMessage(), 500);
        }
    }
    public function logout($vars = []) {
        $this->sendSuccessResponse(['success' => true, 'message' => 'Logged out successfully'], 200);
    }
    
    public function getLoggedInUser($vars = []) {
        try{
            // Validate JWT token from Authorization header
            $userId = JWTMiddleware::getUserIdFromToken();
            $user = $this->userService->getUserById($userId);
            
            if (!$user) {
                $this->sendErrorResponse(['success' => false, 'message' => 'User not found'], 404);
                return;
            }
            $dto = new UserDTO($user);
             $this->sendSuccessResponse(['success' => true, 'user' => $dto], 200);
            
            // $this->sendSuccessResponse([
            //     'success' => true,
            //     'loggedIn' => true,
            //     'user' => $dto,
            // ], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            $this->sendErrorResponse(['success' => false, 'loggedIn' => false, 'message' => $e->getMessage()], $code);
        }
    }
    public function signUp($vars = []){
        try {
            $data = $this->getPostData();

            $requiredFields = ['email', 'password', 'fname', 'lname'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new RequiredFieldException(ucfirst($field) . ' is required.');
                }
            }
            if ($this->userService->getUserByEmail($data['email'])) {
                throw new UserAlreadyExistsException();
            }

            
            // validate password strength
            $passwordValidation = $this->authService->validatePassword($data['password']);
            if (!$passwordValidation['valid']) {
                $errorMsg = "Password does not meet the following criteria: " . implode(", ", $passwordValidation['errors']);
                throw new PasswordStrengthException($errorMsg);
                }
                
            $user = User::fromArray($data);

            $created = $this->userService->createUser($user);
            if (!$created) {
                throw new \Exception('Failed to create user.');
            }

            $this->sendSuccessResponse(['success' => true, 'message' => 'Signup for ' . htmlspecialchars($user->email) . ' successful. Feel free to log in.'], 201);
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    public function forgotPasswordPost($vars = []) {
        $data = $this->getPostData();
        $email = $data['email'] ?? $_POST['email'] ?? '';
        try {
            $user = $this->userService->getUserByEmail($email);
            if (!$user) {
                throw new \Exception("No user found with that email address.");
            }
            $token = $this->generatePasswordResetToken($user);
            $resetLink = Secrets::$domain . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);
            // Send reset email
            $this->mailService->resetPasswordMail($user->email, $resetLink);
            $this->sendSuccessResponse(['success' => true, 'message' => 'Password reset link has been sent to your email address.']);
            
            
        } catch (\Exception $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    public function resetPassword() {
        try {
            $token = $_GET['token'] ?? '';
            $email = $_GET['email'] ?? '';
            $user = $this->userService->getUserByEmail($email);

            if ($this->authService->validateResetToken($user, $token)) {
                $this->sendSuccessResponse(['success' => true, 'message' => 'Token is valid. You can now reset your password.', 'email' => $email, 'token' => $token], 200);
            } else {
                throw new \Exception("Invalid or expired password reset token.");
            }
            
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
        // Reset password logic here
    }
    public function resetPasswordPost($vars = []) {
        $data = $this->getPostData();
        $token = $data['token'] ?? $_POST['token'] ?? '';
        $email = $data['email'] ?? $_POST['email'] ?? '';
        $newPassword = $data['password'] ?? $_POST['password'] ?? '';
        $repeatPassword = $data['repeatPassword'] ?? $_POST['repeatPassword'] ?? '';
        try {
            // Validate password strength
            $passwordValidation = $this->authService->validatePassword($data['password']);
            if ($newPassword !== $repeatPassword) {
                $this->sendErrorResponse(['success' => false, 'message' => "Passwords do not match."], 400);
                return;
            }
            if (!$passwordValidation['valid']) {
                $errorMsg = "Password does not meet the following criteria: " . implode(", ", $passwordValidation['errors']);
                throw new PasswordStrengthException($errorMsg);
                }

            $user = $this->userService->getUserByEmail($email);
            if ($this->authService->validateResetToken($user, $token)) {
                // Token is valid, proceed with password reset
                // Update the user's password
                $user->password_hash = password_hash($newPassword, PASSWORD_BCRYPT);
                // Clear the reset token and expiry
                $user->resset_token = null;
                $user->resset_token_expiry = null;
                $this->userService->updateUser($user);
                // Redirect to login with success message
                $this->sendSuccessResponse(['success' => true, 'message' => 'Password has been reset successfully.'], 200);
            } else {
                throw new \Exception("Invalid or expired password reset token.");
            }
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    private function generateSecureToken(int $length = 32): string {
        $str = bin2hex(random_bytes($length));
        return base64_encode($str);
    }
    private function generatePasswordResetToken(User $user): string {
        try {
        $token = $this->generateSecureToken();
        $user->resset_token = $token;
        $user->resset_token_expiry = new \DateTime('+1 hour'); // Token valid for 1 hour
        $this->userService->updateUser($user);
        return $token;
        } catch (\Throwable $e) {
            die("Error generating password reset token: " . $e->getMessage());
        }

    }

    public function currentUser()
    {
        try {

            // Get token from Authorization header
            if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                return $this->sendErrorResponse('Authorization header is required', 401);
            }

            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            $headerParts = explode(' ', $authHeader);
            if (count($headerParts) !== 2 || strtolower($headerParts[0]) !== 'bearer') {
                return $this->sendErrorResponse('Invalid authorization header format', 401);
            }
            $token = $headerParts[1];

            $user = $this->authService->getUserFromToken($token);

            if (!$user) {
                return $this->sendErrorResponse('Invalid or expired token', 401);
            }

            // Return user DTO
            $userDTO = new UserDTO($user);
            return $this->sendSuccessResponse($userDTO);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }
}