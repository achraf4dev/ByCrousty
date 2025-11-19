<script setup>
/**
 * Top Navbar Component
 * Fixed navigation bar at the top with logo, menu, cart, and auth buttons
 */
import { computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';
import { useCartStore } from '../store/cart';
import { useUI } from '../store/ui';

const router = useRouter();
const { user, isAuthenticated } = useAuth();
const cartStore = useCartStore();
const { openSidebar } = useUI();

// Destructure itemCount from store to ensure reactivity
const { itemCount } = cartStore;

const cartItemCount = computed(() => itemCount.value);

const goToCart = () => {
  router.push('/cart');
};

const goToAuth = () => {
  if (isAuthenticated.value) {
    router.push('/profile');
  } else {
    router.push('/login');
  }
};

const goToHome = () => {
  router.push('/home');
};

// Ensure cart is loaded on mount
onMounted(() => {
  cartStore.loadCart();
});
</script>

<template>
  <nav class="top-navbar">
    <div class="navbar-content">
      <!-- Left: Toggle Button -->
      <div class="navbar-left">
        <button class="toggle-button" @click="openSidebar" aria-label="Menu">
          <i class="bi bi-list"></i>
        </button>
      </div>

      <!-- Center: Logo -->
      <div class="navbar-center">
        <div class="navbar-logo" @click="goToHome">
          <img src="/logo.png" alt="ByCrousty Logo" class="logo-image" />
        </div>
      </div>

      <!-- Right: Profile & Cart Icons -->
      <div class="navbar-right">
        <!-- User/Login Icon -->
        <button class="nav-button" @click="goToAuth" :aria-label="isAuthenticated ? 'User Profile' : 'Login'">
          <i :class="isAuthenticated ? 'bi bi-person-circle-fill' : 'bi bi-person-circle'"></i>
        </button>

        <!-- Cart Icon with Badge -->
        <button class="nav-button" @click="goToCart" aria-label="Shopping Cart">
          <i class="bi bi-cart3"></i>
          <span v-if="cartItemCount > 0" class="cart-badge">{{ cartItemCount }}</span>
        </button>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.top-navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: var(--bg-dark);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  z-index: 1000;
  padding-top: env(safe-area-inset-top, 0);
}

.navbar-content {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0.75rem 1rem;
  min-height: 70px;
  gap: 1rem;
}

/* Left Section - Toggle Button */
.navbar-left {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  height: 100%;
}

.toggle-button {
  background: transparent;
  border: none;
  color: var(--text-primary);
  cursor: pointer;
  width: 45px;
  height: 45px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.toggle-button:hover {
  background: rgba(255, 215, 0, 0.1);
  color: var(--primary-color);
  transform: scale(1.1);
}

.toggle-button i {
  font-size: 1.8rem;
  font-weight: 600;
}

/* Center Section - Logo */
.navbar-center {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

.navbar-logo {
  cursor: pointer;
  transition: transform 0.3s ease;
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.navbar-logo:hover {
  transform: scale(1.1) rotate(5deg);
}

.logo-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
  transition: all 0.3s ease;
}

.navbar-logo:hover .logo-image {
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
}

/* Right Section - Profile & Cart */
.navbar-right {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 0.5rem;
  height: 100%;
}

.nav-button {
  position: relative;
  background: transparent;
  border: none;
  color: var(--text-primary);
  cursor: pointer;
  width: 45px;
  height: 45px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.nav-button i {
  font-size: 1.5rem;
}

.nav-button:hover {
  background: rgba(255, 215, 0, 0.1);
  color: var(--primary-color);
  transform: scale(1.1);
}

.cart-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: var(--primary-color);
  color: #1a1a1a;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 700;
  border: 2px solid var(--bg-dark);
  box-shadow: 0 2px 8px rgba(252, 186, 27, 0.6);
  animation: pulse-alert 1.5s ease-in-out infinite;
}

@keyframes pulse-alert {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 2px 8px rgba(252, 186, 27, 0.6);
  }
  50% {
    transform: scale(1.15);
    box-shadow: 0 4px 16px rgba(252, 186, 27, 0.8);
  }
}

/* Tablet and Desktop */
@media (min-width: 768px) {
  .navbar-content {
    padding: 0.75rem 2rem;
  }

  .toggle-button,
  .nav-button {
    width: 50px;
    height: 50px;
  }

  .toggle-button i {
    font-size: 2rem;
  }

  .nav-button i {
    font-size: 1.6rem;
  }

  .navbar-logo {
    width: 60px;
    height: 60px;
  }

  .navbar-right {
    gap: 0.75rem;
  }
}

@media (min-width: 1200px) {
  .navbar-content {
    padding: 0.75rem 3rem;
  }
}

/* Loading state for logo */
.logo-image {
  background: var(--bg-card);
}

/* Fallback if logo doesn't load */
.logo-image[alt]::after {
  content: "BC";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--primary-color);
}
</style>
