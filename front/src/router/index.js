import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import store from '../store'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: HomeView, meta: { requiresAuth: true } },
    { path: '/connexion', name: 'connexion', component: () => import('../views/ConnexionView.vue')},
    { path: '/inscription', name: 'inscription', component: () => import('../views/RegisterView.vue')},
    { path: '/settings', name: 'settings', component: () => import('../views/SettingsView.vue'), meta: { requiresAuth: true }},
    { path: "/mot-de-passe-oublie", name: "ForgotPassword", component: () => import("../views/ForgotPasswordView.vue")},
    { path: "/reset-password", name: "ResetPassword", component: () => import("../views/ResetPasswordView.vue")},
  ],
})

// Gestion des accès avant chaque navigation
router.beforeEach(async (to, from, next) => {
  const isAuthenticated = !!store.state.token;

  try {
    const isTokenExpired = await store.dispatch('checkTokenExpiration');
    if (isTokenExpired) {
      await store.dispatch('logout');
      return next('/connexion');
    }

    if (!isAuthenticated && to.meta.requiresAuth) {
      return next('/connexion');
    }

    if (isAuthenticated && ['/connexion', '/reset-password', '/mot-de-passe-oublie', '/inscription'].includes(to.path)) {
      return next('/');
    }

    return next();
  } catch (error) {
    console.error('Erreur dans le garde de navigation:', error);
    return next('/connexion');
  }
});

export default router;
