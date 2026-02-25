const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'
import { reactive, computed } from 'vue'
import axios from 'axios'

const authState = reactive({
  isLoggedIn: false,
  user: null,
  loading: true,
});

const isLoggedIn = computed(() => !!authState.user);

async function fetchLoggedInUser() {
  authState.loading = true
  try {
    const response = await axios.get(`${apiBaseUrl}/getLoggedInUser`, { withCredentials: true })
    if (response.data.success) {
      authState.isLoggedIn = true
      authState.user = response.data.user
      console.log('Logged in user:', authState.user);
    } else {
      authState.isLoggedIn = false
      authState.user = null
      console.log(response.data);
    }
  } catch (error) {
    console.error('Error fetching logged in user:', error)
    authState.isLoggedIn = false
    authState.user = null
  } finally {
    authState.loading = false
  }
}

export function useAuth() {
  return {
    authState,
    isLoggedIn,
    fetchLoggedInUser,
  }
}