<script setup>
import {RouterLink} from 'vue-router';
import { useStore } from 'vuex';
import {computed, inject, onMounted, ref} from 'vue';
import {
    Trash2,
    FilePenLine,
    ArrowLeft,
    ArrowRight,
    Ellipsis,
    Search,
    ChevronLeft, ChevronRight
} from 'lucide-vue-next';
import axios from 'axios';
import DropdownMenu from "@/components/DropdownMenu.vue";
import Button from "@/components/Button.vue";
import {pulsar} from 'ldrs'

pulsar.register()

const triggerToast = inject('triggerToast');

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();

const TotalUsers = ref(0);
const TotalDevoirs = ref(0);
const users = ref([]);
const loading = ref(false);
const isModalOpen = ref(false);
const modifierUser = ref(null);
const isModalDeleteOpen = ref(false);

// Variables pour la recherche, le filtrage et la pagination
const searchQuery = ref('');
const selectedFilters = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Pagination calculée
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredUsers.value.length));
const totalPages = computed(() => Math.ceil(filteredUsers.value.length / itemsPerPage.value));

// Filtrage des classes
const filteredUsers = computed(() => {
    return users.value.filter((user) => {
        const matchesFilters =
            selectedFilters.value.every((filter) =>
                [user.nom].includes(filter) || [user.prenom].includes(filter) || [user.email].includes(filter) || [user.promo].includes(filter) || [user.td].includes(filter) || [user.tp].includes(filter) || [user.roleapp].includes(filter)
            );

        const matchesSearch =
            searchQuery.value === '' ||
            Object.values(user).some((value) =>
                String(value).toLowerCase().includes(searchQuery.value.toLowerCase())
            );

        return matchesFilters && matchesSearch;
    });
});

const pageRange = computed(() => {
    const range = [];
    const maxVisiblePages = 10;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisiblePages / 2));
    const end = Math.min(start + maxVisiblePages - 1, totalPages.value);

    if (end - start + 1 < maxVisiblePages) {
        start = Math.max(1, end - maxVisiblePages + 1);
    }

    for (let i = start; i <= end; i++) {
        range.push(i);
    }
    return range;
});

// Pagination des données filtrées
const paginatedUsers = computed(() => filteredUsers.value.slice(startIndex.value, endIndex.value));

// Gestion des filtres
const addFilter = (filter) => {
    if (!selectedFilters.value.includes(filter)) {
        selectedFilters.value.push(filter);
    }
};

const removeFilter = (filter) => {
    selectedFilters.value = selectedFilters.value.filter((f) => f !== filter);
};

// Navigation entre les pages
const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

const openModal = (user) => {
    modifierUser.value = { ...user };
    isModalOpen.value = true;
};
const closeModal = () => {
    isModalOpen.value = false;
};

const openDeleteModal = () => {
    isModalDeleteOpen.value = true;
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
        loading.value = false;
    } catch (e) {
        triggerToast("Erreur lors de la récupération des utilisateurs", "Une erreur s'est produite lors de la récupération des utilisateurs.", 'error');
        console.error('Erreur lors de la récupération des utilisateurs:', e);
        loading.value = false;
    }
};

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
        triggerToast("Erreur lors de la récupération des devoirs", "Une erreur s'est produite lors de la récupération des devoirs.", 'error');
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
            promo: modifierUser.value.promo,
            td: modifierUser.value.td,
            tp: modifierUser.value.tp,
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
        triggerToast("Utilisateur modifié avec succès", "L'utilisateur a été modifié avec succès.", 'success');
        closeModal();
    } catch (error) {
        triggerToast("Erreur lors de la mise à jour de l'utilisateur", "Une erreur s'est produite lors de la mise à jour de l'utilisateur.", 'error');
        console.error('Erreur lors de la mise à jour de l\'utilisateur:', error);
    }
};

