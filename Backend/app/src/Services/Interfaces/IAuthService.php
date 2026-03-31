<?php
namespace App\Services\Interfaces;

use App\Models\Enums\UserRole;
use App\Models\User;

interface IAuthService
{
    public function hasRole(UserRole $roleToCheck): bool;
    
    public function logout(string $message): void;
    
    public function generateActionToken(): string;
    public function validateToken(string $token): bool;
    public function generateJWTToken(User $user): string;
    public function getUserFromToken(string $token): ?User;
    public function validatePassword(string $password): array;
    public function validateResetToken(User $user, string $token): bool;
    public function validateUserId(int $idToValidate, int $userId): bool;
}