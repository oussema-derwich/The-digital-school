<template>
  <div>
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
    </div>

    <div v-else-if="error" class="flex flex-col items-center py-8">
      <div class="text-red-500 mb-4">{{ error }}</div>
      <button
        @click="$emit('retry')"
        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
      >
        Retry
      </button>
    </div>

    <div v-else>
      <slot></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  loading: boolean;
  error?: string;
}>();

defineEmits<{
  (e: 'retry'): void;
}>();
</script>