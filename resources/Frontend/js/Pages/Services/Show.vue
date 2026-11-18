<template>
    <div class="bg-[#111322] min-h-screen text-white selection:bg-primary selection:text-white">
        <!-- Head / SEO -->
        <Head>
            <title>{{ pageTitle }}</title>
            <meta name="description" :content="pageDescription" />
            <meta property="og:title" :content="pageTitle" />
            <meta property="og:description" :content="pageDescription" />
            <meta property="og:type" content="article" />
        </Head>

        <!-- Main Container (1152px on Desktop) -->
        <div class="app-container">
            <!-- Section 1: Hero & Khung Minh Họa Kỹ Thuật (Figma Node 2367:2156) -->
            <ServiceHero
                :title="serviceData.title"
                :headline="heroData.headline"
                :description="heroData.description"
                :image="heroData.image || heroImage"
                :input-items="heroData.inputItems"
                :process-items="heroData.processItems"
            />

            <!-- Section 2: Layout Kỹ Thuật 2 Cột Bất Đối Xứng (Figma Node 2367:2177) -->
            <div class="pt-4 pb-8 sm:pb-12 lg:pt-0 lg:pb-16">
                <div class="flex flex-col lg:flex-row lg:items-start gap-8 lg:gap-[64px]">
                    <!-- Cột Trái - Sticky Sidebar Mục Lục (TOC - Node 2367:2178 - 248px) -->
                    <ServiceToc
                        :items="tocItems"
                        :active-id="activeSection"
                        @select="handleTocSelect"
                    />

                    <!-- Cột Phải - Nội Dung Bài Viết Kỹ Thuật (Node 2367:2196 - 840px) -->
                    <ServiceArticleContent
                        :service="serviceData"
                        :sections="sectionsData"
                        :steps="stepsData"
                        :checklist-items="checklistData"
                        :dynamic-html="serviceData.content"
                    />
                </div>
            </div>

            <!-- Section 3: Dịch Vụ Liên Quan (Figma Node 2367:2270) -->
            <ServiceRelated
                :current-slug="serviceData.slug"
                :services="services"
            />

            <!-- Section 4: Banner CTA "Lời mời sáng tạo" (Figma Node 2352:11794) -->
            <ServiceCta
                :service-slug="serviceData.slug"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, getCurrentInstance } from 'vue'
import { Head, usePage } from '@inertiajs/inertia-vue3'

import ServiceHero from './Components/ServiceHero.vue'
import ServiceToc from './Components/ServiceToc.vue'
import ServiceArticleContent from './Components/ServiceArticleContent.vue'
import ServiceRelated from './Components/ServiceRelated.vue'
import ServiceCta from './Components/ServiceCta.vue'

const props = defineProps({
    service: {
        type: Object,
        default: () => ({}),
    },
    services: {
        type: Array,
        default: () => [],
    },
    seo: {
        type: Object,
        default: () => ({}),
    },
    hero: {
        type: Object,
        default: () => ({}),
    },
    toc_items: {
        type: Array,
        default: () => [],
    },
    tocItems: {
        type: Array,
        default: () => [],
    },
    sections: {
        type: Object,
        default: () => ({}),
    },
    steps: {
        type: Array,
        default: () => [],
    },
    checklist_items: {
        type: Array,
        default: () => [],
    },
    checklistItems: {
        type: Array,
        default: () => [],
    },
})

const instance = getCurrentInstance()
const i18n = {
    tt: (text) => (typeof instance?.proxy?.tt === 'function' ? instance.proxy.tt(text) : text),
}
const page = usePage()
const currentLocale = computed(() => page.props.value?.locale?.current || 'vi')

const serviceData = computed(() => {
    return {
        ...(props.service || {}),
        title: props.service?.title || '',
        slug: props.service?.slug || '',
        description: props.service?.description || '',
        content: props.service?.content || '',
    }
})

