<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAppStore } from '../store/app';

const router = useRouter();
const appStore = useAppStore();

const pointsHistory = ref([]);
const loading = ref(false);
const searchQuery = ref('');

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

// Load points history
const loadPointsHistory = async () => {
  loading.value = true;
  try {
    await appStore.fetchHistoryPoints(searchQuery.value);
    pointsHistory.value = appStore.getHistoryPoints.value || [];
  } catch (error) {
    console.error('Error loading points history:', error);
  } finally {
    loading.value = false;
  }
};

// Handle search
const handleSearch = () => {
  loadPointsHistory();
};

// Go back
const goBack = () => {
  router.back();
};

onMounted(() => {
  loadPointsHistory();
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
        <h1 class="page-title">Historial de Puntos</h1>
        <p class="page-subtitle">Consulta tus puntos acumulados</p>
      </div>
    </div>

    <!-- Content -->
    <div class="page-content">
      <!-- Search -->
      <div class="search-box">
        <div class="input-group">
          <input
            v-model="searchQuery"
            type="text"
            class="form-control"
            placeholder="Buscar por nombre o teléfono..."
            @keyup.enter="handleSearch"
          />
          <button class="btn-search" @click="handleSearch">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-spinner">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="pointsHistory.length === 0" class="empty-state">
        <i class="bi bi-coin"></i>
        <p>No se encontró historial de puntos</p>
      </div>

      <!-- History List -->
      <div v-else class="history-list">
        <div
          v-for="item in pointsHistory"
          :key="item.id"
          class="history-item"
        >
          <p class="item-date">{{ formatDate(item.created_at) }}</p>
          <div class="item-info">
            <h4 class="item-user">{{ item.user?.full_name || 'Usuario Desconocido' }}</h4>
            <p class="item-admin">
              <i class="bi bi-person-badge me-1"></i>
              {{ item.admin?.full_name || 'Admin' }}
            </p>
            <p v-if="item.remark" class="item-remark">
              <i class="bi bi-chat-left-text me-1"></i>
              {{ item.remark }}
            </p>
          </div>
          <div class="item-meta">
            <span class="item-points">+{{ item.points_added }} pts</span>
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
  padding: 1rem 0.65rem;
}

.search-box {
  margin-bottom: 0.65rem;
}

.input-group {
  display: flex;
  gap: 0;
}

.form-control {
  flex: 1;
  padding: 0.75rem 1rem;
  background: #1a1a1a;
  border: 2px solid #333333;
  border-right: none;
  color: #ffffff;
  border-radius: 10px 0 0 10px;
  font-size: 0.9rem;
  height: 50px;
}

.form-control:focus {
  outline: none;
  border-color: #FFD700;
  background: #2a2a2a;
}

.form-control::placeholder {
  color: #666666;
}

.btn-search {
  width: 50px;
  height: 50px;
  padding: 0;
  background: #FFD700;
  border: 2px solid #333333;
  border-left: 2px solid #FFD700;
  color: #0a0a0a;
  border-radius: 0 10px 10px 0;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-search:hover {
  background: #ffd700ee;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 3rem;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: #666666;
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  display: block;
  color: #FFD700;
}

.history-list {
  display: flex;
  flex-direction: column;
  gap: 0;
  background: #1a1a1a;
  border-radius: 0;
  border-top: 2px solid #333333;
  border-bottom: 2px solid #333333;
  overflow: hidden;
}

.history-item {
  display: grid;
  grid-template-columns: 1fr auto;
  grid-template-rows: auto auto;
  gap: 0 1rem;
  background: #1a1a1a;
  padding: 0.9rem;
  border-radius: 0;
  border: none;
  border-bottom: 1px solid #333333;
}

.history-item:last-child {
  border-bottom: none;
}

.item-date {
  grid-column: 2;
  grid-row: 1;
  font-size: 0.65rem;
  margin: 0;
  text-align: right;
  align-self: start;
  color: #b0b0b0;
}

.item-info {
  grid-column: 1;
  grid-row: 1 / 3;
}

.item-user {
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 0.2rem;
  color: #ffffff;
}

.item-admin {
  font-size: 0.75rem;
  margin: 0.1rem 0;
  color: #b0b0b0;
}

.item-remark {
  font-size: 0.75rem;
  margin: 0.4rem 0 0 0;
  color: #999999;
  font-style: italic;
  padding-top: 0.4rem;
  border-top: 1px solid #333333;
}

.item-meta {
  grid-column: 2;
  grid-row: 2;
  display: flex;
  align-items: flex-end;
  justify-content: flex-end;
}

.item-points {
  font-size: 1rem;
  font-weight: 700;
  color: #FFD700;
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

  .item-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .item-points {
    align-self: flex-start;
  }
}
</style>
