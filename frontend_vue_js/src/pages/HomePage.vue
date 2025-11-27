<script setup>
import { ref, onMounted } from 'vue';
import { useAuth } from '../store/auth';
import { useAppStore } from '../store/app';
import PageHeader from '../components/PageHeader.vue';

const { getUser } = useAuth();
const appStore = useAppStore();

// Component state
const loading = ref(true);
const summary = ref(null);

// Fetch data on mount
onMounted(async () => {
  try {
    await appStore.fetchSummary();
    summary.value = appStore.getSummary.value;
  } catch (error) {
    console.error('Error loading summary:', error);
  } finally {
    loading.value = false;
  }
});

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Get status badge class
const getStatusBadge = (status) => {
  const badges = {
    pending: 'bg-warning',
    preparing: 'bg-info',
    ready: 'bg-success',
    completed: 'bg-secondary',
    cancelled: 'bg-danger'
  };
  return badges[status] || 'bg-secondary';
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
</script>

<template>
  <div class="page-container">
    <!-- Page Header -->
    <PageHeader 
      title="¡Bienvenido!"
      subtitle="Esto es lo que está pasando hoy"
      icon="house"
    />

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>

    <!-- Content -->
    <div v-else>
      <!-- Stats Cards 2x2 Grid -->
      <div class="stats-grid mb-4">
        <!-- Scans Today -->
        <div class="stat-card-pro">
          <i class="bi bi-qr-code-scan"></i>
          <div class="stat-content">
            <p class="stat-label-pro">Escaneos Hoy</p>
            <h3 class="stat-value-pro">{{ summary?.totals_today?.scans || 0 }}</h3>
          </div>
        </div>

        <!-- Points Added Today -->
        <div class="stat-card-pro">
          <i class="bi bi-coin"></i>
          <div class="stat-content">
            <p class="stat-label-pro">Puntos Añadidos</p>
            <h3 class="stat-value-pro">{{ summary?.totals_today?.points_added || 0 }}</h3>
          </div>
        </div>

        <!-- Orders Today -->
        <div class="stat-card-pro">
          <i class="bi bi-cart-check"></i>
          <div class="stat-content">
            <p class="stat-label-pro">Pedidos Hoy</p>
            <h3 class="stat-value-pro">{{ summary?.totals_today?.orders || 0 }}</h3>
          </div>
        </div>

        <!-- Gain Today -->
        <div class="stat-card-pro">
          <i class="bi bi-currency-euro"></i>
          <div class="stat-content">
            <p class="stat-label-pro">Ganancias Hoy</p>
            <h3 class="stat-value-pro">{{ summary?.totals_today?.total_revenue || 0 }}€</h3>
          </div>
        </div>
      </div>

      <!-- Latest Points Actions -->
      <div class="section-header">
        <h2 class="section-title">
          <i class="bi bi-coin me-2"></i>
          Últimos Puntos Añadidos
        </h2>
      </div>
      <div v-if="summary?.last_points_actions?.length > 0" class="actions-list mb-4">
        <div v-for="action in summary.last_points_actions.slice(0, 5)" :key="action.id" class="action-item">
          <p class="action-date text-muted">{{ formatDate(action.created_at) }}</p>
          <div class="action-info">
            <h4 class="action-name">{{ action.user?.full_name || 'Usuario Desconocido' }}</h4>
            <p class="action-admin text-muted">
              <i class="bi bi-person-badge me-1"></i>
              {{ action.admin?.full_name || 'Admin' }}
            </p>
          </div>
          <div class="action-meta">
            <span class="action-points">
              +{{ action.points_added }} pts
            </span>
          </div>
        </div>
      </div>
      <div v-else class="empty-state-small text-center py-3">
        <p class="text-muted">No hay acciones de puntos todavía</p>
      </div>

      <!-- Latest Orders -->
      <div class="section-header">
        <h2 class="section-title">
          <i class="bi bi-receipt me-2"></i>
          Últimos Pedidos
        </h2>
      </div>
      <div v-if="summary?.last_orders?.length > 0" class="orders-list">
        <div v-for="order in summary.last_orders.slice(0, 5)" :key="order.id" class="order-item card-mobile">
          <div class="order-info">
            <h4 class="order-name">{{ order.order_number }}</h4>
            <p class="order-user text-muted">
              <i class="bi bi-person me-1"></i>
              {{ order.user?.full_name || 'Desconocido' }}
            </p>
            <p class="order-date text-muted">{{ formatDate(order.created_at) }}</p>
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
      </div>
      <div v-else class="empty-state-small text-center py-3">
        <p class="text-muted">No hay pedidos todavía</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-header {
  margin-bottom: 0.5rem;
}

/* Professional Stats Grid 2x2 */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.85rem;
}

