<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

// État de visibilité du menu
const isOpen = ref(false);

// Référence à l'élément du menu pour détecter les clics extérieurs
const dropdownRef = ref(null);

// Gestion de l'ouverture/fermeture du menu
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

// Fermer le menu quand on clique à l'extérieur
const closeWhenClickedOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

// Ajout/suppression des écouteurs d'événements
onMounted(() => {
    document.addEventListener('click', closeWhenClickedOutside);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen.value) {
            isOpen.value = false;
        }
    });
});

onUnmounted(() => {
    document.removeEventListener('click', closeWhenClickedOutside);
});
</script>

<template>
    <div ref="dropdownRef" class="relative inline-block text-left">
        <!-- Slot pour le bouton déclencheur -->
        <div @click="toggleDropdown">
            <slot name="trigger">
                <button class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Menu
                    <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </slot>
        </div>

        <!-- Menu déroulant -->
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div v-if="isOpen" class="z-50 w-36 absolute right-0 mt-2 origin-top-right bg-white rounded-lg shadow-lg divide-y divide-gray-100 border border-[#D8D9E0]">
                <div class="py-1">
                    <!-- Slot pour le contenu du menu -->
                    <slot></slot>
                </div>
            </div>
        </transition>
    </div>
</template>
