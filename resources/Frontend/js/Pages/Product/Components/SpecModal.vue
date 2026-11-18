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
                                Thông số kỹ thuật tham khảo
                            </h3>
                        </div>

                        <!-- BẢNG THÔNG SỐ KỸ THUẬT (FIGMA 2390:5393) -->
                        <div class="divide-y divide-[#4A5578]">
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
                        <div class="bg-[#1E2B45] rounded-lg p-3 text-xs space-y-1.5 border border-[#4A5578]/50">
                            <div class="flex justify-between text-slate-300">
                                <span>Dung sai gia công:</span>
                                <span class="text-white font-medium">±0.05 mm (ISO/DIN)</span>
                            </div>
                            <div class="flex justify-between text-slate-300">
                                <span>Tiêu chuẩn kiểm định:</span>
                                <span class="text-white font-medium">EN 115 / ISO 9001 / CO-CQ</span>
                            </div>
                            <div class="flex justify-between text-slate-300">
                                <span>Xử lý bề mặt:</span>
                                <span class="text-white font-medium">Tôi cao tần / Mạ chống ăn mòn</span>
                            </div>
                        </div>

                        <!-- LƯU Ý KỸ THUẬT (FIGMA 2390:5415) -->
                        <div class="text-xs leading-relaxed text-[#B9C0D4] font-sans">
                            Thông số, thời gian hoàn thiện và chi phí sẽ được xác nhận theo kích thước, vật liệu và số lượng thực tế.
                        </div>

                        <!-- NÚT HÀNH ĐỘNG -->
                        <div class="flex items-center gap-3 pt-2">
                            <a
                                href="#contact-quote"
                                @click="handleConsult"
                                class="flex-1 py-2.5 px-4 rounded-lg bg-gradient-to-r from-[#2970FF] to-[#00359E] hover:opacity-95 text-white text-xs font-semibold text-center transition-opacity shadow-md"
                            >
                                Tư vấn thông số riêng
                            </a>
                            <button
                                type="button"
                                @click="close"
                                class="py-2.5 px-4 rounded-lg border border-[#4A5578] hover:bg-white/5 text-[#B9C0D4] hover:text-white text-xs font-medium transition-colors"
                            >
                                Đóng
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
        customSpecs: {
            type: Array,
            default: () => [],
        },
    },
    emits: ['close', 'consult'],
    computed: {
        allSpecs() {
            if (this.customSpecs && this.customSpecs.length > 0) {
                return this.customSpecs
            }
            return [
                { label: 'Tiêu chuẩn / Công nghệ', value: 'Universal Chains / ISO' },
                { label: 'Vật liệu', value: 'Thép hợp kim 40Cr / Inox 304' },
                { label: 'Bước xích tiêu chuẩn', value: '133.33 mm / 135.47 mm' },
                { label: 'Tải trọng kéo đứt tối thiểu', value: '≥ 70 kN - 130 kN' },
                { label: 'Nhiệt độ vận hành', value: '-10°C đến +80°C' },
                { label: 'Chứng chỉ đi kèm', value: 'Đầy đủ CO / CQ' },
                { label: 'Gia công theo yêu cầu', value: 'OEM / ODM theo bản vẽ' },
            ]
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
