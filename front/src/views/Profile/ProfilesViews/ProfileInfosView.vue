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
  <div>
  <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200">
    <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-between block">
      <div class="md:mb-10">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Informations personnelles</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Gérez vos informations personnelles et vos paramètres de connexion.</p>
      </div>
    </div>
    <div class="border-t border-gray-200 md:flex block">
      <div class="w-full">
        <!-- photo de profil + input de changement de photo -->
        <div>
            <div class="sm:gap-4 px-5 py-5 flex flex-col">
            <label class="text-sm font-medium text-gray-500">Photo de profil</label>
            <div class="mt-1 text-sm text-gray-900 sm:col-span-2 flex flex-col items-center m-auto">
              <img class="w-full rounded-lg" :src="`https://api.dicebear.com/9.x/dylan/svg?seed=${user.nom || 'User'}`" alt="">
            </div>
          </div>
        </div>
      </div>
      <div class="w-full">
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 px-5 py-5">
          <div class="text-sm font-normal text-gray-500">Nom</div>
          <div class="mt-1 text-sm text-gray-900 sm:col-span-2">
            {{ user.nom }}
          </div>
        </div>
        <div class="border-t border-gray-200"></div>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 px-5 py-5">
          <div class="text-sm font-normal text-gray-500">Prénom</div>
          <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ user.prenom }}</div>
        </div>
        <div class="border-t border-gray-200"></div>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 px-5 py-5">
          <div class="text-sm font-normal text-gray-500">Adresse email</div>
          <div class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ user.email }}</div>
        </div>
        <div class="border-t border-gray-200"></div>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 px-5 py-5">
          <div class="text-sm font-normal text-gray-500">Rôles</div>
          <div class="mt-1 text-sm text-gray-900 sm:col-span-2">
            <span :class="CouleurRoles(getRoleLabel(user))" class="h-4 rounded-lg w-auto py-1 px-2 ">{{ getRoleLabel(user) }}</span>
          </div>
        </div>
        <div class="border-t border-gray-200"></div>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 px-5 py-5">
          <div class="text-sm font-normal text-gray-500">Mot de passe</div>
          <div class="mt-1 text-sm text-gray-900 sm:col-span-2">
            <a href="/mot-de-passe-oublie" class="mt-1 max-w-2xl text-sm text-gray-500 hover:underline transition duration-200">Changer de mot de passe</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>