<script setup>
import { ref, onMounted } from 'vue';
import { useAppStore } from '../store/app';
import PageHeader from '../components/PageHeader.vue';

const appStore = useAppStore();

// Active tab
const activeTab = ref('points');

// Search inputs
const pointsSearch = ref('');
const ordersSearch = ref('');

// Local state
const pointsHistory = ref([]);
const ordersHistory = ref([]);
const loading = ref(false);

// Fetch points history
const loadPointsHistory = async () => {
  loading.value = true;
  try {
    await appStore.fetchHistoryPoints(pointsSearch.value);
    pointsHistory.value = appStore.getHistoryPoints.value;
  } catch (error) {
    console.error('Error loading points history:', error);
  } finally {
    loading.value = false;
  }
};

// Fetch orders history
const loadOrdersHistory = async () => {
  loading.value = true;
  try {
    await appStore.fetchHistoryOrders(ordersSearch.value);
    ordersHistory.value = appStore.getHistoryOrders.value;
  } catch (error) {
    console.error('Error loading orders history:', error);
  } finally {
    loading.value = false;
  }
};

// Handle search for points
const handlePointsSearch = () => {
  loadPointsHistory();
};

// Handle search for orders
const handleOrdersSearch = () => {
  loadOrdersHistory();
};

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
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

onMounted(() => {
  loadPointsHistory();
});
</script>

<template>
  <div class="settings-page">
    <PageHeader 
      title="Configuración"
      subtitle="Gestiona puntos e historial de pedidos"
      icon="gear"
    />

    <div class="settings-container">
      <!-- Tabs Navigation -->
      <div class="tabs-nav">
        <button
          :class="['tab-btn', { active: activeTab === 'points' }]"
          @click="activeTab = 'points'; loadPointsHistory()"
        >
          <i class="bi bi-coin me-2"></i>
          Historial de Puntos
        </button>
        <button
          :class="['tab-btn', { active: activeTab === 'orders' }]"
          @click="activeTab = 'orders'; loadOrdersHistory()"
        >
          <i class="bi bi-receipt me-2"></i>
          Historial de Pedidos
        </button>
      </div>

      <!-- Points History Tab -->
      <div v-if="activeTab === 'points'" class="tab-content">
        <!-- Search -->
        <div class="search-box">
          <div class="input-group">
            <input
              v-model="pointsSearch"
              type="text"
              class="form-control"
              placeholder="Buscar por nombre o teléfono..."
              @keyup.enter="handlePointsSearch"
            />
            <button class="btn btn-search" @click="handlePointsSearch">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </div>

        <!-- Points History List -->
        <div v-if="loading" class="loading-spinner">
          <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>

        <div v-else-if="pointsHistory.length === 0" class="empty-state">
          <i class="bi bi-inbox"></i>
          <p>No se encontró historial de puntos</p>
        </div>

        <div v-else class="history-list">
          <div
            v-for="item in pointsHistory"
            :key="item.id"
            class="history-item"
          >
            <p class="item-date">{{ formatDate(item.created_at) }}</p>
            <div class="item-details">
              <div class="item-header">
                <span class="item-user">{{ item.user?.full_name || 'Usuario Desconocido' }}</span>
              </div>
              <div class="item-meta">
                <span class="item-admin">
                  <i class="bi bi-person-badge me-1"></i>
                  {{ item.admin?.full_name || 'Admin' }}
                </span>
              </div>
              <div v-if="item.remark" class="item-remark">
                <i class="bi bi-chat-left-text me-1"></i>
                {{ item.remark }}
              </div>
            </div>
            <div class="item-points-container">
              <span class="item-points">+{{ item.points_added }} pts</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders History Tab -->
      <div v-if="activeTab === 'orders'" class="tab-content">
        <!-- Search -->
        <div class="search-box">
          <div class="input-group">
            <input
              v-model="ordersSearch"
              type="text"
              class="form-control"
              placeholder="Buscar por número de pedido, nombre o teléfono..."
              @keyup.enter="handleOrdersSearch"
            />
            <button class="btn btn-search" @click="handleOrdersSearch">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </div>

        <!-- Orders History List -->
        <div v-if="loading" class="loading-spinner">
          <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>

        <div v-else-if="ordersHistory.length === 0" class="empty-state">
          <i class="bi bi-inbox"></i>
          <p>No se encontraron pedidos</p>
        </div>

        <div v-else class="history-list">
          <div
            v-for="order in ordersHistory"
            :key="order.id"
            class="history-item order-item"
          >
            <div class="item-details">
              <p class="item-date">{{ formatDate(order.created_at) }}</p>
              <div class="item-header">
                <span class="order-number">{{ order.order_number }}</span>
                <span :class="['badge', getStatusBadge(order.status)]">
                  {{ translateStatus(order.status) }}
                </span>
              </div>
              <div class="item-meta">
                <span class="item-user">
                  <i class="bi bi-person me-1"></i>
                  {{ order.user?.full_name || 'Desconocido' }}
                </span>
              </div>
              <div class="item-meta">
                <span class="item-amount">
                  {{ order.total_amount }}€
                </span>
                <span v-if="order.points_used > 0" class="item-points-used">
                  <i class="bi bi-coin me-1"></i>
                  {{ order.points_used }} pts usados
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.settings-page {
  min-height: 100vh;
  background: #242424;
  padding: 0.85rem 0.65rem;
  padding-bottom: calc(60px + 0.65rem);
}

