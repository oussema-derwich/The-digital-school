<template>
  <AdminLayout pageTitle="Gestion des Inscriptions" pageDescription="Gérez les demandes d'inscription des utilisateurs">
    <div class="max-w-7xl mx-auto">
      <!-- Filtres -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div class="flex flex-wrap gap-2">
            <button
              @click="loadRequests('pending')"
              :class="[
                'px-4 py-2.5 rounded-lg font-medium transition-all duration-200',
                currentStatus === 'pending'
                  ? 'bg-blue-50 text-blue-700 border border-blue-200'
                  : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'
              ]"
            >
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>En attente</span>
                <span v-if="statusCounts.pending > 0" class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                  {{ statusCounts.pending }}
                </span>
              </div>
            </button>
            
            <button
              @click="loadRequests('approved')"
              :class="[
                'px-4 py-2.5 rounded-lg font-medium transition-all duration-200',
                currentStatus === 'approved'
                  ? 'bg-green-50 text-green-700 border border-green-200'
                  : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'
              ]"
            >
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Approuvées</span>
                <span v-if="statusCounts.approved > 0" class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                  {{ statusCounts.approved }}
                </span>
              </div>
            </button>
            
            <button
              @click="loadRequests('rejected')"
              :class="[
                'px-4 py-2.5 rounded-lg font-medium transition-all duration-200',
                currentStatus === 'rejected'
                  ? 'bg-red-50 text-red-700 border border-red-200'
                  : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'
              ]"
            >
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Rejetées</span>
                <span v-if="statusCounts.rejected > 0" class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                  {{ statusCounts.rejected }}
                </span>
              </div>
            </button>
          </div>

          <!-- Statistiques -->
          <div class="flex items-center gap-4 text-sm text-gray-600">
            <div class="flex items-center gap-1">
              <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              <span>{{ statusCounts.pending }} en attente</span>
            </div>
            <div class="flex items-center gap-1">
              <div class="w-3 h-3 bg-green-500 rounded-full"></div>
              <span>{{ statusCounts.approved }} approuvées</span>
            </div>
            <div class="flex items-center gap-1">
              <div class="w-3 h-3 bg-red-500 rounded-full"></div>
              <span>{{ statusCounts.rejected }} rejetées</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tableau -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Utilisateur
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Rôle
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Date
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Statut
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="req in requests" :key="req.id" class="hover:bg-gray-50 transition-colors duration-150">
                <!-- Informations utilisateur -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                      <span class="text-blue-600 font-semibold">
                        {{ (req.user?.name ?? req.name ?? 'U').charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ req.user?.name ?? req.name ?? 'Non spécifié' }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ req.email }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Rôle -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                    {{ req.role || 'Non défini' }}
                  </span>
                </td>

                <!-- Date -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(req.created_at) }}
                </td>

                <!-- Statut -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="req.is_approved" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Approuvé
                  </span>
                  <span v-else-if="req.is_rejected" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Rejeté
                  </span>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    En attente
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center gap-2">
                    <button
                      v-if="!req.is_approved && !req.is_rejected"
                      @click="approve(req.id)"
                      class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-sm hover:shadow"
                    >
                      <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      Accepter
                    </button>
                    
                    <button
                      v-if="!req.is_rejected && !req.is_approved"
                      @click="rejectPrompt(req.id)"
                      class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-sm hover:shadow"
                    >
                      <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      Rejeter
                    </button>

                    <button
                      @click="showDetails(req)"
                      class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all duration-200"
                    >
                      <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Détails
                    </button>
                  </div>
                </td>
              </tr>

              <!-- État vide -->
              <tr v-if="requests.length === 0">
                <td colspan="5" class="px-6 py-16 text-center">
                  <div class="flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-lg font-medium text-gray-600 mb-2">Aucune demande</p>
                    <p class="text-gray-500">Aucune demande {{ getStatusText(currentStatus) }} pour le moment.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal de confirmation -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
          <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Rejeter la demande</h3>
            <p class="text-gray-600 mb-4">Veuillez indiquer la raison du rejet :</p>
            <textarea
              v-model="rejectReason"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
              rows="3"
              placeholder="Explication du rejet..."
            ></textarea>
            <div class="flex justify-end gap-3 mt-6">
              <button
                @click="cancelReject"
                class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200"
              >
                Annuler
              </button>
              <button
                @click="confirmReject"
                :disabled="!rejectReason.trim()"
                :class="[
                  'px-4 py-2 rounded-lg transition-all duration-200',
                  rejectReason.trim()
                    ? 'bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700'
                    : 'bg-gray-200 text-gray-500 cursor-not-allowed'
                ]"
              >
                Confirmer le rejet
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import AdminLayout from './AdminLayout.vue'
import { getAdminRegistrationRequests, approveRegistrationRequest, rejectRegistrationRequest } from '@/services/adminApi'

