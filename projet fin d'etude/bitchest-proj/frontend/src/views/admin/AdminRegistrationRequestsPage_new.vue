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
                  <div class="flex items-center gap-2 flex-wrap">
                    <button
                      v-if="!req.is_approved && !req.is_rejected"
                      @click="approve(req.id)"
                      class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-sm hover:shadow"
                      title="Approuver la demande"
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
                      title="Rejeter la demande"
                    >
                      <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      Rejeter
                    </button>

                    <button
                      @click="showDetails(req)"
                      class="inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition-all duration-200"
                      title="Voir/Modifier les détails"
                    >
                      <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      Modifier
                    </button>

                    <button
                      v-if="!req.is_approved"
                      @click="deleteRequest(req.id)"
                      class="inline-flex items-center px-3 py-1.5 rounded-lg bg-orange-100 text-orange-700 hover:bg-orange-200 transition-all duration-200"
                      title="Supprimer la demande"
                    >
                      <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Supprimer
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

      <!-- Modal de rejet -->
      <div v-if="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
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

      <!-- Modal voir/modifier détails -->
      <div v-if="showDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-xl font-bold text-gray-900">{{ isEditingDetails ? 'Modifier' : 'Détails de' }} la demande</h3>
              <button
                @click="closeDetailsModal"
                class="text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="space-y-4 mb-6">
              <!-- Nom -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                <input
                  v-model="detailsForm.name"
                  type="text"
                  :disabled="!isEditingDetails"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
                  placeholder="Nom complet"
                />
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input
                  v-model="detailsForm.email"
                  type="email"
                  :disabled="!isEditingDetails"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
                  placeholder="Email"
                />
              </div>

              <!-- Rôle -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                <select
                  v-model="detailsForm.role"
                  :disabled="!isEditingDetails"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
                >
                  <option value="client">Client</option>
                  <option value="trader">Trader</option>
                  <option value="analyst">Analyst</option>
                  <option value="admin">Admin</option>
                </select>
              </div>

              <!-- Statut -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <div class="flex items-center gap-2">
                  <span v-if="detailsForm.is_approved" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    ✓ Approuvé
                  </span>
                  <span v-else-if="detailsForm.is_rejected" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    ✕ Rejeté
                  </span>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    ⏳ En attente
                  </span>
                </div>
              </div>

              <!-- Date de création -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de création</label>
                <input
                  type="text"
                  :value="formatDate(detailsForm.created_at)"
                  disabled
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                />
              </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end gap-3">
              <button
                v-if="!isEditingDetails && !detailsForm.is_approved && !detailsForm.is_rejected"
                @click="startEditingDetails"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
              >
                Modifier
              </button>
              <button
                v-if="isEditingDetails"
                @click="cancelEditingDetails"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
              >
                Annuler
              </button>
              <button
                v-if="isEditingDetails"
                @click="saveDetails"
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
              >
                Enregistrer
              </button>
              <button
                @click="closeDetailsModal"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
              >
                Fermer
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
import {
  getAdminRegistrationRequests,
  approveRegistrationRequest,
  rejectRegistrationRequest,
  getRegistrationRequestDetails,
  updateRegistrationRequest,
  deleteRegistrationRequest
} from '@/services/adminApi'

const requests = ref<any[]>([])
const currentStatus = ref<string>('pending')
const showRejectModal = ref(false)
const showDetailsModal = ref(false)
const rejectReason = ref('')
const selectedRequestId = ref<number | null>(null)
const isEditingDetails = ref(false)
const detailsForm = reactive({
  name: '',
  email: '',
  role: 'client',
  is_approved: false,
  is_rejected: false,
  created_at: ''
})

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
    const [pendingRes, approvedRes, rejectedRes] = await Promise.all([
      getAdminRegistrationRequests(1, 1, 'pending'),
      getAdminRegistrationRequests(1, 1, 'approved'),
      getAdminRegistrationRequests(1, 1, 'rejected')
    ])
    
    statusCounts.pending = pendingRes.pagination?.total || pendingRes.data?.length || 0
    statusCounts.approved = approvedRes.pagination?.total || approvedRes.data?.length || 0
    statusCounts.rejected = rejectedRes.pagination?.total || rejectedRes.data?.length || 0
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
  showRejectModal.value = true
}

function cancelReject() {
  showRejectModal.value = false
  selectedRequestId.value = null
  rejectReason.value = ''
}

async function confirmReject() {
  if (!selectedRequestId.value || !rejectReason.value.trim()) return
  
  try {
    await rejectRegistrationRequest(selectedRequestId.value, rejectReason.value)
    await loadRequests(currentStatus.value)
    showRejectModal.value = false
    alert('❌ Demande rejetée')
  } catch (e: any) {
    alert(e?.message || 'Erreur lors du rejet')
  } finally {
    selectedRequestId.value = null
    rejectReason.value = ''
  }
}

async function showDetails(req: any) {
  try {
    const res = await getRegistrationRequestDetails(req.id)
    const data = res.data
    detailsForm.name = data.user?.name || data.name || ''
    detailsForm.email = data.email || ''
    detailsForm.role = data.role || 'client'
    detailsForm.is_approved = data.is_approved || false
    detailsForm.is_rejected = data.is_rejected || false
    detailsForm.created_at = data.created_at || ''
    selectedRequestId.value = data.id
    isEditingDetails.value = false
    showDetailsModal.value = true
  } catch (e: any) {
    alert('Erreur lors de la récupération des détails')
  }
}

function startEditingDetails() {
  isEditingDetails.value = true
}

function cancelEditingDetails() {
  isEditingDetails.value = false
}

async function saveDetails() {
  if (!selectedRequestId.value) return
  
  try {
    await updateRegistrationRequest(selectedRequestId.value, {
      name: detailsForm.name,
      email: detailsForm.email,
      role: detailsForm.role
    })
    await loadRequests(currentStatus.value)
    isEditingDetails.value = false
    showDetailsModal.value = false
    alert('✅ Demande modifiée avec succès')
  } catch (e: any) {
    alert(e?.message || 'Erreur lors de la modification')
  }
}

function closeDetailsModal() {
  showDetailsModal.value = false
  isEditingDetails.value = false
  selectedRequestId.value = null
}

async function deleteRequest(id: number) {
  if (!confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette demande ? Cette action est irréversible.')) return
  
  try {
    await deleteRegistrationRequest(id)
    await loadRequests(currentStatus.value)
    alert('✅ Demande supprimée avec succès')
  } catch (e: any) {
    alert(e?.message || 'Erreur lors de la suppression')
  }
}

onMounted(() => {
  loadRequests('pending')
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
