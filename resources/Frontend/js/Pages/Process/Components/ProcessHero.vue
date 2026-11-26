<template>
    <section class="relative w-full pt-4 sm:pt-6 lg:pt-0 pb-6 lg:pb-16 overflow-hidden">
        <!-- Hero Card Container (Figma Node 2701:5759, 1279x616px, borderRadius 16px) -->
        <div class="max-w-[1279px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
            <div
                class="relative w-full rounded-2xl overflow-hidden min-h-[500px] sm:min-h-[560px] lg:h-[616px] lg:min-h-[616px] flex flex-col justify-between lg:justify-end p-6 sm:p-10 lg:p-16"
                style="background: linear-gradient(0deg, #111322 10%, #1D4290 57%, #2970FF 94%);"
            >
                <!-- Background Artwork Image (Figma Node 2701:5759 & 2701:5760 Mask Group) -->
                <div class="absolute inset-0 pointer-events-none select-none overflow-hidden rounded-2xl">
                    <img
                        :src="bgImage"
                        :alt="bgImageAlt"
                        class="w-full h-full object-cover object-top lg:object-fill"
                        loading="eager"
                        decoding="async"
                    />
                </div>

                <!-- Text Header Content (Figma Node 2701:5764: 599x224px, left: 64px, bottom: 64px, gap: 16px) -->
                <div class="relative z-10 w-full max-w-[599px] flex flex-col gap-4">
                    <!-- Category (Figma Node 2701:5765: 16px / 22px / Regular / #D1E0FF) -->
                    <p class="text-[#D1E0FF] text-sm sm:text-[16px] font-normal leading-[22px] uppercase select-none tracking-wide">
                        {{ tt(categoryText) }}
                    </p>

                    <!-- Main Headline (Figma Node 2701:5766: 38px / 46px / #FAFAFA / Bold) -->
                    <h1 class="text-[#FAFAFA] text-2xl sm:text-3xl lg:text-[38px] lg:leading-[46px] font-bold uppercase whitespace-pre-line tracking-tight">
                        {{ tt(headlineText) }}
                    </h1>

                    <!-- Description (Figma Node 2701:5767: 16px / 26px / #D1E0FF) -->
                    <p class="text-[#D1E0FF] text-sm sm:text-[16px] leading-relaxed lg:leading-[26px] font-normal max-w-[599px]">
                        {{ tt(descriptionText) }}
                    </p>

                    <!-- Mobile Quick CTA Button -->
                    <div class="pt-2 sm:hidden">
                        <button
                            type="button"
                            @click="handleCtaClick"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-sm font-medium transition-colors shadow-glow-primary-subtle"
                        >
                            <span>{{ tt(ctaText) }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Rotating Badge 360° & Scroll CTA (Figma Node 2701:5768 Frame 237, 114x114px, right: 64px, bottom: 64px) -->
                <div class="relative z-10 self-end sm:self-auto sm:absolute sm:right-8 sm:bottom-8 lg:right-16 lg:bottom-16 mt-6 sm:mt-0">
                    <CircularBadge
                        tag="button"
                        type="button"
                        :title="tt('Cuộn xuống xem 5 bước quy trình')"
                        :aria-label="tt('Cuộn xuống xem 5 bước quy trình')"
                        @click="scrollToSteps"
                    />
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { useTranslate } from '@/composables/useTranslate.js'
import CircularBadge from '@/Components/CircularBadge.vue'

const props = defineProps({
    banner: {
        type: Object,
        default: () => null,
    },
})

const { translate, currentLocale } = useTranslate()

const bgImage = computed(() => {
    return props.banner?.image?.url || '/assets/images/process/process-hero-banner.png'
})

const bgImageAlt = computed(() => {
    return props.banner?.image?.alt || translate('Quy trình làm việc Thành Khang Tecsat - 5 bước chuẩn hóa')
})

const categoryText = computed(() => {
    if (props.banner?.subtitle && !props.banner.subtitle.includes('100%')) {
        return props.banner.subtitle
    }
    return currentLocale.value === 'en' ? 'WORKFLOW PROCESS' : translate('QUY TRÌNH LÀM VIỆC')
})

const headlineText = computed(() => {
    if (
        props.banner?.title &&
        !props.banner.title.toLowerCase().includes('bước trong quy trình') &&
        !props.banner.title.toLowerCase().includes('steps in our professional')
    ) {
        return props.banner.title
    }
    return currentLocale.value === 'en'
        ? 'CLEAR AT EVERY STEP\nMETICULOUS IN EVERY PIECE'
        : translate('RÕ RÀNG TỪNG BƯỚC\nCHỈN CHU TỪNG SẢN PHẨM')
})

const descriptionText = computed(() => {
    if (
        props.banner?.description &&
        !props.banner.description.includes('Khách hàng gửi file 3D') &&
        !props.banner.description.includes('Clients share their 3D files')
    ) {
        return props.banner.description
    }
    return currentLocale.value === 'en'
        ? 'From initial inquiry to final handover, Thành Khang Tecsat works closely with your engineering team to verify specifications, align on OEM standards and lead time. Every milestone is communicated transparently.'
        : translate('Từ lúc tiếp nhận thông số đến khi bàn giao thiết bị, Thành Khang Tecsat luôn đồng hành cùng đội ngũ kỹ thuật của doanh nghiệp để đối chiếu chuẩn OEM, thống nhất báo giá và tiến độ. Mọi giai đoạn đều minh bạch để chủ đầu tư dễ dàng theo dõi.')
})

const ctaText = computed(() => props.banner?.primary_cta_text || translate('Khám phá 5 bước'))
const ctaLink = computed(() => props.banner?.primary_cta_link || props.banner?.link || null)

const scrollToSteps = () => {
    if (typeof document === 'undefined') return
    const target = document.getElementById('process-steps')
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
}

const handleCtaClick = () => {
    if (ctaLink.value && !ctaLink.value.startsWith('#')) {
        window.location.href = ctaLink.value
        return
    }
    scrollToSteps()
}
</script>
