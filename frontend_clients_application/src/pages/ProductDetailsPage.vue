<script setup>
/**
 * Product Details Page
 * Displays detailed information about a specific product
 */
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import { useCartStore } from '../store/cart';
import { useUI } from '../store/ui';

const route = useRoute();
const router = useRouter();
const cartStore = useCartStore();
const { showSuccess, showError } = useUI();

const product = ref(null);
const quantity = ref(1);
const loading = ref(false);
const error = ref(null);

const productImage = computed(() => {
  return product.value?.image_url || product.value?.image || 'https://placehold.co/600x400/1a1a1a/FFD700?text=No+Image';
});

const formattedPrice = computed(() => {
  return `€${Number(product.value?.price || 0).toFixed(2)}`;
});

const totalPrice = computed(() => {
  return `€${(Number(product.value?.price || 0) * quantity.value).toFixed(2)}`;
});

const isInCart = computed(() => {
  return product.value ? cartStore.isInCart(product.value.id) : false;
});

const loadProduct = async () => {
  loading.value = true;
  error.value = null;

  try {
    const productId = route.params.id;
    const response = await api.getProduct(productId);
    product.value = response.data.data || response.data;
  } catch (err) {
    error.value = 'Error al cargar el producto';
    console.error('Error loading product:', err);
  } finally {
    loading.value = false;
  }
};

const addToCart = async () => {
  if (!product.value) return;

  const result = await cartStore.addItem(product.value, quantity.value);
  
  if (result.success) {
    showSuccess(`${quantity.value} x ${product.value.name} añadido al carrito`);
  } else {
    showError('Error al añadir al carrito');
  }
};

const goBack = () => {
  router.back();
};

const incrementQuantity = () => {
  quantity.value++;
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

onMounted(() => {
  loadProduct();
});
</script>

<template>
  <div class="product-details-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Cargando producto...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error text-center py-5">
      <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
      <p class="text-secondary mt-3">{{ error }}</p>
      <button class="btn btn-warning me-2" @click="loadProduct">
        <i class="bi bi-arrow-clockwise me-2"></i>Reintentar
      </button>
      <button class="btn btn-outline-light" @click="goBack">
        <i class="bi bi-arrow-left me-2"></i>Volver
      </button>
    </div>

    <!-- Product Details -->
    <div v-else-if="product" class="product-details">
      <!-- Back Button -->
      <button class="btn btn-outline-light mb-4" @click="goBack">
        <i class="bi bi-arrow-left me-2"></i>Volver
      </button>

      <!-- Product Image -->
      <div class="product-image">
        <img :src="productImage" :alt="product.name" />
      </div>

      <!-- Product Info -->
      <div class="product-info">
        <h1 class="product-name">{{ product.name }}</h1>
        <p v-if="product.description" class="product-description">
          {{ product.description }}
        </p>

        <div class="product-price">{{ formattedPrice }}</div>

        <!-- Quantity Selector -->
        <div class="quantity-selector mb-4">
          <label class="form-label fw-bold">Cantidad:</label>
          <div class="d-flex align-items-center gap-3 bg-dark p-3 rounded">
            <button 
              class="btn btn-warning"
              @click="decrementQuantity" 
              :disabled="quantity <= 1"
            >
              <i class="bi bi-dash"></i>
            </button>
            <span class="quantity-value fs-4 fw-bold text-white mx-4">{{ quantity }}</span>
            <button 
              class="btn btn-warning"
              @click="incrementQuantity"
            >
              <i class="bi bi-plus"></i>
            </button>
          </div>
        </div>

        <!-- Total Price -->
        <div class="total-price">
          <span>Total:</span>
          <span class="price">{{ totalPrice }}</span>
        </div>

        <!-- Add to Cart Button -->
        <button 
          class="btn btn-lg w-100 fw-bold"
          :class="isInCart ? 'btn-success' : 'btn-warning'"
          @click="addToCart"
          :disabled="isInCart"
        >
          <i :class="isInCart ? 'bi bi-check-circle me-2' : 'bi bi-cart-plus me-2'"></i>
          {{ isInCart ? 'Ya en el Carrito' : 'Añadir al Carrito' }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.product-details-page {
  max-width: 1200px;
  margin: 0 auto;
}

.btn-back {
  margin-bottom: 2rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.product-details {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

.product-image {
  width: 100%;
  height: 400px;
  border-radius: 12px;
  overflow: hidden;
  background: var(--bg-card);
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding: 1rem;
}

.product-name {
  color: var(--text-primary);
  font-size: 2rem;
  margin-bottom: 1rem;
}

.product-description {
  color: var(--text-secondary);
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.product-price {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-bottom: 2rem;
}

.quantity-selector {
  margin-bottom: 1.5rem;
}

.quantity-selector label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--text-primary);
  font-weight: 500;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: var(--bg-card);
  padding: 0.5rem;
  border-radius: 8px;
  width: fit-content;
}

.quantity-controls button {
  background: var(--primary-color);
  color: #000;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  transition: all 0.3s ease;
}

.quantity-controls button:hover:not(:disabled) {
  transform: scale(1.1);
}

.quantity-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity-value {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  min-width: 60px;
  text-align: center;
}

.total-price {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: var(--bg-card);
  border-radius: 8px;
  margin-bottom: 2rem;
}

.total-price span:first-child {
  font-size: 1.25rem;
  color: var(--text-secondary);
}

.total-price .price {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--primary-color);
}

.btn-add-to-cart {
  width: 100%;
  padding: 1.25rem;
  font-size: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.btn-add-to-cart:disabled {
  background: var(--success-color);
  cursor: not-allowed;
}

.loading,
.error {
  text-align: center;
  padding: 3rem 1rem;
}

.error i {
  font-size: 3rem;
  color: var(--text-secondary);
  margin-bottom: 1rem;
}

.error button {
  margin: 0.5rem;
}

/* Tablet and Desktop */
@media (min-width: 768px) {
  .product-details {
    grid-template-columns: 1fr 1fr;
  }

  .product-image {
    height: 500px;
  }
}
</style>
