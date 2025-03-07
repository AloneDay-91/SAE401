<script setup>
import { RouterLink, useRouter } from 'vue-router';
import { useStore } from 'vuex';
import {computed, onBeforeUnmount, ref} from 'vue';
import { User } from 'lucide-vue-next';
import { LayoutList } from 'lucide-vue-next';
import { List } from 'lucide-vue-next';
import { ListTodo } from 'lucide-vue-next';
import { ArrowLeft } from 'lucide-vue-next';
import { ListTree } from 'lucide-vue-next';

const store = useStore();
const router = useRouter();

const openProfile = ref(false);

const user = computed(() => store.state.user || {}); // Eviter des erreurs si user est undefined

// Fonction pour se déconnecter
const handleLogout = () => {
    store.dispatch('logout');
    router.push('/connexion');
};

// Vérifier le rôle de l'utilisateur
const getRoles = () => {
    try {
        return JSON.parse(localStorage.getItem('roles') || '[]');
    } catch (error) {
        console.error("Erreur lors de la lecture des rôles depuis localStorage", error);
        return [];
    }
};

const isAdmin = computed(() => getRoles().includes('ROLE_ADMIN'));
const isDelegue = computed(() => getRoles().includes('ROLE_DELEGUE'));
</script>
<template>
    <div class="text-left w-full border-b border-gray-200 mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <p class="text-gray-800 font-semibold">Paramètres</p>
        <div class="mt-1 text-sm text-gray-500">Gérer les paramètres du site</div>
    </div>
    <div class="mx-4 my-4">
        <section>
            <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
                <div class="">
                    <div class="rounded-lg divide-y divide-gray-200 ring-1 ring-gray-200 shadow bg-white">
                        <div class="divide-y divide-gray-200 gap-4 flex flex-col px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                <div class="">
                                    <div class="flex content-center items-center justify-between text-sm">
                                        <label for="maintenance_toggle" class="block font-medium text-gray-700">Mode maintenance</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Activer la maintenance du site ?</p>
                                </div>
                                <div class="relative flex items-center hidden">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="maintenance_status" value="0">
                                        <input type="checkbox" class="sr-only peer" id="maintenance_toggle" name="maintenance_status" value="1">
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-black rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                                    </label>
                                </div>
                                <div class="relative flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-400">
                                                En cours de développement
                                            </span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                <div class="">
                                    <div class="flex content-center items-center justify-between text-sm">
                                        <label for="series_visibility_toggle" class="block font-medium text-gray-700">Desactiver compétitions</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Cachées toutes les séries au public ?</p>
                                </div>
                                <div class="relative flex items-center">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="series_visibility_status" value="0">
                                        <input type="checkbox" class="sr-only peer" id="series_visibility_toggle" name="series_visibility_status" value="1" checked="">
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-black rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-6">
                    <div class="flex lg:flex-col justify-between flex-row flex-wrap gap-4">
                        <div class="flex items-start gap-4">
                            <div>
                                <p class="text-gray-900 font-semibold"></p>
                                <div class="mt-1 text-sm text-gray-500"></div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg ring-1 ring-gray-200 shadow bg-white">
                        <div class="divide-y divide-gray-200 gap-4 flex flex-col px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                <div class="">
                                    <div class="flex content-center items-center justify-between text-sm">
                                        <label for="alert_banner_toggle" class="block font-medium text-gray-700">Bannière d'alerte</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Activer la bannière pour avertir les utilisateurs ?</p>
                                </div>
                                <div class="relative flex items-center">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="alert_banner_status" value="0">
                                        <input type="checkbox" class="sr-only peer" id="alert_banner_toggle" name="alert_banner_status" value="1">
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-black rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <form action="updateMsgBanner.php" method="post" id="alertBannerForm" style="display: none;">
                            <div class="w-auto gap-4 flex flex-col mx-4 my-5 sm:m-6 ring-gray-200 mt-0">
                                <textarea class="rounded-lg shadow ring-1 ring-gray-200 border-none p-4 text-black text-sm" name="alert_message" id="alert_message" placeholder="Entrer votre message">Le site est actuellement en maintenance !</textarea>
                            </div>
                            <div class="flex justify-end gap-4 px-6 pb-4">
                                <button type="submit" class="text-white bg-black hover:shadow-lg font-normal text-xs py-2 px-4 rounded-lg">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="py-6">
                    <div class="flex lg:flex-col justify-between flex-row flex-wrap gap-4">
                        <div class="flex items-start gap-4">
                            <div>
                                <p class="text-gray-900 font-semibold"></p>
                                <div class="mt-1 text-sm text-gray-500"></div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg ring-1 ring-gray-200 shadow bg-white">
                        <div class="divide-y divide-gray-200 gap-4 flex flex-col px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-between pt-4 first:pt-0 gap-2">
                                <div class="">
                                    <div class="flex content-center items-center justify-between text-sm">
                                        <label for="alert_Competbanner_toggle" class="block font-medium text-gray-700">Bannière de réservation</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Activer la bannière pour avertir les utilisateurs ?</p>
                                </div>
                                <div class="relative flex items-center">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="alert_Competbanner_status" value="0">
                                        <input type="checkbox" class="sr-only peer" id="alert_Competbanner_toggle" name="alert_Competbanner_status" value="1" checked="">
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-black rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <form action="updateMsgRdv.php" method="post" id="competBannerForm" style="display: block;">
                            <div class="w-auto gap-4 flex flex-col mx-4 my-5 sm:m-6 ring-gray-200 mt-0">
                                <label for="compet_banner_title" class="block text-sm font-medium text-gray-900">Titre de la bannière</label>
                                <input type="text" class="rounded-lg shadow ring-1 ring-gray-200 border-none p-4 text-black text-sm" name="compet_banner_title" id="compet_banner_title" placeholder="Entrer votre titre" value="Ouverture prochaine des inscriptions">
                            </div>
                            <div class="w-auto gap-4 flex flex-col mx-4 my-5 sm:m-6 ring-gray-200 mt-0">
                                <label for="compet_banner_message" class="block text-sm font-medium text-gray-900">Message de la bannière</label>
                                <textarea class="rounded-lg shadow ring-1 ring-gray-200 border-none p-4 text-black text-sm" name="compet_banner_message" id="compet_banner_message" placeholder="Entrer votre message">Les inscriptions ouvrirons le vendredi 19 juillet  à partir de 18h</textarea>
                            </div>
                            <div class="flex justify-end gap-4 px-6 pb-4">
                                <button type="submit" class="text-white bg-black hover:shadow-lg font-normal text-xs py-2 px-4 rounded-lg">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>