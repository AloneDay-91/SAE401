<!-- TODO: Corriger bug pagination devoirs admin -->

<script setup>
import {computed, inject, onMounted, ref} from 'vue';
import {useStore} from 'vuex';
import axios from 'axios';
import {ArrowLeft, ArrowRight, ChevronLeft, ChevronRight, Ellipsis, Eye, FilePenLine, Trash2, Search} from "lucide-vue-next";
import DropdownMenu from "@/components/DropdownMenu.vue";
import Button from "@/components/Button.vue";
import {pulsar} from 'ldrs'

pulsar.register()

const API_URL = import.meta.env.VITE_API_BASE_URL;

const loading = ref(false)
const store = useStore();
const devoirs = ref([]);
const matieres = ref([]);
const categories = ref([]);
const error = ref('');
const isModalOpen = ref(false);
const modifierDevoir = ref(null);
const isModalDeleteOpen = ref(false);

const triggerToast = inject('triggerToast');

// Variables pour la recherche, le filtrage et la pagination
const searchQuery = ref('');

const currentPage = ref(1);
const itemsPerPage = ref(10);

// Nouvelles propriétés calculées pour la pagination
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredDevoirs.value.length));

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

const openDeleteModal = () => {
    isModalDeleteOpen.value = true;
};

// Ouvrir/fermer le modal de modification
const openModal = (devoir) => {
    modifierDevoir.value = { ...devoir, date: extractDate(devoir.date), heure: extractTime(devoir.heure) };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    isModalDeleteOpen.value = false;
};

// Formater la date et l'heure
const formatDate = (date) => {
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(date).toLocaleDateString('fr-FR', options);
};

const formatTime = (date) => {
    const options = { hour: '2-digit', minute: '2-digit', timeZone: 'Europe/Paris' };
    return new Date(date).toLocaleTimeString('fr-FR', options);
};

const extractDate = (dateTime) => {
    return dateTime ? dateTime.split('T')[0] : '';
};

const extractTime = (dateTime) => {
    if (!dateTime) return '';
    const date = new Date(dateTime);
    date.setHours(date.getHours() + 1);
    return date.toISOString().substring(11, 16);
};

