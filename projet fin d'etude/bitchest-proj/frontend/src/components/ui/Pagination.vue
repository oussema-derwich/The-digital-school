<template>
  <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
      <button
        @click="$emit('update:page', page - 1)"
        :disabled="page === 1"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        :class="{ 'opacity-50 cursor-not-allowed': page === 1 }"
      >
        Previous
      </button>
      <button
        @click="$emit('update:page', page + 1)"
        :disabled="page === totalPages"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        :class="{ 'opacity-50 cursor-not-allowed': page === totalPages }"
      >
        Next
      </button>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium">{{ startIndex + 1 }}</span>
          to
          <span class="font-medium">{{ Math.min(endIndex, total) }}</span>
          of
          <span class="font-medium">{{ total }}</span>
          results
        </p>
      </div>
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <button
            @click="$emit('update:page', page - 1)"
            :disabled="page === 1"
            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
            :class="{ 'opacity-50 cursor-not-allowed': page === 1 }"
          >
            <span class="sr-only">Previous</span>
            <span class="h-5 w-5">←</span>
          </button>

          <template v-for="p in visiblePages" :key="p">
            <span
              v-if="p === '...'"
              class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300"
            >
              ...
            </span>
            <button
              v-else
              @click="$emit('update:page', Number(p))"
              :class="[
                p === page
                  ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
                  : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
                'relative inline-flex items-center px-4 py-2 text-sm font-semibold'
              ]"
            >
              {{ p }}
            </button>
          </template>

          <button
            @click="$emit('update:page', page + 1)"
            :disabled="page === totalPages"
            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
            :class="{ 'opacity-50 cursor-not-allowed': page === totalPages }"
          >
            <span class="sr-only">Next</span>
            <span class="h-5 w-5">→</span>
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  page: number
  perPage: number
  total: number
}>()

const emits = defineEmits<{
  (e: 'update:page', page: number): void
}>()

const totalPages = computed(() => Math.ceil(props.total / props.perPage))
const startIndex = computed(() => (props.page - 1) * props.perPage)
const endIndex = computed(() => startIndex.value + props.perPage)

const visiblePages = computed(() => {
  const delta = 2
  const range: (number | string)[] = []
  const rangeWithDots: (number | string)[] = []
  let l: number | undefined

  if (totalPages.value <= 1) return [1]

  range.push(1)

  for (let i = props.page - delta; i <= props.page + delta; i++) {
    if (i < totalPages.value && i > 1) {
      range.push(i)
    }
  }

  if (totalPages.value > 1) range.push(totalPages.value)

  for (const i of range) {
    if (l !== undefined) {
      if (Number(i) - Number(l) === 2) {
        rangeWithDots.push(Number(l) + 1)
      } else if (Number(i) - Number(l) !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = Number(i)
  }

  return rangeWithDots
})
</script>