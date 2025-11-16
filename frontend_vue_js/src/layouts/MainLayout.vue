<script setup>
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';
import Navbar from '../components/Navbar.vue';

const router = useRouter();
const { isAuthenticated } = useAuth();

// Handle navigation from Navbar
const handleNavigation = (item) => {
  router.push(item.route);
};
</script>

<template>
  <div class="main-layout">
    <main class="main-content">
      <slot />
    </main>

    <!-- Show Navbar only when authenticated -->
    <Navbar v-if="isAuthenticated()" @navigate="handleNavigation" />
  </div>
</template>

<style scoped>
.main-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

/* Prevent scroll bouncing on iOS */
@supports (-webkit-overflow-scrolling: touch) {
  .main-content {
    position: relative;
  }
}
</style>
