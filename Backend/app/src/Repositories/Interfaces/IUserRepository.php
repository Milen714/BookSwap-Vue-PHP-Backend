<?php
namespace App\Repositories\Interfaces;
use App\Models\User;

interface IUserRepository {
    public function getAllUsers(): array;
    public function getUserByEmail(string $email): ?User;
    public function getUserById(int $id): ?User;
    public function createUser(User $user): bool;
    public function updateUser(User $user): bool;
    public function deductSwapToken(int $userId): bool;
    public function addSwapTokens(int $userId, int $amount): bool;
    public function numberOfListedBooks(int $userId): int;
}