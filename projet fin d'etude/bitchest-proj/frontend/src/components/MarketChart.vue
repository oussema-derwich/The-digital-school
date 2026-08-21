<template>
  <div>
    <canvas ref="canvas" :height="height"></canvas>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref, watch } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

export default defineComponent({
  name: 'MarketChart',
  props: {
    data: { type: Array as () => any[], default: () => [] },
    labels: { type: Array as () => string[], default: () => [] },
    period: { type: [String, Number], default: '24h' },
    height: { type: Number, default: 200 }
  },
  setup(props) {
    const canvas = ref<HTMLCanvasElement | null>(null)
    let chart: Chart | null = null

    const buildChart = () => {
      if (!canvas.value || !props.data || props.data.length === 0) return
      if (chart) chart.destroy()
      
      // Extraire les prix de clôture pour le graphique
      const prices = props.data.map((item: any) => {
        if (typeof item === 'object' && item.c !== undefined) {
          return item.c
        }
        return item
      })
      
      chart = new Chart(canvas.value.getContext('2d') as CanvasRenderingContext2D, {
        type: 'line',
        data: {
          labels: props.labels && props.labels.length > 0 ? props.labels : prices.map((_, i) => i.toString()),
          datasets: [
            {
              label: 'Prix',
              data: prices,
              borderColor: '#3B82F6',
              backgroundColor: 'rgba(59, 130, 246, 0.15)',
              borderWidth: 2,
              fill: true,
              tension: 0.4,
              pointRadius: 4,
              pointBackgroundColor: '#3B82F6',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointHoverRadius: 6,
              hoverBackgroundColor: '#3B82F6'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            mode: 'index',
            intersect: false
          },
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
              displayColors: true
            }
          },
          scales: {
            x: {
              display: true,
              grid: {
                display: false
              }
            },
            y: {
              display: true,
              beginAtZero: false,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false
              },
              ticks: {
                callback: function(value: any) {
                  return value.toFixed(0)
                }
              }
            }
          }
        }
      })
    }

    onMounted(() => buildChart())

    watch(() => [props.data, props.labels], () => {
      buildChart()
    }, { deep: true })

    return { canvas }
  }
})
</script>

<style scoped>
canvas { width: 100%; }
</style>
