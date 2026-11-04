<template>
    <section class="relative w-full pb-16 lg:pb-24">
        <!-- Container max-width 1152px -->
        <div class="max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
            <div
                class="relative overflow-hidden rounded-2xl p-6 sm:p-8 lg:py-10 lg:px-12 flex flex-col lg:flex-row lg:items-center justify-between gap-6 lg:gap-8 shadow-xl"
                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
            >
                <!-- Decorative background elements -->
                <div
                    class="absolute -right-16 -bottom-16 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"
                ></div>
                <div
                    class="absolute left-1/3 -top-20 w-48 h-48 rounded-full bg-[#84ADFF]/20 blur-xl pointer-events-none"
                ></div>

                <!-- Left Content: Title & Invitation Text (Figma Node 2352:11796) -->
                <div class="relative z-10 max-w-[817px] space-y-3">
                    <h2 class="text-2xl sm:text-3xl lg:text-display-banner font-bold text-[#FAFAFA] leading-tight">
                        {{ tt(title) }}
                    </h2>
                    <p class="text-sm sm:text-base text-white/90 leading-relaxed lg:leading-[26px] font-normal">
                        {{ tt(description) }}
                    </p>
                </div>

                <!-- Right Action: CTA Button (Figma Node 2352:11799) -->
                <div class="relative z-10 shrink-0">
                    <Link
                        :href="contactHref"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-[#F5F8FF] hover:bg-white text-[#111322] font-semibold text-sm sm:text-base transition-all duration-200 shadow-md hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] select-none text-center"
                    >
                        <span>{{ tt(ctaText) }}</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
    banner: {
        type: Object,
        default: () => null,
    },
})

const page = usePage()

const ctaData = computed(() => {
    return (
        props.banner ||
        page.props.value?.globalCtaBanner ||
        page.props.value?.data?.banner?.BANNER_GLOBAL_CTA ||
        null
    )
})

const title = computed(() => ctaData.value?.title || 'Tạo nên phiên bản mang dấu ấn riêng')
const description = computed(() => {
    return (
        ctaData.value?.description ||
        'Gửi hình ảnh tham khảo hoặc chia sẻ ý tưởng để HH Maker cùng bạn định hình sản phẩm mong muốn. Chúng tôi sẽ tư vấn giải pháp phù hợp với phong cách, nhu cầu và ngân sách của bạn.'
    )
})
const ctaText = computed(() => ctaData.value?.primary_cta_text || 'Nhận tư vấn ngay')

const contactHref = computed(() => {
    if (ctaData.value?.primary_cta_link) return ctaData.value.primary_cta_link
    if (ctaData.value?.link) return ctaData.value.link
    try {
        if (typeof route === 'function') {
            return route('contact')
        }
    } catch {
        // Fallback
    }
    return '/lien-he'
})
</script>
