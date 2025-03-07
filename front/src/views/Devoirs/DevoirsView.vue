<script setup>
import { onMounted, ref } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const user = ref(store.state.user);

const token = localStorage.getItem('token');
const devoirs = ref([]);
const matieres = ref([]);
const categories = ref([]);
const error = ref('');

onMounted(async () => {
    try {
        // Récupérer les devoirs
        const devoirsResponse = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${token}`
            }
        });

        // Filtrer les devoirs pour l'utilisateur actuel
        devoirs.value = devoirsResponse.data.member.filter(devoir => devoir.id_users === `/api/users/${user.value.id}`);

        // Récupérer les matières
        const matieresResponse = await axios.get(`${API_URL}/matieres`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${token}`
            }
        });
        matieres.value = matieresResponse.data.member;

        // Récupérer les catégories
        const categoriesResponse = await axios.get(`${API_URL}/categories`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${token}`
            }
        });
        categories.value = categoriesResponse.data.member;

    } catch (e) {
        console.error('Erreur lors de la récupération des données:', e);
        error.value = 'Impossible de récupérer les données. Veuillez réessayer plus tard.';
    }
});
</script>


<template>
    <div class="mt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center">
        <router-link to="/devoirs/new" class="px-4 py-2 border rounded bg-[#00D478] text-[#004319] border-[#00D478] text-sm font-medium hover:bg-[#00C26F] transition">Créer un devoir</router-link>
    </div>

    <!-- Afficher un message d'erreur si nécessaire -->
    <div v-if="error" class="text-red-500 mt-4">
        {{ error }}
    </div>

    <!-- Tableau des devoirs -->
    <div v-else class="overflow-x-auto mt-4">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
            <thead class="bg-[#00D478] text-white">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">Intitulé</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Description</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Date</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Heure</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Matière</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Catégorie</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="devoirs" v-for="devoir in devoirs" :key="devoir['@id']" class="border-b border-gray-100 hover:bg-gray-50 transition">
                <!-- Intitulé -->
                <td class="px-6 py-4 text-sm text-gray-700">{{ devoir.intitule }}</td>

                <!-- Description -->
                <td class="px-6 py-4 text-sm text-gray-700">{{ devoir.contenu }}</td>

                <!-- Date -->
                <td class="px-6 py-4 text-sm text-gray-700">{{ new Date(devoir.date).toLocaleDateString('fr-FR') }}</td>

                <!-- Heure (avec ajustement de fuseau horaire) -->
                <td class="px-6 py-4 text-sm text-gray-700">
                    {{ new Date(new Date(devoir.heure).setHours(new Date(devoir.heure).getHours() - 1)).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) }}
                </td>

                <!-- Statut -->
                <td class="px-6 py-4 text-sm text-gray-700">{{ devoir.status }}</td>

                <!-- Matière -->
                <td class="px-6 py-4 text-sm text-gray-700">
                    {{ matieres.find(matiere => matiere['@id'] === devoir.id_matieres)?.nom || 'Non défini' }}
                    ({{ matieres.find(matiere => matiere['@id'] === devoir.id_matieres)?.code || 'N/A' }})
                </td>

                <!-- Catégorie -->
                <td class="w-auto px-6 py-4">
                    <div :class="[
                                `bg-${categories.find(categorie => categorie['@id'] === devoir.id_categories)?.couleur || 'gray'}-200`,
                                `text-${categories.find(categorie => categorie['@id'] === devoir.id_categories)?.couleur || 'gray'}-500`,
                                'text-center rounded text-sm font-light py-1 px-3'
                            ]">
                        {{ categories.find(categorie => categorie['@id'] === devoir.id_categories)?.nom || 'Non défini' }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="7" v-if="!devoirs.length" class="text-gray-500 text-center py-4">Aucun devoir disponible pour le moment.</td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Router View -->
    <router-view></router-view>
</template>



