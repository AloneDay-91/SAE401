<script setup>
import { ref } from 'vue'
import axios from "axios";
import {RouterLink, useRouter} from 'vue-router'
import { useStore } from 'vuex'
import {ArrowLeft, ArrowRight} from "lucide-vue-next";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const intitule = ref('')
const promo = ref('')
const td = ref('')
const tp = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useStore()


const openModal = (user) => {
  modifierUser.value = { ...user };
  isModalOpen.value = true;
};
const closeModal = () => {
  isModalOpen.value = false;
};

const AjouterClasses = async () => {
    loading.value = true;
    error.value = "";

    const classesData = {
        intitule: intitule.value,
        promo: promo.value,
        td: td.value,
        tp: tp.value
    };

    try {
        await axios.post(`${API_URL}/classes`, classesData, {  // <== Correction ici
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            }
        });
        console.log("Classe ajoutée avec succès");
        router.push("/classes");
    } catch (err) {
        console.error("Erreur API :", err.response?.data || err);
        error.value = err.response?.data?.detail || "Erreur lors de l'ajout de la classe";
    }

    loading.value = false;
};
</script>
<template>
    <form @submit.prevent="AjouterClasses"> <!-- Ajout de @submit.prevent -->
        <div>
            <label for="intitule">Intitulé</label>
            <input v-model="intitule" type="text" id="intitule" name="intitule" required/>
        </div>
        <div>
            <label for="promo">Promo</label>
            <input v-model="promo" type="text" id="promo" name="promo" required/>
        </div>
        <div>
            <label for="td">TD</label>
            <input v-model="td" type="text" id="td" name="td" required/>
        </div>
        <div>
          <label for="tp">TP</label>
          <input v-model="tp" type="text" id="tp" name="tp" required/>
        </div>
        <button type="submit" :disabled="loading">
            {{ loading ? 'Ajout en cours...' : 'Ajouter la classe' }}
        </button>
        <p v-if="error" style="color: red;">{{ error }}</p> <!-- Affichage des erreurs -->
    </form>
    <div class="flex items-center w-full justify-between gap-8 mt-24">
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
        <router-link to="/admin/categories" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
            <div>
                <div class="flex items-center gap-4 justify-end">
                    <div class="text-right">
                        <p class="text-gray-800 font-normal text-sm">Liste des catégories</p>
                        <div class="text-xs text-gray-500 font-light">Gérer les différentes catégories</div>
                    </div>
                    <ArrowRight stroke-width="1.5" size="24" class="mr-2" />
                </div>
            </div>
        </router-link>
    </div>
</template>
