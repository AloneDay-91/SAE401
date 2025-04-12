<script setup>
import Button from "@/components/Button.vue";
</script>

<template>
    <div class="flex flex-col items-center justify-center h-screen">
        <h1 class="text-7xl font-bold text-green-400">404</h1>
        <p class="mt-2 text-lg text-gray-600">Oops! La page que vous recherchez n'existe pas.</p>
        <p class="mt-2 text-sm text-gray-500">Il se peut qu'elle ait été supprimée ou que l'URL soit incorrecte.</p>
        <Button class="mt-4" variant="solid" size="small" tag="a" href="/">Retour à l'accueil</Button>
    </div>
</template>

<style scoped>

</style>