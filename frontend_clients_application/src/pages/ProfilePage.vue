<script setup>
/**
 * Profile Page
 * User profile information and logout
 */
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';
import { useUI } from '../store/ui';

const router = useRouter();
const { user, logout } = useAuth();
const { showSuccess } = useUI();

const userPoints = computed(() => user.value?.points || 0);
const userName = computed(() => user.value?.full_name || user.value?.username || 'Usuario');
const userEmail = computed(() => user.value?.email || '');
const userPhone = computed(() => user.value?.phone_number || 'No especificado');

const handleLogout = async () => {
  if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
    await logout();
    showSuccess('Sesión cerrada correctamente');
    router.push('/login');
  }
};

const goToHome = () => {
  router.push('/home');
};
</script>

<template>
  <div class="profile-page">
    <div class="profile-container">
      <!-- Profile Header -->
      <div class="profile-header">
        <div class="profile-avatar">
          <i class="bi bi-person-circle"></i>
        </div>
        <h1>{{ userName }}</h1>
        <p>{{ userEmail }}</p>
      </div>

      <!-- Points Card -->
      <div class="points-card">
        <div class="points-icon">
          <i class="bi bi-star-fill"></i>
        </div>
        <div class="points-info">
          <h3>Tus Puntos</h3>
          <p class="points-value">{{ userPoints }}</p>
          <span class="points-label">puntos acumulados</span>
        </div>
      </div>

      <!-- Profile Info -->
      <div class="profile-info">
        <h3>Información Personal</h3>
        
        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-person"></i>
            <span>Nombre Completo</span>
          </div>
          <div class="info-value">{{ userName }}</div>
        </div>

        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-envelope"></i>
            <span>Email</span>
          </div>
          <div class="info-value">{{ userEmail }}</div>
        </div>

        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-telephone"></i>
            <span>Teléfono</span>
          </div>
          <div class="info-value">{{ userPhone }}</div>
        </div>
      </div>

      <!-- Actions -->
      <div class="profile-actions">
        <button class="btn btn-outline-light btn-lg w-100 mb-3" @click="goToHome">
          <i class="bi bi-house me-2"></i>Ir al Inicio
        </button>

        <button class="btn btn-danger btn-lg w-100" @click="handleLogout">
          <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-page {
  max-width: 600px;
  margin: 0 auto;
}

.profile-container {
  animation: fadeIn 0.3s ease;
}

.profile-header {
  text-align: center;
  padding: 2rem 1rem;
  background: var(--bg-card);
  border-radius: 12px;
  margin-bottom: 2rem;
  border: 1px solid var(--border-color);
}

.profile-avatar {
  margin-bottom: 1rem;
}

.profile-avatar i {
  font-size: 5rem;
  color: var(--primary-color);
}

.profile-header h1 {
  color: var(--text-primary);
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.profile-header p {
  color: var(--text-secondary);
  font-size: 1rem;
}

.points-card {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 2rem;
  background: linear-gradient(135deg, var(--primary-color) 0%, #ffed4e 100%);
  border-radius: 12px;
  margin-bottom: 2rem;
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.points-icon {
  font-size: 3rem;
  color: #000;
}

.points-info {
  flex: 1;
}

.points-info h3 {
  color: #000;
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
}

.points-value {
  color: #000;
  font-size: 2.5rem;
  font-weight: 800;
  margin: 0;
}

.points-label {
  color: rgba(0, 0, 0, 0.7);
  font-size: 0.875rem;
}

.profile-info {
  background: var(--bg-card);
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
  border: 1px solid var(--border-color);
}

.profile-info h3 {
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  text-align: center;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid var(--border-color);
}

.info-item:last-child {
  border-bottom: none;
}

.info-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.info-label i {
  font-size: 1.25rem;
  color: var(--primary-color);
}

.info-value {
  color: var(--text-primary);
  font-weight: 600;
  text-align: right;
}

.profile-actions {
  display: flex;
  flex-direction: column;
}

.btn-outline-light {
  border: 2px solid var(--border-color);
  color: var(--text-primary);
}

.btn-outline-light:hover {
  background-color: var(--bg-card);
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.btn-danger:hover {
  background-color: #dc3545;
  border-color: #dc3545;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
