<template>
    <div :data-service-slug="service.slug" class="border-t border-[#404968] py-12 lg:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
            <!-- Illustration Card (Clickable to Detail Page) -->
            <div
                :class="[
                    'w-full max-w-[460px] mx-auto lg:mx-0 lg:max-w-none',
                    reverse
                        ? 'lg:col-span-5 order-1 lg:order-2'
                        : 'lg:col-span-5 order-1 lg:order-1'
                ]"
            >
                <Link
                    :href="detailUrl"
                    class="group relative w-full h-[400px] sm:h-[448px] rounded-[16px] p-6 sm:p-8 lg:p-10 flex flex-col justify-between items-center overflow-hidden transition-all duration-300 hover:shadow-[0_8px_32px_rgba(45,75,226,0.35)] block cursor-pointer"
                    style="background: linear-gradient(180deg, #101121 0%, #2D4BE2 100%)"
                >
                    <!-- Top Category Code -->
                    <span class="text-sm font-medium text-[#84ADFF] tracking-wider uppercase text-center select-none font-sans">
                        {{ tt(service.category) }}
                    </span>

                    <!-- Center 3D Illustration -->
                    <div class="relative w-[200px] h-[200px] sm:w-[240px] sm:h-[240px] flex items-center justify-center">
                        <img
                            :src="service.image"
                            :alt="service.title"
                            class="w-full h-full object-contain drop-shadow-[0_12px_24px_rgba(0,0,0,0.5)] transition-transform duration-500 group-hover:scale-105"
                            loading="lazy"
                        />
                    </div>

                    <!-- Bottom Tags with Dots -->
                    <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-2.5 text-xs sm:text-[14px] text-[#FAFAFA] font-sans">
                        <template v-for="(tag, tIdx) in service.tags" :key="tIdx">
                            <span class="whitespace-nowrap">{{ tt(tag) }}</span>
                            <span
                                v-if="tIdx < service.tags.length - 1"
                                class="w-1 h-1 rounded-full bg-[#FAFAFA]/70 shrink-0"
                            ></span>
                        </template>
                    </div>
                </Link>
            </div>

            <!-- Service Details -->
            <div
                :class="[
                    'w-full',
                    reverse
                        ? 'lg:col-span-7 order-2 lg:order-1'
                        : 'lg:col-span-7 order-2 lg:order-2'
                ]"
            >
                <div class="flex flex-col space-y-5 lg:space-y-6">
                    <!-- Title & Subtitle (Title linked to Detail) -->
                    <div>
                        <Link :href="detailUrl" class="inline-block group/title">
                            <h2 class="text-2xl sm:text-3xl lg:text-display-hero font-bold text-[#FAFAFA] group-hover/title:text-[#84ADFF] transition-colors font-sans">
                                {{ tt(service.title) }}
                            </h2>
                        </Link>
                        <p class="text-lg sm:text-xl lg:text-[22px] font-normal text-[#FAFAFA] leading-normal lg:leading-[31px] mt-2 font-sans">
                            {{ tt(service.subtitle) }}
                        </p>
                    </div>

                    <!-- Description -->
                    <p class="text-sm sm:text-base text-[#B9C0D4] leading-relaxed lg:leading-[26px] font-sans">
                        {{ tt(service.description) }}
                    </p>

                    <!-- 2-Column Comparison Table (Preparation vs Completion) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8 pt-2 pb-2">
                        <!-- Col 1: Bạn chuẩn bị -->
                        <div class="space-y-2">
                            <h3 class="text-[13px] font-semibold text-[#84ADFF] tracking-wider uppercase font-sans">
                                {{ tt(service.prepTitle || 'BẠN CHUẨN BỊ') }}
                            </h3>
                            <p class="text-sm sm:text-[16px] text-[#B9C0D4] leading-relaxed lg:leading-[26px] font-sans">
                                {{ tt(service.preparation) }}
                            </p>
                        </div>

                        <!-- Col 2: Cùng hoàn thiện -->
                        <div class="space-y-2">
                            <h3 class="text-[13px] font-semibold text-[#84ADFF] tracking-wider uppercase font-sans">
                                {{ tt(service.compTitle || 'CÙNG HOÀN THIỆN') }}
                            </h3>
                            <p class="text-sm sm:text-[16px] text-[#B9C0D4] leading-relaxed lg:leading-[26px] font-sans">
                                {{ tt(service.completion) }}
                            </p>
                        </div>
                    </div>

                    <!-- CTA Actions (Primary Contact + Secondary Detail Link) -->
                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <Link
                            :href="contactHref"
                            class="inline-flex items-center justify-center h-10 px-5 sm:px-6 rounded-[8px] bg-[#F5F8FF] hover:bg-white text-[#111322] font-medium text-sm sm:text-[16px] leading-6 transition-all duration-200 shadow-sm hover:shadow-[0_0_16px_rgba(255,255,255,0.3)] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary/50 font-sans"
                        >
                            {{ tt(service.ctaText) }}
                        </Link>
                        <Link
                            :href="detailUrl"
                            class="inline-flex items-center justify-center h-10 px-4 sm:px-5 rounded-[8px] border border-[#404968] hover:border-[#2970FF] text-[#84ADFF] hover:text-white font-medium text-sm sm:text-[16px] leading-6 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#2970FF]/50 font-sans group/detail"
                        >
                            <span>{{ tt('Tìm hiểu chi tiết') }}</span>
                            <svg
                                class="w-4 h-4 ml-1.5 transition-transform duration-200 group-hover/detail:translate-x-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
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
})

const page = usePage()

const detailUrl = computed(() => {
    const slugMap = {
        'Gia công 3D': 'gia-cong-3d',
        'Thiết kế 3D': 'thiet-ke-3d',
        'Custom': 'lam-do-custom',
        'Làm đồ custom': 'lam-do-custom',
        'Làm đồ Custom': 'lam-do-custom',
        '3D Design': 'thiet-ke-3d',
        '3D Machining & Printing': 'gia-cong-3d',
        'Custom Bespoke Creations': 'lam-do-custom',
    }
    const slug = props.service.slug || slugMap[props.service.title] || (
        props.service.serviceKey === 'print' ? 'gia-cong-3d' :
        props.service.serviceKey === 'custom' ? 'lam-do-custom' :
        'thiet-ke-3d'
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
