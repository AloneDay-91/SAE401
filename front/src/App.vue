<script setup>
import { RouterLink, useRouter, useRoute } from 'vue-router';
import { useStore } from 'vuex';
import {computed, inject, onBeforeUnmount, onMounted, ref, watch} from 'vue';
import {ArrowLeft, LayoutList, List, ListTodo, ListTree, User, Github} from "lucide-vue-next";

const store = useStore();
const router = useRouter();
const route = useRoute();

const tokenCheckInterval = ref(null);

const isAuthenticated = computed(() => !!store.state.token);
const user = computed(() => store.state.user || {});

// Vérification si nous sommes en mode admin basé sur l'URL
const isAdminMode = ref(false);

const checkIfAdminMode = () => {
    if (typeof window !== 'undefined') {
        const url = window.location.href;
        isAdminMode.value = url.includes('/admin/') ||
            url.includes('/admin/');
    }
};

onMounted(() => {
    checkIfAdminMode();

    store.dispatch('checkTokenExpiration');
    tokenCheckInterval.value = setInterval(() => {
        store.dispatch('checkTokenExpiration');
    }, 60000);

    document.addEventListener('click', handleClickOutside);

});

// Surveiller les changements de route et mettre à jour isAdminMode
watch(() => route.path, () => {
    checkIfAdminMode();
});

onBeforeUnmount(() => {
    if (tokenCheckInterval.value) {
        clearInterval(tokenCheckInterval.value);
    }
});

// Fonction pour se déconnecter
const handleLogout = () => {
    store.dispatch('logout');
    router.push('/connexion');
};

// Vérifier le rôle de l'utilisateur
const getRoles = () => {
    try {
        return JSON.parse(localStorage.getItem('roles') || '[]');
    } catch (error) {
        console.error("Erreur lors de la lecture des rôles depuis localStorage", error);
        return [];
    }
};

const isAdmin = computed(() => getRoles().includes('ROLE_ADMIN'));
const isDelegue = computed(() => getRoles().includes('ROLE_DELEGUE'));

// Récupérer le nom de la page actuelle
const PageName = computed(() => {
    const path = route.path;
    if (path === '/admin/dashboard') return 'Tableau de bord';
    if (path === '/admin/matieres') return 'Matières';
    if (path === '/admin/matieres/new') return 'Nouvelle matière';
    if (path === '/admin/devoirs') return 'Devoirs';
    if (path === '/admin/devoirs/new') return 'Nouveau devoir';
    if (path === '/admin/devoirs/:id') return 'Devoir';
    if (path === '/admin/categories') return 'Catégories';
    if (path === '/admin/categories/new') return 'Nouvelle catégorie';
    if (path === '/admin/classes') return 'Classes';
    if (path === '/admin/classes/new') return 'Nouvelle classe';
    if (path === '/admin/users') return 'Utilisateurs';
    if (path === '/admin/users/new') return 'Nouvel utilisateur';
    if (path === '/admin/settings') return 'Paramètres';
    return 'Page inconnue';
});

const openProfile = ref(false);
const profileMenu = ref(null);

const toggleProfile = () => {
    openProfile.value = !openProfile.value;
};

const handleClickOutside = (event) => {
    const profileButton = document.getElementById('user-menu-button');

    if (
        openProfile.value &&
        profileMenu.value &&
        !profileMenu.value.contains(event.target) &&
        event.target !== profileButton &&
        !profileButton.contains(event.target)
    ) {
        openProfile.value = false;
    }
};

</script>

