<template>
    <div class="process-page-root bg-[#111322] min-h-screen text-white selection:bg-primary selection:text-white">
        <!-- SEO Head Metadata -->
        <Head>
            <title>{{ pageTitle }}</title>
            <meta name="description" :content="pageDescription" />
            <meta property="og:title" :content="pageTitle" />
            <meta property="og:description" :content="pageDescription" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- SECTION 1: Hero Banner (Figma Node 2283:6841, 1440x680px) -->
        <ProcessHero :banner="heroBanner" />

        <!-- SECTION 2: Stepper Bar 5 Điểm Quy Trình (Figma Node 2283:6851, 1440x176px) -->
        <ProcessStepper v-if="processSteps.length > 0" :steps="processSteps" />

        <!-- SECTION 3: Chi Tiết 5 Bước Gia Công (Figma Node 2283:6882, 1440x1273px) -->
        <section v-if="processSteps.length > 0" class="relative w-full py-6 lg:py-10">
            <div class="app-container">
                <ProcessStepItem
                    v-for="step in processSteps"
                    :key="step.number"
                    :step="step"
                />
            </div>
        </section>

        <!-- SECTION 4: FAQ Accordion (Figma Node 2334:8455, 1440x621px) -->
        <ProcessFaq :faqs="faqs" />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'
import ProcessHero from './Components/ProcessHero.vue'
import ProcessStepper from './Components/ProcessStepper.vue'
import ProcessStepItem from './Components/ProcessStepItem.vue'
import ProcessFaq from './Components/ProcessFaq.vue'

const props = defineProps({
    heroBanner: {
        type: Object,
        default: () => null,
    },
    steps: {
        type: Array,
        default: () => [],
    },
    processSteps: {
        type: Array,
        default: () => [],
    },
    faqs: {
        type: Array,
        default: () => [],
    },
    seo: {
        type: Object,
        default: () => null,
    },
})

const { translate, currentLocale } = useTranslate()

const pageTitle = computed(() => {
    return (
        props.seo?.title ||
        (currentLocale.value === 'en'
            ? 'Professional Supply & Technical Process - Thành Khang'
            : translate('Quy Trình Cung Ứng & Hỗ Trợ Kỹ Thuật Chuyên Nghiệp - Thành Khang'))
    )
})

const pageDescription = computed(() => {
    return (
        props.seo?.description ||
        (currentLocale.value === 'en'
            ? 'Explore Thành Khang\'s 5-step industrial supply process: From receiving technical specs, OEM matching, transparent quotation, CO/CQ quality inspection to on-site delivery.'
            : translate('Khám phá quy trình 5 bước cung ứng thiết bị & phụ tùng công nghiệp tại Thành Khang: Từ tiếp nhận thông số, đối chiếu mã OEM, báo giá, kiểm định CO/CQ đến giao hàng tận nơi.'))
    )
})

const processSteps = computed(() => {
    if (Array.isArray(props.processSteps) && props.processSteps.length) {
        return props.processSteps
    }
    if (Array.isArray(props.steps) && props.steps.length) {
        return props.steps
    }
    return []
})
</script>

<style>
html:has(.process-page-root)::-webkit-scrollbar,
body:has(.process-page-root)::-webkit-scrollbar {
    display: none;
}
html:has(.process-page-root),
body:has(.process-page-root) {
    scrollbar-width: none;
}
@media (min-width: 1024px) {
    body:has(.process-page-root) #footer-section .max-w-\[361px\] {
        padding-top: 0;
    }
    body:has(.process-page-root) #footer-section .max-w-\[361px\] p {
        line-height: 22px;
    }
}
</style>
