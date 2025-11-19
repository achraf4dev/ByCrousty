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
const loading = ref(true);
const productsLoading = ref(true);
const error = ref(null);
const productsError = ref(null);

// Featured products/stats
const stats = ref([
  { icon: 'bi-award-fill', value: '10+', label: 'Años de Experiencia' },
  { icon: 'bi-people-fill', value: '5000+', label: 'Clientes Satisfechos' },
  { icon: 'bi-shop', value: '100+', label: 'Productos Frescos' }
]);

// Professional hero slider
const currentSlide = ref(0);
const heroSlides = ref([
  {
    id: 1,
    title: 'Sabores Auténticos',
    subtitle: 'Descubre la excelencia culinaria',
    description: 'Experiencia única con ingredientes frescos y de la más alta calidad',
    image: 'https://placehold.co/1920x700/1a1a1a/FFD700?text=Premium+Quality',
    highlight: 'Productos Premium'
  },
  {
    id: 2,
    title: 'Tradición y Pasión',
    subtitle: 'Cada producto cuenta una historia',
    description: 'Elaborados con dedicación siguiendo recetas tradicionales',
    image: 'https://placehold.co/1920x700/242424/FFD700?text=Fresh+Daily',
    highlight: 'Frescos Diarios'
  },
  {
    id: 3,
    title: 'Calidad Garantizada',
    subtitle: 'Compromiso con la excelencia',
    description: 'Los mejores ingredientes seleccionados para tu satisfacción',
    image: 'https://placehold.co/1920x700/0f0f0f/FFD700?text=Top+Selection',
    highlight: 'Mejor Selección'
  }
]);

// Auto-play slider
let sliderInterval = null;

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % heroSlides.value.length;
};

const goToSlide = (index) => {
  currentSlide.value = index;
  stopAutoPlay();
  startAutoPlay();
};

const startAutoPlay = () => {
  sliderInterval = setInterval(nextSlide, 4000); // Change every 4 seconds
};

const stopAutoPlay = () => {
  if (sliderInterval) {
    clearInterval(sliderInterval);
  }
};

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

// Load categories
const loadCategories = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response = await api.getCategories();
    categories.value = response.data.data || [];
  } catch (err) {
    console.error('Error loading categories:', err);
    error.value = 'Error al cargar las categorías. Por favor, intenta de nuevo.';
  } finally {
    loading.value = false;
  }
};

