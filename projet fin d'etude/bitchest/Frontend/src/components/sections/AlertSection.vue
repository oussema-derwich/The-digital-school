<template>
  <div class="container mx-auto p-4 sm:p-6">
    <h1
      class="text-2xl sm:text-3xl font-bold text-center text-bitchest-black mb-4 sm:mb-6"
    >
      Alert Management
    </h1>

    <!-- Button to create an alert -->
    <div class="flex justify-end mb-4">
      <button
        @click="openCreateModal"
        class="bg-bitchest-success text-black font-bold px-6 py-2 rounded-md hover:bg-gray-200"
      >
        Create Alert
      </button>
    </div>

    <!-- Modal for creating an alert -->
    <div
      v-if="showCreateModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-40"
      role="dialog"
      aria-labelledby="create-modal-title"
      aria-modal="true"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 id="create-modal-title" class="text-lg font-bold mb-4">
          Create Alert
        </h2>
        <form @submit.prevent="handleCreateAlert">
          <!-- Alert Name -->
          <div class="mb-4">
            <label
              for="alert-name"
              class="block text-sm font-medium text-gray-700"
            >
              Alert Name
            </label>
            <input
              v-model="newAlert.name"
              type="text"
              id="alert-name"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>

          <!-- Crypto Selection -->
          <div class="mb-4">
            <label
              for="crypto-select"
              class="block text-sm font-medium text-gray-700"
            >
              Crypto
            </label>
            <select
              v-model="newAlert.crypto_id"
              id="crypto-select"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              required
              @change="updateCurrentPrice"
            >
              <option
                v-for="crypto in cryptos"
                :key="crypto.id"
                :value="crypto.id"
              >
                {{ crypto.name }}
              </option>
            </select>
          </div>

          <!-- Current Price -->
          <div class="mb-4">
            <p class="text-sm text-gray-700">
              <strong>Current Price:</strong> {{ currentPrice }} €
            </p>
          </div>

          <!-- Alert Price -->
          <div class="mb-4">
            <label
              for="alert-price"
              class="block text-sm font-medium text-gray-700"
            >
              Alert Price
            </label>
            <input
              v-model="newAlert.price_alert"
              type="number"
              id="alert-price"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>

          <!-- Condition -->
          <div class="mb-4">
            <label
              for="alert-condition"
              class="block text-sm font-medium text-gray-700"
            >
              Condition
            </label>
            <select
              v-model="newAlert.condition"
              id="alert-condition"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              required
            >
              <option value="less_than">Less Than (Price < Alert Price)</option>
              <option value="greater_than">
                Greater Than (Price > Alert Price)
              </option>
            </select>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end">
            <button
              type="button"
              @click="closeCreateModal"
              class="px-4 py-2 mr-2 bg-gray-300 rounded hover:bg-gray-400"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
              Validate
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal for editing an alert -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-40"
      role="dialog"
      aria-labelledby="edit-modal-title"
      aria-modal="true"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 id="edit-modal-title" class="text-lg font-bold mb-4">Edit Alert</h2>
        <form @submit.prevent="handleEditAlert">
          <!-- Alert Name -->
          <div class="mb-4">
            <label
              for="edit-alert-name"
              class="block text-sm font-medium text-gray-700"
            >
              Alert Name
            </label>
            <input
              v-model="alertToEdit.name"
              type="text"
              id="edit-alert-name"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>

          <!-- Crypto Selection -->
          <div class="mb-4">
            <label
              for="edit-crypto-select"
              class="block text-sm font-medium text-gray-700"
            >
              Crypto
            </label>
            <select
              v-model="alertToEdit.crypto_id"
              id="edit-crypto-select"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              required
              @change="updateCurrentPrice"
            >
              <option
                v-for="crypto in cryptos"
                :key="crypto.id"
                :value="crypto.id"
              >
                {{ crypto.name }}
              </option>
            </select>
          </div>

          <!-- Current Price -->
          <div class="mb-4">
            <p class="text-sm text-gray-700">
              <strong>Current Price:</strong> {{ currentPrice }} €
            </p>
          </div>

          <!-- Alert Price -->
          <div class="mb-4">
            <label
              for="edit-alert-price"
              class="block text-sm font-medium text-gray-700"
            >
              Alert Price
            </label>
            <input
              v-model="alertToEdit.price_alert"
              type="number"
              id="edit-alert-price"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>

          <!-- Condition -->
          <div class="mb-4">
            <label
              for="edit-alert-condition"
              class="block text-sm font-medium text-gray-700"
            >
              Condition
            </label>
            <select
              v-model="alertToEdit.condition"
              id="edit-alert-condition"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              required
            >
              <option value="less_than">Less Than (Price < Alert Price)</option>
              <option value="greater_than">
                Greater Than (Price > Alert Price)
              </option>
            </select>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end">
            <button
              type="button"
              @click="closeEditModal"
              class="px-4 py-2 mr-2 bg-gray-300 rounded hover:bg-gray-400"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
              Validate
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirmation modal for deletion -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center"
      role="dialog"
      aria-labelledby="modal-title"
      aria-modal="true"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 id="modal-title" class="text-lg font-bold">Confirm Deletion</h2>
        <p class="mt-2">Are you sure you want to delete this alert?</p>
        <div class="flex justify-end mt-4">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 mr-2 bg-gray-300 rounded"
            aria-label="Cancel"
          >
            Cancel
          </button>
          <button
            @click="handleDeleteAlert"
            class="px-4 py-2 bg-red-600 text-white rounded"
            aria-label="Confirm Deletion"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div v-if="loading" class="text-center w-full">
      <Loader />
    </div>

    <div
      v-else-if="alerts.length === 0"
      class="text-center text-gray-500 text-sm sm:text-base"
    >
      <p>No alerts found.</p>
    </div>
    <div v-else>
      <!-- Responsive version for small screens -->
      <div class="lg:hidden">
        <div
          v-for="alert in alerts"
          :key="alert.id"
          :class="{
            'bg-gray-100': alert.status === 'inactive',
            'alert-triggered': alert.status === 'triggered',
          }"
          class="relative p-6 mb-4 rounded-lg shadow-md w-full flex flex-col justify-between h-full"
        >
          <!-- Toggle Status Button  -->
          <button
            @click="handleToggleAlertStatus(alert.id)"
            class="absolute top-2 right-2 p-4 text-3xl"
            :class="{
              'text-green-500': alert.status === 'active',
              'text-gray-500': alert.status === 'inactive',
            }"
            :title="
              alert.status === 'active' ? 'Disable alert' : 'Enable alert'
            "
            aria-label="Toggle Status"
          >
            <font-awesome-icon
              :icon="alert.status === 'active' ? 'toggle-on' : 'toggle-off'"
            />
          </button>

          <!-- Contenu principal -->
          <div
            class="flex-1 pt-10 text-center text-gray-700 text-lg font-semibold flex flex-col justify-left items-center"
          >
            <p>
              <strong>Alert Name: {{ alert.name }}</strong>
            </p>
            <p class="mt-2">
              <strong
                >Alert Price: {{ formatNumber(alert.price_alert) }} €</strong
              >
              <font-awesome-icon
                :icon="
                  alert.condition === 'greater_than' ? 'arrow-up' : 'arrow-down'
                "
                class="ml-2 text-xl"
                :class="{
                  'text-green-500': alert.condition === 'greater_than',
                  'text-red-500': alert.condition === 'less_than',
                }"
              />
            </p>
            <p class="mt-1 text-base">
              Crypto Name: {{ getCryptoName(alert.crypto_id) }}
            </p>
          </div>

          <!-- Boutons Edit & Delete (En bas à gauche) -->
          <div class="mt-4 flex items-center space-x-4">
            <button
              @click="openEditModal(alert)"
              class="p-2 text-yellow-500 text-xl"
              aria-label="Edit"
              title="Edit alert"
            >
              <i class="fas fa-edit"></i>
            </button>

            <button
              @click="openDeleteModal(alert.id)"
              class="p-2 text-red-500 text-xl"
              aria-label="Delete"
              title="Delete alert"
            >
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Desktop version -->
      <div class="overflow-hidden hidden lg:block">
        <table
          class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md"
        >
          <thead class="bg-gray-100 text-gray-700 text-sm font-semibold">
            <tr>
              <th class="px-4 py-2">Alert Name</th>
              <th class="px-4 py-2">Alert Price</th>
              <th class="px-4 py-2">Crypto</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="4" class="px-4 py-2 text-center">
                <Loader />
              </td>
            </tr>
            <tr
              v-else
              v-for="alert in alerts"
              :key="alert.id"
              class="border-b border-gray-200"
              :class="{
                'bg-gray-100': alert.status === 'inactive',
                'alert-triggered': alert.status === 'triggered',
              }"
            >
              <td class="px-4 py-2 text-sm text-gray-700 text-center">
                {{ alert.name }}
              </td>
              <td class="px-4 py-2 text-sm text-gray-700 text-center">
                <div class="flex items-center justify-center gap-x-2">
                  {{ formatNumber(alert.price_alert) }} €
                  <font-awesome-icon
                    :icon="
                      alert.condition === 'greater_than'
                        ? 'arrow-alt-circle-up'
                        : 'arrow-alt-circle-down'
                    "
                    class="h-5 w-5"
                    :class="{
                      'text-green-500': alert.condition === 'greater_than',
                      'text-red-500': alert.condition === 'less_than',
                    }"
                  />
                  <span class="text-xs font-medium">
                    {{
                      alert.condition === "greater_than"
                        ? "greater than"
                        : "less than"
                    }}
                  </span>
                </div>
              </td>

              <td class="px-4 py-2 text-sm text-gray-700 text-center">
                {{ getCryptoName(alert.crypto_id) }}
              </td>
              <td class="px-4 py-2 text-sm text-gray-700 text-center">
                <div class="flex justify-center space-x-2">
                  <!-- Toggle Status Button -->
                  <button
                    @click="handleToggleAlertStatus(alert.id)"
                    class="p-3 rounded-full text-lg"
                    :class="{
                      'text-green-500': alert.status === 'active',
                      'text-gray-500': alert.status === 'inactive',
                    }"
                    :title="
                      alert.status === 'active'
                        ? 'Disable alert'
                        : 'Enable alert'
                    "
                    aria-label="Toggle Status"
                  >
                    <font-awesome-icon
                      :icon="
                        alert.status === 'active' ? 'toggle-on' : 'toggle-off'
                      "
                    />
                  </button>

                  <!-- Edit Button -->
                  <button
                    @click="openEditModal(alert)"
                    class="p-2 text-yellow-500"
                    aria-label="Edit"
                    title="Edit alert"
                  >
                    <i class="fas fa-edit"></i>
                  </button>

                  <!-- Delete Button -->
                  <button
                    @click="openDeleteModal(alert.id)"
                    class="p-2 text-bitchest-alert"
                    aria-label="Delete"
                    title="Delete alert"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import { useToast } from "vue-toastification";
