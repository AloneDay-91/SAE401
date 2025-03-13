<script setup>
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import {RouterLink} from "vue-router";
import {FilePenLine, Trash2} from "lucide-vue-next";
import DropdownMenu from "@/components/DropdownMenu.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const classes = ref([]);
const error = ref('');

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
        console.error('Erreur lors de la récupération des matières:', e);
        error.value = 'Impossible de récupérer les matières.';
    }
});
</script>
<template>
    <div class="flex items-center justify-between text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div>
            <p class="text-gray-800 font-semibold">Liste des classes</p>
            <div class="mt-1 text-sm text-gray-500">Gérer les différentes classes</div>
        </div>
        <div class="">
            <router-link to="classes/new"
                         class="px-3 py-1.5 border rounded bg-[#00D478] text-[#004319] border-[#00D478] text-sm font-light">
                Ajouter une classes
            </router-link>
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
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">ID</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Promo</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">TP</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">TD</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="border-b border-gray-200" v-for="classe in classes">
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.id }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.intitule }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.promo }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.td }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.tp }}</td>
                                <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                    <DropdownMenu>
                                        <!-- Personnalisation du bouton déclencheur -->
                                        <template #trigger>
                                            <button
                                                class="flex items-center px-2 py-1.5 text-xs border border-gray-300 text-gray-500 rounded-md">
                                                Options
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                        </template>

                                        <div class="px-2">
                                            <router-link :to="`classes/${classe.id}/edit`"
                                                         class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                                <span>Modifier</span>
                                                <FilePenLine stroke-width="1.5" size="16"/>
                                            </router-link>
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
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>