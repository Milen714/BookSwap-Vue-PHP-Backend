<?php
namespace App\Controllers;
use App\Framework\Controller;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\RequiredFieldException;
use App\Exceptions\PasswordStrengthException;
use App\Exceptions\ApplicationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ServiceException;
use App\Exceptions\ValidationException;
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

/**
 * AuthController
 * 
 * Handles all authentication operations including login, signup,
 * password reset, and JWT token management.
 */
class AuthController extends Controller {
    private UserService $userService;
    private UserRepository $userRepository;
    private MailService $mailService;
    private IAuthService $authService;
    /**
     * Initialize all authentication services
     */
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->mailService = new MailService();
        $this->authService = new AuthService();
    }

    /**
     * Authenticate user with email and password, return JWT token
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
        } catch (ApplicationException $e) {
            $this->sendErrorResponse($e->getMessage(), $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse('Login failed due to an unexpected error.', 500);
        }
    }
    /**
     * Log out user and close session
     * 
     * @param array $vars URL parameters
     * @return void
     */
    public function logout($vars = []) {
        $this->sendSuccessResponse(['success' => true, 'message' => 'Logged out successfully'], 200);
    }
    
    /**
     * Retrieve currently logged-in user information from JWT token
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
           
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'loggedIn' => false, 'message' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'loggedIn' => false, 'message' => 'Failed to fetch logged in user.'], 500);
        }
    }
    /**
     * Create new user account with validation and signup email
     * 
     * @param array $vars URL parameters
     * @return void
     */
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
                throw new ServiceException('Failed to create user.');
            }

            $this->sendSuccessResponse(['success' => true, 'message' => 'Signup for ' . htmlspecialchars($user->email) . ' successful. Feel free to log in.'], 201);
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => 'Signup failed due to an unexpected error.'], 500);
        }
    }
    
    /**
     * Initiate password reset by sending reset link to user email
     * 
     * @param array $vars URL parameters
     * @return void
     */
    public function forgotPasswordPost($vars = []) {
        $data = $this->getPostData();
        $email = $data['email'] ?? $_POST['email'] ?? '';
        try {
            $user = $this->userService->getUserByEmail($email);
            if (!$user) {
                throw new NotFoundException("No user found with that email address.");
            }
            $token = $this->authService->generatePasswordResetToken($user);
            $resetLink = Secrets::$frontendUrl . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);
            // Send reset email
            $this->mailService->resetPasswordMail($user->email, $resetLink);
            $this->sendSuccessResponse(['success' => true, 'message' => 'Password reset link has been sent to your email address.']);
            
            
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => 'Failed to process forgot password request.'], 500);
        }
    }
    /**
     * Validate password reset token and display reset form
     * 
     * @param array $vars URL parameters
     * @return void
     */
    public function resetPassword() {
        try {
            $token = $_GET['token'] ?? '';
            $email = $_GET['email'] ?? '';
            $user = $this->userService->getUserByEmail($email);

            if (!$user) {
                throw new NotFoundException("No user found with that email address.");
            }

            if ($this->authService->validateResetToken($user, $token)) {
                $this->sendSuccessResponse(['success' => true, 'message' => 'Token is valid. You can now reset your password.', 'email' => $email, 'token' => $token], 200);
            } else {
                throw new ValidationException("Invalid or expired password reset token.");
            }
            
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => 'Failed to validate reset token.'], 500);
        }
        // Reset password logic here
    }
    /**
     * Process password reset form submission and update password
     * 
     * @param array $vars URL parameters
     * @return void
     */
    public function resetPasswordPost($vars = []) {
        $data = $this->getPostData();
        $token = $data['token'] ?? $_POST['token'] ?? '';
        $email = $data['email'] ?? $_POST['email'] ?? '';
        $newPassword = $data['password'] ?? $_POST['password'] ?? '';
        $repeatPassword = $data['repeatPassword'] ?? $_POST['repeatPassword'] ?? '';
        try {
            // Validate password strength
            $passwordValidation = $this->authService->validatePassword($newPassword);
            if ($newPassword !== $repeatPassword) {
                $this->sendErrorResponse(['success' => false, 'message' => "Passwords do not match."], 400);
                return;
            }
            if (!$passwordValidation['valid']) {
                $errorMsg = "Password does not meet the following criteria: " . implode(", ", $passwordValidation['errors']);
                throw new PasswordStrengthException($errorMsg);
                }

            $user = $this->userService->getUserByEmail($email);
            if (!$user) {
                throw new NotFoundException("No user found with that email address.");
            }
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
                throw new ValidationException("Invalid or expired password reset token.");
            }
        } catch (ApplicationException $e) {
            $this->sendErrorResponse(['success' => false, 'message' => $e->getMessage()], $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->sendErrorResponse(['success' => false, 'message' => 'Failed to reset password.'], 500);
        }
    }

   
}