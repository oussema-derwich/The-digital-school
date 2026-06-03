// store/modules/users.js
import * as userService from "@/services/userService";

export default {
  namespaced: true,
  state: {
    users: [],
  },
  mutations: {
    setUsers(state, users) {
      state.users = users;
    },
    addUser(state, user) {
      state.users.push(user);
    },
    updateUser(state, updatedUser) {
      const index = state.users.findIndex((user) => user.id === updatedUser.id || user.email === updatedUser.email);
      if (index !== -1) {
        state.users.splice(index, 1, updatedUser);
      }
    },
    removeUser(state, userId) {
      state.users = state.users.filter((user) => user.id !== userId);
    },
  },
  actions: {
    async fetchUsers({ commit }) {
      try {
        const token = localStorage.getItem("token");
        const users = await userService.getUsers(token);
        const usersWithPhotos = users.map((user) => ({
          ...user,
          photo: user.photo ? `http://localhost:8000/storage/${user.photo}` : null,
        }));
        commit("setUsers", usersWithPhotos);
      } catch (error) {
        console.error("Erreur lors du chargement des utilisateurs:", error);
      }
    },
    async addUser({ commit }, formData) {
      try {
        const token = localStorage.getItem("token");
        const tempUser = {
          id: Date.now(), 
          name: formData.get("name"),
          email: formData.get("email"),
          role: formData.get("role"),
          photo: formData.get("photo") ? URL.createObjectURL(formData.get("photo")) : null,
          isTemp: true, 
        };
        commit("addUser", tempUser);
        
        const user = await userService.createUser(formData, token);
        commit("updateUser", user);
      } catch (error) {
        console.error("Erreur lors de l'ajout de l'utilisateur:", error);
      }
    },
    async fetchUser(_, userId) {
      try {
        const token = localStorage.getItem("token");
        return await userService.getUser(userId, token);
      } catch (error) {
        console.error("Erreur lors de la récupération de l'utilisateur:", error);
        throw error;
      }
    },
    async updateUser({ commit }, { id, userData }) {
      try {
        const token = localStorage.getItem("token");
        const formData = new FormData();
        formData.append("name", userData.name);
        formData.append("email", userData.email);
        formData.append("role", userData.role);
        if (userData.photo) {
          formData.append("photo", userData.photo);
        }
        
        const updatedUser = await userService.updateUser(id, formData, token);
        commit("updateUser", updatedUser);
      } catch (error) {
        console.error("Erreur lors de la mise à jour de l'utilisateur:", error);
        throw error;
      }
    },
    async deleteUser({ commit }, userId) {
      try {
        const token = localStorage.getItem("token");
        await userService.deleteUser(userId, token);
        commit("removeUser", userId);
      } catch (error) {
        console.error("Erreur lors de la suppression de l'utilisateur:", error);
      }
    },
  },
  getters: {
    allUsers: (state) => state.users,
  },
};
