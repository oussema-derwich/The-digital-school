<template>
  <div class="w-full max-w-4xl mx-auto p-4 bg-white rounded-lg shadow-md">
    <!-- Nom et Logo de la crypto -->
    <div v-if="crypto" class="flex items-center space-x-4 mb-4">
      <img
        v-if="crypto.image"
        :src="`http://localhost:8000/storage/cryptos/${crypto.image}`"
        alt="Crypto Logo"
        class="w-12 h-12 rounded-full"
      />
      <h2 class="text-xl font-bold">{{ crypto.name }}</h2>
    </div>

    <!-- Sélection de la période -->
    <div class="flex justify-center space-x-4 mb-4">
      <label
        v-for="option in timeOptions"
        :key="option.value"
        class="flex items-center space-x-2 cursor-pointer"
      >
        <input
          type="radio"
          v-model="selectedDays"
          :value="option.value"
          class="hidden"
          @change="updateChart"
        />
        <span
          :class="[
            'px-4 py-2 rounded-full text-sm',
            selectedDays === option.value
              ? 'bg-blue-500 text-white'
              : 'bg-gray-200 text-gray-700',
          ]"
        >
          {{ option.label }}
        </span>
      </label>
    </div>

    <!-- Graphique -->
    <div class="relative h-80 flex items-center justify-center">
      <Loader v-if="isLoading" />
      <canvas v-show="!isLoading" ref="priceChart"></canvas>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6 text-center">
      <div class="p-4 bg-gray-100 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Max Price</p>
        <p class="text-lg font-bold">€{{ formatNumber(maxPrice) }}</p>
      </div>
      <div class="p-4 bg-gray-100 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Min Price</p>
        <p class="text-lg font-bold">€{{ formatNumber(minPrice) }}</p>
      </div>
      <div class="p-4 bg-gray-100 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Price Change</p>
        <p
          class="text-lg font-bold"
          :class="priceChange >= 0 ? 'text-green-500' : 'text-red-500'"
        >
          {{ priceChange.toFixed(5) }}%
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import { Chart } from "chart.js/auto";
import moment from "moment";
import Loader from "../utils/Loader.vue";

export default {
  components: { Loader },
  props: ["id"],
  data() {
    return {
      selectedDays: 30,
      chartInstance: null,
      timeOptions: [
        { label: "7 Days", value: 7 },
        { label: "15 Days", value: 15 },
        { label: "1 Month", value: 30 },
      ],
      isLoading: false,
    };
  },
  computed: {
    ...mapState("crypto", ["priceHistory", "crypto"]),

    maxPrice() {
      return this.priceHistory.length
        ? Math.max(...this.priceHistory.map((entry) => entry.value))
        : 0;
    },
    minPrice() {
      return this.priceHistory.length
        ? Math.min(...this.priceHistory.map((entry) => entry.value))
        : 0;
    },
    priceChange() {
      if (this.priceHistory.length < 2) return 0;
      const start = this.priceHistory[0].value;
      const end = this.priceHistory[this.priceHistory.length - 1].value;
      return start !== 0 ? ((end - start) / start) * 100 : 0;
    },
  },
  methods: {
    ...mapActions("crypto", ["loadPriceHistory"]),

    formatNumber(value) {
      if (!value) return "0";
      return new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
        useGrouping: true,
      }).format(parseFloat(value));
    },

    async updateChart() {
      if (!this.id) {
        console.error("ID de la crypto manquant");
        return;
      }

      this.isLoading = true;
      try {
        await this.loadPriceHistory({
          cryptoId: this.id,
          days: this.selectedDays,
        });
        this.$nextTick(() => this.drawChart());
      } catch (error) {
        console.error("Erreur lors du chargement des données :", error);
      } finally {
        this.isLoading = false;
      }
    },

    drawChart() {
      if (!this.$refs.priceChart) {
        console.error("Le canvas du graphique est introuvable.");
        return;
      }
      if (!this.priceHistory.length) {
        console.warn("Aucune donnée pour afficher le graphique.");
        return;
      }

      if (this.chartInstance) this.chartInstance.destroy();

      const ctx = this.$refs.priceChart.getContext("2d");
      if (!ctx) {
        console.error("Impossible d'obtenir le contexte du canvas.");
        return;
      }

      this.chartInstance = new Chart(ctx, {
        type: "line",
        data: {
          labels: this.priceHistory.map((entry) =>
            moment(entry.date).format("DD/MM")
          ),
          datasets: [
            {
              label: this.crypto
                ? `${this.crypto.name} Price (€)`
                : "Crypto Price (€)",
              data: this.priceHistory.map((entry) => entry.value),
              borderColor: "#3b82f6",
              backgroundColor: "rgba(59, 130, 246, 0.2)",
              tension: 0.3,
              borderWidth: 2,
              pointRadius: 4,
              pointBackgroundColor: "#3b82f6",
              pointBorderWidth: 2,
            },
          ],
        },
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
              ticks: {
                color: "#6B7280",
                callback: (value) => `€${parseFloat(value.toFixed(2))}`,
              },
              grid: { color: "#E5E7EB", borderDash: [5, 5] },
            },
          },
        },
      });
    },
  },
  async mounted() {
    await this.updateChart();
  },
};
</script>
