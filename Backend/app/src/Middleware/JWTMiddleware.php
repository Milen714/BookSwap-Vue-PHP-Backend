<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use App\config\Secrets;

class JWTMiddleware
{
    /**
     * Validate JWT token from Authorization header
     * Returns decoded token or throws exception
     */
    public static function validateToken(): \stdClass
    {
        // Try to get Authorization header from multiple sources
        $authHeader = null;
        
        // First try getallheaders() (most reliable)
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }
        
        // Fallback to $_SERVER['HTTP_AUTHORIZATION']
        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!$authHeader) {
            throw new \Exception('Missing Authorization header', 401);
        }

        // Extract token from "Bearer <token>" format
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            throw new \Exception('Invalid Authorization header format', 401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key(Secrets::$secretKey, Secrets::JWT_ALGORITHM));
            return $decoded;
        } catch (ExpiredException $e) {
            throw new \Exception('Token has expired', 401);
        } catch (\Exception $e) {
            throw new \Exception('Invalid token: ' . $e->getMessage(), 401);
        }
    }

    /**
     * Get user ID from JWT token
     */
    public static function getUserIdFromToken(): int
    {
        $decoded = self::validateToken();
        return $decoded->data->id ?? null;
    }

    /**
     * Get the raw JWT token from Authorization header
     */
    public static function getTokenFromHeader(): ?string
    {
        // Try to get Authorization header from multiple sources
        $authHeader = null;
        
        // First try getallheaders() (most reliable)
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }
        
        // Fallback to $_SERVER['HTTP_AUTHORIZATION']
        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!$authHeader) {
            return null;
        }

        // Extract token from "Bearer <token>" format
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }
}