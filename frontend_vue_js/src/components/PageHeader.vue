<template>
  <div class="page-header">
    <h1 class="page-title">
      <i v-if="icon" :class="`bi bi-${icon}`" class="title-icon"></i>
      {{ title }}
    </h1>
    <p v-if="subtitle" class="page-subtitle">
      {{ subtitle }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
.page-header {
  padding: 0 0.85rem 0.5rem;
  text-align: left;
}

.page-title {
  color: #FFD700;
  font-size: 1.15rem;
  font-weight: 700;
  margin: 0 0 0.4rem 0;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 0.4rem;
}

.title-icon {
  font-size: 1.05rem;
}

.page-subtitle {
  color: #b0b0b0;
  font-size: 0.8rem;
  margin: 0;
  line-height: 1.4;
}

@media (min-width: 768px) {
  .page-header {
    padding: 0 1rem 0.5rem;
  }

  .page-title {
    font-size: 1.3rem;
  }

  .title-icon {
    font-size: 1.2rem;
  }

  .page-subtitle {
    font-size: 0.9rem;
  }
}
</style>
