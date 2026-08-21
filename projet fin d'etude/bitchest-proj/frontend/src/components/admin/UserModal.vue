<template>
  <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">
        {{ user ? 'Modifier' : 'Ajouter' }} un utilisateur
      </h2>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
          <input type="text" 
                 id="name" 
                 v-model="form.name"
                 required
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" 
                 id="email" 
                 v-model="form.email"
                 required
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
          <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
          <select id="role" 
                  v-model="form.role"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="user">Utilisateur</option>
            <option value="admin">Administrateur</option>
          </select>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">
            {{ user ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe' }}
          </label>
          <input type="password" 
                 id="password" 
                 v-model="form.password"
                 :required="!user"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
          <label class="flex items-center">
            <input type="checkbox" 
                   v-model="form.is_active"
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-600">Compte actif</span>
          </label>
        </div>

        <div class="flex justify-end space-x-3">
          <button type="button"
                  @click="$emit('close')"
                  class="px-4 py-2 border rounded-md hover:bg-gray-100">
            Annuler
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            {{ user ? 'Mettre à jour' : 'Créer' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch, PropType } from 'vue'

export default defineComponent({
  name: 'UserModal',

  props: {
    user: {
      type: Object as PropType<Record<string, any> | null>,
      default: null
    }
  },

  emits: ['close', 'save'],

  setup(props, { emit }) {
    const form = ref({
      name: '',
      email: '',
      role: 'user',
      password: '',
      is_active: true
    })

    watch(() => props.user, (newUser) => {
      if (newUser) {
        form.value = {
          name: newUser.name,
          email: newUser.email,
          role: newUser.role,
          password: '',
          is_active: newUser.is_active
        }
      }
    }, { immediate: true })

    const handleSubmit = () => {
      const userData = { ...form.value }
      if (props.user && !userData.password) {
        // TypeScript requires delete operand to be optional; cast to any to perform runtime delete
        delete (userData as any).password
      }
      emit('save', userData)
    }

    return {
      form,
      handleSubmit
    }
  }
})
</script>