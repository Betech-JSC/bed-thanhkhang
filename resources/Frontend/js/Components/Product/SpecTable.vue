<template>
    <!-- BẢNG THÔNG SỐ KỸ THUẬT (FIGMA 2358:3066 / 2390:5388: 480x445 px, bg-[#30374F], border-[#30374F], rounded-[12px], p-[28px], gap-[20px]) -->
    <div class="w-full lg:w-[480px] lg:h-[445px] lg:min-h-[445px] bg-[#30374F] border border-[#30374F] rounded-[12px] p-6 sm:p-[28px] gap-[18px] flex flex-col justify-between font-sans">
        <!-- 1. TIÊU ĐỀ THÔNG SỐ (FIGMA 2358:3067: gap 12px) -->
        <div class="flex items-center gap-3">
            <h3 class="text-lg sm:text-xl font-semibold text-[#FAFAFA] leading-6 tracking-tight">
                {{ tt(title) }}
            </h3>
            <img
                src="/images/pdp/printer.svg"
                alt="Printer icon"
                class="w-5 h-5 object-contain shrink-0"
            />
        </div>

        <!-- 2. BẢNG THÔNG SỐ (FIGMA 2358:3071: 7 hàng thông số) -->
        <div class="flex flex-col">
            <div
                v-for="(row, idx) in specList"
                :key="idx"
                class="flex items-center justify-between py-3 border-b border-[#4A5578] last:border-b-0 h-[41px]"
            >
                <span class="text-sm font-normal text-[#DCDFEA] leading-[17px]">{{ row.label }}</span>
                <span class="text-sm font-medium text-[#FAFAFA] leading-[17px] text-right">{{ row.value }}</span>
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
            default: tt('Thông số in tham khảo'),
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
            if (this.specs && this.specs.length > 0) {
                return this.specs.map(s => ({
                    label: this.tt(s.label),
                    value: this.tt(s.value),
                }))
            }
            // Standard specifications strictly from Figma node 2358:3071
            return [
                { label: this.tt('Công nghệ in'), value: 'FDM' },
                { label: this.tt('Vật liệu'), value: 'PLA' },
                { label: this.tt('Kích thước mẫu'), value: '60 × 31 × 48 mm' },
                { label: this.tt('Chiều cao lớp in'), value: '0,20 mm' },
                { label: this.tt('Đầu phun'), value: '0,40 mm' },
                { label: this.tt('Mật độ ruột'), value: '15%' },
                { label: this.tt('Vật liệu hỗ trợ'), value: this.tt('Không cần') },
            ]
        },
    },
}
</script>
