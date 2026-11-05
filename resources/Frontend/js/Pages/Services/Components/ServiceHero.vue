<template>
    <section v-scroll-reveal class="pt-8 pb-10 sm:pb-12 lg:pb-14">
        <!-- Breadcrumb & Title Section (Figma Node 2352:11827) -->
        <div class="space-y-6 max-w-[1152px]">
            <!-- Breadcrumbs (Figma Node 2367:2157) -->
            <nav class="flex items-center gap-[10px] select-none font-sans" aria-label="Breadcrumb">
                <Link
                    :href="servicesUrl"
                    class="text-[16px] leading-[24px] font-normal text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors inline-flex items-center gap-[10px]"
                >
                    <svg
                        class="w-[18px] h-[18px] shrink-0"
                        viewBox="0 0 18 18"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M14.25 9H3.75M3.75 9L9 14.25M3.75 9L9 3.75"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                    <span>{{ tt('Dịch vụ') }}</span>
                </Link>
                <span class="text-[14px] leading-[17px] text-[#84ADFF]">/</span>
                <span class="text-[14px] leading-[17px] font-normal text-[#84ADFF] truncate max-w-[240px] sm:max-w-none">
                    {{ title ? tt(title) : tt('Thiết kế 3D') }}
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
                class="relative w-full rounded-2xl overflow-hidden p-6 sm:p-8 lg:h-[310px] lg:py-[15px] lg:px-[64px] flex items-center transition-shadow duration-300 hover:shadow-[0_8px_32px_rgba(45,75,226,0.35)]"
                style="background: linear-gradient(180deg, #101121 0%, #2D4BE2 100%)"
            >
                <div class="w-full grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- 3D Render Image (Node 2352:11860 - 280x280px) -->
                    <div class="md:col-span-4 flex items-center justify-center">
                        <div class="relative w-[220px] h-[220px] sm:w-[260px] sm:h-[260px] lg:w-[280px] lg:h-[280px] flex items-center justify-center shrink-0">
                            <img
                                :src="imageSrc"
                                :alt="title ? tt(title) : tt('Thiết kế 3D')"
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
                                v-for="(item, idx) in inputItems"
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
                                v-for="(item, idx) in processItems"
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
import { computed, getCurrentInstance } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
    title: {
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
})

const instance = getCurrentInstance()
const i18n = {
    tt: (text) => (typeof instance?.proxy?.tt === 'function' ? instance.proxy.tt(text) : text),
}
const page = usePage()
const isEn = computed(() => page.props.value?.locale?.current === 'en')

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
        ? 'An idea does not need to be complete to begin. Together with HH Maker, clarify proportions, structure, and details, ensuring the 3D model is not only easy to visualize but also ready for real-world manufacturing.'
        : i18n.tt('Một ý tưởng chưa cần hoàn chỉnh để bắt đầu. Cùng HH Maker làm rõ tỉ lệ, cấu trúc và chi tiết, để mô hình 3D không chỉ dễ hình dung mà còn hướng đến khả năng chế tác thực tế.')
)

const displayHeadline = computed(() => {
    const baseTitle = props.title || i18n.tt('Thiết kế 3D')
    const rawTitle = (
        (typeof instance?.proxy?.tt === 'function' ? instance.proxy.tt(baseTitle) : baseTitle) || ''
    ).trim()
    if (isEn.value) {
        if (!rawTitle) return '3D Design: From sketch to concrete geometry'
        if (rawTitle.includes(':')) return rawTitle
        return `${rawTitle}: From sketch to concrete geometry`
    }
    if (!rawTitle) return i18n.tt('Thiết kế 3D: Từ phác thảo đến hình khối rõ ràng')
    if (rawTitle.includes(':')) return rawTitle
    return `${rawTitle}: ${i18n.tt('Từ phác thảo đến hình khối rõ ràng')}`
})

const imageSrc = computed(() => {
    return props.image || '/assets/images/services/hero-3d-illustration.png'
})

const inputItems = computed(() => [
    i18n.tt('Ý tưởng'),
    i18n.tt('Ảnh tham khảo'),
    i18n.tt('Bản phác thảo'),
])

const processItems = computed(() => [
    i18n.tt('Tỉ lệ & hình khối'),
    i18n.tt('Cấu trúc & chi tiết'),
    i18n.tt('Khả năng chế tác'),
])
</script>
