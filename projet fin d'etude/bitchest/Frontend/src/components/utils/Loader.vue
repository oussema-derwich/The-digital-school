<template>
  <div
    class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-70 z-50"
  >
    <img src="@/assets/logo.svg" alt="Loading" class="logo-pulse" />
  </div>
</template>

<style scoped>
/* Logo Animation */
.logo-pulse {
  width: 80px;
  height: 80px;
  animation: pulse 0.7s ease-in-out infinite;
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(3);
  }
  50% {
    transform: scale(3.1);
  }
}
</style>
