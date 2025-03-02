<script setup>
import axios from "axios";
import {onMounted, ref} from "vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const devoirs = ref([]);
const loading = ref(false);
const error = ref("");

// Fonction pour charger la liste des devoirs
onMounted(async () => {
    try {
        // Récupérer le token depuis le stockage local ou les cookies
        const token = localStorage.getItem('token'); // ou autre source

        // Ajouter le token à l'en-tête d'autorisation
        const response = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        console.log(response.data);
    } catch (e) {
        console.error("Erreur lors de la récupération des devoirs:", e);
    }
});



</script>

<template>
  <main class="max-w-xl m-auto">
      <div class="flex items-center justify-between gap-4">
          <div class="border rounded w-full p-4">Calendar</div>
          <div class="border rounded w-full p-4">liste devoirs
              <div>
                    <p v-if="error" class="error-message border rounded p-3 text-sm font-light border-red-400/20 bg-red-200/10 text-red-900 flex items-center gap-2">
                        <span v-html="errorIcon"></span> {{ error }}
                    </p>
                    <br>
                    <div v-if="loading" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a10 10 0 00-10 10h2zm2 8a8 8 0 018-8h2a10 10 0 00-10-10v2zm8 2a8 8 0 01-8-8h-2a10 10 0 0010 10v-2z"></path>
                        </svg>
                    </div>
                    <div v-else>
                        <div v-for="devoir in devoirs" :key="devoir.id" class="border rounded p-4 mb-4">
                            <h3 class="text-lg font-semibold">{{ devoir.intitule }}</h3>
                            <p class="text-sm font-light">{{ devoir.contenu }}</p>
                        </div>
                    </div>
              </div>
          </div>
      </div>
  </main>
</template>
