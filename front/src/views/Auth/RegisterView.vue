<script setup>
import {onMounted, ref} from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import axios from "axios";

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
const classe = ref('')
const classes = ref([])

const register = async () => {
    loading.value = true;
    error.value = "";

    if (!classe.value) {
        error.value = "Veuillez sélectionner une classe";
        loading.value = false;
        return;
    }

    if (password.value !== confirmPassword.value) {
        error.value = "Les mots de passe ne correspondent pas";
        loading.value = false;
        return;
    }

    password.value = await hash(password.value);

    const userData = {
        email: email.value,
        password: password.value,
        nom: nom.value,
        prenom: prnm.value,
        id_classes: `/api/classes/${classe.value}`,
        roleapp: "ROLE_ELEVE"
    };

    try {
        await store.dispatch("register", userData);
        router.push("/connexion");
    } catch (err) {
        console.error("Erreur API :", err.response?.data || err);
        error.value = err.response?.data?.detail || "Erreur lors de l'inscription";
    }

    loading.value = false;
};


const hash = async (password) => {
  const bcrypt = await import('bcryptjs');
  const salt = await bcrypt.genSalt(10);
  return await bcrypt.hash(password, salt);
};

// fonction pour recuperer la liste des classes
onMounted(async () => {
    try {
        const response = await axios.get(`${API_URL}/classes`);
        classes.value = response.data.member;
    } catch (err) {
        console.error("Erreur lors de la récupération des classes:", err);
    }
});


</script>

<template>
    <p v-if="error" class="error-message border rounded p-3 text-sm font-light border-red-400/20 bg-red-200/10 text-red-900 flex items-center gap-2 mx-12 mt-4">
        <span v-html="errorIcon"></span> {{ error }}
    </p>
    <br>
    <form @submit.prevent="register" class="px-12">
        <h2 class="text-left text-2xl font-semibold mb-4">Inscription</h2>
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
            <label for="classe" class="text-sm font-light">Classe</label>
            <select id="classe" v-model="classe" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" required>
                <option value="">Choisir une classe</option>
                <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                    {{ cls.intitule }} ({{ cls.tp }})
                </option>
            </select>
        </div>
        <br>
        <div class="flex items-center justify-end gap-4 mt-6 w-full">
            <a href="/connexion" class="btn-submit bg-green-600/10 px-2 py-1.5 rounded text-green-700 text-xs">Se connecter</a>
            <button
                type="submit"
                :disabled="loading"
                class="
                            inline-flex items-center px-2 py-1.5 border border-transparent rounded shadow-sm text-xs font-normal
                             bg-[#00D478] text-[#004319] border-[#00D478]
                            disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer
                        "
            >
                <!-- Spinner -->
                <svg v-if="loading" class="-ml-[6px] mr-[6px] h-[14px] w-[14px] animate-spin text-[#004319]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="#fff" stroke-width="4"></circle>
                    <path class="opacity-75" fill="#fff" d="
                                M4 12a8 8 0 018-8V0C5.373
                                0 0 5.373
                                0 h4zm2
                                A7.962z"></path>
                </svg>
                {{ loading ? "Inscription..." : "S'inscrire"}}
            </button>
        </div>
    </form>
</template>


<style scoped>

</style>