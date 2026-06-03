<template>
  <div class="responsive-card bg-white p-4 rounded-lg shadow">
    <h3 class="text-lg text-bitchest-black font-medium mb-2">{{ title }}</h3>
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
  },
};
</script>

<style scoped>
.responsive-card {
  width: 100%;
  max-width: 100%; /* Assure que le contenu ne dépasse pas la largeur de l'écran */
  box-sizing: border-box; /* Inclut les marges et paddings dans le calcul de la largeur */
  overflow-x: auto; /* Active le scroll horizontal si nécessaire */
}

@media (max-width: 768px) {
  .responsive-card {
    padding: 2rem; /* Ajuste le padding sur les écrans plus petits */
  }
}

@media (max-width: 480px) {
  .responsive-card {
    padding: 1rem; /* Réduit encore le padding sur les petits écrans */
  }
}
</style>
