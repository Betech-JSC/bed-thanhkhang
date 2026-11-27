<template>
    <div class="bg-[#FAFAFA] text-[#4B5565] overflow-x-clip selection:bg-primary selection:text-white">
        <Head>
            <title v-if="pageTitle">{{ pageTitle }}</title>
            <meta v-if="pageDescription" name="description" :content="pageDescription" />
            <meta v-if="pageTitle" property="og:title" :content="pageTitle" />
            <meta v-if="pageDescription" property="og:description" :content="pageDescription" />
            <meta v-if="pageImage" property="og:image" :content="pageImage" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- Compact Empty State when all 3 CMS sections on Documents page are empty -->
        <section
            v-if="!hasAnySectionContent"
            id="documents-empty-state"
            class="bg-[#00284B] py-10 md:py-14 lg:py-16"
        >
            <div class="app-container-narrow">
                <EmptyState
                    variant="dark"
                    heading-tag="h1"
                    :title="tt('Thư viện tài liệu kỹ thuật đang được cập nhật')"
                    :description="tt('Vui lòng liên hệ đội ngũ kỹ sư Thành Khang để nhận catalogue và data sheet thông số kỹ thuật mới nhất.')"
                    :primary-action-text="tt('Liên hệ kỹ thuật')"
                    :primary-action-href="contactHref"
                />
            </div>
        </section>

        <!-- Figma Frame 1264:49095 — Tài liệu - danh mục (3 Dynamic Sections) -->
        <template v-else>
            <!-- Section 1: Hero Banner (Figma Node 1264:49120 — CMS BANNER_DOCUMENT_HERO) -->
            <DocumentHeroSection :hero-banner="resolvedHeroBanner" />

            <!-- Section 2: Catalogue Category Directory (Figma Node 1264:49131 — DocumentDirectorySection) -->
            <DocumentDirectorySection
                :documents="documents"
                :product-categories="product_categories"
                :categories="categories"
                :documents-library-url="documents_library_url"
            />

            <!-- Section 3: Document Support CTA Banner (Figma Node 1264:49145 — DocumentCtaBanner) -->
            <DocumentCtaBanner :cta-banner="resolvedCtaBanner" />
        </template>
    </div>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
    name: 'DocumentsIndexPage',
    layout: MainLayout,
}
</script>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'
import EmptyState from '@/Components/EmptyState.vue'
import DocumentHeroSection from './Components/DocumentHeroSection.vue'
import DocumentDirectorySection from './Components/DocumentDirectorySection.vue'
import DocumentCtaBanner from './Components/DocumentCtaBanner.vue'

const props = defineProps({
    heroBanner: {
        type: [Object, Array],
        default: null,
    },
    documents: {
        type: Array,
        default: () => [],
    },
    product_categories: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    documents_library_url: {
        type: String,
        default: '',
    },
    ctaBanner: {
        type: [Object, Array],
        default: null,
    },
    seo: {
        type: Object,
        default: null,
    },
})

const instance = getCurrentInstance()
const { isEn, pageProps } = useTranslate()

function safeRoute(name, params, fallback) {
    const proxy = instance?.proxy
    if (proxy && typeof proxy.route === 'function') {
        if (typeof proxy.routes === 'function') {
            const allRoutes = proxy.routes() || {}
            const localizedName =
                typeof proxy.routeLocaleName === 'function' ? proxy.routeLocaleName(name) : name
            if (!allRoutes[localizedName] && !allRoutes[name]) {
                return fallback
            }
        }
        try {
            const url = proxy.route(name, params)
            if (url && url !== '#') return url
        } catch (e) {
            // Fall through to localized fallback
        }
    }
    return fallback
}

const contactHref = computed(() =>
    safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he')
)

function hasNonEmptyStr(val) {
    return typeof val === 'string' && val.trim().length > 0
}

function extractMediaUrl(media) {
    if (!media) return ''
    if (typeof media === 'string') return media.trim()
    if (typeof media === 'object') {
        const url = media.url || media.image?.url || media.mobile_url || media.image_mobile?.url
        return typeof url === 'string' ? url.trim() : ''
    }
    return ''
}

function resolveBannerProp(rawBanner) {
    if (Array.isArray(rawBanner)) {
        return rawBanner.length > 0 ? rawBanner[0] : null
    }
    if (rawBanner && typeof rawBanner === 'object') {
        if (Array.isArray(rawBanner.sliders)) {
            return rawBanner.sliders.length > 0 ? rawBanner.sliders[0] : null
        }
        return rawBanner
    }
    return null
}

const resolvedHeroBanner = computed(() => resolveBannerProp(props.heroBanner))

const resolvedCtaBanner = computed(() => resolveBannerProp(props.ctaBanner))

const hasHeroContent = computed(() => {
    const b = resolvedHeroBanner.value
    if (!b || typeof b !== 'object') return false
    return Boolean(
        hasNonEmptyStr(b.title) ||
            hasNonEmptyStr(b.description) ||
            hasNonEmptyStr(b.subtitle) ||
            extractMediaUrl(b.image) ||
            extractMediaUrl(b.image_mobile)
    )
})

function hasValidNamedItem(list) {
    if (!Array.isArray(list)) return false
    return list.some(
        (item) =>
            item &&
            typeof item === 'object' &&
            (hasNonEmptyStr(item.title) || hasNonEmptyStr(item.name))
    )
}

const hasDirectoryContent = computed(() =>
    Boolean(
        hasValidNamedItem(props.documents) ||
            hasValidNamedItem(props.product_categories) ||
            hasValidNamedItem(props.categories)
    )
)

const hasCtaContent = computed(() => {
    const c = resolvedCtaBanner.value
    if (!c || typeof c !== 'object') return false
    return Boolean(
        hasNonEmptyStr(c.title) ||
            hasNonEmptyStr(c.description) ||
            hasNonEmptyStr(c.primary_cta_text) ||
            hasNonEmptyStr(c.secondary_cta_text) ||
            (hasNonEmptyStr(c.link) && c.link.trim() !== '#') ||
            extractMediaUrl(c.image) ||
            extractMediaUrl(c.image_mobile)
    )
})

const hasAnySectionContent = computed(() =>
    Boolean(hasHeroContent.value || hasDirectoryContent.value || hasCtaContent.value)
)

const resolvedSeo = computed(() => props.seo || pageProps.value?.seo || {})
const globalConfig = computed(() => ({
    ...(pageProps.value?.global || {}),
    ...(pageProps.value?.configs || {}),
}))

const pageTitle = computed(() => {
    return (
        resolvedSeo.value.seo_meta_title ||
        resolvedSeo.value.title ||
        globalConfig.value.seo_meta_title ||
        globalConfig.value.general_site_name ||
        globalConfig.value.general_business_name ||
        ''
    )
})

const pageDescription = computed(() => {
    return (
        resolvedSeo.value.seo_meta_description ||
        resolvedSeo.value.description ||
        globalConfig.value.seo_meta_description ||
        globalConfig.value.general_description ||
        ''
    )
})

const pageImage = computed(() => {
    return (
        resolvedSeo.value.seo_image ||
        resolvedSeo.value.image ||
        globalConfig.value.seo_image ||
        ''
    )
})
</script>
