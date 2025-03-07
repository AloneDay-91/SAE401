<script setup>
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const successMessage = ref('')
const errorMessage = ref('')
const loading = ref(false)

const baseUrl = 'http://badge.elouanb.fr:8319/api'

const handlePasswordReset = async () => {
  successMessage.value = ''
  errorMessage.value = ''
  loading.value = true

  try {
    const response = await axios.post('http://badge.elouanb.fr:8319/api/password-reset',
        {email: email.value},
        {
          headers: {
            'Content-Type': 'application/json',
          },
        }
    )

    successMessage.value = 'Un e-mail de réinitialisation a été envoyé si l\'adresse est correcte.'
  } catch (error) {
    console.error('Erreur lors de la demande de réinitialisation:', error)
    errorMessage.value = error.response?.data?.message || 'Une erreur est survenue.'
  }

  loading.value = false
}
</script>

<template>
  <div class="password-reset-form">
    <form @submit.prevent="handlePasswordReset">
      <label for="email">Entrez votre e-mail :</label>
      <input type="email" id="email" v-model="email" required/>
      <button type="submit" :disabled="loading">
        {{ loading ? "Envoi en cours..." : "Réinitialiser le mot de passe" }}
      </button>
    </form>
    <p v-if="successMessage" class="success">{{ successMessage }}</p>
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
  </div>
</template>

<style scoped>
.success {
  color: green;
}

.error {
  color: red;
}
</style>