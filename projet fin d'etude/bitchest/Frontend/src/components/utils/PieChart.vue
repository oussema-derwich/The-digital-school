<template>
  <div>
    <canvas ref="pieChart"></canvas>
  </div>
</template>

<script>
import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";
Chart.register(...registerables, ChartDataLabels);

export default {
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
  mounted() {
    this.renderChart();
  },
  watch: {
    data: {
      handler() {
        this.renderChart();
      },
      deep: true,
    },
  },
  methods: {
    renderChart() {
      const ctx = this.$refs.pieChart.getContext("2d");

      // Détruire le graphique existant s'il y en a un
      if (this.chart) {
        this.chart.destroy();
      }

      this.chart = new Chart(ctx, {
        type: "pie",
        data: {
          labels: this.data.map((item) => item.label),
          datasets: [
            {
              label: "Total Value (€)",
              data: this.data.map((item) => item.totalValue),
              backgroundColor: [
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
              ],
              hoverOffset: 4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            tooltip: {
              callbacks: {
                label: (context) => {
                  const label = context.label || "";
                  const value = context.raw || 0;
                  const crypto = this.data.find((item) => item.label === label);

                  if (!crypto) {
                    return `Crypto: ${label}`;
                  }

                  return [
                    `Current Price: ${crypto.currentPrice || "N/A"} €`,
                    `Total Quantity: ${crypto.totalQuantity || "N/A"}`,
                    `Total Value: ${
                      crypto.totalValue?.toLocaleString("en-US") || "N/A"
                    } €`,
                  ];
                },
              },
            },
            datalabels: {
              color: "#fff",
              font: {
                weight: "bold",
                size: 14,
              },
              rotation: 0,
              align: "end",
              offset: 10,
              formatter: (value, context) => {
                const total = context.chart.data.datasets[0].data.reduce(
                  (a, b) => a + b,
                  0
                );
                const percentage = ((value / total) * 100).toFixed(2);
                return `${percentage}%`;
              },
            },
          },
        },
      });
    },
  },
};
</script>
