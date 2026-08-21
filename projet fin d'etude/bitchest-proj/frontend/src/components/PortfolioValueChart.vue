// Graph de l'historique de valeur du portfolio
<template>
  <div class="relative">
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref, watch } from 'vue'
import { Chart, ChartData, ChartOptions } from 'chart.js/auto'

export default defineComponent({
  name: 'PortfolioValueChart',
  
  props: {
    data: {
      type: Array as () => Array<{ date: string; value: number }>,
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

      const chartData: ChartData = {
        labels: props.data.map(item => new Date(item.date).toLocaleDateString('fr-FR')),
        datasets: [{
          label: 'Valeur du portefeuille',
          data: props.data.map(item => item.value),
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59, 130, 246, 0.15)',
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: '#3B82F6',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointHoverRadius: 6
        }]
      }

      const options: ChartOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: true,
            labels: { 
              usePointStyle: true,
              padding: 15 
            }
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: { size: 14 },
            bodyFont: { size: 13 },
            cornerRadius: 8,
            callbacks: {
              label: (context) => {
                return `Valeur: ${new Intl.NumberFormat('fr-FR', {
                  style: 'currency',
                  currency: 'EUR'
                }).format(context.raw as number)}`
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              callback: (value) => {
                return new Intl.NumberFormat('fr-FR', {
                  style: 'currency',
                  currency: 'EUR',
                  minimumFractionDigits: 0
                }).format(value as number)
              }
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }

      if (chart) chart.destroy()
      chart = new Chart(ctx, {
        type: 'line',
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