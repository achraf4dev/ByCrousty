<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useAppStore } from '../store/app';
import PageHeader from '../components/PageHeader.vue';

const appStore = useAppStore();

// Component state
const loading = ref(true);
const orders = ref([]);
const pollInterval = ref(null);

// Fetch orders
const loadOrders = async () => {
  try {
    await appStore.fetchOrders();
    orders.value = appStore.getOrders.value;
  } catch (error) {
    console.error('Error loading orders:', error);
  } finally {
    loading.value = false;
  }
};

// Update order status
const updateStatus = async (orderId, newStatus) => {
  try {
    await appStore.updateOrderStatus(orderId, newStatus);
    // The store will update the local orders array
    orders.value = appStore.getOrders.value;
  } catch (error) {
    console.error('Error updating order status:', error);
    alert('Error al actualizar el estado del pedido. Por favor, inténtalo de nuevo.');
  }
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

// Get status icon
const getStatusIcon = (status) => {
  const icons = {
    pending: 'bi-clock-fill',
    preparing: 'bi-arrow-repeat',
    ready: 'bi-check-circle-fill',
    completed: 'bi-check-all',
    cancelled: 'bi-x-circle-fill'
  };
  return icons[status] || 'bi-receipt';
};

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInMinutes = Math.floor((now - date) / 60000);

  if (diffInMinutes < 1) return 'Justo ahora';
  if (diffInMinutes < 60) return `hace ${diffInMinutes} min`;
  if (diffInMinutes < 1440) return `hace ${Math.floor(diffInMinutes / 60)} horas`;
  
  return date.toLocaleDateString('es-ES', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Get available status transitions
const getAvailableStatuses = (currentStatus) => {
  const statusFlow = {
    pending: ['preparing', 'cancelled'],
    preparing: ['ready', 'cancelled'],
    ready: ['completed', 'cancelled'],
    completed: [],
    cancelled: []
  };
  return statusFlow[currentStatus] || [];
};

// Start polling
const startPolling = () => {
  pollInterval.value = setInterval(() => {
    loadOrders();
  }, 10000); // Poll every 10 seconds
};

// Stop polling
const stopPolling = () => {
  if (pollInterval.value) {
    clearInterval(pollInterval.value);
    pollInterval.value = null;
  }
};

onMounted(() => {
  loadOrders();
  startPolling();
});

onUnmounted(() => {
  stopPolling();
});
</script>

<template>
  <div class="page-container">
    <!-- Page Header -->
    <PageHeader 
      title="Gestión de Pedidos"
      subtitle="Administra y actualiza el estado de los pedidos"
      icon="bell"
    />

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-warning" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>

    <!-- Orders List -->
    <div v-else-if="orders.length > 0" class="orders-list">
      <div 
        v-for="order in orders" 
        :key="order.id"
        class="order-item card-mobile"
      >
        <div class="order-header">
          <div class="order-icon">
            <i :class="['bi', getStatusIcon(order.status), 'text-warning']"></i>
          </div>
          <div class="order-info">
            <h4 class="order-number">{{ order.order_number }}</h4>
            <p class="order-user">
              <i class="bi bi-person me-1"></i>
              {{ order.user?.full_name || 'Desconocido' }}
            </p>
            <p class="order-phone">
              <i class="bi bi-telephone me-1"></i>
              {{ order.user?.phone_number || 'N/D' }}
            </p>
          </div>
          <div class="order-meta">
            <span :class="['badge', getStatusBadge(order.status)]">
              {{ translateStatus(order.status) }}
            </span>
            <span class="order-amount">
              <i class="bi bi-currency-dollar"></i>
              {{ order.total_amount }}
            </span>
          </div>
        </div>

        <div class="order-time">
          <i class="bi bi-clock me-1"></i>
          {{ formatDate(order.created_at) }}
        </div>

        <!-- Status Update Buttons -->
        <div v-if="getAvailableStatuses(order.status).length > 0" class="status-actions">
          <p class="status-label">Actualizar Estado:</p>
          <div class="status-buttons">
            <button
              v-for="status in getAvailableStatuses(order.status)"
              :key="status"
              :class="['btn-status', `btn-${status}`]"
              @click="updateStatus(order.id, status)"
            >
              {{ translateStatus(status) }}
            </button>
          </div>
        </div>

        <!-- Order Items -->
        <div v-if="order.items && order.items.length > 0" class="order-items">
          <p class="items-label">Artículos:</p>
          <ul class="items-list">
            <li v-for="(item, index) in order.items" :key="index">
              {{ item.quantity }}x {{ item.name }} - ${{ item.price }}
            </li>
          </ul>
        </div>

        <!-- Order Notes -->
        <div v-if="order.notes" class="order-notes">
          <i class="bi bi-chat-left-text me-1"></i>
          <span>{{ order.notes }}</span>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="empty-state text-center py-5">
      <i class="bi bi-inbox empty-icon"></i>
      <h3 class="empty-title">No Hay Pedidos</h3>
      <p class="empty-text text-muted">No hay pedidos para mostrar en este momento</p>
    </div>
  </div>
</template>

<style scoped>
.orders-list {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.order-item {
  background: #242424;
  border: 1px solid #333333;
  padding: 1rem;
}

.order-item:hover {
  border-color: #FFD700;
}

.order-header {
  display: flex;
  gap: 0.85rem;
  margin-bottom: 0.85rem;
}

.order-icon {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1a1a1a;
  border-radius: 10px;
  font-size: 1.3rem;
  flex-shrink: 0;
}

.order-info {
  flex: 1;
}

.order-number {
  font-size: 0.95rem;
  font-weight: 700;
  color: #FFD700;
  margin-bottom: 0.2rem;
}

.order-user,
.order-phone {
  font-size: 0.75rem;
  color: #b0b0b0;
  margin: 0.1rem 0;
}

.order-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.4rem;
}

.order-amount {
  font-size: 0.9rem;
  font-weight: 700;
  color: #FFD700;
  display: flex;
  align-items: center;
  gap: 0.2rem;
}

.badge {
  padding: 0.2rem 0.6rem;
  border-radius: 6px;
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

.order-time {
  font-size: 0.75rem;
  color: #666666;
  margin-bottom: 0.85rem;
  padding-bottom: 0.85rem;
  border-bottom: 1px solid #333333;
}

.status-actions {
  margin-bottom: 0.85rem;
}

.status-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 0.4rem;
}

.status-buttons {
  display: flex;
  gap: 0.4rem;
  flex-wrap: wrap;
}

.btn-status {
  padding: 0.45rem 0.85rem;
  border: none;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-preparing {
  background: #17a2b8;
  color: #fff;
}

.btn-preparing:hover {
  background: #138496;
}

.btn-ready {
  background: #28a745;
  color: #fff;
}

.btn-ready:hover {
  background: #218838;
}

.btn-completed {
  background: #6c757d;
  color: #fff;
}

.btn-completed:hover {
  background: #5a6268;
}

.btn-cancelled {
  background: #dc3545;
  color: #fff;
}

.btn-cancelled:hover {
  background: #c82333;
}

.order-items {
  margin-bottom: 0.85rem;
}

.items-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 0.4rem;
}

.items-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.items-list li {
  font-size: 0.75rem;
  color: #b0b0b0;
  padding: 0.2rem 0;
  padding-left: 0.85rem;
  position: relative;
}

.items-list li::before {
  content: '•';
  position: absolute;
  left: 0;
  color: #FFD700;
}

.order-notes {
  font-size: 0.75rem;
  color: #999999;
  font-style: italic;
  padding: 0.65rem;
  background: #1a1a1a;
  border-radius: 6px;
  border-left: 3px solid #FFD700;
}

/* Empty State */
.empty-state {
  padding: 3rem 0.85rem;
}

.empty-icon {
  font-size: 4rem;
  color: #333333;
  margin-bottom: 1.2rem;
}

.empty-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 0.4rem;
}

.empty-text {
  font-size: 0.9rem;
  color: #b0b0b0;
}

@media (max-width: 768px) {
  .order-header {
    flex-wrap: wrap;
  }

  .order-meta {
    width: 100%;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }

  .status-buttons {
    flex-direction: column;
  }

  .btn-status {
    width: 100%;
  }
}
</style>
