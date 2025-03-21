<script setup>
import {computed, onMounted, ref, inject} from 'vue';
import {useStore} from 'vuex';
import axios from 'axios';
import DropdownMenu from "@/components/DropdownMenu.vue";
import {RouterLink} from "vue-router";
import {Eye, FilePenLine, Trash2} from "lucide-vue-next";
import Button from "@/components/Button.vue";
import 'ldrs/ring'
import { pulsar } from 'ldrs'

const triggerToast = inject('triggerToast');

pulsar.register()

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const user = ref(store.state.user);

const token = localStorage.getItem('token');
const devoirs = ref([]);
const error = ref('');
const isLoading = ref(true);

const isModalOpen = ref(false);
const isModalEditOpen = ref(false);
const isModalViewOpen = ref(false);
const selectedDevoirId = ref(null);

onMounted(async () => {
    await getDevoirs();
    await getMatieres();
    await getCategories();
    await getFormatRendu();
    await getClasses();
});

const getDevoirs = async () => {
    try {
        const response = await axios.get(`${API_URL}/devoirs`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        devoirs.value = response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des devoirs:', e);
        error.value = 'Impossible de récupérer les devoirs.';
    } finally {
        isLoading.value = false;
    }
};

const devoirsUtilisateur = computed(() => {
    if (!devoirs.value || !user.value || !user.value.id) {
        return [];
    }
    return devoirs.value.filter(devoir =>
        devoir.id_users && devoir.id_users['@id'] === `/api/users/${user.value.id}`
    );
});

const formatDate = (date) => new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: '2-digit', day: '2-digit' });
const formatTime = (date) => new Date(date).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

const openModal = (devoirId) => {
    selectedDevoirId.value = devoirId;
    isModalOpen.value = true;
};

const closeModal = () => {
    selectedDevoirId.value = null;
    isModalOpen.value = false;
    isModalEditOpen.value = false;
    isModalViewOpen.value = false;
};

