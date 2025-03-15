<script setup>
import { onMounted, ref } from 'vue';
import { useStore } from 'vuex';
import {RouterLink, useRouter} from 'vue-router';
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

// Appeler les fonctions au montage du composant
onMounted(() => {
    GetMatieres();
    GetCategories();
    GetClasses();
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
</script>


<template>
  <main class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200">
    <section class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
      <div class="flex items-center justify-between border border-t-0 border-l-0 border-r-0 border-gray-200">
        <div class="sm:flex sm:items-start flex flex-col items-center">
          <h1 class="text-3xl font-extrabold text-gray-900">Nouveau devoir</h1>
          <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Création d'un nouveau devoir</p>
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center">
            <Button variant="solid" size="small" tag="a" href="/devoirs">Retour</Button>
        </div>
      </div>
        <form class="flex flex-wrap gap-12 mt-6 border border-gray-200 rounded-lg mx-auto px-4 py-8 sm:px-6 sm:py-12 lg:px-8" @submit.prevent="AjouterDevoir">
            <div class="flex flex-col gap-2 w-xs">
                <label for="intitule" class="font-medium">Intitulé</label>
                <input type="text" id="intitule" v-model="intitule" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md" placeholder="Entrer un intitulé" required />
            </div>

            <div class="flex flex-col gap-2 w-xs">
                <label for="desc" class="font-medium">Description</label>
                <input type="text" id="desc" v-model="desc" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md" placeholder="Entrer une description" required />
            </div>

            <div class="flex flex-col gap-2">
                <label for="date" class="font-medium">Date</label>
                <input type="date" id="date" v-model="date" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md" required />
            </div>

            <div class="flex flex-col gap-2">
                <label for="heure" class="font-medium">Heure</label>
                <input type="time" id="heure" v-model="heure" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md" required />
            </div>

            <!-- Matières -->
            <div class="flex flex-col gap-2">
                <label for="matiere" class="font-medium">Matière</label>
                <select id="matiere" v-model="matiere" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md" required>
                    <option value="">Sélectionner une matière</option>
                    <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                        {{ matiere.nom }}
                    </option>
                </select>
            </div>

            <!-- Catégories -->
            <div class="flex flex-col gap-2">
                <label for="categorie" class="font-medium">Catégories</label>
                <select id="categorie" v-model="categorie" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md" required>
                    <option value="">Sélectionner une catégorie</option>
                    <option v-for="categorie in categories" :key="categorie.id" :value="categorie.id">
                        {{ categorie.nom }}
                    </option>
                </select>
            </div>

            <!-- Nouveau : Type de filtre -->
            <div class="flex flex-col gap-2">
                <label for="filterType" class="font-medium">Filtrer par :</label>
                <select id="filterType" v-model="filterType" @change="filterClassesByType" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md">
                    <option value="">Sélectionner un filtre</option>
                    <option value="TP">TP</option>
                    <option value="TD">TD</option>
                    <option value="Promo">Promo</option>
                </select>
            </div>

            <!-- Classes -->
            <div v-if="classes.length > 0" class="flex flex-col gap-2 w-xs">
                <label for="classe" class="font-medium">Classe</label>
                <select id="classe" v-model="classe" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md" required>
                    <option value="">Sélectionner une classe</option>
                    <option v-for="classe in classes" :key="classe.id" :value="classe.id">
                        {{ classe.intitule }} (Promo : {{ classe.promo }}) (TP: {{ classe.tp }}) (TD: {{ classe.td }})
                    </option>
                </select>
            </div>

            <!-- Format rendu -->
<!--            <br />-->
<!--            <span>Format de rendu</span>-->

            <!-- Intitulé format rendu -->
            <div class="flex flex-col gap-2">
                <label for="intituler" class="font-medium">Intitulé format rendu</label>
                <input type="text" id="intituler" v-model="intituler" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md" required />
            </div>

            <!-- Lien format rendu -->
            <div class="flex flex-col gap-2">
                <label for="lien" class="font-medium">Lien format rendu</label>
                <input type="text" id="lien" v-model="lien" class="border rounded-md border-gray-200 py-2 px-3 placeholder-gray-300 placeholder:font-light placeholder:text-md" required />
            </div>

            <!-- Bouton soumettre -->
            <div class="mt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center">
                <Button variant="solid" size="small" tag="button" type="submit" :disabled="loading">
                    {{ loading ? 'Ajout en cours...' : 'Ajouter le devoir' }}
                </Button>
            </div>

            <!-- Message d'erreur -->
            <p v-if="error" style="color: red;">{{ error }}</p>
        </form>
      </section>
  </main>
</template>

