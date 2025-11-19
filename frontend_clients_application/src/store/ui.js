/**
 * UI Store - Composition API
 * Manages UI state (sidebar, modals, loading states, etc.)
 */

import { ref } from 'vue';

const sidebarOpen = ref(false);
const loading = ref(false);
const toast = ref({
  show: false,
  message: '',
  type: 'info', // 'info', 'success', 'error', 'warning'
  duration: 3000,
});

export const useUI = () => {
  /**
   * Toggle sidebar
   */
  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
  };

  /**
   * Open sidebar
   */
  const openSidebar = () => {
    sidebarOpen.value = true;
  };

  /**
   * Close sidebar
   */
  const closeSidebar = () => {
    sidebarOpen.value = false;
  };

  /**
   * Set loading state
   */
  const setLoading = (state) => {
    loading.value = state;
  };

  /**
   * Show toast notification
   */
  const showToast = (message, type = 'info', duration = 3000) => {
    toast.value = {
      show: true,
      message,
      type,
      duration,
    };

    // Auto-hide after duration
    setTimeout(() => {
      hideToast();
    }, duration);
  };

  /**
   * Hide toast notification
   */
  const hideToast = () => {
    toast.value.show = false;
  };

  /**
   * Show success message
   */
  const showSuccess = (message, duration = 3000) => {
    showToast(message, 'success', duration);
  };

  /**
   * Show error message
   */
  const showError = (message, duration = 3000) => {
    showToast(message, 'error', duration);
  };

  /**
   * Show warning message
   */
  const showWarning = (message, duration = 3000) => {
    showToast(message, 'warning', duration);
  };

  /**
   * Show info message
   */
  const showInfo = (message, duration = 3000) => {
    showToast(message, 'info', duration);
  };

  return {
    sidebarOpen,
    loading,
    toast,
    toggleSidebar,
    openSidebar,
    closeSidebar,
    setLoading,
    showToast,
    hideToast,
    showSuccess,
    showError,
    showWarning,
    showInfo,
  };
};
