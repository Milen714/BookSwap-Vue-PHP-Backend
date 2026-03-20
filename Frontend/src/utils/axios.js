import axios from 'axios';
import config from '../config.js';

// Create axios instance with base URL
const apiClient = axios.create({
  baseURL: config.apiDomain,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Store token in memory (also persisted in localStorage for page reloads)
let authToken = localStorage.getItem('auth_token') || null;

// Request interceptor to add token to all requests
apiClient.interceptors.request.use(
  (config) => {
    if (authToken) {
      config.headers.Authorization = `Bearer ${authToken}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

/**
 * Set the authentication token
 * This will be automatically added to all subsequent requests
 * @param {string} token - The authentication token
 */
export function setAuthToken(token) {
  authToken = token;
  if (token) {
    localStorage.setItem('auth_token', token);
  } else {
    localStorage.removeItem('auth_token');
  }
}

/**
 * Get the current authentication token
 * @returns {string|null} The authentication token or null
 */
export function getAuthToken() {
  return authToken;
}

export default apiClient;
