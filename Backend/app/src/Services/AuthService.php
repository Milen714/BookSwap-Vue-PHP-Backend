<?php
namespace App\Services;

use App\Models\Enums\UserRole;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Services\Interfaces\IAuthService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\config\Secrets;
use App\Exceptions\ValidationException;
use App\Exceptions\ServiceException;
use Dotenv\Parser\Value;

/**
 * Service responsible for authentication, JWT handling, and password reset token workflows.
 */
class AuthService implements IAuthService{
    /**
     * Cached authenticated user for role checks in the current lifecycle.
     */
    private ?User $user = null;

    /**
     * Service used for user updates (for example reset token persistence).
     */
    private UserService $userService;

    /**
     * Repository used to resolve users from token payload data.
     */
    private UserRepository $userRepository;

    /**
     * Initialize auth dependencies.
     */
    public function __construct()
    {   $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        
    }
   
    /**
     * Check whether the cached authenticated user has the requested role.
     *
     * @param UserRole $roleToCheck Role that must match exactly.
     * @return bool True when a user is loaded and role matches.
     */
    public function hasRole(UserRole $roleToCheck): bool {
        return $this->user !== null && $this->user->role === $roleToCheck;
    }

    /**
     * Destroy the current session and redirect to login with a message.
     *
     * @param string $message Message appended to login route.
     * @return void
     */
    public function logout(string $message): void {
        session_unset();
        session_destroy();
        $this->user = null;
        header('Location: /login/' . urlencode($message));
        exit();
    }
    
    /**
     * Generate a secure action token used for one-time flows.
     *
     * @return string Base64-encoded random token.
     * @throws \Random\RandomException When random bytes generation fails.
     */
    public function generateActionToken(): string{
        $token = bin2hex(random_bytes(32));
        return base64_encode($token);
        
    }

    /**
     * Generate a signed JWT token for the given user.
     *
     * @param User $user Authenticated user.
     * @return string Signed JWT token.
     */
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
                'phone_number' => $user->phone_number,
                'bio' => $user->bio,
                'address' => $user->address,
                'state' => $user->state,
                'country' => $user->country,
                'post_code' => $user->post_code,
                'role' => $user->role->value,
                'swapTokens' => $user->swapTokens
            ],
        ];
        
        return JWT::encode($payload, Secrets::$secretKey, Secrets::JWT_ALGORITHM);
    }

    /**
     * Validate JWT signature and required claims.
     *
     * @param string $token Token to validate.
     * @return bool True when token is valid and claim checks pass.
     */
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

    /**
     * Decode token and load the referenced user.
     *
     * @param string $token JWT token.
     * @return User|null User when token is valid and user exists; otherwise null.
     */
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

    /**
     * Validate password complexity requirements.
     *
     * @param string $password Raw password value.
     * @return array{valid: bool, errors: string[]} Validation result and messages.
     */
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

    /**
     * Validate reset token value and expiration for a user.
     *
     * @param User|null $user User owning the reset token.
     * @param string $token Token sent by client.
     * @return bool True when token matches and is not expired.
     * @throws ValidationException When token is invalid or expired.
     */
    public function validateResetToken(?User $user, string $token): bool
    {
        if (!$user || $user->resset_token !== $token) {
            throw new ValidationException("Invalid or expired password reset link.");
        }
        
        $now = new \DateTime();

        if ($user->resset_token_expiry < $now) {
            throw new ValidationException("Password reset token has expired.");
        }

        return true;

    }

    /**
     * Validate that a target user id matches authenticated user id.
     *
     * @param int $idToValidate User id from route/body.
     * @param int $userId Authenticated user id.
     * @return bool True when both ids are identical.
     */
    public function validateUserId(int $idToValidate, int $userId): bool
    {
        try {
            return $idToValidate === $userId;
        } catch (\Exception $e) {
            return false; // Invalid token
        }
    }

    /**
     * Generate a secure random token for password reset
     * 
     * @param int $length Token length in bytes
     * @return string Base64 encoded secure token
     * @throws \Random\RandomException When random bytes generation fails.
     */
    public function generateSecureToken(int $length = 32): string {
        $str = bin2hex(random_bytes($length));
        return base64_encode($str);
    }

    /**
     * Generate password reset token and update user with expiry
     * 
     * @param User $user User to generate token for
     * @return string Generated reset token
        * @throws ServiceException When token generation or persistence fails.
     */
    public function generatePasswordResetToken(User $user): string {
        try {
            $token = $this->generateSecureToken();
            $user->resset_token = $token;
            $user->resset_token_expiry = new \DateTime('+1 hour'); // Token valid for 1 hour
            $this->userService->updateUser($user);
            return $token;
        } catch (\Throwable $e) {
            throw new ServiceException("Error generating password reset token.", 500, $e);
        }
    }
}