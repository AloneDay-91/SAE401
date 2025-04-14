<script setup>
import { inject, ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import Button from '@/components/Button.vue';
import { ArrowLeft, ArrowRight } from "lucide-vue-next";


const API_URL = import.meta.env.VITE_API_BASE_URL;

const intitule = ref('');
const promo = ref('');
const type = ref('');

const loading = ref(false);
const error = ref('');
const router = useRouter();
const store = useStore();

const triggerToast = inject('triggerToast');

// Liste des promotions en fonction de l'intitulé
const promoOptions = {
    '1ère année': ['S1/S2'],
    '2ème année': ['S3/S4'],
    '3ème année': ['S5/S6'],
};

// Options filtrées dynamiquement selon l'intitulé sélectionné
const filteredPromoOptions = computed(() => promoOptions[intitule.value] || []);

// Fonction pour ajouter une classe
const AjouterClasse = async () => {
    loading.value = true;
    error.value = '';

    // Validation des champs obligatoires
    if (!intitule.value || !promo.value || !type.value) {
        triggerToast(
            'Champs manquants',
            'Veuillez remplir tous les champs obligatoires.',
            'error'
        );
        loading.value = false;
        return;
    }

    // Vérification de l'existence de la classe
    const classeExistante = await VerifClasseExistante();
    if (classeExistante) {
        loading.value = false;
        return;
    }

    // Données à envoyer à l'API
    const classeData = {
        intitule: intitule.value,
        promo: promo.value,
        type: type.value,
    };

    try {
        await axios.post(`${API_URL}/classes`, classeData, {
            headers: {
                'Content-Type': 'application/ld+json',
                Authorization: `Bearer ${store.state.token}`,
            },
        });
        triggerToast(
            'Classe ajoutée',
            'La classe a été ajoutée avec succès.',
            'success'
        );
        router.push('/admin/classes');
    } catch (err) {
        console.error('Erreur API :', err.response?.data || err);
        error.value =
            err.response?.data?.detail || "Erreur lors de l'ajout de la classe";
        triggerToast(
            'Erreur d\'ajout',
            "Une erreur est survenue lors de l'ajout de la classe.",
            'error'
        );
    } finally {
        loading.value = false;
    }
};

// Vérification si une classe existe déjà avec les mêmes données
async function VerifClasseExistante() {
    try {
        const response = await axios.get(
            `${API_URL}/classes?intitule=${intitule.value}&promo=${promo.value}&type=${type.value}`,
            {
                headers: {
                    'Content-Type': 'application/ld+json',
                    Authorization: `Bearer ${store.state.token}`,
                },
            }
        );

        if (response.data.length > 0) {
            triggerToast(
                'Classe existante',
                'Une classe avec le même intitulé, promo et type existe déjà.',
                'error'
            );
            return true;
        }
        return false;
    } catch (error) {
        console.error('Erreur API :', error.response?.data || error);
        triggerToast(
            'Erreur',
            "Impossible de vérifier l'existence de la classe.",
            'error'
        );
        return false;
    }
}
</script>

<template>
    <div class="max-w-2xl m-auto w-full">
        <div class="text-left flex items-center justify-between mt-12">
            <div class="w-full flex justify-start flex-col">
                <h1 class="font-semibold">Ajouter une classe</h1>
                <p class="text-gray-500 text-sm mt-2">Remplissez le formulaire ci-dessous pour ajouter une nouvelle classe.</p>
            </div>
            <div class="w-full flex justify-end">
                <Button variant="outline" size="small" tag="a" href="/admin/classes">Retour</Button>
            </div>
        </div>
        <div class="border border-gray-200 rounded-lg bg-gray-100/50 mt-4">
            <form class="py-6 px-12 flex-colum">
                <div class="flex items-center justify-between">
                    <div class="w-full flex justify-start flex-col">
                        <div class="flex items-start justify-items-start">
                            <span class="font-normal text-gray-600">Intitulé</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Intitulé d'une classe</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="intitule" class="hidden w-32.5">Intitulé : </label>
                            <select v-model="intitule" id="intitule" name="intitule" class="bg-white border border-gray-300 text-gray-500 font-light text-xs rounded-lg block p-2 py-1.5 w-50" required>
                                <option value="" selected>Sélectionner un intitulé</option>
                                <option value="1ère année">1ère année</option>
                                <option value="2ème année">2ème année</option>
                                <option value="3ème année">3ème année</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="my-4 text-gray-200">
                    <hr>
                </div>
                <div class="flex items-center justify-between">
                    <div class="w-full flex justify-start flex-col">
                        <div class="flex items-start justify-items-start">
                            <span class="font-normal text-gray-600">Promotion</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Promotion d'une classe</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="promo" class="w-32.5 hidden">Promotion : </label>
                            <select v-model="promo" id="promo" name="promo" class="bg-white border border-gray-300 text-gray-500 text-xs font-light rounded-lg block p-2 py-1.5 w-50" required>
                                <option value="" selected>Sélectionner une promo</option>
                                <!-- Options dynamiques basées sur "intitule" -->
                                <option v-for="option in filteredPromoOptions" :key="option" :value="option">{{ option }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="my-4 text-gray-200">
                    <hr>
                </div>
                <div class="flex items-center justify-between">
                    <div class="w-full flex justify-start flex-col">
                        <div class="flex items-start justify-items-start">
                            <span class="font-normal text-gray-600">TD/TP</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Le type d'une classe</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="type" class="w-32.5 hidden">Classe : </label>
                            <select v-model="type" id="type" name="type" class="bg-white border border-gray-300 text-gray-500 font-light text-xs rounded-lg block p-2 py-1.5 w-50" required>
                                <option value="" selected>Sélectionner une classe</option>
                                <option value="TPA">TPA</option>
                                <option value="TPB">TPB</option>
                                <option value="TPC">TPC</option>
                                <option value="TPD">TPD</option>
                                <option value="TPE">TPE</option>
                                <option value="TPF">TPF</option>
                                <option value="TPG">TPG</option>
                                <option value="TPH">TPH</option>
                                <option value="TDAB">TDAB</option>
                                <option value="TDCD">TDCD</option>
                                <option value="TDEF">TDEF</option>
                                <option value="TDGH">TDGH</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-4 flex items-center justify-end w-full">
            <Button variant="solid" size="small" type="submit" :disabled="loading" @click.prevent="AjouterClasse">
                {{ loading ? 'Ajout en cours...' : 'Ajouter la classe' }}
            </Button>
        </div>
    </div>

    <div class="flex items-center w-full justify-between gap-8 mt-24 px-4">
        <router-link to="/admin/classes" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
            <div>
                <div class="flex items-center gap-4 justify-start">
                    <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
                    <div class="text-left">
                        <p class="text-gray-800 font-normal text-sm">Liste des classes</p>
                        <div class="text-xs text-gray-500 font-light">Gérer les différentes classes</div>
                    </div>
                </div>
            </div>
        </router-link>
        <router-link to="/admin/classes" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
            <div>
                <div class="flex items-center gap-4 justify-end">
                    <div class="text-right">
                        <p class="text-gray-800 font-normal text-sm">Liste des classes</p>
                        <div class="text-xs text-gray-500 font-light">Gérer les différentes classes</div>
                    </div>
                    <ArrowRight stroke-width="1.5" size="24" class="mr-2" />
                </div>
            </div>
        </router-link>
    </div>
</template>
