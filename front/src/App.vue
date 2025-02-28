<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { computed, onMounted } from 'vue'

const store = useStore()
const router = useRouter()

// Vérifie si l'utilisateur est connecté
const isAuthenticated = computed(() => !!store.state.token)

// Récupérer l'utilisateur depuis Vuex
const user = computed(() => store.state.user)

// Charger l'utilisateur si connecté
onMounted(async () => {
  if (isAuthenticated.value && user.value === null) {
    try {
      await store.dispatch('fetchUser') // Appelle l'action pour récupérer l'utilisateur
    } catch (error) {
      console.error("Erreur lors du chargement de l'utilisateur:", error)
    }
  }
})

// Fonction pour se déconnecter
const handleLogout = () => {
  store.dispatch('logout')
  router.push('/connexion') // Redirection après déconnexion
}
</script>

<template>
  <header>
    <img alt="Vue logo" class="logo" src="@/assets/logo.svg" width="125" height="125" />

    <div class="wrapper">
      <nav>
        <div v-if="isAuthenticated" class="flex items-center">
          <RouterLink to="/">Home</RouterLink>
          <span v-if="user" class="flex items-center">Bonjour, <RouterLink to="/settings">{{ user.nom }} {{ user.prenom }}</RouterLink></span>
          <button @click="handleLogout">Déconnexion</button>
        </div>

        <div v-else>
          <RouterLink to="/connexion">Connexion</RouterLink>
          <RouterLink to="/inscription">Inscription</RouterLink>
        </div>
      </nav>
    </div>
  </header>

  <RouterView />
</template>

<style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
  margin-top: 2rem;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }

  nav {
    text-align: left;
    margin-left: -1rem;
    font-size: 1rem;
    padding: 1rem 0;
    margin-top: 1rem;
  }
}
</style>