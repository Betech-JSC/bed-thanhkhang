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
                            <div class="w-6 h-6 flex items-center justify-center shrink-0">
                                <img
                                    src="/images/pdp/printer.svg"
                                    alt="Printer icon"
                                    class="w-5 h-5 object-contain"
                                />
                            </div>
                            <h3 class="text-[#FAFAFA] font-semibold text-xl tracking-tight font-sans">
                                Thông số in tham khảo
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
                                <span>Dung sai kích thước:</span>
                                <span class="text-white font-medium">±0.15 mm</span>
                            </div>
                            <div class="flex justify-between text-slate-300">
                                <span>Khối lượng in ước tính:</span>
                                <span class="text-white font-medium">~ 42 gam</span>
                            </div>
                            <div class="flex justify-between text-slate-300">
                                <span>Thời gian in ước tính:</span>
                                <span class="text-white font-medium">~ 2 giờ 45 phút</span>
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
                { label: 'Công nghệ in', value: 'FDM' },
                { label: 'Vật liệu', value: 'PLA' },
                { label: 'Kích thước mẫu', value: '60 × 31 × 48 mm' },
                { label: 'Chiều cao lớp in', value: '0,20 mm' },
                { label: 'Đầu phun', value: '0,40 mm' },
                { label: 'Mật độ ruột', value: '15%' },
                { label: 'Vật liệu hỗ trợ', value: 'Không cần' },
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
