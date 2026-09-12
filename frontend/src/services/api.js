import axios from 'axios';

// Determine API base URL based on environment
const getBaseURL = () => {
  // In development, use localhost
  if (import.meta.env.DEV) {
    return 'http://localhost:8000/api';
  }
  // In production, use the current domain (same domain as frontend)
  return `${window.location.origin}/api`;
};

const api = axios.create({
  baseURL: getBaseURL(),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add token to requests if it exists
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;