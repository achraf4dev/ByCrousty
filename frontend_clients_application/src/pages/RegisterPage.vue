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
  <div class="register-page bg-dark-section">
    <div class="register-container card-mobile">
      <div style="padding-top: 1.2rem;"></div>
      <img src="/logo.png" alt="ByCrousty Logo" class="register-logo" />
      <h2 class="register-title page-title text-primary">Crea tu cuenta</h2>
      <form @submit.prevent="handleRegister" class="register-form-modern">
        <div class="form-group-mobile">
          <label for="full_name"><i class="bi bi-person" style="margin-right:6px;"></i>Nombre completo</label>
          <input
            id="full_name"
            v-model="formData.full_name"
            type="text"
            :class="['input-mobile', { 'is-invalid': errors.full_name } ]"
            placeholder="Introduce tu nombre completo"
          />
          <div v-if="errors.full_name" class="invalid-feedback">{{ errors.full_name }}</div>
        </div>
        <div class="form-group-mobile">
          <label for="email"><i class="bi bi-envelope" style="margin-right:6px;"></i>Correo electrónico</label>
          <input
            id="email"
            v-model="formData.email"
            type="email"
            :class="['input-mobile', { 'is-invalid': errors.email } ]"
            placeholder="Introduce tu correo profesional"
            autocomplete="username"
          />
          <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
        </div>
        <div class="form-group-mobile">
          <label for="phone_number"><i class="bi bi-telephone" style="margin-right:6px;"></i>Teléfono <small class="text-muted">(opcional)</small></label>
          <input
            id="phone_number"
            v-model="formData.phone_number"
            type="tel"
            class="input-mobile"
            placeholder="+34 123 456 789"
          />
        </div>
        <div class="form-group-mobile">
          <label for="password"><i class="bi bi-lock" style="margin-right:6px;"></i>Contraseña</label>
          <input
            id="password"
            v-model="formData.password"
            type="password"
            :class="['input-mobile', { 'is-invalid': errors.password } ]"
            placeholder="Introduce tu contraseña"
            autocomplete="new-password"
          />
          <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
        </div>
        <div class="form-group-mobile">
          <label for="password_confirmation"><i class="bi bi-lock-fill" style="margin-right:6px;"></i>Confirmar contraseña</label>
          <input
            id="password_confirmation"
            v-model="formData.password_confirmation"
            type="password"
            :class="['input-mobile', { 'is-invalid': errors.password_confirmation } ]"
            placeholder="Confirma tu contraseña"
            autocomplete="new-password"
          />
          <div v-if="errors.password_confirmation" class="invalid-feedback">{{ errors.password_confirmation }}</div>
        </div>
        <button type="submit" class="btn-primary-mobile btn-mobile register-btn-compact" :disabled="loading">
          <span v-if="loading">Registrando...</span>
          <span v-else><i class="bi bi-person-plus" style="margin-right:6px;"></i>Crear cuenta</span>
        </button>
      </form>
      <div class="register-actions">
        <button type="button" class="btn-link w-100" @click="goToLogin">¿Ya tienes cuenta? Inicia sesión</button>
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

/* Modern, clean, dark style matching LoginPage.vue */
.register-page {
  min-height: 100vh;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  background: var(--bg-darker);
  padding-top: 4rem;
}

.register-container {
  width: 100%;
  max-width: 350px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.18);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  background: var(--bg-card);
  padding-bottom: 2rem;
}
.register-container {
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

.register-logo {
  display: block;
  margin: 0 auto 1rem auto;
  max-width: 90px;
  max-height: 90px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 2px 8px rgba(0,0,0,0.10);
}

.register-title {
  text-align: center;
  font-size: 1.15rem;
  font-weight: 700;
  margin-bottom: 0.3rem;
  margin-top: 0;
  color: var(--primary-color);
}

.register-form-modern {
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
  padding: 2rem 0rem;
}

.form-group-mobile label {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text-primary);
  display: flex;
  align-items: center;
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
.input-mobile:focus {
  border-color: var(--primary-color);
  outline: none;
  background: var(--bg-card);
}
.input-mobile.is-invalid {
  border-color: #e74c3c;
}
.invalid-feedback {
  color: #e74c3c;
  font-size: 0.92rem;
  margin-top: 0.2rem;
}

.btn-primary-mobile.btn-mobile.register-btn-compact {
  width: auto;
  min-width: 120px;
  max-width: 100%;
  display: block;
  margin: 0 auto;
  padding: 0.7rem 1.2rem;
  font-size: 1rem;
  background: var(--primary-color);
  color: #181a20;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-primary-mobile.btn-mobile.register-btn-compact:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
.btn-primary-mobile.btn-mobile.register-btn-compact:hover:not(:disabled) {
  background: var(--primary-dark);
}

.register-actions {
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
</style>
