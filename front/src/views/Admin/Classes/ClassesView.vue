<script setup>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import { ChevronLeft, ChevronRight, Search, Ellipsis, FilePenLine, Trash2, ArrowLeft } from "lucide-vue-next";
import DropdownMenu from "@/components/DropdownMenu.vue";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const classes = ref([]);
const error = ref('');
const isModalOpen = ref(false);
const modifierClasse = ref(null);

// Variables pour la recherche, le filtrage et la pagination
const searchQuery = ref('');
const selectedFilters = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Pagination calculée
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredClasses.value.length));
const totalPages = computed(() => Math.ceil(filteredClasses.value.length / itemsPerPage.value));

// Récupération des données depuis l'API
onMounted(async () => {
    try {
        const response = await axios.get(`${API_URL}/classes`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        classes.value = response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des classes:', e);
        error.value = 'Impossible de récupérer les classes.';
    }
});

// Filtrage des classes
const filteredClasses = computed(() => {
    return classes.value.filter((classe) => {
        const matchesFilters =
            selectedFilters.value.every((filter) =>
                [classe.intitule].includes(filter)
            );

        const matchesSearch =
            searchQuery.value === '' ||
            Object.values(classe).some((value) =>
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
const paginatedClasses = computed(() => filteredClasses.value.slice(startIndex.value, endIndex.value));

// Gestion du modal
const openModal = (classe) => {
    modifierClasse.value = { ...classe };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

// Mise à jour d'une classe
const updateClasse = async () => {
    if (!modifierClasse.value) return;

    try {
        const classeData = JSON.stringify({
            intitule: modifierClasse.value.intitule,
            promo: modifierClasse.value.promo
        });

        await axios.patch(`${API_URL}/classes/${modifierClasse.value.id}`, classeData, {
            headers: {
                "Content-Type": "application/merge-patch+json",
                "Authorization": `Bearer ${store.state.token}`
            }
        });

        const index = classes.value.findIndex(u => u.id === modifierClasse.value.id);
        if (index !== -1) {
            Object.assign(classes.value[index], modifierClasse.value);
        }

        closeModal();
    } catch (error) {
        console.error('Erreur lors de la mise à jour de la classe:', error);
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
            <p class="text-gray-800 font-semibold">Liste des classes</p>
            <div class="mt-1 text-sm text-gray-500">Gérer les différentes classes</div>
        </div>
    </div>

    <div class="mx-4 my-4">
        <section>
            <div>
                <div>
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div class="relative w-full max-w-xl">
                            <label for="search" class="mb-2 text-sm font-light text-gray-500 sr-only">Rechercher une classe</label>
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <Search class="w-4 h-4 text-gray-400" stroke-width="1.5" />
                            </div>
                            <input
                                id="search"
                                v-model="searchQuery"
                                type="text"
                                placeholder="Rechercher une classe..."
                                class="pl-10 border border-gray-300 rounded-lg px-2 py-3 w-full text-sm bg-gray-50"
                            />
                        </div>
                        <!-- Listes déroulantes pour sélectionner les filtres -->
                        <div class="flex gap-4">
                            <select @change="addFilter($event.target.value)" class="border border-gray-300 text-gray-500 font-light text-sm rounded px-2 py-1 w-full bg-gray-50">
                                <option value="" disabled selected>Filtrer par intitulé</option>
                                <option value="1ère année">1ère année</option>
                                <option value="2ème année">2ème année</option>
                                <option value="3ème année">3ème année</option>
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
                    <div class="max-w-full border border-gray-200 border-b-0 rounded-lg">
                        <table class="w-full text-left text-gray-500">
                            <thead class="border-b border-gray-200">
                            <tr class="uppercase">
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">ID</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Promo</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Type</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="border-b border-gray-200" v-for="classe in paginatedClasses" :key="classe.id">
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.id }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.intitule }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.promo }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.type }}</td>
                                <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                    <DropdownMenu>
                                        <!-- Personnalisation du bouton déclencheur -->
                                        <template #trigger>
                                            <Button class="inline-flex hover:cursor-pointer" variant="ghost" size="small">
                                                <Ellipsis stroke-width="1.5" size="16" />
                                            </Button>
                                        </template>

                                        <div class="px-2">
                                          <button @click="openModal(classe)" class="py-2 flex items-center justify-between w-full text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                            <span>Modifier</span>
                                            <FilePenLine stroke-width="1.5" size="16"/>
                                          </button>
                                            <hr class="text-gray-200">
                                            <router-link :to="`classes/${classe.id}/delete`"
                                                         class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                                <span class="text-red-600">Supprimer</span>
                                                <Trash2 stroke-width="1.5" size="16"/>
                                            </router-link>
                                        </div>
                                    </DropdownMenu>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4 border-b border-gray-200 rounded-bl-lg rounded-br-lg" aria-label="Table navigation">
                  <span class="text-sm font-normal text-gray-500">
                    Affichage
                    <span class="font-semibold text-gray-900">{{ startIndex + 1 }} - {{ endIndex }}</span> sur
                    <span class="font-semibold text-gray-900">{{ filteredClasses.length }}</span>
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
                                        <ChevronLeft class="w-5 h-5" stroke-width="1.5" size="18" />
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
                                        <ChevronRight class="w-5 h-5" stroke-width="1.5" size="18" />
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>

                  <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                      <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                          <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                          <h2 class="text-lg font-light mb-4">Modifier une classe</h2>
                      <div>

                          <div class="">
                              <form @submit.prevent="updateClasse" class="px-4 py-5 sm:px-6">
                                  <div class="mb-6">
                                    <label for="intitule" class="block mb-2 text-sm font-medium text-gray-900">Année en cours</label>
                                    <select v-model="modifierClasse.intitule" id="intitule" name="intitule" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                                      <option value="1ère année">1ère année</option>
                                      <option value="2ème année">2ème année</option>
                                      <option value="3ème année">3ème année</option>
                                    </select>
                                  </div>
                                  <div class="mb-6">
                                    <label for="promo" class="block mb-2 text-sm font-medium text-gray-900">Promo</label>
                                    <select v-model="modifierClasse.promo" id="promo" name="promo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                                      <option value="S1/S2">S1/S2</option>
                                      <option value="S3/S4">S3/S4</option>
                                      <option value="S5/S6">S5/S6</option>
                                    </select>
                                  </div>
                              <Button variant="solid" size="small" type="submit">Modifier</Button>
                          </form>
                      </div>
                  </div>
              </div>
          </div>

                    <div class="flex items-center w-full justify-between gap-8 mt-24">
                        <router-link to="/admin/devoirs" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-start">
                                    <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
                                    <div class="text-left">
                                        <p class="text-gray-800 font-normal text-sm">Liste des devoirs</p>
                                        <div class="text-xs text-gray-500 font-light">Gérer les différents devoirs</div>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                        <router-link to="/admin/matieres" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-end">
                                    <div class="text-right">
                                        <p class="text-gray-800 font-normal text-sm">Liste des matières</p>
                                        <div class="text-xs text-gray-500 font-light">Gérer les différentes matières</div>
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