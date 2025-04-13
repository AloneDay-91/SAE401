<script setup>
import {inject, onMounted, ref} from 'vue'
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

const triggerToast = inject('triggerToast');

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

    if (!intitule.value || !contenu.value || !matiere1.value || !categorie1.value || !date.value || !heure.value || !rendu1.value || !classe.value) {
        triggerToast("Champs manquants","Veuillez remplir tous les champs obligatoires.", 'error');
        loading.value = false;
        return;
    }

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
    triggerToast("Devoir ajouté","Le devoir a été ajouté avec succès.", 'success');
    router.push("/admin/devoirs");
  } catch (err) {
    console.error("Erreur API :", err.response?.data || err);
    error.value = err.response?.data?.detail || "Erreur lors de l'ajout du devoir";
    triggerToast("Erreur d'ajout","Une erreur est survenue lors de l'ajout du devoir.", 'error');
  }

  loading.value = false;
};
</script>
<template>
    <div class="max-w-2xl m-auto w-full">
        <div class="text-left flex items-center justify-between mt-12">
            <div class="w-full flex justify-start flex-col">
                <h1 class="font-semibold">Ajouter un devoir</h1>
                <p class="text-gray-500 text-sm mt-2">Remplissez le formulaire ci-dessous pour ajouter un nouveau devoir.</p>
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
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Intitulé du devoir</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="intitule" class="hidden w-32.5">Intitulé : </label>
                            <input v-model="intitule" type="text" id="intitule" name="intitule" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
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
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Description du devoir</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="description" class="w-32.5 hidden">Description : </label>
                            <input v-model="contenu" type="text" id="description" name="description" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
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
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Matière du devoir</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="matiere" class="w-32.5 hidden">Matière : </label>
                            <select v-model="matiere1" id="matiere" name="matiere" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
                                <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                                    {{ matiere.nom }}
                                </option>
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
                            <span class="font-normal text-gray-600">Catégorie</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Catégorie du devoir</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="categorie" class="w-32.5 hidden">Catégorie : </label>
                            <select v-model="categorie1" id="categorie" name="categorie" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
                                <option v-for="categorie in categories" :key="categorie.id" :value="categorie.id">
                                    {{ categorie.nom }}
                                </option>
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
                            <span class="font-normal text-gray-600">Date</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Date du devoir</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="date" class="w-32.5 hidden">Date : </label>
                            <input v-model="date" type="date" id="date" name="date" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
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
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Heure du devoir</p>
                    </div>
                    <div class="flex gap-1 items-center mb-3">
                        <label for="heure" class="w-32.5 hidden">Heure : </label>
                        <input v-model="heure" type="time" id="heure" name="heure" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
                    </div>
                </div>
                <div class="my-4 text-gray-200">
                    <hr>
                </div>
                <div class="flex items-center justify-between">
                    <div class="w-full flex justify-start flex-col">
                        <div class="flex items-start justify-items-start">
                            <span class="font-normal text-gray-600">Format de rendu</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Format de rendu du devoir</p>
                    </div>
                    <div class="flex gap-1 items-center mb-3">
                        <label for="rendu" class="w-32.5 hidden">Format de rendu : </label>
                        <select v-model="rendu1" id="rendu" name="rendu" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
                            <option v-for="rendu in format_rendus" :key="rendu.id" :value="rendu.id">
                                {{ rendu.intitule }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="my-4 text-gray-200">
                    <hr>
                </div>
                <div class="flex items-center justify-between">
                    <div class="w-full flex justify-start flex-col">
                        <div class="flex items-start justify-items-start">
                            <span class="font-normal text-gray-600">Classe</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Classe du devoir</p>
                    </div>
                    <div class="flex gap-1 items-center mb-3">
                        <label for="rendu" class="w-32.5 hidden">Classe : </label>
                        <select v-model="classe" id="classe" name="classe" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
                            <option v-for="classe in classes" :key="classe.id" :value="classe.id">
                                {{ classe.intitule }} {{ classe.promo }} {{ classe.type }}
                            </option>
                        </select>
                    </div>
                </div>

                <p v-if="error" style="color: red;">{{ error }}</p> <!-- Affichage des erreurs -->
            </form>
        </div>
        <div class="mt-4 flex items-center justify-end w-full">
            <Button variant="solid" size="small" type="submit" :disabled="loading" @click.prevent="AjouterDevoirs">
                {{ loading ? 'Ajout en cours...' : 'Ajouter le devoir' }}
            </Button>
        </div>
    </div>

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
