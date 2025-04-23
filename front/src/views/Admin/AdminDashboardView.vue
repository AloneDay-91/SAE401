<script setup>
import {useStore} from 'vuex';
import {computed, onMounted, ref} from 'vue';
import {
    Book,
    ChevronLeft,
    ChevronRight,
    CircleUser,
    GraduationCap,
    Search,
} from 'lucide-vue-next';
import axios from 'axios';
import ApexCharts from 'apexcharts'

let chart = null;

onMounted(() => {
    TotalUser();
    TotalDevoir();
    TotalMatiere();
    TotalClasse();
});

onMounted(async () => {

    await TotalDevoir();

    const {dates, counts} = generateChartData();
    const timestamps = dates.map(date => new Date(date).getTime());

    const options = {
        chart: {
            type: 'area',
            dropShadow: {
                enabled: false,
            },
            toolbar: {
                show: false,
            },
        },
        fill: {
            type: "gradient",
            gradient: {
                opacityFrom: 0.55,
                opacityTo: 0,
                shade: "#05df72",
                gradientToColors: ["#05df72"],
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            width: 6,
        },
        grid: {
            show: false,
            strokeDashArray: 4,
            padding: {
                left: 2,
                right: 2,
                top: 0
            },
        },
        series: [{
            name: 'Devoirs créés',
            data: counts,
            color: "#05df72",
        }],
        yaxis: {
            show: false,
        },
        xaxis: {
            type: 'datetime',
            categories: timestamps,
            labels: {
                format: 'dd/MM',
                datetimeUTC: false,
                show: false,
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        tooltip: {
            x: {
                format: 'dd/MM',
            }
        },
    };

    chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
});

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();

const TotalUsers = ref(0);
const TotalDevoirs = ref(0);
const TotalMatieres = ref(0);
const TotalClasses = ref(0);
const users = ref([]);
const loading = ref(false);
const isModalOpen = ref(false);
const modifierUser = ref(null);
const devoirsParJour = ref({});

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
    modifierUser.value = {...user};
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

const TotalDevoir = async () => {
    try {
        const response = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });

        console.log(response.data.member);

        const counts = response.data.member.reduce((acc, devoir) => {
            try {
                // Utiliser le champ 'date' au lieu de 'createdAt'
                const dateObj = new Date(devoir.date);
                if (isNaN(dateObj)) new Error('Date invalide');

                const date = dateObj.toISOString().split('T')[0];
                acc[date] = (acc[date] || 0) + 1;
            } catch (e) {
                console.error('Erreur de date pour le devoir', devoir.id, e);
            }
            return acc;
        }, {});

        devoirsParJour.value = counts;
        console.log(counts);


        TotalDevoirs.value = response.data.totalItems;
    } catch (e) {
        console.error('Erreur lors de la récupération des devoirs:', e);
    }
};

const generateChartData = () => {
    const endDate = new Date('2025-04-22'); // Date actuelle selon ton contexte
    const startDate = new Date(endDate);
    startDate.setDate(endDate.getDate() - 30);

    const allDates = {};
    for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
        const dateStr = d.toISOString().split('T')[0];
        allDates[dateStr] = devoirsParJour.value[dateStr] || 0;
    }

    return {
        dates: Object.keys(allDates).sort(),
        counts: Object.values(allDates)
    };
};


