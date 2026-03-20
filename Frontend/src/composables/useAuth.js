import { reactive, computed } from 'vue'
import axios from '../utils/axios.js'

const authState = reactive({
  isLoggedIn: false,
  user: null,
  loading: true,
  token: localStorage.getItem('authToken') || null,
});

const isLoggedIn = computed(() => !!authState.user);

// Set up axios to send JWT token with every request
const setupAxiosInterceptors = () => {
  axios.interceptors.request.use((config) => {
    if (authState.token) {
      config.headers.Authorization = `Bearer ${authState.token}`;
      console.log('JWT token added to request:', config.url);
    } else {
      console.warn('No JWT token available for request:', config.url);
    }
    return config;
  }, (error) => {
    return Promise.reject(error);
  });

  // Handle 401 responses - token expired or invalid
  axios.interceptors.response.use(
    (response) => response,
    (error) => {
      if (error.response?.status === 401) {
        // Clear auth on unauthorized
        authState.token = null;
        authState.user = null;
        authState.isLoggedIn = false;
        localStorage.removeItem('authToken');
      }
      return Promise.reject(error);
    }
  );
};

async function fetchLoggedInUser() {
  if (!authState.token) {
    authState.isLoggedIn = false;
    authState.user = null;
    authState.loading = false;
    return;
  }

  authState.loading = true;
  try {
    const response = await axios.get(`/getLoggedInUser`);
    
    if (response.data.success) {
      authState.isLoggedIn = true;
      authState.user = response.data.user;
      console.log('Logged in user:', authState.user);
    } else {
      authState.isLoggedIn = false;
      authState.user = null;
      authState.token = null;
      localStorage.removeItem('authToken');
    }
  } catch (error) {
    console.error('Error fetching logged in user:', error);
    authState.isLoggedIn = false;
    authState.user = null;
    authState.token = null;
    localStorage.removeItem('authToken');
  } finally {
    authState.loading = false;
  }
}

// Store token and user after login
function setAuthToken(token, user) {
  authState.token = token;
  authState.user = user;
  authState.isLoggedIn = true;
  localStorage.setItem('authToken', token);
}

// Clear auth state on logout
function clearAuth() {
  authState.token = null;
  authState.user = null;
  authState.isLoggedIn = false;
  localStorage.removeItem('authToken');
}

export function useAuth() {
  return {
    authState,
    isLoggedIn,
    fetchLoggedInUser,
    setAuthToken,
    clearAuth,
    setupAxiosInterceptors,
  }
}