/**
 * API Service for ByCrousty Client Application
 * Handles all HTTP requests to Laravel backend
 */

import axios from 'axios';

// Base API URL - Update this to match your Laravel API
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1';

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 10000, // 10 seconds
});

// Request interceptor - Add auth token to requests
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - Handle errors globally
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      // Server responded with error status
      if (error.response.status === 401) {
        // Unauthorized - Clear auth and redirect to login
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        window.location.href = '/login';
      }
    } else if (error.request) {
      // Request made but no response received
      console.error('No response from server:', error.request);
    } else {
      // Error setting up the request
      console.error('Request error:', error.message);
    }
    return Promise.reject(error);
  }
);

/**
 * API Service Object
 */
const api = {
  // ==================== AUTH ====================
  
  /**
   * Register a new client user
   */
  register(userData) {
    return apiClient.post('/register', userData);
  },

  /**
   * Login client user
   */
  login(credentials) {
    return apiClient.post('/login', credentials);
  },

  /**
   * Get current authenticated user profile
   */
  getProfile() {
    return apiClient.get('/profile');
  },

  /**
   * Logout user
   */
  logout() {
    return apiClient.post('/logout');
  },

  // ==================== CATEGORIES ====================
  
  /**
   * Get all active categories
   */
  getCategories() {
    return apiClient.get('/categories/active');
  },

  /**
   * Get all active categories (alias)
   */
  getActiveCategories() {
    return apiClient.get('/categories/active');
  },

  /**
   * Get single category by ID
   */
  getCategory(id) {
    return apiClient.get(`/categories/${id}`);
  },

  // ==================== PRODUCTS ====================
  
  /**
   * Get all active products
   */
  getProducts() {
    return apiClient.get('/products/active');
  },

  /**
   * Get all active products (alias)
   */
  getActiveProducts() {
    return apiClient.get('/products/active');
  },

  /**
   * Get products by category
   */
  getProductsByCategory(categoryId) {
    return apiClient.get(`/categories/${categoryId}/products`);
  },

  /**
   * Get single product by ID
   */
  getProduct(id) {
    return apiClient.get(`/products/${id}`);
  },

  // ==================== CART ====================
  
  /**
   * Get user's cart (authenticated users only)
   */
  getCart() {
    return apiClient.get('/cart');
  },

  /**
   * Add item to cart (authenticated users only)
   */
  addToCart(productId, quantity = 1) {
    return apiClient.post('/cart/add', {
      product_id: productId,
      quantity: quantity,
    });
  },

  /**
   * Remove item from cart (authenticated users only)
   */
  removeFromCart(cartItemId) {
    return apiClient.delete(`/cart/remove/${cartItemId}`);
  },

  /**
   * Clear entire cart (authenticated users only)
   */
  clearCart() {
    return apiClient.post('/cart/clear');
  },

  /**
   * Sync localStorage cart to database after login
   */
  syncCart(cartItems) {
    return apiClient.post('/cart/sync', {
      items: cartItems,
    });
  },

  // ==================== STATIC CONTENT ====================
  
  /**
   * Get About Us content
   */
  getAbout() {
    return apiClient.get('/static/about');
  },

  /**
   * Get Contact information
   */
  getContact() {
    return apiClient.get('/static/contact');
  },

  /**
   * Get Find Us (location) information
   */
  getFindUs() {
    return apiClient.get('/static/find-us');
  },
};

export default api;
