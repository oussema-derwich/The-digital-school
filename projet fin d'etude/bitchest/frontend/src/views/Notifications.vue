<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-6">Notifications</h1>
      
      <div v-if="notifications.length === 0" class="text-center py-12">
        <p class="text-gray-500">Aucune notification</p>
      </div>
      
      <div v-else class="space-y-3">
        <div 
          v-for="notification in notifications" 
          :key="notification.id"
          class="bg-white p-4 rounded-lg shadow hover:shadow-md transition flex items-start justify-between"
        >
          <div class="flex-1">
            <h3 class="font-semibold text-gray-800">{{ notification.title }}</h3>
            <p class="text-gray-600 text-sm mt-1">{{ notification.message }}</p>
            <p class="text-xs text-gray-400 mt-2">{{ formatDate(notification.created_at) }}</p>
          </div>
          <button 
            @click="deleteNotification(notification.id)"
            class="ml-4 text-red-500 hover:text-red-700"
          >
            ✕
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'

interface Notification {
  id: number
  title: string
  message: string
  created_at: string
}

export default defineComponent({
  setup() {
    const notifications = ref<Notification[]>([])

    const formatDate = (date: string) => {
      return new Date(date).toLocaleString('fr-FR')
    }

    const deleteNotification = (id: number) => {
      notifications.value = notifications.value.filter(n => n.id !== id)
    }

    onMounted(() => {
      // Charger les notifications depuis le localStorage ou l'API
      const stored = localStorage.getItem('notifications')
      if (stored) {
        notifications.value = JSON.parse(stored)
      }
    })

    return { notifications, formatDate, deleteNotification }
  }
})
</script>
