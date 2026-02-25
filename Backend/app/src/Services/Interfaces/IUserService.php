<?php

namespace App\Services\Interfaces;
use App\Repositories\Interfaces\IUserRepository;
use App\Models\User;
interface IUserService {
    public function getAllUsers(): array;
    public function getUserByEmail(string $email): ?User;
    public function authenticateUser(string $email, string $password): ?User;
    public function getUserById(int $id): ?User;
    public function createUser(User $user): bool;
    public function updateUser(User $user): bool;
    public function deductSwapToken(int $userId): bool;
    public function addSwapTokens(int $userId, int $amount): bool;
    public function numberOfListedBooks(int $userId): int;
}