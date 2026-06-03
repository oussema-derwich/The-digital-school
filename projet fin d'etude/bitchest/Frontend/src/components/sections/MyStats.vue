<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6 text-center">Data Overview</h1>

    <div v-if="loading" class="text-center w-full">
      <Loader />
    </div>

    <div v-else>
      <!-- Client Statistics -->
      <div v-if="isClient">
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <StatCard
            title="Wallet Value"
            :value="portfolioComparison?.totalCurrentValue"
            unit="€"
            icon="wallet"
            color="blue"
          />
          <StatCard
            title="Total Investment"
            :value="portfolioComparison?.totalInvestmentValue"
            unit="€"
            icon="arrow-trend-up"
            color="green"
          />
          <StatCard
            title="Total Profit/Loss"
            :value="portfolioComparison?.profitOrLoss"
            unit="€"
            :percentage="portfolioComparison?.percentage"
            icon="chart-line"
            :color="portfolioComparison?.profitOrLoss >= 0 ? 'green' : 'red'"
          />
        </div>

        <!-- Tableau des investissements -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-6">
          <ChartCard title="Profit/Loss per Crypto">
            <div class="overflow-x-auto">
              <Table
                v-if="userInvestments.length"
                :data="userInvestments"
                tableName="investmentDetails"
                :headers="[
                  { text: 'Crypto', value: 'name' },
                  { text: 'Quantity', value: 'quantity' },
                  { text: 'Total Coast (€)', value: 'total-coast' },
                  { text: 'Average Coast (€)', value: 'average-coast' },
                  { text: 'Current Price (€)', value: 'current-price' },
                  { text: 'Current Value (€)', value: 'current-value' },
                ]"
              />
            </div>
          </ChartCard>
        </div>

        <!-- Chart Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
          <ChartCard title="Investment per Crypto">
            <PieChart
              v-if="userCryptoDetails.length"
              :data="userCryptoDetails"
            />
          </ChartCard>

          <ChartCard title="Profit/Loss per Crypto">
            <div class="overflow-x-auto">
              <Table
                v-if="cryptoProfitLoss.length"
                :data="cryptoProfitLoss"
                :headers="[
                  { text: 'Crypto', value: 'name' },
                  { text: 'Value', value: 'value' },
                  { text: 'Percentage', value: 'percentage' },
                ]"
              />
            </div>
          </ChartCard>
        </div>
      </div>

      <!-- Admin Statistics -->
      <div v-if="isAdmin">
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
          <!-- Stat Card -->
          <div
            class="p-4 rounded-lg bg-white shadow-lg flex flex-col justify-end items-center h-full"
          >
            <StatCard
              title="Platform Total Value"
              :value="platformTotalValue?.totalValue"
              unit="€"
              icon="bank"
              color="purple"
              class="w-full h-full text-center"
            />
          </div>

          <!-- Total Transaction Volume -->
          <ChartCard class="h-full">
            <div
              class="p-4 rounded-lg bg-white shadow-lg flex flex-col justify-center items-center h-full"
            >
              <div class="flex flex-row w-full p-2">
                <i class="fas fa-exchange-alt text-2xl"></i>
                <h3 class="text-lg font-semibold text-left pl-5">
                  Total Transaction Volume
                </h3>
              </div>

              <div class="text-center">
                <p class="flex justify-center items-center space-x-2 text-xl">
                  <i class="fas fa-arrow-up text-green-600"></i>
                  <span class="font-bold text-green-600">Buy:</span>
                  <span
                    >{{
                      formatNumber(totalTransactionVolume.total_buy)
                    }}
                    €</span
                  >
                </p>
                <p
                  class="flex justify-center items-center space-x-2 text-xl mt-2"
                >
                  <i class="fas fa-arrow-down text-red-600"></i>
                  <span class="font-bold text-red-600">Sell:</span>
                  <span
                    >{{
                      formatNumber(totalTransactionVolume.total_sell)
                    }}
                    €</span
                  >
                </p>
              </div>
            </div>
          </ChartCard>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
          <ChartCard title="Platform Crypto Details">
            <div class="rounded-lg bg-white shadow-lg p-3">
              <PieChart
                v-if="platformCryptoDetailsForPieChart.length"
                :data="platformCryptoDetailsForPieChart"
              />
            </div>
          </ChartCard>

          <ChartCard title="Top Cryptos Exchanged by Volume">
            <div class="overflow-x-auto">
              <Table
                v-if="topCryptos.length"
                :data="topCryptos"
                :headers="[
                  { text: 'Crypto Name', value: 'name' },
                  { text: 'Volume', value: 'volume' },
                ]"
              />
            </div>
          </ChartCard>

          <ChartCard
            title="Top Cryptos Exchanged by Revenue"
            class="col-span-1 md:col-span-2"
          >
            <BarChart
              v-if="formattedTopCryptosByRevenue.length"
              :data="formattedTopCryptosByRevenue"
            />
          </ChartCard>
        </div>

        <!-- Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
          <ChartCard title="Top Buyers">
            <div class="overflow-x-auto">
              <Table
                v-if="topBuyers.length"
                :data="topBuyers"
                :headers="[
                  { text: 'Client Name', value: 'name' },
                  { text: 'Amount', value: 'amount' },
                ]"
              />
            </div>
          </ChartCard>

          <ChartCard title="Top Wallets">
            <div class="overflow-x-auto">
              <Table
                v-if="topWallets.length"
                :data="topWallets"
                :headers="[
                  { text: 'Client Name', value: 'name' },
                  { text: 'Value (€)', value: 'value' },
                ]"
              />
            </div>
          </ChartCard>
        </div>

        <!-- Recent Activity -->
        <ChartCard title="Recent Activity" class="mt-6 lg:col-span-2">
          <div class="overflow-x-auto">
            <Table
              v-if="recentActivity.length"
              :data="recentActivity"
              :headers="[
                { text: 'Client Name', value: 'name' },
                { text: 'Crypto', value: 'crypto' },
                { text: 'Transaction Type', value: 'transaction-type' },
                { text: 'Quantity', value: 'quantity' },
                { text: 'Unit Price (€)', value: 'unit-price' },
                { text: 'Total Price (€)', value: 'total-price' },
                { text: 'Date', value: 'date' },
              ]"
            />
          </div>
        </ChartCard>

        <!-- Inactive Users -->
        <ChartCard title="Inactive Users" class="mt-6 lg:col-span-2">
          <div class="overflow-x-auto">
            <Table
              v-if="inactiveUsers.length"
              :data="inactiveUsers"
              :headers="[
                { text: 'Client Name', value: 'name' },
                { text: 'Email', value: 'email' },
                { text: 'Last Transaction', value: 'last-transaction' },
              ]"
            />
          </div>
        </ChartCard>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import LineChart from "../utils/LineChart.vue";
