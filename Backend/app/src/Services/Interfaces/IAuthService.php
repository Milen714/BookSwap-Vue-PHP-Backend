<?php
namespace App\Services\Interfaces;

use App\Models\Enums\UserRole;
use App\Models\User;

/**
 * IAuthService
 * 
 * Authentication service interface defining contract for user authentication,
 * JWT token management, password validation, and password reset functionality.
 */
interface IAuthService
{
    /**
     * Check if the current user has a specific role
     * 
     * @param UserRole $roleToCheck The role to validate against
     * @return bool True if user has the role, false otherwise
     */
    public function hasRole(UserRole $roleToCheck): bool;
    
    /**
     * Log out user and destroy session
     * 
     * @param string $message Message to display after logout
     * @return void
     */
    public function logout(string $message): void;
    
    /**
     * Generate a secure action token for user operations
     * 
     * @return string Generated action token
     */
    public function generateActionToken(): string;
    
    /**
     * Validate JWT token integrity and expiration
     * 
     * @param string $token JWT token to validate
     * @return bool True if token is valid, false otherwise
     */
    public function validateToken(string $token): bool;
    
    /**
     * Generate JWT token for authenticated user
     * 
     * @param User $user User to create token for
     * @return string Encoded JWT token
     */
    public function generateJWTToken(User $user): string;
    
    /**
     * Extract user information from valid JWT token
     * 
     * @param string $token JWT token to decode
     * @return User|null User object if token is valid, null otherwise
     */
    public function getUserFromToken(string $token): ?User;
    
    /**
     * Validate password meets security requirements
     * 
     * @param string $password Password to validate
     * @return array Array with 'valid' bool and 'errors' array of validation failures
     */
    public function validatePassword(string $password): array;
    
    /**
     * Validate password reset token is valid and not expired
     * 
     * @param User $user User attempting password reset
     * @param string $token Reset token to validate
     * @return bool True if token is valid and not expired
     */
    public function validateResetToken(?User $user, string $token): bool;
    
    /**
     * Validate that a user ID matches the current authenticated user
     * 
     * @param int $idToValidate User ID to check
     * @param int $userId Current authenticated user ID
     * @return bool True if IDs match, false otherwise
     */
    public function validateUserId(int $idToValidate, int $userId): bool;
    
    /**
     * Generate a secure random token
     * 
     * @param int $length Token length in bytes
     * @return string Base64 encoded secure token
     */
    public function generateSecureToken(int $length = 32): string;
    
    /**
     * Generate password reset token and update user with expiry
     * 
     * @param User $user User to generate token for
     * @return string Generated reset token
     */
    public function generatePasswordResetToken(User $user): string;
}