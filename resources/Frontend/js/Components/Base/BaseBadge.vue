<template>
    <span
        :class="[
            'inline-flex items-center font-medium select-none transition-colors',
            pill ? 'rounded-full' : 'rounded-lg',
            sizeClasses[size] || sizeClasses.md,
            variantClasses[variant] || variantClasses.neutral
        ]"
    >
        <!-- Status Dot -->
        <span
            v-if="dot"
            :class="[
                'rounded-full shrink-0',
                size === 'sm' ? 'w-1.5 h-1.5' : 'w-2 h-2',
                dotClasses[variant] || dotClasses.neutral
            ]"
            aria-hidden="true"
        ></span>

        <!-- Icon Slot -->
        <span v-if="$slots.icon" class="inline-flex items-center shrink-0">
            <slot name="icon"></slot>
        </span>

        <!-- Label Slot -->
        <span class="inline-flex items-center truncate">
            <slot></slot>
        </span>
    </span>
</template>

<script setup>
defineProps({
    variant: {
        type: String,
        default: 'neutral',
        validator: (val) =>
            ['primary', 'success', 'warning', 'info', 'neutral', 'danger'].includes(val),
    },
    size: {
        type: String,
        default: 'md',
        validator: (val) => ['sm', 'md'].includes(val),
    },
    dot: {
        type: Boolean,
        default: false,
    },
    pill: {
        type: Boolean,
        default: true,
    },
})

const sizeClasses = {
    sm: 'text-xs px-2 py-0.5 gap-1.5 leading-none',
    md: 'text-xs px-2.5 py-1 gap-1.5 leading-tight',
}

const variantClasses = {
    primary: 'bg-primary/10 text-primary border border-primary/30',
    success: 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
    warning: 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
    info: 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30',
    neutral: 'bg-white/5 text-text-secondary border border-dark-border',
    danger: 'bg-rose-500/10 text-rose-400 border border-rose-500/30',
}

const dotClasses = {
    primary: 'bg-primary shadow-[0_0_6px_rgba(0,82,255,0.6)]',
    success: 'bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.6)]',
    warning: 'bg-amber-400 shadow-[0_0_6px_rgba(251,191,36,0.6)]',
    info: 'bg-cyan-400 shadow-[0_0_6px_rgba(34,211,238,0.6)]',
    neutral: 'bg-text-muted',
    danger: 'bg-rose-400 shadow-[0_0_6px_rgba(251,113,133,0.6)]',
}
</script>
