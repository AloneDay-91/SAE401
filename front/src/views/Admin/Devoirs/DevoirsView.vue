<script setup>
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import {RouterLink} from "vue-router";
import {FilePenLine, Trash2, Eye, ArrowLeft, ArrowRight, Ellipsis} from "lucide-vue-next";
import DropdownMenu from "@/components/DropdownMenu.vue";
import Button from "@/components/Button.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const devoirs = ref([]);
const matieres = ref([]);
const categories = ref([]);
const error = ref('');
const isModalOpen = ref(false);
const modifierDevoir = ref(null);

const openModal = (devoir) => {
  modifierDevoir.value = { ...devoir, date: extractDate(devoir.date), heure: extractTime(devoir.heure) };
  isModalOpen.value = true;
};
const closeModal = () => {
  isModalOpen.value = false;
};

// formater la date et l'heure
const formatDate = (date) => {
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
  return new Date(date).toLocaleDateString('fr-FR', options);
};

const formatTime = (date) => {
  const options = { hour: '2-digit', minute: '2-digit', timeZone: 'Europe/Paris' };
  return new Date(date).toLocaleTimeString('fr-FR', options);
};

const extractDate = (dateTime) => {
  return dateTime ? dateTime.split('T')[0] : '';
};

const extractTime = (dateTime) => {
  if (!dateTime) return '';

  const date = new Date(dateTime);
  date.setHours(date.getHours() + 1);
  return date.toISOString().substring(11, 16);
};

onMounted(async () => {
  try {
    const devoirsresponse = await axios.get(`${API_URL}/devoirs`, {
      headers: {
        "Content-Type": "application/ld+json",
        "Authorization": `Bearer ${store.state.token}`
      },
    });
    devoirs.value = devoirsresponse.data.member;

    const matieresresponse = await axios.get(`${API_URL}/matieres`, {
      headers: {
        "Content-Type": "application/ld+json",
        "Authorization": `Bearer ${store.state.token}`
      },
    });
    matieres.value = matieresresponse.data.member;

    const categoriesresponse = await axios.get(`${API_URL}/categories`, {
      headers: {
        "Content-Type": "application/ld+json",
        "Authorization": `Bearer ${store.state.token}`
      },
    });
    categories.value = categoriesresponse.data.member;
  } catch (e) {
    console.error('Erreur lors de la récupération des matières:', e);
    error.value = 'Impossible de récupérer les matières.';
  }
});

