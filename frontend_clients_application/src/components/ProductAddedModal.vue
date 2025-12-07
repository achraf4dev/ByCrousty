<template>
  <div v-if="visible" class="modal-backdrop">
    <div class="modal-card">
      <div style="position: relative;">
        <button class="close-btn" @click="$emit('continue')" aria-label="Cerrar">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="modal-title-row modal-title-top">
        <i class="bi bi-cart-plus-fill primary-icon"></i>
        <h3 class="modal-title">¡Producto añadido al carrito!</h3>
      </div>
      <div class="modal-main">
        <div class="modal-image-section">
          <img :src="product.image_url || product.image || 'https://placehold.co/300x200/1a1a1a/FFD700?text=No+Image'" :alt="product.name" class="modal-product-image" />
        </div>
        <div class="modal-info-section">
          <h4 class="product-name">{{ product.name }}</h4>
          <p v-if="product.description" class="product-description">{{ product.description }}</p>
          <p v-if="product.price" class="product-price"><strong>Precio:</strong> {{ Number(product.price).toFixed(2) }}€</p>
          <div v-if="quantity" class="product-quantity"><strong>Cantidad:</strong> {{ quantity }}</div>
        </div>
      </div>
      <div class="modal-actions">
        <button class="btn btn-secondary" @click="$emit('continue')">Seguir comprando</button>
        <button class="btn btn-primary" @click="$emit('goToCart')">Ir al carrito</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  visible: Boolean,
  product: Object,
  quantity: Number
});
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-card {
  background: #2f2f2f;
  border-radius: 24px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.28);
  padding: 2rem 1rem;
  max-width: 440px;
  width: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}
.close-btn {
  position: absolute;
  top: -2rem;
  right: -1rem;
  border: none;
  font-size: 1.5rem;
  color: var(--primary-color);
  background-color: #2f2f2f;
  cursor: pointer;
  z-index: 10;
  padding: 0.3rem 0.6rem;
  border-radius: 50%;
  transition: box-shadow 0.2s, background 0.2s;
}
.modal-main {
  display: flex;
  flex-direction: row;
  gap: 1.5rem;
  align-items: flex-start;
}
.modal-image-section {
  flex: 0 0 110px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-product-image {
  width: 110px;
  height: 110px;
  object-fit: cover;
  border-radius: 18px;
  box-shadow: 0 2px 8px rgba(255,215,0,0.10);
  border: 2px solid #ffd70022;
}
.modal-info-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  align-items: flex-start;
  justify-content: flex-start;
}
.modal-title-row {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  margin-bottom: 0.7rem;
}
.modal-title-top {
  justify-content: center;
  width: 100%;
  margin-bottom: 1.2rem;
}
.primary-icon {
  font-size: 1.5rem;
  color: var(--primary-color);
  background: transparent;
  border-radius: 50%;
  padding: 0.1rem;
  box-shadow: none;
  border: none;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-title {
  color: var(--primary-color);
  font-weight: 700;
  font-size: 1.15rem;
  margin-bottom: 0;
}
.product-name {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.2rem;
  color: #fff;
}
.product-description {
  font-size: 0.98rem;
  color: #b0b3b8;
  margin-bottom: 0.2rem;
}
.product-price {
  font-size: 1rem;
  color: var(--primary-color);
  margin-bottom: 0.2rem;
}
.product-quantity {
  font-size: 1rem;
  color: var(--primary-color);
  background: #23272f;
  border-radius: 8px;
  padding: 0.2rem 0.7rem;
  margin-top: 0.5rem;
  font-weight: 500;
  border: 1px solid #ffd70044;
}
.modal-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 1.2rem;
}
.btn {
  padding: 0.6rem 1.2rem;
  border-radius: 50px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  border: none;
  transition: background 0.2s;
}
.btn-secondary {
  background: #2c3036;
  color: var(--primary-color);
}
.btn-primary {
  background: var(--primary-color);
  color: #23272f;
}
.btn-primary:hover {
  background: #fffbe6;
  color: #23272f;
}
</style>
