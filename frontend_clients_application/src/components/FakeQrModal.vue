<template>
  <div v-if="visible" class="qr-modal-backdrop">
    <div class="qr-modal">
      <button class="close-btn" @click="close">×</button>
      <h3 class="title">Código QR de canje</h3>
      <p class="subtitle">Presenta este código en caja antes de que caduque</p>

      <div class="qr-area">
        <!-- Simple placeholder QR graphic -->
        <svg width="180" height="180" viewBox="0 0 200 200" class="qr-svg" xmlns="http://www.w3.org/2000/svg">
          <rect width="200" height="200" fill="#fff" />
          <g fill="#111">
            <rect x="10" y="10" width="50" height="50" />
            <rect x="140" y="10" width="50" height="50" />
            <rect x="10" y="140" width="50" height="50" />
            <!-- random modules -->
            <rect x="80" y="40" width="16" height="16" />
            <rect x="100" y="60" width="12" height="12" />
            <rect x="60" y="90" width="10" height="10" />
            <rect x="140" y="80" width="8" height="8" />
            <rect x="120" y="120" width="18" height="18" />
          </g>
        </svg>

        <div class="countdown">
          <div class="time">{{ minutes }}:{{ seconds }}</div>
          <div class="hint">Tiempo restante</div>
        </div>
      </div>

      <div class="actions">
        <button class="btn btn-light" @click="close">Cerrar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onUnmounted, computed } from 'vue';

const props = defineProps({
  visible: { type: Boolean, default: false },
  durationSeconds: { type: Number, default: 15 * 60 },
});
const emit = defineEmits(['close']);

const remaining = ref(props.durationSeconds);
let timer = null;

const start = () => {
  clearInterval(timer);
  remaining.value = props.durationSeconds;
  timer = setInterval(() => {
    if (remaining.value > 0) {
      remaining.value -= 1;
    } else {
      clearInterval(timer);
      emit('close');
    }
  }, 1000);
};

watch(() => props.visible, (v) => {
  if (v) start(); else clearInterval(timer);
});

onUnmounted(() => clearInterval(timer));

const minutes = computed(() => String(Math.floor(remaining.value / 60)).padStart(2, '0'));
const seconds = computed(() => String(remaining.value % 60).padStart(2, '0'));

const close = () => emit('close');
</script>

<style scoped>
.qr-modal-backdrop {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.45);
  z-index: 9999;
}
.qr-modal {
  background: var(--bg-card);
  padding: 1.25rem;
  border-radius: 10px;
  width: 360px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.4);
  text-align: center;
  position: relative;
}
.qr-modal .close-btn {
  position: absolute;
  right: 8px;
  top: 6px;
  background: transparent;
  border: none;
  font-size: 20px;
  cursor: pointer;
}
.qr-modal .qr-area { display:flex; gap:16px; align-items:center; justify-content:center; margin:12px 0; }
.qr-svg { border: 8px solid #fff; box-shadow: 0 6px 18px rgba(0,0,0,0.12); }
.countdown .time { font-size: 1.2rem; font-weight: 800; }
.countdown .hint { font-size: 0.85rem; color: var(--text-secondary); }
.actions { margin-top: 8px; }
</style>
