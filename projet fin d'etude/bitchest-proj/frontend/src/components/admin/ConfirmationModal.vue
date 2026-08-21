<template>
  <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
    <div class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div
          class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
        >
          <div class="sm:flex sm:items-start">
            <div
              class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full"
              :class="{
                'bg-red-100': type === 'delete',
                'bg-orange-100': type === 'suspend'
              }"
            >
              <span
                class="h-6 w-6 text-2xl"
                :class="{
                  'text-red-600': type === 'delete',
                  'text-orange-600': type === 'suspend'
                }"
              >
                {{ type === 'delete' ? '⚠️' : '⏸️' }}
              </span>
            </div>
            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
              <h3 class="text-lg font-semibold leading-6 text-gray-900">
                {{ title }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">{{ message }}</p>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="$emit('confirm')"
              class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto"
              :class="{
                'bg-red-600 hover:bg-red-500': type === 'delete',
                'bg-orange-600 hover:bg-orange-500': type === 'suspend'
              }"
            >
              {{ type === 'delete' ? 'Delete' : 'Suspend' }}
            </button>
            <button
              type="button"
              @click="$emit('close')"
              class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface User {
  id: string
  name: string
  email: string
}

const props = defineProps<{
  type: 'delete' | 'suspend'
  user: User | null
}>()

const title = computed(() => (props.type === 'delete' ? 'Delete User Account' : 'Suspend User Account'))

const message = computed(() => {
  if (!props.user) return ''
  if (props.type === 'delete') {
    return `Are you sure you want to delete ${props.user.name}'s account? This action cannot be undone.`
  }
  return `Are you sure you want to suspend ${props.user.name}'s account? The user will not be able to access their account until it is reactivated.`
})
</script>