import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from '@/utils/axios.js'

export const useProfileSettingsStore = defineStore('profileSettings', () => {
  // Profile Information State
  const profileInfo = ref({
    phone_number: '',
    bio: ''
  })

  // Address Information State
  const addressInfo = ref({
    address: '',
    state: '',
    country: '',
    post_code: ''
  })

  // UI State
  const isLoading = ref(false)
  const isSaving = ref(false)
  const showError = ref(false)
  const showSuccess = ref(false)
  const message = ref('')

  // Initialize settings from auth store
  const initializeFromAuthStore = (user) => {
    if (user) {
      profileInfo.value = {
        phone_number: user.phone_number || '',
        bio: user.bio || ''
      }
      addressInfo.value = {
        address: user.address || '',
        state: user.state || '',
        country: user.country || '',
        post_code: user.post_code || ''
      }
    }
  }

  // Update Profile Information
  const updateProfileInfo = async (userId, data) => {
    isSaving.value = true
    showError.value = false
    showSuccess.value = false
    message.value = ''

    try {
      const response = await axios.post(`/updateProfile`, {
        userId: userId,
        phone_number: data.phone_number,
        bio: data.bio
      }, {
        headers: {
          'Content-Type': 'application/json',
        },
      })

      const responseData = response.data

      if (responseData?.success) {
        profileInfo.value = {
          phone_number: data.phone_number,
          bio: data.bio
        }
        showSuccess.value = true
        message.value = responseData.message || 'Profile updated successfully!'

        setTimeout(() => {
          showSuccess.value = false
          message.value = ''
        }, 5000)

        return true
      } else {
        showError.value = true
        message.value = responseData?.message || 'Failed to update profile'
        return false
      }
    } catch (error) {
      showError.value = true
      console.error('Error updating profile:', error)

      let errorMessage = 'An error occurred while updating profile'
      if (error?.response?.data) {
        const data = error.response.data
        if (data?.error && typeof data.error === 'object' && data.error.error) {
          errorMessage = data.error.error
        } else if (data?.error && typeof data.error === 'string') {
          errorMessage = data.error
        } else if (data?.message) {
          errorMessage = data.message
        }
      } else if (error?.message) {
        errorMessage = error.message
      }

      message.value = errorMessage
      return false
    } finally {
      isSaving.value = false
    }
  }

  // Update Address Information
  const updateAddressInfo = async (userId, data) => {
    isSaving.value = true
    showError.value = false
    showSuccess.value = false
    message.value = ''

    try {
      const response = await axios.post(`/updateAddress`, {
        userId: userId,
        address: data.address,
        state: data.state,
        country: data.country,
        post_code: data.post_code
      }, {
        headers: {
          'Content-Type': 'application/json',
        },
      })

      const responseData = response.data

      if (responseData?.success) {
        addressInfo.value = {
          address: data.address,
          state: data.state,
          country: data.country,
          post_code: data.post_code
        }
        showSuccess.value = true
        message.value = responseData.message || 'Address updated successfully!'

        setTimeout(() => {
          showSuccess.value = false
          message.value = ''
        }, 5000)

        return true
      } else {
        showError.value = true
        message.value = responseData?.message || 'Failed to update address'
        return false
      }
    } catch (error) {
      showError.value = true
      console.error('Error updating address:', error)

      let errorMessage = 'An error occurred while updating address'
      if (error?.response?.data) {
        const data = error.response.data
        if (data?.error && typeof data.error === 'object' && data.error.error) {
          errorMessage = data.error.error
        } else if (data?.error && typeof data.error === 'string') {
          errorMessage = data.error
        } else if (data?.message) {
          errorMessage = data.message
        }
      } else if (error?.message) {
        errorMessage = error.message
      }

      message.value = errorMessage
      return false
    } finally {
      isSaving.value = false
    }
  }

  // Change Password
  const changePassword = async (userId, data) => {
    isSaving.value = true
    showError.value = false
    showSuccess.value = false
    message.value = ''

    try {
      const response = await axios.post(`/changePassword`, {
        userId: userId,
        old_password: data.old_password,
        new_password: data.new_password,
        confirm_password: data.confirm_password
      }, {
        headers: {
          'Content-Type': 'application/json',
        },
      })

      const responseData = response.data

      if (responseData?.success) {
        showSuccess.value = true
        message.value = responseData.message || 'Password changed successfully!'

        setTimeout(() => {
          showSuccess.value = false
          message.value = ''
        }, 5000)

        return true
      } else {
        showError.value = true
        message.value = responseData?.message || 'Failed to change password'
        return false
      }
    } catch (error) {
      showError.value = true
      console.error('Error changing password:', error)

      let errorMessage = 'An error occurred while changing password'
      if (error?.response?.data) {
        const data = error.response.data
        if (data?.error && typeof data.error === 'object' && data.error.error) {
          errorMessage = data.error.error
        } else if (data?.error && typeof data.error === 'string') {
          errorMessage = data.error
        } else if (data?.message) {
          errorMessage = data.message
        }
      } else if (error?.message) {
        errorMessage = error.message
      }

      message.value = errorMessage
      return false
    } finally {
      isSaving.value = false
    }
  }

  // Reset state
  const resetState = () => {
    showError.value = false
    showSuccess.value = false
    message.value = ''
  }

  return {
    // State
    profileInfo,
    addressInfo,
    isLoading,
    isSaving,
    showError,
    showSuccess,
    message,
    
    // Actions
    initializeFromAuthStore,
    updateProfileInfo,
    updateAddressInfo,
    changePassword,
    resetState
  }
})
