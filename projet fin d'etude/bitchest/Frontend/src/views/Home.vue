<template>
  <div>
    <header>
      <NavBar />
    </header>
    <main>
      <HomeSection />
      <AboutSection />
      <TradeSection />
      <ServicesSection />
      <FooterSection />
    </main>
  </div>
</template>

<script>
//import Trade from "../components/sections/TradeSection.vue";
import NavBar from "@/components/NavBar.vue";
import HomeSection from "@/components/sections/HomeSection.vue";
import AboutSection from "../components/sections/AboutSection.vue";
import ServicesSection from "../components/sections/ServicesSection.vue";
import TradeSection from "../components/sections/TradeSection.vue";
import FooterSection from "@/components/sections/FooterSection.vue";

export default {
  components: {
    NavBar,
    HomeSection,
    AboutSection,
    TradeSection,
    ServicesSection,
    FooterSection,
  },
};
</script>

<style>
header {
  position: sticky;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
}
</style>
