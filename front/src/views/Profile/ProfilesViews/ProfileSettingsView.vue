<script setup>
import { ref } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const user = ref(store.state.user)

</script>

<template>
  <section class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200">
    <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
      <div class="sm:flex sm:items-start flex flex-col items-center border border-t-0 border-l-0 border-r-0 border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900">Mon compte</h1>
        <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Gérez les paramètres de votre compte</p>
      </div>
    </div>
    <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
      <div class="mt-8 md:flex md:justify-between block w-full gap-4">
        <div class="w-full">
          <div class="px-4 py-5 sm:px-6 items-center">
            <div>
              <h3 class="text-lg font-medium leading-6 text-gray-900">Devoirs ajoutés</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">Gérez les devoirs ajoutés</p>
            </div>
          </div>
        </div>
        <div class="w-full">
          <form class="border border-gray-200 bg-white shadow-sm sm:rounded-lg">
            <div class="w-full">
              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                <div class="text-sm font-normal text-gray-500">Intitulé</div>
                <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{user.id_classes.intitule}}</div>
              </div>
              <div class="border-t border-gray-200"></div>
              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                <div class="text-sm font-normal text-gray-500">Promo</div>
                <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{user.id_classes.promo}}</div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <router-view></router-view>
    </div>
  </section>
</template>

Modifier Adresse mail
Modif MDP
Report bug
Supprimer Utilisateur
