<template>
    <!-- Center Aligned Layout -->
    <div
        v-if="align === 'center'"
        class="flex flex-col items-center text-center max-w-3xl mx-auto space-y-3"
    >
        <span
            v-if="subtitle"
            class="text-xs sm:text-sm font-bold text-primary tracking-widest uppercase block"
        >
            {{ subtitle }}
        </span>

        <component
            :is="tag"
            class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-white uppercase tracking-tight leading-tight font-sans"
        >
            {{ title }}
        </component>

        <div
            v-if="highlightBar"
            class="w-16 h-1 bg-primary rounded-full mx-auto my-1"
            aria-hidden="true"
        ></div>

        <p
            v-if="description"
            class="text-text-secondary text-sm sm:text-base font-normal leading-relaxed max-w-2xl"
        >
            {{ description }}
        </p>

        <slot></slot>
    </div>

    <!-- Left Aligned Layout -->
    <div
        v-else
        class="flex flex-col md:flex-row md:items-end justify-between gap-6"
    >
        <div class="space-y-3 max-w-xl">
            <span
                v-if="subtitle"
                class="text-xs sm:text-sm font-bold text-primary tracking-widest uppercase block"
            >
                {{ subtitle }}
            </span>

            <component
                :is="tag"
                class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-white uppercase tracking-tight leading-tight font-sans"
            >
                {{ title }}
            </component>

            <div
                v-if="highlightBar"
                class="w-16 h-1 bg-primary rounded-full mt-1"
                aria-hidden="true"
            ></div>
        </div>

        <div
            v-if="description || $slots.action || $slots.default"
            class="max-w-md flex flex-col justify-end space-y-3"
        >
            <p
                v-if="description"
                class="text-text-secondary text-sm sm:text-base font-normal leading-relaxed"
            >
                {{ description }}
            </p>

            <div v-if="$slots.action" class="pt-1">
                <slot name="action"></slot>
            </div>

            <slot></slot>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    align: {
        type: String,
        default: 'center',
        validator: (val) => ['left', 'center'].includes(val),
    },
    highlightBar: {
        type: Boolean,
        default: true,
    },
    tag: {
        type: String,
        default: 'h2',
    },
})
</script>
