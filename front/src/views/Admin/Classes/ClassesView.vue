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
const classes = ref([]);
const error = ref('');
const isModalOpen = ref(false);
const modifierClasse = ref(null);

const openModal = (classe) => {
  modifierClasse.value = { ...classe };
  isModalOpen.value = true;
};
const closeModal = () => {
  isModalOpen.value = false;
};


onMounted(async () => {
    try {
        const response = await axios.get(`${API_URL}/classes`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        classes.value = response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des classes:', e);
        error.value = 'Impossible de récupérer les classes.';
    }
});

const updateClasse = async () => {
  if (!modifierClasse.value) return;

  if (modifierClasse.value.intitule === "3ème année") {
    modifierClasse.value.promo = "S5/S6";
  } else if (modifierClasse.value.intitule === "2ème année") {
    modifierClasse.value.promo = "S3/S4";
  } else {
    modifierClasse.value.promo = "S1/S2";
  }

  try {
    // Créer un nouvel objet avec seulement les propriétés à mettre à jour
    const classeData = JSON.stringify({
      intitule: modifierClasse.value.intitule,
      promo: modifierClasse.value.promo
    });

    await axios.patch(`${API_URL}/classes/${modifierClasse.value.id}`, classeData, {
      headers: {
        "Content-Type": "application/merge-patch+json",
        "Authorization": `Bearer ${store.state.token}`
      }
    });

    // Mettre à jour la classe dans le tableau local
    const index = classes.value.findIndex(u => u.id === modifierClasse.value.id);
    if (index !== -1) {
      classes.value[index] = { ...modifierClasse.value };
    }

    closeModal();

  } catch (error) {
    console.error('Erreur lors de la mise à jour de la classe:', error);
  }
};
</script>
<template>
    <div class="flex items-center justify-between text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div>
            <p class="text-gray-800 font-semibold">Liste des classes</p>
            <div class="mt-1 text-sm text-gray-500">Gérer les différentes classes</div>
        </div>
        <div class="">
            <Button variant="solid" size="small" tag="a" href="classes/new">Ajouter une classes</Button>
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
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">ID</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Promo</th>
                                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="border-b border-gray-200" v-for="classe in classes">
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.id }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.intitule }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ classe.promo }}</td>
                                <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                    <DropdownMenu>
                                        <!-- Personnalisation du bouton déclencheur -->
                                        <template #trigger>
                                            <Button class="inline-flex hover:cursor-pointer" variant="ghost" size="small">
                                                <Ellipsis stroke-width="1.5" size="16" />
                                            </Button>
                                        </template>

                                        <div class="px-2">
                                          <button @click="openModal(classe)" class="py-2 flex items-center justify-between w-full text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                                            <span>Modifier</span>
                                            <FilePenLine stroke-width="1.5" size="16"/>
                                          </button>
                                            <hr class="text-gray-200">
                                            <router-link :to="`classes/${classe.id}/delete`"
                                                         class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
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
                          <h2 class="text-lg font-light mb-4">Modifier une classe</h2>
                      <div>

                          <div class="">
                              <form @submit.prevent="updateClasse" class="px-4 py-5 sm:px-6">
                                  <div class="mb-6">
                                    <label for="intitule" class="block mb-2 text-sm font-medium text-gray-900">Année en cours</label>
                                    <select v-model="modifierClasse.intitule" id="intitule" name="intitule" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                                      <option value="1ère année">1ère année</option>
                                      <option value="2ème année">2ème année</option>
                                      <option value="3ème année">3ème année</option>
                                    </select>
                                  </div>
                                  <div class="mb-6">
                                    <label for="promo" class="block mb-2 text-sm font-medium text-gray-900">Promo</label>
                                    <select v-model="modifierClasse.promo" id="promo" name="promo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                                      <option value="S1/S2">S1/S2</option>
                                      <option value="S3/S4">S3/S4</option>
                                      <option value="S5/S6">S5/S6</option>
                                    </select>
                                  </div>
                              <Button variant="solid" size="small" type="submit">Modifier</Button>
                          </form>
                      </div>
                  </div>
              </div>
          </div>

                    <div class="flex items-center w-full justify-between gap-8 mt-24">
                        <router-link to="/admin/devoirs" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-start">
                                    <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
                                    <div class="text-left">
                                        <p class="text-gray-800 font-normal text-sm">Liste des devoirs</p>
                                        <div class="text-xs text-gray-500 font-light">Gérer les différents devoirs</div>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                        <router-link to="/admin/classes/new" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
                            <div>
                                <div class="flex items-center gap-4 justify-end">
                                    <div class="text-right">
                                        <p class="text-gray-800 font-normal text-sm">Nouvelle classe</p>
                                        <div class="text-xs text-gray-500 font-light">Ajouter une nouvelle classe</div>
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