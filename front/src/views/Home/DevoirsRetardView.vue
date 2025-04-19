<script setup>
import {useStore} from 'vuex';
import axios from "axios";
import {onMounted, ref, inject, computed} from "vue";
import {pulsar} from 'ldrs'
import {ChevronLeft, ChevronRight} from "lucide-vue-next";

pulsar.register()

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const loading = ref(true);
const error = ref('');
const triggerToast = inject('triggerToast');

const devoirsUtilisateur = ref([]);

const formatDate = (dateString) => {
    const options = {year: 'numeric', month: '2-digit', day: '2-digit'};
    return new Date(dateString).toLocaleDateString('fr-FR', options);
};

const formatTime = (timeString) => {
    const options = {hour: '2-digit', minute: '2-digit'};
    return new Date(`1970-01-01T${timeString}`).toLocaleTimeString('fr-FR', options);
};

const getDevoirsUtilisateur = async () => {
    try {
        const response = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                "Content-Type": "application/ld+json",
                Authorization: `Bearer ${store.state.token}`,
            },
        });
        devoirsUtilisateur.value = response.data.member.filter(devoir => {
            return devoir.id_users.id === store.state.id;
        });

    } catch (e) {
        error.value = e.response?.data?.detail || "Impossible de récupérer les devoirs.";
        triggerToast("Erreur", "Impossible de récupérer les devoirs.", 'error');
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    try {
        await getDevoirsUtilisateur();
        // afficher uniquement ceux ou la date est inférieure à aujourd'hui
        devoirsUtilisateur.value = devoirsUtilisateur.value.filter(devoir => {
            const dateDevoir = new Date(devoir.date);
            console.log("Date du devoir:", dateDevoir);
            const today = new Date();
            console.log("Date d'aujourd'hui:", today);
            return dateDevoir < today;
        });

    } catch (error) {
        console.error("Erreur lors du chargement:", error);
        triggerToast("Erreur", "Impossible de charger les devoirs.", 'error');
    }
});


const currentPage = ref(1);
const itemsPerPage = ref(10);

// Nouvelles propriétés calculées pour la pagination
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, devoirsUtilisateur.value.length));

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
const paginatedDevoirs = computed(() => {
    return devoirsUtilisateur.value.slice(startIndex.value, endIndex.value);
});

// Calcul du nombre total de pages
const totalPages = computed(() => Math.ceil(devoirsUtilisateur.value.length / itemsPerPage.value));

// Méthodes pour naviguer entre les pages
const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};
</script>

<template>
    <div v-if="loading" class="flex items-center justify-center">
        <l-pulsar size="40" speed="1.75" color="#05df72"></l-pulsar>
    </div>
    <div v-else>
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-left">
            <thead class="border-b border-gray-200">
            <tr class="uppercase bg-gray-100">
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Date</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Heure</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Matière</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Catégorie</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Classes</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="devoirsUtilisateur.length > 0" v-for="devoir in devoirsUtilisateur" :key="devoir['@id']"
                class="border-b border-gray-200">
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.intitule }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatDate(devoir.date) }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatTime(devoir.heure) }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_matieres.code }}
                    {{ devoir.id_matieres.nom }}
                </td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_categories.nom }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_classes.promo }}
                    {{ devoir.id_classes.type }}
                </td>
            </tr>
            <tr>
                <td colspan="7" v-if="devoirsUtilisateur.length === 0"
                    class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                    Aucun devoir terminé pour le moment.
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
                    <span class="font-semibold text-gray-900">{{ devoirsUtilisateur.length }}</span>
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
</template>
