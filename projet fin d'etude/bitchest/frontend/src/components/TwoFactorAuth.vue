<template>
  <div class="space-y-6">
    <!-- État initial -->
    <div v-if="!enabled && !setupMode" class="flex justify-between items-center">
      <div>
        <h3 class="text-lg font-medium text-gray-900">Double authentification (2FA)</h3>
        <p class="mt-1 text-sm text-gray-500">
          Ajoutez une couche de sécurité supplémentaire avec Google Authenticator
        </p>
      </div>
      <button
        @click="startSetup"
        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
      >
        Activer 2FA
      </button>
    </div>

    <!-- Mode configuration -->
    <div v-if="setupMode" class="space-y-4">
      <div v-if="qrCode" class="space-y-4">
        <h3 class="text-lg font-medium text-gray-900">Configuration de Google Authenticator</h3>
        
        <ol class="list-decimal list-inside space-y-3 text-sm text-gray-600">
          <li>Installez Google Authenticator sur votre téléphone</li>
          <li>Ouvrez l'application et scannez ce QR code</li>
          <li>Entrez le code à 6 chiffres généré ci-dessous</li>
        </ol>

        <div class="flex justify-center my-6">
          <!-- QR Code affiché comme une image -->
          <img 
            :src="'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' + encodeURIComponent(qrCode)"
            alt="QR Code pour Google Authenticator"
            class="border p-2 rounded-md shadow-sm"
          />
        </div>

        <!-- En cas de problème avec le QR -->
        <div class="bg-gray-50 p-4 rounded-md text-sm">
          <p class="font-medium text-gray-700">Code secret (si le QR ne fonctionne pas) :</p>
          <code class="block mt-2 font-mono bg-white p-2 rounded border select-all">{{ secret }}</code>
        </div>

        <!-- Formulaire de vérification -->
        <form @submit.prevent="confirmSetup" class="mt-6">
          <div>
            <label for="code" class="block text-sm font-medium text-gray-700">
              Code de vérification
            </label>
            <div class="mt-1 flex space-x-4">
              <input
                type="text"
                id="code"
                v-model="verificationCode"
                maxlength="6"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm"
                placeholder="123456"
              />
              <button
                type="submit"
                :disabled="verifying"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
              >
                {{ verifying ? 'Vérification...' : 'Vérifier' }}
              </button>
            </div>
          </div>
        </form>

        <div class="flex justify-end mt-4">
          <button
            @click="cancelSetup"
            class="text-sm text-gray-600 hover:text-gray-900"
          >
            Annuler la configuration
          </button>
        </div>
      </div>
    </div>

    <!-- 2FA activé -->
    <div v-if="enabled && !setupMode" class="flex justify-between items-center">
      <div>
        <h3 class="text-lg font-medium text-gray-900">Double authentification (2FA)</h3>
        <p class="mt-1 text-sm text-gray-600">
          La double authentification est active sur votre compte
        </p>
      </div>
      <button
        @click="disable2FA"
        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-danger hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-danger"
      >
        Désactiver 2FA
      </button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import twoFactorAuth from '@/services/twoFactorAuth'

export default defineComponent({
  name: 'TwoFactorAuth',
  
  props: {
    initialEnabled: {
      type: Boolean,
      default: false
    }
  },

  emits: ['statusChange'],

  setup(props, { emit }) {
    const enabled = ref(props.initialEnabled)
    const setupMode = ref(false)
    const qrCode = ref('')
    const secret = ref('')
    const verificationCode = ref('')
    const verifying = ref(false)

    const startSetup = async () => {
      try {
        const response = await twoFactorAuth.enable2FA()
        qrCode.value = response.data.qr_code
        secret.value = response.data.secret
        setupMode.value = true
      } catch (error) {
        console.error('Erreur lors de l\'activation de 2FA:', error)
      }
    }

    const confirmSetup = async () => {
      if (!verificationCode.value || verificationCode.value.length !== 6) {
        alert('Veuillez entrer un code à 6 chiffres')
        return
      }

      verifying.value = true
      try {
        await twoFactorAuth.confirm2FA(verificationCode.value)
        enabled.value = true
        setupMode.value = false
        emit('statusChange', true)
      } catch (error: any) {
        alert(error.response?.data?.message || 'Code invalide')
      } finally {
        verifying.value = false
      }
    }

    const cancelSetup = () => {
      setupMode.value = false
      qrCode.value = ''
      secret.value = ''
      verificationCode.value = ''
    }

    const disable2FA = async () => {
      if (!confirm('Êtes-vous sûr de vouloir désactiver la double authentification ?')) {
        return
      }

      try {
        await twoFactorAuth.disable2FA()
        enabled.value = false
        emit('statusChange', false)
      } catch (error) {
        console.error('Erreur lors de la désactivation de 2FA:', error)
      }
    }

    return {
      enabled,
      setupMode,
      qrCode,
      secret,
      verificationCode,
      verifying,
      startSetup,
      confirmSetup,
      cancelSetup,
      disable2FA
    }
  }
})
</script>