import Loader from "@/components/utils/Loader.vue";
import api from "@/services/api";

export default {
  name: "AlertView",
  components: {
    Loader,
  },
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      showCreateModal: false,
      showEditModal: false,
      showDeleteModal: false,
      alertIdToDelete: null,
      alertToEdit: null,
      loading: true,
      currentPrice: 0,
      cryptos: [],
      newAlert: {
        name: "",
        crypto_id: null,
        price_alert: null,
        condition: "less_than",
        user_id: null,
      },
    };
  },
  computed: {
    ...mapState("alert", ["alerts"]),
    ...mapState("auth", ["user"]),
  },
  async created() {
    try {
      await this.fetchAlerts();
      await this.fetchCryptoCurrencies();
    } catch (error) {
      console.error("Error fetching alerts or cryptos:", error);
      this.toast.error("Failed to fetch data. ❌", {
        className:
          "bg-red-700 text-white font-bold px-4 py-3 rounded shadow-md",
      });
    } finally {
      this.loading = false;
    }
  },
  methods: {
    ...mapActions("alert", [
      "fetchAlerts",
      "deleteAlert",
      "toggleAlertStatus",
      "createAlert",
      "updateAlert",
    ]),
    ...mapActions("auth", ["fetchProfile"]),

    // Fetch cryptocurrencies
    async fetchCryptoCurrencies() {
      try {
        const response = await api.get("/cryptos/current");
        this.cryptos = response.data;
      } catch (error) {
        console.error("Error fetching cryptos:", error);
        this.toast.error("Failed to fetch cryptocurrencies. ❌", {
          className:
            "bg-red-700 text-white font-bold px-4 py-3 rounded shadow-md",
        });
      }
    },
    // Update current price when crypto is selected
    updateCurrentPrice() {
      const cryptoId = this.showCreateModal
        ? this.newAlert.crypto_id
        : this.alertToEdit.crypto_id;
      const selectedCrypto = this.cryptos.find(
        (crypto) => crypto.id === cryptoId
      );
      this.currentPrice = selectedCrypto ? selectedCrypto.currentPrice : 0;
    },
    // Open create modal
    openCreateModal() {
      this.showCreateModal = true;
      this.updateCurrentPrice();
    },

    // Close create modal
    closeCreateModal() {
      this.showCreateModal = false;
      this.newAlert = { name: "", crypto_id: null, price_alert: null };
    },

    // Handle alert creation
    async handleCreateAlert() {
      try {
        this.newAlert.user_id = this.user.id;
        this.loading = true;
        await this.createAlert(this.newAlert);
        await this.fetchAlerts();
        this.loading = false;
        this.toast.success("Alert created successfully! ✅", {
          className:
            "bg-green-700 text-white font-bold px-4 py-3 rounded shadow-md",
        });
        this.closeCreateModal();
      } catch (error) {
        console.error("Error creating alert:", error);
        this.toast.error("Failed to create alert. ❌", {
          className:
            "bg-red-700 text-white font-bold px-4 py-3 rounded shadow-md",
        });
      }
    },

    // Open edit modal
    openEditModal(alert) {
      this.alertToEdit = { ...alert };
      const selectedCrypto = this.cryptos.find(
        (crypto) => crypto.id === alert.crypto_id
      );

      this.showEditModal = true;
      this.updateCurrentPrice();
    },

    // Close edit modal
    closeEditModal() {
      this.showEditModal = false;
      this.alertToEdit = null;
    },

    async handleEditAlert() {
      try {
        this.loading = true;
        const { id, ...alertData } = this.alertToEdit;
        await this.updateAlert({ id, alert: alertData });
        await this.fetchAlerts();
        this.loading = false;
        this.toast.success("Alert updated successfully! ✅", {
          className:
            "bg-green-700 text-white font-bold px-4 py-3 rounded shadow-md",
        });
        this.closeEditModal();
      } catch (error) {
        console.error("Error updating alert:", error);
        this.toast.error("Failed to update alert. ❌", {
          className:
            "bg-red-700 text-white font-bold px-4 py-3 rounded shadow-md",
        });
      }
    },

    // Open delete modal
    openDeleteModal(alertId) {
      this.alertIdToDelete = alertId;
      this.showDeleteModal = true;
    },

    // Handle alert deletion
    async handleDeleteAlert() {
      try {
        await this.deleteAlert(this.alertIdToDelete);
        this.toast.success("Alert deleted successfully! 🗑️", {
          className:
            "bg-green-700 text-white font-bold px-4 py-3 rounded shadow-md",
        });
      } catch (error) {
        console.error("Error deleting alert:", error);
        this.toast.error("Failed to delete alert. ❌", {
          className:
            "bg-red-700 text-white font-bold px-4 py-3 rounded shadow-md",
        });
      }
      this.showDeleteModal = false;
      this.alertIdToDelete = null;
    },

    // Toggle alert status
    async handleToggleAlertStatus(alertId) {
      try {
        this.loading = true;
        await this.toggleAlertStatus(alertId);
        this.toast.success("Alert status toggled successfully! ✅", {
          className:
            "bg-green-700 text-white font-bold px-4 py-3 rounded shadow-md",
        });
        await this.fetchAlerts();
        this.loading = false;
      } catch (error) {
        console.error("Error toggling alert status:", error);
        this.toast.error("Failed to toggle alert status. ❌", {
          className:
            "bg-red-700 text-white font-bold px-4 py-3 rounded shadow-md",
        });
      }
    },
    // Get crypto name by ID
    getCryptoName(cryptoId) {
      const crypto = this.cryptos.find((c) => c.id === cryptoId);
      return crypto ? crypto.name : "Unknown Crypto";
    },
    // Format number
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
<style>
.alert-triggered {
  animation: blink 1s ease-in-out 5;
}

@keyframes blink {
  0% {
    background-color: rgba(255, 0, 0, 0.1);
  }
  50% {
    background-color: rgba(255, 0, 0, 0.3);
  }
  100% {
    background-color: rgba(255, 0, 0, 0.1);
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
