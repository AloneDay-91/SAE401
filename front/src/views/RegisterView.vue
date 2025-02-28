<script setup>
import { ref } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

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

const register = async () => {
    loading.value = true;
    error.value = "";

    if (password.value !== confirmPassword.value) {
        error.value = "Les mots de passe ne correspondent pas";
        loading.value = false;
        return;
    }

    password.value = await hash(password.value);

    const userData = {
        email: email.value,
        roles: ["ROLE_USER"],
        password: password.value,
        nom: nom.value,
        prenom: prnm.value,
    };

    try {
        await store.dispatch("register", userData);
        // Si l'inscription réussit, redirigez l'utilisateur ou affichez un message
        router.push("/login"); // ou toute autre route appropriée
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

</script>

<template>
  <div class="register-form">
    <form @submit.prevent="register">
      <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" v-model="nom" required />
      </div><div class="form-group">
      <label for="prnm">Prénom</label>
      <input type="text" id="prnm" v-model="prnm" required />
    </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" v-model="email" required />
      </div>
      <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" v-model="password" required />
      </div>
      <div class="form-group">
        <label for="confirmPassword">Confirmer le mot de passe</label>
        <input type="password" id="confirmPassword" v-model="confirmPassword" required />
      </div>
      <button type="submit" class="btn-submit" :disabled="loading">
        {{ loading ? "Inscription..." : "S'inscrire" }}
      </button>
      <p v-if="error" class="error-message">{{ error }}</p>
    </form>
  </div>
</template>

<style scoped>

</style>