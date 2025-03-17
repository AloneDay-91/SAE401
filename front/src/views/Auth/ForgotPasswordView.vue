<script setup>
import { ref, inject } from 'vue'
import axios from 'axios'
import Button from "@/components/Button.vue";

const email = ref('')
const loading = ref(false)


const API_URL = import.meta.env.VITE_API_BASE_URL

const triggerToast = inject('triggerToast');

const handlePasswordReset = async () => {
    loading.value = true

    try {
        const response = await axios.post(`${API_URL}/password-reset`,
        {email: email.value},
        {
          headers: {
            'Content-Type': 'application/json',
          },
        }
    )
        triggerToast("E-mail envoyé","Un e-mail de réinitialisation a été envoyé si l'adresse est correcte.", 'success', 'topRight', 10000);
    } catch (error) {
        triggerToast("E-mail envoyé","Un e-mail de réinitialisation a été envoyé si l'adresse est correcte.", 'success', 'topRight', 10000);
    }

    loading.value = false
}
</script>

<template>
  <div class="max-w-lg m-auto mt-12">
    <h1 class="text-xl font-light mb-6">Réinitialisation du mot de passe</h1>
    <form @submit.prevent="handlePasswordReset">
        <div class="flex flex-col">
            <label for="email" class="text-sm font-light">Entrez votre e-mail :</label>
            <input class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer un email" type="email" id="email" v-model="email" required/>
        </div>
        <br>
        <div class="flex items-center justify-between gap-2">
            <Button tag="a" href="/connexion" variant="outline" size="small">Retour</Button>
            <Button type="submit" variant="solid" size="small">{{ loading ? "Envoi en cours..." : "Réinitialiser le mot de passe" }}</Button>
        </div>
    </form>
  </div>
</template>

<style scoped>
</style>