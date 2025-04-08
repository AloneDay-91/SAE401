<script setup>
import {onMounted, ref} from 'vue'
import axios from "axios";
import {RouterLink, useRouter} from 'vue-router'
import { useStore } from 'vuex'
import {ArrowLeft, ArrowRight} from "lucide-vue-next";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const matieres = ref([])
const categories = ref([])
const format_rendus = ref([])
const classes = ref([])

const intitule = ref('')
const contenu = ref('')
const matiere1 = ref('')
const categorie1 = ref('')
const date = ref('')
const heure = ref('')
const rendu1 = ref('')
const status = ref('')
const classe = ref('');

const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useStore()
const token = localStorage.getItem('token');
const user = ref(store.state.user);

// Récupérer les matières
const GetMatieres = async () => {
  if (!token) {
    error.value = 'Authentification requise';
    return;
  }

  try {
    const response = await axios.get(`${API_URL}/matieres`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    matieres.value = response.data.member;
  } catch (e) {
    console.error('Erreur lors de la récupération des matières:', e);
    error.value = 'Impossible de récupérer les matières.';
  }
};

// Récupérer les catégories
const GetCategories = async () => {
  if (!token) {
    error.value = 'Authentification requise';
    return;
  }

  try {
    const response = await axios.get(`${API_URL}/categories`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    categories.value = response.data.member;
  } catch (e) {
    console.error('Erreur lors de la récupération des catégories:', e);
    error.value = 'Impossible de récupérer les catégories.';
  }
};

// Récupérer les classes
const GetClasses = async () => {
  if (!token) {
    error.value = 'Authentification requise';
    return;
  }

  try {
    const response = await axios.get(`${API_URL}/classes`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    classes.value = response.data.member;
  } catch (e) {
    console.error('Erreur lors de la récupération des classes:', e);
    error.value = 'Impossible de récupérer les classes.';
  }
};

const GetRendus = async () => {
  if (!token) {
    error.value = 'Authentification requise';
    return;
  }

  try {
    const response = await axios.get(`${API_URL}/format_rendus`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    format_rendus.value = response.data.member;
  } catch (e) {
    console.error('Erreur lors de la récupération des formats de rendu:', e);
    error.value = 'Impossible de récupérer les formats de rendu.';
  }
};

// Appeler les fonctions au montage du composant
onMounted(() => {
  GetMatieres();
  GetCategories();
  GetClasses();
  GetRendus();
});


const AjouterDevoirs = async () => {
  loading.value = true;
  error.value = "";

  console.log("Promo utilisateur :", classe.value);

  const devoirData = {
    intitule: intitule.value,
    contenu: contenu.value,
    matiere: matiere1.value,
    categorie: categorie1.value,
    date: date.value,
    heure: heure.value,
    rendu: rendu1.value,
    status: 'A faire',
    id_users: `/api/users/${user.value.id}`,
    id_matieres: `/api/matieres/${matiere1.value}`,
    id_categories: `/api/categories/${categorie.value}`,
    id_formatRendu: `/api/format_rendus/${rendu1.value}`,
    id_classes: `/api/classes/${classe.value}`
  };

  try {
    await axios.post(`${API_URL}/devoirs`, devoirData, {  // <== Correction ici
      headers: {
        "Content-Type": "application/ld+json",
        "Authorization": `Bearer ${store.state.token}`
      }
    });
    console.log("Devoir ajouté avec succès");
    router.push("/admin/devoirs");
  } catch (err) {
    console.error("Erreur API :", err.response?.data || err);
    error.value = err.response?.data?.detail || "Erreur lors de l'ajout du devoir";
  }

  loading.value = false;
};
</script>
<template>
  <form @submit.prevent="AjouterDevoirs" class="py-6 px-12 flex-column"> <!-- Ajout de @submit.prevent -->
    <div class="flex gap-1 items-center mb-3">
      <label for="intitule" class="w-32.5">Intitulé : </label>
      <input v-model="intitule" type="text" id="intitule" name="intitule" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="description" class="w-32.5">Description : </label>
      <input v-model="contenu" type="text" id="description" name="description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="matiere" class="w-32.5">Matière : </label>
      <select v-model="matiere1" id="matiere" name="matiere" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
        <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
          {{ matiere.nom }}
        </option>
      </select>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="categorie" class="w-32.5">Catégorie : </label>
      <select v-model="categorie1" id="categorie" name="categorie" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
        <option v-for="categorie in categories" :key="categorie.id" :value="categorie.id">
          {{ categorie.nom }}
        </option>
      </select>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="date" class="w-32.5">Date : </label>
      <input v-model="date" type="date" id="date" name="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="heure" class="w-32.5">Heure : </label>
      <input v-model="heure" type="time" id="heure" name="heure" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="rendu" class="w-32.5">Format de rendu : </label>
      <select v-model="rendu1" id="rendu" name="rendu" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
        <option v-for="rendu in format_rendus" :key="rendu.id" :value="rendu.id">
          {{ rendu.intitule }}
        </option>
      </select>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="rendu" class="w-32.5">Classe : </label>
      <select v-model="classe" id="classe" name="classe" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
        <option v-for="classe in classes" :key="classe.id" :value="classe.id">
          {{ classe.intitule }} {{ classe.promo }} {{ classe.type }}
        </option>
      </select>
    </div>
    <Button variant="solid" size="small" type="submit" :disabled="loading">
      {{ loading ? 'Ajout en cours...' : 'Ajouter le devoir' }}
    </Button>
    <p v-if="error" style="color: red;">{{ error }}</p> <!-- Affichage des erreurs -->
  </form>


  <div class="flex items-center w-full justify-between gap-8 mt-24 px-4">
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
