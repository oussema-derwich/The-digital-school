<template>
  <div>
    <canvas ref="chart"></canvas>
  </div>
</template>

<script>
import { Chart } from "chart.js/auto";

export default {
  props: {
    data: {
      type: Object,
      required: true,
    },
  },
  mounted() {
    this.renderChart();
  },
  methods: {
    renderChart() {
      const ctx = this.$refs.chart.getContext("2d");
      new Chart(ctx, {
        type: "line",
        data: this.data,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: true },
          },
          scales: {
            x: {
              ticks: { color: "#6B7280" },
              grid: { display: false },
            },
            y: {
              ticks: { color: "#6B7280", callback: (value) => `$${value}` },
              grid: { color: "#E5E7EB", borderDash: [5, 5] },
            },
          },
        },
      });
    },
  },
  watch: {
    data: {
      handler() {
        this.renderChart();
      },
      deep: true,
    },
  },
};
</script>

<style scoped>
canvas {
  max-height: 400px;
}
</style>
