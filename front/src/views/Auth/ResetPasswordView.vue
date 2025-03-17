<script setup>
import {ref, inject} from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import Button from "@/components/Button.vue";

const route = useRoute();
const router = useRouter();
const token = ref(route.query.token);
const newPassword = ref('');
const confirmPassword = ref('');
const error = ref('');
const successMessage = ref('');
const loading = ref(false);

const API_URL = import.meta.env.VITE_API_BASE_URL;

const triggerToast = inject('triggerToast');

const resetPassword = async () => {
  if (newPassword.value !== confirmPassword.value) {
    error.value = "Les mots de passe ne correspondent pas.";
    return;
  }

  loading.value = true;
  error.value = '';
  successMessage.value = '';

  try {
    const response = await axios.post(`${API_URL}/password-reset/confirm`, {
      token: token.value,
      newPassword: newPassword.value,
    });

    successMessage.value = response.data.message;
    setTimeout(() => {
      router.push('/connexion'); // Redirige vers la connexion après succès
    triggerToast("Mot de passe réinitialisé","Veuillez vous connecter.", 'success');
    }, 2000);
  } catch (err) {
    error.value = err.response?.data?.message || "Une erreur est survenue.";
    triggerToast("Une erreur est survenue",error.value, 'error');
  }

  loading.value = false;
};
</script>

<template>
    <div class="max-w-lg m-auto mt-12">
        <h1 class="text-xl font-light mb-6">Réinitialisation du mot de passe</h1>
        <form @submit.prevent="resetPassword">
            <div class="flex flex-col">
                <label for="newPassword" class="text-sm font-light">Nouveau mot de passe</label>
                <input class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer un nouveau mot de passe" type="password" id="newPassword" v-model="newPassword" required />
            </div>
            <br>
            <div class="flex flex-col">
                <label for="confirmPassword" class="text-sm font-light">Confirmer nouveau mot de passe</label>
                <input class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Confirmer le nouveau mot de passe" type="password" id="confirmPassword" v-model="confirmPassword" required />
            </div>
            <br>
            <div class="flex items-center justify-between gap-2">
                <Button tag="a" href="/connexion" variant="outline" size="small">Retour</Button>
                <Button type="submit" variant="solid" size="small">{{ loading ? "Modification..." : "Réinitialiser le mot de passe" }}</Button>
            </div>
        </form>
    </div>
</template>

<style scoped>
</style>
