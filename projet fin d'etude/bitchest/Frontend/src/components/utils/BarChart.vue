<template>
  <div class="bar-chart-container">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script>
import { ref, onMounted, watch } from "vue";
import { Chart } from "chart.js/auto";

export default {
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
  setup(props) {
    const chartCanvas = ref(null);
    let chartInstance = null;

    const colors = [
      "#FF6384",
      "#36A2EB",
      "#FFCE56",
      "#4BC0C0",
      "#9966FF",
      "#FF9F40",
      "#C9CBCF",
      "#FFCD56",
      "#4D5360",
      "#F7464A",
    ];

    const renderChart = () => {
      if (chartInstance) {
        chartInstance.destroy();
      }

      if (!props.data.length) return;

      chartInstance = new Chart(chartCanvas.value, {
        type: "bar",
        data: {
          labels: props.data.map((crypto) => crypto.name),
          datasets: [
            {
              label: "Total Revenue (€)",
              data: props.data.map((crypto) => parseFloat(crypto.revenue)),
              backgroundColor: colors.slice(0, props.data.length),
              borderColor: colors
                .slice(0, props.data.length)
                .map((c) => c.replace("0.6", "1")),
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,

          plugins: {
            legend: { display: false },
          },
          scales: {
            x: {
              ticks: { color: "#6B7280" },
              grid: { display: false },
            },
            y: {
              ticks: { color: "#6B7280", callback: (value) => `${value} €` },
              grid: { color: "#E5E7EB", borderDash: [5, 5] },
            },
          },
        },
      });
    };

    onMounted(renderChart);
    watch(() => props.data, renderChart, { deep: true });

    return { chartCanvas };
  },
};
</script>

<style scoped>
.bar-chart-container {
  position: relative;
  height: 400px;
  width: 100%;
}
</style>
