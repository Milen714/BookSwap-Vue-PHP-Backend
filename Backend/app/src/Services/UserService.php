<?php
namespace App\Services;
use App\Repositories\Interfaces\IUserRepository;
use App\Services\Interfaces\IUserService;
use App\Models\User;
class UserService implements IUserService {
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): array {
        return $this->userRepository->getAllUsers();
    }
    public function getUserByEmail(string $email): ?User {
        $user = $this->userRepository->getUserByEmail($email);
        return $user;
    }
    public function authenticateUser(string $email, string $password): ?User {
        $user = $this->userRepository->getUserByEmail($email);
        if ($user && password_verify($password, $user->password_hash)) {
            return $user;
        }
        return null;
    }
    public function getUserById(int $id): ?User {
        $user = $this->userRepository->getUserById($id);
        return $user;
    }
    public function createUser(User $user): bool {
        return $this->userRepository->createUser($user);
    }
    public function updateUser(User $user): bool {
        return $this->userRepository->updateUser($user);
    }
    public function deductSwapToken(int $userId): bool {
        return $this->userRepository->deductSwapToken($userId);
    }
    public function addSwapTokens(int $userId, int $amount): bool {
        return $this->userRepository->addSwapTokens($userId, $amount);
    }
    public function numberOfListedBooks(int $userId): int {
        return $this->userRepository->numberOfListedBooks($userId);
    }
}