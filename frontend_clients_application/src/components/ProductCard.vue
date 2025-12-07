<script setup>
/**
 * Product Card Component
 * Displays product information with add-to-cart functionality
 */
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../store/cart';
import { useUI } from '../store/ui';

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  hideAddToCart: {
    type: Boolean,
    default: false,
  },
  usePoints: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  imageHeight: {
    type: [String, Number],
    default: null,
  },
  preventDefaultOnClick: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['card-click']);

const router = useRouter();
const cartStore = useCartStore();
const { showSuccess, showError } = useUI();

const productImage = computed(() => {
  return props.product.image_url || props.product.image || 'https://placehold.co/300x200/1a1a1a/FFD700?text=No+Image';
});

const formattedPrice = computed(() => {
  return `${Number(props.product.price).toFixed(2)}€`;
});

const displayedPrice = computed(() => {
  if (props.usePoints) {
    const pts = Number(props.product.points || 0);
    return `${pts} pts`;
  }
  return formattedPrice.value;
});

const isInCart = computed(() => {
  return cartStore.isInCart(props.product.id);
});

const viewDetails = () => {
  if (props.preventDefaultOnClick) {
    console.log('ProductCard: emitting card-click for', props.product?.id || props.product?.name);
    emit('card-click', props.product);
    return;
  }

  router.push(`/products/${props.product.id}`);
};

const addToCart = async (event) => {
  event.stopPropagation();
  if (props.disabled) return;

  const result = await cartStore.addItem(props.product, 1);
  
  if (result.success) {
    showSuccess(`${props.product.name} añadido al carrito`);
  } else {
    showError('Error al añadir al carrito');
  }
};
</script>

<template>
  <div class="product-card" :class="{ 'is-disabled': disabled, 'no-border': usePoints }" @click="disabled ? null : viewDetails" :aria-disabled="disabled">
    <!-- Product Image -->
    <div class="product-image" :style="imageHeight ? { height: typeof imageHeight === 'number' ? imageHeight + 'px' : imageHeight } : {}">
      <img :src="productImage" :alt="product.name" :style="imageHeight ? { height: '100%' } : {}" />
      
      <!-- Price Tag (only show in image when not using points) -->
      <div v-if="!usePoints" class="price-tag">
        {{ displayedPrice }}
      </div>

      <!-- Product Info Overlay -->
      <div class="product-info-overlay">
        <h4 class="product-name">{{ product.name }}</h4>
        <p v-if="product.description" class="product-description">
          {{ product.description.substring(0, 60) }}{{ product.description.length > 60 ? '...' : '' }}
        </p>
      </div>
    </div>

    <!-- Points badge (when using points) -->
    <div v-if="usePoints" :class="['points-centered', { 'points-has-bg': !disabled }]">
      <i class="bi bi-star-fill" style="margin-right:8px"></i>
      <span class="pts-text">{{ displayedPrice }}</span>
    </div>

    <!-- Add to Cart Button -->
    <div v-if="!hideAddToCart" class="product-footer">
      <button 
        class="btn btn-sm rounded-circle"
        :class="isInCart ? 'btn-success' : 'btn-warning'"
        style="width: 40px; height: 40px;"
        @click="addToCart"
        :disabled="isInCart || disabled"
      >
        <i :class="isInCart ? 'bi bi-check' : 'bi bi-cart-plus'"></i>
      </button>
    </div>

    <div v-if="disabled" class="disabled-overlay" aria-hidden="true"></div>
  </div>
</template>

<style scoped>
.product-card {
  background: var(--bg-card);
  border-radius: 0 0 12px 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  height: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
}

/* hover transform/box-shadow removed per request */

.product-image {
  position: relative;
  width: 100%;
  height: 180px;
  overflow: hidden;
  background: #1a1a1a;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

/* image scale on hover removed per request */

.price-tag {
  position: absolute;
  top: 0;
  right: 0;
  background: var(--primary-color);
  color: #1a1a1a;
  padding: 0.3rem 0.5rem;
  /* remove rounding on top corners */
  border-radius: 0 0 12px 12px;
  font-size: 0.85rem;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  z-index: 2;
}

.price-left {
  left: 0;
  right: auto;
  /* remove top rounding, keep bottom corners rounded */
  border-radius: 0 0 12px 12px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.points-centered {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin: 0 auto;
  background: transparent;
  color: var(--primary-color);
  padding: 0.15rem 0.25rem;
  border-radius: 0;
  font-weight: 700;
  box-shadow: none;
  width: fit-content;
  z-index: 2;
}

.pts-text { font-size: 0.95rem; }

.points-has-bg {
  background: var(--primary-color);
  color: #1a1a1a;
  padding: 0.35rem 0.9rem;
  /* remove top rounding on badge, keep bottom rounding */
  border-radius: 0 0 20px 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.product-info-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 1rem;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.6) 70%, transparent 100%);
  z-index: 1;
  transition: all 0.3s ease;
}


.product-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 0.5rem;
  line-height: 1.3;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
}

.product-description {
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 0;
  line-height: 1.4;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
}

.product-footer {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
  background: var(--bg-card);
}

.is-disabled {
  opacity: 0.65;
}

.is-disabled .product-footer button {
  pointer-events: none;
}

.disabled-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: rgba(0,0,0,0.45);
  color: #fff;
  font-weight: 700;
  z-index: 4;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  pointer-events: auto;
}

.btn-add-cart {
  background: var(--primary-color);
  color: #000;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  font-size: 1.25rem;
}


.btn-add-cart:disabled {
  background: var(--success-color);
  color: white;
  cursor: not-allowed;
  opacity: 0.8;
}

.no-border {
  border: none !important;
}

/* Tablet */
@media (min-width: 768px) {
  .product-image {
    height: 220px;
  }

  .product-name {
    font-size: 1rem;
  }
}
</style>
