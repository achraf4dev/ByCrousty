<template>
  <ProductAddedModal
    :visible="modalVisible"
    :product="modalProduct"
    :quantity="modalQuantity"
    @continue="handleContinueShopping"
    @goToCart="handleGoToCart"
  />
  <div class="menu-page">
    <section class="categories-section bg-light-section">
      <!-- Title removed as requested -->

      <div v-if="loading" class="loading-state">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3 text-secondary">Cargando categorías...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <i class="bi bi-exclamation-circle text-danger"></i>
        <p class="mt-3 text-secondary">{{ error }}</p>
      </div>

      <div v-else-if="categories.length > 0" class="categories-grid">
        <div v-for="category in categories" :key="category.id" class="category-card">
          <div class="category-header">
            <h3 class="category-title">{{ category.name }}</h3>
            <p class="category-description">{{ category.description }}</p>
          </div>
          <div v-if="category.products && category.products.length > 0" class="products-grid">
            <div v-for="product in category.products" :key="product.id" class="product-card-wrapper">
              <ProductCard :product="product" :hideAddToCart="true" />
              <div class="quantity-selector">
                <button @click="decrement(product)" class="qty-btn">-</button>
                <span class="qty-value">{{ quantities[product.id] || 1 }}</span>
                <button @click="increment(product)" class="qty-btn">+</button>
              </div>
              <div class="add-to-cart-row">
                    <div class="add-to-cart-row-inner">
                      <button class="btn btn-warning add-to-cart-btn" @click="addToCart(product)">
                        <i class="bi bi-cart-plus"></i>
                      </button>
                      <span class="add-to-cart-text" @click="addToCart(product)" style="cursor:pointer;">Agregar</span>
                    </div>
              </div>
            </div>
          </div>
          <div v-else class="empty-state">
            <i class="bi bi-inbox text-secondary"></i>
            <p class="mt-3 text-secondary">No hay productos en esta categoría</p>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <i class="bi bi-inbox text-secondary"></i>
        <p class="mt-3 text-secondary">No hay categorías disponibles</p>
      </div>
    </section>
    <ProductAddedModal
      :visible="modalVisible"
      :product="modalProduct"
      :quantity="modalQuantity"
      @continue="handleContinueShopping"
      @goToCart="handleGoToCart"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getMenu } from '../services/menu';

import { reactive } from 'vue';
import ProductCard from '../components/ProductCard.vue';
import ProductAddedModal from '../components/ProductAddedModal.vue';
import { useCartStore } from '../store/cart';
import { useUI } from '../store/ui';

import { useRouter } from 'vue-router';

const router = useRouter();
const cartStore = useCartStore();
const { showError } = useUI();
const quantities = reactive({});

const modalVisible = ref(false);
const modalProduct = ref(null);
const modalQuantity = ref(1);

function increment(product) {
  if (!quantities[product.id]) quantities[product.id] = 1;
  quantities[product.id]++;
}
function decrement(product) {
  if (!quantities[product.id]) quantities[product.id] = 1;
  if (quantities[product.id] > 1) quantities[product.id]--;
}
async function addToCart(product) {
  const qty = quantities[product.id] || 1;
  const result = await cartStore.addItem(product, qty);
  if (result.success) {
    modalProduct.value = product;
    modalQuantity.value = qty;
    modalVisible.value = true;
  } else {
    showError('Error al añadir al carrito');
  }
}
function handleContinueShopping() {
  modalVisible.value = false;
}
function handleGoToCart() {
  modalVisible.value = false;
  // Use router to go to cart
    router.push('/cart');
}

const categories = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    categories.value = await getMenu();
  } catch (e) {
    error.value = 'No se pudo cargar la carta.';
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.add-to-cart-row-inner {
  display: flex;
  align-items: center;
  gap: 1rem;
  justify-content: center;
}
.product-card-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  /* lighter grey background for menu page product tiles */
  background: #2f2f2f;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  margin-bottom: 0.5rem;
  padding: 0rem 0rem 0.5rem 0rem;
  gap: 0.5rem;
}
.quantity-selector {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0.5rem 0 0.5rem 0;
}
.qty-btn {
  background: var(--primary-color);
  color: #1a1a1a;
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  font-size: 1.2rem;
  font-weight: bold;
  margin: 0 0.5rem;
  cursor: pointer;
  transition: background 0.2s;
}
.qty-btn:hover {
  background: #ffd700;
}
.qty-value {
  font-size: 1.1rem;
  font-weight: 600;
  min-width: 32px;
  text-align: center;
}
 .add-to-cart-btn {
   display: flex;
   align-items: center;
   gap: 0.5rem;
   font-size: 1rem;
   font-weight: 600;
   border-radius: 50px;
   background: transparent;
   color: var(--primary-color);
   border: 2px solid var(--primary-color);
   box-shadow: none;
   transition: color 0.2s, border-color 0.2s;
 }
 .add-to-cart-btn:hover {
   color: #1a1a1a;
   border-color: #ffd700;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 50px;
}
.add-to-cart-text {
  font-size: 1rem;
  font-weight: 600;
  color: var(--primary-color);
  text-align: center;
}
.menu-page {
  max-width: 100%;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}
.categories-section {
  max-width: 1400px;
  margin: 0 auto;
  background: var(--bg-card);
}
.section-header {
  margin-bottom: 4rem;
}
.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  font-family: 'Georgia', serif;
}
.section-subtitle {
  font-size: 0.95rem;
  color: var(--text-secondary);
  font-weight: 300;
  padding: 0 0.5rem;
}
.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  max-width: 1200px;
  margin: 0 auto;
  animation: fadeIn 0.6s ease;
}
.category-card {
  background: var(--bg-card);
  padding: 2rem 1rem 1.5rem 1rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  margin-bottom: 1rem;
  /* dark separator between categories */
  border-bottom: 6px solid var(--bg-darker);
}
/* Remove separator for the very last category in the grid to avoid extra bottom stroke */
.categories-grid .category-card:last-child {
  border-bottom: none;
}
.category-header {
  margin-bottom: 1.5rem;
}
.category-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-bottom: 0.5rem;
  font-family: 'Georgia', serif;
}
.category-description {
  color: var(--text-secondary);
  font-size: 0.95rem;
  margin-bottom: 1rem;
}
.products-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-top: 1rem;
  padding-left: 0;
  padding-right: 0;
}
.products-grid ::v-deep .product-card {
  /* Ensure product cards show a subtle border and lighter background on Menu page only */
  border: 1px solid var(--border-color) !important;
  border-radius: 12px !important;
  background: #2f2f2f !important;
}
.product-card {
  background: var(--bg-card);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  margin-bottom: 0.5rem;
}
.product-name {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.3rem;
}
.product-description {
  color: var(--text-secondary);
  font-size: 0.9rem;
}
.loading-state,
.error-state,
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
}
.loading-state .spinner-border {
  width: 3.5rem;
  height: 3.5rem;
  border-width: 4px;
}
.error-state i,
.empty-state i {
  font-size: 4rem;
  opacity: 0.6;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