const requests = ref<any[]>([])
const currentStatus = ref<string>('pending')
const showModal = ref(false)
const rejectReason = ref('')
const selectedRequestId = ref<number | null>(null)

const statusCounts = reactive({
  pending: 0,
  approved: 0,
  rejected: 0
})

async function loadRequests(status: string = 'pending') {
  try {
    currentStatus.value = status
    const res = await getAdminRegistrationRequests(1, 100, status !== 'all' ? status : undefined)
    requests.value = res.data || res || []
    
    // Charger les statistiques
    loadStatistics()
  } catch (e) {
    console.error('Error loading requests', e)
    requests.value = []
  }
}

async function loadStatistics() {
  try {
    // Charger les comptes pour chaque statut
    const [pendingRes, approvedRes, rejectedRes] = await Promise.all([
      getAdminRegistrationRequests(1, 1, 'pending'),
      getAdminRegistrationRequests(1, 1, 'approved'),
      getAdminRegistrationRequests(1, 1, 'rejected')
    ])
    
    statusCounts.pending = pendingRes.pagination?.total || 0
    statusCounts.approved = approvedRes.pagination?.total || 0
    statusCounts.rejected = rejectedRes.pagination?.total || 0
  } catch (e) {
    console.error('Error loading statistics', e)
  }
}

function formatDate(d: string | undefined) {
  if (!d) return '—'
  try {
    const date = new Date(d)
    return date.toLocaleDateString('fr-FR', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return d
  }
}

function getStatusText(status: string) {
  const texts: { [key: string]: string } = {
    pending: "en attente",
    approved: "approuvée",
    rejected: "rejetée",
    all: ""
  }
  return texts[status] || ""
}

async function approve(id: number) {
  if (!confirm('Êtes-vous sûr de vouloir approuver cette demande ?')) return
  try {
    await approveRegistrationRequest(id)
    await loadRequests(currentStatus.value)
    alert('✅ Demande approuvée avec succès')
  } catch (e: any) {
    alert(e?.message || '❌ Erreur lors de l\'approbation')
  }
}

function rejectPrompt(id: number) {
  selectedRequestId.value = id
  rejectReason.value = ''
  showModal.value = true
}

function cancelReject() {
  showModal.value = false
  selectedRequestId.value = null
  rejectReason.value = ''
}

async function confirmReject() {
  if (!selectedRequestId.value || !rejectReason.value.trim()) return
  
  try {
    await rejectRegistrationRequest(selectedRequestId.value, rejectReason.value)
    await loadRequests(currentStatus.value)
    showModal.value = false
    alert('❌ Demande rejetée')
  } catch (e: any) {
    alert(e?.message || 'Erreur lors du rejet')
  } finally {
    selectedRequestId.value = null
    rejectReason.value = ''
  }     
}

function showDetails(req: any) {
  // Implémentez une modal de détails si nécessaire
  console.log('Détails de la demande:', req)
}

onMounted(() => {
  loadRequests('pending')
})
</script>

<style scoped>
/* Ajoutez des animations si nécessaire */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>