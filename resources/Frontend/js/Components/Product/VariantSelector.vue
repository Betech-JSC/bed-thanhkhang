<template>
    <!-- LỰA CHỌN THAM KHẢO (FIGMA 2358:3013: 480x132 px, border-t border-[#404968], py-5, gap-4) -->
    <div class="w-full border-t border-[#404968] py-5 flex flex-col gap-4 font-sans">
        <!-- HÀNG 1: VẬT LIỆU GỢI Ý (FIGMA 2358:3014) -->
        <div class="flex items-center justify-between w-full h-5">
            <span class="text-sm text-[#B9C0D4] leading-5">Vật liệu gợi ý</span>
            <span class="text-sm font-medium text-[#FAFAFA] leading-5">{{ currentMaterial }}</span>
        </div>

        <!-- HÀNG 2: MÀU TRONG ẢNH (FIGMA 2358:3017) -->
        <div class="flex items-center justify-between w-full h-5">
            <span class="text-sm text-[#B9C0D4] leading-5">Màu trong ảnh</span>
            <span class="text-sm font-normal text-[#FAFAFA] leading-5">{{ currentColor }}</span>
        </div>

        <!-- HÀNG 3: CÓ THỂ TUỲ CHỈNH (FIGMA 2358:3022) -->
        <div class="flex items-center justify-between w-full h-5">
            <span class="text-sm text-[#B9C0D4] leading-5">Có thể tuỳ chỉnh</span>
            <span class="text-sm font-normal text-[#FAFAFA] leading-5">{{ currentCustomization }}</span>
        </div>
    </div>
</template>

<script>
export default {
    name: 'VariantSelector',
    props: {
        material: {
            type: String,
            default: 'Nhựa PLA · Công nghệ FDM',
        },
        color: {
            type: String,
            default: 'Cam',
        },
        customization: {
            type: String,
            default: 'Màu sắc · Kích thước · Số lượng',
        },
    },
    computed: {
        currentMaterial() {
            return this.material || 'Nhựa PLA · Công nghệ FDM'
        },
        currentColor() {
            return this.color || 'Cam'
        },
        currentCustomization() {
            return this.customization || 'Màu sắc · Kích thước · Số lượng'
        },
    },
}
</script>
