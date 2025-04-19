<!--
TABLE CHECKBOX-STATUSES
@id -> int
user_id -> int
devoirs_id -> int
status -> boolean (0 ou 1)

TABLE DEVOIRS
@id -> int
id_users -> int
id_matieres -> int
id_categories -> int
id_format_rendu -> int
intitule -> string
contenu -> string
date -> date
heure -> time
id_classes -> int
-->

<script setup>
import {useStore} from 'vuex';
import axios from "axios";
import {onMounted, ref, inject} from "vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const loading = ref(true);
const error = ref('');
const triggerToast = inject('triggerToast');
const checkboxStatuses = ref([]);

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
</script>

<template>
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
</template>
