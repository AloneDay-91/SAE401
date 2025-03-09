<script setup>
import { ref } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const user = ref(store.state.user)

const getRoleLabel = (user) => {
    if (!user.roles || user.roles.length === 0) return 'Inconnu';

    if (user.roles.includes('ROLE_ADMIN')) return 'Administrateur';
    if (user.roles.includes('ROLE_USER')) return 'Utilisateur';
    if (user.roles.includes('ROLE_PROFESSEUR')) return 'Professeur';
    if (user.roles.includes('ROLE_ELEVE')) return 'Étudiant';

    return 'Inconnu';
};

// Fonction corrigée pour utiliser le libellé du rôle
const CouleurRoles = (roleLabel) => {
    if (roleLabel === 'Administrateur') return 'bg-purple-200/50 text-purple-500/70';
    if (roleLabel === 'Professeur') return 'bg-green-200/50 text-green-500/70';
    if (roleLabel === 'Étudiant') return 'bg-blue-200/50 text-blue-500/70';
    return 'bg-gray-200/50 text-gray-500';
};

</script>

<template>
    <section class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200">
        <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="sm:flex sm:items-start flex flex-col items-center border border-t-0 border-l-0 border-r-0 border-gray-200">
                <h1 class="text-3xl font-extrabold text-gray-900">Mon compte</h1>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Gérez les paramètres de votre compte</p>
            </div>
            <div class="mt-8">
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-between block">
                        <div class="md:mb-10">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Informations personnelles</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Gérez vos informations personnelles et vos paramètres de connexion.</p>
                        </div>
                        <div class="flex items-center gap-1.5">
<!--                            <a href="" class="focus:outline-none disabled:cursor-not-allowed disabled:opacity-75 rounded-md md:text-sm text-xs md:px-5 px-2 py-1 md:py-3 shadow-sm font-normal text-white bg-black transition duration-200">Modifier mes informations</a>-->
                        </div>
                    </div>
                    <div class="border-t border-gray-200 md:flex block">
                        <div class="w-full">
                            <!-- photo de profil + input de changement de photo -->
                            <div>
                                <div class="sm:gap-4 sm:px-6 sm:py-5 flex flex-col">
                                    <label class="text-sm font-medium text-gray-500">Photo de profil</label>
                                    <div class="mt-1 text-sm text-gray-900 sm:col-span-2 flex flex-col items-center m-auto">
                                        <img class="w-full rounded-lg" :src="`https://api.dicebear.com/9.x/dylan/svg?seed=${user.nom || 'User'}`" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                                <div class="text-sm font-normal text-gray-500">Nom</div>
                                <div class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                {{ user.nom }}
                                </div>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                                <div class="text-sm font-normal text-gray-500">Prénom</div>
                                <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ user.prenom }}</div>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                                <div class="text-sm font-normal text-gray-500">Adresse email</div>
                                <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ user.email }}</div>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                                <div class="text-sm font-normal text-gray-500">Rôles</div>
                                <div class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                    <span :class="CouleurRoles(getRoleLabel(user))" class="h-4 rounded-lg w-auto py-1 px-2 ">{{ getRoleLabel(user) }}</span>
                                </div>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                                <div class="text-sm font-normal text-gray-500">Mot de passe</div>
                                <div class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                    <a href="/mot-de-passe-oublie" class="mt-1 max-w-2xl text-sm text-gray-500 hover:underline transition duration-200">Changer de mot de passe</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
            <div class="mt-8 md:flex md:justify-between block w-full gap-4">
                <div class="w-full">
                    <div class="px-4 py-5 sm:px-6 items-center">
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Informations sur la classe</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Gérez les informations de la classe</p>
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
                            <div class="border-t border-gray-200"></div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                                <div class="text-sm font-normal text-gray-500">TD</div>
                                <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ user.id_classes.td }}</div>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                                <div class="text-sm font-normal text-gray-500">TP</div>
                                <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ user.id_classes.tp }}</div>
                            </div>
                        </div>
                    </form>
<!--                    <div>
                   <div v-if="user.roles === "ROLE_ADMIN"><a class="mt-6 hover:cursor-pointer disabled:cursor-not-allowed disabled:opacity-75 focus:outline-none border-0 inline-flex items-center text-center cursor-default rounded-md font-normal text-sm gap-x-1.5 px-5 py-3 shadow-sm bg-gray-50 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-500" href="modifClub?club=2"><span class="text-sm font-medium">Modifier les informations</span></a></div>                </div>-->
                </div>
            </div>
        </div>
    </section>
</template>