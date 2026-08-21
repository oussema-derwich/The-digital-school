<template>
  <div class="container py-8">
    <header class="mb-8">
      <h1 class="text-2xl font-semibold text-secondary">Tableau de Bord</h1>
    </header>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <StatsCard
        title="Utilisateurs Actifs"
        :value="stats.activeUsers"
        :change="stats.userGrowth"
          icon="👥"
        variant="primary"
      />
      
      <StatsCard
        title="Volume des Transactions"
        :value="formatCurrency(stats.transactionVolume) + ' DT'"
        :change="stats.volumeChange"
          icon="📊"
        variant="success"
      />

      <StatsCard
        title="Alertes Actives"
        :value="stats.activeAlerts"
          icon="🔔"
        variant="warning"
      />

      <StatsCard
        title="Montant Total Échangé"
        :value="formatCurrency(stats.totalTraded) + ' DT'"
        :change="stats.tradedChange"
          icon="💰"
        variant="primary"
      />

        <StatsCard
          title="Alertes actives"
          :value="stats.activeAlerts"
          :subtitle="stats.highAlert ? 'Seuil élevé' : ''"
            icon="🔔"
          iconBg="bg-white"
          valueClass="text-gray-900"
        />

        <StatsCard
          title="Montant total échangé"
          :value="formatCurrency(stats.totalTraded)"
            icon="💰"
          iconBg="bg-white"
          valueClass="text-gray-900"
        />
      </div>

      <!-- Graphique d'évolution du marché -->
      <div class="bg-white rounded-md shadow-card p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-medium text-secondary">Évolution du Marché</h2>
          <div class="flex items-center space-x-2">
            <button 
              v-for="period in periods" 
              :key="period.value"
              @click="selectedPeriod = period.value"
              :class="[
                'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                selectedPeriod === period.value 
                  ? 'bg-primary text-white' 
                  : 'text-secondary hover:bg-gray-100'
              ]"
            >
              {{ period.label }}
            </button>
          </div>
        </div>
        <MarketChart :period="selectedPeriod" />
      </div>

      <!-- Tableau des dernières transactions -->
      <div class="bg-white rounded-md shadow-card p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-medium text-secondary">Dernières Transactions</h2>
          <div class="flex items-center space-x-4">
            <button 
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-secondary hover:text-primary transition-colors"
              @click="refreshData"
            >
                <span class="mr-2">🔄</span>
              Actualiser
            </button>
            <button 
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-secondary hover:text-primary transition-colors"
              @click="exportReport"
            >
                <span class="mr-2">⬇️</span>
              Exporter
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th scope="col" class="table-header">Utilisateur</th>
                <th scope="col" class="table-header">Crypto</th>
                <th scope="col" class="table-header">Type</th>
                <th scope="col" class="table-header">Quantité</th>
                <th scope="col" class="table-header">Montant</th>
                <th scope="col" class="table-header">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="transaction in transactions" :key="transaction.id" class="hover:bg-gray-50">
                <td class="table-cell">
                  <div class="flex items-center">
                    <img class="h-8 w-8 rounded-full" :src="transaction.user.avatar" :alt="transaction.user.name">
                    <div class="ml-4">
                      <div class="font-medium text-secondary">{{ transaction.user.name }}</div>
                      <div class="text-secondary/70">{{ transaction.user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="table-cell">
                  <div class="flex items-center">
                    <img class="h-6 w-6 rounded-full" :src="transaction.crypto.icon" :alt="transaction.crypto.symbol">
                    <span class="ml-2">{{ transaction.crypto.symbol }}</span>
                  </div>
                </td>
                <td class="table-cell">
                  <span :class="[
                    'badge',
                    transaction.type === 'buy' ? 'badge-success' : 'badge-danger'
                  ]">
                    {{ transaction.type === 'buy' ? 'Achat' : 'Vente' }}
                  </span>
                </td>
                <td class="table-cell">{{ formatAmount(transaction.quantity) }}</td>
                <td class="table-cell">{{ formatCurrency(transaction.amount) }} DT</td>
                <td class="table-cell">{{ formatDate(transaction.date) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { Ref } from 'vue';
import StatsCard from '@/components/admin/StatsCard.vue';
import MarketChart from '@/components/admin/MarketChart.vue';
import api from '@/services/api';
import type { Transaction, DashboardStats } from '@/types/dashboard';

// État
const selectedPeriod = ref<string>('24h');
const stats = ref<DashboardStats>({
  activeUsers: '0',
  userGrowth: 0,
  transactionVolume: 0,
  volumeChange: 0,
  activeAlerts: '0',
  totalTraded: 0,
  tradedChange: 0,
  highAlert: false
});

const transactions = ref<Transaction[]>([]);

// Périodes disponibles
const periods = [
  { label: '24h', value: '24h' },
  { label: '7j', value: '7d' },
  { label: '30j', value: '30d' },
  { label: '90j', value: '90d' }
];

// Formatage
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('fr-FR', { 
    minimumFractionDigits: 2,
    maximumFractionDigits: 2 
  }).format(value);
};

const formatAmount = (value: number): string => {
  return new Intl.NumberFormat('fr-FR', { 
    minimumFractionDigits: 2,
    maximumFractionDigits: 8 
  }).format(value);
};

const formatDate = (date: string): string => {
  return new Date(date).toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Actions
const ensureFullAvatarUrl = (avatar: string | undefined): string | undefined => {
  if (!avatar) return undefined;
  return avatar.startsWith('http') ? avatar : `http://localhost:8000/storage/${avatar}`;
};

const refreshData = async () => {
  try {
    const response = await api.get('/admin/dashboard');
    if (response.data?.stats) {
      stats.value = response.data.stats;
      // Ensure avatars are full URLs
      if (response.data.stats.recentTransactions) {
        response.data.stats.recentTransactions.forEach((tx: any) => {
          if (tx.user?.avatar) {
            tx.user.avatar = ensureFullAvatarUrl(tx.user.avatar);
          }
        });
      }
    }
    if (response.data?.transactions) {
      transactions.value = response.data.transactions;
      // Ensure avatars are full URLs
      transactions.value.forEach((tx: any) => {
        if (tx.user?.avatar) {
          tx.user.avatar = ensureFullAvatarUrl(tx.user.avatar);
        }
      });
    }
  } catch (error: any) {
    console.log('Admin dashboard - Chargement des stats échoué (normal si pas admin):', error.response?.status);
    // Les données par défaut resteront affichées
  }
};

const exportReport = async () => {
  try {
    const response = await api.get('/admin/transactions/export', {
      responseType: 'blob'
    });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `rapport-${new Date().toISOString()}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error('Erreur lors de l\'export du rapport:', error);
  }
};

// Chargement initial
onMounted(async () => {
  await refreshData();
});
</script>