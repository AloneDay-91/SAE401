<script setup>
import {computed, onMounted, ref, inject} from 'vue';
import {useStore} from 'vuex';
import axios from 'axios';
import {Check, X, BadgeCheck} from "lucide-vue-next";
import Button from "@/components/Button.vue";
import DropdownMenu from "@/components/DropdownMenu.vue";

const API_URL = import.meta.env.VITE_API_BASE_URL;

const store = useStore();
const user = ref(store.state.user);

const token = localStorage.getItem('token');
const devoirs = ref([]);
const error = ref('');

const maxVote = 5;

const triggerToast = inject('triggerToast');

onMounted(async () => {
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
    }
});

const devoirsUtilisateur = computed(() => {
    if (!devoirs.value || !user.value || !user.value.id) {
        return [];
    }
    return devoirs.value.filter(devoir => {
        const { id_classes } = devoir;
        if (!id_classes) return false;

        // Vérification de la promo
        const promoCorrespond = id_classes.promo === user.value.promo;

        // Vérification du type de groupe (TD ou TP)
        const tdCorrespond = id_classes.type === user.value.td;
        const tpCorrespond = id_classes.type === user.value.tp;

        // Vérification du nombre de votes
        const votesCount = countVotes(devoir.id);

        // si le devoir a déjà été voté, alors on ne l'affiche pas
        if (hasVoted(devoir.id)) return false;

        // Le devoir est valide si la promo correspond, soit le TD ou TP correspond ou aucun type spécifique n'est requis, et le nombre de votes est inférieur au maximum requis
        return promoCorrespond &&
            (!id_classes.type || tdCorrespond || tpCorrespond) &&
            votesCount < maxVote;
    });
});


const formatDate = (date) => {
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(date).toLocaleDateString('fr-FR', options);
};

const formatTime = (date) => {
    const options = { hour: '2-digit', minute: '2-digit' };
    return new Date(date).toLocaleTimeString('fr-FR', options);
};

