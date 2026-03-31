/**
 * Password validation utility for signup forms
 */

export const passwordRequirements = {
  minLength: 8,
  uppercase: /[A-Z]/,
  lowercase: /[a-z]/,
  number: /\d/,
  special: /[\W_]/
}

/**
 * Check if password meets all requirements
 * @param {string} password - Password to validate
 * @returns {Object} Object with validation results
 */
export function validatePassword(password) {
  return {
    length: password.length >= passwordRequirements.minLength,
    uppercase: passwordRequirements.uppercase.test(password),
    lowercase: passwordRequirements.lowercase.test(password),
    number: passwordRequirements.number.test(password),
    special: passwordRequirements.special.test(password)
  }
}

/**
 * Check if password is strong (all requirements met)
 * @param {string} password - Password to validate
 * @returns {boolean} True if all requirements are met
 */
export function isPasswordStrong(password) {
  const checks = validatePassword(password)
  return Object.values(checks).every(Boolean)
}

/**
 * Get human-readable feedback on password requirements
 * @param {string} password - Password to validate
 * @returns {Array} Array of requirement objects with status
 */
export function getPasswordFeedback(password) {
  const checks = validatePassword(password)
  
  return [
    {
      id: 'length',
      text: `At least ${passwordRequirements.minLength} characters`,
      valid: checks.length
    },
    {
      id: 'uppercase',
      text: 'Contains an uppercase letter',
      valid: checks.uppercase
    },
    {
      id: 'lowercase',
      text: 'Contains a lowercase letter',
      valid: checks.lowercase
    },
    {
      id: 'number',
      text: 'Contains a number',
      valid: checks.number
    },
    {
      id: 'special',
      text: 'Contains a special character',
      valid: checks.special
    }
  ]
}