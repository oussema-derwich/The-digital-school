<template>
  <aside class="fixed left-0 top-0 h-full w-64 bg-white rounded-r-xl shadow-sm overflow-y-auto">
    <div class="p-6 space-y-8">
      <div class="flex items-center space-x-4">
        <div class="p-2 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg">
          <span class="text-xl text-white">🛡</span>
        </div>
        <div>
          <h2 class="text-lg font-semibold">Admin Panel</h2>
          <p class="text-sm text-gray-500">System Control</p>
        </div>
      </div>

      <nav class="space-y-2">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200"
          :class="{
            'bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold': isActive(item.path),
            'hover:bg-gray-50': !isActive(item.path)
          }"
        >
          <span class="text-xl" :class="{ 'text-white': isActive(item.path) }">{{ item.icon }}</span>
          <span>{{ item.name }}</span>
        </router-link>
      </nav>
    </div>
  </aside>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { useRoute } from 'vue-router'

export default defineComponent({
  name: 'AdminSidebar',
  setup() {
    const route = useRoute()

    const menuItems = [
      { name: 'Overview', icon: '📊', path: '/admin' },
      { name: 'Manage Clients', icon: '👥', path: '/admin/users' },
      { name: 'Manage Cryptos', icon: '💱', path: '/admin/cryptos' },
      { name: 'Manage Transactions', icon: '🔄', path: '/admin/transactions' }
    ]

    const isActive = (path: string): boolean => {
      return route.path === path || route.path.startsWith(path + '/')
    }

    return {
      menuItems,
      isActive
    }
  }
})
</script>

<style scoped>
.router-link-active {
  position: relative;
  overflow: hidden;
}

.router-link-active::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.2) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  animation: shine 1.5s infinite;
}

@keyframes shine {
  100% {
    left: 100%;
  }
}
</style>