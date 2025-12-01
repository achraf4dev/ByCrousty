<script setup>
/**
 * Bottom Navbar Component
 * Fixed bottom navigation for authenticated users
 */
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

// Navigation items
const navItems = [
  { name: 'home', icon: 'bi-house', label: 'Inicio', route: '/home' },
  { name: 'categories', icon: 'bi-list', label: 'Carta', route: '/menu' },
  { name: 'offers', icon: 'bi-star-fill', label: 'Ofertas', route: '/offer' },
  // Navigate directly to the account child to avoid empty-path redirect issues
  { name: 'profile', icon: 'bi-person', label: 'Perfil', route: '/profile' },
];

// Check if current route is active
const isActive = (routePath) => {
  if (!routePath) return false;
  // Exact match or any nested route under the routePath (e.g. /profile/account)
  return route.path === routePath || route.path.startsWith(routePath + '/') || route.path.startsWith(routePath);
};

// Handle navigation (robust)
const handleNavigation = async (item) => {
  try {
    if (!item) {
      console.debug('BottomNavbar: invalid nav item', item);
      return;
    }

    // Log click + auth token status for debugging
    const token = localStorage.getItem('auth_token');
    // navigation click

    if (!item.route) {
      console.debug('BottomNavbar: no route defined for', item);
      return;
    }

    // Support both string paths and route objects
    if (typeof item.route === 'string') {
      await router.push({ path: item.route });
    } else {
      await router.push(item.route);
    }

    // navigation completed
  } catch (err) {
    // ignore duplicate navigation errors, log others
    console.debug('BottomNavbar navigation error', err);
  }
};
</script>

<template>
  <nav class="bottom-navbar">
    <div class="navbar-container">
      <button
        v-for="item in navItems"
        :key="item.name"
        type="button"
        :class="['nav-item', { active: isActive(item.route) }]"
        @click.prevent="handleNavigation(item)"
      >
        <i :class="['bi', item.icon, 'nav-icon']"></i>
        <span class="nav-label">{{ item.label }}</span>
      </button>
    </div>
  </nav>
</template>

<style scoped>
.bottom-navbar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: var(--bg-dark);
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.5);
  z-index: 1000;
  padding-bottom: env(safe-area-inset-bottom, 0);
}

.navbar-container {
  display: flex;
  justify-content: space-around;
  align-items: center;
  height: 60px;
  max-width: 100%;
  margin: 0 auto;
}

.nav-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0.35rem;
  transition: all 0.3s ease;
  color: var(--text-secondary);
}

.nav-icon {
  font-size: 1.25rem;
  margin-bottom: 0.15rem;
  transition: all 0.3s ease;
}

.nav-label {
  font-size: 0.65rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.nav-item:hover {
  color: var(--primary-color);
}

.nav-item.active {
  color: var(--primary-color);
}

.nav-item.active .nav-icon {
  transform: scale(1.1);
}

/* Tablet and Desktop */
@media (min-width: 768px) {
  .navbar-container {
    max-width: 600px;
  }

  .nav-icon {
    font-size: 1.5rem;
  }

  .nav-label {
    font-size: 0.75rem;
  }
}
</style>
