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

console.log('API URL detected:', apiURL);

const api = axios.create({
  baseURL: apiURL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, 
  withXSRFToken: true,   
});

// Log all requests
api.interceptors.request.use(config => {
  console.log('Request:', config.method?.toUpperCase(), config.url);
  return config;
}, error => {
  console.error('Request error:', error);
  return Promise.reject(error);
});

// Log all responses
api.interceptors.response.use(
  response => {
    console.log('Response OK:', response.status, response.config.url);
    return response;
  },
  error => {
    console.error('Response error:', error.response?.status, error.config?.url, error.response?.data);
    return Promise.reject(error);
  }
);

export default api;