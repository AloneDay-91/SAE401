<script setup>
import { ref } from 'vue'
import { useStore } from 'vuex'

const API_URL = import.meta.env.VITE_API_BASE_URL
const store = useStore()
const user = ref(store.state.user)

</script>

<template>
  <div>
    <h1>Paramètres</h1>
    <p>Vous pouvez modifier vos informations personnelles ici.</p>
    <br>
    <div>
      <h2>Informations personnelles</h2>
        <div>
            <img class="h-16 w-16 rounded-lg" :src="`https://api.dicebear.com/9.x/dylan/svg?seed=${user.nom || 'User'}`" alt="Avatar">
        </div>
      <p>Nom: {{ user.nom }}</p>
      <p>Prénom: {{ user.prenom }}</p>
      <p>Email: {{ user.email }}</p>
        <p>Promo: {{ user.id_classes.promo }}</p>
        <p>TP: {{ user.id_classes.tp }}</p>
        <p>TD: {{ user.id_classes.td }}</p>
    </div>
  </div>
</template>