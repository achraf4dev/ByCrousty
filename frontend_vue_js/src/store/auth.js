import { ref } from 'vue';
import * as api from '../services/api';

// Auth State - Load from localStorage on init
const token = ref(localStorage.getItem('token') || null);
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));

// Check if user is authenticated
export const isAuthenticated = () => {
  return !!token.value;
};

// Get Token
export const getToken = () => {
  return token.value;
};

// Set Token
export const setToken = (newToken) => {
  token.value = newToken;
  if (newToken) {
    localStorage.setItem('token', newToken);
  } else {
    localStorage.removeItem('token');
  }
};

// Get User
export const getUser = () => {
  return user.value;
};

// Set User
export const setUser = (userData) => {
  user.value = userData;
  if (userData) {
    localStorage.setItem('user', JSON.stringify(userData));
  } else {
    localStorage.removeItem('user');
  }
};

// Login
export const login = async (credentials) => {
  try {
    const data = await api.login(credentials);
    
    // Store token and user
    setToken(data.token);
    setUser(data.user);
    
    return { success: true, data };
  } catch (error) {
    console.error('Login error:', error);
    return { success: false, error: error.message };
  }
};

// Load User
export const loadUser = async () => {
  if (!token.value) {
    return { success: false, error: 'No token found' };
  }

  try {
    const data = await api.getUser();
    // The API returns the user directly, not wrapped in {user: ...}
    setUser(data);
    
    return { success: true, data };
  } catch (error) {
    console.error('Load user error:', error);
    logout();
    return { success: false, error: error.message };
  }
};

// Logout
export const logout = () => {
  setToken(null);
  setUser(null);
  localStorage.removeItem('token');
  localStorage.removeItem('user');
};

// Export auth store as composable
export const useAuth = () => {
  return {
    token,
    user,
    isAuthenticated,
    getToken,
    setToken,
    getUser,
    setUser,
    login,
    loadUser,
    logout,
  };
};
