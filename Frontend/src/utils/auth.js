/**
 * Authentication utility functions
 * Handles token storage and retrieval
 */

const TOKEN_KEY = 'auth_token';

/**
 * Save authentication token to localStorage
 * @param {string} token - The authentication token
 */
export function saveToken(token) {
  localStorage.setItem(TOKEN_KEY, token);
}

/**
 * Get authentication token from localStorage
 * @returns {string|null} The authentication token or null if not found
 */
export function getToken() {
  return localStorage.getItem(TOKEN_KEY);
}

/**
 * Remove authentication token from localStorage
 */
export function removeToken() {
  localStorage.removeItem(TOKEN_KEY);
}

/**
 * Check if user is authenticated (has a token)
 * @returns {boolean} True if token exists
 */
export function isAuthenticated() {
  return !!getToken();
}
