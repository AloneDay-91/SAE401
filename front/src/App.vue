<script setup>
import { RouterLink, useRouter } from 'vue-router';
import { useStore } from 'vuex';
import {computed, markRaw, onBeforeUnmount, onMounted, ref} from 'vue';
import axios from "axios";

const store = useStore();
const router = useRouter();

const openProfile = ref(false);
const tokenCheckInterval = ref(null);

const isAuthenticated = computed(() => !!store.state.token);
const user = computed(() => store.state.user || {}); // Eviter des erreurs si user est undefined

onMounted(() => {
    store.dispatch('checkTokenExpiration');
    tokenCheckInterval.value = setInterval(() => {
        store.dispatch('checkTokenExpiration');
    }, 60000);
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

const userEmail = localStorage.getItem('email');

</script>

<template>
    <header class="antialiased">
        <nav class="bg-white border-gray-200 px-4 lg:px-6 py-2.5">
            <div class="flex flex-wrap justify-between items-center">
                <div class="flex items-center">
                    <RouterLink to="/" class="flex mr-4">LOGO</RouterLink>
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
                    <button @click="openProfile = !openProfile" class="flex items-center p-2 rounded-lg border border-gray-300 gap-2">
                        <div>
                            <img class="h-8 w-8 rounded-lg" :src="`https://api.dicebear.com/9.x/dylan/svg?seed=${user.nom || 'User'}`" alt="Avatar">
                        </div>
                        <div class="text-left">
                            <div class="flex items-center gap-1">
                                <span class="block text-sm font-medium text-gray-900">{{ user.prenom || 'Prénom' }}</span>
                                <span class="block text-sm font-medium text-gray-900">{{ user.nom || 'Nom' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-light text-gray-500">{{user.id_classes.promo}} {{ user.id_classes.tp }}</span>
                            </div>
                        </div>
                    </button>
                    <div v-show="openProfile" class="z-50 w-48 absolute right-0 top-15 bg-white rounded-lg shadow-lg divide-y divide-gray-100 border border-[#D8D9E0]">
                        <div class="py-3 px-4">
                            <span class="block text-sm font-medium text-gray-900 mx-2">{{ user.nom || 'Nom' }} {{ user.prenom || 'Prénom' }}</span>
                            <span class="block text-sm font-light text-gray-500 truncate mx-2">{{ userEmail || 'Email' }}</span>
                        </div>
                        <ul class="py-1 text-black">
                            <li><RouterLink class="block py-2 px-4 text-sm font-light hover:bg-[#00DF82] rounded-lg mx-2" to="/settings">Mon profil</RouterLink></li>
                            <li><RouterLink class="block py-2 px-4 text-sm font-light hover:bg-[#00DF82] rounded-lg mx-2" to="/devoirs">Mes devoirs</RouterLink></li>
                            <li><RouterLink class="block py-2 px-4 text-sm font-light hover:bg-[#00DF82] rounded-lg mx-2" to="/badges">Mes badges</RouterLink></li>
                            <li v-if="isDelegue"><RouterLink class="block py-2 px-4 text-sm font-light hover:bg-[#00DF82] rounded-lg mx-2" to="/classe">Ma classe</RouterLink></li>
                            <li v-if="isAdmin"><RouterLink class="block py-2 px-4 text-sm font-light hover:bg-[#00DF82] rounded-lg mx-2" to="/admin/dashboard">Administration</RouterLink></li>
                        </ul>
                        <ul class="py-1 text-gray-500">
                            <li><button @click="handleLogout" class="block py-2 px-4 text-sm hover:underline mx-2 text-red-500">Déconnexion</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <RouterView />
</template>

<style scoped>
</style>
