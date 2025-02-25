<script setup>
import { ref } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

const store = useStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    await store.dispatch('login', { email: email.value, password: password.value })
    router.push('/')
  } catch (e) {
    if (e.response && e.response.data && e.response.data.message) {
      error.value = e.response.data.message
    } else {
      error.value = "Une erreur est survenue lors de la connexion."
      console.error(e) // Affiche l'erreur complète dans la console pour le debugging
    }
  }

  loading.value = false
}


</script>

<template>
  <div class="login-form">
    <form @submit.prevent="handleLogin">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" v-model="email" required />
      </div>
      <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" v-model="password" required />
      </div>
      <button type="submit" class="btn-submit" :disabled="loading">
        {{ loading ? "Connexion..." : "Se connecter" }}
      </button>
      <p v-if="error" class="error-message">{{ error }}</p>
    </form>

    <!-- Lien vers la page de réinitialisation -->
    <p class="forgot-password">
      <router-link to="/mot-de-passe-oublie">Mot de passe oublié ?</router-link>
    </p>
  </div>
</template>

<style scoped>
.forgot-password {
  text-align: center;
  margin-top: 1rem;
}
</style>
