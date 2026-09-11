import api from './api';

export const authService = {
  // Register a new user
  register: async (name, email, password, password_confirmation) => {
    const response = await api.post('/auth/register', {
      name,
      email,
      password,
      password_confirmation,
    });
    
    if (response.data.success) {
      localStorage.setItem('auth_token', response.data.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.data.user));
      api.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`;
    }
    
    return response.data;
  },

  // Login
  login: async (email, password) => {
    const response = await api.post('/auth/login', {
      email,
      password,
    });
    
    if (response.data.success) {
      localStorage.setItem('auth_token', response.data.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.data.user));
      api.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`;
    }
    
    return response.data;
  },

  // Logout
  logout: async () => {
    try {
      await api.post('/auth/logout');
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      delete api.defaults.headers.common['Authorization'];
    }
  },

  // Get current user
  getUser: async () => {
    const response = await api.get('/user');
    return response.data.data;
  },

  // Check if user is authenticated
  isAuthenticated: () => {
    return !!localStorage.getItem('auth_token');
  },

  // Get stored user
  getStoredUser: () => {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },

  // Get stored token
  getToken: () => {
    return localStorage.getItem('auth_token');
  },
};