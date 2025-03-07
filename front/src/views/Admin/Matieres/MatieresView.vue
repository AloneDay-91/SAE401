<script setup>
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const matieres = ref([]);
const error = ref('');

onMounted(async () => {
    try {
        const response = await axios.get(`${API_URL}/matieres`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        matieres.value = response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des matières:', e);
        error.value = 'Impossible de récupérer les matières.';
    }
});
</script>
<template>
    <div class="flex items-center justify-between text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div>
            <p class="text-gray-800 font-semibold">Liste des matières</p>
            <div class="mt-1 text-sm text-gray-500">Gérer les différentes matières</div>
        </div>
        <div class="">
            <router-link to="matieres/new" class="px-3 py-1.5 border rounded bg-[#00D478] text-[#004319] border-[#00D478] text-sm font-light">Ajouter une matière</router-link>
        </div>
    </div>

    <div class="mx-4 my-4">
        <section>
            <div>
                <div>
                    <div class="max-w-full overflow-x-auto border border-gray-200 border-b-0 rounded-lg">
                        <table class="w-full text-left text-gray-500">
                            <thead class="border-b border-gray-200">
                                <tr class="uppercase">
                                    <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Nom</th>
                                    <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Code</th>
                                    <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Couleur</th>
                                    <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-200" v-for="matiere in matieres">
                                    <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ matiere.nom }}</td>
                                    <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ matiere.code }}</td>
                                    <td class="px-6 py-4 text-xs font-normal w-auto">
                                        <span class="h-4 rounded-lg w-auto py-1 px-2" :class="matiere.couleur">{{matiere.couleur}}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-normal w-auto">
                                        <router-link :to="`matieres/${matiere.id}/edit`" class="text-[#00D478] hover:text-[#004319]">Modifier</router-link>
                                        <router-link :to="`matieres/${matiere.id}/delete`" class="text-[#00D478] hover:text-[#004319]">Supprimer</router-link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>