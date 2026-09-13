import axios from 'axios';

// Determine API URL based on environment
let apiURL = import.meta.env.VITE_API_URL;

if (!apiURL) {
  // If on production domain, use the same domain
  if (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
    apiURL = `${window.location.protocol}//${window.location.hostname}/api`;
  } else {
    // For local development
    apiURL = 'http://localhost:8000/api';
  }
}

const api = axios.create({
  baseURL: apiURL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, 
  withXSRFToken: true,   
});

// Add token to every request
api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  
  if (import.meta.env.DEV) {
    console.log('Request:', config.method?.toUpperCase(), config.url);
  }
  
  return config;
}, error => {
  if (import.meta.env.DEV) {
    console.error('Request error:', error);
  }
  return Promise.reject(error);
});

// Log responses in development
api.interceptors.response.use(
  response => {
    if (import.meta.env.DEV) {
      console.log('Response OK:', response.status, response.config.url);
    }
    return response;
  },
  error => {
    if (import.meta.env.DEV) {
      console.error('Response error:', error.response?.status, error.config?.url);
    }
    return Promise.reject(error);
  }
);

export default api;