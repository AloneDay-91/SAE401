<script setup>
import { ref } from 'vue'
import { useStore } from 'vuex'
import {RouterLink, useRoute} from "vue-router";

const store = useStore()
const user = ref(store.state.user)
const route = useRoute()

</script>

<template>
    <section class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200">
        <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="sm:flex sm:items-start flex flex-col items-center border border-t-0 border-l-0 border-r-0 border-gray-200">
                <h1 class="text-3xl font-extrabold text-gray-900">Mon compte</h1>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Gérez les paramètres de votre compte</p>
                <nav class="mb-2">
                  <ul class="flex items-center gap-4 text-sm">
                    <li>
                      <RouterLink class="p-2 font-light text-gray-400" to="/profil" :class="{ '!text-black border-b-2 border-b-green-500': route.path === '/profil' }">Profil</RouterLink>
                    </li>
                    <li>
                      <RouterLink class="p-2 font-light text-gray-400" to="/profil/classe" :class="{ '!text-black border-b-2 border-b-green-500': route.path === '/profil/classe' }">Classe</RouterLink>
                    </li>
                    <li>
                      <RouterLink class="p-2 font-light text-gray-400" to="/profil/devoirs" :class="{ '!text-black border-b-2 border-b-green-500': route.path === '/profil/devoirs' }">Devoirs ajoutés</RouterLink>
                    </li>
                    <li>
                      <RouterLink class="p-2 font-light text-gray-400" to="/profil/settings" :class="{ '!text-black border-b-2 border-b-green-500': route.path === '/profil/settings' }">Sécurité</RouterLink>
                    </li>
                  </ul>
                </nav>
            </div>
        </div>

      <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
        <router-view></router-view>
      </div>
    </section>
</template>