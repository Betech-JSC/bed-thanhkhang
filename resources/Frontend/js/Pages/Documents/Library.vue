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

        <!-- Compact Empty State when all CMS sections on Document Library page are empty -->
        <section
            v-if="!hasAnySectionContent"
            id="documents-library-empty-state"
            class="bg-[#00284B] py-10 md:py-14 lg:py-16"
        >
            <div class="app-container-narrow">
                <EmptyState
                    variant="dark"
                    heading-tag="h1"
                    :title="tt('Thư viện tài liệu kỹ thuật đang được cập nhật')"
                    :description="tt('Vui lòng liên hệ đội ngũ kỹ sư Thành Khang Tecsat để nhận catalogue và data sheet thông số kỹ thuật mới nhất.')"
                    :primary-action-text="tt('Liên hệ kỹ thuật')"
                    :primary-action-href="contactHref"
                />
            </div>
        </section>

        <!-- Figma Frame 1264:49166 — Tài liệu - chi tiết -->
        <template v-else>
            <!-- Section 1: Hero Banner (Figma Node 1264:49191 — CMS BANNER_DOCUMENT_HERO) -->
            <DocumentHeroSection :hero-banner="resolvedHeroBanner" />

            <!-- Section 2: Technical Documents List (Figma Node 1264:49202 — DocumentLibrarySection) -->
            <DocumentLibrarySection
                :documents="documents"
                :document-categories="documentCategories"
                :total-documents-count="totalDocumentsCount"
                :pagination="pagination"
                :initial-filters="initialFilters"
                :has-hero="hasHeroContent"
            />

            <!-- Section 3: Document Support CTA Banner (Figma Node 1264:49247 — DocumentCtaBanner) -->
            <DocumentCtaBanner :cta-banner="resolvedCtaBanner" />
        </template>
    </div>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
    name: 'DocumentsLibraryPage',
    layout: MainLayout,
}
</script>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'
import EmptyState from '@/Components/EmptyState.vue'
import DocumentHeroSection from './Components/DocumentHeroSection.vue'
import DocumentLibrarySection from './Components/DocumentLibrarySection.vue'
import DocumentCtaBanner from './Components/DocumentCtaBanner.vue'

const props = defineProps({
    heroBanner: {
        type: [Object, Array],
        default: null,
    },
    ctaBanner: {
        type: [Object, Array],
        default: null,
    },
    documentCategories: {
        type: Array,
        default: () => [],
    },
    totalDocumentsCount: {
        type: Number,
        default: 0,
    },
    documents: {
        type: Array,
        default: () => [],
    },
    pagination: {
        type: Object,
        default: () => ({
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0,
        }),
    },
    initialFilters: {
        type: Object,
        default: () => ({
            category: '',
            search: '',
            page: 1,
        }),
    },
    documents_index_url: {
        type: String,
        default: '',
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

const hasLibraryContent = computed(() =>
    Boolean(
        Number(props.totalDocumentsCount) > 0 ||
            (Array.isArray(props.documents) && props.documents.length > 0) ||
            (Array.isArray(props.documentCategories) && props.documentCategories.length > 0)
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
    Boolean(hasHeroContent.value || hasLibraryContent.value || hasCtaContent.value)
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
