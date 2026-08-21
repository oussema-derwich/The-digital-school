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

  const chartData: ChartData<'doughnut'> = {
    labels: props.data.map(d => d.label),
    datasets: [
      {
        data: props.data.map(d => d.value),
        backgroundColor: [
          'rgba(59, 130, 246, 0.7)',
          'rgba(147, 51, 234, 0.7)',
          'rgba(236, 72, 153, 0.7)',
          'rgba(248, 113, 113, 0.7)'
        ].slice(0, props.data.length),
        borderColor: [
          'rgb(59, 130, 246)',
          'rgb(147, 51, 234)',
          'rgb(236, 72, 153)',
          'rgb(248, 113, 113)'
        ].slice(0, props.data.length),
        borderWidth: 2
      }
    ]
  }

  const options: ChartOptions<'doughnut'> = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '60%',
    plugins: { 
      legend: { 
        position: 'right',
        labels: {
          padding: 15,
          font: { size: 13 }
        }
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        padding: 12,
        titleFont: { size: 14 },
        bodyFont: { size: 13 },
        cornerRadius: 8
      }
    }
  }

  if (chart) chart.destroy()
  chart = new Chart(ctx, {
    type: 'doughnut',
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