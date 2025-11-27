<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useAppStore } from '../store/app';
import { Html5Qrcode } from 'html5-qrcode';
import PageHeader from '../components/PageHeader.vue';

const appStore = useAppStore();

// Component state
const scanning = ref(false);
const scannedUser = ref(null);
const error = ref('');
const selectedPoints = ref(null);
const manualPoints = ref('');
const remark = ref('');
const submitting = ref(false);
const cameraStarted = ref(false);
const initializingCamera = ref(false);
const showSuccess = ref(false);
const addedPoints = ref(0);

// QR Scanner instance
let html5QrCode = null;

// Ref for manual input
const manualPointsInput = ref(null);

// Points options
const pointsOptions = [50, 100, 200, 500, 1000];

// Start camera scanning
const startCamera = async () => {
  try {
    initializingCamera.value = true;
    scanning.value = true;
    cameraStarted.value = false;
    error.value = '';
    scannedUser.value = null;
    selectedPoints.value = null;
    remark.value = '';

    // Check camera permission first (if browser supports it)
    if (navigator.permissions && navigator.permissions.query) {
      try {
        const permissionStatus = await navigator.permissions.query({ name: 'camera' });
        if (permissionStatus.state === 'denied') {
          throw new Error('NotAllowedError');
        }
      } catch (permErr) {
        // Some browsers don't support permissions API, continue anyway
        console.log('Permissions API not supported or error:', permErr);
      }
    }

    // Stop any existing scanner first
    if (html5QrCode) {
      try {
        await html5QrCode.stop();
        html5QrCode.clear();
      } catch (e) {
        // Ignore errors when stopping non-started scanner
      }
    }

    // Wait for DOM to render the qr-reader element
    await new Promise(resolve => setTimeout(resolve, 300));

    // Check if element exists
    const element = document.getElementById('qr-reader');
    if (!element) {
      throw new Error('Elemento del lector QR no encontrado en el DOM');
    }

    // Initialize QR Code scanner
    html5QrCode = new Html5Qrcode("qr-reader");
    
    // Get available cameras
    const devices = await Html5Qrcode.getCameras();
    
    if (!devices || devices.length === 0) {
      throw new Error('No se encontraron cámaras en este dispositivo');
    }

    // Find back camera or use the last camera (usually back on mobile)
    let cameraId = devices[devices.length - 1].id;
    for (const device of devices) {
      if (device.label && device.label.toLowerCase().includes('back')) {
        cameraId = device.id;
        break;
      }
    }
    
    // Start scanning with specific camera
    await html5QrCode.start(
      cameraId,
      {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
      },
      onScanSuccess,
      onScanFailure
    );
    
    cameraStarted.value = true;
    initializingCamera.value = false;
  } catch (err) {
    let errorMessage = 'Error al iniciar la cámara';
    const errMsg = err?.message || err?.name || String(err) || '';
    
    if (errMsg.includes('NotReadableError') || errMsg.includes('Could not start video source')) {
      errorMessage = 'La cámara está siendo utilizada por otra aplicación. Por favor, cierra otras pestañas o aplicaciones que usen la cámara e intenta de nuevo.';
    } else if (errMsg.includes('NotAllowedError') || errMsg.includes('Permission denied')) {
      errorMessage = 'Permiso de cámara denegado. Por favor, permite el acceso a la cámara en la configuración del navegador.';
    } else if (errMsg.includes('NotFoundError')) {
      errorMessage = 'No se encontró ninguna cámara en este dispositivo.';
    } else if (errMsg) {
      errorMessage = errorMessage + ': ' + errMsg;
    }
    
    error.value = errorMessage;
    scanning.value = false;
    cameraStarted.value = false;
    initializingCamera.value = false;
    console.error('Camera error:', err);
  }
};

// Handle successful QR scan
const onScanSuccess = async (decodedText, decodedResult) => {
  console.log('QR Code detected:', decodedText);
  
  // Stop the scanner
  await stopCamera();
  
  // Fetch user by QR code
  await handleScan(decodedText);
};

// Handle scan failure (not an error, just no QR detected)
const onScanFailure = (error) => {
  // Do nothing - this fires constantly when no QR is detected
};

// Stop camera
const stopCamera = async () => {
  if (html5QrCode && cameraStarted.value) {
    try {
      await html5QrCode.stop();
      html5QrCode.clear();
      cameraStarted.value = false;
    } catch (err) {
      console.error('Error stopping camera:', err);
    }
  }
};

