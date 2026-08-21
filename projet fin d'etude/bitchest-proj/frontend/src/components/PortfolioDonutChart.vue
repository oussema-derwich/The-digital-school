// Graph de répartition des actifs en donut chart
<template>
  <div class="relative">
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref, watch } from 'vue'
import { Chart, ChartData, ChartOptions, DoughnutController } from 'chart.js'

export default defineComponent({
  name: 'PortfolioDonutChart',
  
  props: {
    data: {
      type: Array as () => Array<{ name: string; value: number; color: string }>,
      required: true
    }
  },

  setup(props) {
    const canvas = ref<HTMLCanvasElement | null>(null)
    let chart: Chart | null = null

    const createChart = () => {
      if (!canvas.value || !props.data || props.data.length === 0) return
      
      const ctx = canvas.value.getContext('2d')
      if (!ctx) return

      const chartData: ChartData<'doughnut'> = {
        labels: props.data.map(item => item.name),
        datasets: [{
          data: props.data.map(item => item.value),
          backgroundColor: props.data.map(item => item.color),
          borderWidth: 2,
          borderColor: '#fff'
        }]
      }

      const options: ChartOptions<'doughnut'> = {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '70%',
        plugins: {
          legend: {
            position: 'right',
            labels: {
              boxWidth: 12,
              padding: 15,
              font: { size: 13 }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: { size: 14 },
            bodyFont: { size: 13 },
            cornerRadius: 8,
            callbacks: {
              label: (context) => {
                const value = context.raw as number
                const total = (context.dataset.data as number[]).reduce((a, b) => a + b, 0)
                const percentage = ((value / total) * 100).toFixed(1)
                return `${context.label}: ${percentage}%`
              }
            }
          }
        },
        animation: {
          duration: 800,
          easing: 'easeOutQuart'
        }
      }

      if (chart) chart.destroy()
      chart = new Chart(ctx, {
        type: 'doughnut',
        data: chartData,
        options
      })
    }

    onMounted(() => {
      createChart()
    })

    watch(() => props.data, () => {
      if (chart) {
        chart.destroy()
      }
      createChart()
    }, { deep: true })

    return { canvas }
  }
})
</script>