<template>
    <section
        v-if="hasContent"
        id="documents-hero-section"
        class="relative w-full overflow-hidden bg-[#00284B]"
    >
        <!-- Aspect-Ratio Locked Hero Viewport (Mobile 375/280, Desktop 1440/252 — Figma 1264:49120) -->
        <div
            class="aspect-[375/280] min-h-[260px] md:aspect-[1440/252] md:min-h-[220px] xl:min-h-0 w-full relative overflow-hidden flex items-center"
        >
            <!-- Dynamic CMS Hero Background Image -->
            <picture
                v-if="desktopImageUrl"
                class="absolute inset-0 block w-full h-full"
            >
                <source
                    v-if="mobileImageUrl && mobileImageUrl !== desktopImageUrl"
                    media="(max-width: 767px)"
                    :srcset="mobileImageUrl"
                />
                <img
                    :src="desktopImageUrl"
                    :alt="imageAlt"
                    width="1440"
                    height="252"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                    class="w-full h-full object-cover object-center select-none pointer-events-none"
                    @error="handleImageError"
                />
            </picture>

            <!-- Mobile Contrast Scrim -->
            <div
                class="md:hidden absolute inset-0 bg-[#01101E]/60 pointer-events-none"
                aria-hidden="true"
            />

            <!-- Desktop Left Gradient Scrim (Figma 1264:49122: 62.36% width, 270deg rgba(1,16,30,0)->rgba(1,16,30,0.8)) -->
            <div
                class="hidden md:block absolute inset-y-0 left-0 w-[62.36%] min-w-[640px] bg-[linear-gradient(270deg,rgba(1,16,30,0)_0%,rgba(1,16,30,0.8)_100%)] pointer-events-none"
                aria-hidden="true"
            />

            <!-- Foreground Content Container (1152px inner via .app-container-narrow) -->
            <div class="app-container-narrow relative z-10 py-8 md:py-[52px]">
                <div class="max-w-[580px] flex flex-col gap-4 md:gap-6">
                    <!-- Breadcrumb (Figma 1264:49124) -->
                    <nav
                        :aria-label="tt('Breadcrumb')"
                        class="flex items-center gap-2 text-[13px] md:text-[14px] leading-[22px]"
                    >
                        <Link
                            :href="homeHref"
                            class="text-[#CDD5DF] hover:text-[#FAFAFA] transition-colors focus:outline-none focus-visible:underline"
                        >
                            {{ tt('Trang chủ') }}
                        </Link>
                        <span class="text-[#CDD5DF]" aria-hidden="true">/</span>
                        <span class="text-[#FAFAFA] font-medium" aria-current="page">
                            {{ breadcrumbLabel }}
                        </span>
                    </nav>

                    <!-- Title & Subtitle (Figma 1264:49128) -->
                    <div
                        v-if="heroTitle || heroDescription"
                        class="space-y-2"
                    >
                        <h1
                            v-if="heroTitle"
                            class="text-[26px] leading-[34px] md:text-[36px] md:leading-[50px] font-bold text-[#FAFAFA] uppercase tracking-normal"
                        >
                            {{ heroTitle }}
                        </h1>
                        <p
                            v-if="heroDescription"
                            class="text-[13px] md:text-[14px] leading-[22px] text-[#E3E8EF] max-w-[470px]"
                        >
                            {{ heroDescription }}
                        </p>
                    </div>
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

function safeRoute(name, params, fallback = '/') {
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

const imageFailed = ref(false)

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
    return trimmed
}

const rawDesktopUrl = computed(() => {
    const raw = props.heroBanner?.image
    const candidate = typeof raw === 'string' ? raw : raw?.url || props.heroBanner?.image_url
    return normalizeImageUrl(candidate)
})

const rawMobileUrl = computed(() => {
    const raw = props.heroBanner?.image_mobile
    return normalizeImageUrl(typeof raw === 'string' ? raw : raw?.url)
})

watch([rawDesktopUrl, rawMobileUrl], () => {
    imageFailed.value = false
})

const desktopImageUrl = computed(() => {
    if (imageFailed.value) return ''
    return rawDesktopUrl.value || rawMobileUrl.value
})

const mobileImageUrl = computed(() => {
    if (imageFailed.value) return ''
    return rawMobileUrl.value || rawDesktopUrl.value
})

function handleImageError() {
    imageFailed.value = true
}

const breadcrumbLabel = computed(() => {
    const val = props.heroBanner?.subtitle
    return typeof val === 'string' && val.trim() ? val.trim() : translate('Tài liệu')
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
            desktopImageUrl.value ||
            mobileImageUrl.value
    )
})
</script>
