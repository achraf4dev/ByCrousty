<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';

const router = useRouter();
const { login } = useAuth();

// Form data
const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');

// Handle login
const handleLogin = async () => {
  error.value = '';
  
  if (!email.value || !password.value) {
    error.value = 'Please fill in all fields';
    return;
  }

  loading.value = true;

  try {
    const result = await login({
      email: email.value,
      password: password.value,
    });

    if (result.success) {
      // Check if user is admin
      if (result.data.user && result.data.user.role === 'admin') {
        // Redirect to home page for admin
        router.push('/home');
      } else {
        error.value = 'Access denied. Admin privileges required.';
        // Logout non-admin user
        const { logout } = useAuth();
        logout();
      }
    } else {
      error.value = result.error || 'Login failed. Please try again.';
    }
  } catch (err) {
    error.value = 'An unexpected error occurred. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="login-page">
    <div class="login-card">
      <!-- Logo/Brand Section -->
      <div class="brand-section">
        <!-- ByCrousty Logo -->
        <div class="brand-logo-container">
          <img src="/logo.png" alt="ByCrousty Logo" class="brand-logo" />
        </div>
        <h1 class="brand-title">ByCrousty</h1>
        <p class="brand-subtitle">Admin Access Only</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="login-form">
        <!-- Email Field -->
        <div class="form-group-mobile">
          <label for="email">Email Address</label>
          <input
            id="email"
            v-model="email"
            type="email"
            class="input-mobile"
            placeholder="Enter your email"
            required
          />
        </div>

        <!-- Password Field -->
        <div class="form-group-mobile">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="password"
            type="password"
            class="input-mobile"
            placeholder="Enter your password"
            required
          />
        </div>

        <!-- Error Message -->
        <div v-if="error" class="alert alert-danger" role="alert">
          {{ error }}
        </div>

        <!-- Login Button -->
        <button
          type="submit"
          class="btn-mobile btn-primary-mobile"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner-mobile"></span>
          <span v-else>Sign In</span>
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #0a0a0a;
  padding: 0;
  overflow: hidden;
  width: 100vw;
  height: 100vh;
}

.login-card {
  background: #1a1a1a;
  border: 2px solid #333333;
  border-radius: 20px;
  padding: 3rem 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  width: 100%;
  max-width: 400px;
  margin: 0 1rem;
}

.brand-section {
  text-align: center;
  margin-bottom: 2.5rem;
}

.brand-logo-container {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.brand-logo {
  width: 140px;
  height: 140px;
  object-fit: contain;
  filter: drop-shadow(0 4px 12px rgba(255, 215, 0, 0.3));
}

.brand-title {
  font-size: 2rem;
  font-weight: 700;
  color: #FFD700;
  margin-bottom: 0.5rem;
}

.brand-subtitle {
  color: #b0b0b0;
  font-size: 0.95rem;
  font-weight: 500;
}

.login-form {
  margin-bottom: 0;
}

@media (max-width: 576px) {
  .login-card {
    padding: 2rem 1.5rem;
    margin: 0 0.5rem;
  }

  .brand-logo {
    width: 110px;
    height: 110px;
  }

  .brand-title {
    font-size: 1.75rem;
  }

  .brand-subtitle {
    font-size: 0.875rem;
  }
}
</style>
