<script setup>
/**
 * Home Page Component
 * Professional restaurant-style landing page
 */
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import CategoryCard from '../components/CategoryCard.vue';
import ProductCard from '../components/ProductCard.vue';
import api from '../services/api';

const router = useRouter();

// State
const categories = ref([]);
const products = ref([]);
const promotionalProducts = ref([]);
const loading = ref(true);
const productsLoading = ref(true);
const promotionalLoading = ref(true);
const error = ref(null);
const productsError = ref(null);
const promotionalError = ref(null);

// Featured products/stats
const stats = ref([
  { icon: 'bi-award-fill', value: '10+', label: 'Años de Experiencia' },
  { icon: 'bi-people-fill', value: '5000+', label: 'Clientes Satisfechos' },
  { icon: 'bi-shop', value: '100+', label: 'Productos Frescos' }
]);

// ...existing code...

// Features
const features = ref([
  {
    icon: 'bi-clock-fill',
    title: 'Servicio Rápido',
    description: 'Preparación y entrega en tiempo récord'
  },
  {
    icon: 'bi-heart-fill',
    title: 'Hecho con Amor',
    description: 'Cada producto elaborado con pasión'
  },
  {
    icon: 'bi-shield-check',
    title: 'Calidad Garantizada',
    description: 'Ingredientes frescos y certificados'
  },
  {
    icon: 'bi-truck',
    title: 'Envío a Domicilio',
    description: 'Llevamos frescura a tu puerta'
  }
]);


// Load most sold categories (limit 4)
const loadCategories = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.getMostSoldCategories();
    categories.value = response.data.data || [];
  } catch (err) {
    console.error('Error loading categories:', err);
    error.value = 'Error al cargar las categorías. Por favor, intenta de nuevo.';
  } finally {
    loading.value = false;
  }
};

// Load most sold products (limit 8)
const loadProducts = async () => {
  productsLoading.value = true;
  productsError.value = null;
  try {
    const response = await api.getMostSoldProducts();
    products.value = response.data.data || [];
  } catch (err) {
    console.error('Error loading products:', err);
    productsError.value = 'Error al cargar los productos. Por favor, intenta de nuevo.';
  } finally {
    productsLoading.value = false;
  }
};

// Load promotional products (with points > 0)
const loadPromotionalProducts = async () => {
  promotionalLoading.value = true;
  promotionalError.value = null;
  
  try {
    const response = await api.getProductsWithPoints({ per_page: 5 });
    promotionalProducts.value = response.data.data?.data || response.data.data || [];
  } catch (err) {
    console.error('Error loading promotional products:', err);
    promotionalError.value = 'Error al cargar las promociones. Por favor, intenta de nuevo.';
  } finally {
    promotionalLoading.value = false;
  }
};

// Navigate to all categories
const viewAllCategories = () => {
  router.push('/categories');
};

// Navigate to products
const exploreMenu = () => {
  router.push('/products');
};

// Navigate to offers
const viewAllOffers = () => {
  router.push('/products'); // You can change this to a specific offers page if available
};

// Initialize
onMounted(() => {
  loadCategories();
  loadProducts();
  loadPromotionalProducts();
});
</script>

