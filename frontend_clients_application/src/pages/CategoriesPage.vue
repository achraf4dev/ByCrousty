<script setup>
/**
 * Categories Page
 * Lists all available categories
 */
import { ref, onMounted } from 'vue';
import api from '../services/api';
import CategoryCard from '../components/CategoryCard.vue';

const categories = ref([]);
const loading = ref(false);
const error = ref(null);

const loadCategories = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await api.getActiveCategories();
    categories.value = response.data.data || response.data || [];
  } catch (err) {
    error.value = 'Error al cargar las categorías';
    console.error('Error loading categories:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadCategories();
});
</script>

<template>
  <div class="categories-page">
    <div class="page-header">
      <h1>Categorías</h1>
      <p>Explora nuestras diferentes categorías de productos</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Cargando categorías...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error">
      <i class="bi bi-exclamation-triangle"></i>
      <p>{{ error }}</p>
      <button class="btn-primary" @click="loadCategories">Reintentar</button>
    </div>

    <!-- Categories Grid -->
    <div v-else-if="categories.length > 0" class="categories-grid">
      <CategoryCard 
        v-for="category in categories" 
        :key="category.id"
        :category="category"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="empty-state">
      <i class="bi bi-inbox"></i>
      <p>No hay categorías disponibles</p>
    </div>
  </div>
</template>

<style scoped>
.categories-page {
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

.categories-grid {
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
  .categories-grid {
    grid-template-columns: 1fr;
  }

  .page-header h1 {
    font-size: 2rem;
  }
}
</style>
