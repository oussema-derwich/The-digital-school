import { onMounted, onUnmounted } from 'vue'

/**
 * Composable to detect clicks outside of an element
 */
export function onClickOutside(element: any, callback: () => void) {
  if (!element) return

  const handleClickOutside = (e: MouseEvent) => {
    if (element.value && !element.value.contains(e.target as Node)) {
      callback()
    }
  }

  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })

  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
}
