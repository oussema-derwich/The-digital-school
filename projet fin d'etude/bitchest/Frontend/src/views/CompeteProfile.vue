<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-3xl w-full bg-white rounded-lg shadow-xl p-8">
      <!-- Barre de progression -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div
            class="flex-1 h-2 bg-gray-200 rounded-full"
            :class="{ 'bg-green-400': currentStep >= 1 }"
          ></div>
          <div class="mx-2"></div>
          <div
            class="flex-1 h-2 bg-gray-200 rounded-full"
            :class="{ 'bg-green-400': currentStep >= 2 }"
          ></div>
        </div>
        <div class="flex justify-between mt-2">
          <span class="text-sm font-medium text-gray-700">Change Password</span>
          <span class="text-sm font-medium text-gray-700"
            >Complete Profile</span
          >
        </div>
      </div>

      <!-- Titre -->
      <h1 class="text-3xl text-bitchest-black font-semibold mb-8 text-center">
        {{ currentStep === 1 ? "Change Password" : "Complete Profile" }}
      </h1>

      <!-- Chargement -->
      <div v-if="loading" class="text-center w-full">
        <Loader />
      </div>

      <!-- Étape 1 : Changement de mot de passe -->
      <form
        v-if="currentStep === 1"
        @submit.prevent="handleChangePassword"
        class="space-y-6"
      >
        <!-- Current Password -->
        <div>
          <label
            for="current_password"
            class="block text-sm font-medium text-gray-700"
          >
            Current Password
          </label>
          <input
            v-model="passwordData.current_password"
            type="password"
            id="current_password"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-bitchest-success focus:border-bitchest-success"
            required
          />
        </div>

        <!-- New Password -->
        <div>
          <label
            for="new_password"
            class="block text-sm font-medium text-gray-700"
          >
            New Password
          </label>
          <input
            v-model="passwordData.new_password"
            type="password"
            id="new_password"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-bitchest-success focus:border-bitchest-success"
            required
            @input="checkPasswordRequirements"
          />
          <div
            v-if="passwordData.new_password.length > 0"
            class="mt-2 text-sm text-gray-600"
          >
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

        <!-- Confirm New Password -->
        <div>
          <label
            for="confirm_password"
            class="block text-sm font-medium text-gray-700"
          >
            Confirm New Password
          </label>
          <input
            v-model="passwordData.confirm_password"
            type="password"
            id="confirm_password"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-bitchest-success focus:border-bitchest-success"
            required
          />
        </div>

        <!-- Bouton Next -->
        <button
          type="submit"
          class="w-full bg-bitchest-success text-black font-bold px-6 py-2 rounded-lg hover:bg-bitchest-success-dark transition duration-300"
        >
          Next
        </button>
      </form>

      <!-- Étape 2 : Complétion du profil -->
      <form
        v-if="currentStep === 2"
        @submit.prevent="handleCompleteProfile"
        class="space-y-6"
      >
        <!-- Champ Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">
            Name
          </label>
          <input
            v-model="profileData.name"
            id="name"
            type="text"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-bitchest-success focus:border-bitchest-success"
            required
          />
        </div>

        <!-- Champ Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Email
          </label>
          <input
            v-model="profileData.email"
            id="email"
            type="email"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-bitchest-success focus:border-bitchest-success"
            disabled
          />
        </div>

        <!-- Champ Photo -->
        <div>
          <label for="photo" class="block text-sm font-medium text-gray-700">
            Photo
          </label>
          <input
            id="photo"
            type="file"
            @change="handlePhoto"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-bitchest-success focus:border-bitchest-success"
          />
        </div>

        <!-- Bouton Submit -->
        <button
          type="submit"
          class="w-full bg-bitchest-success text-black font-bold px-6 py-2 rounded-lg hover:bg-bitchest-success-dark transition duration-300"
        >
          Complete Profile
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import Loader from "../components/utils/Loader.vue";
import { useToast } from "vue-toastification";

export default {
  components: {
    Loader,
  },
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      currentStep: 1, // 1 pour l'étape de changement de mot de passe, 2 pour la complétion du profil
      passwordData: {
        current_password: "",
        new_password: "",
        confirm_password: "",
      },
      profileData: {
        name: "",
        email: "",
        photo: null,
      },
      hasMinLength: false,
      hasUppercase: false,
      hasLowercase: false,
      hasNumber: false,
      hasSpecialChar: false,
      loading: false,
    };
  },
  async created() {
    try {
      const user = await this.$store.dispatch("auth/fetchProfile");
      if (user) {
        this.profileData = { ...user.user }; // Remplacer complètement l'objet user
      }
    } catch (error) {
      console.error("Error fetching profile:", error);
      this.toast.error("Failed to fetch profile data. ❌", {
        className:
          "bg-red-700 text-white font-bold px-4 py-3 rounded shadow-md",
      });
    }
  },
  methods: {
    // Vérification des exigences de complexité du mot de passe
    checkPasswordRequirements() {
      const password = this.passwordData.new_password;

      this.hasMinLength = password.length >= 8;
      this.hasUppercase = /[A-Z]/.test(password);
      this.hasLowercase = /[a-z]/.test(password);
      this.hasNumber = /\d/.test(password);
      this.hasSpecialChar = /[@$!%*?&]/.test(password);
    },

    // Gestion du changement de mot de passe
    async handleChangePassword() {
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

      if (
        !this.hasMinLength ||
        !this.hasUppercase ||
        !this.hasLowercase ||
        !this.hasNumber ||
        !this.hasSpecialChar
      ) {
        this.toast.error("Password does not meet complexity requirements ❌");
        return;
      }

      try {
        const response = await this.$store.dispatch(
          "auth/changePassword",
          this.passwordData
        );

        if (response && response.success) {
          this.toast.success(
            response.message || "Password changed successfully! 🔒"
          );
          this.currentStep = 2; // Passer à l'étape suivante
        } else {
          throw new Error("Unexpected response from server");
        }
      } catch (error) {
        console.error("Error changing password:", error);
        this.toast.error(error.message || "An unexpected error occurred.");
      }
    },

    // Gestion de la photo de profil
    handlePhoto(event) {
      this.profileData.photo = event.target.files[0];
    },

    // Gestion de la complétion du profil
    async handleCompleteProfile() {
      try {
        const formData = new FormData();
        formData.append("name", this.profileData.name);
        formData.append("email", this.profileData.email);
        if (this.profileData.photo instanceof File) {
          formData.append("photo", this.profileData.photo);
        }

        // Mettre à jour le profil et vérifier l'e-mail
        await this.$store.dispatch("auth/updateProfile", formData);
        this.toast.success("Profile completed successfully! 🎉");

        // Rediriger vers le tableau de bord
        this.$router.push("/dashboard");
      } catch (error) {
        this.toast.error("Error completing profile.");
      }
    },
  },
};
</script>
