<script setup>
import { ref, onMounted } from 'vue'
import axios from "axios";
import {RouterLink, useRouter} from 'vue-router'
import { useStore } from 'vuex'
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useStore()
const devoirs = ref(null)

const idDevoir = router.currentRoute.value.params.id

const formatDate = (date) => {
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(date).toLocaleDateString('fr-FR', options);
};

const formatTime = (date) => {
    const options = { hour: '2-digit', minute: '2-digit' };
    return new Date(date).toLocaleTimeString('fr-FR', options);
};

const GetDevoir = async () => {
    try {
        const response = await axios.get(`${API_URL}/devoirs/${idDevoir}`, {  // <== Correction ici
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            }
        });
        devoirs.value = response.data
        console.log(devoirs.value)
    } catch (err) {
        console.error("Erreur API :", err.response?.data || err);
        error.value = err.response?.data?.detail || "Erreur lors de l'ajout de la matière";
    }

    loading.value = false;
};

onMounted(GetDevoir)

</script>
<template>
    <div class="flex items-center justify-between text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div>
            <p class="text-gray-800 font-semibold">Devoir n°{{idDevoir}}</p>
        </div>
        <div class="">
            <Button variant="solid" size="small" tag="a" href="/admin/devoirs">Retour</Button>
        </div>
    </div>
    <div>
        <div v-if="error" class="text-red-500 mt-4">
            {{ error }}
        </div>
        <div class="mx-4 my-4">
            <section>
                <div class="mx-auto max-w-screen-xl">
                    <div class="">
                        <div class="rounded-lg divide-y divide-gray-200 ring-1 ring-gray-200 shadow bg-white">
                            <div v-if="devoirs" class="gap-4 flex flex-col px-4 py-5 sm:p-6">
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">Intitulé</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">l'intitulé du devoir</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{devoirs.intitule}}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">Description</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">Description du devoir</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{devoirs.contenu}}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">Date</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">La date du devoir</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{ formatDate(devoirs.date) }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">Heure</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">L'heure du devoir</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{formatTime(devoirs.heure)}}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">L'utilisateur</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">Le créateur du devoir</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{devoirs.id_users.nom}} {{devoirs.id_users.prenom}}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">Matière</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">La matière du devoir</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{devoirs.id_matieres.nom}}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">Catégorie</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">La catégorie du devoir</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{devoirs.id_categories.nom}}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">Classe</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">La classe du devoir</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{devoirs.id_classes.intitule}} {{devoirs.id_classes.promo}} {{devoirs.id_classes.td}} {{devoirs.id_classes.tp}}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">Plateforme rendu</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">La plateforme du rendu</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{devoirs.id_formatRendu.intitule}}</span>
                                    </div>
                                </div>
                                <div v-if="devoirs.id_formatRendu.lien === null" class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                    <div>
                                        <div class="">
                                            <div class="flex content-center items-center justify-between text-sm">
                                                <label for="maintenance_toggle" class="block font-medium text-gray-700">Lien rendu</label>
                                            </div>
                                            <p class="text-gray-500 text-sm">Le lien du rendu</p>
                                        </div>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span>{{devoirs.id_formatRendu.lien}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
