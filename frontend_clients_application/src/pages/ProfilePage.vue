<script setup>
/**
 * Profile Page
 * User profile information and logout
 */
import { computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';
import { useUI } from '../store/ui';
import api from '../services/api';

const router = useRouter();
const { user, logout } = useAuth();
const { showSuccess } = useUI();

const userPoints = computed(() => user.value?.points || 0);
const userName = computed(() => user.value?.full_name || user.value?.username || 'Usuario');
const userEmail = computed(() => user.value?.email || '');
const userPhone = computed(() => user.value?.phone_number || 'No especificado');

const qrSrc = ref('');
const loadingQr = ref(false);

const fetchQr = async () => {
  loadingQr.value = true;
  try {
    const res = await api.getMyQrCode();

    const contentType = (res?.headers && (res.headers['content-type'] || res.headers['Content-Type'])) || '';

    // If backend returned JSON (typical: { qr: 'data:...', qr_url: '...' })
    if (contentType.toLowerCase().includes('application/json')) {
      const data = res?.data || {};
      qrSrc.value = data.qr || data.qr_url || data.url || data.data?.qr || data.data?.qr_url || '';
      if (!qrSrc.value && data.code) {
        qrSrc.value = `data:image/png;base64,${data.code}`;
      }
    } else if (contentType.toLowerCase().startsWith('image/')) {
      // Backend served image/png (binary). Fetch as arraybuffer and convert to data URI.
      try {
        const dataUri = await api.getMyQrCodeImage();
        qrSrc.value = dataUri;
      } catch (err) {
        console.warn('ProfilePage: failed to fetch QR image as arraybuffer', err);
        qrSrc.value = '';
      }
    } else {
      // Fallback: try to parse as JSON first, otherwise try binary fetch
      const data = res?.data || {};
      qrSrc.value = data.qr || '';
      if (!qrSrc.value) {
        try {
          const dataUri = await api.getMyQrCodeImage();
          qrSrc.value = dataUri;
        } catch (err) {
          console.warn('ProfilePage: unexpected QR response format', err);
          qrSrc.value = '';
        }
      }
    }
  } catch (err) {
    console.warn('Could not load user QR code', err);
    qrSrc.value = '';
  } finally {
    loadingQr.value = false;
  }
};

onMounted(() => {
  fetchQr();
});

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
      <!-- QR + Name Header -->
      <div class="profile-header-qr">
        <div class="qr-area">
          <div v-if="loadingQr" class="qr-placeholder">Cargando QR...</div>
          <img v-else-if="qrSrc" :src="qrSrc" alt="QR code" class="qr-image" />
          <div v-else class="profile-avatar-fallback">
            <i class="bi bi-person-circle"></i>
          </div>
        </div>

        <h1>{{ userName }}</h1>
      </div>

      <!-- Tabs Navigation -->
      <nav class="profile-tabs" role="navigation" aria-label="Perfil">
        <router-link to="/profile/account" class="tab" active-class="active">
          <i class="bi bi-person tab-icon" aria-hidden="true"></i>
          <span class="tab-text">Cuenta</span>
        </router-link>

        <router-link to="/profile/settings" class="tab" active-class="active">
          <i class="bi bi-gear tab-icon" aria-hidden="true"></i>
          <span class="tab-text">Ajustes</span>
        </router-link>

        <router-link to="/profile/orders" class="tab" active-class="active">
          <i class="bi bi-bag tab-icon" aria-hidden="true"></i>
          <span class="tab-text">Pedidos</span>
        </router-link>

        <router-link to="/profile/locations" class="tab" active-class="active">
          <i class="bi bi-geo-alt tab-icon" aria-hidden="true"></i>
          <span class="tab-text">Dónde encontrarnos</span>
        </router-link>

        <router-link to="/profile/change-password" class="tab" active-class="active">
          <i class="bi bi-key tab-icon" aria-hidden="true"></i>
          <span class="tab-text">Cambiar contraseña</span>
        </router-link>

        <button class="tab logout" type="button" @click="handleLogout">
          <i class="bi bi-box-arrow-right tab-icon" aria-hidden="true"></i>
          <span class="tab-text">Cerrar sesión</span>
        </button>
      </nav>

      <!-- Sub-pages removed per request -->
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

.profile-header-qr {
  text-align: center;
  padding: 1.25rem 0.5rem; /* reduce left/right padding so QR uses more horizontal space */
  background: var(--bg-card);
  border-radius: 12px;
  margin-bottom: 1rem;
  border: 1px solid var(--border-color);
}

.qr-area { width: 200px; height: 200px; margin: 0 auto .6rem; display:flex; align-items:center; justify-content:center; overflow: hidden; background: #fff; border: 1px solid var(--border-color); box-shadow: 0 6px 14px rgba(0,0,0,0.06); padding: 5px; }
.qr-image { width: 100%; height: 100%; object-fit: contain; border-radius: 6px; background: transparent; display: block; transform: none; }
.qr-placeholder { width: 190px; height: 190px; display:flex; align-items:center; justify-content:center; background:var(--bg-card); border-radius:10px; color:var(--text-secondary); }
.profile-avatar-fallback i { font-size:5.5rem; color:var(--primary-color); }

.profile-header-qr h1 { color: var(--text-primary); font-size:1.45rem; margin: .25rem 0; font-weight:600; }
.profile-header-qr .email { color:var(--text-secondary); margin:0; }

.profile-tabs {
  display: flex;
  flex-direction: column;
  gap: 0; /* no gap so elements are flush; separation via bottom border */
  margin: 1rem 0 1.25rem 0;
}
.profile-tabs .tab {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  text-align: left;
  padding: 1rem 1rem; /* a bit larger */
  border-radius: 0; /* square corners */
  background: transparent; /* remove boxed backgrounds */
  border: none;
  border-bottom: 1px solid var(--border-color); /* only bottom border */
  color: var(--text-primary);
  text-decoration: none;
  font-weight: 600;
  font-size: 1.05rem; /* slightly larger text */
}
.profile-tabs .tab.active {
  color: var(--primary-contrast, #fff);
  border-bottom-color: var(--primary-color);
}
.profile-tabs .tab.logout {
  background: transparent;
  color: var(--danger-color);
  margin-left: 0;
  text-align: left;
}

.tab-icon {
  font-size: 1.2rem;
  color: var(--text-secondary);
}
.profile-tabs .tab.active .tab-icon {
  color: var(--primary-color);
}
.profile-tabs .tab.logout .tab-icon {
  color: var(--danger-color);
}
.tab-text {
  display: inline-block;
}

.profile-subpage { margin-top: .5rem; }

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
