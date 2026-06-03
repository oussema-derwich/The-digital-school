import axios from "axios";
import store from "@/store"; // Importer Vuex

const api = axios.create({
  baseURL: "http://localhost:8000/api",
  withCredentials: true, // Permettre l'utilisation des cookies
  headers: {
    Accept: "application/json",
  },
});

// Ajouter le token d'authentification à chaque requête
api.interceptors.request.use((config) => {
  const token = store.state.auth.token;

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Gérer les erreurs globales
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      console.error("Unauthorized - redirecting to login.");
      store.dispatch("user/logout"); // Déconnecter l'utilisateur
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

export default api;
