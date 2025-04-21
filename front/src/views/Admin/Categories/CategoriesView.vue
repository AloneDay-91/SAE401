<script setup>
import {ref, onMounted, inject, computed} from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import {RouterLink} from "vue-router";
import {
    ArrowLeft,
    ArrowRight,
    ChevronLeft,
    ChevronRight,
    Ellipsis,
    FilePenLine,
    Info,
    Search,
    Trash2
} from "lucide-vue-next";
import DropdownMenu from "@/components/DropdownMenu.vue";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const categories = ref([]);
const error = ref('');
const isModalOpen = ref(false);
const modifierCategorie = ref(null);
const triggerToast = inject('triggerToast');
const isModalDeleteOpen = ref(false);

// Variables pour la recherche, le filtrage et la pagination
const searchQuery = ref('');
const selectedFilters = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Pagination calculée
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredCat.value.length));
const totalPages = computed(() => Math.ceil(filteredCat.value.length / itemsPerPage.value));

// Filtrage des classes
const filteredCat = computed(() => {
    return categories.value.filter((categorie) => {
        const matchesFilters =
            selectedFilters.value.every((filter) =>
                [categorie.nom].includes(filter) || [categorie.code].includes(filter)
            );

        const matchesSearch =
            searchQuery.value === '' ||
            Object.values(categorie).some((value) =>
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
const paginatedCat = computed(() => filteredCat.value.slice(startIndex.value, endIndex.value));

const openDeleteModal = () => {
    isModalDeleteOpen.value = true;
};

const openModal = (categorie) => {
  modifierCategorie.value = { ...categorie };
  isModalOpen.value = true;
};
const closeModal = () => {
    isModalOpen.value = false;
    isModalDeleteOpen.value = false;
};

onMounted(async () => {
    try {
        const response = await axios.get(`${API_URL}/categories`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        categories.value = response.data.member;
    } catch (e) {
        triggerToast("Erreur", "Impossible de récupérer les catégories.", 'error');
    }
});

const updateCategorie = async () => {
  if (!modifierCategorie.value) return;

  try {
    // Créer un nouvel objet avec seulement les propriétés à mettre à jour
    const categorieData = JSON.stringify({
      nom: modifierCategorie.value.nom,
      couleur: modifierCategorie.value.couleur
    });

    await axios.patch(`${API_URL}/categories/${modifierCategorie.value.id}`, categorieData, {
      headers: {
        "Content-Type": "application/merge-patch+json",
        "Authorization": `Bearer ${store.state.token}`
      }
    });

    // Mettre à jour la categorie dans le tableau local
    const index = categories.value.findIndex(u => u.id === modifierCategorie.value.id);
    if (index !== -1) {
      categories.value[index] = { ...modifierCategorie.value };
    }
      triggerToast("Catégorie mise à jour", "La catégorie a été mise à jour avec succès.", 'success');
    closeModal();
  } catch (error) {
      triggerToast("Erreur", "Impossible de mettre à jour la catégorie.", 'error');
  }
};

const deleteCategorie = async (categorieId) => {
    try {
        await axios.delete(`${API_URL}/categories/${categorieId}`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        categories.value = categories.value.filter((categorie) => categorie.id !== categorieId);
        closeModal();
        triggerToast("Catégorie supprimé", "La catégorie a été supprimé avec succès.", 'success');
    } catch (error) {
        triggerToast("Erreur lors de la suppression de la catégorie", "Impossible de supprimer la catégorie.", 'error');
    }
};

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
</script>
<template>
    <div class="flex items-center justify-between text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div>
            <p class="text-gray-800 font-semibold">Liste des catégories</p>
            <div class="mt-1 text-sm text-gray-500">Gérer les différentes catégories</div>
        </div>
        <div class="">
            <Button variant="solid" size="small" tag="a" href="categories/new">Ajouter une catégorie</Button>
        </div>
    </div>

    <div class="mx-4 my-4">
        <section>
            <div>
                <div>
                    <div>
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div class="relative w-full max-w-xl">
                                <label for="search" class="mb-2 text-sm font-light text-gray-500 sr-only">Rechercher une
                                    catégorie</label>
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <Search class="w-4 h-4 text-gray-400" stroke-width="1.5"/>
                                </div>
                                <input
                                    id="search"
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Rechercher une catégorie..."
                                    class="pl-10 border border-gray-300 rounded-lg px-2 py-3 w-full text-sm bg-gray-50"
                                />
                            </div>
                            <!-- Listes déroulantes pour sélectionner les filtres -->
                            <div class="flex gap-4">
                                <select @change="addFilter($event.target.value)"
                                        class="border border-gray-300 text-gray-500 font-light text-sm rounded px-2 py-1 w-full bg-gray-50">
                                    <option value="" disabled selected>Filtrer par nom</option>
                                    <option v-for="categorie in categories" :key="categorie.id" :value="categorie.code">
                                        {{ categorie.nom }}
                                    </option>
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
                      <button @click="removeFilter(filter)" class="text-green-500 hover:text-red-500">&times;</button>
                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-full border border-gray-200 border-b-0 rounded-lg">
                        <table class="w-full text-left text-gray-500">
                            <thead class="border-b border-gray-200">
                            <tr class="uppercase">
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">ID</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Nom</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Couleur</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="border-b border-gray-200" v-for="categorie in filteredCat">
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ categorie.id }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ categorie.nom }}</td>
                                <td class="px-6 py-4 text-xs font-normal w-auto">
                                    <span class="h-4 rounded-lg w-auto py-1 px-2 "
                                          :class="categorie.couleur">{{ categorie.couleur }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                    <DropdownMenu>
                                        <!-- Personnalisation du bouton déclencheur -->
                                        <template #trigger>
                                            <Button class="inline-flex hover:cursor-pointer" variant="ghost" size="small">
                                                <Ellipsis stroke-width="1.5" size="16" />
                                            </Button>
                                        </template>

                                        <div class="px-2">
                                            <button @click="openModal(categorie)" class="py-2 flex items-center justify-between w-full text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                                <span>Modifier</span>
                                                <FilePenLine stroke-width="1.5" size="16"/>
                                            </button>
                                            <hr class="text-gray-200">
                                            <button
                                                class="w-full py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1"
                                                @click="openDeleteModal"><span class="text-red-600">Supprimer</span>
                                                <Trash2 stroke-width="1.5" size="16"/>
                                            </button>
                                            <transition name="modal-fade">
                                                <div v-if="isModalDeleteOpen"
                                                     class="fixed inset-0 bg-black/70 flex items-center justify-center">
                                                    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                                                        <h2 class="text-lg mb-2">Supprimer la catégorie</h2>
                                                        <p class="text-xs font-light">Êtes-vous sûr de vouloir supprimer
                                                            la catégorie n°{{ categorie.id }} ?</p>
                                                        <div class="mt-4 flex justify-end gap-2">
                                                            <Button variant="outline" @click="closeModal"
                                                                    class="bg-gray-200 px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-gray-300 transition">
                                                                Annuler
                                                            </Button>
                                                            <button @click="deleteCategorie(categorie.id)"
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
                        <nav
                            class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4 border-b border-gray-200 rounded-bl-lg rounded-br-lg"
                            aria-label="Table navigation">
                  <span class="text-sm font-normal text-gray-500">
                    Affichage
                    <span class="font-semibold text-gray-900">{{ startIndex + 1 }} - {{ endIndex }}</span> sur
                    <span class="font-semibold text-gray-900">{{ filteredCat.length }}</span>
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
                                        <ChevronLeft class="w-5 h-5" stroke-width="1.5" size="18"/>
                                    </button>
                                </li>

                                <!-- Numéros de Page -->
                                <li v-for="page in pageRange" :key="page">
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
                                        <ChevronRight class="w-5 h-5" stroke-width="1.5" size="18"/>
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                            <button @click="closeModal"
                                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;
                            </button>
                            <h2 class="text-lg font-light">Modifier une catégorie</h2>
                            <p class="text-xs font-light mb-4">Modifier les informations de la catégorie
                                n°{{ modifierCategorie.id }}</p>
                            <div>

                                <div class="">
                                    <form @submit.prevent="updateCategorie" class="py-5">
                                        <div class="mb-6">
                                            <label for="intitule" class="block mb-2 text-sm text-gray-500 font-light">Intitulé</label>
                                            <input type="text" v-model="modifierCategorie.nom" id="intitule"
                                                   name="intitule"
                                                   class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                   required>
                                        </div>
                                        <div class="mb-6">
                                            <label for="couleur" class="block mb-2 text-sm text-gray-500 font-light">Couleur</label>
                                            <input type="text" v-model="modifierCategorie.couleur" id="couleur"
                                                   name="couleur"
                                                   class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                   required/>
                                            <div>
                                                <span
                                                    class="text-xs text-gray-500 font-light mt-4 inline-flex items-center"><Info
                                                    size="14"
                                                    class="mr-1"/> La couleur ajoutée doit être du type : </span>
                                                <br>
                                                <span
                                                    class="text-xs text-gray-500 font-light underline">bg-blue-500/40</span>
                                            </div>
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

                    <div class="flex items-center w-full justify-between gap-8 mt-24">
                        <router-link to="/admin/matieres" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-start">
                                    <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
                                    <div class="text-left">
                                        <p class="text-gray-800 font-normal text-sm">Liste des matières</p>
                                        <div class="text-xs text-gray-500 font-light">Gérer les différentes matières</div>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                        <router-link to="/admin/categories/new" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-end">
                                    <div class="text-right">
                                        <p class="text-gray-800 font-normal text-sm">Nouvelle catégorie</p>
                                        <div class="text-xs text-gray-500 font-light">Ajouter une nouvelle catégorie</div>
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