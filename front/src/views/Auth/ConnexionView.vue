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
const errorIcon = ref('')

const handleLogin = async () => {
    loading.value = true
    error.value = ''
    errorIcon.value = ''

    try {
        await store.dispatch('login', { email: email.value, password: password.value })
        router.push('/')
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
        }
    }

    loading.value = false
}
</script>

<template>
    <div class="w-full max-w-lg m-auto border border-gray-200 shadow-xs rounded-xl p-12 px-8">
        <div class="login-form m-auto w-full">
            <p v-if="error" class="error-message border rounded p-3 text-sm font-light border-red-400/20 bg-red-200/10 text-red-900 flex items-center gap-2">
                <span v-html="errorIcon"></span> {{ error }}
            </p>
            <br>
            <form @submit.prevent="handleLogin">
                <h2 class="text-left text-2xl font-semibold mb-4">Connexion</h2>
                <div class="form-group flex flex-col">
                    <label for="email" class="text-sm font-light">Email</label>
                    <input type="email" id="email" v-model="email" class="border rounded border-gray-300 text-sm px-2 py-1 font-light shadow-xs" placeholder="Entrer un email" required />
                </div>
                <br>
                <div class="form-group flex flex-col">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-sm font-light">Password</label>
                        <router-link to="/mot-de-passe-oublie" class="text-sm font-light text-green-700/70">Mot de passe oublié ?</router-link>
                    </div>
                    <input type="password" id="password" v-model="password" class="border rounded border-gray-300 text-sm px-2 py-1 font-light shadow-xs" placeholder="Entrer un mot de passe" required />
                </div>
                <div class="flex items-center justify-end gap-4 mt-6 w-full">
                    <a href="/inscription" class="btn-submit bg-green-600/10 px-2 py-1.5 rounded text-green-700 text-sm">Créer un compte</a>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="
                            inline-flex items-center px-3 py-[6px] border border-transparent rounded-md shadow-sm text-xs font-medium
                            text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500
                            disabled:opacity-50 disabled:cursor-not-allowed
                        "
                    >
                        <!-- Spinner -->
                        <svg v-if="loading" class="-ml-[6px] mr-[6px] h-[14px] w-[14px] animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="#fff" stroke-width="4"></circle>
                            <path class="opacity-75" fill="#fff" d="
                                M4 12a8 8 0 018-8V0C5.373
                                0 0 5.373
                                0 h4zm2
                                A7.962z"></path>
                        </svg>
                        {{ loading ? "Connexion..." : "Connexion"}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
</style>
