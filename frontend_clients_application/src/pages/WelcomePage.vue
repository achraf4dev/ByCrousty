<script setup>
/**
 * Welcome Page - First Run Screen
 * Shown only the first time the user opens the app
 */
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const getStarted = () => {
  // Mark that user has seen the welcome screen
  localStorage.setItem('has_seen_welcome', 'true');
  router.push('/home');
};

// Check if user has already seen welcome page
onMounted(() => {
  const hasSeenWelcome = localStorage.getItem('has_seen_welcome');
  if (hasSeenWelcome) {
    router.push('/home');
  }
});
</script>

<template>
  <div class="welcome-page">
    <div class="welcome-content">
      <div class="welcome-image">
        <img src="/logo.png" alt="ByCrousty Logo" class="logo-image" />
      </div>

      <div class="welcome-text">
        <h2>¡Bienvenido!</h2>
        <p>Descubre nuestros deliciosos productos</p>
        <p>Realiza pedidos fácilmente</p>
        <p>Acumula puntos con cada compra</p>
      </div>

      <button class="btn btn-warning btn-lg px-4 py-2 fw-bold" style="font-size: 1rem;" @click="getStarted">
        Comenzar
        <i class="bi bi-arrow-right ms-2"></i>
      </button>
    </div>
  </div>
</template>

<style scoped>
.welcome-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: linear-gradient(135deg, var(--bg-darker) 0%, var(--bg-dark) 100%);
}

.welcome-content {
  text-align: center;
  max-width: 500px;
  animation: fadeIn 0.8s ease;
}

.welcome-image {
  margin: 2rem 0;
  animation: floatAndRotate 3s ease-in-out infinite;
}

.logo-image {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  border: 4px solid var(--primary-color);
  box-shadow: 
    0 0 20px rgba(255, 215, 0, 0.3),
    0 0 40px rgba(255, 215, 0, 0.2),
    0 0 60px rgba(255, 215, 0, 0.1);
  object-fit: cover;
  animation: pulse 2s ease-in-out infinite;
}

.welcome-image:hover .logo-image {
  animation: spinAndPulse 1s ease-in-out;
}

.welcome-text {
  margin: 1.5rem 0 2rem;
}

.welcome-text h2 {
  color: var(--text-primary);
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

.welcome-text p {
  color: var(--text-secondary);
  font-size: 0.95rem;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.welcome-text p::before {
  content: "✓";
  color: var(--primary-color);
  font-weight: bold;
}

.btn-large {
  padding: 1rem 3rem;
  font-size: 1.25rem;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  animation: pulse 2s infinite;
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

@keyframes floatAndRotate {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
  }
  25% {
    transform: translateY(-15px) rotate(5deg);
  }
  50% {
    transform: translateY(-20px) rotate(0deg);
  }
  75% {
    transform: translateY(-15px) rotate(-5deg);
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 
      0 0 20px rgba(255, 215, 0, 0.3),
      0 0 40px rgba(255, 215, 0, 0.2),
      0 0 60px rgba(255, 215, 0, 0.1);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 
      0 0 30px rgba(255, 215, 0, 0.5),
      0 0 60px rgba(255, 215, 0, 0.3),
      0 0 90px rgba(255, 215, 0, 0.2);
  }
}

@keyframes spinAndPulse {
  0% {
    transform: scale(1) rotate(0deg);
  }
  50% {
    transform: scale(1.15) rotate(180deg);
  }
  100% {
    transform: scale(1) rotate(360deg);
  }
}

/* Tablet and Desktop */
@media (min-width: 768px) {
  .logo-image {
    width: 180px;
    height: 180px;
  }

  .welcome-text h2 {
    font-size: 1.75rem;
  }

  .welcome-text p {
    font-size: 1rem;
  }
}
</style>
