<?php

namespace App\Services\Interfaces;
use App\Models\User;

/**
 * IUserService
 * 
 * User service interface defining contract for user data access,
 * authentication, and swap token management operations.
 */
interface IUserService {
    /**
     * Retrieve all users from the system
     * 
     * @return array List of all User objects
     */
    public function getAllUsers(): array;
    
    /**
     * Find user by email address
     * 
     * @param string $email Email to search for
     * @return User|null User object if found, null otherwise
     */
    public function getUserByEmail(string $email): ?User;
    
    /**
     * Authenticate user with email and password
     * 
     * @param string $email User email
     * @param string $password User password
     * @return User|null Authenticated user object if credentials match, null otherwise
     */
    public function authenticateUser(string $email, string $password): ?User;
    
    /**
     * Retrieve user by ID
     * 
     * @param int $id User ID
     * @return User|null User object if found, null otherwise
     */
    public function getUserById(int $id): ?User;
    
    /**
     * Create new user in the system
     * 
     * @param User $user User object to create
     * @return bool True if creation successful, false otherwise
     */
    public function createUser(User $user): bool;
    
    /**
     * Update existing user data
     * 
     * @param User $user User object with updated data
     * @return bool True if update successful, false otherwise
     */
    public function updateUser(User $user): bool;

    /**
     * Set whether a user account is active
     *
     * @param int $userId User ID
     * @param bool $isActive Active state to save
     * @return bool True if update successful, false otherwise
     */
    public function setUserActive(int $userId, bool $isActive): bool;
    
    /**
     * Deduct one swap token from user balance
     * 
     * @param int $userId User ID
     * @return bool True if deduction successful, false otherwise
     */
    public function deductSwapToken(int $userId): bool;
    
    /**
     * Add swap tokens to user balance
     * 
     * @param int $userId User ID
     * @param int $amount Number of tokens to add
     * @return bool True if addition successful, false otherwise
     */
    public function addSwapTokens(int $userId, int $amount): bool;
    
    /**
     * Count number of active book listings for a user
     * 
     * @param int $userId User ID
     * @return int Number of books listed by user
     */
    public function numberOfListedBooks(int $userId): int;

    /**
     * Retrieve admin analytics for users and swaps
     *
     * @return array Analytics summary and breakdowns
     */
    public function getAdminAnalytics(): array;
}