const getVotes = async () => {
    try {
        const response = await axios.get(`${API_URL}/user_devoir_votes`, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        return response.data.member;
    } catch (e) {
        console.error('Erreur lors de la récupération des votes:', e);
        error.value = 'Impossible de récupérer les votes.';
        return [];
    }
}

const verifDevoir = async (devoirId) => {
    if (await checkExistingVote(devoirId)) {
        triggerToast("Erreur", "Vous avez déjà voté pour ce devoir", 'error');
        return;
    }

    try {
        const response = await axios.post(`${API_URL}/user_devoir_votes`, {
            vote: 1,
            verif: false,
            devoirs: '/api/devoirs/' + devoirId,
            user: '/api/users/' + user.value.id
        }, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        console.log('Devoir vérifié:', response.data);
        triggerToast("Vote ajouté","Votre vote à été pris en compte", 'success');
        await refreshVotes();
    } catch (e) {
        triggerToast("Erreur","Impossible de vérifier le devoir", 'error');
        console.error('Erreur lors de la vérification du devoir:', e);
        error.value = 'Impossible de vérifier le devoir.';
    }
    await refreshVotes();
};

const refusDevoir = async (devoirId) => {
    if (await checkExistingVote(devoirId)) {
        triggerToast("Erreur", "Vous avez déjà voté pour ce devoir", 'error');
        return;
    }
    try {
        const response = await axios.post(`${API_URL}/user_devoir_votes`, {
            vote: 0,
            verif: false,
            devoirs: '/api/devoirs/' + devoirId,
            user: '/api/users/' + user.value.id
        }, {
            headers: {
                "Content-Type": "application/ld+json",
                "Authorization": `Bearer ${store.state.token}`
            },
        });
        console.log('Devoir refusé:', response.data);
        triggerToast("Vote ajouté","Votre vote à été pris en compte", 'success');
        await refreshVotes();
    }
    catch (e) {
        console.error('Erreur lors du refus du devoir:', e);
        triggerToast("Erreur","Impossible de refuser le devoir", 'error');
        error.value = 'Impossible de refuser le devoir.';
    }
    await refreshVotes();
};

const votes = ref([]);

// Récupération initiale des votes
onMounted(async () => {
    votes.value = await getVotes();
});

// Fonction de rafraîchissement des votes
const refreshVotes = async () => {
    votes.value = await getVotes();
};

const countVotes = (devoirId) => {
    if (!votes.value.length) return 0;

    return votes.value.reduce((total, vote) => {
        const voteDevoirId = vote.devoirs['@id'].split('/').pop();
        return (voteDevoirId === devoirId.toString()) ? total + vote.vote : total;
    }, 0);
};

const checkExistingVote = async (devoirId) => {
    return votes.value.some(vote =>
        vote.devoirs['@id'] === `/api/devoirs/${devoirId}` &&
        vote.user['@id'] === `/api/users/${user.value.id}`
    );
};

const hasVoted = (devoirId) => {
    return votes.value.some(vote =>
        vote.devoirs['@id'] === `/api/devoirs/${devoirId}` &&
        vote.user['@id'] === `/api/users/${user.value.id}`
    );
};

// filtrer les devoirs vérifiés ou le vote est supérieur à 5 pour les afficher dans la section devoirs vérifiés
const devoirsVerifies = computed(() => {
    if (!devoirs.value || !votes.value) {
        return [];
    }
    return devoirs.value.filter(devoir => {
        return countVotes(devoir.id) >= maxVote;
    });
});

// si le vote est vérifier supprimer le devoir de la liste des devoirs en attente

</script>


<template>
    <section class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200">
        <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="flex items-center justify-between border border-t-0 border-l-0 border-r-0 border-gray-200">
                <div class="sm:flex sm:items-start flex flex-col items-center">
                    <h1 class="text-3xl font-extrabold text-gray-900">Devoirs en attentes</h1>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Gérez les devoirs en attentes de vérification</p>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center">
                    <Button variant="solid" size="small" tag="a" href="/devoirs/new">Créer un devoir</Button>
                </div>
            </div>
            <div class="mt-8">
                <div v-if="error" class="text-red-500 mt-4">
                    {{ error }}
                </div>

                <!-- Tableau des devoirs -->
                <div v-else class="mt-4">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md text-left">
                        <thead class="border-b border-gray-200">
                        <tr class="uppercase bg-gray-100">
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Date</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Heure</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Matière</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Catégorie</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Classes</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Votes</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="devoirsUtilisateur.length > 0" v-for="devoir in devoirsUtilisateur" :key="devoir['@id']" class="border-b border-gray-200">
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.intitule }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatDate(devoir.date) }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatTime(devoir.heure) }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_matieres.code }} {{ devoir.id_matieres.nom }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_categories.nom}}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                {{devoir.id_classes.promo}}
                                {{devoir.id_classes.type}}
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ countVotes(devoir.id) }}</td>
                            <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                <DropdownMenu>
                                    <!-- Personnalisation du bouton déclencheur -->
                                    <template #trigger>
                                        <Button class="inline-flex" variant="outline" size="small"
                                                :disabled="hasVoted(devoir.id)">
                                            {{ hasVoted(devoir.id) ? 'Déjà voté' : 'Vérifier' }}
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </Button>
                                    </template>

                                    <div class="px-2">
                                        <Button @click="verifDevoir(devoir.id)" variant="ghost" :to="`devoirs/${devoir.id}`" class="w-full py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded my-1">
                                            <span>Valider</span>
                                            <Check class="text-green-600" stroke-width="1.5" size="16" />
                                        </Button>
                                        <Button @click="refusDevoir(devoir.id)" variant="ghost" :to="`devoirs/${devoir.id}`" class="w-full py-2 flex items-center justify-between text-gray-600 font-light hover:bg-gray-200/50 rounded my-1">
                                            <span>Refuser</span>
                                            <X class="text-red-400" stroke-width="1.5" size="16" />
                                        </Button>
                                    </div>
                                </DropdownMenu>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" v-if="devoirsUtilisateur.length === 0" class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                Aucun devoir disponible pour le moment.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="flex items-center justify-between border border-t-0 border-l-0 border-r-0 border-gray-200">
                <div class="sm:flex sm:items-start flex flex-col items-center">
                    <h1 class="text-3xl font-extrabold text-gray-900">Devoirs vérifiés</h1>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Gérez les devoirs vérifiés</p>
                </div>
            </div>
            <div class="mt-8">
                <div v-if="error" class="text-red-500 mt-4">
                    {{ error }}
                </div>

                <!-- Tableau des devoirs -->
                <div v-else class=" mt-4">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md text-left">
                        <thead class="border-b border-gray-200">
                        <tr class="uppercase bg-gray-100">
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Intitulé</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Date</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Heure</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Matière</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Catégorie</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Classes</th>
                            <th scope="col" class="px-6 py-3 text-gray-500 text-xs font-normal">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="devoirsVerifies.length > 0" v-for="devoir in devoirsVerifies" :key="devoir['@id']" class="border-b border-gray-200">
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.intitule }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatDate(devoir.date) }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ formatTime(devoir.heure) }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_matieres.code }} {{ devoir.id_matieres.nom }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">{{ devoir.id_categories.nom}}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                {{devoir.id_classes.promo}}
                                {{devoir.id_classes.td}}
                                {{devoir.id_classes.tp}}
                            </td>
                            <td class="px-6 py-4 text-xs font-normal flex items-center gap-2">
                                <BadgeCheck stroke-width="1.5" size="24" fill="#04e073" color="white" />
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="7" v-if="devoirsVerifies.length === 0" class="px-6 py-4 text-gray-500 text-xs font-normal w-auto">
                                Aucun devoir vérifié pour le moment.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Router View -->
    <router-view></router-view>
</template>