// Load products
const loadProducts = async () => {
  productsLoading.value = true;
  productsError.value = null;
  
  try {
    const response = await api.getProducts({ per_page: 6 });
    products.value = response.data.data?.data || response.data.data || [];
  } catch (err) {
    console.error('Error loading products:', err);
    productsError.value = 'Error al cargar los productos. Por favor, intenta de nuevo.';
  } finally {
    productsLoading.value = false;
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

// Initialize
onMounted(() => {
  loadCategories();
  loadProducts();
  startAutoPlay();
});
</script>

<template>
  <div class="home-page">
    <!-- Professional Hero Slider -->
    <section class="hero-slider" @mouseenter="stopAutoPlay" @mouseleave="startAutoPlay">
      <div class="slider-container">
        <!-- Slides -->
        <TransitionGroup name="slide-fade">
          <div 
            v-for="(slide, index) in heroSlides" 
            v-show="currentSlide === index"
            :key="slide.id"
            class="hero-slide"
            :style="{ backgroundImage: `url(${slide.image})` }"
          >
            <div class="hero-overlay"></div>
            <div class="hero-content">
              <div class="content-wrapper">
                <span class="hero-highlight">{{ slide.highlight }}</span>
                <h1 class="hero-title">{{ slide.title }}</h1>
                <p class="hero-subtitle">{{ slide.subtitle }}</p>
                <p class="hero-description">{{ slide.description }}</p>
                <div class="hero-actions">
                  <button class="btn btn-warning btn-lg px-5 py-3" @click="exploreMenu">
                    <i class="bi bi-compass me-2"></i>Explorar Menú
                  </button>
                  <button class="btn btn-outline-light btn-lg px-5 py-3" @click="viewAllCategories">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Categorías
                  </button>
                </div>
              </div>
            </div>
          </div>
        </TransitionGroup>

        <!-- Indicators -->
        <div class="slider-indicators">
          <button
            v-for="(slide, index) in heroSlides"
            :key="`indicator-${slide.id}`"
            :class="['indicator', { active: currentSlide === index }]"
            @click="goToSlide(index)"
            :aria-label="`Go to slide ${index + 1}`"
          >
            <span class="indicator-progress" v-if="currentSlide === index"></span>
          </button>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
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
    <section class="features-section">
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

    <!-- Categories Section -->
    <section class="categories-section">
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
          v-for="category in categories.slice(0, 6)" 
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

    <!-- Products Section -->
    <section class="products-section">
      <div class="section-header text-center mb-5">
        <div>
          <h2 class="section-title">Nuestros Productos</h2>
          <p class="section-subtitle">Descubre nuestra selección de productos frescos y de calidad</p>
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
      <div v-else-if="products.length > 0" class="products-grid">
        <ProductCard 
          v-for="product in products.slice(0, 6)" 
          :key="product.id"
          :product="product"
        />
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

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-content">
        <i class="bi bi-star-fill cta-icon"></i>
        <h2 class="cta-title">Experimenta la Diferencia</h2>
        <p class="cta-subtitle">Descubre sabores auténticos y productos de calidad premium que harán de cada comida un momento especial</p>
        <div class="cta-buttons">
          <button class="btn btn-warning btn-lg px-5 py-3" @click="exploreMenu">
            <i class="bi bi-shop me-2"></i>Ver Productos
          </button>
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

/* Professional Hero Slider */
.hero-slider {
  position: relative;
  margin: 0;
  margin-top: 1rem;
  height: 400px;
  overflow: hidden;
  width: 100%;
}

.slider-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.hero-slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
}

.hero-content {
  position: relative;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0 1.5rem;
  z-index: 1;
}

.content-wrapper {
  max-width: 600px;
  animation: slideInLeft 0.8s ease-out;
}

.hero-highlight {
  display: inline-block;
  background: var(--primary-color);
  color: #1a1a1a;
  padding: 0.35rem 1rem;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 1rem;
  box-shadow: 0 4px 15px rgba(252, 186, 27, 0.4);
}

.hero-title {
  font-size: 2.5rem;
  font-weight: 900;
  color: #ffffff;
  margin-bottom: 0.75rem;
  line-height: 1.1;
  text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.8);
  font-family: 'Georgia', serif;
  letter-spacing: -0.02em;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: var(--primary-color);
  margin-bottom: 0.75rem;
  font-weight: 600;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
}

.hero-description {
  font-size: 0.95rem;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 1.5rem;
  line-height: 1.6;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
  max-width: 500px;
}

.hero-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.hero-actions .btn {
  font-weight: 600;
  padding: 0.5rem 1.25rem;
  font-size: 0.9rem;
  border-radius: 50px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  border-width: 2px;
}

.hero-actions .btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
}

/* Slider Indicators */
.slider-indicators {
  position: absolute;
  bottom: 1.5rem;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 0.75rem;
  z-index: 10;
}

.indicator {
  width: 40px;
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  border: none;
  border-radius: 3px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.indicator:hover {
  background: rgba(255, 255, 255, 0.5);
}

.indicator.active {
  background: var(--primary-color);
  box-shadow: 0 0 15px rgba(255, 215, 0, 0.6);
}

.indicator-progress {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  background: var(--primary-color);
  animation: progressBar 4s linear;
}

/* Slide Transitions */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.8s ease;
}

.slide-fade-enter-from {
  opacity: 0;
  transform: translateX(100px);
}

.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(-100px);
}

/* Content Animation */
@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes progressBar {
  from {
    width: 0%;
  }
  to {
    width: 100%;
  }
}

/* Stats Section */
.stats-section {
  background: var(--bg-card);
  padding: 0.5rem 0;
  margin: 0;
  display: flex;
  align-items: center;
}

.stats-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
  max-width: 100%;
  margin: 0 auto;
  padding: 0;
  width: 100%;
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
  transform: translateY(-2px);
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
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
  border-color: var(--text-secondary);
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
  padding: 5rem 1.5rem;
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
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(255, 215, 0, 0.2);
  border-color: var(--primary-color);
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
  transform: scale(1.15) rotate(10deg);
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
  padding: 5rem 1.5rem;
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
  padding: 5rem 1.5rem;
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
  padding: 5rem 2rem;
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
  background: var(--primary-color);
  color: #1a1a1a;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
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
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
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

  .section-title {
    font-size: 1.5rem;
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

  .btn-view-all:hover {
    background: transparent;
    transform: none;
    box-shadow: none;
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
</style>
