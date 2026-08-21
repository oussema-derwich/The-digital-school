<template>
  <AdminLayout pageTitle="Paramètres" pageDescription="Configuration système et préférences d'administration">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- General Settings -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Platform Settings -->
        <div class="bg-white rounded-xl shadow-md p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <span>⚙️</span>
            <span>Paramètres de la plateforme</span>
          </h3>

          <form @submit.prevent="savePlatformSettings" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la plateforme</label>
              <input
                v-model="settings.platformName"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="settings.description"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                rows="3"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email de support</label>
              <input
                v-model="settings.supportEmail"
                type="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <button
              type="submit"
              class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition"
            >
              Enregistrer
            </button>
          </form>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-xl shadow-md p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <span>🔒</span>
            <span>Paramètres de sécurité</span>
          </h3>

          <form @submit.prevent="saveSecuritySettings" class="space-y-4">
            <div>
              <label class="flex items-center gap-3">
                <input
                  v-model="settings.requireTwoFA"
                  type="checkbox"
                  class="w-4 h-4 rounded border-gray-300"
                />
                <span class="text-sm font-medium text-gray-700">Activer l'authentification à deux facteurs (2FA)</span>
              </label>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Délai de session (minutes)</label>
              <input
                v-model.number="settings.sessionTimeout"
                type="number"
                min="15"
                max="1440"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="flex items-center gap-3">
                <input
                  v-model="settings.requireStrongPassword"
                  type="checkbox"
                  class="w-4 h-4 rounded border-gray-300"
                />
                <span class="text-sm font-medium text-gray-700">Exiger des mots de passe forts</span>
              </label>
            </div>

            <button
              type="submit"
              class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition"
            >
              Enregistrer
            </button>
          </form>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-xl shadow-md p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <span>🔔</span>
            <span>Notifications</span>
          </h3>

          <form @submit.prevent="saveNotificationSettings" class="space-y-4">
            <label class="flex items-center gap-3">
              <input
                v-model="settings.notifyNewUsers"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300"
              />
              <span class="text-sm font-medium text-gray-700">Notifier sur les nouvelles inscriptions</span>
            </label>

            <label class="flex items-center gap-3">
              <input
                v-model="settings.notifyTransactions"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300"
              />
              <span class="text-sm font-medium text-gray-700">Notifier sur les grosses transactions</span>
            </label>

            <label class="flex items-center gap-3">
              <input
                v-model="settings.notifyAlerts"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300"
              />
              <span class="text-sm font-medium text-gray-700">Notifier sur les anomalies détectées</span>
            </label>

            <button
              type="submit"
              class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition"
            >
              Enregistrer
            </button>
          </form>
        </div>
      </div>

      <!-- System Info -->
      <div class="space-y-6">
        <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
          <h4 class="font-bold text-blue-900 mb-4">ℹ️ Informations système</h4>
          <div class="space-y-3 text-sm">
            <div>
              <p class="text-blue-700 font-medium">Version</p>
              <p class="text-blue-600">1.0.0</p>
            </div>
            <div>
              <p class="text-blue-700 font-medium">Utilisateurs actifs</p>
              <p class="text-blue-600">1,247</p>
            </div>
            <div>
              <p class="text-blue-700 font-medium">Transactions</p>
              <p class="text-blue-600">12,854</p>
            </div>
            <div>
              <p class="text-blue-700 font-medium">Espace disque</p>
              <p class="text-blue-600">45% utilisé</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <h4 class="font-bold text-gray-800 mb-4">🧹 Maintenance</h4>
          <button class="w-full py-2 px-4 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition mb-2">
            Nettoyer les logs
          </button>
          <button class="w-full py-2 px-4 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
            Réinitialiser le cache
          </button>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <h4 class="font-bold text-gray-800 mb-4">📊 Sauvegarde</h4>
          <button class="w-full py-2 px-4 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
            Créer une sauvegarde
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'
import api from '@/services/api'

const settings = reactive({
  platformName: 'BitChest',
  description: 'Plateforme d\'échange de cryptomonnaies sécurisée',
  supportEmail: 'support@bitchest.com',
  requireTwoFA: true,
  sessionTimeout: 30,
  requireStrongPassword: true,
  notifyNewUsers: true,
  notifyTransactions: true,
  notifyAlerts: true
})

const savePlatformSettings = async () => {
  try {
    await api.post('/admin/settings/platform', {
      platformName: settings.platformName,
      description: settings.description,
      supportEmail: settings.supportEmail
    })
    alert('Paramètres enregistrés avec succès !')
  } catch (e) {
    console.error('Error saving settings:', e)
  }
}

const saveSecuritySettings = async () => {
  try {
    await api.post('/admin/settings/security', {
      requireTwoFA: settings.requireTwoFA,
      sessionTimeout: settings.sessionTimeout,
      requireStrongPassword: settings.requireStrongPassword
    })
    alert('Paramètres de sécurité enregistrés avec succès !')
  } catch (e) {
    console.error('Error saving security settings:', e)
  }
}

const saveNotificationSettings = async () => {
  try {
    await api.post('/admin/settings/notifications', {
      notifyNewUsers: settings.notifyNewUsers,
      notifyTransactions: settings.notifyTransactions,
      notifyAlerts: settings.notifyAlerts
    })
    alert('Paramètres de notifications enregistrés avec succès !')
  } catch (e) {
    console.error('Error saving notification settings:', e)
  }
}

onMounted(async () => {
  try {
    const response = await api.get('/admin/settings')
    Object.assign(settings, response.data)
  } catch (e) {
    console.error('Error loading settings:', e)
  }
})
</script>

<style scoped>
</style>
