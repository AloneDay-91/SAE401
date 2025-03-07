<script setup>
import axios from "axios";
import { onMounted, ref, computed } from "vue";
import { useStore } from "vuex";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const user = computed(() => store.state.user); // Récupère l'utilisateur depuis Vuex

const devoirs = ref([]); // Liste des devoirs
const loading = ref(true); // Indique si les données sont en cours de chargement
const error = ref(""); // Message d'erreur

// Fonction pour charger la liste des devoirs
onMounted(async () => {
    try {
        const token = localStorage.getItem("token");

        if (!token) {
            throw new Error("Token d'authentification manquant.");
        }

        const response = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                "Content-Type": "application/ld+json",
                Authorization: `Bearer ${token}`,
            },
        });

        devoirs.value = response.data.member || []; // Assure que `devoirs` est toujours un tableau
        console.log("Devoirs récupérés avec succès:", devoirs.value);
    } catch (e) {
        console.error("Erreur lors de la récupération des devoirs:", e);
        error.value =
            e.response?.data?.detail || "Impossible de récupérer les devoirs.";
    } finally {
        loading.value = false;
    }
});

// Filtrage des devoirs pour l'utilisateur actuel
const devoirsUser = computed(() =>
    devoirs.value.filter(
        (devoir) => devoir.id_users === `/api/users/${user.value.id}`
    )
);
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
                        <router-link
                            to="/devoirs/new"
                            class="px-3 py-1.5 border rounded bg-[#00D478] text-[#004319] border-[#00D478] text-sm font-light"
                        >
                            Créer un devoir
                        </router-link>
                        <router-link
                            to="#"
                            class="px-3 py-1.5 rounded hover:bg-[#00D478]/20 text-[#00D478] text-sm font-light"
                        >
                            Gérer les classes
                        </router-link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section des devoirs -->
        <div class="bg-gray-50 border border-gray-200 w-full">
            <div class="flex flex-col md:flex-row items-start justify-between gap-4 mx-auto py-8 max-w-screen-xl">
                <!-- Liste des devoirs -->
                <div class="w-full p-4">
                    <h2 class="text-lg font-semibold">Liste des devoirs</h2>

                    <!-- Affichage des erreurs -->
                    <p
                        v-if="error"
                        class="error-message border rounded p-3 text-sm font-light border-red-400/20 bg-red-200/10 text-red-900 flex items-center gap-2"
                    >
                        ⚠ {{ error }}
                    </p>

                    <!-- Loader de chargement -->
                    <div v-if="loading" class="space-y-4 mt-4">
                        <div class="h-20 bg-gray-300 animate-pulse rounded-lg"></div>
                        <div class="h-20 bg-gray-300 animate-pulse rounded-lg"></div>
                        <div class="h-20 bg-gray-300 animate-pulse rounded-lg"></div>
                    </div>

                    <!-- Affichage des devoirs filtrés -->
                    <div v-else-if="devoirsUser.length > 0" class="space-y-4 mt-4">
                        <div
                            v-for="devoir in devoirsUser"
                            :key="devoir['@id']"
                            class="border rounded p-4 mb-4 bg-white shadow-md"
                        >
                            <h3 class="text-lg font-semibold">{{ devoir.intitule }}</h3>
                            <p class="text-sm font-light">{{ devoir.contenu }}</p>
                            <p class="text-sm font-light">
                                {{ new Date(devoir.date).toLocaleDateString("fr-FR") }}
                            </p>
                            <p class="text-sm font-light">
                                {{
                                    new Date(devoir.heure).toLocaleTimeString("fr-FR", {
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    })
                                }}
                            </p>
                            <p class="text-sm font-light">{{ devoir.status }}</p>
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

/* Ajustements pour la mise en page responsive */
@media (max-width: 768px) {
    .calendar-grid {
        font-size: 0.75rem;
    }

    .day-cell {
        min-height: 100px;
    }
}
</style>

