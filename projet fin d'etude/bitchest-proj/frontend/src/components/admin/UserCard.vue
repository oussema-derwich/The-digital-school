<template>
  <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
    <div class="flex flex-col items-center">
      <!-- Avatar auto-généré -->
      <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-2xl mb-4">
        {{ getInitials(user.name) }}
      </div>

      <!-- Informations utilisateur -->
      <div class="text-center mb-6">
        <h3 class="font-semibold text-lg">{{ user.name }}</h3>
        <p class="text-gray-500 text-sm truncate max-w-[200px]">{{ user.email }}</p>
      </div>

      <!-- Détails -->
      <div class="w-full space-y-2 mb-6">
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Balance:</span>
          <span class="font-medium">{{ formatCurrency(user.balance) }}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Role:</span>
          <span class="font-medium uppercase text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
            {{ user.role }}
          </span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">KYC Expiry:</span>
          <span class="font-medium">{{ formatDate(user.kycExpiry) }}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Registered:</span>
          <span class="font-medium">{{ formatDate(user.createdAt) }}</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-2 w-full">
        <button
          @click="$emit('view-details', user.id)"
          class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm transition-colors duration-200"
        >
          Details
        </button>
        <button
          @click="$emit('suspend-user', user.id)"
          class="flex-1 bg-orange-50 hover:bg-orange-100 text-orange-700 px-4 py-2 rounded-lg text-sm transition-colors duration-200"
        >
          Suspend
        </button>
        <button
          @click="$emit('delete-user', user.id)"
          class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm transition-colors duration-200"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">

interface User {
  id: string
  name: string
  email: string
  balance: number
  role: string
  kycExpiry: string | null
  createdAt: string
}

const props = defineProps<{ user: User }>()
const emit = defineEmits<{
  (e: 'view-details', id: string): void
  (e: 'suspend-user', id: string): void
  (e: 'delete-user', id: string): void
}>()

const getInitials = (name: string): string => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
}

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}

const formatDate = (date: string | null): string => {
  if (!date) return 'Not set'
  return new Intl.DateTimeFormat('fr-FR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  }).format(new Date(date))
}
</script>