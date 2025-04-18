<script setup>
import { RouterLink, useRouter } from 'vue-router';
import { useStore } from 'vuex';
import {computed, inject, onBeforeUnmount, onMounted, ref, watch} from 'vue';
import {CircleUser, Book, Trash2, FilePenLine, ArrowLeft, ArrowRight, Ellipsis} from 'lucide-vue-next';
import axios from 'axios';
import DropdownMenu from "@/components/DropdownMenu.vue";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();

const TotalUsers = ref(0);
const TotalDevoirs = ref(0);
const users = ref([]);
const recherche = ref('');
const loading = ref(false);
const userRecherche = ref('');
const isModalOpen = ref(false);
const modifierUser = ref(null);

const openModal = (user) => {
    modifierUser.value = { ...user };
    isModalOpen.value = true;
};
const closeModal = () => {
    isModalOpen.value = false;
};

const TotalUser = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`${API_URL}/users`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        TotalUsers.value = response.data.totalItems;
        users.value = response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des utilisateurs:', e);
    }
};

const utilisateursFiltres = computed(() => {
    if (!userRecherche.value) return users.value;

    const searchTerm = userRecherche.value.toLowerCase();
    return users.value.filter(user => {
        return user.nom.toLowerCase().includes(searchTerm) ||
            user.prenom.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm) ||
            user.id.toString().includes(searchTerm);
    });
});

let timeoutId = null;
watch(userRecherche, (newValue) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        recherche.value = newValue;
    }, 300);
});

const TotalDevoir = async () => {
    try {
        const response = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        TotalDevoirs.value = response.data.totalItems;
    } catch (e) {
        console.error('Erreur lors de la récupération des devoirs:', e);
    }
};

const getRoleLabel = (user) => {
    if (!user.roleapp || user.roleapp.length === 0) return 'Inconnu';

    if (user.roleapp.includes('ROLE_ADMIN')) return 'Administrateur';
    if (user.roleapp.includes('ROLE_USER')) return 'Utilisateur';
    if (user.roleapp.includes('ROLE_PROFESSEUR')) return 'Professeur';
    if (user.roleapp.includes('ROLE_ELEVE')) return 'Etudiant';

    return 'Inconnu';
};

const CouleurRoles = (role) => {
    if (role === 'ROLE_ADMIN') return 'bg-purple-200/50 text-purple-500/70';
    if (role === 'ROLE_PROFESSEUR') return 'bg-green-200/50 text-green-500/70';
    if (role === 'ROLE_ELEVE') return 'bg-blue-200/50 text-blue-500/70';
    return 'bg-gray-200/50';
};

onMounted(() => {
    TotalUser();
    TotalDevoir();
});

const updateUser = async () => {
    if (!modifierUser.value) return;

    try {
        // Créer un nouvel objet avec seulement les propriétés à mettre à jour
        const userData = JSON.stringify({
            nom: modifierUser.value.nom,
            prenom: modifierUser.value.prenom,
            email: modifierUser.value.email,
            roleapp: modifierUser.value.roleapp
        });

        await axios.patch(`${API_URL}/users/${modifierUser.value.id}`, userData, {
            headers: {
                "Content-Type": "application/merge-patch+json",
                "Authorization": `Bearer ${store.state.token}`
            }
        });

        // Mettre à jour l'utilisateur dans le tableau local
        const index = users.value.findIndex(u => u.id === modifierUser.value.id);
        if (index !== -1) {
            users.value[index] = { ...modifierUser.value };
        }

        closeModal();

    } catch (error) {
        console.error('Erreur lors de la mise à jour de l\'utilisateur:', error);
    }
};


</script>

