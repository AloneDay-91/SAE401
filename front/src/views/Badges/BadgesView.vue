<script setup>
import Button from "@/components/Button.vue";
</script>
<template>
    <section class="min-h-screen pb-8 border border-l-0 border-r-0 border-gray-200">
        <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="flex items-center justify-between border border-t-0 border-l-0 border-r-0 border-gray-200">
                <div class="sm:flex sm:items-start flex flex-col items-center">
                    <h1 class="text-3xl font-extrabold text-gray-900">Mes succès</h1>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-5">Gérez les succès de votre compte</p>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:mt-0 sm:flex-row sm:items-center">
                    <Button variant="solid" size="small" tag="a" href="/succes">Actualiser</Button>
                    <Button variant="outline" size="small" tag="a" href="/">Retour</Button>
                </div>
            </div>
            <div class="mt-8">
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
                        <div class="max-w-md w-1/5">
                            <img src="@/assets/LOGO.png" alt="">
                        </div>
                        <div class="w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Champion</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Terminer 10 devoirs</p>
                            <div class="flex items-center gap-2 text-xs font-light">
                                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                                    <div class="w-1/2 h-full bg-green-400 rounded-full"></div>
                                </div>
                                <div>
                                    <span>10/20</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
                        <div class="max-w-md w-1/5">
                            <img src="@/assets/LOGO.png" alt="">
                        </div>
                        <div class="w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Champion</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Terminer 10 devoirs</p>
                            <div class="flex items-center gap-2 text-xs font-light">
                                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                                    <div class="w-1/3 h-full bg-green-400 rounded-full"></div>
                                </div>
                                <div>
                                    <span>10/20</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
                        <div class="max-w-md w-1/5">
                            <img src="@/assets/LOGO.png" alt="">
                        </div>
                        <div class="w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Champion</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Terminer 10 devoirs</p>
                            <div class="flex items-center gap-2 text-xs font-light">
                                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                                    <div class="w-1/5 h-full bg-green-400 rounded-full"></div>
                                </div>
                                <div>
                                    <span>10/20</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
                        <div class="max-w-md w-1/5">
                            <img src="@/assets/LOGO.png" alt="">
                        </div>
                        <div class="w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Champion</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Terminer 10 devoirs</p>
                            <div class="flex items-center gap-2 text-xs font-light">
                                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                                    <div class="w-2/3 h-full bg-green-400 rounded-full"></div>
                                </div>
                                <div>
                                    <span>10/20</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:px-6 md:flex md:items-center md:justify-start gap-4 block">
                        <div class="max-w-md w-1/5">
                            <img src="@/assets/LOGO.png" alt="">
                        </div>
                        <div class="w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Champion</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Terminer 10 devoirs</p>
                            <div class="flex items-center gap-2 text-xs font-light">
                                <div class="mt-2 w-full h-2 bg-gray-200 rounded-full">
                                    <div class="w-5/6 h-full bg-green-400 rounded-full"></div>
                                </div>
                                <div>
                                    <span>10/20</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>