<script setup>
import Button from "@/components/Button.vue";
import {ref, computed, onMounted, inject} from "vue";
import axios from 'axios';
import {useStore} from "vuex";

const triggerToast = inject('triggerToast');

const API_URL = import.meta.env.VITE_API_BASE_URL;
const store = useStore();
const user = ref(store.state.user);
const token = localStorage.getItem('token');

const devoirsAjoutes = ref(0);
const devoirsTermines = ref(12);
const devoirsAvance = ref(8);
const devoirsVotes = ref(5);

import BadgeAjoute1 from '@/assets/badges/Ajouter_Lvl_1.svg';
import BadgeAjoute2 from '@/assets/badges/Ajouter_Lvl_2.svg';
import BadgeAjoute3 from '@/assets/badges/Ajouter_Lvl_3.svg';
import BadgeTermine1 from '@/assets/badges/Terminer_Lvl_1.svg';
import BadgeTermine2 from '@/assets/badges/Terminer_Lvl_2.svg';
import BadgeTermine3 from '@/assets/badges/Terminer_Lvl_3.svg';
import BadgeAvance1 from '@/assets/badges/Avance_Lvl_1.svg';
import BadgeAvance2 from '@/assets/badges/Avance_Lvl_2.svg';
import BadgeAvance3 from '@/assets/badges/Avance_Lvl_3.svg';
import BadgeVote1 from '@/assets/badges/Voter_Lvl_1.svg';
import BadgeVote2 from '@/assets/badges/Voter_Lvl_2.svg';
import BadgeVote3 from '@/assets/badges/Voter_Lvl_3.svg';


// Les niveaux pour chaque catégorie
const niveauxAjoutes = [
    {niveau: 1, objectif: 0, badge: BadgeAjoute1},
    {niveau: 2, objectif: 6, badge: BadgeAjoute2},
    {niveau: 3, objectif: 15, badge: BadgeAjoute3},
];

const niveauxTermines = [
    {niveau: 1, objectif: 0, badge: BadgeTermine1},
    {niveau: 2, objectif: 5, badge: BadgeTermine2},
    {niveau: 3, objectif: 12, badge: BadgeTermine3},
];

const niveauxAvances = [
    {niveau: 1, objectif: 0, badge: BadgeAvance1},
    {niveau: 2, objectif: 5, badge: BadgeAvance2},
    {niveau: 3, objectif: 10, badge: BadgeAvance3},
];

const niveauxVotes = [
    {niveau: 1, objectif: 0, badge: BadgeVote1},
    {niveau: 2, objectif: 10, badge: BadgeVote2},
    {niveau: 3, objectif: 25, badge: BadgeVote3},
];

// Calculer le niveau actuel pour chaque catégorie
const niveauAjoutes = computed(() => {
  for (let i = niveauxAjoutes.length - 1; i >= 0; i--) {
    if (devoirsAjoutes.value >= niveauxAjoutes[i].objectif) return niveauxAjoutes[i];
  }
  return niveauxAjoutes[0];
});

const niveauTermines = computed(() => {
  for (let i = niveauxTermines.length - 1; i >= 0; i--) {
    if (devoirsTermines.value >= niveauxTermines[i].objectif) return niveauxTermines[i];
  }
  return niveauxTermines[0];
});

const niveauAvances = computed(() => {
  for (let i = niveauxAvances.length - 1; i >= 0; i--) {
    if (devoirsAvance.value >= niveauxAvances[i].objectif) return niveauxAvances[i];
  }
  return niveauxAvances[0];
});

const niveauVotes = computed(() => {
  for (let i = niveauxVotes.length - 1; i >= 0; i--) {
    if (devoirsVotes.value >= niveauxVotes[i].objectif) return niveauxVotes[i];
  }
  return niveauxVotes[0];
});

// Calculer les objectifs des prochains niveaux
const prochainObjectifAjoutes = computed(() => {
  const index = niveauxAjoutes.findIndex(n => n.niveau === niveauAjoutes.value.niveau);
  return index < niveauxAjoutes.length - 1 ? niveauxAjoutes[index + 1].objectif : niveauAjoutes.value.objectif;
});

const prochainObjectifTermines = computed(() => {
  const index = niveauxTermines.findIndex(n => n.niveau === niveauTermines.value.niveau);
  return index < niveauxTermines.length - 1 ? niveauxTermines[index + 1].objectif : niveauTermines.value.objectif;
});

const prochainObjectifAvances = computed(() => {
  const index = niveauxAvances.findIndex(n => n.niveau === niveauAvances.value.niveau);
  return index < niveauxAvances.length - 1 ? niveauxAvances[index + 1].objectif : niveauAvances.value.objectif;
});

const prochainObjectifVotes = computed(() => {
  const index = niveauxVotes.findIndex(n => n.niveau === niveauVotes.value.niveau);
  return index < niveauxVotes.length - 1 ? niveauxVotes[index + 1].objectif : niveauVotes.value.objectif;
});

