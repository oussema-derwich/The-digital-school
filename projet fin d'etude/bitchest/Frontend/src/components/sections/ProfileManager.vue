<template>
  <div class="container mx-auto p-6">
    <h1 class="text-3xl text-bitchest-black font-semibold mb-8 text-center">
      Manage Profile
    </h1>
    <div v-if="loading" class="text-center w-full">
      <Loader />
    </div>
    <div v-else>
      <!-- SECTION: Update Profile -->
      <form @submit.prevent="updateProfile" v-if="user">
        <!-- Champ Name -->
        <div class="mb-4">
          <label for="name" class="block text-sm font-semibold text-gray-700"
            >Name</label
          >
          <input
            v-model="user.name"
            id="name"
            type="text"
            class="w-full px-4 py-2 border rounded-lg"
            :class="{ 'border-red-500': errors.name }"
            required
          />
          <p v-if="errors.name" class="text-red-500 text-sm mt-1">
            {{ errors.name[0] }}
          </p>
        </div>

        <!-- Champ Email (readonly) -->
        <div class="mb-4">
          <label for="email" class="block text-sm font-semibold text-gray-700"
            >Email</label
          >
          <input
            v-model="user.email"
            id="email"
            type="email"
            class="w-full px-4 py-2 border rounded-lg"
            disabled
          />
        </div>

        <!-- Champ Photo -->
        <div class="mb-4">
          <label for="photo" class="block text-sm font-semibold text-gray-700"
            >Photo</label
          >
          <input
            id="photo"
            type="file"
            @change="handlePhoto"
            class="cursor-pointer"
          />
          <p v-if="errors.photo" class="text-red-500 text-sm mt-1">
            {{ errors.photo[0] }}
          </p>
        </div>

        <!-- Bouton Update Profile -->
        <button
          type="submit"
          class="bg-bitchest-success text-black font-bold px-6 py-2 rounded-md hover:bg-gray-200"
        >
          Update Profile
        </button>
      </form>

      <!-- SECTION: Change Password -->
      <div class="mt-10 border-t pt-6">
        <h2 class="text-xl font-semibold mb-4">Change Password</h2>
        <form @submit.prevent="changePassword">
          <!-- Current Password -->
          <div class="mb-4">
            <label
              for="current_password"
              class="block text-sm font-semibold text-gray-700"
            >
              Current Password
            </label>
            <input
              v-model="passwordData.current_password"
              type="password"
              id="current_password"
              class="w-full px-4 py-2 border rounded-lg"
              required
            />
            <p v-if="errors.current_password" class="text-red-500 text-sm mt-1">
              {{ errors.current_password[0] }}
            </p>
          </div>

          <!-- New Password -->
          <div class="mb-4">
            <label
              for="new_password"
              class="block text-sm font-semibold text-gray-700"
            >
              New Password
            </label>
            <input
              v-model="passwordData.new_password"
              type="password"
              id="new_password"
              class="w-full px-4 py-2 border rounded-lg"
              required
              @input="checkPasswordRequirements"
            />
            <p v-if="errors.new_password" class="text-red-500 text-sm mt-1">
              {{ errors.new_password[0] }}
            </p>
            <div v-if="passwordData.new_password.length > 0">
              <!-- Liste des exigences de complexité -->
              <div class="mt-2 text-sm">
                <p :class="{ 'text-green-500': hasMinLength }">
                  - At least 8 characters
                </p>
                <p :class="{ 'text-green-500': hasUppercase }">
                  - At least one uppercase letter
                </p>
                <p :class="{ 'text-green-500': hasLowercase }">
                  - At least one lowercase letter
                </p>
                <p :class="{ 'text-green-500': hasNumber }">
                  - At least one number
                </p>
                <p :class="{ 'text-green-500': hasSpecialChar }">
                  - At least one special character (@$!%*?&)
                </p>
              </div>
            </div>
          </div>

          <!-- Confirm New Password -->
          <div class="mb-4">
            <label
              for="confirm_password"
              class="block text-sm font-semibold text-gray-700"
            >
              Confirm New Password
            </label>
            <input
              v-model="passwordData.confirm_password"
              type="password"
              id="confirm_password"
              class="w-full px-4 py-2 border rounded-lg"
              required
            />
          </div>

          <!-- Bouton Change Password -->
          <button
            type="submit"
            class="bg-red-600 text-white font-bold px-6 py-2 rounded-md hover:bg-red-700"
          >
            Change Password
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from "vue-toastification";
import Loader from "../utils/Loader.vue";

export default {
  name: "ManageProfile",
  components: {
    Loader,
  },
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      user: {
        name: "",
        email: "",
        photo: null,
      },
      passwordData: {
        current_password: "",
        new_password: "",
        confirm_password: "",
      },
      errors: {},
      hasMinLength: false,
      hasUppercase: false,
      hasLowercase: false,
      hasNumber: false,
      hasSpecialChar: false,
      loading: true,
    };
  },

  async created() {
    try {
      const user = await this.$store.dispatch("auth/fetchProfile");
      console.log(user);
      if (user) {
        this.user = { ...user.user }; // Remplacer complètement l'objet user
      }
    } catch (error) {
      console.error("Error fetching profile:", error);
      this.toast.error("Failed to fetch profile data. ❌", {
        className:
          "bg-red-700 text-white font-bold px-4 py-3 rounded shadow-md",
      });
    } finally {
      this.loading = false;
    }
  },
  methods: {
    handlePhoto(event) {
      this.user.photo = event.target.files[0];
    },
    async updateProfile() {
      this.errors = {};

      try {
        const formData = new FormData();
        formData.append("name", this.user.name);
        formData.append("email", this.user.email);

        if (this.user.photo instanceof File) {
          formData.append("photo", this.user.photo);
        }

        await this.$store.dispatch("auth/updateProfile", formData);

        this.toast.success("Profile updated successfully! 🎉");

        // Rafraîchir les données utilisateur
        await this.$store.dispatch("auth/fetchProfile");
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          console.error("Error updating profile:", error);
        }
      }
    },
    checkPasswordRequirements() {
      const password = this.passwordData.new_password;

      this.hasMinLength = password.length >= 8;
      this.hasUppercase = /[A-Z]/.test(password);
      this.hasLowercase = /[a-z]/.test(password);
      this.hasNumber = /\d/.test(password);
      this.hasSpecialChar = /[@$!%*?&]/.test(password);
    },
    async changePassword() {
      this.errors = {};

      // Vérification avant d'envoyer la requête
      if (
        !this.passwordData.current_password ||
        !this.passwordData.new_password ||
        !this.passwordData.confirm_password
      ) {
        this.toast.error("All fields are required ❌");
        return;
      }

      if (
        this.passwordData.new_password !== this.passwordData.confirm_password
      ) {
        this.toast.error("Passwords do not match ❌");
        return;
      }

      try {
        await this.$store.dispatch("auth/changePassword", {
          current_password: this.passwordData.current_password,
          new_password: this.passwordData.new_password,
        });

        this.toast.success("Password changed successfully! 🔒");

        // Réinitialiser les champs après modification
        this.passwordData = {
          current_password: "",
          new_password: "",
          confirm_password: "",
        };
      } catch (error) {
        console.error("Error changing password:", error);
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
          console.log(this.errors);
          this.toast.error(
            error.response.data.message || "Validation failed ❌"
          );
        } else {
          this.toast.error("An error occurred while changing the password ❌");
        }
      }
    },
  },
};
</script>
