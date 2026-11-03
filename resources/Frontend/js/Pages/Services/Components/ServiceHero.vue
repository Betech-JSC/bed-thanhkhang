<template>
    <section class="pt-8 pb-10 sm:pb-12 lg:pb-14">
        <!-- Breadcrumb & Title Section (Figma Node 2352:11827) -->
        <div class="space-y-6 max-w-[1152px]">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2.5 text-sm select-none" aria-label="Breadcrumb">
                <Link
                    href="/"
                    class="text-[#B9C0D4] hover:text-white transition-colors"
                >
                    Trang chủ
                </Link>
                <span class="text-[#404968]">/</span>
                <Link
                    :href="servicesUrl"
                    class="text-[#B9C0D4] hover:text-white transition-colors"
                >
                    Dịch vụ
                </Link>
                <span class="text-[#84ADFF]">/</span>
                <span class="text-[#84ADFF] font-medium truncate max-w-[240px] sm:max-w-none">
                    {{ title }}
                </span>
            </nav>

            <!-- Main Heading H1 & Subtitle (Figma Node 2367:2162) -->
            <div class="space-y-3 max-w-[840px]">
                <h1 class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] leading-tight lg:leading-[46px] font-sans">
                    {{ displayHeadline }}
                </h1>
                <p class="text-base sm:text-lg text-[#B9C0D4] leading-relaxed lg:leading-[26px] font-sans">
                    {{ description || defaultDescription }}
                </p>
            </div>
        </div>

        <!-- Khung Minh Họa Bài Viết Nổi Bật (Figma Node 2352:11858 - 1152x310px) -->
        <div class="mt-8 sm:mt-10 max-w-[1152px]">
            <div
                class="relative w-full rounded-2xl overflow-hidden p-6 sm:p-8 lg:p-10 transition-shadow duration-300 hover:shadow-[0_8px_32px_rgba(45,75,226,0.35)]"
                style="background: linear-gradient(180deg, #101121 0%, #2D4BE2 100%)"
            >
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- 3D Render Image (Node 2352:11860 - 280x280px) -->
                    <div class="md:col-span-4 flex items-center justify-center">
                        <div class="relative w-[220px] h-[220px] sm:w-[260px] sm:h-[260px] lg:w-[280px] lg:h-[280px] flex items-center justify-center shrink-0">
                            <img
                                :src="imageSrc"
                                :alt="title"
                                class="w-full h-full object-contain drop-shadow-[0_16px_32px_rgba(0,0,0,0.6)] transition-transform duration-500 hover:scale-105"
                                loading="eager"
                            />
                        </div>
                    </div>

                    <!-- Column 1: BẮT ĐẦU TỪ (Node 2352:11861) -->
                    <div class="md:col-span-4 flex flex-col justify-center space-y-4 md:pl-2">
                        <span class="text-xs sm:text-[12px] font-semibold text-[#D1E0FF] tracking-wider uppercase select-none font-sans leading-[15px]">
                            BẮT ĐẦU TỪ
                        </span>
                        <div class="space-y-2 font-sans">
                            <div
                                v-for="(item, idx) in inputItems"
                                :key="idx"
                                class="text-lg sm:text-[20px] font-normal text-[#FAFAFA] leading-[29px]"
                            >
                                {{ item }}
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: CÙNG LÀM RÕ (Node 2352:11867) -->
                    <div class="md:col-span-4 flex flex-col justify-center space-y-4 md:pl-2">
                        <span class="text-xs sm:text-[12px] font-semibold text-[#D1E0FF] tracking-wider uppercase select-none font-sans leading-[15px]">
                            CÙNG LÀM RÕ
                        </span>
                        <div class="space-y-2 font-sans">
                            <div
                                v-for="(item, idx) in processItems"
                                :key="idx"
                                class="text-lg sm:text-[20px] font-normal text-[#FAFAFA] leading-[29px]"
                            >
                                {{ item }}
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

const props = defineProps({
    title: {
        type: String,
        default: 'Thiết kế 3D',
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

const defaultDescription =
    'Một ý tưởng chưa cần hoàn chỉnh để bắt đầu. Cùng HH Maker làm rõ tỉ lệ, cấu trúc và chi tiết, để mô hình 3D không chỉ dễ hình dung mà còn hướng đến khả năng chế tác thực tế.'

const servicesUrl = computed(() => {
    try {
        if (typeof route === 'function') {
            return route('services.index')
        }
    } catch {
        // Fallback
    }
    return '/dich-vu'
})

const displayHeadline = computed(() => {
    if (!props.title) return 'Thiết kế 3D: Từ phác thảo đến hình khối rõ ràng'
    if (props.title.includes(':')) return props.title
    return `${props.title}: Từ phác thảo đến hình khối rõ ràng`
})

const imageSrc = computed(() => {
    return props.image || '/assets/images/services/hero-3d-illustration.png'
})

const inputItems = [
    'Ý tưởng',
    'Ảnh tham khảo',
    'Bản phác thảo',
]

const processItems = [
    'Tỉ lệ & hình khối',
    'Cấu trúc & chi tiết',
    'Khả năng chế tác',
]
</script>
