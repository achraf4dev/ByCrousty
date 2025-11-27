/**
 * Cart Store - Composition API
 * Manages shopping cart state (localStorage for guests, API for authenticated users)
 */

import { ref, computed } from 'vue';
import api from '../services/api';
import cartService from '../services/cart';
import { useAuth } from './auth';

// Global state shared across all components
const cartItems = ref([]);
const loading = ref(false);
const error = ref(null);
const initialized = ref(false);

// Computed values
const itemCount = computed(() => {
  return cartItems.value.reduce((total, item) => total + item.quantity, 0);
});

const totalPrice = computed(() => {
  return cartItems.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const isEmpty = computed(() => {
  return cartItems.value.length === 0;
});

export const useCartStore = () => {
  const { isAuthenticated } = useAuth();

  /**
   * Load cart from localStorage or API depending on auth status
   */
  const loadCart = async () => {
    loading.value = true;
    error.value = null;

    try {
      if (isAuthenticated.value) {
        // Load from API for authenticated users
        const response = await api.getCart();
        cartItems.value = response.data.items || [];
      } else {
        // Load from localStorage for guests
        cartItems.value = cartService.getCart();
      }
    } catch (err) {
      error.value = 'Failed to load cart';
      console.error('Error loading cart:', err);
      // Fallback to localStorage on error
      cartItems.value = cartService.getCart();
    } finally {
      loading.value = false;
    }
  };

  /**
   * Add item to cart
   */
  const addItem = async (product, quantity = 1) => {
    loading.value = true;
    error.value = null;

    try {
      if (isAuthenticated.value) {
        // Add via API for authenticated users
        await api.addToCart(product.id, quantity);
        await loadCart(); // Reload cart from server
      } else {
        // Add to localStorage for guests
        const updatedCart = cartService.addItem(product, quantity);
        cartItems.value = updatedCart;
      }
      return { success: true };
    } catch (err) {
      error.value = 'Failed to add item to cart';
      console.error('Error adding to cart:', err);
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  };

  /**
   * Remove item from cart
   */
  const removeItem = async (itemId) => {
    loading.value = true;
    error.value = null;

    try {
      if (isAuthenticated.value) {
        // Remove via API for authenticated users
        await api.removeFromCart(itemId);
        await loadCart();
      } else {
        // Remove from localStorage for guests
        const updatedCart = cartService.removeItem(itemId);
        cartItems.value = updatedCart;
      }
      return { success: true };
    } catch (err) {
      error.value = 'Failed to remove item from cart';
      console.error('Error removing from cart:', err);
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  };

  /**
   * Update item quantity
   */
  const updateQuantity = async (itemId, quantity) => {
    loading.value = true;
    error.value = null;

    try {
      if (isAuthenticated.value) {
        // For authenticated users, remove and re-add with new quantity
        // Note: Your API might need an update endpoint
        if (quantity <= 0) {
          await removeItem(itemId);
        } else {
          await api.removeFromCart(itemId);
          await api.addToCart(itemId, quantity);
          await loadCart();
        }
      } else {
        // Update in localStorage for guests
        const updatedCart = cartService.updateQuantity(itemId, quantity);
        cartItems.value = updatedCart;
      }
      return { success: true };
    } catch (err) {
      error.value = 'Failed to update quantity';
      console.error('Error updating quantity:', err);
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  };

  /**
   * Clear entire cart
   */
  const clearCart = async () => {
    loading.value = true;
    error.value = null;

    try {
      if (isAuthenticated.value) {
        // Clear via API for authenticated users
        await api.clearCart();
      } else {
        // Clear localStorage for guests
        cartService.clearCart();
      }
      cartItems.value = [];
      return { success: true };
    } catch (err) {
      error.value = 'Failed to clear cart';
      console.error('Error clearing cart:', err);
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  };

  /**
   * Check if product is in cart
   */
  const isInCart = (productId) => {
    return cartItems.value.some(item => item.id === productId || item.product_id === productId);
  };

  /**
   * Get quantity of specific product
   */
  const getItemQuantity = (productId) => {
    const item = cartItems.value.find(item => item.id === productId || item.product_id === productId);
    return item ? item.quantity : 0;
  };

  // Initialize cart on first use (only once)
  if (!initialized.value) {
    initialized.value = true;
    loadCart();
  }

  return {
    cartItems,
    loading,
    error,
    itemCount,
    totalPrice,
    isEmpty,
    loadCart,
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    isInCart,
    getItemQuantity,
  };
};