const deleteUser = async (userId) => {
    try {
        await axios.delete(`${API_URL}/users/${userId}`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            }
        });
        users.value = users.value.filter(user => user.id !== userId);
        triggerToast("Utilisateur supprimé avec succès", "L'utilisateur a été supprimé avec succès.", 'success');
    } catch (error) {
        triggerToast("Erreur lors de la suppression de l'utilisateur", "Une erreur s'est produite lors de la suppression de l'utilisateur.", 'error');
        console.error('Erreur lors de la suppression de l\'utilisateur:', error);
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
                                                    <div>
                                                        <div class="flex items-center justify-between gap-4 mb-4">
                                                            <div class="relative w-full max-w-xl">
                                                                <label for="search"
                                                                       class="mb-2 text-sm font-light text-gray-500 sr-only">Rechercher
                                                                    un utilisateur</label>
                                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                                    <Search class="w-4 h-4 text-gray-400"
                                                                            stroke-width="1.5"/>
                                                                </div>
                                                                <input
                                                                        id="search"
                                                                        v-model="searchQuery"
                                                                        type="text"
                                                                        placeholder="Rechercher un utilisateur..."
                                                                        class="pl-10 border border-gray-300 rounded-lg px-2 py-3 w-full text-sm bg-gray-50"
                                                                />
                                                            </div>
                                                            <!-- Listes déroulantes pour sélectionner les filtres -->
                                                            <div class="flex gap-4">
                                                                <select @change="addFilter($event.target.value)"
                                                                        class="border border-gray-300 text-gray-500 font-light text-sm rounded px-2 py-1 w-full bg-gray-50">
                                                                    <option value="" disabled selected>Filtrer par
                                                                        rôle
                                                                    </option>
                                                                    <option value="ROLE_PROFESSEUR">Professeur</option>
                                                                    <option value="ROLE_ELEVE">Etudiant</option>
                                                                    <option value="ROLE_ADMIN">Administrateur</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-col gap-4 mb-4">
                                                            <!-- Affichage des chips -->
                                                            <div class="flex gap-2 flex-wrap">
                                                                <span
                                                                        v-for="filter in selectedFilters"
                                                                        :key="filter"
                                                                        class="text-green-500 bg-green-300/10 border border-green-300 px-2 py-1 font-light text-sm rounded-lg flex items-center gap-2"
                                                                >
                                                                  {{ filter }}
                                                                  <button @click="removeFilter(filter)"
                                                                          class="text-green-500 hover:text-red-500">&times;</button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div v-if="loading" class="w-full text-center">
                                                            <l-pulsar size="40" speed="1.75" color="#05df72"></l-pulsar>
                                                        </div>
                                                        <div v-else>
                                                            <div class="max-w-full border border-gray-200 rounded-lg">
                                                                <div class="relative sm:rounded-lg">
                                                                    <div class="">
                                                                        <table class="w-full text-left">
                                                                            <thead class="uppercase border-b border-gray-200 bg-gray-200/30">
                                                                            <tr>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-gray-500 text-xs font-normal">
                                                                                    ID
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-gray-500 text-xs font-normal">
                                                                                    Nom & prénom
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-gray-500 text-xs font-normal">
                                                                                    Email
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-gray-500 text-xs font-normal">
                                                                                    Rôles
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-gray-500 text-xs font-normal">
                                                                                    Classe
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-gray-500 text-xs font-normal">
                                                                                    Actions
                                                                                </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <tr v-for="user in paginatedUsers"
                                                                                :key="user.id"
                                                                                class="border-b border-gray-200">
                                                                                <td class="px-4 py-4 text-gray-500 text-xs font-normal">
                                                                                    {{ user.id }}
                                                                                </td>
                                                                                <td class="px-4 py-4 text-gray-500 text-xs font-normal">
                                                                                    {{ user.nom }} {{ user.prenom }}
                                                                                </td>
                                                                                <td class="px-4 py-4 text-gray-500 text-xs font-normal">
                                                                                    {{ user.email }}
                                                                                </td>
                                                                                <td class="px-4 py-4 text-gray-500 text-xs font-normal">
                                                                                <span class="rounded px-2 py-1 border">
                                                                                    {{ getRoleLabel(user) }}
                                                                                </span>
                                                                                </td>
                                                                                <td class="px-4 py-4 text-gray-500 text-xs font-normal">
                                                                                    {{ user.promo }} {{ user.td }}
                                                                                    {{ user.tp }}
                                                                                </td>
                                                                                <td class="px-4 py-4 text-xs font-normal flex items-center gap-2">
                                                                                    <DropdownMenu>
                                                                                        <!-- Personnalisation du bouton déclencheur -->
                                                                                        <template #trigger>
                                                                                            <Button class="inline-flex hover:cursor-pointer"
                                                                                                    variant="ghost"
                                                                                                    size="small">
                                                                                                <Ellipsis
                                                                                                        stroke-width="1.5"
                                                                                                        size="16"/>
                                                                                            </Button>
                                                                                        </template>

                                                                                        <div class="px-2">
                                                                                            <button @click="openModal(user)"
                                                                                                    class="py-2 flex items-center justify-between w-full text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                                                                                <span>Modifier</span>
                                                                                                <FilePenLine
                                                                                                        stroke-width="1.5"
                                                                                                        size="16"/>
                                                                                            </button>
                                                                                            <hr class="text-gray-200">
                                                                                            <button
                                                                                                    class="w-full py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1"
                                                                                                    @click="openDeleteModal">
                                                                                                <span class="text-red-600">Supprimer</span>
                                                                                                <Trash2 stroke-width="1.5"
                                                                                                        size="16"/>
                                                                                            </button>
                                                                                            <transition
                                                                                                    name="modal-fade">
                                                                                                <div v-if="isModalDeleteOpen"
                                                                                                     class="fixed inset-0 bg-black/70 flex items-center justify-center">
                                                                                                    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                                                                                                        <h2 class="text-lg mb-2">
                                                                                                            Supprimer
                                                                                                            l'utilisateur</h2>
                                                                                                        <p class="text-xs font-light">
                                                                                                            Êtes-vous
                                                                                                            sûr de
                                                                                                            vouloir
                                                                                                            supprimer
                                                                                                            l'utilisateur
                                                                                                            n°{{
                                                                                                                user.id
                                                                                                            }}
                                                                                                            ?</p>
                                                                                                        <div class="mt-4 flex justify-end gap-2">
                                                                                                            <Button variant="outline"
                                                                                                                    @click="closeModal"
                                                                                                                    class="bg-gray-200 px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-gray-300 transition">
                                                                                                                Annuler
                                                                                                            </Button>
                                                                                                            <button @click="deleteUser(user.id)"
                                                                                                                    class="bg-red-600 text-white px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-red-700 transition">
                                                                                                                Supprimer
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </transition>
                                                                                        </div>
                                                                                    </DropdownMenu>
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4 border-b border-gray-200 rounded-bl-lg rounded-br-lg"
                                                                             aria-label="Table navigation">
                                                                          <span class="text-sm font-normal text-gray-500">
                                                                            Affichage
                                                                            <span class="font-semibold text-gray-900">{{
                                                                                    startIndex + 1
                                                                                }} - {{ endIndex }}</span> sur
                                                                            <span class="font-semibold text-gray-900">{{
                                                                                    filteredUsers.length
                                                                                }}</span>
                                                                          </span>
                                                                            <ul class="inline-flex items-stretch -space-x-px">
                                                                                <!-- Bouton Précédent -->
                                                                                <li>
                                                                                    <button
                                                                                            @click.prevent="prevPage"
                                                                                            :disabled="currentPage === 1"
                                                                                            class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 hover:cursor-pointer rounded-tl-lg rounded-bl-lg"
                                                                                    >
                                                                                        <span class="sr-only">Précédent</span>
                                                                                        <ChevronLeft class="w-5 h-5"
                                                                                                     stroke-width="1.5"
                                                                                                     size="18"/>
                                                                                    </button>
                                                                                </li>

                                                                                <!-- Numéros de Page -->
                                                                                <li v-for="page in pageRange"
                                                                                    :key="page">
                                                                                    <button
                                                                                            @click.prevent="currentPage = page"
                                                                                            :class="{'text-primary-600 bg-green-400 border-green-400 hover:bg-green-400': page === currentPage, 'text-gray-500 bg-white border-gray-300': page !== currentPage}"
                                                                                            class="flex items-center justify-center text-sm py-2 px-3 leading-tight border hover:bg-gray-100 hover:cursor-pointer"
                                                                                    >
                                                                                        {{ page }}
                                                                                    </button>
                                                                                </li>
                                                                                <li>
                                                                                    <button
                                                                                            @click.prevent="nextPage"
                                                                                            :disabled="currentPage === totalPages"
                                                                                            class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 hover:cursor-pointer rounded-tr-lg rounded-br-lg"
                                                                                    >
                                                                                        <span class="sr-only">Suivant</span>
                                                                                        <ChevronRight class="w-5 h-5"
                                                                                                      stroke-width="1.5"
                                                                                                      size="18"/>
                                                                                    </button>
                                                                                </li>
                                                                            </ul>
                                                                        </nav>
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
                        </div>

                        <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                            <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                                <button @click="closeModal"
                                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                                    &times;
                                </button>
                                <h2 class="text-lg font-light">Modifier un utilisateur</h2>
                                <p class="text-xs font-light mb-4">Modifier les informations d'un utilisateur
                                    n°{{ modifierUser.id }}</p>
                                <div>

                                    <div class="">
                                        <form @submit.prevent="updateUser" class="py-5">
                                            <div class="mb-6">
                                                <label for="nom"
                                                       class="block mb-2 text-sm text-gray-500 font-light">Nom</label>
                                                <input type="text" v-model="modifierUser.nom" id="nom" name="nom"
                                                       class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                       required>
                                            </div>
                                            <div class="mb-6">
                                                <label for="prenom" class="block mb-2 text-sm text-gray-500 font-light">Prénom</label>
                                                <input type="text" v-model="modifierUser.prenom" id="prenom"
                                                       name="prenom"
                                                       class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                       required>
                                            </div>
                                            <div class="mb-6">
                                                <label for="email" class="block mb-2 text-sm text-gray-500 font-light">Adresse
                                                    mail</label>
                                                <input type="email" v-model="modifierUser.email" id="email" name="email"
                                                       class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                       required>
                                            </div>
                                            <div class="mb-6">
                                                <label for="classe" class="block mb-2 text-sm text-gray-500 font-light">Promo</label>
                                                <input type="text" v-model="modifierUser.promo " id="promo" name="promo"
                                                       class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                       required>
                                            </div>
                                            <div class="flex items-center justify-between gap-4">
                                                <div class="mb-6">
                                                    <label for="td" class="block mb-2 text-sm text-gray-500 font-light">TD</label>
                                                    <input type="text" v-model="modifierUser.td" id="td" name="td"
                                                           class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                           required>
                                                </div>
                                                <div class="mb-6">
                                                    <label for="tp" class="block mb-2 text-sm text-gray-500 font-light">TP</label>
                                                    <input type="text" v-model="modifierUser.tp" id="tp" name="tp"
                                                           class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="mb-6">
                                                <label for="role" class="block mb-2 text-sm text-gray-500 font-light">Rôle</label>
                                                <select v-model="modifierUser.roleapp" id="role" name="role"
                                                        class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                        required>
                                                    <option value="ROLE_PROFESSEUR">Professeur</option>
                                                    <option value="ROLE_ELEVE">Etudiant</option>
                                                    <option value="ROLE_ADMIN">Administrateur</option>
                                                </select>
                                            </div>
                                            <div class="w-full flex justify-end gap-2">
                                                <Button variant="outline" size="small" class="hover:cursor-pointer"
                                                        @click="closeModal">Annuler
                                                </Button>
                                                <Button variant="solid" size="small" type="submit"
                                                        class="hover:cursor-pointer">
                                                    Modifier
                                                </Button>
                                            </div>
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
            </div>
        </section>
    </div>
</template>

<style scoped>

</style>