const updateDevoir = async () => {
  if (!modifierDevoir.value) return;

  try {
    const [hours, minutes] = modifierDevoir.value.heure.split(':');
    const dateTime = new Date(modifierDevoir.value.date); // Date de l'input
    dateTime.setHours(parseInt(hours), parseInt(minutes), 0); // Heure locale

    const devoirData = JSON.stringify({
      intitule: modifierDevoir.value.intitule,
      date: modifierDevoir.value.date,
      heure: dateTime.toISOString(),
      matiere: modifierDevoir.value.matiere,
      categorie: modifierDevoir.value.categorie,
    });

    await axios.patch(`${API_URL}/devoirs/${modifierDevoir.value.id}`, devoirData, {
      headers: {
        "Content-Type": "application/merge-patch+json",
        "Authorization": `Bearer ${store.state.token}`,
      },
    });

    const index = devoirs.value.findIndex((u) => u.id === modifierDevoir.value.id);
    if (index !== -1) {
      Object.assign(devoirs.value[index] = {
        ...modifierDevoir.value,
      heure: dateTime.toISOString(),
      });
    }

    closeModal();
  } catch (error) {
    console.error('Erreur lors de la mise à jour du devoir:', error);
  }
};
</script>
<template>
  <div class="flex items-center justify-between text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div>
      <p class="text-gray-800 font-semibold">Liste des devoirs</p>
      <div class="mt-1 text-sm text-gray-500">Gérer les différents devoirs</div>
    </div>
    <div class="">
      <Button variant="solid" size="small" tag="a" href="devoirs/new">Ajouter un devoir</Button>
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
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Date</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Heure</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Matière</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Catégorie</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Utilisateur</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Classes</th>
                <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr class="border-b border-gray-200" v-for="devoir in devoirs">
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.intitule }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatDate(devoir.date) }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatTime(devoir.heure) }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_matieres.nom }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_categories.nom }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_users.nom }} {{ devoir.id_users.prenom }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_classes.promo }} {{ devoir.id_users.td }} {{ devoir.id_users.tp }}</td>
                <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                  <DropdownMenu>
                    <!-- Personnalisation du bouton déclencheur -->
                    <template #trigger>
                      <Button class="inline-flex hover:cursor-pointer" variant="ghost" size="small">
                        <Ellipsis stroke-width="1.5" size="16" />
                      </Button>
                    </template>

                    <div class="px-2">
                      <router-link :to="`devoirs/${devoir.id}`" class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                        <span>Voir</span>
                        <Eye stroke-width="1.5" size="16" />
                      </router-link>
                      <button @click="openModal(devoir)" class="py-2 flex items-center justify-between w-full text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
                        <span>Modifier</span>
                        <FilePenLine stroke-width="1.5" size="16"/>
                      </button>
                      <hr class="text-gray-200">
                      <router-link :to="`devoirs/${devoir.id}/delete`" class="py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1">
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


          <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 overflow-y-auto pt-24">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative m-12">
              <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
              <h2 class="text-lg font-light mb-4">Modifier un devoir</h2>
              <div>

                <div class="">
                  <form @submit.prevent="updateDevoir" class="px-4 py-5 sm:px-6">
                    <div class="mb-6">
                      <label for="intitule" class="block mb-2 text-sm font-medium text-gray-900">Intitulé du devoir</label>
                      <input type="text" id="intitule" name="intitule" v-model="modifierDevoir.intitule" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="L'intitule du devoir" required>
                    </div>
                    <div class="mb-6">
                      <label for="contenu" class="block mb-2 text-sm font-medium text-gray-900">Contenu du devoir</label>
                      <input type="text" id="contenu" name="contenu" v-model="modifierDevoir.contenu" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="Le contenu du devoir" required>
                    </div>
                    <div class="mb-6 flex-col gap-4">
                      <label for="date" class="block mb-2 text-sm font-medium text-gray-900">Pour le :</label>
                      <div class="flex gap-4">
                        <input type="date" id="date" name="date" v-model="modifierDevoir.date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>

                        <label for="heure" class="block mb-2 text-sm font-medium text-gray-900"></label>
                        <input type="time" id="heure" name="heure" v-model="modifierDevoir.heure" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                      </div>
                    </div>
                    <div class="mb-6">
                      <label for="td" class="block mb-2 text-sm font-medium text-gray-900">Matière</label>
                      <select v-model="modifierDevoir.id_matieres.nom" id="matiere" name="matiere"
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                        <option v-for="matiere in matieres" :value="matiere.nom">
                          {{ matiere.nom }}
                        </option>
                      </select>
                    </div>
                    <div class="mb-6">
                      <label for="categorie" class="block mb-2 text-sm font-medium text-gray-900">Categorie</label>
                      <select v-model="modifierDevoir.id_categories.nom" id="categorie" name="categorie"
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                        <option v-for="categorie in categories" :value="categorie.nom">
                          {{ categorie.nom }}
                        </option>
                      </select>
                    </div>
                    <Button variant="solid" size="small" type="submit">Modifier</Button>
                  </form>
                </div>
              </div>
            </div>
          </div>


          <div class="flex items-center w-full justify-between gap-8 mt-24">
            <router-link to="/admin/dashboard" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
              <div>
                <div class="flex items-center gap-4 justify-start">
                  <ArrowLeft stroke-width="1.5" size="24" class="mr-2" />
                  <div class="text-left">
                    <p class="text-gray-800 font-normal text-sm">Tableau de bord</p>
                    <div class="text-xs text-gray-500 font-light">Accueil de l'administration</div>
                  </div>
                </div>
              </div>
            </router-link>
            <router-link to="/admin/devoirs/new" class="w-full border border-gray-200 hover:border-green-400 p-12 rounded-lg hover:bg-gray-100 transition duration-400">
              <div>
                <div class="flex items-center gap-4 justify-end">
                  <div class="text-right">
                    <p class="text-gray-800 font-normal text-sm">Nouveau devoir</p>
                    <div class="text-xs text-gray-500 font-light">Ajouter un nouveau devoir</div>
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