<template>
  <div v-if="loading" class="text-center">
    <Loader />
  </div>
  <section
    v-else
    id="home"
    class="container min-h-screen mx-auto flex flex-col-reverse md:flex-row justify-center p-4"
  >
    <div class="w-full md:w-1/2 text-center md:text-left">
      <h1 class="text-4xl lg:text-7xl font-bitchest font-bold mt-28 mb-12">
        The largest Crypto Marketplace
      </h1>
      <p class="text-xl w-3/4 mx-auto md:mx-0 mb-16">
        Buy, sell, and store hundreds of cryptocurrencies and protect your
        wallet in an easy way.
      </p>
      <p class="text-xl w-3/4 mx-auto md:mx-0 mb-3">
        Join us and sign up to explore more
      </p>

      <CustomInput
        v-model="email"
        placeholder="Enter your email"
        class="mr-8 CustomInput"
      />

      <CustomButton variant="primary" @click="submitEmail">
        Registration request
      </CustomButton>

      <!-- Modal pour afficher les messages -->
      <div
        v-if="showModal"
        class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex items-center justify-center"
      >
        <div class="bg-white p-6 rounded shadow-lg text-center">
          <p>{{ modalMessage }}</p>
          <button
            @click="handleModalClose"
            class="bg-blue-500 text-white px-4 py-2 rounded mt-4"
          >
            {{ modalRedirect ? "Go to Sign In" : "Close" }}
          </button>
        </div>
      </div>
    </div>

    <div class="w-full md:w-1/2 md:flex md:items-center md:mb-0">
      <img
        src="@/assets/homeImage.png"
        alt="Crypto Marketplace"
        class="w-full h-auto"
      />
    </div>
  </section>
</template>

<script>
import { useRouter } from "vue-router";
import CustomButton from "@/components/CustomButton.vue";
import CustomInput from "@/components/CustomInput.vue";
import registrationService from "@/services/registrationService.js";
import Loader from "@/components/utils/Loader.vue";

export default {
  name: "HomeSection",
  components: {
    CustomButton,
    CustomInput,
    Loader,
  },
  data() {
    return {
      email: "",
      showModal: false,
      modalMessage: "",
      modalRedirect: false,
      loading: false,
    };
  },
  methods: {
    setInputData(value) {
      this.email = value;
    },
    async submitEmail() {
      if (!this.email || !this.validateEmail(this.email)) {
        this.modalMessage = "Please enter a valid email.";
        this.showModal = true;
        return;
      }

      try {
        this.loading = true;
        const response = await registrationService.submitRegistrationRequest(
          this.email
        );
        this.loading = false;
        this.modalMessage = response.message;
        this.modalRedirect = response.status === "user_exists";
      } catch {
        this.modalMessage = "An error occurred. Please try again later.";
      }

      this.showModal = true;
      this.email = ""; // Réinitialisation de l'email après envoi
    },
    handleModalClose() {
      this.showModal = false;
      if (this.modalRedirect) {
        this.$router.push("/login");
      }
    },
    validateEmail(email) {
      const regEx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regEx.test(email);
    },
  },
};
</script>

<style scoped>
.CustomInput {
  width: 50%;
}
@media screen and (max-width: 768px) {
  .CustomInput {
    width: 100%;
  }
}
</style>
