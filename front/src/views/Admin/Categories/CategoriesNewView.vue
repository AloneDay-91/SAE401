<script setup>
import { ref } from 'vue'
import axios from "axios";
import {RouterLink, useRouter} from 'vue-router'
import { useStore } from 'vuex'
import {ArrowLeft, ArrowRight} from "lucide-vue-next";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const nom = ref('')
const couleur = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useStore()

const AjouterCategories = async () => {
  loading.value = true;
  error.value = "";

  const CategorieData = {
    nom: nom.value,
    couleur: couleur.value
  };

  try {
    await axios.post(`${API_URL}/categories`, CategorieData, {  // <== Correction ici
      headers: {
        "Content-Type": "application/ld+json",
        "Authorization": `Bearer ${store.state.token}`
      }
    });
    console.log("Catégorie ajoutée avec succès");
    router.push("/admin/categories");
  } catch (err) {
    console.error("Erreur API :", err.response?.data || err);
    error.value = err.response?.data?.detail || "Erreur lors de l'ajout de la catégorie";
  }

  loading.value = false;
};
</script>
<template>
  <form @submit.prevent="AjouterCategories" class="py-6 px-12 flex-column"> <!-- Ajout de @submit.prevent -->
    <div class="flex gap-1 items-center mb-3">
      <label for="nom" class="w-17.5">Nom : </label>
      <input v-model="nom" type="text" id="nom" name="nom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="couleur" class="w-17.5">Couleur : </label>
      <input v-model="couleur" type="text" id="couleur" name="couleur" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <Button variant="solid" size="small" type="submit" :disabled="loading">
      {{ loading ? 'Ajout en cours...' : 'Ajouter la catégorie' }}
    </Button>
    <p v-if="error" style="color: red;">{{ error }}</p> <!-- Affichage des erreurs -->
  </form>


  <div class="flex items-center w-full justify-between gap-8 mt-24">
    <router-link to="/admin/categories" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
      <div>
        <div class="flex items-center gap-4 justify-start">
          <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
          <div class="text-left">
            <p class="text-gray-800 font-normal text-sm">Liste des catégories</p>
            <div class="text-xs text-gray-500 font-light">Gérer les différentes catégories</div>
          </div>
        </div>
      </div>
    </router-link>
    <router-link to="/admin/users" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
      <div>
        <div class="flex items-center gap-4 justify-end">
          <div class="text-right">
            <p class="text-gray-800 font-normal text-sm">Liste des utilisateurs</p>
            <div class="text-xs text-gray-500 font-light">Gérer les différents utilisateurs</div>
          </div>
          <ArrowRight stroke-width="1.5" size="24" class="mr-2" />
        </div>
      </div>
    </router-link>
  </div>
</template>
