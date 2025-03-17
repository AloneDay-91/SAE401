<script setup>
import { ref, inject } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import Button from "@/components/Button.vue";

const store = useStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const errorIcon = ref('')

const triggerToast = inject('triggerToast');

const handleLogin = async () => {
    loading.value = true
    error.value = ''
    errorIcon.value = ''

    try {
        await store.dispatch('login', { email: email.value, password: password.value })
        router.push('/')
        triggerToast("Connexion réussie","Vous êtes maintenant connecté.", 'success');
    } catch (e) {
        if (e.response && e.response.data && e.response.data.message) {
            error.value = e.response.data.message
        } else {
            error.value = "L'email ou le mot de passe est incorrect."
            errorIcon.value = `
        <svg class="w-4 h-4" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" color="currentColor" fill="none">
          <path d="M12 11.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M12 7.51L12.01 7.49889" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
      `
            triggerToast("Connexion échouée","L'email ou le mot de passe est incorrect.", 'error', 'topRight', 5000);
        }
    }

    loading.value = false
}
</script>

<template>
    <p v-if="error" class="error-message border rounded p-3 text-sm font-light border-red-400/20 bg-red-200/10 text-red-900 flex items-center gap-2 mx-12 mt-4 max-w-lg mx-auto">
        <span v-html="errorIcon"></span> {{ error }}
    </p>
    <br>
    <form @submit.prevent="handleLogin" class="px-12 max-w-xl mx-auto">
        <div class="text-center">
            <h1 class="text-2xl font-bold sm:text-3xl">Commencer aujourd'hui !</h1>
            <p class="mt-4 text-gray-500">
                Connectez-vous pour accéder à votre compte utilisateur et profiter de nos services.
            </p>
        </div>
        <div class="form-group flex flex-col">
            <label for="email" class="text-sm font-light">Email</label>
            <input type="email" id="email" v-model="email" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer un email" required />
        </div>
        <br>
        <div class="form-group flex flex-col">
            <div class="flex items-center justify-between">
                <label for="password" class="text-sm font-light">Password</label>
                <router-link to="/mot-de-passe-oublie" class="text-sm font-light text-green-700/70">Mot de passe oublié ?</router-link>
            </div>
            <input type="password" id="password" v-model="password" class="border rounded border-gray-300 text-sm p-2 font-light shadow-xs" placeholder="Entrer un mot de passe" required />
        </div>
        <div class="flex items-center justify-between gap-4 mt-6 w-full">
            <div class="inline-flex items-center gap-1">
                <span class="text-gray-600 font-light text-xs">Pas de compte ?</span>
                <Button class="hover:border-b border-b-green-500 !rounded-none !py-0 !px-0" variant="ghost" size="small" tag="a" href="/inscription">Créer un compte</Button>
            </div>
            <Button type="submit" class="inline-flex" variant="solid" size="small">
                <span v-if="loading" class="mr-2">
                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 0A7.962 7.962 0 014 4.038V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
                {{ loading ? "Connexion..." : "Connexion" }}
            </Button>
        </div>
    </form>
</template>

<style scoped></style>
