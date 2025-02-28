import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import useStore from '../store/index.js'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/connexion', name: 'connexion', component: () => import('../views/ConnexionView.vue')},
    { path: '/inscription', name: 'inscription', component: () => import('../views/RegisterView.vue')},
    { path: '/settings', name: 'settings', component: () => import('../views/SettingsView.vue'), meta: { requiresAuth: true }},
    { path: "/mot-de-passe-oublie", name: "ForgotPassword", component: () => import("../views/ForgotPasswordView.vue")},
    { path: "/reset-password", name: "ResetPassword", component: () => import("../views/ResetPasswordView.vue")},
  ],
})

// 🔥 Gestion des accès avant chaque navigation
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!useStore.state.token; // Vérifie si l'utilisateur est connecté

  // 🔹 Si l'utilisateur N'EST PAS connecté et essaie d'accéder à une page publique (y compris '/')
  if (!isAuthenticated && !['/connexion', '/mot-de-passe-oublie', '/reset-password', '/inscription'].includes(to.path)) {
    next('/connexion');
  }
  // 🔹 Si l'utilisateur EST connecté et tente d'aller sur une page d'authentification
  else if (isAuthenticated && ['/connexion', '/reset-password', '/mot-de-passe-oublie'].includes(to.path)) {
    next('/');
  }
  else {
    next(); // Autorise la navigation
  }
});

export default router;
