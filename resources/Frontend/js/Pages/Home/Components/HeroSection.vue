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
        <!-- Aspect-Ratio Locked Hero Viewport (Mobile 375/480, Desktop 1440/582) -->
        <div class="relative w-full aspect-[375/480] md:aspect-[1440/582] overflow-hidden">
            <!-- Empty state wrapper (zero fake copy) -->
            <div v-if="normalizedSlides.length === 0" class="w-full h-full bg-[#00284B]" />

            <!-- Horizontal Slide Track (translate-x, zero opacity blank flash) -->
            <div
                v-else
                class="flex h-full w-full transition-transform duration-500 ease-out will-change-transform"
                :style="{ transform: `translateX(-${activeIndex * 100}%)` }"
            >
                <div
                    v-for="(slide, index) in normalizedSlides"
                    :key="slide.id || index"
                    class="relative w-full h-full shrink-0 basis-full"
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

                    <!-- Left Dark Scrim Overlay (Figma 595:15538: 744px / 51.67% left gradient on desktop) -->
                    <div
                        class="absolute inset-y-0 left-0 w-full md:w-[51.67%] md:min-w-[744px] bg-[#01101E]/[0.45] md:bg-transparent md:bg-[linear-gradient(to_right,rgba(1,16,30,0.6)_0%,rgba(1,16,30,0)_100%)] pointer-events-none"
                        aria-hidden="true"
                    />

                    <!-- Slide Content Container (1280px inner content width via .app-container-wide, px-4 / md:px-8) -->
                    <div
                        class="relative z-10 h-full app-container-wide flex items-end justify-between pb-6 md:pb-10 pt-8 md:pt-12 lg:pt-16"
                    >
                        <div class="w-full max-w-[542px] flex flex-col items-start">
                            <!-- Eyebrow -->
                            <p
                                v-if="slide.subtitle"
                                class="w-full bg-[linear-gradient(to_right,#009FFF_0%,#C9E0FF_40%)] bg-clip-text text-transparent font-normal text-lg md:text-[24px] lg:text-[28px] leading-[1.5] md:leading-[1.6]"
                            >
                                {{ slide.subtitle }}
                            </p>

                            <!-- Main Heading (single <h1> on first slide for valid SEO) -->
                            <component
                                :is="index === 0 ? 'h1' : 'p'"
                                v-if="slide.title"
                                class="font-bold text-[28px] sm:text-[32px] md:text-[38px] lg:text-[48px] leading-[1.25] md:leading-[1.4] text-white uppercase tracking-normal"
                            >
                                {{ slide.title }}
                            </component>

                            <!-- Description -->
                            <p
                                v-if="slide.description"
                                class="mt-2 font-medium text-sm md:text-[16px] lg:text-[18px] leading-[1.6] text-[#C9E0FF] line-clamp-4 md:line-clamp-none"
                            >
                                {{ slide.description }}
                            </p>

                            <!-- 2 CTA Buttons (strictly guarded: never render dead href="#") -->
                            <div
                                v-if="(slide.primary_cta_text && slide.primary_cta_link) || (slide.secondary_cta_text && slide.secondary_cta_link)"
                                class="mt-5 md:mt-8 xl:mt-12 flex flex-wrap gap-3 md:gap-4 items-center"
                            >
                                <Link
                                    v-if="slide.primary_cta_text && slide.primary_cta_link && isInertiaLink(slide.primary_cta_link)"
                                    :href="slide.primary_cta_link"
                                    :tabindex="index === activeIndex ? undefined : -1"
                                    class="bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] font-medium text-sm md:text-[16px] lg:text-[18px] leading-[1.6] px-5 md:px-6 py-2.5 rounded-[4px] transition-colors inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                                >
                                    {{ slide.primary_cta_text }}
                                </Link>
                                <a
                                    v-else-if="slide.primary_cta_text && slide.primary_cta_link"
                                    :href="slide.primary_cta_link"
                                    :tabindex="index === activeIndex ? undefined : -1"
                                    class="bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] font-medium text-sm md:text-[16px] lg:text-[18px] leading-[1.6] px-5 md:px-6 py-2.5 rounded-[4px] transition-colors inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                                >
                                    {{ slide.primary_cta_text }}
                                </a>

                                <Link
                                    v-if="slide.secondary_cta_text && slide.secondary_cta_link && isInertiaLink(slide.secondary_cta_link)"
                                    :href="slide.secondary_cta_link"
                                    :tabindex="index === activeIndex ? undefined : -1"
                                    class="bg-[#C9E0FF] hover:bg-[#81BDFF] text-[#003967] font-medium text-sm md:text-[16px] lg:text-[18px] leading-[1.6] px-5 md:px-6 py-2.5 rounded-[4px] transition-colors inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                                >
                                    {{ slide.secondary_cta_text }}
                                </Link>
                                <a
                                    v-else-if="slide.secondary_cta_text && slide.secondary_cta_link"
                                    :href="slide.secondary_cta_link"
                                    :tabindex="index === activeIndex ? undefined : -1"
                                    class="bg-[#C9E0FF] hover:bg-[#81BDFF] text-[#003967] font-medium text-sm md:text-[16px] lg:text-[18px] leading-[1.6] px-5 md:px-6 py-2.5 rounded-[4px] transition-colors inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                                >
                                    {{ slide.secondary_cta_text }}
                                </a>
                            </div>

                            <!-- Reserve vertical space for fixed bottom slider controls (80px gap + 10px dots on xl desktop; clears 40px arrows on mobile) -->
                            <div
                                v-if="normalizedSlides.length > 1"
                                class="mt-6 md:mt-10 lg:mt-14 xl:mt-20 h-10 md:h-[10px]"
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
                <div class="app-container-wide pb-6 md:pb-10 flex items-end justify-between gap-4">
                    <!-- Pagination Squares -->
                    <div
                        class="flex items-center gap-4 md:gap-6 pointer-events-auto"
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
                            class="w-[10px] h-[10px] rounded-[2px] transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#01101E]"
                            :class="
                                idx === activeIndex
                                    ? 'bg-[#FAFAFA]'
                                    : 'border border-[#FAFAFA] bg-transparent hover:bg-white/40'
                            "
                            @click="goToSlide(idx)"
                        />
                    </div>

                    <!-- Right Prev / Next Arrow Controls -->
                    <div class="flex items-center gap-3 pointer-events-auto">
                        <button
                            type="button"
                            class="w-10 h-10 rounded-[4px] bg-[#394C6B]/80 hover:bg-[#394C6B] flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            :aria-label="tt('Slide trước')"
                            @click="prevSlide"
                        >
                            <img
                                src="/assets/svg/icons/hero-chevron-left.svg"
                                alt=""
                                aria-hidden="true"
                                width="20"
                                height="20"
                                class="w-5 h-5"
                            />
                        </button>
                        <button
                            type="button"
                            class="w-10 h-10 rounded-[4px] bg-[#394C6B]/80 hover:bg-[#394C6B] flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            :aria-label="tt('Slide tiếp theo')"
                            @click="nextSlide"
                        >
                            <img
                                src="/assets/svg/icons/hero-chevron-left.svg"
                                alt=""
                                aria-hidden="true"
                                width="20"
                                height="20"
                                class="w-5 h-5 rotate-180"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'

const AUTOPLAY_INTERVAL_MS = 6000

const props = defineProps({
    sliders: {
        type: Array,
        default: () => [],
    },
})

const activeIndex = ref(0)
const failedImages = reactive({})
let autoplayTimer = null
let isPaused = false

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
