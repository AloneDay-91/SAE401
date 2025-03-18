<script setup>
import { ref, computed, onMounted } from 'vue';
import { tv } from 'tailwind-variants';
import { Check, CircleAlert, TriangleAlert, Info, X } from 'lucide-vue-next';

const toast = tv({
    base: "fixed z-50 p-4 px-6 rounded-lg shadow-lg transition-all duration-300 ease-in-out border border-gray-300 flex items-start bg-white",
    variants: {
        type: {
            success: "text-green-400",
            error: "text-red-500",
            warning: "text-yellow-500",
            info: "text-blue-500",
        },
        position: {
            topRight: "top-4 right-4",
            topLeft: "top-4 left-4",
            bottomRight: "bottom-4 right-4",
            bottomLeft: "bottom-4 left-4",
            topCenter: "top-4 left-1/2 transform -translate-x-1/2",
            bottomCenter: "bottom-4 left-1/2 transform -translate-x-1/2",
        },
    },
});

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    position: {
        type: String,
        default: 'topRight',
        validator: (value) => ['topRight', 'topLeft', 'bottomRight', 'bottomLeft'].includes(value)
    },
    message: {
        type: String,
        required: true
    },
    title: {
        type: String,
        default: ''
    },
    duration: {
        type: Number,
        default: 7000
    }
});

const visible = ref(true);

const toastClasses = computed(() => {
    return toast({
        type: props.type,
        position: props.position,
    });
});

// Associer les icônes aux types de notifications
const iconComponent = computed(() => {
    const icons = {
        success: Check,
        error: CircleAlert,
        warning: TriangleAlert,
        info: Info
    };
    return icons[props.type] || null;
});

const closeToast = () => {
    visible.value = false;
};

// Fermeture automatique après la durée spécifiée
onMounted(() => {
    setTimeout(closeToast, props.duration);
});
</script>

<template>
    <Transition name="fade">
        <div v-if="visible" :class="toastClasses">
            <component :is="iconComponent" size="18" class="mr-2" v-if="iconComponent" />
            <div class="flex items-start flex-col">
                <span class="text-sm font-medium text-gray-700">
                    {{ title }}
                </span>
                <span class="text-sm font-light text-gray-500">
                {{ message }}
            </span>
            </div>
            <button @click="closeToast" class="ml-2 text-gray-700"><X size="18" stroke-width="1.5" /></button>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