// Récupération des données depuis l'API
onMounted(async () => {
    loading.value = true;
    try {
        const devoirsresponse = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        devoirs.value = devoirsresponse.data.member;

        const matieresresponse = await axios.get(`${API_URL}/matieres`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        matieres.value = matieresresponse.data.member;

        const categoriesresponse = await axios.get(`${API_URL}/categories`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        categories.value = categoriesresponse.data.member;
        loading.value = false;
    } catch (e) {
        triggerToast("Erreur lors de la récupération des devoirs", "Impossible de récupérer les devoirs.", 'error');
    }
});

// Mise à jour d'un devoir
const updateDevoir = async () => {
    if (!modifierDevoir.value) return;

    try {
        const [hours, minutes] = modifierDevoir.value.heure.split(':');
        const dateTime = new Date(modifierDevoir.value.date);
        dateTime.setHours(parseInt(hours), parseInt(minutes), 0);

        const devoirData = JSON.stringify({
            intitule: modifierDevoir.value.intitule,
            date: modifierDevoir.value.date,
            heure: dateTime.toISOString(),
            matiere: modifierDevoir.value.matiere,
            categorie: modifierDevoir.value.categorie,
        });

        await axios.patch(`${API_URL}/devoirs/${modifierDevoir.value.id}`, devoirData, {
            headers: {
                "Content-Type": "application/merge-patch+json",
                "Authorization": `Bearer ${store.state.token}`,
            },
        });

        const index = devoirs.value.findIndex((u) => u.id === modifierDevoir.value.id);
        if (index !== -1) {
            Object.assign(devoirs.value[index], {
                ...modifierDevoir.value,
                heure: dateTime.toISOString(),
            });
        }
        triggerToast("Devoir mis à jour", "Le devoir a été mis à jour avec succès.", 'success');
        closeModal();
    } catch (error) {
        triggerToast("Erreur lors de la mise à jour du devoir", "Impossible de mettre à jour le devoir.", 'error');
    }
};

// Filtrage des devoirs avec les filtres sélectionnés
const filteredDevoirs = computed(() => {
    return devoirs.value.filter((devoir) => {
        // Vérifier si le devoir correspond aux filtres sélectionnés
        const matchesFilters =
            selectedFilters.value.every((filter) =>
                [devoir.id_matieres.nom, devoir.id_categories.nom].includes(filter)
            );

        // Vérifier si le devoir correspond à la recherche
        const matchesSearch =
            searchQuery.value === '' ||
            Object.values(devoir).some((value) =>
                String(value).toLowerCase().includes(searchQuery.value.toLowerCase())
            );

        return matchesFilters && matchesSearch;
    });
});

// Pagination des données filtrées
const paginatedDevoirs = computed(() => {
    return filteredDevoirs.value.slice(startIndex.value, endIndex.value);
});

// Calcul du nombre total de pages
const totalPages = computed(() => Math.ceil(filteredDevoirs.value.length / itemsPerPage.value));

// Méthodes pour naviguer entre les pages
const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};



// Liste réactive pour les filtres sélectionnés
const selectedFilters = ref([]);

// Ajouter un filtre
const addFilter = (filter) => {
    if (!selectedFilters.value.includes(filter)) {
        selectedFilters.value.push(filter);
    }
};

// Supprimer un filtre
const removeFilter = (filter) => {
    selectedFilters.value = selectedFilters.value.filter((f) => f !== filter);
};

// axios delete
const deleteDevoir = async (devoirId) => {
    try {
        await axios.delete(`${API_URL}/devoirs/${devoirId}`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        devoirs.value = devoirs.value.filter((devoir) => devoir.id !== devoirId);
        closeModal();
        triggerToast("Devoir supprimé", "Le devoir a été supprimé avec succès.", 'success');
    } catch (error) {
        triggerToast("Erreur lors de la suppression du devoir", "Impossible de supprimer le devoir.", 'error');
    }
};

</script>

<template>
  <div class="flex items-center justify-between text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div>
      <p class="text-gray-800 font-semibold">Liste des devoirs</p>
      <div class="mt-1 text-sm text-gray-500">Gérer les différents devoirs</div>
    </div>
    <div class="">
      <Button variant="solid" size="small" tag="a" href="devoirs/new">Ajouter un devoir</Button>
    </div>
  </div>

  <div class="mx-4 my-4">
    <section>
        <div class="mx-auto w-full">
            <div>
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div class="relative w-full max-w-xl">
                        <label for="search" class="mb-2 text-sm font-light text-gray-500 sr-only">Rechercher un
                            devoir</label>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <Search class="w-4 h-4 text-gray-400" stroke-width="1.5"/>
                        </div>
                        <input
                            id="search"
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher un intitulé..."
                            class="pl-10 border border-gray-300 rounded-lg px-2 py-3 w-full text-sm bg-gray-50"
                        />
                    </div>
                    <!-- Listes déroulantes pour sélectionner les filtres -->
                    <div class="flex gap-4">
                        <select @change="addFilter($event.target.value)"
                                class="border border-gray-300 text-gray-500 font-light text-sm rounded px-2 py-1 w-full bg-gray-50">
                            <option value="" disabled selected>Filtrer par matière</option>
                            <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.nom">
                                {{ matiere.nom }}
                            </option>
                        </select>

                        <select @change="addFilter($event.target.value)"
                                class="border border-gray-300 text-gray-500 font-light text-sm rounded px-2 py-1 w-full bg-gray-50">
                            <option value="" disabled selected>Filtrer par catégorie</option>
                            <option v-for="categorie in categories" :key="categorie.id" :value="categorie.nom">
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

                <div v-if="loading" class="w-full text-center">
                    <l-pulsar size="40" speed="1.75" color="#05df72"></l-pulsar>
                </div>
                <div v-else>
                    <div class="max-w-full border border-gray-200 border-b-0 rounded-lg">
                        <table class="w-full text-left text-gray-500">
                            <thead class="border-b border-gray-200 bg-gray-50">
                            <tr class="uppercase">
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">ID</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Date</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Heure</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Matière</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Catégorie</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Utilisateur</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Classes</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!--              <tr class="border-b border-gray-200" v-for="devoir in devoirs">-->
                            <tr
                                v-for="devoir in paginatedDevoirs"
                                :key="devoir.id"
                                class="border-b border-gray-200"
                            >
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{
                                        devoir.intitule
                                    }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                    {{ formatDate(devoir.date) }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                    {{ formatTime(devoir.heure) }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{
                                        devoir.id_matieres.nom
                                    }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                    {{ devoir.id_categories.nom }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_users.nom }}
                                    {{ devoir.id_users.prenom }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                    {{ devoir.id_classes.promo }} {{ devoir.id_classes.type }}
                                </td>
                                <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                    <DropdownMenu>
                                        <!-- Personnalisation du bouton déclencheur -->
                                        <template #trigger>
                                            <Button class="inline-flex hover:cursor-pointer" variant="ghost"
                                                    size="small">
                                                <Ellipsis stroke-width="1.5" size="16"/>
                                            </Button>
                                        </template>

                                        <div class="px-2">
                                            <router-link :to="`devoirs/${devoir.id}`"
                                                         class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                                <span>Voir</span>
                                                <Eye stroke-width="1.5" size="16"/>
                                            </router-link>
                                            <button @click="openModal(devoir)"
                                                    class="py-2 flex items-center justify-between w-full text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
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
                                                        <h2 class="text-lg mb-2">Supprimer le devoir</h2>
                                                        <p class="text-xs font-light">Êtes-vous sûr de vouloir supprimer
                                                            le devoir n°{{ devoir.id }} ?</p>
                                                        <div class="mt-4 flex justify-end gap-2">
                                                            <Button variant="outline" @click="closeModal"
                                                                    class="bg-gray-200 px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-gray-300 transition">
                                                                Annuler
                                                            </Button>
                                                            <button @click="deleteDevoir(devoir.id)"
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
                    <span class="font-semibold text-gray-900">{{ filteredDevoirs.length }}</span>
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
                </div>

                <div v-if="isModalOpen"
                     class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 overflow-y-auto pt-24">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative m-12">
                        <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                            &times;
                        </button>
                        <h2 class="text-lg font-light">Modifier un devoir</h2>
                        <p class="text-xs font-light mb-4">Modifier les informations du devoir n°{{
                                modifierDevoir.id
                            }}</p>
                        <div>

                            <div class="">
                                <form @submit.prevent="updateDevoir" class="py-5">
                                    <div class="mb-6">
                                        <label for="intitule" class="block mb-2 text-sm font-light text-gray-600">Intitulé
                                            du devoir</label>
                                        <input type="text" id="intitule" name="intitule"
                                               v-model="modifierDevoir.intitule"
                                               class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                               placeholder="L'intitule du devoir" required>
                                    </div>
                                    <div class="mb-6">
                                        <label for="contenu" class="block mb-2 text-sm font-light text-gray-600">Contenu
                                            du devoir</label>
                                        <input type="text" id="contenu" name="contenu" v-model="modifierDevoir.contenu"
                                               class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                               placeholder="Le contenu du devoir" required>
                                    </div>
                                    <div class="mb-6 flex-col gap-4">
                                        <label for="date" class="block mb-2 text-sm font-light text-gray-600">Pour le
                                            :</label>
                                        <div class="flex gap-4">
                                            <input type="date" id="date" name="date" v-model="modifierDevoir.date"
                                                   class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                   required>

                                            <label for="heure"
                                                   class="block mb-2 text-sm font-light text-gray-600"></label>
                                            <input type="time" id="heure" name="heure" v-model="modifierDevoir.heure"
                                                   class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="mb-6">
                                        <label for="td"
                                               class="block mb-2 text-sm font-light text-gray-600">Matière</label>
                                        <select v-model="modifierDevoir.id_matieres.nom" id="matiere" name="matiere"
                                                class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                required>
                                            <option v-for="matiere in matieres" :value="matiere.nom">
                                                {{ matiere.nom }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-6">
                                        <label for="categorie" class="block mb-2 text-sm font-light text-gray-600">Categorie</label>
                                        <select v-model="modifierDevoir.id_categories.nom" id="categorie"
                                                name="categorie"
                                                class="bg-gray-50 border border-gray-300 text-gray-500 font-light text-sm rounded-lg block w-full p-2 py-1.5"
                                                required>
                                            <option v-for="categorie in categories" :value="categorie.nom">
                                                {{ categorie.nom }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="w-full flex justify-end gap-2">
                                        <Button variant="outline" size="small" class="hover:cursor-pointer"
                                                @click="closeModal">Annuler
                                        </Button>
                                        <Button variant="solid" size="small" type="submit" class="hover:cursor-pointer">
                                            Modifier
                                        </Button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="flex items-center w-full justify-between gap-8 mt-24">
                    <router-link to="/admin/dashboard"
                                 class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                        <div>
                            <div class="flex items-center gap-4 justify-start">
                                <ArrowLeft stroke-width="1.5" size="24" class="mr-2"/>
                                <div class="text-left">
                                    <p class="text-gray-800 font-normal text-sm">Tableau de bord</p>
                                    <div class="text-xs text-gray-500 font-light">Accueil de l'administration</div>
                                </div>
                            </div>
                        </div>
                    </router-link>
                    <router-link to="/admin/devoirs/new"
                                 class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                        <div>
                            <div class="flex items-center gap-4 justify-end">
                                <div class="text-right">
                                    <p class="text-gray-800 font-normal text-sm">Nouveau devoir</p>
                                    <div class="text-xs text-gray-500 font-light">Ajouter un nouveau devoir</div>
                                </div>
                                <ArrowRight stroke-width="1.5" size="24" class="mr-2"/>
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
      </div>
    </section>
  </div>
</template>