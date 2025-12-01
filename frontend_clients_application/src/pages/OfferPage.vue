<template>
  <div class="offer-page">
    <section class="offers-section">
      <!-- Title removed as requested -->

      <div v-if="loading" class="loading-state">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>

      <div v-else-if="error" class="error-state">
        <p class="text-danger">{{ error }}</p>
      </div>

      <div v-else-if="productsList.length === 0" class="empty-state">
        <p>No hay ofertas disponibles por puntos.</p>
      </div>

      <div v-else class="offers-grid">
        <div v-for="product in productsList" :key="product.id" class="offer-card" @click="openQrFor(product)">
          <ProductCard
            :product="product"
            :hideAddToCart="true"
            :usePoints="true"
            :disabled="!canRedeem(product)"
          />
        </div>
      </div>
    </section>
  </div>
  <FakeQrModal :visible="showQr" @close="closeQr" />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../services/api';
import ProductCard from '../components/ProductCard.vue';
import FakeQrModal from '../components/FakeQrModal.vue';
import { useAuth } from '../store/auth';
import { useCartStore } from '../store/cart';
import { useUI } from '../store/ui';
import { useRouter } from 'vue-router';

const productsList = ref([]);
const loading = ref(true);
const error = ref(null);

const { user, isAuthenticated } = useAuth();
const cartStore = useCartStore();
const { showSuccess, showError, showWarning } = useUI();
const router = useRouter();

const fetchOffers = async () => {
  loading.value = true;
  error.value = null;

  try {
    const res = await api.getProductsWithPoints();
    // API returns paginated object, extract items safely
    const data = res.data?.data ?? res.data;
    // if pagination, data.data holds items
    productsList.value = Array.isArray(data) ? data : (data?.data || []);
  } catch (e) {
    console.error('Error fetching offers:', e);
    error.value = 'No se pudieron cargar las ofertas.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchOffers();
});

const canRedeem = (product) => {
  const ptsNeeded = Number(product.points || 0);
  const userPts = Number(user.value?.points ?? 0);
  return isAuthenticated.value && userPts >= ptsNeeded;
};

const redeemWithPoints = async (product) => {
  if (!isAuthenticated.value) {
    showWarning('Inicia sesión para canjear productos con puntos');
    router.push('/login');
    return;
  }

  const ptsNeeded = Number(product.points || 0);
  const userPts = Number(user.value?.points ?? 0);

  if (userPts < ptsNeeded) {
    showError('No tienes suficientes puntos');
    return;
  }

  // Add to cart (standard flow). If you want a dedicated "pay with points" API,
  // we can add it later. For now add to cart and let checkout handle points usage.
  const result = await cartStore.addItem(product, 1);
  if (result.success) {
    showSuccess('Producto añadido al carrito. Procede al pago para usar puntos.');
  } else {
    showError('Error al añadir al carrito');
  }
};

// QR modal state
const showQr = ref(false);
const selectedProduct = ref(null);

const openQrFor = (product) => {
  console.log('OfferPage: openQrFor called for', product?.id || product?.name);
  if (!isAuthenticated.value) {
    showWarning('Inicia sesión para canjear productos con puntos');
    router.push('/login');
    return;
  }

  if (!canRedeem(product)) {
    showError('No tienes suficientes puntos');
    return;
  }

  selectedProduct.value = product;
  showQr.value = true;
};

const closeQr = () => {
  showQr.value = false;
  selectedProduct.value = null;
};
</script>

<style scoped>
/* Clean styles for OfferPage - force two products per row */
.offer-page { padding: 1.5rem; }
.section-header { text-align: center; margin-bottom: 1rem; }
.section-title { font-size: 1.25rem; margin-bottom: 0.25rem; }
.section-subtitle { color: var(--text-secondary); font-size: 0.95rem; }

.offers-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  width: 100%;
}

.offer-card {
  background: var(--bg-card);
  border-radius: 10px;
  box-sizing: border-box;
  width: 100%;
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.offer-card > * { width: 100%; }

.offer-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}

.points-required { display:flex; align-items:center; gap:6px; color:var(--primary-color); font-weight:700; }
.pts { font-size:0.95rem; }
.redeem-btn { padding:0.45rem 0.9rem; border-radius:999px; }
.redeem-btn:disabled { opacity:0.6; cursor:not-allowed; }

.loading-state, .error-state, .empty-state { text-align:center; padding:1.5rem; }

/* No media query - force 2 columns even on small screens per request. */
</style>
