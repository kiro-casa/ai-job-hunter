import axios from 'axios';

const api = axios.create({
  // Use the environment variable for production, fallback to localhost for dev
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api', 
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, 
  withXSRFToken: true,   
});

export default api;