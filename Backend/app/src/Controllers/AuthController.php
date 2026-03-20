<?php
namespace App\Controllers;
use App\Framework\Controller;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\DTOs\UserDTO;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Services\MailService;
use App\Services\AuthService;
use App\Services\Interfaces\IAuthService;
use App\Middleware\JWTMiddleware;

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

    public function login() {
        try {
            $data = $this->getPostData();
            $email = $data['email'] ?? $_POST['email'] ?? '';
            $password = $data['password'] ?? $_POST['password'] ?? '';
            if (empty($email) || empty($password)) {
                throw new \Exception("Email and password are required.");
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
    public function logout() {
        header('Content-Type: application/json');
        $this->sendSuccessResponse(['success' => true, 'message' => 'Logged out successfully'], 200);
    }
    
    public function getLoggedInUser() {
        header('Content-Type: application/json');
        try{
            // Validate JWT token from Authorization header
            $userId = JWTMiddleware::getUserIdFromToken();
            $user = $this->userService->getUserById($userId);
            
            if (!$user) {
                throw new \Exception('User not found', 401);
            }
            
            $this->sendSuccessResponse([
                'success' => true,
                'loggedIn' => true,
                'user' => [
                    'id' => $user->id,
                    'fname' => $user->fname,
                    'lname' => $user->lname,
                    'email' => $user->email,
                    'role' => $user->role,
                    'swapTokens' => $user->swapTokens,
                ],
            ], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            $this->sendErrorResponse(['success' => false, 'loggedIn' => false, 'message' => $e->getMessage()], $code);
        }
    }
    public function signUp(){

        try {
           
            $data = $this->getPostData();

            if (!is_array($data)) {
                $data = $_POST;
            }

            $requiredFields = ['email', 'password', 'fname', 'lname'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new \Exception(ucfirst($field) . ' is required.');
                }
            }

            if ($this->userService->getUserByEmail($data['email'])) {
                throw new \Exception('This email is already in use.');
            }

            $user = User::fromArray($data);

            $created = $this->userService->createUser($user);
            if (!$created) {
                throw new \Exception('Failed to create user.');
            }

            http_response_code(201);
            $this->sendSuccessResponse(['success' => true, 'message' => 'Signup for ' . htmlspecialchars($user->email) . ' successful. Feel free to log in.'], 201);
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    public function forgotPassword() {
        $this->view('Account/ForgotPassword', ['title' => 'Forgot Password']);
    }
    public function forgotPasswordPost($vars = []) {
        $email = $_POST['email'] ?? '';
        try {
            $user = $this->userService->getUserByEmail($email);
            if (!$user) {
                throw new \Exception("No user found with that email address.");
            }
            $token = $this->generatePasswordResetToken($user);
            require_once '../config/secrets.php';
            $resetLink = $DOMAIN_URL . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);
            // Send reset email
            $this->mailService->resetPasswordMail($user->email, $resetLink);
            $this->view('Home/Login', ['success' => "Password reset email sent. Please check your inbox.", 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
            
            
        } catch (\Exception $e) {
            $this->view('Account/ForgotPassword', ['title' => 'Forgot Password', 'error' => $e->getMessage()]);
        }
    }
    public function resetPassword() {
        try {
            $token = $_GET['token'] ?? '';
            $email = $_GET['email'] ?? '';
            $user = $this->userService->getUserByEmail($email);
            if (!$user || $user->resset_token !== $token) {
                throw new \Exception("Invalid or expired password reset token.");
            }
            $now = new \DateTime();
            if ($user->resset_token_expiry < $now) {
                throw new \Exception("Password reset token has expired.");
            }
            // Show reset password form
            $this->view('Account/ResetPassword', ['title' => 'Reset Password']);
        } catch (\Exception $e) {
            $this->view('Account/ForgotPassword', ['title' => 'Forgot Password', 'error' => $e->getMessage()]);
        }
        // Reset password logic here
    }
    public function resetPasswordPost($vars = []) {
        $token = $_POST['token'] ?? '';
        $email = $_POST['email'] ?? '';
        $newPassword = $_POST['password'] ?? '';
        $repeatPassword = $_POST['repeatPassword'] ?? '';
        if ($newPassword !== $repeatPassword) {
            $this->view('Account/ResetPassword', ['title' => 'Reset Password', 'error' => "Passwords do not match.",
                        "email" => $email, "token" => $token]);
            return;
        }
        try {
            $user = $this->userService->getUserByEmail($email);
            if (!$user || $user->resset_token !== $token) {
                throw new \Exception("Invalid or expired password reset token.");
            }
            $now = new \DateTime();
            if ($user->resset_token_expiry < $now) {
                throw new \Exception("Password reset token has expired.");
            }
            // Update the user's password
            $user->password_hash = password_hash($newPassword, PASSWORD_BCRYPT);
            // Clear the reset token and expiry
            $user->resset_token = null;
            $user->resset_token_expiry = null;
            $this->userService->updateUser($user);
            // Redirect to login with success message
            $this->view('Home/Login', ['success' => "Password has been reset successfully.", 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
        } catch (\Exception $e) {
            $this->view('Account/ResetPassword', ['title' => 'Reset Password', 'error' => $e->getMessage()]);
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
        } catch (\Exception $e) {
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