// Handle QR Code Scan
const handleScan = async (qrCode) => {
  scanning.value = true;
  error.value = '';

  try {
    console.log('Scanned QR Code:', qrCode);
    console.log('QR Code length:', qrCode?.length);
    
    // Call API to get user by QR code
    const result = await appStore.fetchUserByQR(qrCode);
    
    console.log('API Result:', result);
    
    if (result.success) {
      scannedUser.value = result.data;
      // Auto-focus on manual points input after successful scan
      setTimeout(() => {
        if (manualPointsInput.value) {
          manualPointsInput.value.focus();
        }
      }, 300);
    } else {
      throw new Error(result.message || 'Failed to scan QR code');
    }
  } catch (err) {
    console.error('Scan error details:', err);
    console.error('Error response:', err.response);
    error.value = 'Error al obtener datos del usuario: ' + (err.message || 'Error desconocido');
  } finally {
    scanning.value = false;
  }
};

// Auto-start camera on mount
onMounted(() => {
  startCamera();
});

// Cleanup on unmount
onUnmounted(() => {
  stopCamera();
});

// Select points
const selectPoints = (points) => {
  selectedPoints.value = points;
  manualPoints.value = ''; // Clear manual input when selecting preset
};

// Handle manual points input
const handleManualPoints = () => {
  if (manualPoints.value) {
    selectedPoints.value = null; // Clear preset selection when typing manual
  }
};

// Get final points to submit
const getFinalPoints = () => {
  if (manualPoints.value && !isNaN(manualPoints.value)) {
    return parseInt(manualPoints.value);
  }
  return selectedPoints.value;
};

// Submit points
const submitPoints = async () => {
  const pointsToAdd = getFinalPoints();
  
  if (!pointsToAdd || pointsToAdd <= 0) {
    error.value = 'Por favor selecciona o ingresa la cantidad de puntos';
    return;
  }

  if (!scannedUser.value) {
    error.value = 'No hay usuario escaneado';
    return;
  }

  submitting.value = true;
  error.value = '';

  try {
    const result = await appStore.addPointsToUser(
      scannedUser.value.id,
      pointsToAdd,
      remark.value || null
    );

    if (result.success) {
      // Store points added for success view
      addedPoints.value = pointsToAdd;
      scannedUser.value = result.data;
      showSuccess.value = true;
    } else {
      throw new Error(result.message || 'Error al añadir puntos');
    }
  } catch (err) {
    error.value = err.message || 'Error al añadir puntos. Por favor, inténtalo de nuevo.';
    console.error('Submit error:', err);
    submitting.value = false;
  }
};

// Reset scan
const resetScan = async () => {
  scannedUser.value = null;
  error.value = '';
  selectedPoints.value = null;
  manualPoints.value = '';
  remark.value = '';
  submitting.value = false;
  showSuccess.value = false;
  addedPoints.value = 0;
  appStore.clearScannedUser();
  
  // Restart camera
  await startCamera();
};
</script>

