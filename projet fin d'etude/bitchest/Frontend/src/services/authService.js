import axios from 'axios';

// Function to get CSRF token before login
export const getCsrfToken = async () => {
  try {
    await axios.get('http://localhost:8000/api/sanctum/csrf-cookie', {
      withCredentials: true,
    });
  } catch (error) {
    console.error("Error fetching CSRF token:", error);
    throw error;
  }
};

// Function to login user
export const loginUser = async (email, password) => {
  try {
    await getCsrfToken(); // Ensure CSRF token is fetched
    const response = await axios.post(
      "http://localhost:8000/api/login",
      { email, password },
      { withCredentials: true }
    );

    console.log("Login Response:", response.data); // Debugging step

    return response.data;
  } catch (error) {
    console.error("Login failed:", error.response?.data?.message || error.message);
    throw error;  
  }
};
export default {
  getCsrfToken,
  loginUser,
};