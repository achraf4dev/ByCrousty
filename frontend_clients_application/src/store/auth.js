/**
 * Auth Store - Composition API
 * Manages user authentication state
 */

import { ref, computed } from 'vue';
import api from '../services/api';
import cartService from '../services/cart';
import { useCartStore } from './cart';

const user = ref(null);
const token = ref(null);
const loading = ref(false);
const error = ref(null);

// Initialize from localStorage
const initAuth = () => {
  const savedToken = localStorage.getItem('auth_token');
  const savedUser = localStorage.getItem('user');
  
  if (savedToken && savedUser) {
    token.value = savedToken;
    try {
      user.value = JSON.parse(savedUser);
    } catch (e) {
      console.error('Error parsing saved user:', e);
      clearAuth();
    }
  }
};

// Clear auth data
const clearAuth = () => {
  user.value = null;
  token.value = null;
  localStorage.removeItem('auth_token');
  localStorage.removeItem('user');
};

// Save auth data
const saveAuth = (userData, authToken) => {
  user.value = userData;
  token.value = authToken;
  localStorage.setItem('auth_token', authToken);
  localStorage.setItem('user', JSON.stringify(userData));
};

export const useAuth = () => {
  // Initialize on first use
  if (!user.value && !token.value) {
    initAuth();
  }

  const isAuthenticated = computed(() => !!token.value);

  /**
   * Register new user
   */
  const register = async (userData) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await api.register(userData);
      
      if (response.data.token && response.data.user) {
        saveAuth(response.data.user, response.data.token);
        return { success: true, user: response.data.user };
      }
      
      throw new Error('Invalid response from server');
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed';
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  };

  /**
   * Login user
   */
  const login = async (credentials) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await api.login(credentials);
      
      if (response.data.token && response.data.user) {
        saveAuth(response.data.user, response.data.token);
        
        // Sync cart from localStorage to database
        await syncCartAfterLogin();
        
        return { success: true, user: response.data.user };
      }
      
      throw new Error('Invalid response from server');
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed';
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  };

  /**
   * Sync localStorage cart to database after login
   */
  const syncCartAfterLogin = async () => {
    const localCart = cartService.exportForSync();
    
    if (localCart.length > 0) {
      try {
        await api.syncCart(localCart);
        // Clear localStorage cart after successful sync
        cartService.clearCart();
        
        // Reload cart from server
        const cartStore = useCartStore();
        await cartStore.loadCart();
      } catch (err) {
        console.error('Error syncing cart:', err);
      }
    }
  };

  /**
   * Logout user
   */
  const logout = async () => {
    loading.value = true;

    try {
      await api.logout();
    } catch (err) {
      console.error('Logout error:', err);
    } finally {
      clearAuth();
      
      // Clear cart store
      const cartStore = useCartStore();
      cartStore.clearCart();
      
      loading.value = false;
    }
  };

  /**
   * Refresh user profile
   */
  const refreshProfile = async () => {
    if (!isAuthenticated.value) return;

    try {
      const response = await api.getProfile();
      if (response.data.user) {
        user.value = response.data.user;
        localStorage.setItem('user', JSON.stringify(response.data.user));
      }
    } catch (err) {
      console.error('Error refreshing profile:', err);
      if (err.response?.status === 401) {
        clearAuth();
      }
    }
  };

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    register,
    login,
    logout,
    refreshProfile,
  };
};