const TotalMatiere = async () => {
    try {
        const response = await axios.get(`${API_URL}/matieres`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        TotalMatieres.value = response.data.totalItems;
    } catch (e) {
        console.error('Erreur lors de la récupération des matières:', e);
    }
};

const TotalClasse = async () => {
    try {
        const response = await axios.get(`${API_URL}/classes`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        TotalClasses.value = response.data.totalItems;
    } catch (e) {
        console.error('Erreur lors de la récupération des matières:', e);
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
            users.value[index] = {...modifierUser.value};
        }

        closeModal();

    } catch (error) {
        console.error('Erreur lors de la mise à jour de l\'utilisateur:', error);
    }
};


</script>
<template>
    <div class="text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <p class="text-gray-800 font-semibold">Paramètres</p>
        <div class="mt-1 text-sm text-gray-500">Gérer les paramètres du site</div>
    </div>
    <div class="px-12 py-8">
        <div
            class="text-left w-full mx-auto px-4 sm:px-6 lg:px-12 flex items-center gap-2 justify-between bg-gray-100/50 border border-gray-200 rounded-lg">
            <div class="flex flex-col items-start py-4 border-r border-gray-200 gap-2 w-full">
                <span><CircleUser stroke-width="1.5" size="30" class="text-green-400 fill-green-200/40" /></span>
                <span class="uppercase text-xs font-light">Nombres d'utilisateurs</span>
                <span class="text-2xl font-medium">{{ TotalUsers }} <span class="text-xs font-light">au total</span></span>
            </div>
            <div class="flex flex-col items-start py-4 border-r border-gray-200 gap-2 w-full px-8">
            <span>
                <Book stroke-width="1.5" size="30" class="text-green-400 fill-green-200/40" />
            </span>
                <span class="uppercase text-xs font-light">Nombres de devoirs</span>
                <span class="text-2xl font-medium">{{ TotalDevoirs }} <span class="text-xs font-light">au total</span></span>
            </div>
            <div class="flex flex-col items-start py-4 border-r border-gray-200 gap-2 w-full px-8">
                <span>
                    <GraduationCap stroke-width="1.5" size="30" class="text-green-400 fill-green-200/40"/>
                </span>
                <span class="uppercase text-xs font-light">Nombres de classes</span>
                <span class="text-2xl font-medium">{{ TotalClasses }} <span
                    class="text-xs font-light">au total</span></span>
            </div>
            <div class="flex flex-col items-start py-4 gap-2 w-full px-8">
            <span>
                <Book stroke-width="1.5" size="30" class="text-green-400 fill-green-200/40" />
            </span>
                <span class="uppercase text-xs font-light">Nombres de matières</span>
                <span class="text-2xl font-medium">{{ TotalMatieres }} <span class="text-xs font-light">au total</span></span>
            </div>
        </div>
    </div>
    <div class="mx-4">
        <section class="flex items-start">
            <div class="mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="">
                    <div class="rounded-lg divide-y divide-gray-200 ring-1 ring-gray-200 shadow bg-white">
                        <div class="divide-y divide-gray-200 gap-4 flex flex-col px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                <div class="">
                                    <div class="flex content-center items-center justify-between text-sm">
                                        <label for="maintenance_toggle" class="block font-medium text-gray-700">Mode maintenance</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Activer la maintenance du site ?</p>
                                </div>
                                <div class="relative flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-normal bg-gray-100 text-gray-400">
                                        En cours de développement
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-6">
                    <div class="flex lg:flex-col justify-between flex-row flex-wrap gap-4">
                        <div class="flex items-start gap-4">
                            <div>
                                <p class="text-gray-900 font-semibold"></p>
                                <div class="mt-1 text-sm text-gray-500"></div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg ring-1 ring-gray-200 shadow bg-white">
                        <div class="divide-y divide-gray-200 gap-4 flex flex-col px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                <div class="">
                                    <div class="flex content-center items-center justify-between text-sm">
                                        <label for="alert_banner_toggle" class="block font-medium text-gray-700">Bannière d'alerte</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Activer la bannière pour avertir les utilisateurs ?</p>
                                </div>
                                <div class="relative flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-normal bg-gray-100 text-gray-400">
                                        En cours de développement
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-6">
                    <div class="rounded-lg ring-1 ring-gray-200 shadow bg-white">
                        <div class="w-full p-6">
                            <p class="block font-medium text-gray-700 text-sm">Devoirs crées</p>
                            <p class="text-gray-500 text-sm">Statistiques des devoirs créés</p>
                        </div>
                        <div class="flex items-center justify-center">
                            <div class="max-w-3xl w-full" id="chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="">
                    <div class="rounded-lg border-1 border-gray-200 shadow-xs">
                        <div class="gap-4 flex flex-col px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                <div class="">
                                    <div class="flex content-center items-center justify-between text-sm">
                                        <label for="maintenance_toggle" class="block font-medium text-gray-700">Utilisateurs</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Gérer les utilisateurs</p>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div class="w-full">


                                        <div class="w-full">
                                            <div>
                                                <div class="flex items-center justify-between gap-4 mb-4">
                                                    <div class="relative w-full max-w-xl">
                                                        <label for="search"
                                                               class="mb-2 text-sm font-light text-gray-500 sr-only">Rechercher
                                                            un utilisateur</label>
                                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                            <Search class="w-4 h-4 text-gray-400" stroke-width="1.5"/>
                                                        </div>
                                                        <input
                                                                id="search"
                                                                v-model="searchQuery"
                                                                type="text"
                                                                placeholder="Rechercher un utilisateur..."
                                                                class="pl-10 border border-gray-300 rounded-lg px-2 py-2 w-full text-sm bg-gray-50"
                                                        />
                                                    </div>
                                                    <!-- Listes déroulantes pour sélectionner les filtres -->
                                                    <div class="flex gap-4">
                                                        <select @change="addFilter($event.target.value)"
                                                                class="border border-gray-300 text-gray-500 font-light text-sm rounded px-2 py-1 w-full bg-gray-50">
                                                            <option value="" disabled selected>Filtrer par rôle</option>
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
                                                                        Classe
                                                                    </th>
                                                                    <th scope="col"
                                                                        class="px-4 py-3 text-gray-500 text-xs font-normal">
                                                                        Rôles
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr v-for="user in paginatedUsers" :key="user.id"
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
                                                                        {{ user.promo }} {{ user.td }} {{ user.tp }}
                                                                    </td>
                                                                    <td class="px-4 py-4 text-gray-500 text-xs font-normal">
                                                                            <span class="rounded px-2 py-1 border">
                                                                                {{ getRoleLabel(user) }}
                                                                            </span>
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
                                                                                         stroke-width="1.5" size="18"/>
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
                                                                            <ChevronRight class="w-5 h-5"
                                                                                          stroke-width="1.5" size="18"/>
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
            </div>
        </section>
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
                        <button type="submit" class="bg-[#00D478] text-[#004319] border-[#00D478] text-sm font-medium hover:bg-[#00C26F] transition px-4 py-2 rounded">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>