// Calculer les pourcentages de progression
const pourcentageAjoutes = computed(() => Math.min((devoirsAjoutes.value / prochainObjectifAjoutes.value) * 100, 100));
const pourcentageTermines = computed(() => Math.min((devoirsTermines.value / prochainObjectifTermines.value) * 100, 100));
const pourcentageAvances = computed(() => Math.min((devoirsAvance.value / prochainObjectifAvances.value) * 100, 100));
const pourcentageVotes = computed(() => Math.min((devoirsVotes.value / prochainObjectifVotes.value) * 100, 100));

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
      triggerToast("Erreur", "Une erreur s'est produite lors de la récupération des devoirs ajoutés.", 'error');
    console.error("Erreur lors de la récupération des devoirs ajoutés :", error);
    devoirsAjoutes.value = 0;
  }
};

onMounted(() => {
  if (user.value && user.value.id) {
    fetchDevoirsAjoutes();
  } else {
      triggerToast("Erreur", "Utilisateur non connecté ou ID manquant.", 'error');
    console.error("Utilisateur non connecté ou ID manquant");
  }
});
</script>

<template>
  <section class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200">
    <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="flex-col md:flex-row flex items-center justify-between border border-t-0 border-l-0 border-r-0 border-gray-200">
        <div class="sm:flex sm:items-start flex flex-col items-center">
          <h1 class="text-3xl font-extrabold text-gray-900">Mes succès</h1>
            <p class="mt-1 max-w-2xl text-sm text-gray-500 lg:mb-5">Gérez les succès de votre compte</p>
        </div>
            <div class="mt-4 flex flex-row lg:flex-row gap-4 lg:mt-0 sm:flex-row sm:items-center mb-5 lg:mb-0">
          <Button variant="solid" size="small" tag="a" href="/succes">Actualiser</Button>
          <Button variant="outline" size="small" tag="a" href="/">Retour</Button>
        </div>
      </div>
      <div class="mt-8">
        <!-- Devoirs Ajoutés -->
        <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 mt-6">
          <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
            <div class="max-w-md">
                <img :src="niveauAjoutes.badge" alt="Badge Ajoutés">
            </div>
            <div class="w-full">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Devoirs Ajoutés</h3>
              <div class="flex items-center gap-2 text-xs font-light">
                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                  <div :style="{ width: pourcentageAjoutes + '%' }" class="h-full bg-green-400 rounded-full transition-all duration-300 ease-in-out"></div>
                </div>
                <div><span>{{ devoirsAjoutes }}/{{ prochainObjectifAjoutes }}</span></div>
              </div>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">Ajouter {{ prochainObjectifAjoutes }} devoirs</p>
            </div>
          </div>
        </div>
        <!-- Devoirs Terminés -->
        <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 mt-6">
          <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
            <div class="max-w-md">
                <img :src="niveauTermines.badge" alt="Badge Terminés">
            </div>
            <div class="w-full">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Devoirs Terminés</h3>
              <div class="flex items-center gap-2 text-xs font-light">
                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                  <div :style="{ width: pourcentageTermines + '%' }" class="h-full bg-green-400 rounded-full transition-all duration-300 ease-in-out"></div>
                </div>
                <div><span>{{ devoirsTermines }}/{{ prochainObjectifTermines }}</span></div>
              </div>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">Terminer {{ prochainObjectifTermines }} devoirs</p>
            </div>
          </div>
        </div>
        <!-- Devoirs Avancés -->
        <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 mt-6">
          <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
            <div class="max-w-md">
                <img :src="niveauAvances.badge" alt="Badge Avancés">
            </div>
            <div class="w-full">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Devoirs Avancés</h3>
              <div class="flex items-center gap-2 text-xs font-light">
                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                  <div :style="{ width: pourcentageAvances + '%' }" class="h-full bg-green-400 rounded-full transition-all duration-300 ease-in-out"></div>
                </div>
                <div><span>{{ devoirsAvance }}/{{ prochainObjectifAvances }}</span></div>
              </div>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">Avancer {{ prochainObjectifAvances }} devoirs</p>
            </div>
          </div>
        </div>
        <!-- Devoirs Votés -->
        <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 mt-6">
          <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
            <div class="max-w-md">
                <img :src="niveauVotes.badge" alt="Badge Votés">
            </div>
            <div class="w-full">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Devoirs Votés</h3>
              <div class="flex items-center gap-2 text-xs font-light">
                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                  <div :style="{ width: pourcentageVotes + '%' }" class="h-full bg-green-400 rounded-full transition-all duration-300 ease-in-out"></div>
                </div>
                <div><span>{{ devoirsVotes }}/{{ prochainObjectifVotes }}</span></div>
              </div>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">Voter pour {{ prochainObjectifVotes }} devoirs</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>