const deleteDevoir = async () => {
    if (!selectedDevoirId.value) return;

    try {
        await axios.delete(`${API_URL}/devoirs/${selectedDevoirId.value}`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${token}`
            },
        });
        devoirs.value = devoirs.value.filter(devoir => devoir.id !== selectedDevoirId.value);
    } catch (e) {
        console.error('Erreur lors de la suppression du devoir:', e);
        error.value = 'Impossible de supprimer le devoir.';
    }
    closeModal();
    await getDevoirs();
};

const matieres = ref([]);
const categories = ref([]);
const classes = ref([]);
const formatRendus = ref([]);

const getMatieres = async () => {
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
};

const getCategories = async () => {
    try {
        const response = await axios.get(`${API_URL}/categories`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        categories.value = response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des catégories:', e);
        error.value = 'Impossible de récupérer les catégories.';
    }
};

const getFormatRendu = async () => {
    try {
        const response = await axios.get(`${API_URL}/format_rendus`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        formatRendus.value = response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des formats de rendu:', e);
        error.value = 'Impossible de récupérer les formats de rendu.';
    }
}

const getClasses = async () => {
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
};

const modifierDevoir = ref({
    intitule: '',
    contenu: '',
    date: '',
    heure: '',
    id_matieres: '',
    id_categories: '',
    id_classes: '',
    id_formatRendu: '',
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
            matiere: modifierDevoir.value.id_matieres,
            categorie: modifierDevoir.value.id_categories,
            classe: modifierDevoir.value.id_classes,
            formatRendu: modifierDevoir.value.id_formatRendu,
        });

        await axios.patch(`${API_URL}/devoirs/${selectedDevoirId.value}`, devoirData, {
            headers: {
                "Content-Type": "application/merge-patch+json",
                "Authorization": `Bearer ${store.state.token}`,
            },
        });

        const index = devoirs.value.findIndex((u) => u.id === devoirs.value.id);
        if (index !== -1) {
            Object.assign(devoirs.value[index] = {
                ...modifierDevoir.value,
                heure: dateTime.toISOString(),
            });
        }
        closeModal();
        await getDevoirs();
        triggerToast("Devoir modifié", "Le devoir a été modifié avec succès.", 'success');
    } catch (error) {
        triggerToast("Erreur", "Impossible de modifier le devoir.", 'error');
        console.error('Erreur lors de la mise à jour du devoir:', error);
    }
};

const openModalEdit = (devoirId) => {
    const devoir = devoirs.value.find(d => d.id === devoirId);
    selectedDevoirId.value = devoirId;
    if (!devoir) {
        console.error("Devoir non trouvé !");
        return;
    }

    const convertDate = (date) => {
        const d = new Date(date);
        return d.toISOString().split('T')[0]; // Format YYYY-MM-DD
    };

    modifierDevoir.value = {
        intitule: devoir.intitule,
        contenu: devoir.contenu,
        date: convertDate(devoir.date),
        heure: formatTime(devoir.heure),
        id_matieres: devoir.id_matieres ? { nom: devoir.id_matieres.nom } : { nom: "" },
        id_categories: devoir.id_categories ? { nom: devoir.id_categories.nom } : { nom: "" },
        id_classes: devoir.id_classes ? { type: devoir.id_classes.type } : { type: "" },
        id_formatRendu: devoir.id_formatRendu ? { intitule: devoir.id_formatRendu.intitule, lien: devoir.id_formatRendu.lien} : {intitule: "", lien: ""}
    };

    isModalEditOpen.value = true;
};

const openModalView = (devoirId) => {
    const devoir = devoirs.value.find(d => d.id === devoirId);
    selectedDevoirId.value = devoirId;
    if (!devoir) {
        console.error("Devoir non trouvé !");
        return;
    }

    const convertDate = (date) => {
        const d = new Date(date);
        return d.toISOString().split('T')[0]; // Format YYYY-MM-DD
    };

    modifierDevoir.value = {
        intitule: devoir.intitule,
        contenu: devoir.contenu,
        date: convertDate(devoir.date),
        heure: formatTime(devoir.heure),
        id_matieres: devoir.id_matieres ? devoir.id_matieres.nom : "",
        id_categories: devoir.id_categories ? devoir.id_categories.nom : "",
        id_classes: devoir.id_classes ? devoir.id_classes.type : "",
        id_formatRendu: devoir.id_formatRendu ? devoir.id_formatRendu.intitule : ""
    };

    isModalViewOpen.value = true;
};


</script>



<template>
    <div>
        <div class="bg-white shadow-sm sm:rounded-lg border border-b-0 border-gray-200">
            <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-between block">
                <div class="md:mb-10">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Informations les devoirs ajoutés</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Gérez les devoirs que vous avez ajoutés.</p>
                </div>
            </div>
            <div class="w-full">
                <section class="border border-l-0 border-r-0 border-b-0 border-gray-200">
                    <div>
                        <div v-if="error" class="text-red-500 mt-4">
                            {{ error }}
                        </div>

                        <!-- Affichage du loader -->
                        <div v-if="isLoading" class="flex justify-center items-center py-6">
                            <l-pulsar size="40" speed="1.75" color="#05df72"></l-pulsar>
                        </div>

                        <!-- Tableau des devoirs -->
                        <div v-else>
                            <table class="w-full">
                                <thead class="border-b border-gray-200">
                                <tr class="uppercase bg-gray-100">
                                    <th scope="col" class="text-left px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                                    <th scope="col" class="text-left px-6 py-3 text-gray-500 text-xs font-normal">Date</th>
                                    <th scope="col" class="text-left px-6 py-3 text-gray-500 text-xs font-normal">Heure</th>
                                    <th scope="col" class="text-left px-6 py-3 text-gray-500 text-xs font-normal">Matière</th>
                                    <th scope="col" class="text-left px-6 py-3 text-gray-500 text-xs font-normal">Catégorie</th>
                                    <th scope="col" class="text-left px-6 py-3 text-gray-500 text-xs font-normal">Classes</th>
                                    <th scope="col" class="text-left px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="devoirsUtilisateur.length > 0" v-for="devoir in devoirsUtilisateur" :key="devoir['@id']" class="border-b border-gray-200">
                                    <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.intitule }}</td>
                                    <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatDate(devoir.date) }}</td>
                                    <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatTime(devoir.heure) }}</td>
                                    <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_matieres.nom }} {{ devoir.id_matieres.code }}</td>
                                    <td class="w-auto px-6 py-4">
                                        <div :class="[
                        `bg-${devoir.id_categories.couleur || 'gray'}-200`,
                        `text-${devoir.id_categories.couleur || 'gray'}-500`,
                        ' rounded text-xs font-normal py-1'
                      ]">
                                            {{ devoir.id_categories.nom || 'Non défini' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                        {{ devoir.id_classes.promo }} {{ devoir.id_classes.td }} {{ devoir.id_classes.tp }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                        <DropdownMenu>
                                            <template #trigger>
                                                <Button class="inline-flex hover:cursor-pointer" variant="outline" size="small">
                                                    Options
                                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </Button>
                                            </template>
                                            <div class="px-2">
                                                <button @click.prevent="openModalView(devoir.id)" class="w-full py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1 hover:cursor-pointer">
                                                    <span>Voir</span>
                                                    <Eye stroke-width="1.5" size="16" />
                                                </button>
                                                <button @click="openModalEdit(devoir.id)" class="w-full py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1 hover:cursor-pointer">
                                                    <span>Modifier</span>
                                                    <FilePenLine stroke-width="1.5" size="16"/>
                                                </button>
                                                <hr class="text-gray-200">
                                                <button @click="openModal(devoir.id)" class="w-full py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded px-2 my-1 hover:cursor-pointer">
                                                    <span class="text-red-600">Supprimer</span>
                                                    <Trash2 stroke-width="1.5" size="16"/>
                                                </button>
                                            </div>
                                        </DropdownMenu>
                                    </td>
                                </tr>
                                <tr v-if="devoirsUtilisateur.length === 0">
                                    <td colspan="7" class="px-6 py-8 text-gray-500 text-xs font-normal text-center w-auto">
                                        Vous n'avez pas encore ajouté de devoirs.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <transition name="modal-fade">
        <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 z-60 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg mb-2">Supprimer le devoir</h2>
                <p class="text-xs font-light">Êtes-vous sûr de vouloir supprimer ce devoir ?</p>
                <div class="mt-4 flex justify-end gap-2">
                    <button @click="closeModal" class="bg-gray-200 px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-gray-300 transition">Annuler</button>
                    <button @click="deleteDevoir" class="bg-red-600 text-white px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-red-700 transition">Supprimer</button>
                </div>
            </div>
        </div>
    </transition>

    <transition name="modal-fade">
        <div v-if="isModalEditOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 overflow-y-auto pt-24">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative m-12">
                <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                <h2 class="text-lg font-light mb-4">Modifier un devoir</h2>
                <div>
                    <div class="">
                        <form @submit.prevent="updateDevoir" class="px-4 py-5 sm:px-6">
                            <input type="hidden" v-model="modifierDevoir.id" name="id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
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
                                <select v-model="modifierDevoir.id_matieres.nom" id="matiere" name="matiere" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
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
                            <div class="mb-6">
                                <label for="classe" class="block mb-2 text-sm font-medium text-gray-900">Classe</label>
                                <select v-model="modifierDevoir.id_classes.type" id="classe" name="classe"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                                    <option v-for="classe in classes" :value="classe.type">
                                        {{ classe.promo }} {{ classe.type }}
                                    </option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2 w-full">
                                <div class="mb-6 w-full">
                                    <label for="formatRendu" class="block mb-2 text-sm font-medium text-gray-900">Format</label>
                                    <select v-model="modifierDevoir.id_formatRendu.intitule" id="formatRendu" name="formatRendu"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" required>
                                        <option v-for="formatRendu in formatRendus" :value="formatRendu.intitule">
                                            {{ formatRendu.intitule }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-6 w-full">
                                    <label for="rendu" class="block mb-2 text-sm font-medium text-gray-900">Lien</label>
                                    <input type="text" id="rendu" name="rendu" v-model="modifierDevoir.id_formatRendu.lien" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" placeholder="Lien de rendu">
                                </div>
                            </div>
                            <Button variant="solid" size="small" type="submit" >Modifier</Button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <transition name="modal-fade">
        <div v-if="isModalViewOpen" class="fixed inset-0 bg-black/70 z-60 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative m-12">
                <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                <h2 class="text-lg font-light mb-4">Voir le devoir</h2>
                <form class="px-4 py-5 sm:px-6">
                    <div class="mb-6">
                        <label for="intitule" class="block mb-2 text-sm font-medium text-gray-900">Intitulé du devoir</label>
                        <input type="text" id="intitule" name="intitule" v-model="modifierDevoir.intitule" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" disabled>
                    </div>
                    <div class="mb-6">
                        <label for="contenu" class="block mb-2 text-sm font-medium text-gray-900">Contenu du devoir</label>
                        <input type="text" id="contenu" name="contenu" v-model="modifierDevoir.contenu" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" disabled>
                    </div>
                    <div class="mb-6">
                        <label for="date" class="block mb-2 text-sm font-medium text-gray-900">Pour le :</label>
                        <input type="date" id="date" name="date" v-model="modifierDevoir.date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" disabled>
                    </div>
                    <div class="mb-6">
                        <label for="heure" class="block mb-2 text-sm font-medium text-gray-900">Heure</label>
                        <input type="time" id="heure" name="heure" v-model="modifierDevoir.heure" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" disabled>
                    </div>
                    <div class="mb-6">
                        <label for="matiere" class="block mb-2 text-sm font-medium text-gray-900">Matière</label>
                        <input type="text" id="matiere" name="matiere" v-model="modifierDevoir.id_matieres" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" disabled>
                    </div>
                    <div class="mb-6">
                        <label for="categorie" class="block mb-2 text-sm font-medium text-gray-900">Catégorie</label>
                        <input type="text" id="categorie" name="categorie" v-model="modifierDevoir.id_categories" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" disabled>
                    </div>
                    <div class="mb-6">
                        <label for="classe" class="block mb-2 text-sm font-medium text-gray-900">Classe</label>
                        <input type="text" id="classe" name="classe" v-model="modifierDevoir.id_classes" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" disabled>
                    </div>
                    <div class="mb-6">
                        <label for="formatRendu" class="block mb-2 text-sm font-medium text-gray-900">Format de rendu</label>
                        <input type="text" id="formatRendu" name="formatRendu" v-model="modifierDevoir.id_formatRendu" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 py-1.5" disabled>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button @click.prevent="closeModal" class="bg-gray-200 px-3 py-1.5 text-xs font-light rounded cursor-pointer hover:bg-gray-300 transition">Fermer</button>
                    </div>
                </form>
            </div>
        </div>
    </transition>


</template>
<style>
.modal-fade-enter-active, .modal-fade-leave-active {
    transition: opacity 0.3s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
    opacity: 0;
}
</style>