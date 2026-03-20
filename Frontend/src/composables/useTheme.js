import { ref } from 'vue'
import axios from 'axios'

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'

const isDark = ref(false)
let initialized = false

// Initialize theme from localStorage or system preference
const initializeTheme = () => {
  if (initialized) return
  
  console.log('Initializing theme...')
  
  // Check localStorage first
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme) {
    isDark.value = savedTheme === 'dark'
    console.log('Theme loaded from localStorage:', savedTheme)
  } else {
    // Fall back to system preference
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    console.log('Theme loaded from system preference:', isDark.value ? 'dark' : 'light')
  }
  
  // Apply theme to DOM
  applyTheme()
  initialized = true
}

// Apply theme to document
const applyTheme = () => {
  console.log('Applying theme:', isDark.value ? 'dark' : 'light')
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}

// Toggle theme and save to backend
const toggleTheme = async () => {
  console.log('Toggling theme from', isDark.value ? 'dark' : 'light')
  isDark.value = !isDark.value
  applyTheme()
  
  // Save to localStorage
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
  
  // Save to backend cookie via PHP endpoint
  try {
    const theme = isDark.value ? 'dark' : 'light'
    console.log('Saving theme to backend:', theme)
    await axios.post(`${apiBaseUrl}/setTheme`, 
      new URLSearchParams({ theme }).toString(),
      {
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }
    )
  } catch (error) {
    console.error('Error saving theme preference:', error)
  }
}

export function useTheme() {
  return {
    isDark,
    toggleTheme,
    initializeTheme,
  }
}
