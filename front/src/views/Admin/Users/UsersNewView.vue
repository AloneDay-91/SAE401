<script setup>
import { ref } from 'vue'
import axios from "axios";
import {RouterLink, useRouter} from 'vue-router'
import { useStore } from 'vuex'
import {ArrowLeft, ArrowRight} from "lucide-vue-next";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const nom = ref('')
const prenom = ref('')
const email = ref('')
const roleapp = ref('')
const promo = ref('')
const td = ref('')
const tp = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useStore()

const AjouterUsers = async () => {
  loading.value = true;
  error.value = "";

  const userData = {
    nom: nom.value,
    prenom: prenom.value,
    email: email.value,
    roleapp: roleapp.value,
    promo: promo.value,
    td: td.value,
    tp: tp.value,
    password: password.value
  };

  try {
    await axios.post(`${API_URL}/users`, userData, {  // <== Correction ici
      headers: {
        "Content-Type": "application/ld+json",
        "Authorization": `Bearer ${store.state.token}`
      }
    });
    console.log("Utilisateur ajouté avec succès");
    router.push("/admin/users");
  } catch (err) {
    console.error("Erreur API :", err.response?.data || err);
    error.value = err.response?.data?.detail || "Erreur lors de l'ajout de l' utilisateur";
  }

  loading.value = false;
};
</script>
<template>
  <form @submit.prevent="AjouterUsers" class="py-6 px-12 flex-column"> <!-- Ajout de @submit.prevent -->
    <div class="flex gap-1 items-center mb-3">
      <label for="nom" class="w-27.5">Nom : </label>
      <input v-model="nom" type="text" id="nom" name="nom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="prenom" class="w-27.5">Prenom : </label>
      <input v-model="prenom" type="text" id="prenom" name="prenom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="email" class="w-27.5">Email : </label>
      <input v-model="email" type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="roleapp" class="w-27.5">Role : </label>
      <select v-model="roleapp" id="roleapp" name="roleapp" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
        <option value="ROLE_PROFESSEUR">Professeur</option>
        <option value="ROLE_ELEVE">Etudiant</option>
        <option value="ROLE_ADMIN">Administrateur</option>
      </select>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="promo" class="w-27.5">Promo : </label>
      <select v-model="promo" id="promo" name="promo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
        <option value="S1/S2">S1/S2</option>
        <option value="S3/S4">S3/S4</option>
        <option value="S5/S6">S5/S6</option>
      </select>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="td" class="w-27.5">TD : </label>
      <select v-model="td" id="td" name="td" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
        <option value="TDAB">TDAB</option>
        <option value="TDCD">TDCD</option>
        <option value="TDEF">TDEF</option>
        <option value="TDGH">TDGH</option>
      </select>
    </div>
    <div class="flex gap-1 items-center mb-3">
    <label for="tp" class="w-27.5">TP : </label>
      <select v-model="tp" id="tp" name="tp" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required>
        <option value="TPA">TPA</option>
        <option value="TPB">TPB</option>
        <option value="TPC">TPC</option>
        <option value="TPD">TPD</option>
        <option value="TPE">TPE</option>
        <option value="TPF">TPF</option>
        <option value="TPG">TPG</option>
        <option value="TPH">TPH</option>
      </select>
    </div>
    <div class="flex gap-1 items-center mb-3">
      <label for="password" class="w-27.5">Mot de passe : </label>
      <input v-model="password" type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2 py-1.5 w-50" required/>
    </div>
    <Button variant="solid" size="small" type="submit" :disabled="loading">
      {{ loading ? 'Ajout en cours...' : 'Ajouter l\'utilisateur' }}
    </Button>
    <p v-if="error" style="color: red;">{{ error }}</p> <!-- Affichage des erreurs -->
  </form>


  <div class="flex items-center w-full justify-between gap-8 mt-24">
    <router-link to="/admin/users" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
      <div>
        <div class="flex items-center gap-4 justify-start">
          <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
          <div class="text-left">
            <p class="text-gray-800 font-normal text-sm">Liste des utilisateurs</p>
            <div class="text-xs text-gray-500 font-light">Gérer les différents utilisateurs</div>
          </div>
        </div>
      </div>
    </router-link>
    <router-link to="/admin/devoirs" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
      <div>
        <div class="flex items-center gap-4 justify-end">
          <div class="text-right">
            <p class="text-gray-800 font-normal text-sm">Liste des devoirs</p>
            <div class="text-xs text-gray-500 font-light">Gérer les différentes devoirs</div>
          </div>
          <ArrowRight stroke-width="1.5" size="24" class="mr-2" />
        </div>
      </div>
    </router-link>
  </div>
</template>
