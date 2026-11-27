<template>
    <section
        class="w-full relative overflow-hidden bg-[#00284B]"
        aria-roledescription="carousel"
        :aria-label="tt('Banner trang chủ')"
        @mouseenter="pauseAutoplay"
        @mouseleave="resumeAutoplay"
        @focusin="pauseAutoplay"
        @focusout="resumeAutoplay"
        @keydown.left.prevent="prevSlide"
        @keydown.right.prevent="nextSlide"
    >
        <!-- Compact Empty State Container when CMS HOME_SLIDER is empty -->
        <div v-if="normalizedSlides.length === 0" class="app-container-wide py-10 md:py-14 lg:py-16">
            <EmptyState
                variant="dark"
                heading-tag="h1"
                :title="heroEmptyTitle"
                :description="heroEmptyDescription"
                :primary-action-text="heroPrimaryActionText"
                :primary-action-href="contactHref"
            />
        </div>

        <!-- Height-Locked Hero Viewport (Mobile min-h 460px — Figma 1314:52003, Desktop exact 582px — Figma 1264:48232) -->
        <div v-else class="relative w-full min-h-[460px] md:min-h-[480px] lg:h-[582px] lg:min-h-[582px] overflow-hidden">
            <!-- Horizontal Slide Track (translate-x, zero opacity blank flash) -->
            <div
                class="flex h-full min-h-[460px] md:min-h-[480px] lg:h-[582px] lg:min-h-[582px] w-full transition-transform duration-500 ease-out will-change-transform"
                :style="{ transform: `translateX(-${activeIndex * 100}%)` }"
            >
                <div
                    v-for="(slide, index) in normalizedSlides"
                    :key="slide.id || index"
                    class="relative w-full h-full min-h-[460px] md:min-h-[480px] lg:h-[582px] lg:min-h-[582px] shrink-0 basis-full"
                    role="group"
                    aria-roledescription="slide"
                    :aria-label="`${index + 1} / ${normalizedSlides.length}`"
                    :aria-hidden="index !== activeIndex ? 'true' : undefined"
                >
                    <!-- Slide Background Image (CMS-only, clean surface if empty/errored) -->
                    <picture
                        v-if="getDesktopImage(slide, index)"
                        class="absolute inset-0 block w-full h-full"
                    >
                        <source
                            v-if="getMobileImage(slide, index)"
                            media="(max-width: 767px)"
                            :srcset="getMobileImage(slide, index)"
                        />
                        <img
                            :src="getDesktopImage(slide, index)"
                            :alt="slide.image?.alt || slide.title || ''"
                            class="w-full h-full object-cover object-center select-none"
                            width="1440"
                            height="582"
                            :loading="index === 0 ? 'eager' : 'lazy'"
                            decoding="async"
                            :fetchpriority="index === 0 ? 'high' : undefined"
                            @error="handleImageError(index)"
                        />
                    </picture>

                    <!-- Mobile Left Scrim (Figma 1314:52005) + Desktop Left Scrim (Figma 1171:41718) -->
                    <div
                        class="absolute inset-y-0 left-0 w-full md:w-[51.67%] md:min-w-[744px] bg-[linear-gradient(90deg,#01101E_0%,rgba(1,16,30,0)_100%)] md:bg-[linear-gradient(to_right,rgba(1,16,30,0.6)_0%,rgba(1,16,30,0)_100%)] pointer-events-none"
                        aria-hidden="true"
                    />

                    <!-- Desktop Top Header Transition Gradient Scrim (Figma 1171:41719) -->
                    <div
                        class="hidden md:block absolute inset-x-0 top-0 h-[144px] bg-[linear-gradient(180deg,rgba(1,16,30,0.7)_0%,rgba(1,16,30,0)_100%)] pointer-events-none"
                        aria-hidden="true"
                    />

                    <!-- Slide Content Container (1152px inner content width via .app-container-narrow, px-4 / md:px-8) -->
                    <div
                        class="relative z-10 h-full min-h-[460px] md:min-h-[480px] lg:h-[582px] lg:min-h-[582px] app-container-narrow flex items-start md:items-end justify-between pt-7 pb-6 md:pb-10 md:pt-10 lg:pt-12 xl:pt-16"
                    >
                        <div class="w-full max-w-[542px] flex flex-col items-start">
                            <!-- Eyebrow (Figma 1264:48265 — exact 40.037% gradient stop) -->
                            <p
                                v-if="slide.subtitle"
                                class="w-full bg-[linear-gradient(90deg,#009FFF_0%,#C9E0FF_40.037%)] bg-clip-text text-transparent font-normal text-[18px] md:text-[24px] lg:text-[28px] leading-[1.6]"
                            >
                                {{ slide.subtitle }}
                            </p>

                            <!-- Main Heading (single <h1> on first slide for valid SEO — Figma 1264:48266 48px/1.4 Desktop) -->
                            <component
                                :is="index === 0 ? 'h1' : 'p'"
                                v-if="slide.title"
                                class="mt-0 font-bold text-[28px] md:text-[38px] lg:text-[48px] leading-[36px] md:leading-[1.4] text-white uppercase tracking-normal"
                            >
                                {{ slide.title }}
                            </component>

                            <!-- Description (Figma 1264:48267 18px/1.6 Desktop, full text on Mobile) -->
                            <p
                                v-if="slide.description"
                                class="mt-2 font-medium text-[14px] md:text-[16px] lg:text-[18px] leading-[1.6] text-[#C9E0FF]"
                            >
                                {{ slide.description }}
                            </p>

                            <!-- 2 Equal-Width CTA Buttons (Figma 1264:48268..48270 — w=542px -> 2x 263x49px via flex-1, gap-[16px]) -->
                            <div
                                v-if="(slide.primary_cta_text && slide.primary_cta_link) || (slide.secondary_cta_text && slide.secondary_cta_link)"
                                class="mt-4 md:mt-6 xl:mt-[48px] w-full flex flex-col md:flex-row gap-3 md:gap-[16px] items-stretch md:items-center"
                            >
                                <Link
                                    v-if="slide.primary_cta_text && slide.primary_cta_link && isInertiaLink(slide.primary_cta_link)"
                                    :href="slide.primary_cta_link"
                                    :tabindex="index === activeIndex ? undefined : -1"
                                    class="w-full md:flex-1 h-[49px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] font-medium text-[18px] leading-[1.6] px-4 md:px-[24px] py-[10px] rounded-[4px] transition-colors inline-flex items-center justify-center text-center whitespace-nowrap focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                                >
                                    {{ slide.primary_cta_text }}
                                </Link>
                                <a
                                    v-else-if="slide.primary_cta_text && slide.primary_cta_link"
                                    :href="slide.primary_cta_link"
                                    :tabindex="index === activeIndex ? undefined : -1"
                                    class="w-full md:flex-1 h-[49px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] font-medium text-[18px] leading-[1.6] px-4 md:px-[24px] py-[10px] rounded-[4px] transition-colors inline-flex items-center justify-center text-center whitespace-nowrap focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                                >
                                    {{ slide.primary_cta_text }}
                                </a>

                                <Link
                                    v-if="slide.secondary_cta_text && slide.secondary_cta_link && isInertiaLink(slide.secondary_cta_link)"
                                    :href="slide.secondary_cta_link"
                                    :tabindex="index === activeIndex ? undefined : -1"
                                    class="w-full md:flex-1 h-[49px] bg-[#C9E0FF] hover:bg-[#81BDFF] text-[#003967] font-medium text-[18px] leading-[1.6] px-4 md:px-[24px] py-[10px] rounded-[4px] transition-colors inline-flex items-center justify-center text-center whitespace-nowrap focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                                >
                                    {{ slide.secondary_cta_text }}
                                </Link>
                                <a
                                    v-else-if="slide.secondary_cta_text && slide.secondary_cta_link"
                                    :href="slide.secondary_cta_link"
                                    :tabindex="index === activeIndex ? undefined : -1"
                                    class="w-full md:flex-1 h-[49px] bg-[#C9E0FF] hover:bg-[#81BDFF] text-[#003967] font-medium text-[18px] leading-[1.6] px-4 md:px-[24px] py-[10px] rounded-[4px] transition-colors inline-flex items-center justify-center text-center whitespace-nowrap focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                                >
                                    {{ slide.secondary_cta_text }}
                                </a>
                            </div>

                            <!-- Reserve vertical space for fixed bottom slider controls so longer copy never overlaps controls -->
                            <div
                                v-if="normalizedSlides.length > 1"
                                class="mt-4 h-[44px] md:mt-8 lg:mt-10 xl:mt-20 md:h-[10px]"
                                aria-hidden="true"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slider Bottom Controls Overlay: Pagination Squares (Left) + Prev/Next Arrows (Right) -->
            <div
                v-if="normalizedSlides.length > 1"
                class="absolute inset-x-0 bottom-0 z-20 pointer-events-none"
            >
                <div class="app-container-narrow pb-6 md:pb-10 flex items-center md:items-end justify-between gap-4">
                    <!-- Pagination Squares (10x10 visual; 44x44 touch wrapper on Mobile — Figma 1314:52018..52020 / 1171:41755) -->
                    <div
                        class="flex items-center gap-0 md:gap-6 -ml-4 md:ml-0 pointer-events-auto"
                        role="tablist"
                        :aria-label="tt('Chọn slide')"
                    >
                        <button
                            v-for="(slide, idx) in normalizedSlides"
                            :key="slide.id || idx"
                            type="button"
                            role="tab"
                            :aria-selected="idx === activeIndex"
                            :aria-label="`${tt('Slide')} ${idx + 1}`"
                            class="group min-w-[44px] min-h-[44px] md:min-w-0 md:min-h-0 md:w-[10px] md:h-[10px] inline-flex items-center justify-center rounded-[4px] cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                            @click="goToSlide(idx)"
                        >
                            <span
                                class="block w-[10px] h-[10px] rounded-[2px] transition-colors"
                                :class="
                                    idx === activeIndex
                                        ? 'bg-[#FAFAFA]'
                                        : 'border border-[#FAFAFA] bg-transparent group-hover:bg-white/40'
                                "
                                aria-hidden="true"
                            />
                        </button>
                    </div>

                    <!-- Right Prev / Next Arrow Controls (44x44 touch target Mobile, 40x40 gap-3 bg-[#697586] Desktop Figma 1171:41760) -->
                    <div class="flex items-center gap-2 lg:gap-3 pointer-events-auto">
                        <button
                            type="button"
                            class="w-11 h-11 lg:w-10 lg:h-10 rounded-[4px] bg-[#697586] hover:bg-[#4B5565] flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            :aria-label="tt('Slide trước')"
                            @click="prevSlide"
                        >
                            <img
                                src="/assets/svg/icons/hero-chevron-left.svg"
                                alt=""
                                aria-hidden="true"
                                width="20"
                                height="20"
                                class="w-[18px] h-[18px] lg:w-5 lg:h-5"
                            />
                        </button>
                        <button
                            type="button"
                            class="w-11 h-11 lg:w-10 lg:h-10 rounded-[4px] bg-[#697586] hover:bg-[#4B5565] flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            :aria-label="tt('Slide tiếp theo')"
                            @click="nextSlide"
                        >
                            <img
                                src="/assets/svg/icons/hero-chevron-left.svg"
                                alt=""
                                aria-hidden="true"
                                width="20"
                                height="20"
                                class="w-[18px] h-[18px] lg:w-5 lg:h-5 rotate-180"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'
import EmptyState from '@/Components/EmptyState.vue'

const AUTOPLAY_INTERVAL_MS = 6000

const props = defineProps({
    sliders: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()
const instance = getCurrentInstance()

const activeIndex = ref(0)
const failedImages = reactive({})
let autoplayTimer = null
let isPaused = false

const pageProps = computed(() => page.props?.value ?? page.props ?? {})

const currentLocale = computed(() => {
    const loc = pageProps.value?.locale
    if (typeof loc === 'string') return loc
    return loc?.current || 'vi'
})

const isEn = computed(() => currentLocale.value === 'en')

const siteConfig = computed(() => ({
    ...(pageProps.value?.global || {}),
    ...(pageProps.value?.configs || {}),
}))

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

const heroEmptyTitle = computed(() => {
    const raw =
        siteConfig.value.general_business_name ||
        siteConfig.value.company_name ||
        siteConfig.value.general_site_name ||
        siteConfig.value.site_name ||
        ''
    return typeof raw === 'string' ? raw.trim() : ''
})

const heroEmptyDescription = computed(() => {
    const raw = siteConfig.value.general_description || siteConfig.value.description || ''
    const trimmed = typeof raw === 'string' ? raw.trim() : ''
    if (!trimmed || trimmed === heroEmptyTitle.value) return ''
    return trimmed
})

const contactHref = computed(() =>
    safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he')
)

const heroPrimaryActionText = computed(() =>
    isEn.value ? 'Contact for Consultation' : 'Liên hệ tư vấn'
)

const normalizedSlides = computed(() => {
    if (!Array.isArray(props.sliders)) return []
    return props.sliders.filter((item) => item && typeof item === 'object')
})

watch(
    () => normalizedSlides.value.length,
    (len) => {
        if (len === 0) {
            activeIndex.value = 0
        } else if (activeIndex.value >= len) {
            activeIndex.value = 0
        }
    }
)

function normalizeImageUrl(url) {
    if (!url || typeof url !== 'string') return ''
    const trimmed = url.trim()
    if (!trimmed) return ''
    if (trimmed.includes('/static/assets/')) {
        return trimmed.replace(/^https?:\/\/[^/]+/, '').replace('/static/', '/')
    }
    return trimmed
}

function getDesktopImage(slide, index) {
    if (failedImages[index]) return ''
    return normalizeImageUrl(slide?.image?.url || (typeof slide?.image === 'string' ? slide.image : ''))
}

function getMobileImage(slide, index) {
    if (failedImages[index]) return ''
    const mobileUrl = normalizeImageUrl(
        slide?.image_mobile?.url || (typeof slide?.image_mobile === 'string' ? slide.image_mobile : '')
    )
    if (mobileUrl) return mobileUrl
    return getDesktopImage(slide, index)
}

function handleImageError(index) {
    failedImages[index] = true
}

function isInertiaLink(href) {
    if (!href || typeof href !== 'string') return false
    return href.startsWith('/') && !href.startsWith('//')
}

function goToSlide(index) {
    const total = normalizedSlides.value.length
    if (total <= 1) return
    activeIndex.value = ((index % total) + total) % total
    restartAutoplay()
}

function nextSlide() {
    goToSlide(activeIndex.value + 1)
}

function prevSlide() {
    goToSlide(activeIndex.value - 1)
}

function prefersReducedMotion() {
    if (typeof window === 'undefined' || typeof window.matchMedia !== 'function') return true
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

function stopAutoplay() {
    if (autoplayTimer) {
        clearInterval(autoplayTimer)
        autoplayTimer = null
    }
}

function startAutoplay() {
    stopAutoplay()
    if (typeof window === 'undefined') return
    if (isPaused || normalizedSlides.value.length <= 1 || prefersReducedMotion()) return
    autoplayTimer = setInterval(() => {
        const total = normalizedSlides.value.length
        if (total > 1) {
            activeIndex.value = (activeIndex.value + 1) % total
        }
    }, AUTOPLAY_INTERVAL_MS)
}

function restartAutoplay() {
    if (!isPaused) {
        startAutoplay()
    }
}

function pauseAutoplay() {
    isPaused = true
    stopAutoplay()
}

function resumeAutoplay() {
    isPaused = false
    startAutoplay()
}

onMounted(() => {
    startAutoplay()
})

onBeforeUnmount(() => {
    stopAutoplay()
})
</script>
