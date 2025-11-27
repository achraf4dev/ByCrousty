/**
 * Cart Service for ByCrousty Client Application
 * Manages shopping cart in localStorage for guest users
 */

const CART_STORAGE_KEY = 'bycrousty_cart';

/**
 * Cart Service Object
 */
const cartService = {
  /**
   * Get all items from localStorage cart
   * @returns {Array} Array of cart items
   */
  getCart() {
    try {
      const cart = localStorage.getItem(CART_STORAGE_KEY);
      const items = cart ? JSON.parse(cart) : [];
      
      // Migrate old items that might have relative image paths
      return items.map(item => {
        // If item has image but no image_url, or image is a relative path
        if (item.image && !item.image.startsWith('http')) {
          // If it starts with 'products/', prepend the full URL
          if (item.image.startsWith('products/')) {
            item.image_url = `https://bycrousty.achraf.es/storage/${item.image}`;
            item.image = item.image_url;
          }
        }
        // Ensure image_url is set
        if (!item.image_url && item.image) {
          item.image_url = item.image;
        }
        return item;
      });
    } catch (error) {
      console.error('Error reading cart from localStorage:', error);
      return [];
    }
  },

  /**
   * Save cart to localStorage
   * @param {Array} cart - Array of cart items
   */
  saveCart(cart) {
    try {
      localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    } catch (error) {
      console.error('Error saving cart to localStorage:', error);
    }
  },

  /**
   * Add item to cart or update quantity if exists
   * @param {Object} product - Product object
   * @param {Number} quantity - Quantity to add
   * @returns {Array} Updated cart
   */
  addItem(product, quantity = 1) {
    const cart = this.getCart();
    
    // Check if item already exists
    const existingItemIndex = cart.findIndex(item => item.id === product.id);
    
    if (existingItemIndex > -1) {
      // Update quantity of existing item
      cart[existingItemIndex].quantity += quantity;
    } else {
      // Determine the correct image URL
      const imageUrl = product.image_url || product.image || 'https://placehold.co/100';
      
      // Add new item to cart
      const newItem = {
        id: product.id,
        product_id: product.id,
        name: product.name,
        price: product.price,
        image: imageUrl,
        image_url: imageUrl,
        quantity: quantity,
        category_id: product.category_id,
      };
      
      cart.push(newItem);
    }
    
    this.saveCart(cart);
    return cart;
  },

  /**
   * Remove item from cart by product ID
   * @param {Number} productId - Product ID to remove
   * @returns {Array} Updated cart
   */
  removeItem(productId) {
    let cart = this.getCart();
    cart = cart.filter(item => item.id !== productId);
    this.saveCart(cart);
    return cart;
  },

  /**
   * Update item quantity in cart
   * @param {Number} productId - Product ID
   * @param {Number} quantity - New quantity
   * @returns {Array} Updated cart
   */
  updateQuantity(productId, quantity) {
    const cart = this.getCart();
    const itemIndex = cart.findIndex(item => item.id === productId);
    
    if (itemIndex > -1) {
      if (quantity <= 0) {
        // Remove item if quantity is 0 or less
        cart.splice(itemIndex, 1);
      } else {
        cart[itemIndex].quantity = quantity;
      }
    }
    
    this.saveCart(cart);
    return cart;
  },

  /**
   * Clear entire cart
   */
  clearCart() {
    localStorage.removeItem(CART_STORAGE_KEY);
  },

  /**
   * Get total number of items in cart
   * @returns {Number} Total quantity
   */
  getItemCount() {
    const cart = this.getCart();
    return cart.reduce((total, item) => total + item.quantity, 0);
  },

  /**
   * Get total price of all items in cart
   * @returns {Number} Total price
   */
  getTotalPrice() {
    const cart = this.getCart();
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
  },

  /**
   * Check if a product is in cart
   * @param {Number} productId - Product ID
   * @returns {Boolean} True if product is in cart
   */
  isInCart(productId) {
    const cart = this.getCart();
    return cart.some(item => item.id === productId);
  },

  /**
   * Get quantity of specific product in cart
   * @param {Number} productId - Product ID
   * @returns {Number} Quantity or 0 if not in cart
   */
  getItemQuantity(productId) {
    const cart = this.getCart();
    const item = cart.find(item => item.id === productId);
    return item ? item.quantity : 0;
  },

  /**
   * Export cart in format suitable for API sync
   * @returns {Array} Array of items with product_id and quantity
   */
  exportForSync() {
    const cart = this.getCart();
    return cart.map(item => ({
      product_id: item.id,
      quantity: item.quantity,
    }));
  },
};

export default cartService;
