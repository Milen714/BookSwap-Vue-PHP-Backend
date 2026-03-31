<?php
namespace App\Services;
//require_once __DIR__ . '/../../config/config.php';
use App\Models\Enums\UserRole;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Services\Interfaces\IAuthService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\config\Secrets;
class AuthService implements IAuthService{
    private ?User $user = null;
    private UserService $userService;
    private UserRepository $userRepository;

    public function __construct()
    {   $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        
    }
    public function getLoggedInUser(): ?User {
        if ($this->user === null && isset($_SESSION['loggedInUser'])) {
            $this->user = $this->userService->getUserById($_SESSION['loggedInUser']->id);
            return $this->user;
        }
        throw new \Exception("No user logged in");
    }
    public function hasRole(UserRole $roleToCheck): bool {
        return $this->user !== null && $this->user->role === $roleToCheck;
    }
    public function logout(string $message): void {
        session_unset();
        session_destroy();
        $this->user = null;
        header('Location: /login/' . urlencode($message));
        exit();
    }
    
    public function generateActionToken(): string{
        $token = bin2hex(random_bytes(32));
        return base64_encode($token);
        
    }

    public function generateJWTToken(User $user): string
    {
        $now = time();
        $expiration = $now + (Secrets::$tokenExpirationHours * 3600); // Convert hours to seconds
        
        $payload = [
            'iss' => Secrets::$domain, // Issuer
            'aud' => Secrets::$domain, // Audience
            'iat' => $now, // Issued at
            'nbf' => $now, // Not before
            'exp' => $expiration, // Expiration time (24 hours from now)
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'username' => $user->fname,
                'role' => $user->role,
                'swapTokens' => $user->swapTokens
            ],
        ];
        
        return JWT::encode($payload, Secrets::$secretKey, Secrets::JWT_ALGORITHM);
    }
    public function validateToken(string $token): bool
    {
        try {
            $decoded = JWT::decode($token, new Key(Secrets::$secretKey, Secrets::JWT_ALGORITHM));
            
            // Validate required claims
            if (!isset($decoded->iss) || !isset($decoded->aud) || !isset($decoded->exp)) {
                return false;
            }
            
            // Validate issuer and audience match domain
            if ($decoded->iss !== Secrets::$domain || $decoded->aud !== Secrets::$domain) {
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            return false; // Invalid token
        }
    }
    public function getUserFromToken(string $token): ?User
    {
        try { 
            $decoded = JWT::decode($token, new Key(Secrets::$secretKey, Secrets::JWT_ALGORITHM));
        } catch (\Exception $e) {
            return null; // Invalid token
        }

        // Get user by ID from the decoded token
        if (isset($decoded->data->id)) {
            return $this->userRepository->getUserById($decoded->data->id);
        }

        return null;        
    }
    public function validatePassword(string $password): array
{
    $result = [
        'valid' => true,
        'errors' => []
    ];

    if (strlen($password) < 8) {
        $result['valid'] = false;
        $result['errors'][] = 'At least 8 characters.';
    }

    if (!preg_match('/[a-z]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = 'At least one lowercase letter.';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = 'At least one uppercase letter.';
    }

    if (!preg_match('/\d/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = 'At least one number.';
    }

    if (!preg_match('/[\W_]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = 'At least one special character.';
    }

    return $result;
}
    public function validateResetToken(User $user, string $token): bool
    {
        if (!$user || $user->resset_token !== $token) {
            throw new \Exception("Invalid or expired password reset Link.");
        }
        
        $now = new \DateTime();

        if ($user->resset_token_expiry < $now) {
            throw new \Exception("Password reset token has expired.");
        }

        return true;

    }
}