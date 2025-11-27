<script setup>
/**
 * Main Layout Component
 * Provides the main structure with top navbar, bottom navbar, and sidebar
 */
import { watch } from 'vue';
import { useRoute } from 'vue-router';
import { useAuth } from '../store/auth';
import TopNavbar from '../components/TopNavbar.vue';
import BottomNavbar from '../components/BottomNavbar.vue';
import SidebarDrawer from '../components/SidebarDrawer.vue';

const route = useRoute();
const { isAuthenticated } = useAuth();

// Routes that should hide the bottom navbar
const hideBottomNavbarRoutes = ['/login', '/register', '/welcome'];

// Routes that should hide the top navbar
const hideTopNavbarRoutes = ['/welcome'];

const shouldShowBottomNavbar = () => {
  return isAuthenticated.value && !hideBottomNavbarRoutes.includes(route.path);
};

const shouldShowTopNavbar = () => {
  return !hideTopNavbarRoutes.includes(route.path);
};

// Watch for route changes to handle layout adjustments
watch(() => route.path, () => {
  // Scroll to top on route change
  window.scrollTo(0, 0);
});
</script>

<template>
  <div class="main-layout">
    <!-- Top Navbar (Hidden on welcome page) -->
    <TopNavbar v-if="shouldShowTopNavbar()" />

      <!-- Sidebar Drawer removed -->

    <!-- Main Content Area -->
    <main class="main-content" :class="{ 
      'with-bottom-nav': shouldShowBottomNavbar(),
      'no-top-nav': !shouldShowTopNavbar()
    }">
      <slot />
    </main>

    <!-- Bottom Navbar (Only when authenticated) -->
    <BottomNavbar v-if="shouldShowBottomNavbar()" />
  </div>
</template>

<style scoped>
.main-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #242424;
}

.main-content {
  flex: 1;
  margin-top: var(--navbar-height); /* Height of top navbar */
  padding: 0;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  min-height: calc(100vh - var(--navbar-height));
}

.main-content.no-top-nav {
  margin-top: 0;
  min-height: 100vh;
}

.main-content.with-bottom-nav {
  margin-bottom: var(--navbar-height); /* Height of bottom navbar */
  min-height: calc(100vh - calc(var(--navbar-height) * 2)); /* Top + Bottom navbar */
  padding-bottom: 0;
}

/* Prevent scroll bouncing on iOS */
@supports (-webkit-overflow-scrolling: touch) {
  .main-content {
    position: relative;
  }
}

/* Container within main content */
:deep(.container) {
  max-width: 1200px;
  margin: 0 auto;
}

/* Tablet and Desktop */
@media (min-width: 768px) {
  .main-content {
    padding: 0;
    font-size: 14px;
  }
}

/* Large Desktop */
@media (min-width: 1200px) {
  .main-content {
    padding: 0;
  }
}
</style>
