import "./tailwind.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import CustomButton from "./components/CustomButton.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
  faArrowUp,
  faArrowDown,
  faArrowAltCircleUp,
  faArrowAltCircleDown,
  faToggleOn,
  faToggleOff,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

// Add FontAwesome icons to the library
library.add(
  faArrowUp,
  faArrowDown,
  faArrowAltCircleUp,
  faArrowAltCircleDown,
  faToggleOff,
  faToggleOn
);

// Create the Vue app instance
const app = createApp(App);

// Register global components
app.component("font-awesome-icon", FontAwesomeIcon); // Register FontAwesomeIcon globally
app.component("custom-button", CustomButton); // Register CustomButton globally

// Use plugins
app.use(store);
app.use(router);
app.use(Toast, { position: "bottom-right", timeout: 3000 });

// Mount the app
app.mount("#app");
