<template>
    <!-- LỰA CHỌN THAM KHẢO (FIGMA 2358:3013: 480x132 px, border-t border-[#404968], py-5, gap-4) -->
    <div class="w-full border-t border-[#404968] py-5 flex flex-col gap-4 font-sans">
        <!-- HÀNG 1: VẬT LIỆU GỢI Ý (FIGMA 2358:3014) -->
        <div class="flex items-center justify-between w-full h-5">
            <span class="text-sm text-[#B9C0D4] leading-5">{{ tt('Vật liệu gợi ý') }}</span>
            <span class="text-sm font-medium text-[#FAFAFA] leading-5">{{ tt(currentMaterial) }}</span>
        </div>

        <!-- HÀNG 2: MÀU TRONG ẢNH (FIGMA 2358:3017) -->
        <div class="flex items-center justify-between w-full h-5">
            <span class="text-sm text-[#B9C0D4] leading-5">{{ tt('Màu trong ảnh') }}</span>
            <span class="text-sm font-normal text-[#FAFAFA] leading-5">{{ tt(currentColor) }}</span>
        </div>

        <!-- HÀNG 3: CÓ THỂ TUỲ CHỈNH (FIGMA 2358:3022) -->
        <div class="flex items-center justify-between w-full h-5">
            <span class="text-sm text-[#B9C0D4] leading-5">{{ tt('Có thể tuỳ chỉnh') }}</span>
            <span class="text-sm font-normal text-[#FAFAFA] leading-5">{{ tt(currentCustomization) }}</span>
        </div>
    </div>
</template>

<script>
const tt = (s) => s

export default {
    name: 'VariantSelector',
    props: {
        material: {
            type: String,
            default: tt('Nhựa PLA · Công nghệ FDM'),
        },
        color: {
            type: String,
            default: tt('Cam'),
        },
        customization: {
            type: String,
            default: tt('Màu sắc · Kích thước · Số lượng'),
        },
    },
    computed: {
        currentMaterial() {
            return this.material || this.tt('Nhựa PLA · Công nghệ FDM')
        },
        currentColor() {
            return this.color || this.tt('Cam')
        },
        currentCustomization() {
            return this.customization || this.tt('Màu sắc · Kích thước · Số lượng')
        },
    },
}
</script>
