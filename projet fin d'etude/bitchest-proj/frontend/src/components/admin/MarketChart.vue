<template>
  <div class="w-full h-80 bg-gray-50 rounded border border-gray-200 flex items-center justify-center">
    <div v-if="!chartLoaded" class="text-gray-500 text-center">
      <p>Chargement du graphique...</p>
      <p class="text-xs mt-2">{{ loadingStatus }}</p>
    </div>
    <canvas v-else ref="chartRef" class="w-full h-full"></canvas>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import Chart from 'chart.js/auto'
import type { ChartData } from 'chart.js'
import api from '@/services/api'

defineProps<{ period: string | number }>()

const chartRef = ref<HTMLCanvasElement | null>(null)
const chartLoaded = ref(false)
const loadingStatus = ref('...')
let chart: Chart | null = null

const loadChart = async () => {
  try {
    loadingStatus.value = 'Appel API...'
    const response = await api.get('/cryptocurrencies/1/history')
    
    loadingStatus.value = `Réponse reçue`
    
    if (!response.data?.data?.history) {
      loadingStatus.value = 'Pas de données'
      return
    }

    const historyObj = response.data.data.history
    loadingStatus.value = `Conversion de ${Object.keys(historyObj).length} entrées`
    
    const historyArray = Object.values(historyObj).sort((a: any, b: any) => a.timestamp - b.timestamp)

    if (historyArray.length === 0) {
      loadingStatus.value = 'Array vide'
      return
    }

    const prices = historyArray.map((item: any) => parseFloat(item.price) || 0)
    const labels = historyArray.map((item: any) => {
      const date = new Date(item.timestamp * 1000)
      return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
    })

    loadingStatus.value = `Graphique ${prices.length} points`
    
    if (!chartRef.value) {
      loadingStatus.value = 'Canvas manquant'
      return
    }

    const ctx = chartRef.value.getContext('2d')
    if (!ctx) {
      loadingStatus.value = 'Context 2D manquant'
      return
    }

    if (chart) chart.destroy()

    const chartData: ChartData<'line'> = {
      labels: labels,
      datasets: [
        {
          label: 'Prix (USD)',
          data: prices,
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59, 130, 246, 0.15)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointRadius: 0,
          pointHoverRadius: 6,
          pointBackgroundColor: '#3B82F6'
        }
      ]
    }

    chart = new Chart(ctx, {
      type: 'line',
      data: chartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { mode: 'index', intersect: false }
        },
        scales: {
          x: { grid: { display: false } },
          y: { beginAtZero: false, grid: { color: 'rgba(0, 0, 0, 0.05)' } }
        }
      }
    })

    chartLoaded.value = true
    loadingStatus.value = 'Graphique chargé!'
  } catch (error: any) {
    loadingStatus.value = `Erreur: ${error.message}`
    console.error('MarketChart Error:', error)
  }
}

onMounted(() => {
  loadChart()
})

onBeforeUnmount(() => {
  if (chart) chart.destroy()
})

defineExpose({ chartRef })
</script>