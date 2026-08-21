<template>
  <div class="bg-white rounded-md shadow-card p-6">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm font-medium text-secondary/70">{{ title }}</p>
        <div class="mt-2 flex items-baseline">
          <p class="text-2xl font-semibold text-primary">
            {{ value }}
          </p>
          <p v-if="change !== undefined && change !== null" :class="[
            'ml-2 text-sm',
            changeNumeric >= 0 ? 'text-success' : 'text-danger'
          ]">
            {{ formatChange(changeNumeric) }}
          </p>
        </div>
      </div>
      <div class="rounded-full p-3" :class="iconBgClass">
        <component :is="icon" class="h-6 w-6" :class="iconClass" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  title: string;
  value: string | number;
  change?: string | number;
  icon?: any;
  variant?: 'primary' | 'success' | 'warning' | 'danger';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary'
});

const iconBgClass = computed(() => {
  const variants = {
    primary: 'bg-primary/10',
    success: 'bg-success/10',
    warning: 'bg-warning/10',
    danger: 'bg-danger/10'
  };
  return variants[props.variant];
});

const iconClass = computed(() => {
  const variants = {
    primary: 'text-primary',
    success: 'text-success',
    warning: 'text-warning',
    danger: 'text-danger'
  };
  return variants[props.variant];
});

const changeNumeric = computed(() => {
  const v = props.change
  if (v === undefined || v === null) return 0
  return typeof v === 'number' ? v : parseFloat(String(v))
})

const formatChange = (value: number) => {
  const num = Number(value || 0)
  return `${num >= 0 ? '+' : ''}${num}%`
}
</script>
