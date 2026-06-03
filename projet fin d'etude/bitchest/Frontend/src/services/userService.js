// services/userService.js

import api from "./api";
const apiUrl = "/admin/users";

export const getUsers = async (token) => {
  const response = await api.get("/admin/users", {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
};

export const createUser = async (formData, token) => {
  const response = await api.post("/admin/users", formData, {
    headers: { Authorization: `Bearer ${token}`, "Content-Type": "multipart/form-data" },
  });
  return response.data;
};

export const getUser = async (userId, token) => {
  const response = await api.get(`${apiUrl}/${userId}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
};

export const updateUser = async (id, formData, token) => {
  formData.append("_method", "PUT");
  const response = await api.post(`${apiUrl}/${id}`, formData, {
    headers: { Authorization: `Bearer ${token}`, "Content-Type": "multipart/form-data" },
  });
  return response.data;
};

export const deleteUser = async (userId, token) => {
  await api.delete(`${apiUrl}/${userId}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
};
