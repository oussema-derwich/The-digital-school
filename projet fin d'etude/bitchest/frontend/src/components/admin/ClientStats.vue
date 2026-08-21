<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div
      v-for="stat in stats"
      :key="stat.title"
      class="bg-white rounded-xl p-6 shadow-sm hover:transform hover:scale-[1.03] transition-all duration-200"
      :class="stat.bgColor"
    >
      <div class="flex items-center justify-between">
        <div class="text-4xl">{{ stat.icon }}</div>
        <div class="text-2xl font-bold">{{ stat.value }}</div>
      </div>
      <div class="mt-4 text-sm text-gray-600">{{ stat.title }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Stat {
  title: string;
  value: string;
  icon: string;
  bgColor: string;
}

const props = defineProps<{ stats: Stat[] }>();
</script>