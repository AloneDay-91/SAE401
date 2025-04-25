<script setup>
import { ref, onMounted } from 'vue'
import axios from "axios";
import {useRouter} from 'vue-router'
import { useStore } from 'vuex'
import Button from "@/components/Button.vue";
import {pulsar} from 'ldrs'

pulsar.register()

const API_URL = import.meta.env.VITE_API_BASE_URL;

const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useStore()
const devoirs = ref(null)

const idDevoir = router.currentRoute.value.params.id

const formatDate = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toISOString().split('T')[0]; // format YYYY-MM-DD
};

const formatTime = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toTimeString().slice(0, 5); // format HH:MM
};

const GetDevoir = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`${API_URL}/devoirs/${idDevoir}`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            }
        });
        devoirs.value = response.data
        error.value = ''
    } catch (err) {
        console.error("Erreur API :", err.response?.data || err);
        error.value = err.response?.data?.detail || "Erreur lors de la récupération du devoir";
        devoirs.value = null;
    }
    loading.value = false;
};

onMounted(GetDevoir)
</script>

<template>
    <div>
        <div class="mx-4 my-4">
            <section>
                <div v-if="loading" class="text-center my-8">
                    <l-pulsar size="40" speed="1.75" color="#05df72"></l-pulsar>
                </div>
                <div v-else>
                    <div class="mx-auto max-w-screen-md">
                        <div v-if="devoirs">
                            <div class="text-left flex items-center justify-between mt-12">
                                <div class="w-full flex justify-start flex-col">
                                    <h1 class="font-semibold">Devoir n°{{ devoirs.id }}</h1>
                                    <p class="text-gray-500 text-sm mt-2">Les détails du devoir</p>
                                </div>
                                <div class="w-full flex justify-end">
                                    <Button variant="outline" size="small" tag="a" href="/admin/devoirs">Retour</Button>
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg bg-gray-100/50 mt-4">
                                <form class="py-6 px-12 flex-colum">
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Intitulé</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Intitulé du devoir</p>
                                        </div>
                                        <div>
                                            <div class="flex gap-1 items-center mb-3">
                                                <label for="intitule" class="hidden w-32.5">Intitulé : </label>
                                                <input v-model="devoirs.intitule" type="text" id="intitule"
                                                       aria-label="intitulé"
                                                       name="intitule"
                                                       class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                       required disabled/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-4 text-gray-200">
                                        <hr>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Description</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Description du devoir</p>
                                        </div>
                                        <div>
                                            <div class="flex gap-1 items-center mb-3">
                                                <label for="description" class="w-32.5 hidden">Description : </label>
                                                <input v-model="devoirs.contenu" type="text" id="description"
                                                       aria-label="description"
                                                       name="description"
                                                       class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                       required disabled/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-4 text-gray-200">
                                        <hr>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Matière</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Matière du devoir</p>
                                        </div>
                                        <div>
                                            <div class="flex gap-1 items-center mb-3">
                                                <label for="matiere" class="w-32.5 hidden">Matière : </label>
                                                <input v-model="devoirs.id_matieres.nom" id="matiere" name="matiere"
                                                       aria-label="matière"
                                                       class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                       required disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-4 text-gray-200">
                                        <hr>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Catégorie</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Catégorie du devoir</p>
                                        </div>
                                        <div>
                                            <div class="flex gap-1 items-center mb-3">
                                                <label for="categorie" class="w-32.5 hidden">Catégorie : </label>
                                                <input v-model="devoirs.id_categories.nom" id="categorie"
                                                       aria-label="catégorie"
                                                       name="categorie"
                                                       class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                       required disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-4 text-gray-200">
                                        <hr>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Date</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Date du devoir</p>
                                        </div>
                                        <div>
                                            <div class="flex gap-1 items-center mb-3">
                                                <label for="date" class="w-32.5 hidden">Date : </label>
                                                <input :value="formatDate(devoirs.date)" type="date" id="date"
                                                       aria-label="date"
                                                       name="date"
                                                       class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                       required disabled/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-4 text-gray-200">
                                        <hr>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Heure</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Heure du devoir</p>
                                        </div>
                                        <div class="flex gap-1 items-center mb-3">
                                            <label for="heure" class="w-32.5 hidden">Heure : </label>
                                            <input :value="formatTime(devoirs.heure)" type="time" id="heure"
                                                   aria-label="heure"
                                                   name="heure"
                                                   class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                   required disabled/>
                                        </div>
                                    </div>
                                    <div class="my-4 text-gray-200">
                                        <hr>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Format de rendu</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Format de rendu du devoir</p>
                                        </div>
                                        <div class="flex gap-1 items-center mb-3">
                                            <label for="rendu" class="w-32.5 hidden">Format de rendu : </label>
                                            <input v-model="devoirs.id_formatRendu.intitule" id="rendu" name="rendu"
                                                   aria-label="format de rendu"
                                                   class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                   required disabled>
                                        </div>
                                    </div>
                                    <div class="my-4 text-gray-200">
                                        <hr>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Lien du rendu</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Lien du rendu du devoir</p>
                                        </div>
                                        <div class="flex gap-1 items-center mb-3">
                                            <label for="LienRendu" class="w-32.5 hidden">Lien du rendu : </label>
                                            <input v-model="devoirs.id_formatRendu.lien" id="LienRendu" name="LienRendu"
                                                   aria-label="lien du rendu"
                                                   class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                   required disabled>
                                        </div>
                                    </div>
                                    <div class="my-4 text-gray-200">
                                        <hr>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Créateur</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Créateur du devoir</p>
                                        </div>
                                        <div class="flex gap-1 items-center mb-3">
                                            <label for="createur" class="w-32.5 hidden">Créateur : </label>
                                            <input
                                                :value="devoirs.id_users ? `${devoirs.id_users.nom} ${devoirs.id_users.prenom}` : ''"
                                                id="createur"
                                                aria-label="créateur"
                                                name="createur"
                                                class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                required
                                                disabled
                                            >
                                        </div>

                                    </div>
                                    <div class="my-4 text-gray-200">
                                        <hr>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="w-full flex justify-start flex-col">
                                            <div class="flex items-start justify-items-start">
                                                <span class="font-normal text-gray-600">Classe</span>
                                            </div>
                                            <p class="text-gray-500 font-light text-sm">Classe du devoir</p>
                                        </div>
                                        <div class="flex gap-1 items-center mb-3">
                                            <label for="classe" class="w-32 hidden">Classe :</label>
                                            <input
                                                :value="`${devoirs.id_classes.intitule} ${devoirs.id_classes.promo} ${devoirs.id_classes.type}`"
                                                id="classe"
                                                aria-label="classe"
                                                name="classe"
                                                class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                                required
                                                disabled
                                            />
                                        </div>

                                    </div>

                                    <p v-if="error" style="color: red;">{{ error }}</p> <!-- Affichage des erreurs -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