<template>
    <div v-if="isAdminMode">
        <div class="fixed inset-0 flex">
            <nav class="flex flex-col justify-between w-72 h-full text-black bg-white border border-gray-200 p-4 space-y-2">
                <div class="p-4 space-y-2">
                    <div class="mb-6">
                        <a href="/" class="w-full">
                            <img class="w-full max-w-32" src="@/assets/LOGO.png" alt="Logo">
                        </a>
                    </div>
                    <a href="/admin/dashboard" class="p-2 hover:bg-gray-50 rounded-lg text-xs font-normal hover:text-gray-800 flex items-center text-gray-800 bg-gray-50 border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house mr-2"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                        Accueil
                    </a>
                    <router-link to="/admin/devoirs" class="w-full p-2 hover:bg-gray-50 rounded-lg text-sm font-normal text-gray-400 hover:text-gray-800 flex items-center group">
                        <ListTodo stroke-width="1.5" size="16" class="mr-2" />
                        <span class="flex-1 text-left whitespace-nowrap text-xs">Devoirs</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m1 1 4 4 4-4"></path>
                        </svg>
                    </router-link>
                    <router-link to="/admin/classes" class="w-full p-2 hover:bg-gray-50 rounded-lg text-sm font-normal text-gray-400 hover:text-gray-800 flex items-center group">
                        <List stroke-width="1.5" size="16" class="mr-2" />
                        <span class="flex-1 text-left whitespace-nowrap text-xs">Classes</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m1 1 4 4 4-4"></path>
                        </svg>
                    </router-link>
                    <router-link to="/admin/matieres" class="w-full p-2 hover:bg-gray-50 rounded-lg text-sm font-normal text-gray-400 hover:text-gray-800 flex items-center group">
                        <LayoutList stroke-width="1.5" size="16" class="mr-2" />
                        <span class="flex-1 text-left whitespace-nowrap text-xs">Matières</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m1 1 4 4 4-4"></path>
                        </svg>
                    </router-link>
                    <router-link to="/admin/categories" class="w-full p-2 hover:bg-gray-50 rounded-lg text-sm font-normal text-gray-400 hover:text-gray-800 flex items-center group">
                        <ListTree stroke-width="1.5" size="16" class="mr-2" />
                        <span class="flex-1 text-left whitespace-nowrap text-xs">Catégories</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m1 1 4 4 4-4"></path>
                        </svg>
                    </router-link>
                    <router-link to="/admin/users" class="w-full p-2 hover:bg-gray-50 rounded-lg text-sm font-normal text-gray-400 hover:text-gray-800 flex items-center group">
                        <User stroke-width="1.5" size="16" class="mr-2" />
                        <span class="flex-1 text-left whitespace-nowrap text-xs">Utilisateurs</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m1 1 4 4 4-4"></path>
                        </svg>
                    </router-link>
                </div>
                <div>
                    <div>
                        <div class="p-4 mt-6 rounded-lg bg-gray-50 border border-gray-200" role="alert">
                            <div class="flex items-center mb-3">
                                <span class="bg-orange-100 text-orange-800 text-xs font-light me-2 px-2.5 py-0.5 rounded">Beta</span>
                            </div>
                            <p class="mb-3 text-xs font-light text-gray-600">
                                Le site est en version bêta. Pour toute question ou retour, n'hésitez pas à nous contacter à <a class="underline" href="#">mailtest@mail.com</a>
                            </p>
                        </div>
                    </div>
                    <div>
                        <div class="p-4">
                            <a href="/" class="p-2 hover:bg-gray-50 rounded-lg text-xs font-light text-gray-400 hover:text-gray-800 flex items-center">
                                <ArrowLeft stroke-width="1.5" size="16" class="mr-2" />
                                Retour à l'accueil
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="flex flex-col w-full">
                <div class="w-full py-4 text-black bg-white border-b border-gray-200 px-4 flex items-center justify-between">
                    <div>
                        <p class="text-gray-700 font-light">{{ PageName }}</p>
                    </div>
                    <div>
                        <div class="flex items-center lg:order-2 relative">
                            <!-- Bouton de profil -->
                            <button
                                @click="toggleProfile"
                                class="flex items-center p-2 rounded-lg border border-gray-300 gap-2 origin-bottom-right"
                                aria-haspopup="true"
                                :aria-expanded="openProfile"
                                id="user-menu-button"
                            >
                                <div>
                                    <img
                                        class="h-8 w-8 rounded-lg"
                                        :src="`https://api.dicebear.com/9.x/dylan/svg?seed=${user.nom || 'User'}`"
                                        alt="Avatar de l'utilisateur"
                                    >
                                </div>
                                <div class="text-left">
                                    <div class="flex items-center gap-1">
                                        <span class="block text-sm font-medium text-gray-900">{{ user.prenom || 'Prénom' }}</span>
                                        <span class="block text-sm font-medium text-gray-900">{{ user.nom || 'Nom' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-light text-gray-500">{{user.id_classes?.promo}} {{ user.id_classes?.tp }}</span>
                                    </div>
                                </div>
                            </button>

                            <!-- Menu déroulant -->
                            <transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <div
                                    v-show="openProfile"
                                    ref="profileMenu"
                                    class="z-50 w-48 absolute right-0 top-full mt-2 bg-white rounded-lg shadow-lg divide-y divide-gray-100 border border-[#D8D9E0]"
                                    role="menu"
                                    aria-orientation="vertical"
                                    aria-labelledby="user-menu-button"
                                >
                                    <div class="py-3 px-4">
                                        <span class="block text-sm font-medium text-gray-900 mx-2">{{ user.prenom || 'Prénom' }} {{ user.nom || 'Nom' }}</span>
                                        <span class="block text-sm font-light text-gray-500 truncate mx-2">{{ user.email || 'Email' }}</span>
                                    </div>
                                    <ul class="py-1 text-black">
                                        <li>
                                            <RouterLink
                                                class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                                to="/settings"
                                                role="menuitem"
                                                tabindex="0"
                                            >
                                                Mon profil
                                            </RouterLink>
                                        </li>
                                        <li>
                                            <RouterLink
                                                class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                                to="/devoirs"
                                                role="menuitem"
                                                tabindex="0"
                                            >
                                                Mes devoirs
                                            </RouterLink>
                                        </li>
                                        <li>
                                            <RouterLink
                                                class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                                to="/badges"
                                                role="menuitem"
                                                tabindex="0"
                                            >
                                                Mes badges
                                            </RouterLink>
                                        </li>
                                        <li v-if="isDelegue">
                                            <RouterLink
                                                class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                                to="/classe"
                                                role="menuitem"
                                                tabindex="0"
                                            >
                                                Ma classe
                                            </RouterLink>
                                        </li>
                                        <li v-if="isAdmin">
                                            <RouterLink
                                                class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                                to="/admin/dashboard"
                                                role="menuitem"
                                                tabindex="0"
                                            >
                                                Administration
                                            </RouterLink>
                                        </li>
                                    </ul>
                                    <ul class="py-1 text-gray-500">
                                        <li>
                                            <button
                                                @click="handleLogout"
                                                class="w-full text-left block py-2 px-4 text-sm hover:underline mx-2 text-red-500"
                                                role="menuitem"
                                                tabindex="0"
                                            >
                                                Déconnexion
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
                <div class="w-full flex flex-col text-black bg-white overflow-y-scroll overflow-x-hidden">
                    <router-view></router-view>
                </div>
            </div>
        </div>
    </div>

    <template v-else>
        <header v-if="route.path !== '/connexion' && route.path !== '/connexion/inscription'" class="antialiased">
            <nav class="bg-white border-gray-200 px-4 lg:px-6 py-2.5">
                <div class="flex flex-wrap justify-between items-center">
                    <div class="flex items-center">
                        <RouterLink to="/" class="flex mr-4">
                            <img class="w-32" src="@/assets/LOGO.png" alt="Logo">
                        </RouterLink>
                        <nav v-if="isAuthenticated" class="mr-6">
                            <ul class="flex items-center gap-4 text-sm">
                                <li><RouterLink class="p-2 font-light text-black" to="/">Tableau de bord</RouterLink></li>
                                <li><RouterLink class="p-2 font-light text-gray-400" to="/devoirs">Mes devoirs</RouterLink></li>
                                <li><RouterLink class="p-2 font-light text-gray-400" to="/badges">Mes badges</RouterLink></li>
                                <li><RouterLink class="p-2 font-light text-gray-400" to="/settings">Mon profil</RouterLink></li>
                            </ul>
                        </nav>
                    </div>
                    <div v-if="isAuthenticated" class="flex items-center lg:order-2 relative">
                        <!-- Bouton de profil -->
                        <button
                            @click="toggleProfile"
                            class="flex items-center p-2 rounded-lg border border-gray-300 gap-2 origin-bottom-right"
                            aria-haspopup="true"
                            :aria-expanded="openProfile"
                            id="user-menu-button"
                        >
                            <div>
                                <img
                                    class="h-8 w-8 rounded-lg"
                                    :src="`https://api.dicebear.com/9.x/dylan/svg?seed=${user.nom || 'User'}`"
                                    alt="Avatar de l'utilisateur"
                                >
                            </div>
                            <div class="text-left">
                                <div class="flex items-center gap-1">
                                    <span class="block text-sm font-medium text-gray-900">{{ user.prenom || 'Prénom' }}</span>
                                    <span class="block text-sm font-medium text-gray-900">{{ user.nom || 'Nom' }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-light text-gray-500">{{user.id_classes?.promo}} {{ user.id_classes?.tp }}</span>
                                </div>
                            </div>
                        </button>

                        <!-- Menu déroulant -->
                        <transition
                            enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <div
                                v-show="openProfile"
                                ref="profileMenu"
                                class="z-50 w-48 absolute right-0 top-full mt-2 bg-white rounded-lg shadow-lg divide-y divide-gray-100 border border-[#D8D9E0]"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="user-menu-button"
                            >
                                <div class="py-3 px-4">
                                    <span class="block text-sm font-medium text-gray-900 mx-2">{{ user.prenom || 'Prénom' }} {{ user.nom || 'Nom' }}</span>
                                    <span class="block text-sm font-light text-gray-500 truncate mx-2">{{ user.email || 'Email' }}</span>
                                </div>
                                <ul class="py-1 text-black">
                                    <li>
                                        <RouterLink
                                            class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                            to="/settings"
                                            role="menuitem"
                                            tabindex="0"
                                        >
                                            Mon profil
                                        </RouterLink>
                                    </li>
                                    <li>
                                        <RouterLink
                                            class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                            to="/devoirs"
                                            role="menuitem"
                                            tabindex="0"
                                        >
                                            Mes devoirs
                                        </RouterLink>
                                    </li>
                                    <li>
                                        <RouterLink
                                            class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                            to="/badges"
                                            role="menuitem"
                                            tabindex="0"
                                        >
                                            Mes badges
                                        </RouterLink>
                                    </li>
                                    <li v-if="isDelegue">
                                        <RouterLink
                                            class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                            to="/classe"
                                            role="menuitem"
                                            tabindex="0"
                                        >
                                            Ma classe
                                        </RouterLink>
                                    </li>
                                    <li v-if="isAdmin">
                                        <RouterLink
                                            class="block py-2 px-4 text-sm font-light hover:bg-gray-300/50 rounded-lg mx-2"
                                            to="/admin/dashboard"
                                            role="menuitem"
                                            tabindex="0"
                                        >
                                            Administration
                                        </RouterLink>
                                    </li>
                                </ul>
                                <ul class="py-1 text-gray-500">
                                    <li>
                                        <button
                                            @click="handleLogout"
                                            class="w-full text-left block py-2 px-4 text-sm hover:underline mx-2 text-red-500"
                                            role="menuitem"
                                            tabindex="0"
                                        >
                                            Déconnexion
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </transition>
                    </div>
                </div>
            </nav>
        </header>
        <RouterView class="min-h-screen" />
        <footer v-if="route.path !== '/connexion' && route.path !== '/connexion/inscription'" class="bg-white w-full shadow sm:flex sm:items-center sm:justify-between p-4 sm:p-6 xl:px-8 xl:py-6 antialiased">
            <p class="mb-4 text-sm text-center text-[#00473e] sm:mb-0">
                © 2025 <a href="https://flowbite.com/" class="hover:underline" target="_blank">IUT Troyes</a>.
            </p>
            <div class="flex justify-center items-center space-x-1">
                <a href="/contact" data-tooltip-target="tooltip-facebook" class="inline-flex justify-center p-2  text-[#475d5b] rounded-lg cursor-pointer hover:text-[#faae2b]">
                    <span class="text-sm">Contact</span>
                </a>
                <a href="https://github.com/AloneDay-91/SAE401">
                    <Github stroke-width="1.5" size="20" color="#475d5b" />
                </a>
            </div>
        </footer>
    </template>
</template>

<style scoped>
</style>
