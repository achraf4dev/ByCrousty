/**
 * ByCrousty Client Application
 * Main entry point
 */

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

// Import global styles
import './assets/styles.css';

// Create and mount the Vue app
const app = createApp(App);

// Use router
app.use(router);

// Mount app
app.mount('#app');
