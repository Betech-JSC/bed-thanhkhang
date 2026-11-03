<template>
    <component
        :is="resolvedComponent"
        :type="isButton ? type : undefined"
        :href="resolvedHref"
        :target="isAnchor ? target : undefined"
        :rel="isAnchor && target === '_blank' ? 'noopener noreferrer' : undefined"
        :disabled="isButton ? (disabled || loading) : undefined"
        :aria-disabled="disabled || loading ? 'true' : undefined"
        :class="[
            'inline-flex items-center justify-center font-medium transition-all duration-200 select-none focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 focus:ring-offset-dark-bg',
            variantClasses[variant] || variantClasses.primary,
            sizeClasses[size] || sizeClasses.md,
            block ? 'w-full' : '',
            (disabled || loading) ? 'opacity-60 cursor-not-allowed pointer-events-none' : 'cursor-pointer active:scale-[0.98]'
        ]"
    >
        <!-- Loading Spinner -->
        <svg
            v-if="loading"
            class="animate-spin -ml-0.5 mr-2 h-4 w-4 text-current"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>

        <!-- Leading Icon Slot -->
        <span v-if="$slots.leading || $slots.icon" class="inline-flex items-center shrink-0">
            <slot name="leading">
                <slot name="icon"></slot>
            </slot>
        </span>

        <!-- Default Content Slot -->
        <span class="inline-flex items-center truncate">
            <slot></slot>
        </span>

        <!-- Trailing Icon Slot -->
        <span v-if="$slots.trailing" class="inline-flex items-center shrink-0">
            <slot name="trailing"></slot>
        </span>
    </component>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'

const props = defineProps({
    as: {
        type: String,
        default: null, // 'button' | 'Link' | 'a'
    },
    href: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'button',
    },
    target: {
        type: String,
        default: null,
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (val) => ['primary', 'secondary', 'outline', 'ghost', 'danger'].includes(val),
    },
    size: {
        type: String,
        default: 'md',
        validator: (val) => ['sm', 'md', 'lg'].includes(val),
    },
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    block: {
        type: Boolean,
        default: false,
    },
})

const isExternal = (url) => {
    if (!url) return false
    return (
        url.startsWith('http://') ||
        url.startsWith('https://') ||
        url.startsWith('mailto:') ||
        url.startsWith('tel:') ||
        url.startsWith('//')
    )
}

const resolvedComponent = computed(() => {
    if (props.as === 'Link') return Link
    if (props.as === 'a') return 'a'
    if (props.as === 'button') return 'button'

    if (props.href) {
        if (isExternal(props.href) || props.target === '_blank') {
            return 'a'
        }
        return Link
    }
    return 'button'
})

const isButton = computed(() => resolvedComponent.value === 'button')
const isAnchor = computed(() => resolvedComponent.value === 'a')

const resolvedHref = computed(() => {
    if (isButton.value) return undefined
    if (props.disabled || props.loading) return undefined
    return props.href
})

const variantClasses = {
    primary:
        'bg-primary text-white hover:bg-primary-hover shadow-glow-primary/20 hover:shadow-glow-primary/40 border border-transparent',
    secondary:
        'bg-dark-surface text-white border border-dark-border hover:bg-dark-surface-light hover:border-primary/50',
    outline:
        'bg-transparent text-primary border border-primary hover:bg-primary hover:text-white',
    ghost:
        'bg-transparent text-text-secondary hover:text-white hover:bg-white/5 border border-transparent',
    danger:
        'bg-red-600 text-white hover:bg-red-700 shadow-md shadow-red-900/20 border border-transparent',
}

const sizeClasses = {
    sm: 'text-xs px-3 py-1.5 rounded-lg gap-1.5',
    md: 'text-sm px-4 py-2.5 rounded-xl gap-2',
    lg: 'text-base px-6 py-3.5 rounded-xl gap-2.5 font-semibold',
}
</script>
