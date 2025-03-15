<script setup>
import { ref } from 'vue'
import axios from "axios";
import {RouterLink, useRouter} from 'vue-router'
import { useStore } from 'vuex'
import {ArrowLeft, ArrowRight} from "lucide-vue-next";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const nom = ref('')
const code = ref('')
const couleur = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useStore()

const AjouterMatieres = async () => {
    loading.value = true;
    error.value = "";

    const matiereData = {
        nom: nom.value,
        code: code.value,
        couleur: couleur.value
    };

    try {
        await axios.post(`${API_URL}/matieres`, matiereData, {  // <== Correction ici
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            }
        });
        console.log("Matière ajoutée avec succès");
        router.push("/matieres");
    } catch (err) {
        console.error("Erreur API :", err.response?.data || err);
        error.value = err.response?.data?.detail || "Erreur lors de l'ajout de la matière";
    }

    loading.value = false;
};
</script>
<template>
    <form @submit.prevent="AjouterMatieres"> <!-- Ajout de @submit.prevent -->
        <div>
            <label for="nom">Nom</label>
            <input v-model="nom" type="text" id="nom" name="nom" required/>
        </div>
        <div>
            <label for="code">Code</label>
            <input v-model="code" type="text" id="code" name="code" required/>
        </div>
        <div>
            <label for="couleur">Couleur</label>
            <input v-model="couleur" type="text" id="couleur" name="couleur" required/>
        </div>
        <button type="submit" :disabled="loading">
            {{ loading ? 'Ajout en cours...' : 'Ajouter la matière' }}
        </button>
        <p v-if="error" style="color: red;">{{ error }}</p> <!-- Affichage des erreurs -->
    </form>
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
