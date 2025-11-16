import { ref, computed } from 'vue';
import * as api from '../services/api';

// Admin state
const scannedUser = ref(null);
const summary = ref(null);
const orders = ref([]);
const historyPoints = ref([]);
const historyOrders = ref([]);
const loading = ref(false);
const error = ref(null);

/**
 * Use app store composable
 */
export function useAppStore() {
  // Actions
  
  /**
   * Fetch user by QR code
   */
  const fetchUserByQR = async (code) => {
    loading.value = true;
    error.value = null;
    
    try {
      const result = await api.getUserByQR(code);
      
      if (result.success) {
        scannedUser.value = result.data;
      } else {
        error.value = result.message || 'Failed to fetch user';
        scannedUser.value = null;
      }
      
      return result;
    } catch (err) {
      error.value = err.message || 'Error fetching user by QR code';
      scannedUser.value = null;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Clear scanned user
   */
  const clearScannedUser = () => {
    scannedUser.value = null;
  };

  /**
   * Add points to user
   */
  const addPointsToUser = async (userId, points, remark = null) => {
    loading.value = true;
    error.value = null;
    
    try {
      const result = await api.addPoints({
        user_id: userId,
        points,
        remark
      });
      
      if (result.success) {
        // Update scanned user if it's the same user
        if (scannedUser.value && scannedUser.value.id === userId) {
          scannedUser.value = result.data;
        }
      } else {
        error.value = result.message || 'Failed to add points';
      }
      
      return result;
    } catch (err) {
      error.value = err.message || 'Error adding points';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Fetch admin summary
   */
  const fetchSummary = async () => {
    loading.value = true;
    error.value = null;
    
    try {
      const result = await api.getSummary();
      
      if (result.success) {
        summary.value = result.data;
      } else {
        error.value = result.message || 'Failed to fetch summary';
      }
      
      return result;
    } catch (err) {
      error.value = err.message || 'Error fetching summary';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Fetch orders
   */
  const fetchOrders = async (params = {}) => {
    loading.value = true;
    error.value = null;
    
    try {
      const result = await api.getOrders(params);
      
      if (result.success) {
        orders.value = result.data.data || [];
      } else {
        error.value = result.message || 'Failed to fetch orders';
      }
      
      return result;
    } catch (err) {
      error.value = err.message || 'Error fetching orders';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Update order status
   */
  const updateOrderStatus = async (orderId, status) => {
    loading.value = true;
    error.value = null;
    
    try {
      const result = await api.updateOrderStatus(orderId, status);
      
      if (result.success) {
        // Update local orders array
        const index = orders.value.findIndex(order => order.id === orderId);
        if (index !== -1) {
          orders.value[index] = result.data;
        }
      } else {
        error.value = result.message || 'Failed to update order status';
      }
      
      return result;
    } catch (err) {
      error.value = err.message || 'Error updating order status';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Fetch points history
   */
  const fetchHistoryPoints = async (search = '') => {
    loading.value = true;
    error.value = null;
    
    try {
      const result = await api.getHistoryPoints(search);
      
      if (result.success) {
        historyPoints.value = result.data.data || [];
      } else {
        error.value = result.message || 'Failed to fetch points history';
      }
      
      return result;
    } catch (err) {
      error.value = err.message || 'Error fetching points history';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Fetch orders history
   */
  const fetchHistoryOrders = async (search = '') => {
    loading.value = true;
    error.value = null;
    
    try {
      const result = await api.getHistoryOrders(search);
      
      if (result.success) {
        historyOrders.value = result.data.data || [];
      } else {
        error.value = result.message || 'Failed to fetch orders history';
      }
      
      return result;
    } catch (err) {
      error.value = err.message || 'Error fetching orders history';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Getters
  const getScannedUser = computed(() => scannedUser.value);
  const getSummary = computed(() => summary.value);
  const getOrders = computed(() => orders.value);
  const getHistoryPoints = computed(() => historyPoints.value);
  const getHistoryOrders = computed(() => historyOrders.value);
  const isLoading = computed(() => loading.value);
  const getError = computed(() => error.value);

  return {
    // State
    scannedUser,
    summary,
    orders,
    historyPoints,
    historyOrders,
    loading,
    error,
    
    // Actions
    fetchUserByQR,
    clearScannedUser,
    addPointsToUser,
    fetchSummary,
    fetchOrders,
    updateOrderStatus,
    fetchHistoryPoints,
    fetchHistoryOrders,
    
    // Getters
    getScannedUser,
    getSummary,
    getOrders,
    getHistoryPoints,
    getHistoryOrders,
    isLoading,
    getError,
  };
}