const pageTitle = computed(() => {
    if (props.seo?.title) return props.seo.title
    const suffix = currentLocale.value === 'en' ? 'Thành Khang Tecsat Services' : i18n.tt('Dịch Vụ Thành Khang Tecsat')
    return `${serviceData.value.title} - ${suffix}`
})

const pageDescription = computed(() => {
    if (props.seo?.description) return props.seo.description
    return serviceData.value.description
})

const heroImage = computed(() => {
    if (props.service?.benefit_image?.url) return props.service.benefit_image.url
    if (props.service?.image?.url) return props.service.image.url
    if (serviceData.value.slug === 'xich-tai-cong-nghiep') return '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp'
    if (serviceData.value.slug === 'quat-cong-nghiep-bldc') return '/images/products/thanhkhang/xich-bai-do-xe/vertical-circulation-photo.webp'
    return '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp'
})

const heroData = computed(() => {
    const fromProps = props.hero && Object.keys(props.hero).length > 0 ? props.hero : null
    const fromService = props.service?.hero && Object.keys(props.service.hero).length > 0 ? props.service.hero : null
    const resolved = fromProps || fromService || {}

    return {
        headline: resolved.headline || serviceData.value.title_content || '',
        description: resolved.description || serviceData.value.description || '',
        image: resolved.image || heroImage.value,
        inputItems: resolved.input_items || resolved.inputItems || [],
        processItems: resolved.process_items || resolved.processItems || [],
    }
})

// Dynamic Table of contents configuration
const tocItems = computed(() => {
    if (Array.isArray(props.toc_items) && props.toc_items.length > 0) return props.toc_items
    if (Array.isArray(props.tocItems) && props.tocItems.length > 0) return props.tocItems
    if (Array.isArray(props.service?.toc_items) && props.service.toc_items.length > 0) return props.service.toc_items
    if (Array.isArray(props.service?.tocItems) && props.service.tocItems.length > 0) return props.service.tocItems

    const slug = serviceData.value.slug
    if (slug === 'xich-tai-cong-nghiep') {
        if (currentLocale.value === 'en') {
            return [
                { id: 'section-01', number: '01', title: 'Application & Operating Conditions', shortTitle: 'Applications' },
                { id: 'section-02', number: '02', title: 'OEM Chain Selection & Matching Process', shortTitle: 'Workflow' },
                { id: 'section-03', number: '03', title: 'Technical Specifications to Prepare', shortTitle: 'Specs to prepare' },
                { id: 'section-04', number: '04', title: 'CO/CQ Inspection & Maintenance Standards', shortTitle: 'QC & Maintenance' },
            ]
        }
        return [
            { id: 'section-01', number: '01', title: i18n.tt('Phạm vi ứng dụng & Tải trọng'), shortTitle: i18n.tt('Ứng dụng') },
            { id: 'section-02', number: '02', title: i18n.tt('Quy trình đối chiếu chuẩn OEM'), shortTitle: i18n.tt('Quy trình') },
            { id: 'section-03', number: '03', title: i18n.tt('Thông số kỹ thuật cần chuẩn bị'), shortTitle: i18n.tt('Thông số cần có') },
            { id: 'section-04', number: '04', title: i18n.tt('Kiểm định CO/CQ & Bảo trì'), shortTitle: i18n.tt('Tiêu chuẩn QC') },
        ]
    }

    if (slug === 'quat-cong-nghiep-bldc') {
        if (currentLocale.value === 'en') {
            return [
                { id: 'section-01', number: '01', title: 'Which Facilities Benefit from BLDC Fans?', shortTitle: 'Who is it for?' },
                { id: 'section-02', number: '02', title: 'Site Survey & Airflow Simulation', shortTitle: 'Survey process' },
                { id: 'section-03', number: '03', title: 'What Facility Data to Prepare?', shortTitle: 'Info to prepare' },
                { id: 'section-04', number: '04', title: 'Installation & Warranty Commitment', shortTitle: 'Installation & care' },
            ]
        }
        return [
            { id: 'section-01', number: '01', title: i18n.tt('Giải pháp quạt BLDC phù hợp công trình nào?'), shortTitle: i18n.tt('Phạm vi công trình') },
            { id: 'section-02', number: '02', title: i18n.tt('Khảo sát & Tối ưu tiết kiệm điện'), shortTitle: i18n.tt('Quy trình khảo sát') },
            { id: 'section-03', number: '03', title: i18n.tt('Bạn chuẩn bị thông tin gì?'), shortTitle: i18n.tt('Thông tin chuẩn bị') },
            { id: 'section-04', number: '04', title: i18n.tt('Lắp đặt & Bảo hành động cơ'), shortTitle: i18n.tt('Lắp đặt & bảo hành') },
        ]
    }

    // Default industrial-engineering / phu-tung-thang-cuon
    if (currentLocale.value === 'en') {
        return [
            { id: 'section-01', number: '01', title: 'When to Replace Escalator & Conveyor Parts?', shortTitle: 'When needed?' },
            { id: 'section-02', number: '02', title: 'From Technical Specs to OEM Delivery', shortTitle: 'Workflow' },
            { id: 'section-03', number: '03', title: 'What Should You Prepare?', shortTitle: 'What to prepare?' },
            { id: 'section-04', number: '04', title: 'Inspection & Handover Standards', shortTitle: 'Handover' },
        ]
    }
    return [
        { id: 'section-01', number: '01', title: i18n.tt('Khi nào cần thay thế phụ tùng & nâng cấp hệ thống?'), shortTitle: i18n.tt('Phạm vi kỹ thuật') },
        { id: 'section-02', number: '02', title: i18n.tt('Từ thông số đến cung ứng chuẩn OEM'), shortTitle: i18n.tt('Quy trình') },
        { id: 'section-03', number: '03', title: i18n.tt('Bạn cần chuẩn bị gì?'), shortTitle: i18n.tt('Chuẩn bị gì?') },
        { id: 'section-04', number: '04', title: i18n.tt('Nghiệm thu & Đồng hành bảo trì'), shortTitle: i18n.tt('Hoàn thiện') },
    ]
})

