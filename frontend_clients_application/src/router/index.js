/**
 * Vue Router Configuration
 * Defines all routes and navigation guards for the ByCrousty client app
 */

import { createRouter, createWebHistory } from 'vue-router';

// Import pages
import WelcomePage from '../pages/WelcomePage.vue';
import HomePage from '../pages/HomePage.vue';
import CategoriesPage from '../pages/CategoriesPage.vue';
import ProductsPage from '../pages/ProductsPage.vue';
import ProductDetailsPage from '../pages/ProductDetailsPage.vue';
import LoginPage from '../pages/LoginPage.vue';
import RegisterPage from '../pages/RegisterPage.vue';
import CartPage from '../pages/CartPage.vue';
import ProfilePage from '../pages/ProfilePage.vue';

// Define routes
const routes = [
  {
    path: '/',
    redirect: () => {
      // Check if user has seen welcome page
      const hasSeenWelcome = localStorage.getItem('has_seen_welcome');
      return hasSeenWelcome ? '/home' : '/welcome';
    },
  },
  {
    path: '/welcome',
    name: 'Welcome',
    component: WelcomePage,
    meta: { requiresAuth: false },
  },
  {
    path: '/home',
    name: 'Home',
    component: HomePage,
    meta: { requiresAuth: false },
  },
  {
    path: '/categories',
    name: 'Categories',
    component: CategoriesPage,
    meta: { requiresAuth: false },
  },
  {
    path: '/products',
    name: 'Products',
    component: ProductsPage,
    meta: { requiresAuth: false },
  },
  {
    path: '/products/:id',
    name: 'ProductDetails',
    component: ProductDetailsPage,
    meta: { requiresAuth: false },
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { requiresAuth: false, guestOnly: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterPage,
    meta: { requiresAuth: false, guestOnly: true },
  },
  {
    path: '/cart',
    name: 'Cart',
    component: CartPage,
    meta: { requiresAuth: false }, // Cart works for both guests and authenticated users
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage,
    meta: { requiresAuth: true }, // Profile requires authentication
  },
  // Catch-all redirect to home
  {
    path: '/:pathMatch(.*)*',
    redirect: '/home',
  },
];

// Create router instance
const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  },
});

// Navigation guards
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token');
  const isAuthenticated = !!token;

  // Check if route requires authentication
  if (to.meta.requiresAuth && !isAuthenticated) {
    // Redirect to login if not authenticated
    next({
      path: '/login',
      query: { redirect: to.fullPath }, // Save intended destination
    });
  }
  // Check if route is guest-only (login, register)
  else if (to.meta.guestOnly && isAuthenticated) {
    // Redirect to home if already authenticated
    next('/home');
  }
  // Allow navigation
  else {
    next();
  }
});

// After each navigation
router.afterEach((to, from) => {
  // Update document title
  const baseTitle = 'ByCrousty';
  document.title = to.name ? `${to.name} - ${baseTitle}` : baseTitle;
});

export default router;
