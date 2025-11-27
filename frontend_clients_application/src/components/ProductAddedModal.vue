<template>
  <div v-if="visible" class="modal-backdrop">
    <div class="modal-content">
      <div class="modal-header primary">
        <button class="close-btn" @click="$emit('continue')" aria-label="Cerrar">
          <i class="bi bi-x-lg"></i>
        </button>
        <div class="title-row">
          <i class="bi bi-cart-plus-fill primary-icon"></i>
          <h3 class="modal-title">¡Producto añadido al carrito!</h3>
        </div>
      </div>
      <div class="modal-body">
        <div class="product-details column">
          <img :src="product.image_url || product.image || 'https://placehold.co/300x200/1a1a1a/FFD700?text=No+Image'" :alt="product.name" class="product-image full-width" />
          <div class="product-info">
            <h4>{{ product.name }}</h4>
            <p v-if="product.description">{{ product.description }}</p>
            <p v-if="product.price"><strong>Precio:</strong> {{ Number(product.price).toFixed(2) }}€</p>
            <p v-if="quantity"><strong>Cantidad:</strong> {{ quantity }}</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
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
.modal-content {
  background: var(--bg-card);
  border-radius: 20px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.18);
  padding: 2.2rem 2.7rem;
  max-width: 420px;
  width: 100%;
  text-align: center;
}
.modal-header.primary {
  color: var(--primary-color);
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}
.primary-icon {
  font-size: 1.5rem;
  color: var(--primary-color);
  margin-bottom: 0;
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
.close-btn {
  position: absolute;
  top: -2rem;
  right: -2rem;
  background: transparent;
  border: none;
  font-size: 1.5rem;
  color: var(--primary-color);
  cursor: pointer;
  z-index: 10;
  padding: 0.3rem 0.6rem;
  border-radius: 50%;
  box-shadow: none;
  transition: box-shadow 0.2s, background 0.2s;
}
.close-btn:hover {
  background: var(--primary-color);
  color: #fff;
  box-shadow: none;
}
.title-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.7rem;
  width: 100%;
  margin-top: 2.2rem;
}
.modal-title {
  color: var(--primary-color);
  font-weight: 700;
  margin-bottom: 0;
  font-size: 1.15rem;
}
.product-details {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin-bottom: 1.2rem;
  justify-content: center;
}
.product-details.column {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.2rem;
  margin-bottom: 1.2rem;
  justify-content: center;
}
.product-image {
  width: 90px;
  height: 90px;
  object-fit: cover;
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(255,215,0,0.10);
}
.product-image.large {
  width: 140px;
  height: 140px;
}
.product-image.full-width {
  width: 100%;
  max-width: 320px;
  height: auto;
  object-fit: cover;
  border-radius: 16px;
}
.product-info {
  text-align: center;
  width: 100%;
  margin-top: 0.7rem;
}
.modal-footer {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 1.5rem;
  border-top: 1px solid var(--primary-color);
  padding-top: 1.2rem;
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
  background: var(--bg-darker);
  color: var(--text-primary);
}
.btn-primary {
  background: var(--primary-color);
  color: #1a1a1a;
}
.btn-primary:hover {
  background: #ffd700;
}
</style>
