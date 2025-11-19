<script setup>
/**
 * Cart Page
 * Shopping cart with items and checkout
 */
import { computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../store/cart';
import { useAuth } from '../store/auth';
import { useUI } from '../store/ui';

const router = useRouter();
const cartStore = useCartStore();
const { isAuthenticated } = useAuth();
const { showSuccess, showError } = useUI();

// Use store values directly - they're already reactive refs/computeds
const { cartItems, totalPrice, itemCount, isEmpty } = cartStore;

const formatPrice = (price) => {
  return `€${Number(price).toFixed(2)}`;
};

const updateQuantity = async (item, newQuantity) => {
  const itemId = item.id || item.product_id;
  const result = await cartStore.updateQuantity(itemId, newQuantity);
  
  if (!result.success) {
    showError('Error al actualizar cantidad');
  }
};

const removeItem = async (item) => {
  const itemId = item.id || item.product_id;
  const result = await cartStore.removeItem(itemId);
  
  if (result.success) {
    showSuccess('Producto eliminado del carrito');
  } else {
    showError('Error al eliminar producto');
  }
};

const clearCart = async () => {
  if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
    const result = await cartStore.clearCart();
    
    if (result.success) {
      showSuccess('Carrito vaciado');
    } else {
      showError('Error al vaciar el carrito');
    }
  }
};

const proceedToCheckout = () => {
  if (!isAuthenticated.value) {
    if (confirm('Debes iniciar sesión para continuar. ¿Ir a login?')) {
      router.push('/login');
    }
  } else {
    // TODO: Implement checkout
    showSuccess('Función de pago próximamente');
  }
};

const continueShopping = () => {
  router.push('/home');
};

onMounted(() => {
  cartStore.loadCart();
});
</script>

<template>
  <div class="cart-page">
    <div class="page-header">
      <h1><i class="bi bi-cart"></i> Mi Carrito</h1>
      <p v-if="!isEmpty">{{ itemCount }} artículo(s) en tu carrito</p>
    </div>

    <!-- Empty Cart -->
    <div v-if="isEmpty" class="empty-cart text-center py-5">
      <i class="bi bi-cart-x" style="font-size: 5rem; color: var(--text-secondary);"></i>
      <h2 class="mt-4">Tu carrito está vacío</h2>
      <p class="text-secondary">Añade productos para comenzar tu pedido</p>
      <button class="btn btn-warning btn-lg mt-3" @click="continueShopping">
        <i class="bi bi-shop me-2"></i>Ir de Compras
      </button>
    </div>

    <!-- Cart Items -->
    <div v-else class="cart-content">
      <div class="cart-items">
        <div 
          v-for="item in cartItems" 
          :key="item.id || item.product_id"
          class="cart-item"
        >
          <!-- Item Image -->
          <div class="item-image">
            <img 
              :src="item.image_url || item.image || 'https://placehold.co/100/1a1a1a/FFD700?text=Product'"
              :alt="item.name"
            />
          </div>

          <!-- Item Info -->
          <div class="item-info">
            <h3 class="item-name">{{ item.name }}</h3>
            <p class="item-price">{{ formatPrice(item.price) }}</p>
            
            <!-- Quantity Controls -->
            <div class="quantity-controls">
              <button 
                class="btn btn-warning btn-sm"
                @click="updateQuantity(item, item.quantity - 1)"
                :disabled="item.quantity <= 1"
              >
                <i class="bi bi-dash"></i>
              </button>
              <span class="quantity mx-3">{{ item.quantity }}</span>
              <button 
                class="btn btn-warning btn-sm"
                @click="updateQuantity(item, item.quantity + 1)"
              >
                <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>

          <!-- Item Total and Remove -->
          <div class="item-actions">
            <p class="item-total">{{ formatPrice(item.price * item.quantity) }}</p>
            <button 
              class="btn btn-outline-danger btn-sm"
              @click="removeItem(item)"
              aria-label="Remove item"
            >
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Cart Summary -->
      <div class="cart-summary">
        <div class="summary-card">
          <h3>Resumen del Pedido</h3>
          
          <div class="summary-row">
            <span>Subtotal ({{ itemCount }} artículo{{ itemCount > 1 ? 's' : '' }})</span>
            <span>{{ formatPrice(totalPrice) }}</span>
          </div>

          <div class="summary-row">
            <span>Envío</span>
            <span>Por calcular</span>
          </div>

          <div class="summary-divider"></div>

          <div class="summary-row total">
            <span>Total</span>
            <span>{{ formatPrice(totalPrice) }}</span>
          </div>

          <button 
            class="btn btn-warning btn-lg w-100 fw-bold mb-3"
            @click="proceedToCheckout"
          >
            <i class="bi bi-credit-card me-2"></i>Proceder al Pago
          </button>

          <button 
            class="btn btn-outline-light btn-lg w-100 mb-3"
            @click="continueShopping"
          >
            <i class="bi bi-arrow-left me-2"></i>Seguir Comprando
          </button>

          <button 
            class="btn btn-outline-danger w-100"
            @click="clearCart"
          >
            <i class="bi bi-trash me-2"></i>Vaciar Carrito
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cart-page {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  text-align: center;
  margin-bottom: 3rem;
}

