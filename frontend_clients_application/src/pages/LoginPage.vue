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
  <div class="login-page">
    <div class="login-container">
      <div class="login-header">
        <h1>Iniciar Sesión</h1>
        <p>Accede a tu cuenta de ByCrousty</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <!-- Email Field -->
        <div class="mb-3">
          <label for="email" class="form-label">
            <i class="bi bi-envelope me-2"></i>Email
          </label>
          <input
            id="email"
            v-model="formData.email"
            type="email"
            class="form-control form-control-lg"
            :class="{ 'is-invalid': errors.email }"
            placeholder="tu@email.com"
          />
          <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
        </div>

        <!-- Password Field -->
        <div class="mb-4">
          <label for="password" class="form-label">
            <i class="bi bi-lock me-2"></i>Contraseña
          </label>
          <input
            id="password"
            v-model="formData.password"
            type="password"
            class="form-control form-control-lg"
            :class="{ 'is-invalid': errors.password }"
            placeholder="Tu contraseña"
          />
          <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold mb-3" :disabled="loading">
          <span v-if="loading">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Iniciando sesión...
          </span>
          <span v-else>
            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
          </span>
        </button>
      </form>

      <!-- Alternative Actions -->
      <div class="login-footer">
        <p class="text-center text-secondary mb-3">¿No tienes cuenta?</p>
        <button type="button" class="btn btn-outline-light btn-lg w-100 mb-3" @click="goToRegister">
          <i class="bi bi-person-plus me-2"></i>Registrarse
        </button>

        <div class="divider my-3">
          <span class="px-3">o</span>
        </div>

        <button type="button" class="btn btn-outline-secondary btn-lg w-100" @click="continueAsGuest">
          <i class="bi bi-arrow-right-circle me-2"></i>Continuar como invitado
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: calc(100vh - 120px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.login-container {
  width: 100%;
  max-width: 450px;
  background: var(--bg-card);
  border-radius: 16px;
  padding: 2rem;
  border: 1px solid var(--border-color);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-header h1 {
  color: var(--primary-color);
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.login-header p {
  color: var(--text-secondary);
}

.login-form {
  margin-bottom: 2rem;
}

.form-label {
  color: var(--text-primary);
  font-weight: 600;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
}

.form-control {
  background-color: var(--bg-darker);
  border: 2px solid var(--border-color);
  color: var(--text-primary);
  padding: 0.75rem 1rem;
}

.form-control:focus {
  background-color: var(--bg-card);
  border-color: var(--primary-color);
  color: var(--text-primary);
  box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
}

.form-control::placeholder {
  color: var(--text-secondary);
}

.btn-warning {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  color: #000;
}

.btn-warning:hover {
  background-color: var(--primary-dark);
  border-color: var(--primary-dark);
  color: #000;
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

.btn-outline-secondary {
  border: 2px solid var(--border-color);
}

.login-footer {
  text-align: center;
}

.divider {
  position: relative;
  text-align: center;
  margin: 1.5rem 0;
}

.divider::before,
.divider::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 40%;
  height: 1px;
  background: var(--border-color);
}

.divider::before {
  left: 0;
}

.divider::after {
  right: 0;
}

.divider span {
  background: var(--bg-card);
  color: var(--text-secondary);
  font-size: 0.875rem;
}

/* Animation */
.login-container {
  animation: fadeIn 0.3s ease;
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
