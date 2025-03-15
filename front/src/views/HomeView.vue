<script setup>
import axios from "axios";
import {computed, onMounted, ref} from "vue";
import {useStore} from "vuex";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const user = computed(() => store.state.user);

const devoirs = ref([]);
const loading = ref(true);
const error = ref("");
const checkedDevoirs = ref([]);

onMounted(async () => {
    try {
        const response = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                "Content-Type": "application/ld+json",
                Authorization: `Bearer ${store.state.token}`,
            },
        });
        devoirs.value = response.data.member;

        // Optionnel: Récupérer l'état des checkboxes depuis une API
        await loadCheckboxStatus();
    } catch (e) {
        console.error("Erreur lors de la récupération des devoirs:", e);
        error.value = e.response?.data?.detail || "Impossible de récupérer les devoirs.";
    } finally {
        loading.value = false;
    }
});

// Fonction pour charger les statuts des checkboxes depuis le serveur
const loadCheckboxStatus = async () => {
    try {
        const response = await axios.get(`${API_URL}/checkbox-statuses`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`,
            }
        });

        // Supposons que la réponse est un tableau d'objets avec les IDs des devoirs cochés
        if (response.data && Array.isArray(response.data)) {
            checkedDevoirs.value = response.data
                .filter(item => item.status === true)
                .map(item => {
                    // Extraire l'ID du devoir de l'URL (ex: "https://example.com/devoirs/123" -> "123")
                    const id = item.devoirs.split('/').pop();
                    return id;
                });
        }
    } catch (e) {
        console.error("Erreur lors du chargement des statuts de checkbox:", e);
    }
};

// Fonction pour vérifier si un devoir est coché
const isDevoirChecked = (devoirId) => {
    return checkedDevoirs.value.includes(devoirId);
};

// Fonction pour basculer l'état d'un devoir
const toggleChecked = async (devoir) => {
    const devoirId = devoir.id;

    // Basculer l'état dans la liste locale
    if (isDevoirChecked(devoirId)) {
        checkedDevoirs.value = checkedDevoirs.value.filter(id => id !== devoirId);
    } else {
        checkedDevoirs.value.push(devoirId);
    }

    // Envoyer la mise à jour au serveur
    try {
        await axios.post(`${API_URL}/checkbox-statuses`, {
            user: `https://example.com/users/${user.value.id}`,
            devoirs: `https://example.com/devoirs/${devoirId}`,
            status: isDevoirChecked(devoirId)
        }, {
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${store.state.token}`,
            }
        });
    } catch (e) {
        console.error("Erreur lors de la mise à jour du statut:", e);
        // En cas d'erreur, restaurer l'état précédent
        if (isDevoirChecked(devoirId)) {
            checkedDevoirs.value = checkedDevoirs.value.filter(id => id !== devoirId);
        } else {
            checkedDevoirs.value.push(devoirId);
        }
    }
};

// Fonction pour obtenir les styles d'un devoir en fonction de son état
const getDevoirStyle = (devoirId) => {
    if (isDevoirChecked(devoirId)) {
        return {
            textDecoration: 'line-through',
            color: 'gray'
        };
    } else {
        return {
            textDecoration: 'none',
            color: 'black'
        };
    }
};

const devoirsUser = computed(() => {
    return devoirs.value.filter(
        (devoir) => devoir.id_users && devoir.id_users['@id'] === `/api/users/${user.value.id}`
    );
});

// Tri des devoirs par date
const devoirsFiltres = computed(() => {
    return devoirsUser.value
        .slice()
        .sort((a, b) => new Date(a.date) - new Date(b.date));
});


const getDateDevoirStatus = (dateRendu) => {
    if (!dateRendu) return "Date inconnue";

    const dateRenduObj = new Date(dateRendu);
    const dateActuelle = new Date();
    const diff = dateRenduObj - dateActuelle;
    const diffJours = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (diffJours < 0) {
        return "En retard";
    } else if (diffJours === 0) {
        return "Aujourd'hui";
    } else if (diffJours === 1) {
        return "Demain";
    } else {
        return `Dans ${diffJours} jours`;
    }
};

const getDateDevoirClass = (dateRendu) => {
    if (!dateRendu) return "bg-gray-500/20 text-gray-700";

    const dateRenduObj = new Date(dateRendu);
    const dateActuelle = new Date();
    const diff = dateRenduObj - dateActuelle;
    const diffJours = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (diffJours <= 0) {
        return "bg-red-500/20 text-red-700"; // En retard
    } else if (diffJours === 0) {
        return "bg-orange-500/20 text-orange-700"; // Aujourd'hui
    } else if (diffJours <= 4) {
        return "bg-yellow-500/20 text-yellow-700"; // Moins de 4 jours
    } else {
        return "bg-green-500/20 text-green-700"; // Plus de 4 jours
    }
};

// Fonctionnalités du calendrier
const today = new Date();
const currentWeekStart = ref(getWeekStart(today));

// Formatage du numéro du jour
const formatDayNumber = (date) => {
    return date.getDate();
};

// Vérifier si une date est aujourd'hui
const isToday = (date) => {
    const today = new Date();
    return date.getDate() === today.getDate() &&
        date.getMonth() === today.getMonth() &&
        date.getFullYear() === today.getFullYear();
};

// Obtenir le début de la semaine (lundi) pour une date donnée
function getWeekStart(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1); // Ajustement pour dimanche
    return new Date(d.setDate(diff));
}

// Générer un tableau des jours pour la semaine en cours
const daysOfWeek = computed(() => {
    const days = [];
    const weekStart = new Date(currentWeekStart.value);

    const shortDayNames = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];

    for (let i = 0; i < 7; i++) {
        const date = new Date(weekStart);
        date.setDate(date.getDate() + i);
        days.push({
            shortName: shortDayNames[i],
            date: date
        });
    }

    return days;
});

// Libellé de la semaine en cours
const currentWeekLabel = computed(() => {
    const start = new Date(currentWeekStart.value);
    const end = new Date(start);
    end.setDate(end.getDate() + 6);

    const startDay = start.getDate();
    const endDay = end.getDate();

    // Si le début et la fin sont dans des mois différents
    if (start.getMonth() !== end.getMonth()) {
        const startMonth = start.toLocaleDateString('fr-FR', { month: 'long' });
        const endMonth = end.toLocaleDateString('fr-FR', { month: 'long' });
        return `${startDay} ${startMonth} - ${endDay} ${endMonth} ${end.getFullYear()}`;
    }

    const month = end.toLocaleDateString('fr-FR', { month: 'long' });
    return `${startDay} - ${endDay} ${month} ${end.getFullYear()}`;
});

// Fonctions de navigation
const previousWeek = () => {
    const prevWeek = new Date(currentWeekStart.value);
    prevWeek.setDate(prevWeek.getDate() - 7);
    currentWeekStart.value = prevWeek;
};

const nextWeek = () => {
    const nextWeek = new Date(currentWeekStart.value);
    nextWeek.setDate(nextWeek.getDate() + 7);
    currentWeekStart.value = nextWeek;
};

const goToToday = () => {
    currentWeekStart.value = getWeekStart(new Date());
};

// Formatage de l'heure (HH:MM)
const formatTime = (timeString) => {
    if (!timeString) return '';
    const date = new Date(timeString);
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
};

// Obtenir les devoirs pour un jour spécifique
const getDevoirsForDay = (date) => {
    return devoirsFiltres.value.filter(devoir => {
        const devoirDate = new Date(devoir.date);
        return devoirDate.getDate() === date.getDate() &&
            devoirDate.getMonth() === date.getMonth() &&
            devoirDate.getFullYear() === date.getFullYear();
    });
};

import Button from '@/components/Button.vue';

const handleButtonClick = () => {
    alert('Bouton cliqué !');
};
</script>

<template>
    <main class="m-auto">
        <!-- Section d'accueil -->
        <section class="bg-white">
            <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <!-- Message de bienvenue -->
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl font-bold text-black sm:text-3xl">
                            Bienvenue, {{ user.prenom }} {{ user.nom }} !
                        </h1>
                        <p class="mt-1.5 text-sm text-gray-500">
                            Commençons par créer un devoir ! 🎉
                        </p>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="mt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center">
                        <Button variant="solid" size="small" tag="a" href="/devoirs/new">
                            Créer un devoir
                        </Button>
                        <Button variant="outline" size="small" tag="a" href="/devoirs">
                            Gérer ses devoirs
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section des devoirs -->
        <div class="bg-gray-50 border border-gray-200 w-full">
            <div class="flex flex-col md:flex-row items-start justify-between gap-4 mx-auto py-8 max-w-screen-xl">
                <div class="w-full md:basis-2/3 p-4">
                    <h2 class="text-2xl font-semibold">Calendrier</h2>

                    <p v-if="error" class="error-message border rounded p-3 text-sm font-light border-red-400/20 bg-red-200/10 text-red-900 flex items-center gap-2">
                        ⚠ {{ error }}
                    </p>

                    <div class="calendar-container w-full p-4 bg-white rounded-lg border border-gray-300 space-y-4 mt-4">
                        <div class="calendar-header flex justify-between pb-4 border-b border-gray-200 items-center mb-4">
                            <div class="flex items-center gap-2">
                                <button class="cursor-pointer px-1 py-1 rounded border border-gray-200 hover:bg-gray-200" @click="previousWeek">
                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                  </svg>
                                </button>
                              <button class="cursor-pointer px-1 py-1 rounded border border-gray-200 hover:bg-gray-200" @click="nextWeek">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-600">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                              </button>
                                <button class="cursor-pointer px-2 py-1 rounded hover:bg-gray-200" @click="goToToday">
                                    Aujourd'hui
                                </button>
                            </div>
                            <div class="font-semibold">
                                {{ currentWeekLabel }}
                            </div>
                        </div>

                        <div class="calendar-grid grid grid-cols-7 gap-x-1">
                            <!-- Jours de la semaine -->
                            <div v-for="(day, index) in daysOfWeek" :key="index"
                                 class="flex items-center justify-between text-left py-2 px-2 font-medium bg-gray-50 rounded-t border-b border-gray-200">
                                {{ day.shortName }}
                                <div class="text-sm text-gray-400 font-semibold" :class="isToday(day.date) ? 'text-[#00D478] font-semibold' : ''">
                                    {{ formatDayNumber(day.date) }}
                                </div>
                            </div>

                            <!-- Cellules du calendrier -->
                            <div v-for="(day, index) in daysOfWeek" :key="'day-'+index"
                                 class="min-h-[150px] bg-gray-50 rounded-b p-2"
                                 :class="isToday(day.date) ? 'bg-[#00D478]/10 border-[#00D478]/30' : ''">
                                <!-- Devoirs pour ce jour -->
                                <div v-for="devoir in getDevoirsForDay(day.date)" :key="devoir['@id']"
                                     class="text-xs p-1.5 mb-1 rounded-sm cursor-pointer relative"
                                     :class="getDateDevoirClass(devoir.date)">
                                    <div class="font-medium">{{ devoir.intitule }}</div>
                                    <div>{{ formatTime(devoir.heure) }}</div>
                                    <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l"
                                         :class="devoir.id_categories?.couleur"></div>
                                  <div class="absolute bottom-0 right-0 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                    </svg>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Liste des devoirs -->
                <div class="w-full md:basis-2/5 p-4">
                    <h2 class="text-2xl font-semibold flex items-center gap-2">Liste des devoirs <span class="bg-white border border-gray-200 text-gray-800 text-xs font-semibold me-2 px-2.5 py-0.5 rounded">{{ devoirsFiltres.length }}</span></h2>

                    <p v-if="error" class="error-message border rounded p-3 text-sm font-light border-red-400/20 bg-red-200/10 text-red-900 flex items-center gap-2">
                        ⚠ {{ error }}
                    </p>

                    <!-- Loader de chargement -->
                    <div v-if="loading" class="space-y-4 mt-4">
                        <div class="h-20 bg-gray-300 animate-pulse rounded-lg"></div>
                        <div class="h-20 bg-gray-300 animate-pulse rounded-lg"></div>
                        <div class="h-20 bg-gray-300 animate-pulse rounded-lg"></div>
                    </div>

                    <div v-else-if="devoirsFiltres.length > 0" class="space-y-4 mt-4 max-h-80 overflow-y-auto ">
                        <div v-for="devoir in devoirsFiltres" :key="devoir['@id']" class="flex items-center p-2 rounded-lg border border-gray-300 gap-2 relative bg-white">
                            <div :class="devoir.id_categories.couleur" class="absolute h-full w-2 rounded-tl-lg rounded-bl-lg left-0"></div>
                            <div class="flex items-center gap-4 pl-4 w-full pr-2">
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        class="h-4 w-4"
                                        :checked="isDevoirChecked(devoir.id)"
                                        @change="toggleChecked(devoir)"
                                    />
                                </div>
                                <div class="flex items-start flex-col w-full">
                                    <h3 class="text-lg font-semibold">{{ devoir.intitule }}</h3>
                                    <span class="text-xs font-light uppercase">{{devoir.id_matieres.nom}}</span>
                                    <p class="text-sm font-light">{{ devoir.contenu }}</p>
                                    <div class="flex items-center justify-between w-full">
                                        <div v-if="devoir.id_formatRendu?.intitule && devoir.id_formatRendu.intitule !== 'papier'" class="flex items-start text-sm font-light">
                                            <span>à rendre sur <span class="font-semibold">{{ devoir.id_formatRendu.intitule }}</span></span>
                                        </div>
                                        <Button variant="outline" size="small" tag="a" v-if="devoir.id_formatRendu?.lien" :href="devoir.id_formatRendu.lien">
                                            Rendre le devoir
                                        </Button>
                                    </div>
                                    <div class="absolute right-4 text-xs font-light flex items-center gap-2">
                                        <span class="rounded-lg p-1 px-1.5">
                                            {{ new Date(devoir.date).toLocaleDateString("fr-FR") }} | {{
                                                new Date(devoir.heure).toLocaleTimeString("fr-FR", {
                                                    hour: "2-digit",
                                                    minute: "2-digit",
                                                })
                                            }}
                                        </span>
                                        <p :class="getDateDevoirClass(devoir.date)" class="text-xs font-light p-1 px-2 rounded-lg">
                                            {{ getDateDevoirStatus(devoir.date) }}
                                        </p>
                                    </div>
                                    <div class="absolute right-4 bottom-0 text-xs font-light flex items-center gap-2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Message si aucun devoir -->
                    <p v-else class="text-gray-500 text-left mt-4">
                        Aucun devoir disponible pour le moment.
                    </p>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
/* Animation pulse pour le skeleton */
@keyframes pulse {
    0% {
        opacity: 0.5;
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 1.5s infinite;
}

@media (max-width: 768px) {
    .calendar-grid {
        font-size: 0.75rem;
    }

    .day-cell {
        min-height: 100px;
    }
}
</style>
