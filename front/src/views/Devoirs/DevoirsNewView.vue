<script setup>
import { onMounted, ref, watch } from 'vue';
import { useStore } from 'vuex';
import {useRouter} from 'vue-router';
import axios from 'axios';
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const router = useRouter();

const intitule = ref('');
const desc = ref('');
const date = ref('');
const heure = ref('');
const matiere = ref('');
const categories = ref([]);
const categorie = ref('');
const loading = ref(false);
const error = ref('');
const matieres = ref([]);
const lien = ref('');
const intituler = ref('');
const classes = ref([]);
const classe = ref('');
const format_rendus = ref([]);
const token = localStorage.getItem('token');
const user = ref(store.state.user);

// Nouveau : Type de filtre (TP, TD, Promo)
const filterType = ref(''); // Stocke le choix de l'utilisateur (TP, TD, Promo)

// Fonction pour filtrer les classes dynamiquement
const filterClassesByType = () => {
    if (!filterType.value) {
        return; // Si aucun filtre sélectionné, ne rien faire
    }

    if (filterType.value === 'Promo') {
        // Filtrer par promo
        classes.value = classes.value.filter(classe => classe.promo === user.value.id_classes.promo);
    } else if (filterType.value === 'TP') {
        // Filtrer par TP
        classes.value = classes.value.filter(classe => classe.promo === user.value.id_classes.promo);
        classes.value = classes.value.filter(classe => classe.tp === user.value.id_classes.tp); // Assurez-vous que `tp` est une propriété booléenne ou identifiable
    } else if (filterType.value === 'TD') {
        // Filtrer par TD
        classes.value = classes.value.filter(classe => classe.promo === user.value.id_classes.promo);
        classes.value = classes.value.filter(classe => classe.td === user.value.id_classes.td); // Assurez-vous que `td` est une propriété booléenne ou identifiable
    }
};

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

// Récupérer les formats de rendus
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

// Fonction pour ajouter un devoir
const createFormatRendu = async () => {
    const formatRenduData = {
        intitule: intituler.value,
        lien: lien.value
    };

    try {
        const response = await axios.post(`${API_URL}/format_rendus`, formatRenduData, {
            headers: {
                'Content-Type': 'application/ld+json',
                Authorization: `Bearer ${token}`,
            },
        });
        return response.data;
    } catch (err) {
        console.error('Erreur lors de la création du format rendu:', err.response?.data || err);
        throw err;
    }
};

// 2. Modifier la fonction AjouterDevoir pour utiliser l'IRI du FormatRendu
const AjouterDevoir = async () => {
    if (!token) {
        error.value = 'Authentification requise';
        return;
    }

    if (!categorie.value) {
        error.value = 'Veuillez sélectionner une catégorie.';
        return;
    }

    loading.value = true;
    error.value = '';

    try {
// Créer d'abord le format rendu
        const formatRendu = await createFormatRendu();

        const devoirData = {
            intitule: intitule.value,
            contenu: desc.value,
            date: date.value,
            heure: heure.value,
            status: "A faire",
            id_users: `/api/users/${user.value.id}`,
            id_matieres: `/api/matieres/${matiere.value}`,
            id_categories: `/api/categories/${categorie.value}`,
            id_formatRendu: `/api/format_rendus/${formatRendu.id}`,
            id_devoirVote: null,
            id_checkboxStatus: null,
            id_classes: `/api/classes/${classe.value}`
        };

        console.log(devoirData);

        const response = await axios.post(`${API_URL}/devoirs`, devoirData, {
            headers: {
                'Content-Type': 'application/ld+json',
                Authorization: `Bearer ${token}`,
            },
        });
        console.log(response.data);
        router.push('/devoirs');
    } catch (err) {
        console.error('Erreur API :', err.response?.data || err);
        error.value = err.response?.data?.detail || 'Erreur lors de l\'ajout du devoir';
    } finally {
        loading.value = false;
    }
};

const afficherLien = ref(false);

</script>


<template>
  <main class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200 bg-white">
      <section class="mx-auto max-w-screen-md px-4 py-8 sm:px-6 sm:py-12 lg:px-8">


          <div class="flex items-center justify-between border border-t-0 border-l-0 border-r-0 border-gray-200">
              <div class="sm:flex sm:items-start flex flex-col items-center">
                  <h1 class="text-3xl font-extrabold text-gray-900">Nouveau devoir</h1>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Création d'un nouveau devoir</p>
              </div>
              <div class="mt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center">
                  <Button variant="outline" size="small" tag="a" href="/devoirs">Retour</Button>
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
                              <input v-model="intitule" type="text" id="intitule" name="intitule"
                                     placeholder="Entrer un intitulé"
                                     class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                     required/>
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
                              <input v-model="desc" type="text" id="description" name="description"
                                     placeholder="Entrer une description"
                                     class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                     required/>
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
                              <select v-model="matiere" id="matiere" name="matiere"
                                      class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                      required>
                                  <option value="" selected>Sélectionner une matière</option>
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
                              <select v-model="categorie" id="categorie" name="categorie"
                                      class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                      required>
                                  <option value="" selected>Sélectionner une catégorie</option>
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
                              <input v-model="date" type="date" id="date" name="date"
                                     class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                     required/>
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
                          <input v-model="heure" type="time" id="heure" name="heure"
                                 class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                 required/>
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
                          <select v-model="rendu1" id="rendu" name="rendu"
                                  class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                  required>
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
                          <select v-model="classe" id="classe" name="classe"
                                  class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                  required>
                              <option v-for="classe in classes" :key="classe.id" :value="classe.id">
                                  {{ classe.intitule }} {{ classe.promo }} {{ classe.type }}
                              </option>
                          </select>
                      </div>
                  </div>
                  <div class="my-4 text-gray-200">
                      <hr>
                  </div>
                  <div class="flex flex-row gap-12 mb-4">

                      <div class="flex flex-row justify-between w-full gap-2">
                          <!-- Checkbox pour afficher le champ du lien -->
                          <label class="flex items-center gap-2 text-gray-500 text-sm">
                              <input type="checkbox" v-model="afficherLien" class="form-checkbox border-gray-300">
                              <span class="text-sm font-light">Ajouter un lien</span>
                          </label>

                          <!-- Champ du lien, affiché uniquement si la checkbox est cochée -->
                          <div v-show="afficherLien" class="flex flex-col gap-2">
                              <label for="lien" class="font-light text-gray-500 text-sm hidden">Lien du rendu du
                                  devoir</label>
                              <input type="text" id="lien" v-model="lien"
                                     class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                     placeholder="Entrer un lien"/>
                          </div>
                      </div>
                  </div>

                  <p v-if="error" style="color: red;">{{ error }}</p> <!-- Affichage des erreurs -->
              </form>
          </div>

          <div class="pt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center justify-end">
              <Button variant="solid" size="small" tag="button" type="submit" :disabled="loading">
                  {{ loading ? 'Ajout en cours...' : 'Ajouter le devoir' }}
              </Button>
          </div>

      </section>
  </main>
</template>

