<template>
  <div class="bg-white rounded-lg shadow-md p-4">
    <div class="flex items-center justify-between">
      <div>
        <div class="font-semibold">{{ crypto.name }} <span class="text-sm text-gray-500">({{ crypto.symbol }})</span></div>
        <div class="text-lg">{{ crypto.price }} €</div>
      </div>
      <div class="text-right">
        <div v-if="crypto.trend === 'up'" class="text-green-500">🔼</div>
        <div v-else class="text-red-500">🔽</div>
        <div class="mt-2 flex gap-2">
          <button class="btn-primary">Acheter</button>
          <button class="bg-[#F59E0B] text-white px-3 py-1 rounded">Vendre</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue'

export default defineComponent({
  props: {
    crypto: { type: Object as PropType<any>, required: true }
  }
})
</script>
