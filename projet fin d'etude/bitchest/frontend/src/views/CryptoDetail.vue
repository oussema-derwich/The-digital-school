<template>
  <div class="min-h-screen bg-gray-100 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- En-tête -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-4">
            <img 
              :src="crypto?.logo_url" 
              :alt="crypto?.name"
              class="w-12 h-12 rounded-full"
            >
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ crypto?.name }}</h1>
              <div class="flex items-center gap-2">
                <span class="text-lg text-gray-500">{{ crypto?.symbol }}</span>
                <span class="text-sm bg-gray-100 px-2 py-0.5 rounded">
                  Rang #{{ crypto?.rank }}
                </span>
              </div>
            </div>
          </div>
          <div class="text-right">
            <div class="text-3xl font-bold">{{ formatPrice(crypto?.price) }}</div>
            <div class="flex items-center justify-end gap-2">
              <div :class="[
                'text-sm font-medium px-2 py-0.5 rounded',
                crypto?.price_change_24h > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
              ]">
                {{ formatVariation(crypto?.price_change_24h) }} (24h)
              </div>
              <div :class="[
                'text-sm font-medium',
                crypto?.price_change_7d > 0 ? 'text-green-600' : 'text-red-600'
              ]">
                {{ formatVariation(crypto?.price_change_7d) }} (7j)
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">Volume (24h)</span>
              <span class="text-xs text-blue-600 cursor-help" title="Volume total des échanges sur les dernières 24h">ℹ️</span>
            </div>
            <div class="text-lg font-semibold mt-1">{{ formatPrice(crypto?.volume_24h) }}</div>
          </div>

          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">Capitalisation</span>
              <span class="text-xs text-blue-600 cursor-help" title="Valeur totale des tokens en circulation">ℹ️</span>
            </div>
            <div class="text-lg font-semibold mt-1">{{ formatPrice(crypto?.market_cap) }}</div>
          </div>

          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">Prix max (ATH)</span>
              <span class="text-xs text-blue-600 cursor-help" title="Plus haut prix historique">ℹ️</span>
            </div>
            <div class="text-lg font-semibold mt-1">{{ formatPrice(crypto?.ath) }}</div>
            <div class="text-xs text-gray-500 mt-1">
              {{ formatDate(crypto?.ath_date) }}
            </div>
          </div>

          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">ROI depuis ATH</span>
              <span class="text-xs text-blue-600 cursor-help" title="Retour sur investissement depuis le plus haut historique">ℹ️</span>
            </div>
            <div :class="[
              'text-lg font-semibold mt-1',
              roiFromATH > 0 ? 'text-green-600' : 'text-red-600'
            ]">
              {{ formatVariation(roiFromATH) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Graphique -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium mb-4">Évolution du prix</h2>
        <div class="flex gap-4 mb-4">
          <button 
            v-for="period in ['7J', '30J', '90J']" 
            :key="period"
            @click="changePeriod(period)"
            :class="[
              'px-3 py-1 rounded-md',
              selectedPeriod === period 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-200 hover:bg-gray-300'
            ]"
          >
            {{ period }}
          </button>
        </div>
        <div class="h-96">
          <canvas ref="priceChart"></canvas>
        </div>
      </div>

      <!-- Indicateurs de marché -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium mb-4">Indicateurs de marché</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="text-sm text-gray-500">Capitalisation</div>
            <div class="text-lg font-semibold">{{ formatPrice(crypto?.market_cap) }}</div>
          </div>
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="text-sm text-gray-500">Volume (24h)</div>
            <div class="text-lg font-semibold">{{ formatPrice(crypto?.volume_24h) }}</div>
          </div>
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="text-sm text-gray-500">Offre totale</div>
            <div class="text-lg font-semibold">{{ formatNumber(crypto?.total_supply) }} {{ crypto?.symbol }}</div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Achat/Vente -->
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-medium">Trader</h2>
            <div class="text-sm text-gray-500">
              <span class="font-medium text-gray-900">{{ formatPrice(userBalance) }}</span> disponible
            </div>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Type d'opération -->
            <div class="flex gap-4">
              <button
                v-if="userBalance > 0"
                type="button"
                @click="form.type = 'buy'; form.amount = 0"
                :class="[
                  'flex-1 py-3 px-4 rounded-lg font-medium text-center',
                  form.type === 'buy' 
                    ? 'bg-blue-600 text-white shadow-sm' 
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                Acheter
              </button>
              <button
                v-if="availableQuantity > 0"
                type="button"
                @click="form.type = 'sell'; form.amount = 0"
                :class="[
                  'flex-1 py-3 px-4 rounded-lg font-medium text-center',
                  form.type === 'sell' 
                    ? 'bg-blue-600 text-white shadow-sm' 
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                Vendre
              </button>
            </div>

            <!-- Montant/Quantité -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ form.type === 'buy' ? 'Montant à investir (EUR)' : `Quantité à vendre (${crypto?.symbol})` }}
              </label>
              <div class="relative">
                <input
                  type="number"
                  v-model="form.amount"
                  min="0"
                  :step="form.type === 'buy' ? 1 : 0.000001"
                  :max="form.type === 'sell' ? availableQuantity : userBalance"
                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  :placeholder="form.type === 'buy' ? 'Ex: 1000' : 'Ex: 0.5'"
                />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                  {{ form.type === 'buy' ? '€' : crypto?.symbol }}
                </span>
              </div>
            </div>

            <!-- Résumé de la transaction -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Frais (2%)</span>
                <span class="font-medium">{{ formatPrice(fees) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">
                  {{ form.type === 'buy' ? 'Quantité estimée' : 'Montant estimé' }}
                </span>
                <span class="font-medium">
                  {{ form.type === 'buy' 
                    ? formatQuantity(estimatedQuantity) + ' ' + crypto?.symbol
                    : formatPrice(estimatedAmount)
                  }}
                </span>
              </div>
              <div class="pt-3 border-t">
                <div class="flex justify-between">
                  <span class="font-medium">Total</span>
                  <span class="font-bold text-lg">{{ formatPrice(totalAmount) }}</span>
                </div>
              </div>
            </div>

            <button
              type="submit"
              :disabled="!isValidForm || loading"
              class="w-full py-3 px-4 rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed shadow-sm transition-colors"
            >
              <template v-if="loading">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                En cours...
              </template>
              <template v-else>
                {{ form.type === 'buy' ? 'Confirmer l\'achat' : 'Confirmer la vente' }}
              </template>
            </button>
          </form>
        </div>

        <!-- Alertes -->
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-medium">Alertes de prix</h2>
            <button
              v-if="!showNewAlertForm && userAlerts.length < 5"
              @click="showNewAlertForm = true"
              class="text-blue-600 hover:text-blue-700 font-medium text-sm"
            >
              + Nouvelle alerte
            </button>
            <span 
              v-else-if="userAlerts.length >= 5"
              class="text-sm text-gray-500"
              title="Vous ne pouvez pas créer plus de 5 alertes par crypto"
            >
              Limite atteinte (5/5)
            </span>
          </div>

          <!-- Formulaire nouvelle alerte -->
          <form v-if="showNewAlertForm" @submit.prevent="handleCreateAlert" class="mb-6 space-y-4">
            <div class="flex gap-4">
              <button
                type="button"
                v-for="type in ['above', 'below']"
                :key="type"
                @click="alertForm.type = type"
                :class="[
                  'flex-1 py-2 px-4 rounded-lg font-medium text-center',
                  alertForm.type === type 
                    ? 'bg-blue-600 text-white shadow-sm' 
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                {{ type === 'above' ? 'Au dessus' : 'En dessous' }}
              </button>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Prix seuil (EUR)</label>
              <div class="relative">
                <input
                  type="number"
                  v-model="alertForm.price_threshold"
                  min="0"
                  step="0.01"
                  class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  :placeholder="crypto?.price.toFixed(2)"
                />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">€</span>
              </div>
            </div>

            <div class="flex gap-4">
              <button
                type="button"
                @click="showNewAlertForm = false"
                class="flex-1 py-2 px-4 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="!isValidAlert || alertLoading"
                class="flex-1 py-2 px-4 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
              >
                {{ alertLoading ? 'Création...' : 'Confirmer' }}
              </button>
            </div>
          </form>

          <!-- Liste des alertes -->
          <div v-if="!showNewAlertForm">
            <div v-if="userAlerts.length === 0" class="text-center py-8 text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <p>Aucune alerte configurée</p>
              <button
                @click="showNewAlertForm = true"
                class="mt-2 text-blue-600 hover:text-blue-700 font-medium"
              >
                Créer une alerte
              </button>
            </div>
            
            <div v-else class="space-y-4">
              <div 
                v-for="alert in userAlerts" 
                :key="alert.id"
                class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <div class="font-medium">
                      {{ formatPrice(alert.price_threshold) }}
                      <span class="text-gray-500 text-sm ml-1">
                        ({{ alert.type === 'above' ? 'Au dessus' : 'En dessous' }})
                      </span>
                    </div>
                    <p class="text-sm text-gray-600">
                      Créée le {{ formatDate(alert.created_at) }}
                    </p>
                  </div>
                  <div class="flex items-center gap-3">
                    <button 
                      @click="toggleAlert(alert)"
                      :class="[
                        'p-1.5 rounded-lg transition-colors',
                        alert.active 
                          ? 'text-green-600 hover:bg-green-100' 
                          : 'text-gray-400 hover:bg-gray-200'
                      ]"
                      :title="alert.active ? 'Désactiver l\'alerte' : 'Activer l\'alerte'"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                      </svg>
                    </button>
                    <button 
                      @click="deleteAlert(alert.id)"
                      class="p-1.5 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                      title="Supprimer l'alerte"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

export default defineComponent({
  name: 'CryptoDetail',

  setup() {
    const route = useRoute()
    const crypto = ref<any>(null)
    const loading = ref(false)
    const alertLoading = ref(false)
    const priceChart = ref<HTMLCanvasElement | null>(null)
    const chartInstance = ref<Chart | null>(null)
    const selectedPeriod = ref('30J')
    const showNewAlertForm = ref(false)
    const userAlerts = ref<any[]>([])
    const updateInterval = ref<number | null>(null)

    // États du formulaire d'achat/vente
    const form = ref({
      type: 'buy',
      amount: 0 // Montant en EUR pour l'achat, quantité pour la vente
    })

    // États du formulaire d'alerte
    const alertForm = ref({
      type: 'above',
      price_threshold: 0
    })

    // Données utilisateur
    const userBalance = ref(0)
    const availableQuantity = ref(0)

    const loadUserData = async () => {
      try {
        const [profileRes, portfolioRes] = await Promise.all([
          api.get('/auth/me'),
          api.get('/portfolio')
        ])
        userBalance.value = profileRes.data.data?.balance_eur || 0
        const holdings = portfolioRes.data.data?.holdings || []
        const cryptoAsset = holdings.find(
          (asset: any) => asset.crypto_id === crypto.value?.id
        )
        availableQuantity.value = cryptoAsset?.quantity || 0
      } catch (error) {
        console.error('Erreur lors du chargement des données utilisateur:', error)
      }
    }

    const loadCrypto = async () => {
      try {
        const [cryptoRes, alertsRes] = await Promise.all([
          api.get(`/cryptos/${route.params.id}`),
          api.get('/alerts', { params: { crypto_id: route.params.id } })
        ])
        crypto.value = cryptoRes.data.data || cryptoRes.data
        userAlerts.value = alertsRes.data.data || alertsRes.data
        await loadUserData()
      } catch (error) {
        console.error('Erreur lors du chargement des données:', error)
      }
    }

    // Calculs pour le formulaire d'achat/vente
    const fees = computed(() => {
      const amount = form.value.type === 'buy'
        ? form.value.amount
        : form.value.amount * (crypto.value?.price || 0)
      return amount * 0.02 // 2% de frais
    })

    const totalAmount = computed(() => {
      const baseAmount = form.value.type === 'buy'
        ? form.value.amount
        : form.value.amount * (crypto.value?.price || 0)
      return baseAmount + fees.value
    })

    const estimatedQuantity = computed(() => {
      if (form.value.type === 'sell') return form.value.amount
      return form.value.amount / (crypto.value?.price || 1)
    })

    const estimatedAmount = computed(() => {
      return form.value.amount * (crypto.value?.price || 0)
    })

    const roiFromATH = computed(() => {
      if (!crypto.value?.ath || !crypto.value?.price) return 0
      return ((crypto.value.price - crypto.value.ath) / crypto.value.ath) * 100
    })

    const isValidForm = computed(() => {
      if (form.value.amount <= 0) return false
      if (form.value.type === 'buy' && totalAmount.value > userBalance.value) return false
      if (form.value.type === 'sell' && form.value.amount > availableQuantity.value) return false
      return true
    })

    const isValidAlert = computed(() => 
      alertForm.value.price_threshold > 0 && 
      userAlerts.value.length < 5 // Maximum 5 alertes par crypto
    )

    // Actions
    const handleSubmit = async () => {
      if (!isValidForm.value) return
      loading.value = true
      try {
        await api.post('/transactions', {
          crypto_id: crypto.value.id,
          type: form.value.type,
          quantity: form.value.type === 'buy' ? estimatedQuantity.value : form.value.amount
        })
        form.value.amount = 0
        await loadUserData()
      } catch (error) {
        console.error('Erreur lors de la transaction:', error)
      } finally {
        loading.value = false
      }
    }

    const handleCreateAlert = async () => {
      if (!isValidAlert.value) return
      alertLoading.value = true
      try {
        const response = await api.post('/alerts', {
          crypto_id: crypto.value.id,
          type: alertForm.value.type,
          price_threshold: alertForm.value.price_threshold
        })
        userAlerts.value.push(response.data)
        alertForm.value.price_threshold = 0
        showNewAlertForm.value = false
      } catch (error) {
        console.error("Erreur lors de la création de l'alerte:", error)
      } finally {
        alertLoading.value = false
      }
    }

    const toggleAlert = async (alert: any) => {
      try {
        await api.patch(`/alerts/${alert.id}/toggle`)
        alert.active = !alert.active
      } catch (error) {
        console.error("Erreur lors de la modification de l'alerte:", error)
      }
    }

    const deleteAlert = async (alertId: number) => {
      try {
        await api.delete(`/alerts/${alertId}`)
        userAlerts.value = userAlerts.value.filter(a => a.id !== alertId)
      } catch (error) {
        console.error("Erreur lors de la suppression de l'alerte:", error)
      }
    }

    // Formatage des données
    const formatDate = (date: string) => {
      return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      })
    }

    const formatPrice = (price: number) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price || 0)
    }

    const formatQuantity = (quantity: number) => {
      return Number(quantity?.toFixed(8)).toString()
    }

    const formatVariation = (variation: number) => {
      const prefix = variation > 0 ? '+' : ''
      return `${prefix}${variation?.toFixed(2)}%`
    }

    const formatNumber = (num: number) => {
      return new Intl.NumberFormat('fr-FR').format(num || 0)
    }

    // Graphique
    const initChart = (data: any[]) => {
      if (!priceChart.value || !data || data.length === 0) return
      
      if (chartInstance.value) {
        chartInstance.value.destroy()
      }

      const ctx = priceChart.value.getContext('2d')
      if (!ctx) return

      chartInstance.value = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.map(d => new Date(d.date).toLocaleDateString('fr-FR')),
          datasets: [{
            label: 'Prix',
            data: data.map(d => d.price),
            borderColor: '#2563EB',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#2563EB',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
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
                  return `Prix: ${formatPrice(context.raw as number)}`
                }
              }
            }
          },
          interaction: {
            mode: 'index',
            intersect: false
          },
          scales: {
            y: {
              beginAtZero: false,
              grid: {
                display: true,
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                callback: (value) => formatPrice(value as number)
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      })
    }

    const changePeriod = async (period: string) => {
      selectedPeriod.value = period
      try {
        const days = period === '7J' ? 7 : period === '30J' ? 30 : 90
        const response = await api.get(`/cryptos/${route.params.id}/history/${days}`)
        const historyData = response.data.data?.history || response.data.data
        initChart(historyData)
      } catch (error) {
        console.error('Erreur lors du chargement de l\'historique:', error)
      }
    }

    // Cycle de vie
    onMounted(() => {
      loadCrypto()
      changePeriod('30J')
      // Actualisation toutes les 30 secondes
      updateInterval.value = window.setInterval(loadCrypto, 30000)
    })

    onUnmounted(() => {
      if (chartInstance.value) {
        chartInstance.value.destroy()
      }
      if (updateInterval.value) {
        clearInterval(updateInterval.value)
      }
    })

    return {
      crypto,
      form,
      alertForm,
      loading,
      alertLoading,
      totalAmount,
      availableQuantity,
      userBalance,
      isValidForm,
      isValidAlert,
      showNewAlertForm,
      userAlerts,
      estimatedQuantity,
      estimatedAmount,
      fees,
      roiFromATH,
      handleSubmit,
      handleCreateAlert,
      toggleAlert,
      deleteAlert,
      formatPrice,
      formatQuantity,
      formatVariation,
      formatDate,
      priceChart,
      selectedPeriod,
      changePeriod,
      formatNumber
    }
  }
})
</script>