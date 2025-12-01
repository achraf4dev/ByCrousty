<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../../store/auth';

const router = useRouter();
const { user } = useAuth();
const userName = computed(() => user.value?.full_name || user.value?.username || 'Usuario');
const userEmail = computed(() => user.value?.email || '');
const userPhone = computed(() => user.value?.phone_number || 'No especificado');
const userPoints = computed(() => user.value?.points || 0);

const goBack = () => {
  router.push('/profile');
};
</script>

<template>
  <div class="profile-account-page">
    <header class="subpage-header">
      <button type="button" class="back-btn" @click="goBack"><i class="bi bi-arrow-left"></i></button>
      <h2>Cuenta</h2>
    </header>

    <div class="profile-account">
      <div class="points-card">
        <div class="points-icon"><i class="bi bi-star-fill"></i></div>
        <div class="points-info">
          <h3>Tus Puntos</h3>
          <p class="points-value">{{ userPoints }}</p>
          <span class="points-label">puntos acumulados</span>
        </div>
      </div>

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
    </div>
  </div>
</template>

<style scoped>
.subpage-header { display:flex; align-items:center; gap:1rem; padding:1rem; background:var(--bg-card); border-radius:8px; margin-bottom:1rem; }
.subpage-header h2 { margin:0; font-size:1.25rem; }
.back-btn { background:transparent; border:none; font-size:1.25rem; cursor:pointer; }

.points-card { display:flex; align-items:center; gap:1rem; padding:1.25rem; background:linear-gradient(135deg, var(--primary-color) 0%, #ffed4e 100%); border-radius:10px; margin-bottom:1.25rem; }
.points-icon { font-size:2rem; color:#000; }
.points-value { font-size:1.75rem; font-weight:700; color:#000; }
.profile-info { background:var(--bg-card); padding:1rem; border-radius:10px; border:1px solid var(--border-color); }
.info-item { display:flex; justify-content:space-between; padding:0.75rem 0; border-bottom:1px solid var(--border-color); }
.info-item:last-child { border-bottom:none; }
.info-label { display:flex; gap:0.5rem; align-items:center; color:var(--text-secondary); }
.info-value { color:var(--text-primary); font-weight:600; }
</style>
