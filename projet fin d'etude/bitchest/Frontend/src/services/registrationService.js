import axios from "axios";

export default {
  async submitRegistrationRequest(email) {
    try {
      const response = await axios.post(
        "http://localhost:8000/api/registration-request",
        { email }, // Le corps de la requête doit contenir uniquement l'email
        {
          headers: {
            "Cache-Control": "no-cache", // Désactiver le cache
            "Content-Type": "application/json",
          },
        }
      );
      return response.data;
    } catch (error) {
      console.error("Error submitting registration request:", error);
      throw error.response?.data || { message: "An error occurred" };
    }
  },
};
