<template>
    <Teleport to="body" v-if="isMounted">
        <Transition name="base-modal">
            <div
                v-if="isOpen"
                :class="[
                    'fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto',
                    themeClasses.backdrop
                ]"
                @click.self="handleBackdropClick"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="title ? 'modal-title' : undefined"
            >
                <!-- Modal Dialog Box -->
                <div
                    :class="[
                        'base-modal-content relative w-full rounded-2xl shadow-2xl flex flex-col max-h-[90vh] my-auto transition-all duration-200',
                        themeClasses.dialog,
                        maxWidthClasses[maxWidth] || maxWidthClasses.md
                    ]"
                >
                    <!-- Header -->
                    <div
                        v-if="$slots.header || title || showCloseButton"
                        :class="['px-6 py-4.5 border-b flex items-center justify-between shrink-0', themeClasses.header]"
                    >
                        <slot name="header">
                            <h3 v-if="title" id="modal-title" :class="['text-lg font-semibold truncate pr-6', themeClasses.title]">
                                {{ title }}
                            </h3>
                            <div v-else></div>
                        </slot>

                        <!-- Close Button -->
                        <button
                            v-if="showCloseButton"
                            type="button"
                            @click="close"
                            :aria-label="tt('Đóng')"
                            :class="['p-1.5 rounded-lg transition-colors focus:outline-none focus:ring-2', themeClasses.closeBtn]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body Content -->
                    <div :class="['p-6 overflow-y-auto flex-1 text-sm leading-relaxed', themeClasses.body]">
                        <slot name="body">
                            <slot></slot>
                        </slot>
                    </div>

                    <!-- Footer -->
                    <div
                        v-if="$slots.footer"
                        :class="['px-6 py-4 border-t flex items-center justify-end gap-3 shrink-0 rounded-b-2xl', themeClasses.footer]"
                    >
                        <slot name="footer"></slot>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    maxWidth: {
        type: String,
        default: 'md',
        validator: (val) =>
            ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', 'full'].includes(val),
    },
    closeOnEscape: {
        type: Boolean,
        default: true,
    },
    closeOnClickOutside: {
        type: Boolean,
        default: true,
    },
    showCloseButton: {
        type: Boolean,
        default: true,
    },
    variant: {
        type: String,
        default: 'dark',
        validator: (val) => ['dark', 'light'].includes(val),
    },
})

const emit = defineEmits(['update:modelValue', 'close'])

const isMounted = ref(false)

const isOpen = computed(() => props.modelValue || props.show)

const VARIANT_CLASS_MAP = {
    dark: {
        backdrop: 'bg-black/80 backdrop-blur-sm',
        dialog: 'bg-dark-surface border border-dark-border',
        header: 'border-dark-border',
        title: 'text-white',
        closeBtn: 'text-text-muted hover:text-white hover:bg-white/10 focus:ring-primary/50',
        body: 'text-text-secondary',
        footer: 'border-dark-border bg-dark-bg/30',
    },
    light: {
        backdrop: 'bg-[#00284B]/50 backdrop-blur-[2px]',
        dialog: 'bg-white border border-[#E3E8EF]',
        header: 'border-[#E3E8EF]',
        title: 'text-[#00284B]',
        closeBtn: 'text-[#697586] hover:text-[#00284B] hover:bg-[#EEF2F6] focus:ring-[#005EA4]/40',
        body: 'text-[#4B5565]',
        footer: 'border-[#E3E8EF] bg-[#F8FAFC]',
    },
}

const themeClasses = computed(() => VARIANT_CLASS_MAP[props.variant] || VARIANT_CLASS_MAP.dark)

const maxWidthClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    '3xl': 'max-w-3xl',
    '4xl': 'max-w-4xl',
    '5xl': 'max-w-5xl',
    full: 'max-w-[calc(100vw-2rem)]',
}

const close = () => {
    emit('update:modelValue', false)
    emit('close')
}

const handleBackdropClick = () => {
    if (props.closeOnClickOutside) {
        close()
    }
}

const handleKeyDown = (e) => {
    if (e.key === 'Escape' && props.closeOnEscape && isOpen.value) {
        close()
    }
}

// Scroll locking
watch(
    isOpen,
    (open) => {
        if (typeof document === 'undefined') return
        if (open) {
            document.body.style.overflow = 'hidden'
            window.addEventListener('keydown', handleKeyDown)
        } else {
            document.body.style.overflow = ''
            window.removeEventListener('keydown', handleKeyDown)
        }
    },
    { immediate: false }
)

onMounted(() => {
    isMounted.value = true
    if (isOpen.value && typeof document !== 'undefined') {
        document.body.style.overflow = 'hidden'
        window.addEventListener('keydown', handleKeyDown)
    }
})

onUnmounted(() => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = ''
        window.removeEventListener('keydown', handleKeyDown)
    }
})
</script>

<style scoped>
.base-modal-enter-active,
.base-modal-leave-active {
    transition: opacity 0.25s ease;
}

.base-modal-enter-from,
.base-modal-leave-to {
    opacity: 0;
}

.base-modal-enter-active .base-modal-content,
.base-modal-leave-active .base-modal-content {
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
}

.base-modal-enter-from .base-modal-content,
.base-modal-leave-to .base-modal-content {
    opacity: 0;
    transform: scale(0.96) translateY(6px);
}

.base-modal-enter-to .base-modal-content,
.base-modal-leave-from .base-modal-content {
    opacity: 1;
    transform: scale(1) translateY(0);
}
</style>
