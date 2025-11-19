<script setup>
/**
 * Products Page
 * Lists products, optionally filtered by category
 */
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import api from '../services/api';
import ProductCard from '../components/ProductCard.vue';

const route = useRoute();
const products = ref([]);
const categoryName = ref('Todos los Productos');
const loading = ref(false);
const error = ref(null);

const loadProducts = async () => {
  loading.value = true;
  error.value = null;

  try {
    const categoryId = route.query.category;
    
    if (categoryId) {
      // Load products by category
      const response = await api.getProductsByCategory(categoryId);
      
      // The API returns: { data: { category: {...}, products: { data: [...] } } }
      const responseData = response.data.data;
      if (responseData && responseData.products) {
        products.value = responseData.products.data || [];
        categoryName.value = responseData.category?.name || 'Categoría';
      } else {
        // Fallback for different response structure
        products.value = response.data.data || response.data || [];
      }
    } else {
      // Load all active products
      const response = await api.getActiveProducts();
      products.value = response.data.data || response.data || [];
      categoryName.value = 'Todos los Productos';
    }
  } catch (err) {
    error.value = 'Error al cargar los productos';
    console.error('Error loading products:', err);
  } finally {
    loading.value = false;
  }
};

// Watch for category changes in query params
watch(() => route.query.category, () => {
  loadProducts();
});

onMounted(() => {
  loadProducts();
});
</script>

<template>
  <div class="products-page">
    <div class="page-header">
      <h1>{{ categoryName }}</h1>
      <p v-if="!loading && products.length > 0">{{ products.length }} producto(s) disponible(s)</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Cargando productos...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error">
      <i class="bi bi-exclamation-triangle"></i>
      <p>{{ error }}</p>
      <button class="btn-primary" @click="loadProducts">Reintentar</button>
    </div>

    <!-- Products Grid -->
    <div v-else-if="products.length > 0" class="products-grid">
      <ProductCard 
        v-for="product in products" 
        :key="product.id"
        :product="product"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="empty-state">
      <i class="bi bi-inbox"></i>
      <p>No hay productos disponibles en esta categoría</p>
    </div>
  </div>
</template>

<style scoped>
.products-page {
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
}

.page-header p {
  color: var(--text-secondary);
  font-size: 1.1rem;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.loading,
.error,
.empty-state {
  text-align: center;
  padding: 3rem 1rem;
}

.error i,
.empty-state i {
  font-size: 3rem;
  color: var(--text-secondary);
  margin-bottom: 1rem;
}

/* Responsive */
@media (max-width: 576px) {
  .products-grid {
    grid-template-columns: 1fr;
  }

  .page-header h1 {
    font-size: 2rem;
  }
}
</style>
