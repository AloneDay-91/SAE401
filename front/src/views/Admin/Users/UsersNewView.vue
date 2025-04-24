<script setup>
import {ref, inject} from 'vue'
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

const hash = async (password) => {
    const bcrypt = await import('bcryptjs');
    const salt = await bcrypt.genSalt(10);
    return await bcrypt.hash(password, salt);
};

const triggerToast = inject('triggerToast');

const AjouterUsers = async () => {
  loading.value = true;
  error.value = "";

    // verifier si tous les champs sont remplis
    if (!nom.value || !prenom.value || !email.value || !roleapp.value || !promo.value || !td.value || !tp.value || !password.value) {
        triggerToast("Erreur", "Veuillez remplir tous les champs obligatoires.", 'error');
        loading.value = false;
        return;
    }

    password.value = await hash(password.value);

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
      triggerToast("Utilisateur ajouté avec succès", "L'utilisateur a été ajouté avec succès.", 'success');
      await router.push("/admin/users");
  } catch (err) {
      triggerToast("Erreur lors de l'ajout de l'utilisateur", "Une erreur s'est produite lors de l'ajout de l'utilisateur.", 'error');
    console.error("Erreur API :", err.response?.data || err);
    error.value = err.response?.data?.detail || "Erreur lors de l'ajout de l' utilisateur";
  }

  loading.value = false;
};
</script>
<template>
    <div class="max-w-2xl m-auto w-full">
        <div class="text-left flex items-center justify-between mt-12">
            <div class="w-full flex justify-start flex-col">
                <h1 class="font-semibold">Ajouter un utilisateur</h1>
                <p class="text-gray-500 text-sm mt-2">Remplissez le formulaire ci-dessous pour ajouter une nouvelle
                    catégorie.</p>
            </div>
            <div class="w-full flex justify-end">
                <Button variant="outline" size="small" tag="a" href="/admin/users">Retour</Button>
            </div>
        </div>
        <div class="border border-gray-200 rounded-lg bg-gray-100/50 mt-4">
            <form class="py-6 px-12 flex-colum">
                <div class="flex items-center justify-between">
                    <div class="w-full flex justify-start flex-col">
                        <div class="flex items-start justify-items-start">
                            <span class="font-normal text-gray-600">Nom</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Nom d'un utilisateur</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="nom" class="hidden w-32.5">Nom : </label>
                            <input type="text" v-model="nom" id="nom"
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
                            <span class="font-normal text-gray-600">Prénom</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Prénom d'un utilisateur</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="prenom" class="hidden w-32.5">Prénom : </label>
                            <input type="text" v-model="prenom" id="prenom"
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
                            <span class="font-normal text-gray-600">Email</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Email d'un utilisateur</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="email" class="hidden w-32.5">Email : </label>
                            <input type="email" v-model="email" id="email"
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
                            <span class="font-normal text-gray-600">Rôle</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Rôle d'un utilisateur</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="role" class="hidden w-32.5">Rôle : </label>
                            <select v-model="roleapp" id="roleapp"
                                    class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                    required>
                                <option value="" selected>Sélectionner un rôle</option>
                                <option value="ROLE_PROFESSEUR">Professeur</option>
                                <option value="ROLE_ELEVE">Etudiant</option>
                                <option value="ROLE_ADMIN">Administrateur</option>
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
                            <span class="font-normal text-gray-600">Promotion</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Promotion d'un utilisateur</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="promo" class="hidden w-32.5">Promotion : </label>
                            <select v-model="promo" id="promo"
                                    class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                    required>
                                <option value="" selected>Sélectionner une promo</option>
                                <option value="S1/S2">S1/S2</option>
                                <option value="S3/S4">S3/S4</option>
                                <option value="S5/S6">S5/S6</option>
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
                            <span class="font-normal text-gray-600">TD</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">TD d'un utilisateur</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="td" class="hidden w-32.5">TD : </label>
                            <select v-model="td" id="td"
                                    class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                    required>
                                <option value="" selected>Sélectionner un TD</option>
                                <option value="TDAB">TDAB</option>
                                <option value="TDCD">TDCD</option>
                                <option value="TDEF">TDEF</option>
                                <option value="TDGH">TDGH</option>
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
                            <span class="font-normal text-gray-600">TP</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">TP d'un utilisateur</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="tp" class="hidden w-32.5">TP : </label>
                            <select v-model="tp" id="tp"
                                    class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                    required>
                                <option value="" selected>Sélectionner un TP</option>
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
                    </div>
                </div>
                <div class="my-4 text-gray-200">
                    <hr>
                </div>
                <div class="flex items-center justify-between">
                    <div class="w-full flex justify-start flex-col">
                        <div class="flex items-start justify-items-start">
                            <span class="font-normal text-gray-600">Mot de passe</span>
                            <span class="font-normal text-red-500 ml-1">*</span>
                        </div>
                        <p class="text-gray-500 font-light text-sm">Mot de passe d'un utilisateur</p>
                    </div>
                    <div>
                        <div class="flex gap-1 items-center mb-3">
                            <label for="password" class="hidden w-32.5">Mot de passe : </label>
                            <input type="password" v-model="password" id="password"
                                   class="bg-white border border-gray-300 text-gray-500 font-light text-sm rounded-lg block p-2 py-1.5 w-50"
                                   required>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-4 flex items-center justify-end w-full">
            <Button variant="solid" size="small" type="submit" :disabled="loading" @click.prevent="AjouterUsers">
                {{ loading ? 'Ajout en cours...' : 'Ajouter un utilisateur' }}
            </Button>
        </div>
    </div>

    <div class="flex items-center w-full justify-between gap-8 mt-24 px-4">
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
