<script setup>
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import {RouterLink} from "vue-router";
import {ArrowLeft, ArrowRight, Ellipsis, FilePenLine, Trash2} from "lucide-vue-next";
import DropdownMenu from "@/components/DropdownMenu.vue";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const matieres = ref([]);
const error = ref('');
const isModalOpen = ref(false);
const modifierMatiere = ref(null);

const openModal = (matiere) => {
  modifierMatiere.value = { ...matiere };
  isModalOpen.value = true;
};
const closeModal = () => {
  isModalOpen.value = false;
};

onMounted(async () => {
    try {
        const response = await axios.get(`${API_URL}/matieres`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        matieres.value = response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des matières:', e);
        error.value = 'Impossible de récupérer les matières.';
    }
});

const updateMatiere = async () => {
  if (!modifierMatiere.value) return;

  try {
    // Créer un nouvel objet avec seulement les propriétés à mettre à jour
    const matiereData = JSON.stringify({
      nom: modifierMatiere.value.nom,
      code: modifierMatiere.value.code,
      couleur: modifierMatiere.value.couleur
    });

    await axios.patch(`${API_URL}/matieres/${modifierMatiere.value.id}`, matiereData, {
      headers: {
        "Content-Type": "application/merge-patch+json",
        "Authorization": `Bearer ${store.state.token}`
      }
    });

    // Mettre à jour l'utilisateur dans le tableau local
    const index = matieres.value.findIndex(u => u.id === modifierMatiere.value.id);
    if (index !== -1) {
      matieres.value[index] = { ...modifierMatiere.value };
    }

    closeModal();

  } catch (error) {
    console.error('Erreur lors de la mise à jour de la matière:', error);
  }
};
</script>
<template>
    <div class="flex items-center justify-between text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div>
            <p class="text-gray-800 font-semibold">Liste des matières</p>
            <div class="mt-1 text-sm text-gray-500">Gérer les différentes matières</div>
        </div>
        <div class="">
            <Button variant="solid" size="small" tag="a" href="matieres/new">Ajouter une matière</Button>
        </div>
    </div>

    <div class="mx-4 my-4">
        <section>
            <div>
                <div>
                    <div class="max-w-full overflow-x-auto border border-gray-200 border-b-0 rounded-lg">
                        <table class="w-full text-left text-gray-500">
                            <thead class="border-b border-gray-200">
                                <tr class="uppercase">
                                    <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Nom</th>
                                    <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Code</th>
                                    <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Couleur</th>
                                    <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-200" v-for="matiere in matieres">
                                    <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ matiere.nom }}</td>
                                    <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ matiere.code }}</td>
                                    <td class="px-6 py-4 text-xs font-normal w-auto">
                                        <span class="h-4 rounded-lg w-auto py-1 px-2" :class="matiere.couleur">{{matiere.couleur}}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                        <DropdownMenu>
                                            <!-- Personnalisation du bouton déclencheur -->
                                            <template #trigger>
                                                <Button class="inline-flex hover:cursor-pointer" variant="ghost" size="small">
                                                    <Ellipsis stroke-width="1.5" size="16" />
                                                </Button>
                                            </template>

                                            <div class="px-2">
                                                <button @click="openModal(matiere)" class="py-2 flex items-center justify-between w-full text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                                    <span>Modifier</span>
                                                    <FilePenLine stroke-width="1.5" size="16"/>
                                                </button>
                                                <hr class="text-gray-200">
                                                <router-link :to="`matieres/${matiere.id}/delete`" class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                                    <span class="text-red-600">Supprimer</span>
                                                    <Trash2 stroke-width="1.5" size="16"/>
                                                </router-link>
                                            </div>
                                        </DropdownMenu>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                  <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                      <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                      <h2 class="text-lg font-light mb-4">Modifier un utilisateur</h2>
                      <div>

                        <div class="">
                          <form @submit.prevent="updateMatiere" class="px-4 py-5 sm:px-6">
                            <div class="mb-6">
                              <label for="nom" class="block mb-2 text-sm font-medium text-gray-900">Matière</label>
                              <input type="text" id="nom" name="nom" v-model="modifierMatiere.nom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="Le nom de la matière" required>
                            </div>
                            <div class="mb-6">
                              <label for="code" class="block mb-2 text-sm font-medium text-gray-900">Code</label>
                              <input type="text" id="code" name="code" v-model="modifierMatiere.code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="Le code de la matière" required>
                            </div>
                            <div class="mb-6">
                              <label for="couleur" class="block mb-2 text-sm font-medium text-gray-900">Couleur</label>
                              <input type="text" id="couleur" name="couleur" v-model="modifierMatiere.couleur" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="La couleur de la matière" required>
                            </div>
                            <Button variant="solid" size="small" type="submit">Modifier</Button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>

                    <div class="flex items-center w-full justify-between gap-8 mt-24">
                        <router-link to="/admin/classes" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-start">
                                    <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
                                    <div class="text-left">
                                        <p class="text-gray-800 font-normal text-sm">Liste des classes</p>
                                        <div class="text-xs text-gray-500 font-light">Gérer les différentes classes</div>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                        <router-link to="/admin/matieres/new" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-end">
                                    <div class="text-right">
                                        <p class="text-gray-800 font-normal text-sm">Nouvelle matière</p>
                                        <div class="text-xs text-gray-500 font-light">Ajouter une nouvelle matière</div>
                                    </div>
                                    <ArrowRight stroke-width="1.5" size="24" class="mr-2" />
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>