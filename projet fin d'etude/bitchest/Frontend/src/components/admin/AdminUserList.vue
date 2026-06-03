<template>
  <div
    v-if="showModal"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
  >
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <h2 class="text-lg font-bold mb-4">Confirm Deletion</h2>
      <p>Are you sure you want to delete this user?</p>
      <div class="flex justify-end mt-4">
        <button
          @click="showModal = false"
          class="px-4 py-2 bg-gray-300 rounded-md mr-2"
        >
          Cancel
        </button>
        <button
          @click="handleDeleteUser"
          class="px-4 py-2 bg-red-500 text-white rounded-md"
        >
          Delete
        </button>
      </div>
    </div>
  </div>

  <div v-else class="container mx-auto">
    <h1
      class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-4 sm:mb-6"
    >
      User Management
    </h1>

    <div class="flex justify-center sm:justify-end mb-4">
      <router-link
        to="admin/users/add"
        class="bg-bitchest-success text-black text-sm sm:text-base font-semibold px-4 sm:px-6 py-2 sm:py-3 rounded-md hover:bg-green-300 transition duration-200 shadow-md w-full sm:w-auto text-center"
      >
        Add User
      </router-link>
    </div>

    <!-- Tableau classique avec pagination (desktop) -->
    <div class="overflow-hidden hidden lg:block">
      <table
        class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md"
      >
        <thead class="bg-gray-100 text-gray-700 text-sm font-semibold">
          <tr>
            <th class="px-4 py-2">Photo</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Role</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="isLoading">
            <td colspan="5" class="px-4 py-2 text-center">
              <Loader />
            </td>
          </tr>

          <tr
            v-for="user in paginatedUsers"
            :key="user.id"
            class="border-b border-gray-200"
          >
            <td class="px-4 py-2 text-sm text-gray-700 text-center">
              <img
                v-if="user.photo"
                :src="user.photo"
                class="h-12 w-12 rounded-full object-cover"
              />
              <img
                v-else
                src="/images/unknown.png"
                alt="User Photo"
                class="h-12 w-12 rounded-full object-cover"
              />
            </td>
            <td class="px-4 py-2 text-sm text-gray-700 text-center">
              {{ user.name }}
            </td>
            <td class="px-4 py-2 text-sm text-gray-700 text-center">
              {{ user.email }}
            </td>
            <td class="px-4 py-2 text-sm text-gray-700 text-center">
              {{ user.role }}
            </td>
            <td class="px-4 py-2 text-sm text-gray-700 text-center">
              <button
                @click="editUser(user.id)"
                class="text-yellow-500 hover:text-yellow-600 transition duration-150 mr-2"
              >
                <i class="fas fa-edit text-bitchest-success"></i>
              </button>
              <button
                @click="openModal(user.id)"
                class="text-red-500 hover:text-red-700 transition duration-150"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div
      v-if="showModal"
      class="fixed inset-x-0 top-0 flex justify-center items-start p-4"
    >
      <div class="bg-gray-50 rounded-lg p-4 w-1/3 shadow-md mt-12">
        <h2 class="text-lg font-semibold">Confirm Deletion</h2>
        <p>Are you sure you want to delete this user?</p>
        <div class="mt-4 flex justify-between">
          <button
            @click="cancelDelete"
            class="bg-gray-300 px-4 py-2 rounded-md"
          >
            Cancel
          </button>
          <button
            @click="handleDeleteUser"
            class="bg-red-500 text-white px-4 py-2 rounded-md"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Liste mobile -->
    <div class="lg:hidden">
      <div
        v-for="user in paginatedUsers"
        :key="user.id"
        class="bg-white p-4 mb-4 rounded-lg shadow-md border"
      >
        <div class="flex items-center justify-normal gap-3 space-x-4">
          <img
            v-if="user.photo"
            :src="user.photo"
            class="h-12 w-12 rounded-full object-cover"
          />
          <img
            v-else
            src="/images/unknown.png"
            alt="User Photo"
            class="h-12 w-12 rounded-full object-cover"
          />
          <div class="flex flex-col gap-2">
            <p
              class="text-sm font-bold break-words whitespace-normal break-all w-full"
            >
              {{ user.name }}
            </p>
            <p
              class="text-xs text-gray-500 break-words whitespace-normal break-all w-full"
            >
              {{ user.email }}
            </p>
          </div>
        </div>
        <div class="flex justify-end mt-2 space-x-3">
          <button
            @click="editUser(user.id)"
            class="text-bitchest-success hover:text-yellow-600 text-sm"
          >
            <i class="fas fa-edit"></i>
          </button>
          <button
            @click="openModal(user.id)"
            class="text-red-500 hover:text-red-700 text-sm"
          >
            <i class="fas fa-trash-alt"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination Desktop + Mobile -->
    <Pagination
      v-if="totalPages > 1"
      :currentPage="currentPage"
      :totalPages="totalPages"
      @goToPage="goToPage"
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { useToast } from "vue-toastification";
import Loader from "../utils/Loader.vue";
import Pagination from "../../components/Pagination.vue"; // Import du composant pagination

export default {
  name: "AdminUserList",
  components: {
    Loader,
    Pagination, // Déclaration du composant
  },
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      showModal: false,
      userIdToDelete: null,
      isLoading: true,
      limit: 6,
      currentPage: 1,
      totalPages: 0,
    };
  },
  computed: {
    ...mapGetters("users", ["allUsers"]),
    users() {
      return this.allUsers;
    },
    paginatedUsers() {
      const startIndex = (this.currentPage - 1) * this.limit;
      const endIndex = startIndex + this.limit;
      return this.users.slice(startIndex, endIndex);
    },
  },
  async created() {
    try {
      await this.fetchUsers();
      this.totalPages = Math.ceil(this.users.length / this.limit);
    } catch (error) {
      console.error("Erreur lors de la récupération des utilisateurs :", error);
      this.toast.error("Failed to fetch users List. ❌");
    } finally {
      this.isLoading = false;
    }
  },
  methods: {
    ...mapActions("users", ["fetchUsers", "deleteUser"]),
    editUser(userId) {
      console.log("Navigating to EditUser with ID:", userId);
      this.$router.push({ name: "edit-user", params: { id: userId } });
    },
    openModal(userId) {
      console.log("User ID to delete:", userId);
      this.userIdToDelete = userId;
      this.showModal = true;
    },
    cancelDelete() {
      this.showModal = false;
      this.userIdToDelete = null;
    },
    async handleDeleteUser() {
      try {
        await this.deleteUser(this.userIdToDelete);
        this.toast.success("User deleted successfully! ✅");
        this.fetchUsers(); // Rafraîchir la liste après la suppression
        this.showModal = false;
      } catch (error) {
        this.toast.error("Error deleting user. ❌");
        this.showModal = false;
      }
    },
    goToPage(page) {
      this.currentPage = page;
    },
  },
};
</script>