import PieChart from "../utils/PieChart.vue";
import BarChart from "../utils/BarChart.vue";
import Table from "../utils/Table.vue";
import StatCard from "../utils/StatCard.vue";
import ChartCard from "../utils/ChartCard.vue";
import Loader from "../utils/Loader.vue";

export default {
  components: {
    LineChart,
    PieChart,
    BarChart,
    Table,
    StatCard,
    ChartCard,
    Loader,
  },
  data() {
    return {
      loading: true, // Initialisation de l'état de chargement
    };
  },
  computed: {
    ...mapState("auth", ["user"]),
    ...mapState("stats", [
      "userPortfolio",
      "userCryptoDetails",
      "userInvestments",
      "portfolioComparison",
      "cryptoProfitLoss",
      "evolution",
      "platformTotalValue",
      "platformCryptoDetails",
      "topCryptos",
      "topCryptosByRevenue",
      "topBuyers",
      "topWallets",
      "totalTransactionVolume",
      "recentActivity",
      "inactiveUsers",
    ]),

    isClient() {
      return this.user?.role === "client";
    },
    isAdmin() {
      return this.user?.role === "admin";
    },
    cryptoProfitLossTotal() {
      return this.cryptoProfitLoss.reduce(
        (sum, crypto) => sum + crypto.profitLoss,
        0
      );
    },
    platformCryptoDetailsForPieChart() {
      if (!this.platformCryptoDetails.length) return [];

      return this.platformCryptoDetails.map((crypto) => ({
        label: crypto.name,
        value: crypto.totalValue,
        totalQuantity: crypto.totalQuantity,
        currentPrice: crypto.currentPrice,
        totalValue: crypto.totalValue,
      }));
    },
    formattedTopCryptosByRevenue() {
      return this.topCryptosByRevenue.map((crypto) => ({
        name: crypto.name,
        revenue: parseFloat(crypto.totalRevenue), // Convertir en nombre
      }));
    },
  },
  async created() {
    try {
      this.loading = true;

      if (this.isClient) {
        await this.fetchUserPortfolio(this.user.id);
        await this.fetchUserCryptoDetails(this.user.id);
        await this.fetchUserInvestments(this.user.id);
        await this.fetchPortfolioComparison(this.user.id);
        await this.fetchCryptoProfitLoss(this.user.id);
        await this.loadPortfolioEvolution(this.user.id);
      }
      if (this.isAdmin) {
        await this.fetchPlatformTotalValue();
        await this.fetchPlatformCryptoDetails();
        await this.fetchTopCryptos();
        await this.fetchTopCryptosByRevenue();
        await this.fetchTopBuyers();
        await this.fetchTopWallets();
        await this.fetchTotalTransactionVolume();
        await this.fetchRecentActivity();
        await this.fetchInactiveUsers();
      }
    } catch (error) {
      console.error("Error loading statistics:", error);
    } finally {
      this.loading = false;
    }
  },
  methods: {
    ...mapActions("stats", [
      "fetchUserPortfolio",
      "fetchUserCryptoDetails",
      "fetchUserInvestments",
      "fetchPortfolioComparison",
      "fetchCryptoProfitLoss",
      "fetchPlatformTotalValue",
      "fetchPlatformCryptoDetails",
      "fetchTopCryptos",
      "fetchTopCryptosByRevenue",
      "fetchTopBuyers",
      "fetchTopWallets",
      "fetchTotalTransactionVolume",
      "fetchRecentActivity",
      "fetchInactiveUsers",
      "loadPortfolioEvolution",
    ]),
    formatNumber(value) {
      if (!value) return "0";
      return new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
        useGrouping: true,
      })
        .format(parseFloat(value))
        .replace(/\s/g, " ");
    },
  },
};
</script>

<style scoped>
/* Responsive Grid Layout */
@media (max-width: 640px) {
  .grid-cols-1 {
    grid-template-columns: 1fr;
  }
  .grid-cols-2 {
    grid-template-columns: 1fr;
  }
  .grid-cols-3 {
    grid-template-columns: 1fr;
  }
}

@media (min-width: 641px) and (max-width: 1024px) {
  .grid-cols-2 {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1025px) {
  .grid-cols-3 {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* Scroll horizontal pour les tableaux */
.overflow-x-auto {
  overflow-x: auto;
}

/* Ajustement des cartes pour les petits écrans */
@media (max-width: 640px) {
  .p-4 {
    padding: 1rem;
  }
  .text-xl {
    font-size: 1.25rem;
  }
  .text-2xl {
    font-size: 1.5rem;
  }
}
</style>
