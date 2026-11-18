<template>
    <!-- BẢNG THÔNG SỐ KỸ THUẬT (FIGMA 2358:3066 / 2390:5388: 480x445 px, bg-[#30374F], border-[#30374F], rounded-[12px], p-[28px], gap-[20px]) -->
    <div class="w-full lg:w-[480px] lg:h-[445px] lg:min-h-[445px] bg-[#30374F] border border-[#30374F] rounded-[12px] p-6 sm:p-[28px] gap-[18px] flex flex-col justify-between font-sans">
        <!-- 1. TIÊU ĐỀ THÔNG SỐ (FIGMA 2358:3067: gap 12px) -->
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-[#84ADFF] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
            </svg>
            <h3 class="text-lg sm:text-xl font-semibold text-[#FAFAFA] leading-6 tracking-tight">
                {{ tt(title) }}
            </h3>
        </div>

        <!-- 2. BẢNG THÔNG SỐ (FIGMA 2358:3071: 7 hàng thông số) -->
        <div class="flex flex-col">
            <template v-if="specList.length > 0">
                <div v-for="(row, idx) in specList"
                :key="idx"
                class="flex items-center justify-between py-3 border-b border-[#4A5578] last:border-b-0 h-[41px]"
            >
                <span class="text-sm font-normal text-[#DCDFEA] leading-[17px]">{{ row.label }}</span>
                <span class="text-sm font-medium text-[#FAFAFA] leading-[17px] text-right">{{ row.value }}</span>
            </div>
            </template>
            <div v-else class="py-8 text-center text-sm text-[#B9C0D4]">
                {{ tt('Thông số kỹ thuật đang được cập nhật.') }}
            </div>
        </div>

        <!-- 3. LƯU Ý KỸ THUẬT (FIGMA 2358:3093: text 12px, color #DCDFEA, line-height 19px) -->
        <div>
            <p class="text-xs text-[#DCDFEA] leading-[19px]">
                {{ tt(note) }}
            </p>
        </div>
    </div>
</template>

<script>
const tt = (s) => s

export default {
    name: 'SpecTable',
    props: {
        title: {
            type: String,
            default: tt('Thông số kỹ thuật'),
        },
        specs: {
            type: Array,
            default: () => [],
        },
        note: {
            type: String,
            default: tt('Thông số, thời gian hoàn thiện và chi phí sẽ được xác nhận theo kích thước, vật liệu và số lượng thực tế.'),
        },
    },
    computed: {
        specList() {
            if (this.specs && Array.isArray(this.specs) && this.specs.length > 0) {
                return this.specs.map(s => ({
                    label: this.tt(s.label || s.name || s.key || ''),
                    value: this.tt(s.value || s.val || ''),
                })).filter(s => s.label);
            }
            return [];
        },
    },
}
</script>
