<template>
  <div :class="['flex items-center justify-center', sizeClasses]">
    <img
      :src="logoPath"
      :alt="`${name} logo`"
      :class="['w-full h-full object-contain', 'rounded-full']"
      @error="handleError"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue'
import { getCryptoLogo } from '../utils/cryptoLogos'

export default defineComponent({
  props: {
    name: {
      type: String,
      required: true
    },
    size: {
      type: String,
      default: 'md', // xs, sm, md, lg, xl
      validator: (v: string) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(v)
    }
  },
  setup(props) {
    const sizeClasses = computed(() => {
      const sizes: Record<string, string> = {
        xs: 'w-6 h-6',
        sm: 'w-8 h-8',
        md: 'w-10 h-10',
        lg: 'w-12 h-12',
        xl: 'w-16 h-16'
      }
      return sizes[props.size] || sizes.md
    })

    const logoPath = computed(() => {
      return getCryptoLogo(props.name)
    })

    const handleError = () => {
      console.warn(`Could not load logo for ${props.name}`)
    }

    return {
      sizeClasses,
      logoPath,
      handleError
    }
  }
})
</script>
