<?php
namespace App\Services;
use App\Repositories\Interfaces\IUserRepository;
use App\Repositories\UserRepository;
use App\Services\Interfaces\IUserService;
use App\Models\User;
use App\Exceptions\ForbiddenException;

/**
 * Service layer for user account and admin analytics operations.
 */
class UserService implements IUserService {
    /**
     * User persistence abstraction.
     */
    private IUserRepository $userRepository;

    /**
     * Initialize default user repository.
     */
    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    /**
     * Get all users.
     *
     * @return array<User>
     */
    public function getAllUsers(): array {
        return $this->userRepository->getAllUsers();
    }

    /**
     * Find a user by email address.
     *
     * @param string $email User email.
     * @return User|null User when found.
     */
    public function getUserByEmail(string $email): ?User {
        $user = $this->userRepository->getUserByEmail($email);
        return $user;
    }

    /**
     * Authenticate user credentials and enforce account status.
     *
     * @param string $email User email.
     * @param string $password Plain-text password input.
     * @return User|null Authenticated user or null for invalid credentials.
     * @throws ForbiddenException When the account is suspended.
     */
    public function authenticateUser(string $email, string $password): ?User {
        $user = $this->userRepository->getUserByEmail($email);
        if ($user && password_verify($password, $user->password_hash)) {
            if (!$user->isActive) {
                throw new ForbiddenException('This account has been suspended.');
            }
            return $user;
        }
        return null;
    }

    /**
     * Find a user by id.
     *
     * @param int $id User id.
     * @return User|null User when found.
     */
    public function getUserById(int $id): ?User {
        $user = $this->userRepository->getUserById($id);
        return $user;
    }

    /**
     * Create a new user account.
     *
     * @param User $user User model.
     * @return bool True on success.
     */
    public function createUser(User $user): bool {
        return $this->userRepository->createUser($user);
    }

    /**
     * Persist updates to a user account.
     *
     * @param User $user User model.
     * @return bool True on success.
     */
    public function updateUser(User $user): bool {
        return $this->userRepository->updateUser($user);
    }

    /**
     * Set active/suspended state for a user.
     *
     * @param int $userId User id.
     * @param bool $isActive Whether account should be active.
     * @return bool True on success.
     */
    public function setUserActive(int $userId, bool $isActive): bool {
        return $this->userRepository->setUserActive($userId, $isActive);
    }

    /**
     * Deduct one swap token from a user account.
     *
     * @param int $userId User id.
     * @return bool True when token deduction succeeds.
     */
    public function deductSwapToken(int $userId): bool {
        return $this->userRepository->deductSwapToken($userId);
    }

    /**
     * Add swap tokens to a user account.
     *
     * @param int $userId User id.
     * @param int $amount Amount of tokens to add.
     * @return bool True on success.
     */
    public function addSwapTokens(int $userId, int $amount): bool {
        return $this->userRepository->addSwapTokens($userId, $amount);
    }

    /**
     * Count currently listed books for a user.
     *
     * @param int $userId User id.
     * @return int Number of listed books.
     */
    public function numberOfListedBooks(int $userId): int {
        return $this->userRepository->numberOfListedBooks($userId);
    }

    /**
     * Retrieve admin dashboard analytics values.
     *
     * @return array<string, mixed> Analytics metrics.
     */
    public function getAdminAnalytics(): array {
        return $this->userRepository->getAdminAnalytics();
    }
}