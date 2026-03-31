<script setup>
defineProps({
  label: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'text'
  },
  id: {
    type: String,
    required: true
  },
  name: {
    type: String,
    required: true
  },
  modelValue: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  placeholder: {
    type: String,
    default: ''
  },
  wrapperClass: {
    type: String,
    default: 'input_group'
  }
});

defineEmits(['update:modelValue']);
</script>

<template>
  <article :class="wrapperClass">
    <label class="block text-sm font-medium text-colors mb-1" :for="id">{{ label }}</label>
    <input
      class="form_input"
      :type="type"
      :id="id"
      :name="name"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      @input="$emit('update:modelValue', $event.target.value)"
    />
  </article>
</template>
