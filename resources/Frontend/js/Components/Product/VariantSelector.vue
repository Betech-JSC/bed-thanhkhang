<template>
    <!-- LỰA CHỌN THAM KHẢO (FIGMA 2358:3013: 480x132 px, border-t border-[#404968], py-5, gap-4) -->
    <div
        v-if="hasAnyVariant"
        class="w-full border-t border-[#404968] py-5 flex flex-col gap-4 font-sans"
    >
        <!-- HÀNG 1: VẬT LIỆU GỢI Ý (FIGMA 2358:3014) -->
        <div
            v-if="currentMaterial"
            class="flex items-center justify-between gap-4 w-full min-h-[20px]"
        >
            <span class="text-sm text-[#B9C0D4] leading-5 shrink-0">{{ tt('Vật liệu gợi ý') }}</span>
            <span class="text-sm font-medium text-[#FAFAFA] leading-5 text-right">{{ currentMaterial }}</span>
        </div>

        <!-- HÀNG 2: MÀU TRONG ẢNH (FIGMA 2358:3017) -->
        <div
            v-if="currentColor"
            class="flex items-center justify-between gap-4 w-full min-h-[20px]"
        >
            <span class="text-sm text-[#B9C0D4] leading-5 shrink-0">{{ tt('Màu trong ảnh') }}</span>
            <span class="text-sm font-normal text-[#FAFAFA] leading-5 text-right">{{ currentColor }}</span>
        </div>

        <!-- HÀNG 3: CÓ THỂ TUỲ CHỈNH (FIGMA 2358:3022) -->
        <div
            v-if="currentCustomization"
            class="flex items-center justify-between gap-4 w-full min-h-[20px]"
        >
            <span class="text-sm text-[#B9C0D4] leading-5 shrink-0">{{ tt('Có thể tuỳ chỉnh') }}</span>
            <span class="text-sm font-normal text-[#FAFAFA] leading-5 text-right">{{ currentCustomization }}</span>
        </div>
    </div>
</template>

<script>
export default {
    name: 'VariantSelector',
    props: {
        material: {
            type: String,
            default: '',
        },
        color: {
            type: String,
            default: '',
        },
        customization: {
            type: String,
            default: '',
        },
    },
    computed: {
        currentMaterial() {
            return typeof this.material === 'string' ? this.material.trim() : ''
        },
        currentColor() {
            return typeof this.color === 'string' ? this.color.trim() : ''
        },
        currentCustomization() {
            return typeof this.customization === 'string' ? this.customization.trim() : ''
        },
        hasAnyVariant() {
            return Boolean(this.currentMaterial || this.currentColor || this.currentCustomization)
        },
    },
}
</script>
