<template>
  <div class="min-h-screen bg-gradient-to-b from-white via-gray-50 to-white text-black">
    <!-- Hero Section -->
    <section class="hero-section relative flex flex-col justify-center items-center text-center py-32 px-6">
      <div class="max-w-4xl">
        <!-- Logo & Title -->
        <div class="mb-8 animate-fade-in">
          <img 
            src="/assets/bitchest_logo.png" 
            alt="BitChest" 
            class="h-28 mx-auto mb-8 hover:scale-105 transition-transform duration-300" 
          />
          <h1 class="text-5xl md:text-6xl font-heading font-bold text-gray-900 mb-6 leading-tight">
            Investissez dans le futur avec <span class="text-blue-600">BitChest</span>
          </h1>
          <p class="text-xl md:text-2xl text-gray-700 mb-10">
            La plateforme intuitive pour gérer vos investissements en cryptomonnaies.
          </p>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-20 animate-slide-up">
          <router-link to="/register" class="bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold px-8 py-3 rounded-md shadow-md transition">
            Commencer maintenant
          </router-link>
          <router-link to="/login" class="border border-blue-600 text-blue-600 hover:bg-blue-50 text-lg font-semibold px-8 py-3 rounded-md transition">
            Se connecter
          </router-link>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-3xl mx-auto animate-slide-up">
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">10+</div>
            <div class="text-gray-600">Cryptomonnaies disponibles</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">24/7</div>
            <div class="text-gray-600">Support client</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">100%</div>
            <div class="text-gray-600">Sécurisé et fiable</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Why BitChest Section -->
    <section class="py-20 bg-gradient-to-b from-white via-gray-50 to-white">
      <div class="container mx-auto px-6">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-heading font-bold text-gray-900 mb-4">
            Pourquoi choisir <span class="text-blue-600">BitChest</span> ?
          </h2>
          <p class="text-xl text-gray-700 max-w-2xl mx-auto">
            Une plateforme complète, sécurisée et adaptée à tous les investisseurs.
          </p>
        </div>

        <!-- Features grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="feature-card p-8 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition"
          >
            <div class="text-4xl mb-4">{{ feature.icon }}</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ feature.title }}</h3>
            <p class="text-gray-600">{{ feature.desc }}</p>
          </div>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center mt-20">
          <router-link to="/register" class="bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold px-8 py-3 rounded-md shadow-md transition">
            Créer un compte gratuitement
          </router-link>
          <p class="mt-4 text-gray-600">
            Rejoignez des milliers d’investisseurs qui font confiance à <strong>BitChest</strong>.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'HomePage',
  setup() {
    const features = [
      {
        icon: '🔒',
        title: 'Sécurité maximale',
        desc: 'Protection de niveau bancaire, authentification à deux facteurs et surveillance continue de votre compte.'
      },
      {
        icon: '⚡',
        title: 'Trading instantané',
        desc: 'Exécutez vos ordres en temps réel et profitez des meilleures opportunités du marché.'
      },
      {
        icon: '📊',
        title: 'Analyses détaillées',
        desc: 'Suivez l’évolution de votre portefeuille avec des graphiques et indicateurs avancés.'
      },
      {
        icon: '💬',
        title: 'Support réactif',
        desc: 'Notre équipe d’experts est disponible 24/7 pour répondre à toutes vos questions.'
      },
      {
        icon: '📚',
        title: 'Ressources éducatives',
        desc: 'Apprenez les bases et devenez expert grâce à nos guides et tutoriels détaillés.'
      },
      {
        icon: '📱',
        title: 'Interface mobile',
        desc: 'Accédez à votre portefeuille où que vous soyez, avec une interface fluide et responsive.'
      }
    ]
    return { features }
  }
})
</script>

<style scoped>
.hero-section {
  background: linear-gradient(to bottom, #ffffff, #f9fafb);
}

.feature-card:hover {
  transform: translateY(-4px);
}

.animate-fade-in {
  animation: fadeIn 1s ease-in-out;
}

.animate-slide-up {
  animation: slideUp 1s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