<template>
  <div class="scan-page">
    <!-- Page Header -->
    <PageHeader 
      title="Escáner de Código QR"
      subtitle="Escanea códigos QR de clientes y pedidos"
      icon="qr-code-scan"
    />

    <!-- Scanner Container -->
    <div class="scanner-container">
      <!-- Camera View (Real Scanner) -->
      <div v-if="(initializingCamera || cameraStarted) && !scannedUser && !error" class="camera-view">
        <div id="qr-reader" class="qr-reader"></div>
        <div v-if="cameraStarted" class="scanner-overlay">
          <p class="scanner-text">Posiciona el código QR dentro del marco</p>
        </div>
        <div v-else class="scanner-overlay">
          <p class="scanner-text">Iniciando cámara...</p>
        </div>
      </div>

      <!-- User Scanned - Show User Card -->
      <div v-else-if="scannedUser && !showSuccess" class="scan-result success">
        <div class="user-card card-mobile">
          <div class="user-icon">
            <i class="bi bi-person-circle"></i>
          </div>
          <div class="user-info">
            <h3 class="user-name">{{ scannedUser.name }}</h3>
            <p class="user-phone">
              <i class="bi bi-telephone me-1"></i>
              {{ scannedUser.phone }}
            </p>
            <p class="user-points">
              <i class="bi bi-coin text-warning me-1"></i>
              Puntos Actuales: <strong>{{ scannedUser.points }}</strong>
            </p>
          </div>
        </div>

        <!-- Points Buttons -->
        <div class="points-section">
          <h4 class="section-label">Selecciona Puntos a Añadir:</h4>
          <div class="points-buttons">
            <button
              v-for="points in pointsOptions"
              :key="points"
              :class="['points-btn', { active: selectedPoints === points }]"
              @click="selectPoints(points)"
              :disabled="submitting"
            >
              {{ points }}
            </button>
            <!-- Manual Points Input in Grid -->
            <input
              ref="manualPointsInput"
              v-model="manualPoints"
              type="number"
              class="points-btn manual-input"
              placeholder="..."
              min="1"
              @input="handleManualPoints"
              :disabled="submitting"
            />
          </div>
        </div>

        <!-- Remark Textarea -->
        <div class="remark-section">
          <label for="remark" class="form-label">Nota (Opcional)</label>
          <textarea
            id="remark"
            v-model="remark"
            class="form-control"
            rows="3"
            placeholder="Añade una nota sobre esta transacción..."
            :disabled="submitting"
          ></textarea>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="alert alert-danger" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          {{ error }}
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <button
            class="btn-mobile btn-secondary-mobile"
            @click="resetScan"
            :disabled="submitting"
          >
            Cancelar
          </button>
          <button
            class="btn-mobile btn-primary-mobile"
            @click="submitPoints"
            :disabled="(!selectedPoints && !manualPoints) || submitting"
          >
            <span v-if="submitting">
              <span class="spinner-border spinner-border-sm me-2" role="status"></span>
              Enviando...
            </span>
            <span v-else>
              <i class="bi bi-check-lg me-2"></i>
              Enviar
            </span>
          </button>
        </div>
      </div>

      <!-- Success State -->
      <div v-else-if="showSuccess" class="scan-result success">
        <i class="bi bi-check-circle-fill result-icon text-success"></i>
        <h3 class="result-title">¡Puntos Añadidos Exitosamente!</h3>
        
        <div class="success-details">
          <div class="success-card">
            <div class="success-item">
              <span class="success-label">Usuario:</span>
              <span class="success-value">{{ scannedUser.name }}</span>
            </div>
            <div class="success-item">
              <span class="success-label">Puntos Añadidos:</span>
              <span class="success-value text-warning">
                <i class="bi bi-coin me-1"></i>
                +{{ addedPoints }}
              </span>
            </div>
            <div class="success-item">
              <span class="success-label">Total de Puntos:</span>
              <span class="success-value text-success">
                <i class="bi bi-coin me-1"></i>
                {{ scannedUser.points }}
              </span>
            </div>
          </div>
        </div>

        <button class="btn-mobile btn-primary-mobile" @click="resetScan">
          <i class="bi bi-arrow-clockwise me-2"></i>
          Escanear Otro QR
        </button>
      </div>

      <!-- Error State -->
      <div v-else-if="error && !cameraStarted" class="scan-result error">
        <i class="bi bi-x-circle-fill result-icon text-danger"></i>
        <h3 class="result-title">Escaneo Fallido</h3>
        <p class="text-danger">{{ error }}</p>
        <button class="btn-mobile btn-primary-mobile" @click="startCamera">
          Intentar de Nuevo
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.scan-page {
  min-height: 100vh;
  background: #242424;
  padding: 0.85rem 0.65rem;
  padding-bottom: calc(60px + 0.65rem);
}

.scanner-container {
  background: transparent;
  border: none;
  border-radius: 0;
  padding: 0;
  box-shadow: none;
  min-height: 350px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

/* Camera View */
.camera-view {
  width: 100%;
  max-width: 450px;
  position: relative;
}

#qr-reader {
  width: 100%;
  border-radius: 12px;
  overflow: hidden;
  min-height: 300px;
  background: #1a1a1a;
  border: 3px solid #FFD700;
}

#qr-reader video {
  border-radius: 10px;
}

.scanner-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  pointer-events: none;
  padding-bottom: 2rem;
}

.scanner-overlay .scanner-text {
  color: #FFD700;
  font-weight: 600;
  font-size: 0.9rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
  background: rgba(0, 0, 0, 0.75);
  padding: 0.5rem 1rem;
  border-radius: 8px;
}

/* Scanner Idle */
.scanner-idle {
  text-align: center;
  width: 100%;
}

.scanner-icon {
  font-size: 4rem;
  color: #FFD700;
  margin-bottom: 1.2rem;
}

.scanner-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.4rem;
  color: #ffffff;
}

.scanner-description {
  color: #b0b0b0;
  margin-bottom: 1.5rem;
}

/* Scanner Active */
.scanner-active {
  text-align: center;
  width: 100%;
}

.scanner-frame {
  width: 220px;
  height: 220px;
  margin: 0 auto 1.2rem;
  position: relative;
  background: rgba(255, 215, 0, 0.05);
  border: 2px solid #FFD700;
  border-radius: 14px;
  overflow: hidden;
}

