<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import ErrorCard from '@/components/molecules/ErrorCard.vue'
import SuccessCard from '@/components/molecules/SuccessCard.vue'
import InputGroup from '@/components/organisms/InputGroup.vue'
import PasswordStrengthFeedback from '@/components/PasswordStrengthFeedback.vue'
import axios from '@/utils/axios.js'
import { getPasswordFeedback, isPasswordStrong } from '@/utils/PasswordStrength.js'

const router = useRouter()

const showError = ref(false)
const showSuccess = ref(false)
const message = ref('')
const email = ref('')
const password = ref('')
const fname = ref('')
const lname = ref('')
const country = ref('')
const address = ref('')
const state = ref('')
const post_code = ref('')
const phone_number = ref('')

const passwordFeedback = computed(() => getPasswordFeedback(password.value))
const isPasswordValid = computed(() => isPasswordStrong(password.value))

const handleSignup = async (event) => {
  event.preventDefault()
  showError.value = false
  showSuccess.value = false
  message.value = ''

  // Validate password strength
  if (!isPasswordValid.value) {
    showError.value = true
    message.value = 'Password does not meet all requirements'
    return
  }

  const data = {
    email: email.value,
    password: password.value,
    fname: fname.value,
    lname: lname.value,
    country: country.value,
    address: address.value,
    state: state.value,
    post_code: post_code.value,
    phone_number: phone_number.value,
  }


  try {
    const response = await axios.post(`/signUp`, data, {
      headers: {
        'Content-Type': 'application/json',
      },
    });
    const responseData = response.data;
    if (responseData?.success) {
      showSuccess.value = true
      message.value = responseData.message || 'Signup successful! Please log in.'
      setTimeout(() => {
        router.push({
          path: '/login',
          query: { signupMessage: message.value },
        })
      })
    } else {
      showError.value = true
      console.error('Signup failed:', responseData);
      message.value = responseData?.message 
    }
  } catch (error) {
    showError.value = true
    console.error('Error during signup:', error)
    console.error('Error response:', error?.response?.data)
    
    // Handle nested error structure: {error: {message: "..."}}
    const errorData = error?.response?.data?.error || error?.response?.data
    message.value = errorData?.message || error.message || 'An error occurred during signup'
  }
}
</script>

<template>
  <article class="max-w-md mx-auto bg-colors-secondary-light text-colors p-6 rounded-md shadow-md">
    <h1 class="text-center m-5 text-colors font-serif text-2xl">Signup Page</h1>

    <form @submit="handleSignup">
      <InputGroup 
        label="Email:"
        type="email"
        id="email"
        name="email"
        v-model="email"
        :required="true"
        wrapper-class="mb-4"
      />

      <article class="mb-4">
        <InputGroup 
          label="Password:"
          type="password"
          id="password"
          name="password"
          v-model="password"
          :required="true"
          wrapper-class="mb-2"
        />
        
        <!-- Password Strength Feedback Component -->
        <PasswordStrengthFeedback :feedback="passwordFeedback" />
      </article>

      <InputGroup 
        label="First Name:"
        type="text"
        id="fname"
        name="fname"
        v-model="fname"
        :required="true"
        wrapper-class="mb-4"
      />

      <InputGroup 
        label="Last Name:"
        type="text"
        id="lname"
        name="lname"
        v-model="lname"
        :required="true"
        wrapper-class="mb-4"
      />

      <InputGroup 
        label="Country:"
        type="text"
        id="country"
        name="country"
        v-model="country"
        :required="true"
        wrapper-class="mb-4"
      />

      <InputGroup 
        label="Address:"
        type="text"
        id="address"
        name="address"
        v-model="address"
        :required="true"
        wrapper-class="mb-4"
      />

      <InputGroup 
        label="State/Province:"
        type="text"
        id="state"
        name="state"
        v-model="state"
        :required="true"
        wrapper-class="mb-4"
      />

      <InputGroup 
        label="PostCode:"
        type="text"
        id="post_code"
        name="post_code"
        v-model="post_code"
        :required="true"
        wrapper-class="mb-4"
      />

      <InputGroup 
        label="Phone Number:"
        type="tel"
        id="phone_number"
        name="phone_number"
        v-model="phone_number"
        placeholder="+1 (555) 123-4567"
        wrapper-class="mb-5"
      />

      <button 
        class="w-full rounded-md bg-blue-600 text-white py-2 font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed" 
        type="submit"
        :disabled="password && !isPasswordValid"
      >
        Signup
      </button>
    </form>

    <ErrorCard v-if="showError" :message="message" />
    <SuccessCard v-if="showSuccess" :message="message" />

    <article class="mt-4 text-center text-colors text-sm">
      <p>Already have an account? <RouterLink class="text-blue-600 hover:underline" to="/login">Log in here</RouterLink>.</p>
    </article>
  </article>
</template>
