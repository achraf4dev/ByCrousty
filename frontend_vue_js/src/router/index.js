import { createRouter, createWebHistory } from 'vue-router';
import { isAuthenticated } from '../store/auth';

// Import Pages
import LoginPage from '../pages/LoginPage.vue';
import HomePage from '../pages/HomePage.vue';
import ScanPage from '../pages/ScanPage.vue';
import NotificationsPage from '../pages/NotificationsPage.vue';
import SettingsPage from '../pages/SettingsPage.vue';

// Define Routes
const routes = [
  {
    path: '/',
    redirect: '/home',
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { requiresAuth: false },
  },
  {
    path: '/home',
    name: 'Home',
    component: HomePage,
    meta: { requiresAuth: true },
  },
  {
    path: '/scan',
    name: 'Scan',
    component: ScanPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/notifications',
    name: 'Notifications',
    component: NotificationsPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/settings',
    name: 'Settings',
    component: SettingsPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/home',
  },
];

// Create Router
const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation Guard
router.beforeEach((to, from, next) => {
  const requiresAuth = to.meta.requiresAuth;
  const authenticated = isAuthenticated();

  if (requiresAuth && !authenticated) {
    // Redirect to login if authentication is required
    next({ name: 'Login' });
  } else if (to.name === 'Login' && authenticated) {
    // Redirect to home if already authenticated
    next({ name: 'Home' });
  } else {
    next();
  }
});

export default router;
