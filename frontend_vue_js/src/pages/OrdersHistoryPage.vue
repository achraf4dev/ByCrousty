<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAppStore } from '../store/app';

const router = useRouter();
const appStore = useAppStore();

const ordersHistory = ref([]);
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

// Get status badge class
const getStatusBadge = (status) => {
  const badges = {
    pending: 'badge-warning',
    preparing: 'badge-info',
    ready: 'badge-success',
    completed: 'badge-secondary',
    cancelled: 'badge-danger'
  };
  return badges[status] || 'badge-secondary';
};

// Translate status
const translateStatus = (status) => {
  const translations = {
    pending: 'Pendiente',
    preparing: 'Preparando',
    ready: 'Listo',
    completed: 'Completado',
    cancelled: 'Cancelado'
  };
  return translations[status] || status;
};

// Load orders history
const loadOrdersHistory = async () => {
  loading.value = true;
  try {
    await appStore.fetchHistoryOrders(searchQuery.value);
    ordersHistory.value = appStore.getHistoryOrders.value || [];
  } catch (error) {
    console.error('Error loading orders history:', error);
  } finally {
    loading.value = false;
  }
};

// Handle search
const handleSearch = () => {
  loadOrdersHistory();
};

// Go back
const goBack = () => {
  router.back();
};

onMounted(() => {
  loadOrdersHistory();
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
        <h1 class="page-title">Historial de Pedidos</h1>
        <p class="page-subtitle">Revisa tus pedidos anteriores</p>
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
            placeholder="Buscar por número de pedido, nombre o teléfono..."
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
      <div v-else-if="ordersHistory.length === 0" class="empty-state">
        <i class="bi bi-receipt"></i>
        <p>No se encontraron pedidos</p>
      </div>

      <!-- History List -->
      <div v-else class="history-list">
        <div
          v-for="order in ordersHistory"
          :key="order.id"
          class="history-item"
        >
          <div class="item-icon">
            <i class="bi bi-receipt"></i>
          </div>
          <div class="item-content">
            <div class="item-header">
              <span class="order-number">{{ order.order_number }}</span>
              <span :class="['badge', getStatusBadge(order.status)]">
                {{ translateStatus(order.status) }}
              </span>
            </div>
            <div class="item-details">
              <div class="detail-row">
                <i class="bi bi-person me-1"></i>
                <span>{{ order.user?.full_name || 'Desconocido' }}</span>
              </div>
              <div class="detail-row">
                <i class="bi bi-calendar me-1"></i>
                <span>{{ formatDate(order.created_at) }}</span>
              </div>
              <div class="detail-row amount">
                <i class="bi bi-currency-euro me-1"></i>
                <span>{{ order.total_amount }}€</span>
              </div>
              <div v-if="order.points_used > 0" class="detail-row points">
                <i class="bi bi-coin me-1"></i>
                <span>{{ order.points_used }} pts usados</span>
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
  color: #50C878;
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
  background: rgba(80, 200, 120, 0.1);
  color: #50C878;
  border-radius: 10px;
  font-size: 1.5rem;
  flex-shrink: 0;
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
  gap: 1rem;
}

.order-number {
  font-weight: 600;
  font-size: 1rem;
  color: #ffffff;
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  flex-shrink: 0;
}

.badge-warning {
  background: #ffc107;
  color: #000;
}

.badge-info {
  background: #17a2b8;
  color: #fff;
}

.badge-success {
  background: #28a745;
  color: #fff;
}

.badge-secondary {
  background: #6c757d;
  color: #fff;
}

.badge-danger {
  background: #dc3545;
  color: #fff;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.detail-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: #b0b0b0;
}

.detail-row i {
  color: #666666;
  flex-shrink: 0;
}

.detail-row.amount {
  color: #ffffff;
  font-weight: 600;
}

.detail-row.points {
  color: #FFD700;
  font-weight: 600;
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

  .badge {
    align-self: flex-start;
  }
}
</style>
