<template>
    <div class="bg-surface text-ink-body overflow-x-clip selection:bg-primary selection:text-white">
        <Head>
            <title v-if="pageTitle">{{ pageTitle }}</title>
            <meta v-if="pageDescription" name="description" :content="pageDescription" />
            <meta v-if="pageTitle" property="og:title" :content="pageTitle" />
            <meta v-if="pageDescription" property="og:description" :content="pageDescription" />
            <meta v-if="pageImage" property="og:image" :content="pageImage" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- Compact Empty State when all 5 CMS sections on About page are empty -->
        <section
            v-if="!hasAnySectionContent"
            id="about-empty-state"
            class="bg-[#00284B] py-10 md:py-14 lg:py-16"
        >
            <div class="app-container-narrow">
                <EmptyState
                    variant="dark"
                    heading-tag="h1"
                    :title="tt('Nội dung giới thiệu đang được cập nhật')"
                    :description="tt('Quý khách vui lòng quay lại sau hoặc liên hệ trực tiếp để được đội ngũ kỹ thuật hỗ trợ thông tin chi tiết.')"
                    :primary-action-text="tt('Liên hệ với chúng tôi')"
                    :primary-action-href="contactHref"
                />
            </div>
        </section>

        <!-- Figma Frame 1171:41894 — About Us Thành Khang (5 Dynamic CMS Sections) -->
        <template v-else>
            <!-- Section 1: Hero Banner (Figma Node 1171:41895 — CMS BANNER_ABOUT_HERO) -->
            <AboutHeroSection :hero-banner="heroBanner" />

            <!-- Section 2: Câu chuyện Thành Khang (Figma Node 1171:41928 — CMS storySection) -->
            <AboutStorySection :story-section="storySection" />

            <!-- Section 3: Sứ mệnh và Tầm nhìn (Figma Node 1171:41945 — CMS missionVisionSection) -->
            <AboutMissionVisionSection :mission-vision-section="missionVisionSection" />

            <!-- Section 4: Dấu ấn và Cam kết (Figma Node 1171:41956 — CMS statsItems) -->
            <AboutStatsSection :stats-items="statsItems" />

            <!-- Section 5: Đồng hành cùng khách hàng (Figma Node 1171:41961 — CMS companionSection) -->
            <AboutCompanionSection :companion-section="companionSection" />
        </template>
    </div>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
    name: 'AboutPage',
    layout: MainLayout,
}
</script>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'
import EmptyState from '@/Components/EmptyState.vue'
import AboutHeroSection from './Components/AboutHeroSection.vue'
import AboutStorySection from './Components/AboutStorySection.vue'
import AboutMissionVisionSection from './Components/AboutMissionVisionSection.vue'
import AboutStatsSection from './Components/AboutStatsSection.vue'
import AboutCompanionSection from './Components/AboutCompanionSection.vue'

const props = defineProps({
    heroBanner: {
        type: Object,
        default: null,
    },
    storySection: {
        type: Object,
        default: null,
    },
    missionVisionSection: {
        type: Object,
        default: null,
    },
    statsItems: {
        type: Array,
        default: () => [],
    },
    companionSection: {
        type: Object,
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

const hasHeroContent = computed(() => {
    const b = props.heroBanner
    if (!b || typeof b !== 'object') return false
    return Boolean(
        hasNonEmptyStr(b.subtitle) ||
            hasNonEmptyStr(b.title) ||
            hasNonEmptyStr(b.description) ||
            extractMediaUrl(b.image) ||
            extractMediaUrl(b.image_mobile)
    )
})

const hasStoryContent = computed(() => {
    const s = props.storySection
    if (!s || typeof s !== 'object') return false
    const hasParagraphs = Array.isArray(s.paragraphs)
        ? s.paragraphs.some((p) => hasNonEmptyStr(p))
        : [s.paragraph_1, s.paragraph_2, s.paragraph_3].some((p) => hasNonEmptyStr(p))
    const hasImages =
        Array.isArray(s.images) &&
        s.images.some(
            (img) =>
                img &&
                typeof img === 'object' &&
                Boolean(
                    hasNonEmptyStr(img.url) ||
                        extractMediaUrl(img.image)
                )
        )
    return Boolean(
        hasNonEmptyStr(s.eyebrow) ||
            hasNonEmptyStr(s.heading) ||
            hasParagraphs ||
            hasNonEmptyStr(s.commitment) ||
            hasImages
    )
})

const hasMissionVisionContent = computed(() => {
    const m = props.missionVisionSection
    if (!m || typeof m !== 'object') return false
    const hasBanner = Boolean(extractMediaUrl(m.banner))
    const hasItems =
        Array.isArray(m.items) &&
        m.items.some(
            (item) =>
                item &&
                typeof item === 'object' &&
                Boolean(
                    hasNonEmptyStr(item.subtitle) ||
                        hasNonEmptyStr(item.title) ||
                        hasNonEmptyStr(item.description)
                )
        )
    return Boolean(hasBanner || hasItems)
})

const hasStatsContent = computed(() => {
    if (!Array.isArray(props.statsItems)) return false
    return props.statsItems.some((item) => {
        if (!item || typeof item !== 'object') return false
        const label = String(item.label ?? item.subtitle ?? item.description ?? '').trim()
        const value = String(item.value ?? item.title ?? '').trim()
        return Boolean(label || value)
    })
})

const hasCompanionContent = computed(() => {
    const c = props.companionSection
    if (!c || typeof c !== 'object') return false
    const hasCards =
        Array.isArray(c.cards) &&
        c.cards.some((card) => {
            if (!card || typeof card !== 'object') return false
            const ctaText = String(card.cta_text ?? card.primary_cta_text ?? '').trim()
            const rawLink = String(card.cta_link ?? card.primary_cta_link ?? card.link ?? '').trim()
            const hasCta = Boolean(ctaText && rawLink && rawLink !== '#')
            return Boolean(
                hasNonEmptyStr(card.title) ||
                    hasNonEmptyStr(card.description) ||
                    extractMediaUrl(card.image) ||
                    extractMediaUrl(card.image_mobile) ||
                    hasNonEmptyStr(card.url) ||
                    hasCta
            )
        })
    return Boolean(hasNonEmptyStr(c.eyebrow) || hasNonEmptyStr(c.heading) || hasCards)
})

const hasAnySectionContent = computed(() =>
    Boolean(
        hasHeroContent.value ||
            hasStoryContent.value ||
            hasMissionVisionContent.value ||
            hasStatsContent.value ||
            hasCompanionContent.value
    )
)

const resolvedSeo = computed(() => props.seo || pageProps.value?.seo || {})
const globalConfig = computed(() => pageProps.value?.global || {})

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
