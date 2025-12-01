<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();
const goBack = () => router.push('/profile');
</script>

<template>
  <div class="profile-subpage-page">
    <header class="subpage-header">
      <button type="button" class="back-btn" @click="goBack"><i class="bi bi-arrow-left"></i></button>
      <h2>Dónde encontrarnos</h2>
    </header>

    <div class="profile-locations">
      <p>Direcciones y mapa de nuestras localizaciones. Puedes integrar la API `getFindUs()` del servicio `api`.</p>
    </div>
  </div>
</template>

<style scoped>
.subpage-header { display:flex; align-items:center; gap:1rem; padding:1rem; background:var(--bg-card); border-radius:8px; margin-bottom:1rem; }
.back-btn { background:transparent; border:none; font-size:1.25rem; cursor:pointer; }
h2 { margin:0; font-size:1.1rem; }
.profile-locations p { margin:0.5rem 0; }
</style>