.settings-container {
  padding: 0;
}

.tabs-nav {
  display: flex;
  gap: 0.45rem;
  margin-bottom: 1.2rem;
  background: #1a1a1a;
  padding: 0.45rem;
  border-radius: 10px;
}

.tab-btn {
  flex: 1;
  padding: 0.65rem 0.85rem;
  background: transparent;
  border: none;
  color: #b0b0b0;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tab-btn:hover {
  background: #2a2a2a;
  color: #FFD700;
}

.tab-btn.active {
  background: #FFD700;
  color: #0a0a0a;
}

.tab-content {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.search-box {
  margin-bottom: 1.2rem;
}

.input-group {
  display: flex;
  gap: 0.45rem;
}

.form-control {
  flex: 1;
  padding: 0.65rem 0.85rem;
  background: #1a1a1a;
  border: 2px solid #333333;
  color: #ffffff;
  border-radius: 8px;
  font-size: 0.85rem;
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
  padding: 0.65rem 1.2rem;
  background: #FFD700;
  border: none;
  color: #0a0a0a;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.btn-search:hover {
  background: #ffd700ee;
  transform: scale(1.05);
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2.5rem;
}

.empty-state {
  text-align: center;
  padding: 2.5rem 0.85rem;
  color: #666666;
}

.empty-state i {
  font-size: 2.5rem;
  margin-bottom: 0.85rem;
  display: block;
}

.history-list {
  display: flex;
  flex-direction: column;
  gap: 0;
  border: 1px solid #333333;
  border-radius: 0.5rem;
  overflow: hidden;
  background: #1a1a1a;
}

.history-item {
  display: grid;
  grid-template-columns: 1fr auto;
  grid-template-rows: auto 1fr;
  gap: 0 0.85rem;
  background: #1a1a1a;
  padding: 0.85rem;
  border-radius: 0;
  border: none;
  border-bottom: 1px solid #333333;
  transition: all 0.3s ease;
}

.history-item:last-child {
  border-bottom: none;
}

.history-item:hover {
  border-color: #FFD700;
  transform: translateX(5px);
}

.order-item {
  grid-template-columns: 1fr auto;
}

.order-item .item-details {
  grid-column: 1;
}

.order-item .item-date {
  grid-column: 2;
}

.item-date {
  grid-column: 2;
  grid-row: 1;
  font-size: 0.65rem;
  color: #b0b0b0;
  margin: 0;
  text-align: right;
  align-self: start;
  justify-self: end;
}

.item-details {
  grid-column: 1;
  grid-row: 1 / 3;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.item-points-container {
  grid-column: 2;
  grid-row: 2;
  display: flex;
  align-items: flex-end;
  justify-content: flex-end;
  align-self: end;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.item-user,
.order-number {
  font-weight: 600;
  color: #ffffff;
  font-size: 0.9rem;
}

.item-points {
  font-weight: 700;
  color: #FFD700;
  font-size: 0.9rem;
}

.item-meta {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: #b0b0b0;
}

.item-admin,
.item-amount,
.item-points-used {
  display: flex;
  align-items: center;
}

.item-remark {
  font-size: 0.75rem;
  color: #999999;
  font-style: italic;
  padding-top: 0.4rem;
  margin-top: 0.4rem;
  border-top: 1px solid #333333;
}

.badge {
  padding: 0.2rem 0.65rem;
  border-radius: 5px;
  font-size: 0.65rem;
  font-weight: 600;
  text-transform: uppercase;
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

@media (max-width: 768px) {
  .page-title {
    font-size: 1.5rem;
  }

  .tab-btn {
    font-size: 0.85rem;
    padding: 0.6rem 0.5rem;
  }

  .item-header,
  .item-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }

  .item-points,
  .badge {
    align-self: flex-start;
  }
}
</style>
