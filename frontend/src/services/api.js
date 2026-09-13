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

export default api;