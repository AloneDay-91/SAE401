<script setup>
import { ref } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const user = ref(store.state.user)

</script>

<template>
  <div>
    <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200">
      <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-between block">
        <div class="md:mb-10">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Informations sur la classe</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Gérez les informations de la classe.</p>
        </div>
      </div>
      <div class="border-t border-gray-200 md:flex block">
        <div class="w-full">
<!--          Contenu à gauche-->
        </div>
        <div class="w-full">
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
            <div class="text-sm font-normal text-gray-500">Intitulé</div>
            <div class="mt-1 text-sm text-gray-900 sm:col-span-2">
              {{ user.id_classes.intitule }}
            </div>
          </div>
          <div class="border-t border-gray-200"></div>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
            <div class="text-sm font-normal text-gray-500">Promo</div>
            <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ user.id_classes.promo }}</div>
          </div>
          <div class="border-t border-gray-200"></div>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
            <div class="text-sm font-normal text-gray-500">TD</div>
            <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ user.id_classes.td }}</div>
          </div>
          <div class="border-t border-gray-200"></div>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
            <div class="text-sm font-normal text-gray-500">TP</div>
            <div class="mt-1 text-sm text-gray-900 sm:col-span-2">
              {{ user.id_classes.tp }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>