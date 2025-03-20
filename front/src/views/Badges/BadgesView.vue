<script setup>
import Button from "@/components/Button.vue";
import { ref, computed, onMounted } from "vue";
import axios from 'axios';
import {useStore} from "vuex";

const API_URL = import.meta.env.VITE_API_BASE_URL;
const store = useStore();
const user = ref(store.state.user);
const token = localStorage.getItem('token');
const devoirsAjoutes = ref(0);

// Fonction pour récupérer le nombre de devoirs ajoutés
const fetchDevoirsAjoutes = async () => {
  try {
    const response = await axios.get(`${API_URL}/devoirs`, {
      headers: {
        "Content-Type": "application/ld+json",
        "Authorization": `Bearer ${token}`
      },
      params: {
        id_users: `/api/users/${user.value.id}`,
      },
    });
    devoirsAjoutes.value = response.data.totalItems;
  } catch (error) {
    console.error("Erreur lors de la récupération des devoirs ajoutés :", error);
    devoirsAjoutes.value = 0;
  }
};

onMounted(() => {
  if (user.value && user.value.id) {
    fetchDevoirsAjoutes();
  } else {
    console.error("Utilisateur non connecté ou ID manquant");
  }
});

// Définir les niveaux et leurs objectifs
const niveaux = [
  { niveau: 1, objectif: 10, badge: "Ajouter_Lvl_1.svg" },
  { niveau: 2, objectif: 20, badge: "Ajouter_Lvl_2.svg" },
  { niveau: 3, objectif: 30, badge: "Ajouter_Lvl_3.svg" },
];

// Calculer le niveau actuel en fonction du nombre de devoirs ajoutés
const niveauActuel = computed(() => {
  for (let i = niveaux.length - 1; i >= 0; i--) {
    if (devoirsAjoutes.value >= niveaux[i].objectif) {
      return niveaux[i];
    }
  }
  return niveaux[0];
});

// Calculer l'objectif du prochain niveau
const prochainObjectif = computed(() => {
  const indexNiveauActuel = niveaux.findIndex(n => n.niveau === niveauActuel.value.niveau);
  if (indexNiveauActuel < niveaux.length - 1) {
    return niveaux[indexNiveauActuel + 1].objectif;
  }
  return niveauActuel.value.objectif;
});

// Calculer le pourcentage de la barre de progression
const pourcentageProgression = computed(() => {
  const objectif = prochainObjectif.value;
  return Math.min((devoirsAjoutes.value / objectif) * 100, 100);
});

// Badge à afficher
const badgeActuel = computed(() => {
  return `/src/assets/badges/${niveauActuel.value.badge}`;
});

</script>
<template>
  <section class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200">
    <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
      <div class="flex items-center justify-between border border-t-0 border-l-0 border-r-0 border-gray-200">
        <div class="sm:flex sm:items-start flex flex-col items-center">
          <h1 class="text-3xl font-extrabold text-gray-900">Mes succès</h1>
          <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Gérez les succès de votre compte</p>
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center">
          <Button variant="solid" size="small" tag="a" href="/succes">Actualiser</Button>
          <Button variant="outline" size="small" tag="a" href="/">Retour</Button>
        </div>
      </div>
      <div class="mt-8">
        <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 mt-6">
          <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
            <div class="max-w-md">
              <img :src="badgeActuel" alt="Badge Actuel">
            </div>
            <div class="w-full">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Devoirs Ajoutés</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Ajouter {{ prochainObjectif }} devoirs
              </p>
              <div class="flex items-center gap-2 text-xs font-light">
                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                  <div
                      :style="{ width: pourcentageProgression + '%' }"
                      class="h-full bg-green-400 rounded-full"
                  ></div>
                </div>
                <div>
                  <span>{{ devoirsAjoutes }}/{{ prochainObjectif }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 mt-6">
          <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
            <div class="max-w-md">
              <img :src="badgeActuel" alt="Badge Actuel">
            </div>
            <div class="w-full">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Devoirs Ajoutés</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Ajouter {{ prochainObjectif }} devoirs
              </p>
              <div class="flex items-center gap-2 text-xs font-light">
                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                  <div
                      :style="{ width: pourcentageProgression + '%' }"
                      class="h-full bg-green-400 rounded-full"
                  ></div>
                </div>
                <div>
                  <span>{{ devoirsAjoutes }}/{{ prochainObjectif }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>