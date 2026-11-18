<template>
    <div v-scroll-reveal :data-service-slug="service.slug" class="border-t border-[#404968] py-10 lg:pt-[63px] lg:pb-[64px]">
        <div
            :class="[
                'flex flex-col lg:flex-row items-center gap-8 lg:gap-[64px]',
                reverse ? 'lg:flex-row-reverse' : ''
            ]"
        >
            <!-- Illustration Card (Clickable to Detail Page - Figma 460x448) -->
            <div class="w-full max-w-[460px] lg:w-[460px] lg:h-[448px] shrink-0 mx-auto lg:mx-0">
                <Link
                    :href="detailUrl"
                    class="group relative w-full h-[400px] sm:h-[448px] lg:w-[460px] lg:h-[448px] rounded-[16px] p-6 sm:p-8 lg:py-[40px] lg:px-[52px] flex flex-col justify-between items-center overflow-hidden transition-shadow duration-300 hover:shadow-[0_8px_32px_rgba(45,75,226,0.35)] block cursor-pointer"
                    style="background: linear-gradient(180deg, #101121 0%, #2D4BE2 100%)"
                >
                    <!-- Top Category Code -->
                    <span class="text-[14px] leading-[17px] font-normal text-[#84ADFF] uppercase text-center select-none font-sans">
                        {{ tt(service.category) }}
                    </span>

                    <!-- Center 3D Illustration -->
                    <div class="relative w-[200px] h-[200px] sm:w-[240px] sm:h-[240px] shrink-0 flex items-center justify-center">
                        <img
                            :src="service.image"
                            :alt="tt(service.title)"
                            class="w-full h-full object-contain drop-shadow-[0_12px_24px_rgba(0,0,0,0.5)] transition-transform duration-500 group-hover:scale-105"
                            loading="lazy"
                        />
                    </div>

                    <!-- Bottom Tags with Dots -->
                    <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-[10px] text-xs sm:text-[14px] sm:leading-[20px] font-normal text-[#FAFAFA] font-sans">
                        <template v-for="(tag, tIdx) in service.tags" :key="tIdx">
                            <span class="whitespace-nowrap">{{ tt(tag) }}</span>
                            <span
                                v-if="tIdx < service.tags.length - 1"
                                class="w-1 h-1 rounded-full bg-[#FAFAFA] shrink-0"
                            ></span>
                        </template>
                    </div>
                </Link>
            </div>

            <!-- Service Details (Figma 628x373, gap 20px) -->
            <div class="w-full lg:w-[628px] shrink-0">
                <div class="flex flex-col gap-5">
                    <!-- 1. Title (Linked to Detail) -->
                    <Link :href="detailUrl" class="inline-block group/title self-start">
                        <h2 class="text-2xl sm:text-3xl lg:text-[38px] lg:leading-[44px] font-bold text-[#FAFAFA] tracking-normal group-hover/title:text-[#84ADFF] transition-colors font-sans">
                            {{ tt(service.title) }}
                        </h2>
                    </Link>

                    <!-- 2. Subtitle -->
                    <p class="text-lg sm:text-xl lg:text-[22px] lg:leading-[31px] font-normal text-[#FAFAFA] font-sans">
                        {{ tt(service.subtitle) }}
                    </p>

                    <!-- 3. Description -->
                    <p class="text-sm sm:text-[16px] sm:leading-[26px] font-normal text-[#B9C0D4] font-sans">
                        {{ tt(service.description) }}
                    </p>

                    <!-- 4. 2-Column Comparison Table (Preparation vs Completion - Figma 628x100, py 12px, gap 48px) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-[48px] py-[12px]">
                        <!-- Col 1: Bạn chuẩn bị -->
                        <div class="w-full lg:w-[290px] flex flex-col gap-2">
                            <h3 class="text-[13px] leading-[16px] font-semibold text-[#84ADFF] uppercase font-sans">
                                {{ service.prepTitle ? tt(service.prepTitle) : tt('BẠN CHUẨN BỊ') }}
                            </h3>
                            <p class="text-sm sm:text-[16px] sm:leading-[26px] font-normal text-[#B9C0D4] font-sans">
                                {{ tt(service.preparation) }}
                            </p>
                        </div>

                        <!-- Col 2: Cùng hoàn thiện -->
                        <div class="w-full lg:w-[290px] flex flex-col gap-2">
                            <h3 class="text-[13px] leading-[16px] font-semibold text-[#84ADFF] uppercase font-sans">
                                {{ service.compTitle ? tt(service.compTitle) : tt('CÙNG HOÀN THIỆN') }}
                            </h3>
                            <p class="text-sm sm:text-[16px] sm:leading-[26px] font-normal text-[#B9C0D4] font-sans">
                                {{ tt(service.completion) }}
                            </p>
                        </div>
                    </div>

                    <!-- 5. Primary CTA Button & Secondary Detail Link -->
                    <div class="flex items-center gap-3 sm:gap-4 flex-wrap">
                        <Link
                            :href="contactHref"
                            class="inline-flex items-center justify-center h-[40px] px-[20px] py-[8px] rounded-[8px] bg-[#F5F8FF] hover:bg-white text-[#111322] font-medium text-sm sm:text-[16px] sm:leading-[24px] transition-transform duration-200 shadow-sm hover:shadow-[0_0_16px_rgba(255,255,255,0.3)] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary/50 font-sans"
                        >
                            {{ tt(service.ctaText) }}
                        </Link>
                        <Link
                            :href="detailUrl"
                            class="inline-flex items-center justify-center h-[40px] px-[20px] py-[8px] rounded-[8px] border border-[#84ADFF]/30 hover:border-[#84ADFF] text-[#D1E0FF] hover:text-white font-medium text-sm sm:text-[16px] sm:leading-[24px] transition-all duration-200 hover:bg-[#84ADFF]/10 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-[#84ADFF]/50 font-sans"
                        >
                            {{ tt('Tìm hiểu chi tiết') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
    service: {
        type: Object,
        required: true,
    },
    reverse: {
        type: Boolean,
        default: false,
    },
    href: {
        type: String,
        default: '',
    },
})

const page = usePage()
const key = { tt: (s) => s }

const detailUrl = computed(() => {
    if (props.href) {
        return props.href
    }
    const slugMap = {
        [key.tt('Linh kiện Thang cuốn')]: 'phu-tung-thang-cuon',
        [key.tt('Xích tải & Băng tải')]: 'xich-tai-cong-nghiep',
        [key.tt('Quạt công nghiệp BLDC')]: 'quat-cong-nghiep-bldc',
        [key.tt('Dịch vụ kỹ thuật công nghiệp')]: 'industrial-engineering',
        'Escalator Spare Parts': 'phu-tung-thang-cuon',
        'Industrial Chains & Conveyors': 'xich-tai-cong-nghiep',
        'BLDC Industrial Fans': 'quat-cong-nghiep-bldc',
    }
    const slug = props.service.slug || slugMap[props.service.title] || (
        props.service.serviceKey === 'chains' ? 'xich-tai-cong-nghiep' :
        props.service.serviceKey === 'fans' ? 'quat-cong-nghiep-bldc' :
        'industrial-engineering'
    )
    const isEn = page.props.value?.locale?.current === 'en'
    try {
        if (typeof route === 'function') {
            const routeName = isEn ? 'en.services.show' : 'vi.services.show'
            return route(routeName, { slug })
        }
    } catch {}
    return isEn ? `/en/services/${slug}` : `/dich-vu/${slug}`
})

const contactHref = computed(() => {
    const key = props.service.serviceKey || 'design'
    const isEn = page.props.value?.locale?.current === 'en'
    try {
        if (typeof route === 'function') {
            const routeName = isEn ? 'en.contact' : 'vi.contact'
            return route(routeName, { service: key })
        }
    } catch (e) {}
    return isEn ? `/en/contact?service=${key}` : `/lien-he?service=${key}`
})
</script>
