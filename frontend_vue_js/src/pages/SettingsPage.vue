<script setup>
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';
import PageHeader from '../components/PageHeader.vue';

const router = useRouter();
const { logout } = useAuth();

// Settings menu items
const menuItems = [
  {
    icon: 'bi-clock-history',
    title: 'Historial de Inicios de Sesión',
    description: 'Ver tu historial de accesos',
    route: '/settings/login-history',
    color: '#4A90E2'
  },
  {
    icon: 'bi-coin',
    title: 'Historial de Puntos',
    description: 'Consulta tus puntos acumulados',
    route: '/settings/points-history',
    color: '#FFD700'
  },
  {
    icon: 'bi-receipt',
    title: 'Historial de Pedidos',
    description: 'Revisa tus pedidos anteriores',
    route: '/settings/orders-history',
    color: '#50C878'
  }
];

// Navigate to section
const navigateTo = (route) => {
  router.push(route);
};

// Handle logout
const handleLogout = () => {
  logout();
  router.push('/login');
};
</script>

<template>
  <div class="settings-page">
    <PageHeader 
      title="Configuración"
      subtitle="Gestiona tu cuenta y preferencias"
      icon="gear"
    />

    <div class="settings-container">
      <!-- Settings Menu List -->
      <div class="menu-list">
        <div
          v-for="item in menuItems"
          :key="item.route"
          class="menu-item"
          @click="navigateTo(item.route)"
        >
          <div class="menu-icon" :style="{ color: item.color }">
            <i :class="['bi', item.icon]"></i>
          </div>
          <div class="menu-content">
            <h3 class="menu-title">{{ item.title }}</h3>
            <p class="menu-description">{{ item.description }}</p>
          </div>
          <div class="menu-arrow">
            <i class="bi bi-chevron-right"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Logout Button -->
    <div class="logout-section">
      <button class="btn-logout" @click="handleLogout">
        <i class="bi bi-box-arrow-right me-2"></i>
        Cerrar Sesión
      </button>
    </div>
  </div>
</template>

<style scoped>
.settings-page {
  min-height: 100vh;
  background: #242424;
  padding: 0.85rem 0;
  padding-bottom: calc(60px + 0.65rem);
  display: flex;
  flex-direction: column;
}

.settings-container {
  padding: 0;
  flex: 1;
}

.menu-list {
  display: flex;
  flex-direction: column;
  gap: 0;
  background: #1a1a1a;
  border-radius: 0;
  border: none;
  border-top: 2px solid #333333;
  border-bottom: 2px solid #333333;
  overflow: hidden;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: #1a1a1a;
  padding: 1rem;
  border-radius: 0;
  border: none;
  border-bottom: 1px solid #333333;
  cursor: pointer;
  transition: all 0.3s ease;
}

.menu-item:last-child {
  border-bottom: none;
}

.menu-item:hover {
  border-color: #FFD700;
  transform: translateX(5px);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.1);
}

.menu-item:active {
  transform: translateX(3px);
}

.menu-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.menu-content {
  flex: 1;
}

.menu-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 0.25rem;
}

.menu-description {
  margin: 0;
  font-size: 0.8rem;
  color: #b0b0b0;
}

.menu-arrow {
  color: #666666;
  font-size: 1.2rem;
  transition: all 0.3s ease;
}

.menu-item:hover .menu-arrow {
  color: #FFD700;
  transform: translateX(5px);
}

.logout-section {
  margin-top: auto;
  padding: 0;
  background: #1a1a1a;
  border: none;
  border-top: 2px solid #333333;
  border-bottom: 2px solid #333333;
  position: sticky;
  bottom: 60px;
}

.btn-logout {
  width: 100%;
  padding: 1rem;
  background: #1a1a1a;
  border: none;
  color: #dc3545;
  font-weight: 600;
  font-size: 1rem;
  border-radius: 0;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-logout:hover {
  background: #2a2a2a;
  color: #ff4d5a;
  transform: translateX(5px);
}

.btn-logout:active {
  transform: translateX(3px);
}

@media (max-width: 768px) {
  .menu-icon {
    width: 42px;
    height: 42px;
    font-size: 1.3rem;
  }

  .menu-title {
    font-size: 0.95rem;
  }

  .menu-description {
    font-size: 0.75rem;
  }
}
</style>