.page-header h1 {
  color: var(--primary-color);
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.page-header p {
  color: var(--text-secondary);
  font-size: 1.1rem;
}

.empty-cart {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-cart i {
  font-size: 5rem;
  color: var(--text-secondary);
  margin-bottom: 1.5rem;
}

.empty-cart h2 {
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.empty-cart p {
  color: var(--text-secondary);
  margin-bottom: 2rem;
}

.cart-content {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

.cart-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cart-item {
  display: grid;
  grid-template-columns: 100px 1fr auto;
  gap: 1rem;
  background: var(--bg-card);
  border-radius: 12px;
  padding: 1rem;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.cart-item:hover {
  border-color: var(--primary-color);
}

.item-image {
  width: 100px;
  height: 100px;
  border-radius: 8px;
  overflow: hidden;
  background: #1a1a1a;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-info {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.item-name {
  color: var(--text-primary);
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.item-price {
  color: var(--primary-color);
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.quantity-controls button {
  background: var(--primary-color);
  color: #000;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.quantity-controls button:hover:not(:disabled) {
  transform: scale(1.1);
}

.quantity-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity {
  font-weight: 600;
  color: var(--text-primary);
  min-width: 30px;
  text-align: center;
}

.item-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: space-between;
}

.item-total {
  color: var(--primary-color);
  font-weight: 700;
  font-size: 1.25rem;
}

.btn-remove {
  background: transparent;
  border: 1px solid var(--danger-color);
  color: var(--danger-color);
  width: 36px;
  height: 36px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.btn-remove:hover {
  background: var(--danger-color);
  color: white;
}

.cart-summary {
  position: sticky;
  top: 80px;
}

.summary-card {
  background: var(--bg-card);
  border-radius: 12px;
  padding: 2rem;
  border: 1px solid var(--border-color);
}

.summary-card h3 {
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  text-align: center;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  color: var(--text-secondary);
}

.summary-row.total {
  color: var(--text-primary);
  font-size: 1.5rem;
  font-weight: 700;
  margin-top: 1rem;
}

.summary-row.total span:last-child {
  color: var(--primary-color);
}

.summary-divider {
  height: 1px;
  background: var(--border-color);
  margin: 1rem 0;
}

.btn-block {
  width: 100%;
  margin-bottom: 0.75rem;
  padding: 0.875rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-danger {
  background: transparent;
  border: 1px solid var(--danger-color);
  color: var(--danger-color);
}

.btn-danger:hover {
  background: var(--danger-color);
  color: white;
}

/* Tablet and Desktop */
@media (min-width: 768px) {
  .cart-content {
    grid-template-columns: 2fr 1fr;
  }

  .cart-item {
    grid-template-columns: 120px 1fr auto;
  }

  .item-image {
    width: 120px;
    height: 120px;
  }
}
</style>
