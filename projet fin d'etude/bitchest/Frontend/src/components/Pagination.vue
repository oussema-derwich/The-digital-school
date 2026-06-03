<template>
  <div v-if="totalPages > 1" class="flex justify-center mt-4">
    <button
      v-if="currentPage > 1"
      @click="previousPage"
      class="text-blue-500 mx-2"
    >
      Previous
    </button>

    <button
      v-for="page in displayedPages"
      :key="page"
      :class="{
        'bg-blue-500 text-white': page === currentPage,
        'text-blue-500': page !== currentPage,
        'cursor-default': page === '...',
      }"
      @click="goToPage(page)"
      class="mx-1 px-3 py-1 border rounded-full"
      :disabled="page === '...'"
    >
      {{ page }}
    </button>

    <button
      v-if="currentPage < totalPages"
      @click="nextPage"
      class="text-blue-500 mx-2"
    >
      Next
    </button>
  </div>
</template>

<script>
export default {
  props: {
    currentPage: Number,
    totalPages: Number,
  },
  computed: {
    displayedPages() {
      const pages = [];
      const range = 2; // Nombre de pages affichées autour de la page actuelle

      if (this.totalPages <= 7) {
        // Si le total de pages est petit, affiche tout
        for (let i = 1; i <= this.totalPages; i++) pages.push(i);
      } else {
        // Toujours afficher la première page
        pages.push(1);
        if (this.currentPage > range + 2) pages.push("...");

        // Ajouter les pages autour de la page actuelle
        for (
          let i = this.currentPage - range;
          i <= this.currentPage + range;
          i++
        ) {
          if (i > 1 && i < this.totalPages) pages.push(i);
        }

        if (this.currentPage < this.totalPages - (range + 1)) pages.push("...");
        // Toujours afficher la dernière page
        pages.push(this.totalPages);
      }

      return pages;
    },
  },
  methods: {
    previousPage() {
      if (this.currentPage > 1) {
        this.$emit("goToPage", this.currentPage - 1);
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.$emit("goToPage", this.currentPage + 1);
      }
    },
    goToPage(page) {
      if (page !== "...") {
        this.$emit("goToPage", page);
      }
    },
  },
};
</script>