<template>
  <div class="home-page">
  <!-- Products Section (hero/slider removed) -->
  <section class="products-section bg-light-section">
      <div class="section-header text-center mb-2">
        <div>
          <h2 class="section-title">Descubre Nuestros Productos</h2>
          <p class="section-subtitle">Explora nuestra selección de productos frescos y de calidad</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="productsLoading" class="loading-state">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3 text-secondary">Cargando productos...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="productsError" class="error-state">
        <i class="bi bi-exclamation-circle text-danger"></i>
        <p class="mt-3 text-secondary">{{ productsError }}</p>
        <button class="btn btn-warning mt-3" @click="loadProducts">
          <i class="bi bi-arrow-clockwise me-2"></i>Reintentar
        </button>
      </div>

      <!-- Products Grid -->
      <div v-else-if="products.length > 0">
        <div class="featured-product-full-width mb-2">
          <ProductCard
            :product="products[0]"
            :hideAddToCart="true"
            fullWidth
            :imageHeight="300"
          />
        </div>
        <div v-if="products.length > 1" class="products-grid">
          <ProductCard
            v-for="product in products.slice(1)"
            :key="product.id"
            :product="product"
            :hideAddToCart="true"
          />
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <i class="bi bi-inbox text-secondary"></i>
        <p class="mt-3 text-secondary">No hay productos disponibles</p>
      </div>

      <!-- View All Button -->
      <div v-if="products.length > 0" class="view-all-products-wrapper mt-5">
        <button class="btn btn-view-all" @click="exploreMenu">
          Ver Todos los Productos <i class="bi bi-arrow-right ms-2"></i>
        </button>
      </div>
    </section>

    <!-- Categories Section -->
  <section class="categories-section bg-dark-section">
      <div class="section-header text-center mb-5">
        <div>
          <h2 class="section-title">Nuestras Categorías</h2>
          <p class="section-subtitle">Descubre nuestra variedad de productos premium</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3 text-secondary">Cargando categorías...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-state">
        <i class="bi bi-exclamation-circle text-danger"></i>
        <p class="mt-3 text-secondary">{{ error }}</p>
        <button class="btn btn-warning mt-3" @click="loadCategories">
          <i class="bi bi-arrow-clockwise me-2"></i>Reintentar
        </button>
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
        <i class="bi bi-inbox text-secondary"></i>
        <p class="mt-3 text-secondary">No hay categorías disponibles</p>
      </div>

      <!-- View All Button -->
      <div v-if="categories.length > 0" class="view-all-categories-wrapper mt-5">
        <button class="btn btn-view-all" @click="viewAllCategories">
          Ver Todas las Categorías <i class="bi bi-arrow-right ms-2"></i>
        </button>
      </div>
    </section>

    <!-- Promotions Section -->
  <section class="promotions-section bg-light-section">
      <div class="section-header text-center mb-5">
        <div>
          <h2 class="section-title">Productos con Puntos</h2>
          <p class="section-subtitle">Canjea tus puntos por estos productos especiales</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="promotionalLoading" class="loading-state">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3 text-secondary">Cargando promociones...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="promotionalError" class="error-state">
        <i class="bi bi-exclamation-circle text-danger"></i>
        <p class="mt-3 text-secondary">{{ promotionalError }}</p>
        <button class="btn btn-warning mt-3" @click="loadPromotionalProducts">
          <i class="bi bi-arrow-clockwise me-2"></i>Reintentar
        </button>
      </div>

      <!-- Promotional Products List -->
      <div v-else-if="promotionalProducts.length > 0" class="promotions-list">
        <div 
          v-for="product in promotionalProducts" 
          :key="product.id"
          class="promotion-item"
          @click="router.push(`/products/${product.id}`)"
        >
          <div class="promo-image">
            <img :src="product.image_url || product.image || 'https://placehold.co/200x120/1a1a1a/FFD700?text=Product'" :alt="product.name" />
          </div>
          <div class="promo-content">
            <p class="promo-text">{{ product.name }}</p>
            <p v-if="product.description" class="promo-description">
              {{ product.description.substring(0, 100) }}{{ product.description.length > 100 ? '...' : '' }}
            </p>
          </div>
          <div class="promo-points">
            <span class="points-number">{{ product.points }}</span>
            <span class="points-label">puntos</span>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <i class="bi bi-inbox text-secondary"></i>
        <p class="mt-3 text-secondary">No hay productos promocionales disponibles</p>
      </div>

      <!-- View All Offers Button -->
      <div v-if="promotionalProducts.length > 0" class="view-all-offers-wrapper mt-5 view-all-right">
        <button class="btn btn-view-all" @click="viewAllOffers">
          Ver Todas las Ofertas <i class="bi bi-arrow-right ms-2"></i>
        </button>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section bg-dark-section">
      <div class="stats-header-block">
        <h2 class="section-title">Nuestra Experiencia</h2>
      </div>
      <div class="stats-subtitle-block">
        <p class="section-subtitle">Más de una década ofreciendo calidad y satisfacción</p>
      </div>
      <div class="stats-container">
        <div 
          v-for="stat in stats" 
          :key="stat.label"
          class="stat-item"
        >
          <div class="stat-icon">
            <i :class="stat.icon"></i>
          </div>
          <h3 class="stat-value">{{ stat.value }}</h3>
          <p class="stat-label">{{ stat.label }}</p>
        </div>
      </div>
    </section>

    <!-- Features Section -->
  <section class="features-section bg-light-section">
      <div class="section-header text-center mb-5">
        <h2 class="section-title">¿Por Qué Elegirnos?</h2>
        <p class="section-subtitle">Compromiso con la excelencia en cada detalle</p>
      </div>
      <div class="features-grid">
        <div 
          v-for="feature in features" 
          :key="feature.title"
          class="feature-card"
        >
          <div class="feature-icon">
            <i :class="feature.icon"></i>
          </div>
          <h3 class="feature-title">{{ feature.title }}</h3>
          <p class="feature-description">{{ feature.description }}</p>
        </div>
      </div>
    </section>


    <!-- Restaurant Information Section -->
    <section class="restaurant-info-section">
      <div class="restaurant-info-card">
        <h2 class="restaurant-title">Sobre Nuestro Restaurante</h2>
        <p class="restaurant-subtitle">Autenticidad, calidad y tradición en cada plato</p>
        <div class="restaurant-details">
          <div class="detail-item"><i class="bi bi-geo-alt"></i><span><strong>Dirección:</strong> Calle Principal 123, Ciudad, País</span></div>
          <div class="detail-item"><i class="bi bi-telephone"></i><span><strong>Teléfono:</strong> +34 123 456 789</span></div>
          <div class="detail-item"><i class="bi bi-envelope"></i><span><strong>Email:</strong> contacto@restaurante.com</span></div>
          <div class="detail-item"><i class="bi bi-clock"></i><span><strong>Horario:</strong> Lunes a Domingo, 12:00 - 23:00</span></div>
        </div>
        <div class="restaurant-social">
          <a href="https://facebook.com" target="_blank" rel="noopener" class="social-icon facebook"><i class="bi bi-facebook"></i></a>
          <a href="https://instagram.com" target="_blank" rel="noopener" class="social-icon instagram"><i class="bi bi-instagram"></i></a>
          <a href="https://twitter.com" target="_blank" rel="noopener" class="social-icon twitter"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.home-page {
  max-width: 100%;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

.stats-header-block {
  width: 100%;
  text-align: center;
  margin-bottom: 0.2rem;
}
.stats-subtitle-block {
  width: 100%;
  text-align: center;
  margin-bottom: 1.5rem;
}
.stats-container {
  display: flex;
  flex-direction: row;
  justify-content: center;
  gap: 2rem;
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
}


.bg-light-section {
  background: var(--bg-card) !important;
}
.bg-dark-section {
  background: var(--bg-darker) !important;
}

/* ...existing code... */

/* Stats Section */
.stats-section {
  background: var(--bg-card);
  padding: 2rem 0.5rem 0.5rem 0.5rem;
  margin: 0;
  /* Removed display:flex and align-items:center to allow vertical stacking */
}



.stat-item {
  text-align: center;
  transition: transform 0.3s ease;
  min-width: 0;
  padding: 0.5rem 0.05rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.stat-item:hover {
  /* hover transform removed */
}

.stat-icon {
  width: 45px;
  height: 45px;
  background: var(--bg-darker);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 0.7rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
}

.stat-item:hover .stat-icon {
  /* hover transform/box-shadow removed */
}

.stat-icon i {
  font-size: 1.2rem;
  color: var(--text-primary);
}

.stat-value {
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 0.45rem;
  line-height: 1;
}

.stat-label {
  color: var(--text-secondary);
  font-size: 0.6rem;
  text-transform: uppercase;
  letter-spacing: 0.01em;
  font-weight: 500;
  line-height: 1.3;
  word-break: keep-all;
  overflow-wrap: break-word;
  hyphens: none;
}

/* Features Section */
.features-section {
  padding: 3rem 1.5rem;
  background: var(--bg-darker);
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
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.feature-card {
  background: var(--bg-card);
  padding: 2.5rem 1.5rem;
  border-radius: 16px;
  text-align: center;
  border: 2px solid var(--border-color);
  transition: all 0.3s ease;
  position: relative;
}

.feature-card:hover {
  /* hover transform/box-shadow removed */
}

.feature-icon {
  width: 70px;
  height: 70px;
  background: var(--primary-color);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  transition: transform 0.3s ease;
  box-shadow: 0 4px 15px rgba(252, 186, 27, 0.3);
}

.feature-card:hover .feature-icon {
  /* hover transform removed */
}

.feature-icon i {
  font-size: 2rem;
  color: #1a1a1a;
}

.feature-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.feature-description {
  color: var(--text-secondary);
  line-height: 1.6;
  font-size: 0.95rem;
}

/* Categories Section */
.categories-section {
  padding: 3rem 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
  background: var(--bg-card);
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  animation: fadeIn 0.6s ease;
}

/* Products Section */
.products-section {
  padding: 3rem 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
  background: var(--bg-darker);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  animation: fadeIn 0.6s ease;
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

/* View All Categories Button */
.view-all-categories-wrapper {
  text-align: center;
}

.btn-view-all {
  background: transparent;
  color: var(--primary-color);
  border: 2px solid var(--primary-color);
  padding: 0.75rem 2rem;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 50px;
  transition: all 0.3s ease;
}

.btn-view-all:hover {
  /* hover transform removed */
}

/* Promotions Section */
.promotions-section {
  padding: 3rem 0;
  max-width: 1400px;
  margin: 0 auto;
  background: var(--bg-card);
}

.promotions-section .section-header {
  padding: 0 1.5rem;
}

.promotions-list {
  max-width: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0;
}

.promotion-item {
  display: flex;
  align-items: stretch;
  gap: 0;
  padding: 0;
  background: var(--bg-darker);
  border-radius: 0;
  border-bottom: 1px solid var(--border-color);
  transition: all 0.3s ease;
  cursor: pointer;
  height: 120px;
}

.promotion-item:first-child {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}

.promotion-item:last-child {
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  border-bottom: 1px solid var(--border-color);
}

.promotion-item:hover {
  /* hover styles removed */
}

.promo-image {
  flex-shrink: 0;
  width: 120px;
  height: 100%;
  overflow: hidden;
  border: none;
  background: #1a1a1a;
  padding: 0.5rem 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.promo-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.promo-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 1rem 1.5rem;
  justify-content: center;
  min-width: 0;
}

.promo-text {
  margin: 0;
  color: var(--text-primary);
  font-size: 1rem;
  font-weight: 600;
  line-height: 1.4;
}

.promo-description {
  margin: 0;
  color: var(--text-secondary);
  font-size: 0.875rem;
  line-height: 1.5;
  overflow: hidden;
  text-overflow: ellipsis;
}

.promo-points {
  flex-shrink: 0;
  margin: 0 1.5rem;
  color: #1a1a1a;
  background: var(--primary-color);
  font-weight: 700;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.15rem;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  text-align: center;
  box-shadow: 0 4px 15px rgba(252, 186, 27, 0.4);
  align-self: center;
}

.points-number {
  font-size: 1.4rem;
  line-height: 1;
  font-weight: 800;
}

.points-label {
  font-size: 0.6rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
  color: rgba(26, 26, 26, 0.8);
}

.view-all-offers-wrapper {
  text-align: right;
  padding: 0 1.5rem;
  margin-top: 3rem;
}

.btn-offers {
  background: var(--primary-color);
  color: #1a1a1a;
  border: none;
  padding: 0.75rem 2rem;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 50px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.btn-offers:hover {
  /* hover transform/box-shadow removed */
}

/* CTA Section */
.cta-section {
  background: #1a1a1a;
  padding: 6rem 2rem;
  text-align: center;
  border-top: 2px solid var(--primary-color);
  position: relative;
  overflow: hidden;
}

.cta-section::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 600px;
  height: 600px;
  background: rgba(252, 186, 27, 0.05);
  border-radius: 50%;
}

.cta-content {
  position: relative;
  z-index: 1;
  max-width: 800px;
  margin: 0 auto;
}

.cta-icon {
  font-size: 3.5rem;
  color: var(--primary-color);
  margin-bottom: 1.5rem;
  display: inline-block;
  animation: pulse 2s infinite;
}

.cta-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 1rem;
  font-family: 'Georgia', serif;
}

.cta-subtitle {
  font-size: 1.15rem;
  color: var(--text-secondary);
  margin-bottom: 3rem;
  line-height: 1.7;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
}

.cta-buttons .btn {
  font-size: 1.1rem;
  font-weight: 600;
  border-radius: 50px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.cta-buttons .btn:hover {
  /* hover transform/box-shadow removed */
}

/* Animations */
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

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.8;
  }
}

/* Responsive Design */
@media (max-width: 576px) {
  .hero-slider {
    height: 500px;
  }

  .hero-content {
    padding: 0 1.5rem;
    justify-content: center;
  }

  .content-wrapper {
    text-align: center;
  }

  .hero-title {
    font-size: 2.5rem;
  }

  .hero-subtitle {
    font-size: 1.3rem;
  }

  .hero-description {
    font-size: 1rem;
  }

  .hero-actions {
    justify-content: center;
    flex-direction: column;
  }

  .hero-actions .btn {
    width: 100%;
  }

  .slider-indicators {
    bottom: 1rem;
  }

  .indicator {
    width: 30px;
  }

  .stats-section {
    padding: 1.5rem 0rem;
  }

  .features-section,
  .categories-section,
  .products-section {
    padding: 3rem 0.5rem;
  }

  .promotions-section {
    padding: 3rem 0;
  }

  .promotions-section .section-header {
    padding: 0 0.5rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .promotions-list {
    gap: 0;
  }

  .promotion-item {
    height: 100px;
  }

  .promo-image {
    width: 90px;
    padding: 0.4rem 0;
  }

  .promo-content {
    padding: 0.75rem 1rem;
    gap: 0.3rem;
  }

  .promo-text {
    font-size: 0.9rem;
  }

  .promo-description {
    font-size: 0.8rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .promo-points {
    width: 65px;
    height: 65px;
    margin: 0 1rem;
  }

  .points-number {
    font-size: 1.1rem;
  }

  .points-label {
    font-size: 0.5rem;
  }

  .view-all-offers-wrapper {
    padding: 0 0.5rem;
    margin-top: 2rem;
  }

  .btn-offers {
    padding: 0.5rem 1.5rem;
    font-size: 0.9rem;
  }

  .features-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0;
  }

  .feature-card {
    padding: 1.2rem 0.8rem;
    border-radius: 0;
    border: none;
    border-right: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
  }

  .feature-card:nth-child(2n) {
    border-right: none;
  }

  .feature-card:nth-last-child(-n+2) {
    border-bottom: none;
  }

  .feature-icon {
    width: 50px;
    height: 50px;
    margin-bottom: 0.8rem;
  }

  .feature-icon i {
    font-size: 1.5rem;
  }

  .feature-title {
    font-size: 1rem;
    margin-bottom: 0.5rem;
  }

  .feature-description {
    font-size: 0.8rem;
    line-height: 1.4;
  }

  .categories-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
  }

  .products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
  }

  .view-all-categories-wrapper,
  .view-all-products-wrapper {
    text-align: right;
    margin-top: 1rem !important;
    padding-right: 0.5rem;
  }

  .btn-view-all {
    border: none;
    background: transparent;
    padding: 0.5rem 0;
    font-size: 0.85rem;
    font-weight: 500;
  }

  .cta-section {
    padding: 4rem 1.5rem;
  }

  .cta-title {
    font-size: 1.8rem;
  }

  .cta-subtitle {
    font-size: 1rem;
  }
}

@media (min-width: 768px) {
  .hero-slider {
    height: 650px;
  }

  .hero-content {
    padding: 0 4rem;
  }

  .hero-title {
    font-size: 4.5rem;
  }

  .hero-subtitle {
    font-size: 2rem;
  }

  .hero-description {
    font-size: 1.25rem;
  }

  .stats-section,
  .features-section,
  .cta-section {
    padding-left: 3rem;
    padding-right: 3rem;
  }

  .welcome-title {
    font-size: 3.5rem;
  }

  .categories-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 992px) {
  .stats-section {
    padding: 1.5rem 3rem;
  }

  .stats-container {
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    max-width: 1200px;
  }

  .stat-item {
    padding: 1rem;
  }

  .stat-icon {
    width: 60px;
    height: 60px;
    margin-bottom: 1rem;
  }

  .stat-icon i {
    font-size: 1.8rem;
  }

  .stat-value {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
  }

  .stat-label {
    font-size: 0.75rem;
  }

  .features-grid {
    grid-template-columns: repeat(4, 1fr);
  }

  .categories-grid {
    grid-template-columns: repeat(3, 1fr);
  }

  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1200px) {
  .hero-slider {
    height: 700px;
  }

  .hero-title {
    font-size: 5rem;
  }
}

/* Page Load Animation */
.home-page {
  animation: pageLoad 0.5s ease;
}

@keyframes pageLoad {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
/* --- Restaurant Info Section Styles --- */
.restaurant-info-section {
  background: linear-gradient(135deg, var(--bg-darker) 60%, var(--primary-color) 100%);
  padding: 3rem 0 0 0;
  margin-bottom: 0 !important;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: unset;
}

.restaurant-info-card {
  background: transparent;
  box-shadow: none;
  padding: 3rem 2rem 2.5rem 2rem;
  max-width: 480px;
  width: 100%;
  margin: 0 auto;
  text-align: center;
  animation: fadeIn 0.7s cubic-bezier(.4,0,.2,1);
}

.restaurant-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-bottom: 0.5rem;
  font-family: 'Georgia', serif;
}

.restaurant-subtitle {
  font-size: 1.1rem;
  color: var(--text-secondary);
  margin-bottom: 2.2rem;
  font-weight: 400;
}

.restaurant-details {
  margin-bottom: 2.2rem;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  font-size: 1rem;
  color: var(--text-primary);
  background: var(--bg-card);
  padding: 0.7rem 1.1rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  transition: background 0.2s;
}
.detail-item i {
  font-size: 1.3rem;
  color: var(--primary-color);
}

.restaurant-social {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 1.2rem;
}
.social-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--bg-card);
  color: var(--primary-color);
  font-size: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  transition: background 0.2s, color 0.2s, transform 0.2s;
}
.social-icon:hover {
  /* hover transform removed; keep color change only */
  background: var(--primary-color);
  color: #1a1a1a;
}
.social-icon.facebook:hover,
.social-icon.instagram:hover,
.social-icon.twitter:hover {
  /* hover box-shadow removed */
}

@media (max-width: 576px) {
  .restaurant-info-card {
    padding: 1.5rem 0.5rem 1.5rem 0.5rem;
  }
  .restaurant-title {
    font-size: 1.3rem;
  }
  .restaurant-details {
    gap: 0.6rem;
  }
  .detail-item {
    font-size: 0.95rem;
    padding: 0.5rem 0.7rem;
  }
  .restaurant-social {
    gap: 0.7rem;
  }
  .social-icon {
    width: 38px;
    height: 38px;
    font-size: 1.3rem;
  }
}
</style>
