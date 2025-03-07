import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import store from '../store';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: HomeView, meta: { requiresAuth: true } },

    // AUTH
    { path: '/connexion', name: 'connexion', component: () => import('../views/Auth/ConnexionView.vue') },
    { path: '/inscription', name: 'inscription', component: () => import('../views/Auth/RegisterView.vue') },
    { path: '/mot-de-passe-oublie', name: 'ForgotPassword', component: () => import('../views/Auth/ForgotPasswordView.vue') },
    { path: '/reset-password', name: 'ResetPassword', component: () => import('../views/Auth/ResetPasswordView.vue') },

    // PROFILE
    { path: '/settings', name: 'settings', component: () => import('../views/Profile/SettingsView.vue'), meta: { requiresAuth: true } },
    { path: '/badges', name: 'Badges', component: () => import('../views/Badges/BadgesView.vue') },

    // DEVOIRS
    { path: '/devoirs', name: 'Devoirs', component: () => import('../views/Devoirs/DevoirsView.vue') },
    { path: '/devoirs/new', name: 'DevoirsNew', component: () => import('../views/Devoirs/DevoirsNewView.vue') },

    // ADMIN
    { path: '/admin/dashboard', name: 'AdminDashboard', component: () => import('../views/Admin/AdminDashboardView.vue'), meta: { requiresAdminRole: true } },

    // ADMIN MATIERES
    { path: '/admin/matieres/new', name: 'MatiereNew', component: () => import('../views/Admin/Matieres/MatieresNewView.vue'), meta: { requiresAdminRole: true } },
    { path: '/admin/matieres', name: 'Matieres', component: () => import('../views/Admin/Matieres/MatieresView.vue'), meta: { requiresAdminRole: true } },
  ],
});

// Garde de navigation globale
router.beforeEach(async (to, from, next) => {
  const isAuthenticated = !!store.state.token; // Vérifie si l'utilisateur est authentifié
  const userRoles = store.state.user?.roles || []; // Récupère les rôles de l'utilisateur (par défaut, tableau vide)

  try {
    // Vérifie si le token est expiré
    const isTokenExpired = await store.dispatch('checkTokenExpiration');
    if (isTokenExpired) {
      await store.dispatch('logout');
      return next('/connexion');
    }

    // Redirige vers la page de connexion si une route nécessite une authentification et que l'utilisateur n'est pas authentifié
    if (to.meta.requiresAuth && !isAuthenticated) {
      return next('/connexion');
    }

    // Empêche un utilisateur authentifié d'accéder aux pages d'inscription ou de connexion
    if (isAuthenticated && ['/connexion', '/reset-password', '/mot-de-passe-oublie', '/inscription'].includes(to.path)) {
      return next('/');
    }

    // Vérifie si la route nécessite un rôle admin
    if (to.meta.requiresAdminRole && !userRoles.includes('ROLE_ADMIN')) {
      return next('/'); // Redirige vers la page d'accueil si l'utilisateur n'a pas le rôle admin
    }

    return next(); // Autorise la navigation
  } catch (error) {
    console.error('Erreur dans le garde de navigation:', error);
    return next('/connexion'); // Redirige vers la page de connexion en cas d'erreur
  }
});

export default router;
