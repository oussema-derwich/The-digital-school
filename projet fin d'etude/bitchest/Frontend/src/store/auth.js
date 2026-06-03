import axios from "axios";

// 🔹 URL de base de l'API
const API_BASE_URL = "http://localhost:8000/api";

// 🔹 Configuration d'Axios pour inclure le token d'authentification
axios.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default {
  namespaced: true,
  state: {
    user: null,
    token: localStorage.getItem("token") || null,
    isAuthenticated: !!localStorage.getItem("token"),
  },
  mutations: {
    SET_USER(state, { user, token, id }) {
      state.user = user;
      state.token = token;
      state.isAuthenticated = !!token;
      state.id = id;
      localStorage.setItem("token", token);
    },
    LOGOUT(state) {
      state.user = null;
      state.token = null;
      state.isAuthenticated = false;
      localStorage.removeItem("token");
    },
  },
  actions: {
    // 🔹 Connexion utilisateur
    async login({ commit }, { email, password }) {
      try {
        // Récupérer le cookie CSRF
        await axios.get("http://localhost:8000/sanctum/csrf-cookie");

        // Envoyer les informations de connexion
        const response = await axios.post(`${API_BASE_URL}/login`, {
          email,
          password,
        });

        if (response.data?.token && response.data?.user) {
          commit("SET_USER", {
            user: response.data.user,
            token: response.data.token,
          });
          console.log(this.state.auth.user); // ✅ Affichage correct de l'ID
          return response.data; // ✅ Succès
        } else {
          throw new Error("Réponse de connexion invalide");
        }
      } catch (error) {
        console.error(
          "Échec de connexion:",
          error.response?.data?.message || error.message
        );
        throw new Error(
          "Échec de connexion. Veuillez vérifier vos identifiants."
        );
      }
    },

    // 🔹 Récupérer le profil utilisateur
    async fetchProfile({ commit }) {
      try {
        const response = await axios.get(`${API_BASE_URL}/profile`);
        commit("SET_USER", {
          user: response.data.user,
          token: localStorage.getItem("token"),
        });
        console.log(response.data.user); // ✅ Affichage correct de l'ID
        return response.data;
      } catch (error) {
        console.error(
          "Erreur lors du chargement du profil:",
          error.response?.data?.message || error.message
        );
        throw new Error("Impossible de charger le profil utilisateur.");
      }
    },

    // 🔹 Mise à jour du profil utilisateur
    async updateProfile(_, formData) {
      try {
        if (!formData || typeof formData !== "object") {
          throw new Error("Données de formulaire invalides.");
        }

        const response = await axios.post(
          `${API_BASE_URL}/profile/update`,
          formData,
          {
            headers: { "Content-Type": "multipart/form-data" },
          }
        );

        if (process.env.NODE_ENV !== "production") {
          console.log("Profil mis à jour avec succès:", response.data);
        }
        return response.data;
      } catch (error) {
        console.error(
          "Erreur lors de la mise à jour du profil:",
          error.response?.data?.message || error.message
        );
        throw new Error("Échec de la mise à jour du profil.");
      }
    },

    // 🔹 Changer le mot de passe
    async changePassword(_, passwordData) {
      try {
        if (!passwordData?.current_password || !passwordData?.new_password) {
          throw new Error("Les champs du mot de passe sont requis.");
        }

        const response = await axios.post(
          `${API_BASE_URL}/profile/change-password`,
          {
            old_password: passwordData.current_password,
            new_password: passwordData.new_password,
          }
        );

        if (process.env.NODE_ENV !== "production") {
          console.log("Mot de passe changé avec succès:", response.data);
        }

        return response.data; // ✅ Retourne bien response.data
      } catch (error) {
        console.error(
          "Erreur lors du changement de mot de passe:",
          error.response?.data?.message || error.message
        );

        // ✅ Renvoie l'erreur complète au composant
        throw error.response
          ? error.response.data
          : new Error("Échec du changement de mot de passe.");
      }
    },

    // 🔹 Déconnexion de l'utilisateur
    async logout({ commit }) {
      try {
        await axios.post(`${API_BASE_URL}/logout`);
        commit("LOGOUT");
      } catch (error) {
        console.error(
          "Erreur lors de la déconnexion:",
          error.response?.data?.message || error.message
        );
        throw new Error("Échec de la déconnexion.");
      }
    },
  },
};
