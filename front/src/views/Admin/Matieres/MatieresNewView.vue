<script setup>
import {inject, ref} from 'vue'
import axios from "axios";
import {RouterLink, useRouter} from 'vue-router'
import { useStore } from 'vuex'
import {ArrowLeft, ArrowRight, Info} from "lucide-vue-next";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const nom = ref('')
const code = ref('')
const couleur = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useStore()

const triggerToast = inject('triggerToast');

const AjouterMatieres = async () => {
  loading.value = true;
  error.value = "";

    // Vérifier si les champs sont remplis
    if (!nom.value || !code.value || !couleur.value) {
        triggerToast("Erreur", "Veuillez remplir tous les champs obligatoires.", 'error');
        loading.value = false;
        return;
    }

  const MatiereData = {
    nom: nom.value,
    code: code.value,
    couleur: couleur.value
  };

  try {
    await axios.post(`${API_URL}/matieres`, MatiereData, {  // <== Correction ici
      headers: {
        "Content-Type": "application/ld+json",
        "Authorization": `Bearer ${store.state.token}`
      }
    });
      triggerToast("Matière ajoutée avec succès", "La matière a été ajoutée avec succès.", 'success');
      await router.push("/admin/matieres");
  } catch (err) {
      triggerToast("Erreur lors de l'ajout de la matière", "Une erreur s'est produite lors de l'ajout de la matière.", 'error');
      console.error("Erreur API :", err.response?.data || err);
      error.value = err.response?.data?.detail || "Erreur lors de l'ajout de la matière";
  }

  loading.value = false;
};
</script>
<template>
    <div class="max-w-2xl m-auto w-full">
        <div class="text-left flex items-center justify-between mt-12">
            <div class="w-full flex justify-start flex-col">
                <h1 class="font-semibold">Ajouter une matière</h1>
                <p class="text-gray-500 text-sm mt-2">Remplissez le formulaire ci-dessous pour ajouter une nouvelle
                    matière.</p>
            </div>
            <div class="w-full flex justify-end">
                <Button variant="outline" size="small" tag="a" href="/admin/matieres">Retour</Button>
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
                        <p class="text-gray-500 font-light text-sm">Intitulé d'une matière</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="nom" class="hidden w-32.5">Intitulé : </label>
                            <input type="text" v-model="nom" id="nom" aria-label="intitulé"
                                   class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="my-4 text-gray-200">
                    <hr>
                </div>
                <div class="flex items-center justify-between">
                    <div class="w-full flex justify-start flex-col">
                        <div class="flex items-start justify-items-start">
                            <span class="font-normal text-gray-600">Code</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Code d'une matière</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="code" class="hidden w-32.5">Code : </label>
                            <input type="text" v-model="code" id="code" aria-label="code"
                                   class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="my-4 text-gray-200">
                    <hr>
                </div>
                <div class="flex items-center justify-between">
                    <div class="w-full flex justify-start flex-col">
                        <div class="flex items-start justify-items-start">
                            <span class="font-normal text-gray-600">Couleur</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Couleur d'une matière</p>
                        <span class="text-xs text-gray-500 font-light mt-4 inline-flex items-center"><Info size="14"
                                                                                                           class="mr-1"/> La couleur ajoutée doit être du type : </span>
                        <span class="text-xs text-gray-500 font-light underline">text-blue-500 bg-blue-500/20</span>

                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="couleur" class="hidden w-32.5">Couleur : </label>
                            <input type="text" v-model="couleur" id="couleur" aria-label="couleur"
                                   class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                   required>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-4 flex items-center justify-end w-full">
            <Button variant="solid" size="small" type="submit" :disabled="loading" @click.prevent="AjouterMatieres">
                {{ loading ? 'Ajout en cours...' : 'Ajouter la matière' }}
            </Button>
        </div>
    </div>

    <div class="flex items-center w-full justify-between gap-8 mt-24 px-4">
    <router-link to="/admin/matieres" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
      <div>
        <div class="flex items-center gap-4 justify-start">
          <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
          <div class="text-left">
            <p class="text-gray-800 font-normal text-sm">Liste des matières</p>
            <div class="text-xs text-gray-500 font-light">Gérer les différentes matières</div>
          </div>
        </div>
      </div>
    </router-link>
    <router-link to="/admin/categories" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
      <div>
        <div class="flex items-center gap-4 justify-end">
          <div class="text-right">
            <p class="text-gray-800 font-normal text-sm">Liste des catégories</p>
            <div class="text-xs text-gray-500 font-light">Gérer les différentes catégories</div>
          </div>
          <ArrowRight stroke-width="1.5" size="24" class="mr-2" />
        </div>
      </div>
    </router-link>
  </div>
</template>