const sectionsData = computed(() => {
    if (props.sections && Object.keys(props.sections).length > 0) return props.sections
    if (props.service?.sections && Object.keys(props.service.sections).length > 0) return props.service.sections
    return {}
})

const stepsData = computed(() => {
    if (Array.isArray(props.steps) && props.steps.length > 0) return props.steps
    if (Array.isArray(props.service?.steps) && props.service.steps.length > 0) return props.service.steps
    return []
})

const checklistData = computed(() => {
    if (Array.isArray(props.checklist_items) && props.checklist_items.length > 0) return props.checklist_items
    if (Array.isArray(props.checklistItems) && props.checklistItems.length > 0) return props.checklistItems
    if (Array.isArray(props.service?.checklist_items) && props.service.checklist_items.length > 0) return props.service.checklist_items
    if (Array.isArray(props.service?.checklistItems) && props.service.checklistItems.length > 0) return props.service.checklistItems
    return []
})

const activeSection = ref('section-01')
let observer = null

const handleTocSelect = (id) => {
    activeSection.value = id
    if (typeof window === 'undefined') return

    const targetEl = document.getElementById(id)
    if (targetEl) {
        const headerOffset = 110
        const elementPosition = targetEl.getBoundingClientRect().top
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth',
        })
    }
}

onMounted(() => {
    if (typeof window === 'undefined') return

    const sectionIds = ['section-01', 'section-02', 'section-03', 'section-04']
    const options = {
        root: null,
        rootMargin: '-100px 0px -60% 0px',
        threshold: 0,
    }

    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                activeSection.value = entry.target.id
            }
        })
    }, options)

    sectionIds.forEach((id) => {
        const el = document.getElementById(id)
        if (el) observer.observe(el)
    })
})

onBeforeUnmount(() => {
    if (observer) {
        observer.disconnect()
        observer = null
    }
})
</script>
