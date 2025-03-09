<script setup>
import { ref } from 'vue'
import axios from "axios";
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

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
</template>