.stat-card-pro {
  background: #2a2a2a;
  border: 1px solid #333333;
  border-radius: 12px;
  padding: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.stat-card-pro:hover {
  transform: translateY(-2px);
  border-color: #FFD700;
  box-shadow: 0 4px 16px rgba(255, 215, 0, 0.2);
}

.stat-card-pro i {
  font-size: 1.8rem;
  flex-shrink: 0;
  color: #FFD700;
}

.stat-content {
  flex: 1;
}

.stat-label-pro {
  font-size: 0.7rem;
  color: #b0b0b0;
  margin: 0 0 0.25rem 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-value-pro {
  font-size: 1.4rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0;
  line-height: 1;
}

.section-header {
  margin-bottom: 0.85rem;
}

.section-title {
  font-size: 1.05rem;
  font-weight: 600;
  color: #ffffff;
  display: flex;
  align-items: center;
}

.actions-list {
  display: flex;
  flex-direction: column;
  gap: 0;
  border: 1px solid #333333;
  border-radius: 0.5rem;
  overflow: hidden;
  background: #242424;
}

.action-item {
  display: grid;
  grid-template-columns: 1fr auto;
  grid-template-rows: auto auto;
  gap: 0 1rem;
  background: #242424;
  border: none;
  border-bottom: 1px solid #333333;
  padding: 0.9rem;
  margin: 0;
}

.action-item:last-child {
  border-bottom: none;
}

.action-item:hover {
  border-color: #FFD700;
}

.action-date {
  grid-column: 2;
  grid-row: 1;
  font-size: 0.65rem;
  margin: 0;
  text-align: right;
  align-self: start;
  color: #b0b0b0;
}

.action-info {
  grid-column: 1;
  grid-row: 1 / 3;
}

.action-name {
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 0.2rem;
  color: #ffffff;
}

.action-admin,
.order-user {
  font-size: 0.75rem;
  margin: 0.1rem 0;
  color: #b0b0b0;
}

.action-meta {
  grid-column: 2;
  grid-row: 2;
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.action-points {
  font-size: 1rem;
  font-weight: 700;
  color: #FFD700;
}

.action-meta {
  display: flex;
  align-items: center;
}

.action-points {
  font-size: 1rem;
  font-weight: 700;
  color: #FFD700;
}

.action-card {
  width: 100%;
  background: #242424;
  color: #FFD700;
  border: 2px solid #FFD700;
  border-radius: 12px;
  padding: 1.5rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s;
}

.action-card:hover {
  background: #FFD700;
  color: #0a0a0a;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.action-icon {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.action-label {
  font-weight: 600;
  font-size: 0.9rem;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.order-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #242424;
  border: 1px solid #333333;
}

.order-item:hover {
  border-color: #FFD700;
}

.order-info {
  flex: 1;
}

.order-name {
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 0.2rem;
  color: #ffffff;
}

.order-date {
  font-size: 0.75rem;
  margin: 0;
  color: #b0b0b0;
}

.order-amount {
  font-size: 0.75rem;
  font-weight: 600;
  color: #FFD700;
  display: flex;
  align-items: center;
  gap: 0.2rem;
}

.order-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.4rem;
}

.order-points {
  font-size: 0.75rem;
  font-weight: 600;
  color: #FFD700;
}

.empty-state-small {
  background: #242424;
  border: 1px solid #333333;
  border-radius: 10px;
  padding: 1.5rem 0.85rem;
}

.empty-state {
  padding: 3rem 1rem;
}

.empty-icon {
  font-size: 4rem;
  color: #333333;
  margin-bottom: 1rem;
}

@media (min-width: 768px) {
  .stat-icon {
    font-size: 2rem;
  }

  .stat-value {
    font-size: 1.5rem;
  }

  .stat-label {
    font-size: 0.75rem;
  }
}
</style>
