<script setup>
/**
 * Sidebar Drawer Component
 * Side menu with About Us, Contact, Find Us links
 */
import { useRouter } from 'vue-router';
import { useUI } from '../store/ui';

const router = useRouter();
const { sidebarOpen, closeSidebar } = useUI();

const menuItems = [
  { name: 'about', icon: 'bi-info-circle', label: 'Sobre Nosotros', route: '/about' },
  { name: 'contact', icon: 'bi-envelope', label: 'Contacto', route: '/contact' },
  { name: 'findus', icon: 'bi-geo-alt', label: 'Encuéntranos', route: '/find-us' },
];

const navigate = (route) => {
  router.push(route);
  closeSidebar();
};

const handleBackdropClick = () => {
  closeSidebar();
};
</script>

<template>
  <!-- Backdrop -->
  <div 
    v-if="sidebarOpen" 
    class="sidebar-backdrop"
    @click="handleBackdropClick"
  ></div>

  <!-- Sidebar -->
  <Transition name="slide">
    <div v-if="sidebarOpen" class="sidebar-drawer">
      <div class="sidebar-header">
        <h3>Menú</h3>
        <button class="close-button" @click="closeSidebar" aria-label="Close Menu">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <nav class="sidebar-menu">
        <button
          v-for="item in menuItems"
          :key="item.name"
          class="menu-item"
          @click="navigate(item.route)"
        >
          <i :class="['bi', item.icon, 'menu-icon']"></i>
          <span class="menu-label">{{ item.label }}</span>
          <i class="bi bi-chevron-right menu-arrow"></i>
        </button>
      </nav>

      <div class="sidebar-footer">
        <p>ByCrousty &copy; 2025</p>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.sidebar-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  z-index: 1001;
  backdrop-filter: blur(2px);
}

.sidebar-drawer {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  width: 80%;
  max-width: 320px;
  background: var(--bg-dark);
  border-right: 2px solid var(--border-color);
  box-shadow: 4px 0 20px rgba(0, 0, 0, 0.5);
  z-index: 1002;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 2px solid var(--border-color);
}

.sidebar-header h3 {
  color: var(--primary-color);
  margin: 0;
  font-size: 1.5rem;
}

.close-button {
  background: transparent;
  border: none;
  color: var(--text-primary);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.close-button i {
  font-size: 1.5rem;
}

.close-button:hover {
  background: var(--bg-card);
  color: var(--primary-color);
}

.sidebar-menu {
  flex: 1;
  padding: 1rem 0;
}

.menu-item {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  background: transparent;
  border: none;
  color: var(--text-primary);
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: left;
}

.menu-item:hover {
  background: var(--bg-card);
  color: var(--primary-color);
}

.menu-icon {
  font-size: 1.25rem;
}

.menu-label {
  flex: 1;
  font-size: 1rem;
  font-weight: 500;
}

.menu-arrow {
  font-size: 0.875rem;
  opacity: 0.5;
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 2px solid var(--border-color);
  text-align: center;
}

.sidebar-footer p {
  margin: 0;
  font-size: 0.875rem;
  color: var(--text-secondary);
}

/* Slide Transition */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from {
  transform: translateX(-100%);
}

.slide-leave-to {
  transform: translateX(-100%);
}

/* Tablet and Desktop */
@media (min-width: 768px) {
  .sidebar-drawer {
    max-width: 400px;
  }
}
</style>
