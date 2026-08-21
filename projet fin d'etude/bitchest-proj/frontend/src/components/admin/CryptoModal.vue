<template>
  <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">
        Modifier la cryptomonnaie
      </h2>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
          <input type="text" 
                 id="name" 
                 v-model="form.name"
                 disabled
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50">
        </div>

        <div>
          <label for="symbol" class="block text-sm font-medium text-gray-700">Symbole</label>
          <input type="text" 
                 id="symbol" 
                 v-model="form.symbol"
                 disabled
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50">
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea id="description" 
                    v-model="form.description"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>

        <div>
          <label for="is_active" class="flex items-center">
            <input type="checkbox" 
                   id="is_active"
                   v-model="form.is_active"
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-600">Active</span>
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
            Mettre à jour
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch, PropType } from 'vue'

export default defineComponent({
  name: 'CryptoModal',

  props: {
    crypto: {
      type: Object as PropType<Record<string, any> | null>,
      default: null
    }
  },

  emits: ['close', 'save'],

  setup(props, { emit }) {
    const form = ref({
      name: '',
      symbol: '',
      description: '',
      is_active: true
    })

    watch(() => props.crypto, (newCrypto) => {
      if (newCrypto) {
        form.value = {
          name: newCrypto.name,
          symbol: newCrypto.symbol,
          description: newCrypto.description || '',
          is_active: newCrypto.is_active
        }
      }
    }, { immediate: true })

    const handleSubmit = () => {
      emit('save', form.value)
    }

    return {
      form,
      handleSubmit
    }
  }
})
</script>