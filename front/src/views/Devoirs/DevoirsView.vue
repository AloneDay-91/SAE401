<script setup>
import {computed, onMounted, ref} from 'vue';
import {useStore} from 'vuex';
import axios from 'axios';
import DropdownMenu from "@/components/DropdownMenu.vue";
import {RouterLink} from "vue-router";
import {Eye, FilePenLine, Trash2} from "lucide-vue-next";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const user = ref(store.state.user);

const token = localStorage.getItem('token');
const devoirs = ref([]);
const error = ref('');

onMounted(async () => {
    try {
        const response = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        devoirs.value = response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des devoirs:', e);
        error.value = 'Impossible de récupérer les devoirs.';
    }
});

const devoirsUtilisateur = computed(() => {
    if (!devoirs.value || !user.value || !user.value.id) {
        return [];
    }
    return devoirs.value.filter(devoir => {
        // Vérification que devoir.id_users existe et que son @id correspond à l'utilisateur connecté
        return devoir.id_users && devoir.id_users['@id'] === `/api/users/${user.value.id}`;
    });
});

const formatDate = (date) => {
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(date).toLocaleDateString('fr-FR', options);
};

const formatTime = (date) => {
    const options = { hour: '2-digit', minute: '2-digit' };
    return new Date(date).toLocaleTimeString('fr-FR', options);
};

const isModalOpen = ref(false);
const selectedDevoirId = ref(null);

const openModal = (devoirId) => {
    selectedDevoirId.value = devoirId;
    isModalOpen.value = true;
};

const closeModal = () => {
    selectedDevoirId.value = null;
    isModalOpen.value = false;
};

const deleteDevoir = async () => {
    if (!selectedDevoirId.value) return;

    try {
        await axios.delete(`${API_URL}/devoirs/${selectedDevoirId.value}`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${token}`
            },
        });
        closeModal();
        devoirs.value = devoirs.value.filter(devoir => devoir.id !== selectedDevoirId.value);
        // afficher alert de succès

    } catch (e) {
        console.error('Erreur lors de la suppression du devoir:', e);
        error.value = 'Impossible de supprimer le devoir.';
    }
};
</script>


<template>
    <section class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200">
        <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="flex items-center justify-between border border-t-0 border-l-0 border-r-0 border-gray-200">
                <div class="sm:flex sm:items-start flex flex-col items-center">
                    <h1 class="text-3xl font-extrabold text-gray-900">Mes devoirs</h1>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Gérez mes devoirs</p>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center">
                    <Button variant="solid" size="small" tag="a" href="/devoirs/new">Créer un devoir</Button>
                </div>
            </div>
            <div class="mt-8">
                <div v-if="error" class="text-red-500 mt-4">
                    {{ error }}
                </div>

                <!-- Tableau des devoirs -->
                <div v-else class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                        <thead class="border-b border-gray-200">
                        <tr class="uppercase bg-gray-100">
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Date</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Heure</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Matière</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Catégorie</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Classes</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="devoirsUtilisateur.length > 0" v-for="devoir in devoirsUtilisateur" :key="devoir['@id']" class="border-b border-gray-200">
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.intitule }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatDate(devoir.date) }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatTime(devoir.heure) }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_matieres.nom }} {{ devoir.id_matieres.code }}</td>
                            <td class="w-auto px-6 py-4">
                                <div :class="[
                                `bg-${devoir.id_categories.couleur || 'gray'}-200`,
                                `text-${devoir.id_categories.couleur || 'gray'}-500`,
                                'text-center rounded text-xs font-normal py-1 px-3'
                            ]">
                                    {{ devoir.id_categories.nom || 'Non défini' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                {{devoir.id_classes.promo}}
                                {{devoir.id_classes.td}}
                                {{devoir.id_classes.tp}}
                            </td>
                            <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                <DropdownMenu>
                                    <!-- Personnalisation du bouton déclencheur -->
                                    <template #trigger>
                                        <Button class="inline-flex" variant="outline" size="small">
                                            Options
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </Button>
                                    </template>

                                    <div class="px-2">
                                        <router-link :to="`devoirs/${devoir.id}`" class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                            <span>Voir</span>
                                            <Eye stroke-width="1.5" size="16" />
                                        </router-link>
                                        <router-link :to="`devoirs/${devoir.id}/edit`" class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                            <span>Modifier</span>
                                            <FilePenLine stroke-width="1.5" size="16"/>
                                        </router-link>
                                        <hr class="text-gray-200">
                                        <button @click="openModal(devoir.id)" class="w-full py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                            <span class="text-red-600">Supprimer</span>
                                            <Trash2 stroke-width="1.5" size="16"/>
                                        </button>
                                    </div>
                                </DropdownMenu>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" v-if="devoirsUtilisateur.length === 0" class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                Aucun devoir disponible pour le moment.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg mb-2">Supprimer le devoir</h2>
                <p class="text-xs font-light">Êtes-vous sûr de vouloir supprimer ce devoir ?</p>
                <div class="mt-4 flex justify-end gap-2">
                    <button @click="closeModal" class="bg-gray-200 px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-gray-300 transition">Annuler</button>
                    <button @click="deleteDevoir" class="bg-red-600 text-white px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-red-700 transition">Supprimer</button>
                </div>
            </div>
        </div>

    </section>

    <!-- Router View -->
    <router-view></router-view>
</template>



