<script setup>
/**
 * Register Page
 * New user registration
 */
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';
import { useUI } from '../store/ui';

const router = useRouter();
const { register } = useAuth();
const { showError } = useUI();

const formData = ref({
  full_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone_number: '',
});

const loading = ref(false);
const errors = ref({});

const validateForm = () => {
  errors.value = {};
  let isValid = true;

  if (!formData.value.full_name) {
    errors.value.full_name = 'El nombre completo es requerido';
    isValid = false;
  }

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

  if (formData.value.password !== formData.value.password_confirmation) {
    errors.value.password_confirmation = 'Las contraseñas no coinciden';
    isValid = false;
  }

  return isValid;
};

const handleRegister = async () => {
  if (!validateForm()) return;

  loading.value = true;

  const result = await register({
    full_name: formData.value.full_name,
    email: formData.value.email,
    password: formData.value.password,
    password_confirmation: formData.value.password_confirmation,
    phone_number: formData.value.phone_number,
  });

  loading.value = false;

  if (result.success) {
    router.push('/home');
  } else {
    showError(result.error || 'Error al registrarse');
  }
};

const goToLogin = () => {
  router.push('/login');
};
</script>

<template>
  <div class="register-page">
    <div class="register-container">
      <div class="register-header">
        <h1>Crear Cuenta</h1>
        <p>Únete a la familia ByCrousty</p>
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <!-- Full Name -->
        <div class="mb-3">
          <label for="full_name" class="form-label">
            <i class="bi bi-person me-2"></i>Nombre Completo
          </label>
          <input
            id="full_name"
            v-model="formData.full_name"
            type="text"
            class="form-control form-control-lg"
            :class="{ 'is-invalid': errors.full_name }"
            placeholder="Tu nombre completo"
          />
          <div v-if="errors.full_name" class="invalid-feedback">{{ errors.full_name }}</div>
        </div>

        <!-- Email -->
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

        <!-- Phone Number -->
        <div class="mb-3">
          <label for="phone_number" class="form-label">
            <i class="bi bi-telephone me-2"></i>Teléfono <small class="text-muted">(opcional)</small>
          </label>
          <input
            id="phone_number"
            v-model="formData.phone_number"
            type="tel"
            class="form-control form-control-lg"
            placeholder="+34 123 456 789"
          />
        </div>

        <!-- Password -->
        <div class="mb-3">
          <label for="password" class="form-label">
            <i class="bi bi-lock me-2"></i>Contraseña
          </label>
          <input
            id="password"
            v-model="formData.password"
            type="password"
            class="form-control form-control-lg"
            :class="{ 'is-invalid': errors.password }"
            placeholder="Mínimo 6 caracteres"
          />
          <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
          <label for="password_confirmation" class="form-label">
            <i class="bi bi-lock-fill me-2"></i>Confirmar Contraseña
          </label>
          <input
            id="password_confirmation"
            v-model="formData.password_confirmation"
            type="password"
            class="form-control form-control-lg"
            :class="{ 'is-invalid': errors.password_confirmation }"
            placeholder="Confirma tu contraseña"
          />
          <div v-if="errors.password_confirmation" class="invalid-feedback">{{ errors.password_confirmation }}</div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold mb-3" :disabled="loading">
          <span v-if="loading">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Registrando...
          </span>
          <span v-else>
            <i class="bi bi-person-plus me-2"></i>Crear Cuenta
          </span>
        </button>
      </form>

      <!-- Alternative Actions -->
      <div class="register-footer">
        <p class="text-center text-secondary mb-3">¿Ya tienes cuenta?</p>
        <button type="button" class="btn btn-outline-light btn-lg w-100" @click="goToLogin">
          <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  min-height: calc(100vh - 120px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.register-container {
  width: 100%;
  max-width: 450px;
  background: var(--bg-card);
  border-radius: 16px;
  padding: 2rem;
  border: 1px solid var(--border-color);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.register-header {
  text-align: center;
  margin-bottom: 2rem;
}

.register-header h1 {
  color: var(--primary-color);
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.register-header p {
  color: var(--text-secondary);
}

.register-form {
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

.register-footer {
  text-align: center;
}

/* Animation */
.register-container {
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
