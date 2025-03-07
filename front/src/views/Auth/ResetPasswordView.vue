<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const token = ref(route.query.token);
const newPassword = ref('');
const confirmPassword = ref('');
const error = ref('');
const successMessage = ref('');
const loading = ref(false);

const resetPassword = async () => {
  if (newPassword.value !== confirmPassword.value) {
    error.value = "Les mots de passe ne correspondent pas.";
    return;
  }

  loading.value = true;
  error.value = '';
  successMessage.value = '';

  try {
    const response = await axios.post('http://badge.elouanb.fr:8319/api/password-reset/confirm', {
      token: token.value,
      newPassword: newPassword.value,
    });

    successMessage.value = response.data.message;
    setTimeout(() => {
      router.push('/login'); // Redirige vers la connexion après succès
    }, 2000);
  } catch (err) {
    error.value = err.response?.data?.message || "Une erreur est survenue.";
  }

  loading.value = false;
};
</script>

<template>
  <div class="reset-password-form">
    <h2>Réinitialisation du mot de passe</h2>
    <p v-if="error" class="error-message">{{ error }}</p>
    <p v-if="successMessage" class="success-message">{{ successMessage }}</p>

    <form @submit.prevent="resetPassword">
      <div class="form-group">
        <label for="newPassword">Nouveau mot de passe</label>
        <input type="password" id="newPassword" v-model="newPassword" required />
      </div>

      <div class="form-group">
        <label for="confirmPassword">Confirmez le mot de passe</label>
        <input type="password" id="confirmPassword" v-model="confirmPassword" required />
      </div>

      <button type="submit" class="btn-submit" :disabled="loading">
        {{ loading ? "Modification..." : "Réinitialiser le mot de passe" }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.reset-password-form {
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 15px;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
}

.btn-submit {
  width: 100%;
  padding: 10px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.btn-submit:disabled {
  background-color: #aaa;
}

.error-message {
  color: red;
}

.success-message {
  color: green;
}
</style>
