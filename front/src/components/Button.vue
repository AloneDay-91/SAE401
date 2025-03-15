<script setup>
import { computed } from 'vue';
import { tv } from 'tailwind-variants';

// Définition des variantes de bouton avec tailwind-variants
const button = tv({
    base: "font-light rounded transition-colors duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed",
    variants: {
        variant: {
            solid: "border border-transparent text-black bg-green-400 hover:bg-green-500",
            outline: "bg-transparent border border-gray-300 text-gray-500 border-gray-300 hover:bg-gray-100",
            ghost: "bg-transparent border-transparent hover:bg-opacity-10",
        },
        color: {
            primary: "",
            secondary: "",
            success: "text-white bg-green-500 hover:bg-green-600",
            danger: "text-white bg-red-500 hover:bg-red-600",
            warning: "text-black bg-yellow-500 hover:bg-yellow-600",
        },
        size: {
            small: "text-xs py-1.5 px-3",
            normal: "text-sm py-2 px-4",
            large: "text-base py-3 px-6",
        },
        rounded: {
            none: "rounded-none",
            sm: "rounded-sm",
            md: "rounded-md",
            lg: "rounded-lg",
            full: "rounded-full",
        },
        loading: {
            true: "relative text-transparent pointer-events-none",
        },
    },
    /*compoundVariants: [
        {
            variant: "solid",
            color: "primary",
            class: "border-green-400"
        },
        {
            variant: "outline",
            color: "secondary",
            class: "shadow-sm cursor-pointer"
        },
        {
            variant: "ghost",
            color: "primary",
            class: "text-blue-500 hover:bg-blue-100 focus:ring-blue-400"
        },
        {
            loading: true,
            class: "after:absolute after:inset-0 after:m-auto after:h-4 after:w-4 after:animate-spin after:rounded-full after:border-2 after:border-transparent after:border-t-white"
        },
    ]*/
});

// Définition des props
const props = defineProps({
    variant: {
        type: String,
        default: 'solid',
        validator: (value) => ['solid', 'outline', 'ghost'].includes(value)
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'success', 'danger', 'warning'].includes(value)
    },
    size: {
        type: String,
        default: 'normal',
        validator: (value) => ['small', 'normal', 'large'].includes(value)
    },
    rounded: {
        type: String,
        default: 'md',
        validator: (value) => ['none', 'sm', 'md', 'lg', 'full'].includes(value)
    },
    loading: {
        type: Boolean,
        default: false
    },
    type: {
        type: String,
        default: 'button',
        validator: (value) => ['button', 'submit', 'reset'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    },
    tag: {
        type: String,
        default: 'button'
    }
});

// Définition des émissions
const emit = defineEmits(['click']);

// Gestion du clic sur le bouton
const handleClick = (event) => {
    if (!props.disabled && !props.loading) {
        emit('click', event);
    }
};

// Calcul des classes du bouton
const buttonClasses = computed(() => {
    return button({
        variant: props.variant,
        color: props.color,
        size: props.size,
        rounded: props.rounded,
        loading: props.loading
    });
});
</script>

<template>
    <component
        :is="tag"
        :class="buttonClasses"
        :type="tag === 'button' ? type : undefined"
        :disabled="disabled || loading"
        @click="handleClick"
        v-bind="$attrs"
    >
        <slot></slot>
    </component>
</template>
