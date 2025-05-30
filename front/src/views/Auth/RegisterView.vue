<script setup>
import {onMounted, ref} from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import axios from "axios";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore()
const router = useRouter()

const nom = ref('')
const prnm = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const loading = ref(false)
const error = ref('')
const errorIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c-2.404 0-4.635-.94-6.364-2.636-1.73-1.696-2.69-3.946-2.69-6.364 0-2.418.96-4.668 2.69-6.364C16.025 5.94 13.794 5 11.39 5H4.436"></path></svg>';
const tp = ref('')
const td = ref('')
const promo = ref('')


import { inject } from 'vue';
const triggerToast = inject('triggerToast');

const register = async () => {
    loading.value = true;
    error.value = "";

    if (password.value !== confirmPassword.value) {
        error.value = "Les mots de passe ne correspondent pas";
        loading.value = false;
        triggerToast("Attention","Les mots de passe ne correspondent pas", 'warning');
        return;
    }

    password.value = await hash(password.value);

    const userData = {
        email: email.value,
        password: password.value,
        nom: nom.value,
        prenom: prnm.value,
        roleapp: "ROLE_ELEVE",
        td: td.value,
        tp: tp.value,
        promo: promo.value
    };

    console.log(userData);

    try {
        await store.dispatch("register", userData);
        triggerToast("Inscription réussie","Veuillez vous connecter.", 'success');
        router.push("/connexion");
    } catch (err) {
        console.error("Erreur API :", err.response?.data || err);
        error.value = err.response?.data?.detail || "Erreur lors de l'inscription";
        triggerToast("Une erreur est survenue",error.value, 'error');
    }

    loading.value = false;
};


const hash = async (password) => {
  const bcrypt = await import('bcryptjs');
  const salt = await bcrypt.genSalt(10);
  return await bcrypt.hash(password, salt);
};

</script>

<template>
    <p v-if="error" class="error-message border rounded p-3 text-sm font-light border-red-400/20 bg-red-200/10 text-red-900 flex items-center gap-2 mx-12 mt-4">
        <span v-html="errorIcon"></span> {{ error }}
    </p>
    <br>
    <form @submit.prevent="register" class="px-12 max-w-xl mx-auto">
        <h1 class="text-xl font-light mb-6">Création d'un compte</h1>
        <div class="form-group flex flex-col">
            <label for="nom" class="text-sm font-light">Nom</label>
            <input type="text" id="nom" v-model="nom" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer votre nom" required />
        </div>
        <br>
        <div class="form-group flex flex-col">
            <label for="prnm" class="text-sm font-light">Prénom</label>
            <input type="text" id="prnm" v-model="prnm" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer votre prénom" required />
        </div>
        <br>
        <div class="form-group flex flex-col">
            <label for="email" class="text-sm font-light">Email</label>
            <input type="email" id="email" v-model="email" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer votre email" required />
        </div>
        <br>
        <div class="form-group flex flex-col">
            <label for="password" class="text-sm font-light">Mot de passe</label>
            <input type="password" id="password" v-model="password" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer un mot de passe" required />
        </div>
        <br>
        <div class="form-group flex flex-col">
            <label for="confirmPassword" class="text-sm font-light">Confirmer le mot de passe</label>
            <input type="password" id="confirmPassword" v-model="confirmPassword" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Confirmer votre mot de passe" required />
        </div>
        <br>
        <div class="form-group flex flex-col">
            <label for="tp" class="text-sm font-light">TP</label>
            <input type="text" id="tp" max="3" v-model="tp" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer votre TP" required />
        </div>
        <br>
        <div class="form-group flex flex-col">
            <label for="td" class="text-sm font-light">TD</label>
            <input type="text" id="td" max="4" v-model="td" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer votre TD" required />
        </div>
        <br>
        <div class="form-group flex flex-col">
            <label for="promo" class="text-sm font-light">Promo</label>
            <select id="promo" v-model="promo" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" required>
                <option selected>Choisir un promo</option>
                <option value="S1/S2">S1/S2</option>
                <option value="S3/S4">S3/S4</option>
                <option value="S5/S6">S5/S6</option>
            </select>
        </div>
        <br>
        <div class="flex items-center justify-between gap-4 mt-6 w-full">
            <div class="inline-flex items-center gap-1">
                <span class="text-gray-600 font-light text-xs">Vous avez déjà un compte ?</span>
                <Button class="hover:border-b border-b-green-500 !rounded-none !py-0 !px-0" variant="ghost" size="small" tag="a" href="/connexion">Se connecter</Button>
            </div>
            <Button type="submit" class="inline-flex" variant="solid" size="small">
                <span v-if="loading" class="mr-2">
                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 0A7.962 7.962 0 014 4.038V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
                {{ loading ? "Inscription..." : "S'inscrire"}}
            </Button>
        </div>
    </form>
</template>


<style scoped>

</style>