<template>
    <div class="flex items-center justify-between text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div>
            <p class="text-gray-800 font-semibold">Liste des utilisateurs</p>
            <div class="mt-1 text-sm text-gray-500">Gérer les différents utilisateurs</div>
        </div>
        <div class="">
            <Button variant="solid" size="small" tag="a" href="/admin/users/new">Ajouter un utilisateur</Button>
        </div>
    </div>

    <div class="mx-4 my-4">
        <section>
            <div>
                <div>
                    <div class="max-w-full">
                        <div class="mx-auto w-full">
                            <div class="">
                                <div class="">
                                    <div class="gap-4 flex flex-col">
                                        <div>
                                            <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                                <div class="w-full">
                                                    <div class="w-full mb-2">
                                                        <label for="search" class="sr-only">Search</label>
                                                        <div class="relative w-full">
                                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </div>
                                                            <input v-model="userRecherche" type="text" name="search" id="search" class="w-full max-w-80 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block pl-10 p-2" placeholder="Rechercher..." value="">
                                                        </div>
                                                    </div>
                                                    <div class="max-w-full border border-gray-200 rounded-lg">
                                                        <div class="relative sm:rounded-lg">
                                                            <div class="">
                                                                <table class="w-full text-left">
                                                                    <thead class="uppercase border-b border-gray-200 bg-gray-200/30">
                                                                    <tr>
                                                                        <th scope="col" class="px-4 py-3 text-gray-500 text-xs font-normal">ID</th>
                                                                        <th scope="col" class="px-4 py-3 text-gray-500 text-xs font-normal">Nom & prénom</th>
                                                                        <th scope="col" class="px-4 py-3 text-gray-500 text-xs font-normal">Email</th>
                                                                        <th scope="col" class="px-4 py-3 text-gray-500 text-xs font-normal">Rôles</th>
                                                                        <th scope="col" class="px-4 py-3 text-gray-500 text-xs font-normal">Classe</th>
                                                                        <th scope="col" class="px-4 py-3 text-gray-500 text-xs font-normal">Actions</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tr v-for="user in utilisateursFiltres" :key="user.id" class="border-b border-gray-200">
                                                                        <td class="px-4 py-4 text-gray-500 text-xs font-normal">{{ user.id }}</td>
                                                                        <td class="px-4 py-4 text-gray-500 text-xs font-normal">{{ user.nom }} {{ user.prenom }}</td>
                                                                        <td class="px-4 py-4 text-gray-500 text-xs font-normal">{{ user.email }}</td>
                                                                        <td class="px-4 py-4 text-gray-500 text-xs font-normal">
                                                                                        <span class="rounded px-2 py-1" :class="CouleurRoles(user.roleapp)">
                                                                                            {{ getRoleLabel(user) }}
                                                                                        </span>
                                                                        </td>
                                                                        <td class="px-4 py-4 text-gray-500 text-xs font-normal">{{ user.promo }} {{ user.td }} {{ user.tp }}</td>
                                                                        <td class="px-4 py-4 text-xs font-normal flex items-center gap-2">
                                                                            <DropdownMenu>
                                                                                <!-- Personnalisation du bouton déclencheur -->
                                                                                <template #trigger>
                                                                                    <Button class="inline-flex hover:cursor-pointer" variant="ghost" size="small">
                                                                                        <Ellipsis stroke-width="1.5" size="16" />
                                                                                    </Button>
                                                                                </template>

                                                                                <div class="px-2">
                                                                                    <button @click="openModal(user)" class="py-2 flex items-center justify-between w-full text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                                                                        <span>Modifier</span>
                                                                                        <FilePenLine stroke-width="1.5" size="16"/>
                                                                                    </button>
                                                                                    <hr class="text-gray-200">
                                                                                    <router-link :to="`users/${user.id}/delete`" class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                                                                        <span class="text-red-600">Supprimer</span>
                                                                                        <Trash2 stroke-width="1.5" size="16"/>
                                                                                    </router-link>
                                                                                </div>
                                                                            </DropdownMenu>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                            <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                                <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                                <h2 class="text-lg font-light mb-4">Modifier un utilisateur</h2>
                                <div>

                                    <div class="">
                                        <form @submit.prevent="updateUser" class="px-4 py-5 sm:px-6">
                                            <div class="mb-6">
                                                <label for="nom" class="block mb-2 text-sm font-medium text-gray-900">Nom</label>
                                                <input type="text" id="nom" name="nom" v-model="modifierUser.nom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="Votre nom" required>
                                            </div>
                                            <div class="mb-6">
                                                <label for="prenom" class="block mb-2 text-sm font-medium text-gray-900">Prénom</label>
                                                <input type="text" id="prenom" name="prenom" v-model="modifierUser.prenom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="Votre prénom" required>
                                            </div>
                                            <div class="mb-6">
                                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Adresse mail</label>
                                                <input type="text" id="email" name="email" v-model="modifierUser.email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="Votre adresse mail" required>
                                            </div>
                                            <div class="mb-6">
                                                <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Rôle</label>
                                                <select v-model="modifierUser.roleapp" id="role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                                                    <option value="ROLE_PROFESSEUR">Professeur</option>
                                                    <option value="ROLE_ELEVE">Etudiant</option>
                                                    <option value="ROLE_ADMIN">Administrateur</option>
                                                </select>
                                            </div>
                                            <Button variant="solid" size="small" type="submit">Modifier</Button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center w-full justify-between gap-8 mt-24">
                        <router-link to="/admin/categories" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-start">
                                    <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
                                    <div class="text-left">
                                        <p class="text-gray-800 font-normal text-sm">Liste des catégories</p>
                                        <div class="text-xs text-gray-500 font-light">Gérer les différentes catégories</div>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                        <router-link to="/admin/users/new" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-end">
                                    <div class="text-right">
                                        <p class="text-gray-800 font-normal text-sm">Nouvel utilisateur</p>
                                        <div class="text-xs text-gray-500 font-light">Ajouter un nouvel utilisateur</div>
                                    </div>
                                    <ArrowRight stroke-width="1.5" size="24" class="mr-2" />
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>

</style>