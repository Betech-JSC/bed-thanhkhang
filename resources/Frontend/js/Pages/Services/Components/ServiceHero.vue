<template>
    <section v-scroll-reveal class="pt-8 pb-10 sm:pb-12 lg:pb-14">
        <!-- Breadcrumb & Title Section (Figma Node 2352:11827) -->
        <div class="space-y-6 max-w-[1152px]">
            <!-- Breadcrumbs (Figma Node 2367:2157) -->
            <nav class="flex items-center gap-[10px] select-none font-sans" aria-label="Breadcrumb">
                <Link
                    :href="homeUrl"
                    class="text-[16px] leading-[24px] font-normal text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors"
                >
                    {{ tt('Trang chủ') }}
                </Link>
                <span class="text-[14px] leading-[17px] text-[#84ADFF]">/</span>
                <Link
                    :href="servicesUrl"
                    class="text-[16px] leading-[24px] font-normal text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors"
                >
                    {{ tt('Dịch vụ') }}
                </Link>
                <span class="text-[14px] leading-[17px] text-[#84ADFF]">/</span>
                <span class="text-[14px] leading-[17px] font-normal text-[#84ADFF] truncate max-w-[240px] sm:max-w-none">
                    {{ title ? tt(title) : tt('Giải pháp kỹ thuật công nghiệp') }}
                </span>
            </nav>

            <!-- Main Heading H1 & Subtitle (Figma Node 2367:2162) -->
            <div class="space-y-2 max-w-[838px]">
                <h1 class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] leading-tight lg:leading-[46px] font-sans">
                    {{ displayHeadline }}
                </h1>
                <p class="text-[16px] leading-[22px] text-[#B9C0D4] font-sans">
                    {{ tt(description || defaultDescription) }}
                </p>
            </div>
        </div>

        <!-- Khung Minh Họa Bài Viết Nổi Bật (Figma Node 2352:11858 - 1152x310px) -->
        <div class="mt-8 sm:mt-10 max-w-[1152px]">
            <div
                class="relative w-full rounded-2xl overflow-hidden p-6 sm:p-8 lg:h-[310px] lg:py-[15px] lg:px-20 flex items-center transition-shadow duration-300 hover:shadow-[0_8px_32px_rgba(45,75,226,0.35)]"
                style="background: linear-gradient(180deg, #101121 0%, #2D4BE2 100%)"
            >
                <div class="w-full grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- 3D Render Image (Node 2352:11860 - 280x280px) -->
                    <div class="md:col-span-4 flex items-center justify-center">
                        <div class="relative w-[220px] h-[220px] sm:w-[260px] sm:h-[260px] lg:w-[280px] lg:h-[280px] flex items-center justify-center shrink-0">
                            <img
                                :src="imageSrc"
                                :alt="title ? tt(title) : tt('Giải pháp kỹ thuật công nghiệp Thành Khang')"
                                class="w-full h-full object-contain drop-shadow-[0_16px_32px_rgba(0,0,0,0.6)] transition-transform duration-500 hover:scale-105"
                                loading="eager"
                            />
                        </div>
                    </div>

                    <!-- Column 1: BẮT ĐẦU TỪ (Node 2352:11861) -->
                    <div class="md:col-span-4 flex flex-col justify-center space-y-4 lg:space-y-8 md:pl-2">
                        <span class="text-xs sm:text-[12px] font-semibold text-[#D1E0FF] tracking-wider uppercase select-none font-sans leading-[15px]">
                            {{ tt('BẮT ĐẦU TỪ') }}
                        </span>
                        <div class="space-y-2 font-sans">
                            <div
                                v-for="(item, idx) in displayInputItems"
                                :key="idx"
                                class="text-lg sm:text-[20px] font-normal text-[#FAFAFA] leading-[29px]"
                            >
                                {{ tt(item) }}
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: CÙNG LÀM RÕ (Node 2352:11867) -->
                    <div class="md:col-span-4 flex flex-col justify-center space-y-4 lg:space-y-8 md:pl-2">
                        <span class="text-xs sm:text-[12px] font-semibold text-[#D1E0FF] tracking-wider uppercase select-none font-sans leading-[15px]">
                            {{ tt('CÙNG LÀM RÕ') }}
                        </span>
                        <div class="space-y-2 font-sans">
                            <div
                                v-for="(item, idx) in displayProcessItems"
                                :key="idx"
                                class="text-lg sm:text-[20px] font-normal text-[#FAFAFA] leading-[29px]"
                            >
                                {{ tt(item) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    headline: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    image: {
        type: String,
        default: '',
    },
    inputItems: {
        type: Array,
        default: () => [],
    },
    processItems: {
        type: Array,
        default: () => [],
    },
})

const { translate, isEn } = useTranslate()

const homeUrl = computed(() => (isEn.value ? '/en' : '/'))

const servicesUrl = computed(() => {
    try {
        if (typeof route === 'function') {
            const routeName = isEn.value ? 'en.services.index' : 'vi.services.index'
            return route(routeName)
        }
    } catch {}
    return isEn.value ? '/en/services' : '/dich-vu'
})

const defaultDescription = computed(() =>
    isEn.value
        ? 'Together with Thành Khang, verify technical specifications, material durability, and OEM standards so every escalator or conveyor component operates reliably in real-world service.'
        : translate('Cùng Thành Khang đối chiếu chính xác thông số kỹ thuật, vật liệu chịu tải và chuẩn OEM để mọi linh kiện thang cuốn, xích tải vận hành an toàn, bền bỉ.')
)

const displayHeadline = computed(() => {
    if (props.headline && props.headline.trim() !== '') {
        return translate(props.headline.trim())
    }

    const baseTitle = props.title || translate('Giải pháp kỹ thuật công nghiệp')
    const rawTitle = String(translate(baseTitle) || '').trim()

    if (isEn.value) {
        if (!rawTitle) return 'Industrial Equipment & Spare Parts: Precision OEM Standards'
        if (rawTitle.includes(':')) return rawTitle
        return `${rawTitle}: Precision OEM Standards`
    }

    if (!rawTitle) return translate('Giải pháp kỹ thuật công nghiệp: Chuẩn xác từng thông số vận hành')
    if (rawTitle.includes(':')) return rawTitle
    return `${rawTitle}: ${translate('Chuẩn xác từng thông số vận hành')}`
})

const imageSrc = computed(() => {
    return props.image || '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp'
})

const displayInputItems = computed(() => {
    if (Array.isArray(props.inputItems) && props.inputItems.length > 0) {
        return props.inputItems
    }
    return [
        translate('Ý tưởng'),
        translate('Ảnh tham khảo'),
        translate('Bản phác thảo'),
    ]
})

const displayProcessItems = computed(() => {
    if (Array.isArray(props.processItems) && props.processItems.length > 0) {
        return props.processItems
    }
    return [
        translate('Tỉ lệ & hình khối'),
        translate('Cấu trúc & chi tiết'),
        translate('Khả năng chế tác'),
    ]
})
</script>
