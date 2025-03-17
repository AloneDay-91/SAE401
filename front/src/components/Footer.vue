<script setup>
import {Github} from "lucide-vue-next";
import {useRoute} from "vue-router";
import Button from "@/components/Button.vue";
const route = useRoute();
</script>

<template>
    <footer v-if="route.path !== '/connexion' && route.path !== '/connexion/inscription'" class="bg-white w-full shadow sm:flex sm:items-center sm:justify-between p-4 sm:p-6 xl:px-8 xl:py-6 antialiased">
        <p class="mb-4 text-sm text-center text-[#00473e] sm:mb-0">
            <span class="text-xs font-light mr-2">© 2025</span>
            <Button class="hover:border-b border-b-green-500 !rounded-none !py-0 !px-0" variant="ghost" size="small" tag="a" href="/connexion/inscription">IUT Troyes</Button>
        </p>
        <div class="flex justify-center items-center space-x-1">
            <Button class="hover:border-b border-b-green-500 !rounded-none !py-0 !px-0 mr-8" variant="ghost" size="small" tag="a" href="/connexion/inscription">Contact</Button>
            <a href="https://github.com/AloneDay-91/SAE401">
                <Github stroke-width="1.5" size="20" color="#475d5b" />
            </a>
        </div>
    </footer>
</template>