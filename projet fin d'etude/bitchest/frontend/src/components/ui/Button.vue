<template>
  <button 
    :class="[
      'rounded-md px-4 py-2 font-medium transition-all duration-250 focus:outline-none focus:ring-2 focus:ring-offset-2',
      variant === 'primary' && 'bg-primary text-white hover:bg-primary/90 focus:ring-primary/50',
      variant === 'success' && 'bg-success text-white hover:bg-success/90 focus:ring-success/50',
      variant === 'danger' && 'bg-danger text-white hover:bg-danger/90 focus:ring-danger/50',
    ]"
    v-bind="$attrs"
  >
    <slot></slot>
  </button>
</template>

<script setup lang="ts">
defineProps<{
  variant?: 'primary' | 'success' | 'danger'
}>();
</script>
