<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth.js'

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'
const { authState } = useAuth()
const router = useRouter()

// Props
const props = defineProps({
  bookId: {
    type: [String, Number],
    required: true,
  },
  ownerId: {
    type: [String, Number],
    required: true,
  },
  requesterId: {
    type: [String, Number],
    required: true,
  },
})

// Emits
const emit = defineEmits(['close', 'redirect'])

// Form state
const isFormVisible = ref(false)
const isLoading = ref(false)
const useProfileAddress = ref(false)

const formData = reactive({
  street: '',
  city: '',
  state: '',
  zip: '',
  country: '',
})

// Toggle address form and load profile address if checked
const toggleAddressForm = async () => {
  if (useProfileAddress.value && authState.user?.id) {
    try {
      const response = await fetch(`${apiBaseUrl}/getProfileAddress/${authState.user.id}`, {
        credentials: 'include',
      })
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      
      const data = await response.json()
      
      formData.street = data.address || ''
      formData.zip = data.post_code || ''
      formData.state = data.state || ''
      formData.city = data.city || data.state || ''
      formData.country = data.country || ''
    } catch (error) {
      console.error('Error fetching profile address:', error)
      alert('Failed to load profile address. Please enter manually.')
    }
  } else {
    // Clear the form fields
    formData.street = ''
    formData.zip = ''
    formData.state = ''
    formData.city = ''
    formData.country = ''
  }
}

// Open the form
const openAddressForm = () => {
  isFormVisible.value = true
}

// Reset form and hide it
const closeForm = () => {
  isFormVisible.value = false
  resetForm()
  emit('close')
}

// Reset form data
const resetForm = () => {
  useProfileAddress.value = false
  formData.street = ''
  formData.city = ''
  formData.state = ''
  formData.zip = ''
  formData.country = ''
}

// Submit the form
const submitAddressForm = async () => {
  if (isLoading.value) return

  isLoading.value = true

  const requestData = {
    bookId: props.bookId,
    ownerId: props.ownerId,
    requesterId: props.requesterId,
    street: formData.street,
    zip: formData.zip,
    state: formData.state,
    city: formData.city,
    country: formData.country,
  }

  try {
    const response = await fetch(`${apiBaseUrl}/createBookRequest`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(requestData),
    })

    const text = await response.text()
    console.log('Raw response:', text)

    let data
    try {
      data = JSON.parse(text)
    } catch (e) {
      console.error('Server response was not JSON:', text)
      throw new Error('Server error: ' + text.substring(0, 100))
    }

    if (!response.ok) {
      throw new Error(data.message || 'Request failed')
    }

    console.log(data.message)
    emit('close')

    // Redirect to checkout if redirectUrl is provided
    if (data.redirectUrl) {
      emit('redirect', data.redirectUrl)
      router.push(data.redirectUrl)
    }
  } catch (error) {
    console.error('Error submitting book request:', error)
    alert('Error: ' + error.message)
  } finally {
    isLoading.value = false
  }
}

// Expose methods for parent component if needed
defineExpose({
  openAddressForm,
  closeForm,
})
onMounted(() => {
  openAddressForm()
})
</script>

<template>
  <section v-show="isFormVisible" class="flex flex-col mt-6">
    <h2 class="text-xl font-bold mb-4 text-colors">Shipping Address</h2>
    <div class="flex flex-col items-start">
      <label for="useProfileAddress" class="mb-2 text-sm font-medium text-colors">
        <input
          v-model="useProfileAddress"
          type="checkbox"
          id="useProfileAddress"
          name="useProfileAddress"
          @change="toggleAddressForm"
        />
        Use my profile address
      </label>
    </div>
    <form class="flex flex-col gap-4" @submit.prevent="submitAddressForm">
      <div class="flex flex-col items-start">
        <label for="street" class="mb-1 text-sm font-medium text-colors">Street Address</label>
        <input
          v-model="formData.street"
          type="text"
          id="street"
          name="street"
          class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
          placeholder="123 Main St"
          required
        />
      </div>
      <div class="flex flex-col items-start">
        <label for="city" class="mb-1 text-sm font-medium text-colors">City</label>
        <input
          v-model="formData.city"
          type="text"
          id="city"
          name="city"
          class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
          placeholder="City"
          required
        />
      </div>
      <div class="flex flex-col items-start">
        <label for="state" class="mb-1 text-sm font-medium text-colors">State/Province</label>
        <input
          v-model="formData.state"
          type="text"
          id="state"
          name="state"
          class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
          placeholder="State/Province"
          required
        />
      </div>
      <div class="flex flex-col items-start">
        <label for="zip" class="mb-1 text-sm font-medium text-colors">ZIP/Postal Code</label>
        <input
          v-model="formData.zip"
          type="text"
          id="zip"
          name="zip"
          class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
          placeholder="ZIP/Postal Code"
          required
        />
      </div>
      <div class="flex flex-col items-start">
        <label for="country" class="mb-1 text-sm font-medium text-colors">Country</label>
        <input
          v-model="formData.country"
          type="text"
          id="country"
          name="country"
          class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
          placeholder="Country"
          required
        />
      </div>
      <div class="flex flex-row gap-4 mt-4">
        <button
          type="button"
          @click="closeForm"
          class="button_neutral"
          :disabled="isLoading"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="button_primary mt-4"
          :disabled="isLoading"
        >
          {{ isLoading ? 'Processing...' : 'Request Book' }}
        </button>
      </div>
    </form>
  </section>
</template>