<script setup>
import { computed, onMounted } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

// Récupérer l'utilisateur depuis Vuex
const user = computed(() => store.state.user)

// Charger l'utilisateur si connecté
onMounted(async () => {
  if (user.value === null) {
    try {
      await store.dispatch('fetchUser') // Appelle l'action pour récupérer l'utilisateur
    } catch (error) {
      console.error("Erreur lors du chargement de l'utilisateur:", error)
    }
  }
})

store.dispatch('fetchUser')

</script>

<template>
  <div>
    <h1>Paramètres</h1>
    <p>Vous pouvez modifier vos informations personnelles ici.</p>
    <br>
    <div v-if="user">
      <h2>Informations personnelles</h2>
      <p>Nom: {{ user.nom }}</p>
      <p>Prénom: {{ user.prenom }}</p>
      <p>Email: {{ user.email }}</p>
    </div>
  </div>
</template>