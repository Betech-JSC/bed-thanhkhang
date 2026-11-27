<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
                @click="close"
                role="dialog"
                aria-modal="true"
            >
                    <div
                        @click.stop
                        class="relative w-full max-w-[480px] bg-[#30374F] border border-[#4A5578] rounded-xl p-6 sm:p-7 shadow-2xl flex flex-col gap-5 text-white"
                    >
                        <!-- Nút Đóng X -->
                        <button
                            type="button"
                            @click="close"
                            class="absolute top-5 right-5 w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white flex items-center justify-center transition-colors focus:outline-none"
                            aria-label="Đóng bảng tra cứu"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- TIÊU ĐỀ THÔNG SỐ (FIGMA 2390:5389) -->
                        <div class="flex items-center gap-3 pr-8">
                            <div class="w-6 h-6 flex items-center justify-center shrink-0 text-[#84ADFF]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                                </svg>
                            </div>
                            <h3 class="text-[#FAFAFA] font-semibold text-xl tracking-tight font-sans">
                                {{ title || tt('Thông số kỹ thuật') }}
                            </h3>
                        </div>

                        <!-- BẢNG THÔNG SỐ KỸ THUẬT (FIGMA 2390:5393) -->
                        <div v-if="allSpecs.length > 0" class="divide-y divide-[#4A5578]">
                            <div
                                v-for="(spec, index) in allSpecs"
                                :key="index"
                                class="flex items-center justify-between py-2.5 text-sm"
                            >
                                <span class="text-[#B9C0D4] font-normal font-sans">{{ spec.label }}</span>
                                <span class="text-[#FAFAFA] font-medium font-sans text-right">{{ spec.value }}</span>
                            </div>
                        </div>

                        <!-- THÔNG TIN BỔ SUNG NÂNG CAO -->
                        <div
                            v-if="extraSpecList.length > 0"
                            class="bg-[#1E2B45] rounded-lg p-3 text-xs space-y-1.5 border border-[#4A5578]/50"
                        >
                            <div
                                v-for="(item, index) in extraSpecList"
                                :key="index"
                                class="flex justify-between gap-3 text-slate-300"
                            >
                                <span>{{ item.label }}</span>
                                <span class="text-white font-medium text-right">{{ item.value }}</span>
                            </div>
                        </div>

                        <!-- LƯU Ý KỸ THUẬT (FIGMA 2390:5415) -->
                        <div v-if="note" class="text-xs leading-relaxed text-[#B9C0D4] font-sans">
                            {{ note }}
                        </div>

                        <!-- NÚT HÀNH ĐỘNG -->
                        <div class="flex items-center gap-3 pt-2">
                            <a
                                :href="contactHref"
                                @click="handleConsult"
                                class="flex-1 py-2.5 px-4 rounded-lg bg-gradient-to-r from-[#2970FF] to-[#00359E] hover:opacity-95 text-white text-xs font-semibold text-center transition-opacity shadow-md"
                            >
                                {{ tt('Tư vấn thông số riêng') }}
                            </a>
                            <button
                                type="button"
                                @click="close"
                                class="py-2.5 px-4 rounded-lg border border-[#4A5578] hover:bg-white/5 text-[#B9C0D4] hover:text-white text-xs font-medium transition-colors"
                            >
                                {{ tt('Đóng') }}
                            </button>
                        </div>
                    </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script>
export default {
    name: 'SpecModal',
    props: {
        isOpen: {
            type: Boolean,
            default: false,
        },
        title: {
            type: String,
            default: '',
        },
        customSpecs: {
            type: Array,
            default: () => [],
        },
        extraSpecs: {
            type: Array,
            default: () => [],
        },
        note: {
            type: String,
            default: '',
        },
    },
    emits: ['close', 'consult'],
    computed: {
        isEn() {
            return (this.$page?.props?.locale?.current || 'vi') === 'en'
        },
        contactHref() {
            return this.safeRoute('contact', undefined, this.isEn ? '/en/contact' : '/lien-he')
        },
        allSpecs() {
            return Array.isArray(this.customSpecs) ? this.customSpecs : []
        },
        extraSpecList() {
            return Array.isArray(this.extraSpecs)
                ? this.extraSpecs.filter((item) => item && (item.label || item.value))
                : []
        },
    },
    watch: {
        isOpen(val) {
            if (typeof document !== 'undefined') {
                if (val) {
                    document.body.classList.add('overflow-hidden')
                } else {
                    document.body.classList.remove('overflow-hidden')
                }
            }
        },
    },
    mounted() {
        if (typeof window !== 'undefined') {
            window.addEventListener('keydown', this.handleKeyDown)
        }
    },
    beforeUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('keydown', this.handleKeyDown)
            document.body.classList.remove('overflow-hidden')
        }
    },
    methods: {
        safeRoute(name, params, fallback = '/') {
            if (typeof this.route === 'function' && typeof this.routes === 'function') {
                const allRoutes = this.routes() || {}
                const localizedName =
                    typeof this.routeLocaleName === 'function' ? this.routeLocaleName(name) : name
                if (allRoutes[localizedName] || allRoutes[name]) {
                    try {
                        const url = this.route(name, params)
                        if (url && url !== '#') return url
                    } catch (e) {
                        // Fall through to fallback
                    }
                }
            }
            return fallback
        },
        close() {
            this.$emit('close')
        },
        handleKeyDown(e) {
            if (e.key === 'Escape' && this.isOpen) {
                this.close()
            }
        },
        handleConsult() {
            this.close()
            this.$emit('consult')
        },
    },
}
</script>
