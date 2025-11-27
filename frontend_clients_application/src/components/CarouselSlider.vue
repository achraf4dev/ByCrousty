<script setup>
/**
 * Carousel Slider Component
 * Bootstrap 5 carousel for promotions and featured content
 */
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  slides: {
    type: Array,
    required: true,
    default: () => [],
  },
  interval: {
    type: Number,
    default: 5000, // Auto-slide every 5 seconds
  },
  autoSlide: {
    type: Boolean,
    default: true,
  },
});

const carouselRef = ref(null);
const carouselInstance = ref(null);

onMounted(() => {
  if (carouselRef.value && window.bootstrap) {
    // Initialize Bootstrap carousel
    carouselInstance.value = new window.bootstrap.Carousel(carouselRef.value, {
      interval: props.autoSlide ? props.interval : false,
      ride: props.autoSlide ? 'carousel' : false,
      pause: 'hover',
      wrap: true,
      touch: true,
    });
  }
});

onUnmounted(() => {
  // Dispose carousel instance
  if (carouselInstance.value) {
    carouselInstance.value.dispose();
  }
});
</script>

<template>
  <div 
    v-if="slides.length > 0"
    id="bycroustyCarousel" 
    ref="carouselRef"
    class="carousel slide carousel-fade" 
    data-bs-ride="carousel"
  >
    <!-- Indicators -->
    <div v-if="slides.length > 1" class="carousel-indicators">
      <button
        v-for="(slide, index) in slides"
        :key="`indicator-${index}`"
        type="button"
        data-bs-target="#bycroustyCarousel"
        :data-bs-slide-to="index"
        :class="{ active: index === 0 }"
        :aria-current="index === 0 ? 'true' : 'false'"
        :aria-label="`Slide ${index + 1}`"
      ></button>
    </div>

    <!-- Slides -->
    <div class="carousel-inner">
      <div
        v-for="(slide, index) in slides"
        :key="index"
        :class="['carousel-item', { active: index === 0 }]"
      >
        <!-- Slide Image -->
        <img 
          v-if="slide.image" 
          :src="slide.image" 
          :alt="slide.title || 'Slide'" 
          class="d-block w-100"
        />
        
        <!-- Slide Content Overlay -->
        <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
          <div class="carousel-caption-content">
            <h3 v-if="slide.title" class="carousel-title display-5 fw-bold mb-3">
              {{ slide.title }}
            </h3>
            <p v-if="slide.description" class="carousel-description lead mb-4">
              {{ slide.description }}
            </p>
            <button 
              v-if="slide.buttonText" 
              class="btn btn-warning btn-lg fw-bold px-4 py-2"
            >
              {{ slide.buttonText }}
              <i class="bi bi-arrow-right ms-2"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Controls -->
    <button 
      v-if="slides.length > 1"
      class="carousel-control-prev" 
      type="button" 
      data-bs-target="#bycroustyCarousel" 
      data-bs-slide="prev"
    >
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button 
      v-if="slides.length > 1"
      class="carousel-control-next" 
      type="button" 
      data-bs-target="#bycroustyCarousel" 
      data-bs-slide="next"
    >
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</template>

<style scoped>
/* Carousel Container */
.carousel {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
  height: 300px;
  background: var(--bg-card);
}

.carousel-inner {
  height: 100%;
}

.carousel-item {
  height: 100%;
  position: relative;
  background: var(--bg-darker);
}

/* Carousel Images */
.carousel-item img {
  height: 100%;
  object-fit: cover;
  filter: brightness(0.7);
}

/* Carousel Caption Overlay */
.carousel-caption {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.3),
    rgba(0, 0, 0, 0.7)
  );
  padding: 0;
}

.carousel-caption-content {
  text-align: center;
  max-width: 800px;
  padding: 1rem;
}

/* Carousel Text */
.carousel-title {
  color: white;
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
  font-size: 1.75rem;
  line-height: 1.2;
}

.carousel-description {
  color: rgba(255, 255, 255, 0.95);
  text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.8);
  font-size: 1rem;
}

/* Carousel Controls */
.carousel-control-prev,
.carousel-control-next {
  width: 50px;
  opacity: 0.8;
  transition: all 0.3s ease;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
  opacity: 1;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  width: 40px;
  height: 40px;
  background-color: rgba(0, 0, 0, 0.6);
  border-radius: 50%;
  transition: all 0.3s ease;
}

.carousel-control-prev:hover .carousel-control-prev-icon,
.carousel-control-next:hover .carousel-control-next-icon {
  background-color: var(--primary-color);
  filter: brightness(1.2);
}

/* Carousel Indicators */
.carousel-indicators {
  margin-bottom: 1.5rem;
}

.carousel-indicators [data-bs-target] {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin: 0 6px;
  background-color: rgba(255, 255, 255, 0.5);
  border: 2px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
}

.carousel-indicators .active {
  width: 40px;
  border-radius: 6px;
  background-color: var(--primary-color);
  border-color: var(--primary-color);
}

/* Carousel Fade Animation */
.carousel-fade .carousel-item {
  opacity: 0;
  transition: opacity 0.6s ease-in-out;
}

.carousel-fade .carousel-item.active {
  opacity: 1;
}

/* Button Styling */
.carousel-caption .btn {
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
  border: none;
}

.carousel-caption .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(255, 215, 0, 0.5);
}

/* Responsive Design */
@media (min-width: 768px) {
  .carousel {
    height: 400px;
  }

  .carousel-title {
    font-size: 2.5rem;
  }

  .carousel-description {
    font-size: 1.25rem;
  }

  .carousel-caption-content {
    padding: 2rem;
  }

  .carousel-control-prev-icon,
  .carousel-control-next-icon {
    width: 50px;
    height: 50px;
  }
}

@media (min-width: 992px) {
  .carousel {
    height: 450px;
  }

  .carousel-title {
    font-size: 3rem;
  }
}

/* Touch Devices */
@media (hover: none) {
  .carousel-control-prev,
  .carousel-control-next {
    opacity: 0.6;
  }
}
</style>
