// Base API Configuration
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

/**
 * Get authorization headers with Bearer token
 */
const getAuthHeaders = () => {
  const token = localStorage.getItem('token');
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  return headers;
};

/**
 * Handle API Response
 */
const handleResponse = async (response) => {
  const data = await response.json();

  if (!response.ok) {
    const error = data.message || 'An error occurred';
    throw new Error(error);
  }

  return data;
};

/**
 * Generic API Request Handler
 */
const apiRequest = async (endpoint, options = {}) => {
  const url = `${API_BASE_URL}${endpoint}`;
  const config = {
    ...options,
    headers: {
      ...getAuthHeaders(),
      ...options.headers,
    },
  };

  try {
    const response = await fetch(url, config);
    return await handleResponse(response);
  } catch (error) {
    console.error('API Request Error:', error);
    throw error;
  }
};

/**
 * Authentication APIs
 */
export const login = async (credentials) => {
  return await apiRequest('/login', {
    method: 'POST',
    body: JSON.stringify(credentials),
  });
};

export const register = async (userData) => {
  return await apiRequest('/register', {
    method: 'POST',
    body: JSON.stringify(userData),
  });
};

export const logout = async () => {
  return await apiRequest('/logout', {
    method: 'POST',
  });
};

/**
 * User APIs
 */
export const getUser = async () => {
  return await apiRequest('/user', {
    method: 'GET',
  });
};

export const updateUser = async (userData) => {
  return await apiRequest('/user', {
    method: 'PUT',
    body: JSON.stringify(userData),
  });
};

/**
 * Orders APIs
 */
export const fetchOrders = async (params = {}) => {
  const queryString = new URLSearchParams(params).toString();
  const endpoint = queryString ? `/orders?${queryString}` : '/orders';
  
  return await apiRequest(endpoint, {
    method: 'GET',
  });
};

export const getOrderById = async (orderId) => {
  return await apiRequest(`/orders/${orderId}`, {
    method: 'GET',
  });
};

export const createOrder = async (orderData) => {
  return await apiRequest('/orders', {
    method: 'POST',
    body: JSON.stringify(orderData),
  });
};

/**
 * Notifications APIs
 */
export const fetchNotifications = async (params = {}) => {
  const queryString = new URLSearchParams(params).toString();
  const endpoint = queryString ? `/notifications?${queryString}` : '/notifications';
  
  return await apiRequest(endpoint, {
    method: 'GET',
  });
};

export const markNotificationAsRead = async (notificationId) => {
  return await apiRequest(`/notifications/${notificationId}/read`, {
    method: 'PUT',
  });
};

export const markAllNotificationsAsRead = async () => {
  return await apiRequest('/notifications/read-all', {
    method: 'PUT',
  });
};

/**
 * QR Code / Scan APIs
 */
export const sendScanResult = async (scanData) => {
  return await apiRequest('/scan', {
    method: 'POST',
    body: JSON.stringify(scanData),
  });
};

export const verifyScan = async (code) => {
  return await apiRequest('/scan/verify', {
    method: 'POST',
    body: JSON.stringify({ code }),
  });
};

/**
 * Products APIs
 */
export const fetchProducts = async (params = {}) => {
  const queryString = new URLSearchParams(params).toString();
  const endpoint = queryString ? `/products?${queryString}` : '/products';
  
  return await apiRequest(endpoint, {
    method: 'GET',
  });
};

export const getProductById = async (productId) => {
  return await apiRequest(`/products/${productId}`, {
    method: 'GET',
  });
};

/**
 * Categories APIs
 */
export const fetchCategories = async () => {
  return await apiRequest('/categories', {
    method: 'GET',
  });
};

/**
 * Admin APIs
 */

// Get user by QR code
export const getUserByQR = async (code) => {
  // URL encode the base64 string to handle special characters
  const encodedCode = encodeURIComponent(code);
  return await apiRequest(`/admin/user-by-qr/${encodedCode}`, {
    method: 'GET',
  });
};

// Add points to user
export const addPoints = async (data) => {
  return await apiRequest('/admin/add-points', {
    method: 'POST',
    body: JSON.stringify(data),
  });
};

// Get all orders
export const getOrders = async (params = {}) => {
  const queryString = new URLSearchParams(params).toString();
  const endpoint = queryString ? `/admin/orders?${queryString}` : '/admin/orders';
  
  return await apiRequest(endpoint, {
    method: 'GET',
  });
};

// Update order status
export const updateOrderStatus = async (orderId, status) => {
  return await apiRequest(`/admin/orders/${orderId}/status`, {
    method: 'POST',
    body: JSON.stringify({ status }),
  });
};

// Get admin dashboard summary
export const getSummary = async () => {
  return await apiRequest('/admin/summary', {
    method: 'GET',
  });
};

// Get points history with search
export const getHistoryPoints = async (search = '') => {
  const params = search ? { search } : {};
  const queryString = new URLSearchParams(params).toString();
  const endpoint = queryString ? `/admin/history/points?${queryString}` : '/admin/history/points';
  
  return await apiRequest(endpoint, {
    method: 'GET',
  });
};

// Get orders history with search
export const getHistoryOrders = async (search = '') => {
  const params = search ? { search } : {};
  const queryString = new URLSearchParams(params).toString();
  const endpoint = queryString ? `/admin/history/orders?${queryString}` : '/admin/history/orders';
  
  return await apiRequest(endpoint, {
    method: 'GET',
  });
};

// Get login logs for a user
export const getLoginLogs = async (userId) => {
  return await apiRequest(`/users/${userId}/login-logs`, {
    method: 'GET',
  });
};

// Export API service
export default {
  login,
  register,
  logout,
  getUser,
  updateUser,
  fetchOrders,
  getOrderById,
  createOrder,
  fetchNotifications,
  markNotificationAsRead,
  markAllNotificationsAsRead,
  sendScanResult,
  verifyScan,
  fetchProducts,
  getProductById,
  fetchCategories,
  // Admin APIs
  getUserByQR,
  addPoints,
  getOrders,
  updateOrderStatus,
  getSummary,
  getHistoryPoints,
  getHistoryOrders,
  getLoginLogs,
};
