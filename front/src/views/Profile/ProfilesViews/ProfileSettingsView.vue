<script setup>
import {inject, onMounted, ref, watch} from 'vue'
import { useStore } from 'vuex'
import Button from '@/components/Button.vue'
import axios from "axios";
import router from "@/router/index.js";
import {FilePenLine} from "lucide-vue-next";

const store = useStore()
const user = ref(store.state.user)
const error = ref(null)
const isModalOpen = ref(false)
const isModalDeleteOpen = ref(false)
const updatedEmail = ref(user.value.email);

const openModal = () => {
  updatedEmail.value = user.value.email;
  isModalOpen.value = true;
};

const openDeleteModal = () => {
  isModalDeleteOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  isModalDeleteOpen.value = false;
};

// Fermer la modale avec la touche Escape
onMounted(() => {
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isModalOpen.value) {
      closeModal();
    }
  });
});

const triggerToast = inject('triggerToast');


const API_URL = import.meta.env.VITE_API_BASE_URL;
const token = store.state.token;

const deleteUser = async () => {
  if (!user.value) return;

  try {
    await axios.delete(`${API_URL}/users/${user.value.id}`, {
      headers: {
        "Content-Type": "application/ld+json",
        "Authorization": `Bearer ${token}`
      },
    });

    // Vider le store après suppression
    store.dispatch('logout');

    // Rediriger vers la connexion
    triggerToast("Suppression réussie","Vôtre compte à bien été supprimé.", 'success');
    router.push('/connexion')

  } catch (e) {
    triggerToast("Echec de la suppression","Impossible de supprimer le compte.", 'error');
  }
};

const updateMail = async () => {
  if (!user.value || !updatedEmail.value) return;

  try {
    await axios.patch(`${API_URL}/users/${user.value.id}`,
        { email: updatedEmail.value },
        {
          headers: {
            "Content-Type": "application/merge-patch+json",
            "Authorization": `Bearer ${token}`
          }
        });

    // Loguer pour déboguer
    console.log('Avant mise à jour dans le store:', user.value);

    // Mettre à jour l'email localement dans le store
    store.commit('setUser', { ...user.value, email: updatedEmail.value });
    localStorage.setItem('email', updatedEmail.value );
    // Loguer après mise à jour
    console.log('Après mise à jour dans le store:', store.state.user);

    triggerToast("Succès", "Adresse e-mail mise à jour.", "success");

    store.dispatch('logout');
    closeModal();
    window.location.reload();

  } catch (error) {
    triggerToast("Erreur", "Impossible de mettre à jour l'adresse e-mail.", "error");
  }
};

</script>


<template>
  <div>
    <div class="bg-white shadow-sm sm:rounded-lg border border-b-0 border-gray-200">
      <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-between block">
        <div class="md:mb-10">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Sécurité</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Gérez les paramètres liés au compte.</p>
        </div>
      </div>
      <div class="border-t border-gray-200 md:flex block">
        <div class="w-full">
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
            <div class="text-sm font-normal text-gray-500">Modifier l'adresse mail</div>
            <p @click="openModal" class="mt-1 max-w-2xl text-sm text-gray-500 hover:underline transition duration-200 cursor-pointer">Changer l'adresse mail</p>
            <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                <h2 class="text-lg font-light mb-4">Modifier l'adresse mail</h2>
                <div>
                  <div class="">
                    <form @submit.prevent="updateMail" class="px-4 py-5 sm:px-6">
                      <div class="mb-6">
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Nouvelle adresse mail</label>
                        <input type="text" id="email" name="email" v-model="updatedEmail" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="Votre adresse mail" required>
                      </div>
                      <Button variant="solid" size="small" type="submit">Modifier</Button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="border-t border-gray-200"></div>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
            <div class="text-sm font-normal text-gray-500">Modifier le mot de passe</div>
            <a href="/mot-de-passe-oublie" class="mt-1 max-w-2xl text-sm text-gray-500 hover:underline transition duration-200">Changer de mot de passe</a>
          </div>
          <div class="border-t border-gray-200"></div>
<!--          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">-->
<!--            <div class="text-sm font-normal text-gray-500">Report un bug</div>-->
<!--            <a href="/mot-de-passe-oublie" class="mt-1 max-w-2xl text-sm text-gray-500 hover:underline transition duration-200">Report un bug</a>-->
<!--          </div>-->
        </div>
      </div>
    </div>
    <div class="bg-linear-160 from-red-50/10 to-red-200/30 shadow-sm sm:rounded-lg border border-b-0 border-gray-200 mt-5">
      <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-between block">
        <div class="md:mb-2">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Supprimer le compte</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Tu ne veux plus utiliser nos services ? Tu peux supprimer ton compte en cliquant sur ce bouton. Attention, cette action est irréversible. Toutes les informations liées à ton compte seront supprimées.</p>
          <Button @click="openDeleteModal" variant="solid" size="small" color="danger" class="mt-4">Supprimer</Button>
          <transition name="modal-fade">
            <div v-if="isModalDeleteOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center">
              <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg mb-2">Supprimer le compte</h2>
                <p class="text-xs font-light">Êtes-vous sûr de vouloir supprimer votre compte ?</p>
                <div class="mt-4 flex justify-end gap-2">
                  <button @click="closeModal" class="bg-gray-200 px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-gray-300 transition">Annuler</button>
                  <button @click="deleteUser" class="bg-red-600 text-white px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-red-700 transition">Supprimer</button>
                </div>
              </div>
            </div>
          </transition>
        </div>
      </div>
      <div class="border-t border-gray-200 md:flex block">

      </div>
    </div>
  </div>
</template>

<style>
.modal-fade-enter-active, .modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
}
</style>