.scanner-line {
  position: absolute;
  width: 100%;
  height: 2px;
  background: #FFD700;
  box-shadow: 0 0 10px #FFD700;
  animation: scan 2s linear infinite;
}

@keyframes scan {
  0% { top: 0; }
  100% { top: 100%; }
}

.scanner-corners {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  gap: 0.65rem;
}

.scanner-text {
  font-size: 1.05rem;
  font-weight: 600;
  color: #FFD700;
}

/* Scan Result */
.scan-result {
  text-align: center;
  width: 100%;
}

.user-card {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  padding: 1.2rem;
  background: #1a1a1a;
  border: 2px solid #FFD700;
  margin-bottom: 1.2rem;
}

.user-icon {
  font-size: 3.5rem;
  color: #FFD700;
}

.user-info {
  flex: 1;
  text-align: left;
}

.user-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 0.4rem;
}

.user-phone,
.user-points {
  font-size: 0.85rem;
  color: #b0b0b0;
  margin: 0.2rem 0;
}

.user-points strong {
  color: #FFD700;
  font-size: 0.95rem;
}

.points-section {
  margin-bottom: 1.2rem;
}

.section-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 0.85rem;
  text-align: left;
}

.points-buttons {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.65rem;
}

.points-btn {
  padding: 0.85rem;
  background: #1a1a1a;
  border: 2px solid #333333;
  color: #b0b0b0;
  border-radius: 10px;
  font-size: 1.05rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  box-sizing: border-box;
}

.points-btn:hover {
  border-color: #FFD700;
  color: #FFD700;
  transform: scale(1.05);
}

.points-btn.active {
  background: #FFD700;
  border-color: #FFD700;
  color: #0a0a0a;
  transform: scale(1.05);
}

.points-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.points-btn.manual-input {
  background: #1a1a1a;
  border: 2px solid #333333;
  color: #FFD700;
  font-size: 1.05rem;
  font-weight: 700;
  text-align: center;
  cursor: text;
  padding: 0.85rem 0.5rem;
  width: 100%;
  box-sizing: border-box;
}

.points-btn.manual-input:hover {
  transform: none;
}

.points-btn.manual-input:focus {
  border-color: #FFD700;
  background: #2a2a2a;
  outline: none;
}

.points-btn.manual-input::placeholder {
  color: #666666;
  font-size: 1.2rem;
}

.remark-section {
  margin-bottom: 1.2rem;
  text-align: left;
}

.form-label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 0.4rem;
  display: block;
}

.form-control {
  width: 100%;
  padding: 0.65rem;
  background: #1a1a1a;
  border: 2px solid #333333;
  color: #ffffff;
  border-radius: 8px;
  font-size: 0.85rem;
  resize: vertical;
}

.form-control:focus {
  outline: none;
  border-color: #FFD700;
  background: #2a2a2a;
}

.form-control::placeholder {
  color: #666666;
}

.alert {
  padding: 0.65rem 0.85rem;
  border-radius: 8px;
  margin-bottom: 0.85rem;
  text-align: left;
}

.alert-danger {
  background: rgba(220, 53, 69, 0.1);
  border: 1px solid #dc3545;
  color: #ff6b6b;
}

.action-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.85rem;
}

.btn-secondary-mobile {
  background: #333333;
  color: #ffffff;
  border: 2px solid #333333;
}

.btn-secondary-mobile:hover:not(:disabled) {
  background: #444444;
  border-color: #444444;
}

.btn-secondary-mobile:disabled,
.btn-primary-mobile:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.result-icon {
  font-size: 3.5rem;
  margin-bottom: 0.85rem;
}

.result-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 1.2rem;
  color: #ffffff;
}

.success-details {
  width: 100%;
  margin-bottom: 1.5rem;
}

.success-card {
  background: #1a1a1a;
  border: 2px solid #FFD700;
  border-radius: 12px;
  padding: 1.2rem;
}

.success-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #333333;
}

.success-item:last-child {
  border-bottom: none;
}

.success-label {
  font-size: 0.85rem;
  color: #b0b0b0;
  font-weight: 500;
}

.success-value {
  font-size: 1rem;
  color: #ffffff;
  font-weight: 700;
}

.result-details {
  text-align: left;
  margin-bottom: 1.2rem;
  background: #1a1a1a;
  border: 1px solid #333333;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  padding: 0.65rem 0;
  border-bottom: 1px solid #333333;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-weight: 600;
  color: #b0b0b0;
}

.detail-value {
  font-weight: 600;
  color: #ffffff;
}

@media (min-width: 768px) {
  .scanner-frame {
    width: 260px;
    height: 260px;
  }

  .scanner-icon {
    font-size: 5rem;
  }
}
</style>
