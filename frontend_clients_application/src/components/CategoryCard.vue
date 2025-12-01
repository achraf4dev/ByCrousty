<script setup>
/**
 * Category Card Component
 * Displays category information with navigation
 */
import { computed } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  category: {
    type: Object,
    required: true,
  },
});

const router = useRouter();

const categoryImage = computed(() => {
  return props.category.image_url || props.category.image || 'https://placehold.co/300x200/1a1a1a/FFD700?text=Category';
});

const viewCategory = () => {
  router.push(`/products?category=${props.category.id}`);
};
</script>

<template>
  <div class="category-card" @click="viewCategory">
    <!-- Category Image -->
    <div class="category-image">
      <img :src="categoryImage" :alt="category.name" />
      <div class="category-overlay">
        <h3 class="category-name">{{ category.name }}</h3>
        <p v-if="category.description" class="category-description">
          {{ category.description }}
        </p>
        <i class="bi bi-arrow-right-circle category-icon"></i>
      </div>
    </div>
  </div>
</template>

<style scoped>
.category-card {
  position: relative;
  background: var(--bg-card);
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  height: 200px;
}

.category-card:hover {
  /* hover transform/box-shadow removed */
}

.category-image {
  position: relative;
  width: 100%;
  height: 100%;
}

.category-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.category-card:hover .category-image img {
  /* image scale on hover removed */
}

.category-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.3),
    rgba(0, 0, 0, 0.8)
  );
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  text-align: center;
  transition: background 0.3s ease;
}

.category-card:hover .category-overlay {
  /* overlay hover background change removed */
}

.category-name {
  color: white;
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
}

.category-description {
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.875rem;
  margin-bottom: 1rem;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
}

.category-icon {
  font-size: 2rem;
  color: var(--primary-color);
  transition: transform 0.3s ease;
}

.category-card:hover .category-icon {
  /* icon hover transform removed */
}

/* Mobile - Simple style without images */
@media (max-width: 576px) {
  .category-card {
    height: auto;
    min-height: 100px;
    background: var(--bg-darker);
    border: 2px solid var(--border-color);
  }

  .category-card:hover {
    /* mobile hover transform removed */
  }

  .category-image {
    position: static;
    width: 100%;
    height: 100%;
  }

  .category-image img {
    display: none;
  }

  .category-overlay {
    position: static;
    background: var(--bg-darker);
    padding: 1.25rem;
    justify-content: center;
    width: 100%;
    height: 100%;
  }

  .category-card:hover .category-overlay {
    background: var(--bg-darker);
  }

  .category-name {
    color: var(--text-primary);
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-shadow: none;
  }

  .category-description {
    color: var(--text-secondary);
    font-size: 0.8rem;
    margin-bottom: 0.75rem;
    text-shadow: none;
  }

  .category-icon {
    font-size: 1.5rem;
    color: var(--primary-color);
  }

  .category-card:hover .category-icon {
    /* mobile icon hover transform removed */
  }
}

/* Tablet and Desktop */
@media (min-width: 768px) {
  .category-card {
    height: 250px;
  }

  .category-name {
    font-size: 1.75rem;
  }

  .category-description {
    font-size: 1rem;
  }
}
</style>
