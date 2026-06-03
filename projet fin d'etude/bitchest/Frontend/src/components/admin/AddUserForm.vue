<template>
  <div class="max-w-lg mx-auto mt-9 p-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-semibold mb-8 text-center text-gray-800">Add User</h1>

    <form @submit.prevent="submitForm" class="space-y-6">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input
          v-model="name"
          id="name"
          type="text"
          required
          class="mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="Enter the user's name"
        />
        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          v-model="email"
          id="email"
          type="email"
          required
          class="mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="Enter the user's email"
        />
        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
      </div>

      <div>
        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
        <select
          v-model="role"
          id="role"
          class="mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="client">Client</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div>
        <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
        <input
          id="photo"
          type="file"
          @change="handlePhotoUpload"
          class="mt-1 w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        />
        <p v-if="errors.photo" class="text-red-500 text-sm mt-1">{{ errors.photo }}</p>
      </div>

      <div class="text-center">
        <button
          type="submit"
          class="w-full bg-bitchest-success text-black text-[1rem] font-bold px-4 py-2 rounded-lg shadow hover:bg-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
        >
          Add User
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import { useToast } from "vue-toastification"; // Importing toast notification library

export default {
  setup() {
    const toast = useToast(); // Using toast for success/failure messages
    return { toast };
  },
  data() {
    return {
      name: "",
      email: "",
      role: "client",
      photo: null,
      errors: {}, // Storing validation errors
    };
  },
  methods: {
    ...mapActions("users", ["addUser"]), // Action from Vuex store to add user
    handlePhotoUpload(event) {
      this.photo = event.target.files[0]; // Handling photo upload
    },
    async submitForm() {
      const formData = new FormData();
      formData.append("name", this.name); // Appending user data to FormData
      formData.append("email", this.email);
      formData.append("role", this.role);
      if (this.photo) {
        formData.append("photo", this.photo); // Adding photo if present
      }

      try {
        await this.addUser(formData); // Dispatching the action to add user
        this.toast.success("User added successfully! 🎉"); // Display success message
        this.$router.push({ name: "manage-users" }); // Navigating to 'manage-users' page
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors; // Handling validation errors
        } else {
          console.error("Error adding user:", error); // Logging error in case of failure
        }
      }
    },
  },
};
</script>
