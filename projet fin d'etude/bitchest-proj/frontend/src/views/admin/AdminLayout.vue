<template>
  <div class="flex flex-col bg-gray-50 min-h-screen">
    <div class="flex flex-1">
      <AdminSidebar />
      <div class="flex-1 ml-64 flex flex-col">
        <!-- Header Admin -->
        <header class="bg-white shadow-md sticky top-0 z-20">
          <div class="flex justify-between items-center px-8 py-4">
            <div>
              <h2 class="text-2xl font-bold text-gray-800">{{ pageTitle }}</h2>
              <p class="text-sm text-gray-500">{{ pageDescription }}</p>
            </div>
            <div class="flex items-center gap-6">
              <button class="relative text-gray-600 hover:text-blue-600 transition text-2xl">
                🔔
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
              </button>
              <div class="text-right">
                <p class="text-sm font-medium text-gray-700">Administrateur</p>
                <p class="text-xs text-gray-500">{{ currentDate }}</p>
              </div>
              <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white font-bold">
                👨
              </div>
            </div>
          </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-auto">
          <slot />
        </main>

        <!-- Footer Admin -->
        <footer class="bg-white border-t border-gray-200 mt-8 py-6 px-8">
          <div class="flex justify-between items-center text-sm text-gray-600">
            <p>&copy; 2024 BitChest - Plateforme d'Administration</p>
            <div class="flex gap-6">
              <a href="#" class="hover:text-blue-600 transition">Conditions d'utilisation</a>
              <a href="#" class="hover:text-blue-600 transition">Politique de confidentialité</a>
              <a href="#" class="hover:text-blue-600 transition">Support</a>
            </div>
          </div>
        </footer>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import AdminSidebar from './AdminSidebar.vue'

interface Props {
  pageTitle: string
  pageDescription?: string
}

defineProps<Props>()

const currentDate = computed(() => {
  return new Date().toLocaleDateString('fr-FR', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
})
</script>

<style scoped>
</style>
