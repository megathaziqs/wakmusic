<template>
  <input 
    :class="[
      'w-full px-4 py-2 rounded-sm border transition-colors',
      'bg-white dark:bg-[#1D1D1A] text-[#1b1b18] dark:text-[#EDEDEC]',
      'border-[#e3e3e0] dark:border-[#3E3E3A]',
      'placeholder-gray-400 dark:placeholder-gray-500',
      'focus:outline-none focus:ring-2 focus:ring-[#F53003] focus:border-transparent'
    ]"
    :type="type"
    :placeholder="placeholder"
    v-bind="$attrs"
  />
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'text'
  },
  placeholder: String,
});
</script>
