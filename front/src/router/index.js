import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import ConnexionView from '../views/Auth/ConnexionView.vue'; // Import direct
import store from '../store';
import ProfileView from "@/views/Profile/ProfileView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: HomeView, meta: { requiresAuth: true } },

    // AUTH
    {
      path: '/connexion',
      component: ConnexionView,
      children: [
        { path: '', name: 'connexion', component: () => import('../views/Auth/FormLoginView.vue') },
        { path: '/inscription', name: 'inscription', component: () => import('../views/Auth/RegisterView.vue') },
        { path: '/mot-de-passe-oublie', name: 'ForgotPassword', component: () => import('../views/Auth/ForgotPasswordView.vue') },
        { path: '/reset-password', name: 'ResetPassword', component: () => import('../views/Auth/ResetPasswordView.vue') },
      ],
    },

    // PROFILE
    {
      path: '/profil',
      component: ProfileView,
      meta: { requiresAuth: true },
      children: [
        { path: '', name: 'profil', component: () => import('../views/Profile/ProfilesViews/ProfileInfosView.vue') },
        { path: 'classe', name: 'classe', component: () => import('../views/Profile/ProfilesViews/ProfileClasseView.vue') },
        { path: 'devoirs', name: 'devoirs', component: () => import('../views/Profile/ProfilesViews/ProfileDevoirsView.vue') },
        { path: 'settings', name: 'settings', component: () => import('../views/Profile/ProfilesViews/ProfileSettingsView.vue') },
      ],
    },

    // BADGES
    { path: '/succes', name: 'Succes', component: () => import('../views/Badges/BadgesView.vue'), meta: { requiresAuth: true } },

    // DEVOIRS
    { path: '/devoirs', name: 'Devoirs', component: () => import('../views/Devoirs/DevoirsView.vue'), meta: { requiresAuth: true } },
    { path: '/devoirs/new', name: 'DevoirsNew', component: () => import('../views/Devoirs/DevoirsNewView.vue'), meta: { requiresAuth: true } },

    // ADMIN
    { path: '/admin/dashboard', name: 'AdminDashboard', component: () => import('../views/Admin/AdminDashboardView.vue'), meta: { requiresAdminRole: true, requiresAuth: true} },

    // ADMIN MATIERES
    { path: '/admin/matieres/new', name: 'AdminMatiereNew', component: () => import('../views/Admin/Matieres/MatieresNewView.vue'), meta: { requiresAdminRole: true, requiresAuth: true } },
    { path: '/admin/matieres', name: 'AdminMatieres', component: () => import('../views/Admin/Matieres/MatieresView.vue'), meta: { requiresAdminRole: true, requiresAuth: true } },

    // ADMIN DEVOIRS
    { path: '/admin/devoirs/new', name: 'AdminDevoirsNew', component: () => import('../views/Admin/Devoirs/DevoirsNewView.vue'), meta: { requiresAdminRole: true, requiresAuth: true } },
    { path: '/admin/devoirs/:id', name: 'AdminDevoirsAllView', component: () => import('../views/Admin/Devoirs/DevoirsAllView.vue'), meta: { requiresAdminRole: true, requiresAuth: true } },
    { path: '/admin/devoirs', name: 'AdminDevoirs', component: () => import('../views/Admin/Devoirs/DevoirsView.vue'), meta: { requiresAdminRole: true, requiresAuth: true } },

    // ADMIN USERS
    // { path: '/admin/users/new', name: 'AdminUsersNew', component: () => import('../views/Admin/_formUsersView.vue'), meta: { requiresAdminRole: true } },
    { path: '/admin/users', name: 'AdminUsers', component: () => import('../views/Admin/Users/UsersView.vue'), meta: { requiresAdminRole: true, requiresAuth: true } },

    // ADMIN CATEGORIES
    { path: '/admin/categories', name: 'AdminCategories', component: () => import('../views/Admin/Categories/CategoriesView.vue'), meta: { requiresAdminRole: true, requiresAuth: true } },

    // ADMIN CLASSES
    { path: '/admin/classes', name: 'AdminClasses', component: () => import('../views/Admin/Classes/ClassesView.vue'), meta: { requiresAdminRole: true, requiresAuth: true } },
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
    if (isAuthenticated && ['/connexion'].includes(to.path)) {
      return next('/');
    }

    // Vérifie si la route nécessite un rôle admin
    if (to.meta.requiresAdminRole && !userRoles.includes('ROLE_ADMIN')) {
      return next('/'); // Redirige vers la page d'accueil si l'utilisateur n'a pas le rôle admin
    }

    if (to.path === '/admin') {
      if (userRoles.includes('ROLE_ADMIN')) return next();
      return next('/admin/dashboard');
    }

    return next(); // Autorise la navigation
  } catch (error) {
    console.error('Erreur dans le garde de navigation:', error);
    return next('/connexion'); // Redirige vers la page de connexion en cas d'erreur
  }
});

export default router;
