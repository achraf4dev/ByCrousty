<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';
import * as api from '../services/api';

const router = useRouter();
const auth = useAuth();
const loginHistory = ref([]);
const loading = ref(false);
const filter = ref('all'); // 'all', 'success', 'failed'

// Filtered login history
const filteredHistory = computed(() => {
  if (filter.value === 'all') {
    return loginHistory.value;
  } else if (filter.value === 'success') {
    return loginHistory.value.filter(log => log.success);
  } else if (filter.value === 'failed') {
    return loginHistory.value.filter(log => !log.success);
  }
  return loginHistory.value;
});

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Load login history
const loadLoginHistory = async () => {
  loading.value = true;
  try {
    // Load user if not available
    if (!auth.user.value) {
      await auth.loadUser();
    }
    
    const userId = auth.user.value?.id;
    
    if (userId) {
      const response = await api.getLoginLogs(userId);
      loginHistory.value = response.logs || [];
    }
  } catch (error) {
    console.error('Error loading login history:', error);
  } finally {
    loading.value = false;
  }
};

// Go back
const goBack = () => {
  router.back();
};

onMounted(() => {
  loadLoginHistory();
});
</script>

<template>
  <div class="history-page">
    <!-- Header with Back Button -->
    <div class="page-header">
      <button class="btn-back" @click="goBack">
        <i class="bi bi-arrow-left"></i>
      </button>
      <div class="header-content">
        <h1 class="page-title">Historial de Inicios de Sesión</h1>
        <p class="page-subtitle">Revisa tus accesos recientes</p>
      </div>
    </div>

    <!-- Content -->
    <div class="page-content">
      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <button
          :class="['filter-btn', { active: filter === 'all' }]"
          @click="filter = 'all'"
        >
          Todos
        </button>
        <button
          :class="['filter-btn', { active: filter === 'success' }]"
          @click="filter = 'success'"
        >
          Exitosos
        </button>
        <button
          :class="['filter-btn', { active: filter === 'failed' }]"
          @click="filter = 'failed'"
        >
          Fallidos
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-spinner">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredHistory.length === 0" class="empty-state">
        <i class="bi bi-clock-history"></i>
        <p>No hay historial de inicios de sesión</p>
      </div>

      <!-- History List -->
      <div v-else class="history-list">
        <div
          v-for="log in filteredHistory"
          :key="log.id"
          class="history-item"
        >
          <div class="item-icon" :class="log.success ? 'success' : 'failed'">
            <i :class="log.success ? 'bi bi-check-circle-fill' : 'bi bi-x-circle-fill'"></i>
          </div>
          <div class="item-content">
            <div class="item-header">
              <span class="item-status">{{ log.success ? 'Exitoso' : 'Fallido' }}</span>
              <span class="item-date">{{ formatDate(log.created_at) }}</span>
            </div>
            <div class="item-details">
              <div class="detail-row">
                <i class="bi bi-geo-alt me-1"></i>
                <span>{{ log.ip_address }}</span>
              </div>
              <div v-if="log.user_agent" class="detail-row device">
                <i class="bi bi-device-ssd me-1"></i>
                <span>{{ log.user_agent }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.history-page {
  min-height: 100vh;
  background: #242424;
  padding-bottom: calc(60px + 0.65rem);
}

.page-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 0.85rem;
  background: #1a1a1a;
  border-bottom: 2px solid #333333;
  position: sticky;
  top: 0;
  z-index: 10;
}

.btn-back {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #2a2a2a;
  border: 2px solid #333333;
  color: #FFD700;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1.2rem;
  flex-shrink: 0;
}

.btn-back:hover {
  background: #FFD700;
  color: #0a0a0a;
  transform: translateX(-3px);
}

.header-content {
  flex: 1;
  animation: slideInFromLeft 0.4s ease-out;
  text-align: left;
}

@keyframes slideInFromLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.page-title {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 0.25rem;
  text-align: left;
}

.page-subtitle {
  margin: 0;
  font-size: 0.85rem;
  color: #b0b0b0;
  text-align: left;
}

.page-content {
  padding: 0;
}

.filter-tabs {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 0;
  background: #1a1a1a;
  padding: 0.5rem;
  border-radius: 0;
  border: none;
  border-top: 2px solid #333333;
  border-bottom: 2px solid #333333;
}

.filter-btn {
  flex: 1;
  padding: 0.65rem 1rem;
  background: transparent;
  border: none;
  color: #b0b0b0;
  font-weight: 600;
  font-size: 0.85rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-btn:hover {
  background: #2a2a2a;
  color: #FFD700;
}

.filter-btn.active {
  background: #FFD700;
  color: #0a0a0a;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 3rem;
  background: #1a1a1a;
  border-bottom: 2px solid #333333;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: #666666;
  background: #1a1a1a;
  border-bottom: 2px solid #333333;
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  display: block;
}

.history-list {
  display: flex;
  flex-direction: column;
  gap: 0;
  background: #1a1a1a;
  border-radius: 0;
  border: none;
  border-bottom: 2px solid #333333;
  overflow: hidden;
}

.history-item {
  display: flex;
  gap: 1rem;
  background: #1a1a1a;
  padding: 1rem;
  border-radius: 0;
  border: none;
  border-bottom: 1px solid #333333;
}

.history-item:last-child {
  border-bottom: none;
}

.item-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.item-icon.success {
  background: rgba(40, 167, 69, 0.1);
  color: #28a745;
}

.item-icon.failed {
  background: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}

.item-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.item-status {
  font-weight: 600;
  font-size: 1rem;
  color: #ffffff;
}

.item-date {
  font-size: 0.75rem;
  color: #b0b0b0;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.detail-row {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: #b0b0b0;
}

.detail-row i {
  color: #666666;
  margin-top: 0.15rem;
  flex-shrink: 0;
}

.detail-row.device span {
  word-break: break-word;
  line-height: 1.4;
}

@media (max-width: 768px) {
  .page-title {
    font-size: 1.1rem;
  }

  .page-subtitle {
    font-size: 0.75rem;
  }

  .btn-back {
    width: 38px;
    height: 38px;
    font-size: 1rem;
  }
}
</style>
