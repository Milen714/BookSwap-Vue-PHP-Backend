<?php
namespace App\Controllers;
use App\Framework\Controller;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\Mailer;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Services\MailService;

class AuthController extends Controller {
    private UserService $userService;
    private UserRepository $userRepository;
    private MailService $mailService;
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
        $this->mailService = new MailService();
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
            // Successful login
            $_SESSION['loggedInUser'] = $user;
            $_SESSION['loggedInUserId'] = $user->id;
            session_write_close();
            
            $this->sendSuccessResponse(
                ['success' => true, 'message' => "Login successful. Welcome back, " . htmlspecialchars($user->fname) . "!"]
            , 200 );
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
        session_destroy();
        header('Location: /');
        exit;
    }
    public function getLoggedInUser() {
        $user = $_SESSION['loggedInUser'] ?? null;
        try{
        if (!$user && isset($_SESSION['loggedInUserId'])) {
            $user = $this->userService->getUserById((int) $_SESSION['loggedInUserId']);
            if ($user) {
                $_SESSION['loggedInUser'] = $user;
            }
        }

        if ($user) {
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
            return;
        }
        $this->sendErrorResponse(['success' => false, 'loggedIn' => false, 'message' => 'No user logged in'], 401);
        
        } catch (\Exception $e) {
            $this->sendErrorResponse(['success' => false, 'message' => 'An error occurred while fetching user data.'], 500);
            return;
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
}