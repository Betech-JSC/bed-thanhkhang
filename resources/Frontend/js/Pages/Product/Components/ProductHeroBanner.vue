<template>
    <!-- Section 1: Hero Banner — Sản phẩm (Figma Node 979:30380, 1440×252) -->
    <section
        v-if="hasContent"
        id="product-hero-banner"
        class="relative w-full overflow-hidden bg-[#00284B] aspect-[390/240] md:aspect-[1440/252] min-h-[220px] xl:min-h-0 flex items-center"
    >
        <!-- Background Image (Figma 979:30381 / CMS BANNER_PRODUCT_HERO) -->
        <picture
            v-if="resolvedDesktopUrl"
            class="absolute inset-0 block w-full h-full pointer-events-none select-none"
        >
            <source
                v-if="resolvedMobileUrl && resolvedMobileUrl !== resolvedDesktopUrl"
                media="(max-width: 767px)"
                :srcset="resolvedMobileUrl"
            />
            <img
                :src="resolvedDesktopUrl"
                :alt="imageAlt"
                :aria-hidden="!imageAlt ? 'true' : undefined"
                width="1440"
                height="252"
                loading="eager"
                decoding="async"
                fetchpriority="high"
                class="w-full h-full object-cover object-center select-none"
                @error="handleImageError"
            />
        </picture>

        <!-- Desktop Scrim Overlay (Figma 979:30382: 898/1440 = 62.36%, linear-gradient 270deg) -->
        <div
            class="absolute inset-y-0 left-0 w-full md:w-[62.36%] md:min-w-[680px] bg-[linear-gradient(270deg,rgba(1,16,30,0)_0%,rgba(1,16,30,0.8)_100%)] pointer-events-none"
            aria-hidden="true"
        />

        <!-- Mobile-only WCAG Contrast Scrim -->
        <div
            class="absolute inset-0 bg-[rgba(1,16,30,0.52)] md:hidden pointer-events-none"
            aria-hidden="true"
        />

        <!-- Foreground Content Container (Figma 979:30383: left 144px via .app-container-narrow, max-w 400px) -->
        <div class="app-container-narrow relative z-10 py-6 md:py-0">
            <div class="flex flex-col items-start gap-4 md:gap-6 max-w-[400px]">
                <!-- Breadcrumb (Figma 979:30384) -->
                <nav
                    :aria-label="tt('Breadcrumb')"
                    class="flex items-center gap-2 text-[14px] leading-[22px] font-sans"
                >
                    <Link
                        :href="homeHref"
                        class="text-[#CDD5DF] hover:text-[#FAFAFA] transition-colors focus:outline-none focus-visible:underline"
                    >
                        {{ tt('Trang chủ') }}
                    </Link>
                    <span class="text-[#CDD5DF] select-none" aria-hidden="true">/</span>
                    <span class="font-medium text-[#FAFAFA]" aria-current="page">
                        {{ breadcrumbLabel }}
                    </span>
                </nav>

                <!-- Main H1 & Short Description (Figma 979:30388) -->
                <div
                    v-if="heroTitle || heroDescription"
                    class="flex flex-col items-start gap-2"
                >
                    <h1
                        v-if="heroTitle"
                        class="!text-[28px] !leading-[38px] md:!text-[36px] md:!leading-[50px] font-bold text-[#FAFAFA] uppercase font-sans tracking-normal"
                    >
                        {{ heroTitle }}
                    </h1>
                    <p
                        v-if="heroDescription"
                        class="text-[14px] leading-[22px] font-normal text-[#FAFAFA] font-sans"
                    >
                        {{ heroDescription }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance, ref, watch } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'

const props = defineProps({
    heroBanner: {
        type: Object,
        default: null,
    },
})

const instance = getCurrentInstance()
const { translate, isEn } = useTranslate()

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

const homeHref = computed(() => safeRoute('home', undefined, isEn.value ? '/en' : '/'))

function normalizeImageUrl(url) {
    if (!url || typeof url !== 'string') return ''
    const trimmed = url.trim()
    if (!trimmed) return ''
    if (trimmed.includes('/static/assets/')) {
        return trimmed.replace(/^https?:\/\/[^/]+/, '').replace('/static/', '/')
    }
    if (/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\/(assets|images)\//i.test(trimmed)) {
        return trimmed.replace(/^https?:\/\/[^/]+/i, '')
    }
    if (/^(assets|images)\//i.test(trimmed)) {
        return `/${trimmed}`
    }
    return trimmed
}

const cmsDesktopUrl = computed(() => {
    const raw = props.heroBanner?.image
    const candidate = typeof raw === 'string' ? raw : raw?.url || props.heroBanner?.image_url
    return normalizeImageUrl(candidate)
})

const cmsMobileUrl = computed(() => {
    const raw = props.heroBanner?.image_mobile
    return normalizeImageUrl(typeof raw === 'string' ? raw : raw?.url)
})

const cmsImageFailed = ref(false)

watch([cmsDesktopUrl, cmsMobileUrl], () => {
    cmsImageFailed.value = false
})

const resolvedDesktopUrl = computed(() => {
    if (cmsImageFailed.value) return ''
    return cmsDesktopUrl.value || cmsMobileUrl.value
})

const resolvedMobileUrl = computed(() => {
    if (cmsImageFailed.value) return ''
    return cmsMobileUrl.value || cmsDesktopUrl.value
})

function handleImageError() {
    cmsImageFailed.value = true
}

const breadcrumbLabel = computed(() => {
    const val = props.heroBanner?.subtitle
    return typeof val === 'string' && val.trim() ? val.trim() : translate('Sản phẩm')
})

const heroTitle = computed(() => {
    const val = props.heroBanner?.title
    return typeof val === 'string' ? val.trim() : ''
})

const heroDescription = computed(() => {
    const val = props.heroBanner?.description
    return typeof val === 'string' ? val.trim() : ''
})

const imageAlt = computed(() => {
    const alt =
        props.heroBanner?.image?.alt ||
        props.heroBanner?.image_mobile?.alt ||
        heroTitle.value.replace(/\s+/g, ' ')
    return typeof alt === 'string' ? alt.trim() : ''
})

const hasContent = computed(() => {
    if (!props.heroBanner || typeof props.heroBanner !== 'object') return false
    return Boolean(
        heroTitle.value ||
            heroDescription.value ||
            resolvedDesktopUrl.value ||
            resolvedMobileUrl.value
    )
})
</script>
