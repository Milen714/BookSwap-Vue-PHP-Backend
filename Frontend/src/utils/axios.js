import axios from 'axios';
import config from '../config.js';

const AUTH_TOKEN_KEY = 'authToken';
const LEGACY_AUTH_TOKEN_KEY = 'auth_token';

// Create axios instance with base URL
const apiClient = axios.create({
  baseURL: config.apiDomain,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Store token in memory (also persisted in localStorage for page reloads)
let authToken =
  localStorage.getItem(AUTH_TOKEN_KEY) ||
  localStorage.getItem(LEGACY_AUTH_TOKEN_KEY) ||
  null;

function getStoredAuthToken() {
  const storedToken =
    localStorage.getItem(AUTH_TOKEN_KEY) ||
    localStorage.getItem(LEGACY_AUTH_TOKEN_KEY) ||
    null;

  authToken = storedToken;
  return storedToken;
}

// Request interceptor to add token to all requests
apiClient.interceptors.request.use(
  (config) => {
    const token = getStoredAuthToken();

    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    } else if (config.headers?.Authorization) {
      delete config.headers.Authorization;
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
    localStorage.setItem(AUTH_TOKEN_KEY, token);
    localStorage.removeItem(LEGACY_AUTH_TOKEN_KEY);
  } else {
    localStorage.removeItem(AUTH_TOKEN_KEY);
    localStorage.removeItem(LEGACY_AUTH_TOKEN_KEY);
  }
}

/**
 * Get the current authentication token
 * @returns {string|null} The authentication token or null
 */
export function getAuthToken() {
  return getStoredAuthToken();
}

export default apiClient;
