<template>
  <div class="relative w-full h-full">
    <canvas ref="canvasRef" class="w-full h-full"></canvas>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
import Chart from 'chart.js/auto'
import type { ChartOptions, ChartData } from 'chart.js'

interface Item {
  label: string
  value: number
}

const props = defineProps<{ data: Item[] }>()
const canvasRef = ref<HTMLCanvasElement | null>(null)
let chart: Chart | null = null

const createChart = () => {
  if (!canvasRef.value || !props.data || props.data.length === 0) return
  const ctx = canvasRef.value.getContext('2d')
  if (!ctx) return

  const chartData: ChartData<'bar'> = {
    labels: props.data.map(d => d.label),
    datasets: [
      {
        label: 'Volume',
        data: props.data.map(d => d.value),
        backgroundColor: 'rgba(59, 130, 246, 0.7)',
        borderColor: 'rgb(59, 130, 246)',
        borderWidth: 2,
        borderRadius: 6,
        hoverBackgroundColor: 'rgba(59, 130, 246, 0.9)'
      }
    ]
  }

  const options: ChartOptions<'bar'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: true },
      title: { display: false },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        padding: 12,
        titleFont: { size: 14 },
        bodyFont: { size: 13 },
        cornerRadius: 8
      }
    },
    scales: {
      y: { 
        beginAtZero: true,
        grid: { color: 'rgba(0, 0, 0, 0.05)' },
        ticks: {
          callback: (value) => value.toLocaleString()
        }
      },
      x: {
        grid: { display: false }
      }
    }
  }

  if (chart) chart.destroy()
  chart = new Chart(ctx, {
    type: 'bar',
    data: chartData as any,
    options
  })
}

onMounted(() => createChart())

watch(() => props.data, () => {
  if (!chart) return
  chart.data.labels = props.data.map(d => d.label) as any
  // @ts-ignore
  chart.data.datasets[0].data = props.data.map(d => d.value)
  chart.update()
}, { deep: true })

onBeforeUnmount(() => { if (chart) chart.destroy() })
</script>