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
        <div class="max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
            <!-- Section 1: Hero & Khung Minh Họa Kỹ Thuật (Figma Node 2367:2156) -->
            <ServiceHero
                :title="serviceData.title"
                :description="serviceData.description"
                :image="heroImage"
            />

            <!-- Section 2: Layout Kỹ Thuật 2 Cột Bất Đối Xứng (Figma Node 2367:2177) -->
            <div class="py-8 sm:py-12 lg:py-16">
                <div class="flex flex-col lg:flex-row lg:items-start gap-8 lg:gap-14 xl:gap-16">
                    <!-- Cột Trái - Sticky Sidebar Mục Lục (TOC - Node 2367:2178 - 248px) -->
                    <ServiceToc
                        :items="tocItems"
                        :active-id="activeSection"
                        @select="handleTocSelect"
                    />

                    <!-- Cột Phải - Nội Dung Bài Viết Kỹ Thuật (Node 2367:2196 - 840px) -->
                    <ServiceArticleContent
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
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'

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
})

// Fallback data if service prop is empty or undefined
const defaultService = {
    id: 1,
    title: 'Thiết kế 3D',
    slug: 'thiet-ke-3d',
    description:
        'Một ý tưởng chưa cần hoàn chỉnh để bắt đầu. Cùng HH Maker làm rõ tỉ lệ, cấu trúc và chi tiết, để mô hình 3D không chỉ dễ hình dung mà còn hướng đến khả năng chế tác thực tế.',
    content: '',
}

const serviceData = computed(() => {
    return {
        ...defaultService,
        ...(props.service || {}),
        title: props.service?.title || defaultService.title,
        slug: props.service?.slug || defaultService.slug,
        description: props.service?.description || defaultService.description,
        content: props.service?.content || '',
    }
})

const pageTitle = computed(() => {
    if (props.seo?.title) return props.seo.title
    return `${serviceData.value.title} - Dịch Vụ HH Maker`
})

const pageDescription = computed(() => {
    if (props.seo?.description) return props.seo.description
    return serviceData.value.description
})

const heroImage = computed(() => {
    if (props.service?.benefit_image?.url) return props.service.benefit_image.url
    if (props.service?.image?.url) return props.service.image.url
    if (serviceData.value.slug === 'gia-cong-3d') return '/assets/images/services/service-gia-cong-3d.png'
    if (serviceData.value.slug === 'lam-do-custom') return '/assets/images/services/service-lam-do-custom.png'
    return '/assets/images/services/hero-3d-illustration.png'
})

// Table of contents configuration
const tocItems = [
    {
        id: 'section-01',
        number: '01',
        title: 'Khi nào cần thiết kế 3D?',
        shortTitle: 'Khi nào cần 3D?',
    },
    {
        id: 'section-02',
        number: '02',
        title: 'Từ ý tưởng tới mô hình',
        shortTitle: 'Quy trình',
    },
    {
        id: 'section-03',
        number: '03',
        title: 'Bạn cần chuẩn bị gì?',
        shortTitle: 'Chuẩn bị gì?',
    },
    {
        id: 'section-04',
        number: '04',
        title: 'Kết quả cùng hoàn thiện',
        shortTitle: 'Hoàn thiện',
    },
]

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

    const sectionIds = tocItems.map((item) => item.id)
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
