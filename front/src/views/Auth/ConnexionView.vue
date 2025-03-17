<script setup>
</script>

<template>
    <div class="bg-white relative flex flex-wrap lg:h-screen lg:items-center">
        <div class="w-full px-4 py-12 sm:px-6 sm:py-16 lg:w-1/2 lg:px-8 lg:py-24">
            <div class="mx-auto max-w-lg text-center">
                <img src="@/assets/LOGO.png" class="mx-auto h-14" alt="CDTE Logo">
            </div>
            <router-view></router-view>
        </div>
        <div class="relative h-64 w-full sm:h-96 lg:h-full lg:w-1/2">
            <img class="absolute inset-0 h-full w-full object-cover object-right" src="@/assets/img_accueil.jpeg" alt="">
        </div>
    </div>
</template>

<style scoped>
</style>
