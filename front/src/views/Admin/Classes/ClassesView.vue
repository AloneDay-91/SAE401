<script setup>
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import {RouterLink} from "vue-router";
import {ArrowLeft, ArrowRight, Ellipsis, FilePenLine, Trash2} from "lucide-vue-next";
import DropdownMenu from "@/components/DropdownMenu.vue";
import Button from "@/components/Button.vue";

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
            <Button variant="solid" size="small" tag="a" href="classes/new">Ajouter une classes</Button>
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
                                            <Button class="inline-flex hover:cursor-pointer" variant="ghost" size="small">
                                                <Ellipsis stroke-width="1.5" size="16" />
                                            </Button>
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
                    <div class="flex items-center w-full justify-between gap-8 mt-24">
                        <router-link to="/admin/devoirs" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-start">
                                    <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
                                    <div class="text-left">
                                        <p class="text-gray-800 font-normal text-sm">Liste des devoirs</p>
                                        <div class="text-xs text-gray-500 font-light">Gérer les différents devoirs</div>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                        <router-link to="/admin/classes/new" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-end">
                                    <div class="text-right">
                                        <p class="text-gray-800 font-normal text-sm">Nouvelle classe</p>
                                        <div class="text-xs text-gray-500 font-light">Ajouter une nouvelle classe</div>
                                    </div>
                                    <ArrowRight stroke-width="1.5" size="24" class="mr-2" />
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>