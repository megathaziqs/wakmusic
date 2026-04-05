<template>
  <button 
    :class="[
      'font-medium rounded-sm transition-all duration-200 inline-flex items-center justify-center gap-2',
      sizeClasses,
      variantClasses,
      {
        'opacity-50 cursor-not-allowed': disabled,
        'hover:shadow-md': !disabled && variant !== 'ghost'
      }
    ]"
    :disabled="disabled"
    @click="$emit('click')"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'danger', 'ghost', 'outline'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  disabled: Boolean,
});

defineEmits(['click']);

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-base',
    lg: 'px-6 py-3 text-lg',
  };
  return sizes[props.size];
});

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-[#F53003] text-white hover:bg-[#e63002] dark:bg-[#F61500] dark:hover:bg-[#d41400]',
    secondary: 'bg-gray-200 text-[#1b1b18] hover:bg-gray-300 dark:bg-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:bg-[#4a4a46]',
    danger: 'bg-red-600 text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800',
    ghost: 'text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-100 dark:hover:bg-[#3E3E3A]',
    outline: 'border border-[#19140035] text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]',
  };
  return variants[props.variant];
});
</script>
