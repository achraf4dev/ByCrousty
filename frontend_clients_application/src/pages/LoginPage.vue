<script setup>
/**
 * Login Page
 * User authentication page
 */
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';
import { useUI } from '../store/ui';

const router = useRouter();
const { login } = useAuth();
const { showError } = useUI();

const formData = ref({
  email: '',
  password: '',
});

const loading = ref(false);
const errors = ref({});

const validateForm = () => {
  errors.value = {};
  let isValid = true;

  if (!formData.value.email) {
    errors.value.email = 'El email es requerido';
    isValid = false;
  } else if (!/\S+@\S+\.\S+/.test(formData.value.email)) {
    errors.value.email = 'Email inválido';
    isValid = false;
  }

  if (!formData.value.password) {
    errors.value.password = 'La contraseña es requerida';
    isValid = false;
  } else if (formData.value.password.length < 6) {
    errors.value.password = 'La contraseña debe tener al menos 6 caracteres';
    isValid = false;
  }

  return isValid;
};

const handleLogin = async () => {
  if (!validateForm()) return;

  loading.value = true;

  const result = await login({
    email: formData.value.email,
    password: formData.value.password,
  });

  loading.value = false;

  if (result.success) {
    router.push('/home');
  } else {
    showError(result.error || 'Error al iniciar sesión');
  }
};

const goToRegister = () => {
  router.push('/register');
};

const continueAsGuest = () => {
  router.push('/home');
};
</script>

<template>
  <div class="login-page bg-dark-section">
  <div class="login-container card-mobile">
  <div style="padding-top: 1.2rem;"></div>
  <img src="/logo.png" alt="ByCrousty Logo" class="login-logo" />
  <h2 class="login-title page-title text-primary">Bienvenido a ByCrousty</h2>
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group-mobile">
          <label for="email"><i class="bi bi-envelope" style="margin-right:6px;"></i>Correo electrónico</label>
          <input
            id="email"
            v-model="formData.email"
            type="email"
            :class="['input-mobile', { 'is-invalid': errors.email }]"
            placeholder="Introduce tu correo profesional"
            autocomplete="username"
          />
          <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
        </div>
        <div class="form-group-mobile">
          <label for="password"><i class="bi bi-lock" style="margin-right:6px;"></i>Contraseña</label>
          <input
            id="password"
            v-model="formData.password"
            type="password"
            :class="['input-mobile', { 'is-invalid': errors.password }]"
            placeholder="Introduce tu contraseña"
            autocomplete="current-password"
          />
          <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
        </div>
  <button type="submit" class="btn-primary-mobile btn-mobile login-btn-compact" :disabled="loading">
          <span v-if="loading">Accediendo...</span>
          <span v-else><i class="bi bi-box-arrow-in-right" style="margin-right:6px;"></i>Acceder</span>
        </button>
      </form>
      <div class="login-actions">
  <button type="button" class="btn-link w-100" @click="goToRegister">¿No tienes cuenta? Regístrate</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.form-group-mobile label {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text-primary);
  display: flex;
  align-items: center;
}
.login-logo {
  display: block;
  margin: 0 auto 1rem auto;
  max-width: 90px;
  max-height: 90px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 2px 8px rgba(0,0,0,0.10);
}
.login-btn-compact {
  width: auto;
  min-width: 120px;
  max-width: 100%;
  display: block;
  margin: 0 auto;
}

/* Use global and HomePage color variables and classes for consistency */
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  background: var(--bg-darker);
  padding-top: 4rem;
}

.login-container {
  width: 100%;
  max-width: 350px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.18);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  background: var(--bg-card);
  margin-top: 0;
  padding-top: 0;
}
.login-container {
  width: 100%;
  max-width: 350px;
  /* Use card-mobile for unified style */
  background: var(--bg-card);
  border-radius: 10px;
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  padding: 0.85rem;
  margin-bottom: 0.65rem;
  transition: transform 0.2s, box-shadow 0.2s;
  padding-bottom: 2rem;
}

.login-title {
  text-align: center;
  font-size: 1.15rem;
  font-weight: 700;
  margin-bottom: 0.3rem;
  margin-top: 0;
  color: var(--primary-color);
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
  padding: 2rem 0rem;
}

.invalid-feedback {
  color: #e74c3c;
  font-size: 0.92rem;
  margin-top: 0.2rem;
}

.login-actions {
  margin-top: 0.2rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.btn-link {
  background: none;
  border: none;
  color: var(--text-secondary);
  text-align: center;
  font-size: 1rem;
  cursor: pointer;
  padding: 0.5rem 0;
  border-radius: 4px;
  transition: background 0.15s, color 0.15s;
}
.btn-link:hover {
  background: var(--bg-card);
  color: var(--primary-color);
}
.input-mobile {
  width: 100%;
  padding: 0.65rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 0.95rem;
  background-color: var(--bg-darker);
  color: var(--text-primary);
  transition: border-color 0.3s;
}
.btn-primary-mobile.btn-mobile {
  padding: 0.7rem 1.2rem;
  font-size: 1rem;
}
</style>
