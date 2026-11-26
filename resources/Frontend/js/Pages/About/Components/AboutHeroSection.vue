<template>
    <section
        v-if="hasContent"
        id="about-hero-section"
        class="w-full relative overflow-hidden bg-[#00284B]"
    >
        <!-- Aspect-Ratio Locked Hero Viewport (Mobile 375/480, Desktop 1440/582 below 118px Header) -->
        <div class="aspect-[375/480] md:aspect-[1440/582] w-full relative overflow-hidden">
            <!-- Dynamic CMS Hero Background Image -->
            <picture
                v-if="desktopImageUrl"
                class="absolute inset-0 block w-full h-full"
            >
                <source
                    v-if="mobileImageUrl"
                    media="(max-width: 767px)"
                    :srcset="mobileImageUrl"
                />
                <img
                    :src="desktopImageUrl"
                    :alt="imageAlt"
                    width="1440"
                    height="582"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                    class="w-full h-full object-cover object-center select-none"
                    @error="imageFailed = true"
                />
            </picture>

            <!-- Left Scrim Overlay (Figma 1171:41897: 744px / 51.67% width, 270deg rgba(1,16,30,0)->rgba(1,16,30,0.6); full-width WCAG scrim on mobile) -->
            <div
                class="absolute inset-y-0 left-0 w-full md:w-[51.67%] md:min-w-[744px] bg-[linear-gradient(180deg,rgba(1,16,30,0.48)_0%,rgba(1,16,30,0.76)_100%)] md:bg-[linear-gradient(270deg,rgba(1,16,30,0)_0%,rgba(1,16,30,0.6)_100%)] pointer-events-none"
                aria-hidden="true"
            />

            <!-- Top Edge Subtle Scrim Overlay (Figma 1171:41898 continuation below 118px Header) -->
            <div
                class="hidden md:block absolute inset-x-0 top-0 h-[25%] bg-[linear-gradient(180deg,rgba(1,16,30,0.4)_0%,rgba(1,16,30,0)_100%)] pointer-events-none"
                aria-hidden="true"
            />

            <!-- Foreground Content Container (Figma 1171:41895 / 1171:41923: 1280px inner via .app-container-wide, max-w 577px copy anchored bottom-left) -->
            <div
                class="relative z-10 h-full app-container-wide flex items-end pb-8 md:pb-10 lg:pb-16 xl:pb-[120px] pt-8 md:pt-10"
            >
                <div class="w-full max-w-[577px] flex flex-col items-start gap-2">
                    <div
                        v-if="subtitle || title"
                        class="w-full flex flex-col items-start"
                    >
                        <!-- Eyebrow (Figma 1171:41925: 28px/45px 400, gradient 90deg #009FFF -> #C9E0FF) -->
                        <p
                            v-if="subtitle"
                            class="w-full bg-[linear-gradient(90deg,#009FFF_0%,#C9E0FF_100%)] bg-clip-text text-transparent font-normal text-[18px] leading-[28px] md:text-[22px] md:leading-[34px] lg:text-[26px] lg:leading-[40px] xl:text-[28px] xl:leading-[45px]"
                        >
                            {{ subtitle }}
                        </p>

                        <!-- Main Page Heading H1 (Figma 1171:41926: 48px/67px 700 uppercase #FFFFFF, preserves 2-line break) -->
                        <h1
                            v-if="title"
                            class="font-bold text-[28px] leading-[36px] sm:text-[32px] sm:leading-[42px] md:text-[34px] md:leading-[46px] lg:text-[42px] lg:leading-[58px] xl:text-[48px] xl:leading-[67px] text-white uppercase tracking-normal whitespace-pre-line"
                        >
                            {{ title }}
                        </h1>
                    </div>

                    <!-- Description (Figma 1171:41927: 18px/29px 500 #C9E0FF, max-w 577px) -->
                    <p
                        v-if="description"
                        class="w-full max-w-[577px] font-medium text-[14px] leading-[22px] md:text-[15px] md:leading-[24px] lg:text-[16px] lg:leading-[26px] xl:text-[18px] xl:leading-[29px] text-[#C9E0FF]"
                    >
                        {{ description }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    heroBanner: {
        type: Object,
        default: null,
    },
})

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

const subtitle = computed(() => {
    const val = props.heroBanner?.subtitle
    return typeof val === 'string' ? val.trim() : ''
})

const title = computed(() => {
    const val = props.heroBanner?.title
    return typeof val === 'string' ? val.trim() : ''
})

const description = computed(() => {
    const val = props.heroBanner?.description
    return typeof val === 'string' ? val.trim() : ''
})

const rawDesktopUrl = computed(() => {
    const raw = props.heroBanner?.image
    return normalizeImageUrl(typeof raw === 'string' ? raw : raw?.url)
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

const imageAlt = computed(() => {
    const alt =
        props.heroBanner?.image?.alt ||
        props.heroBanner?.image_mobile?.alt ||
        title.value.replace(/\s+/g, ' ')
    return typeof alt === 'string' ? alt.trim() : ''
})

const hasContent = computed(() => {
    if (!props.heroBanner || typeof props.heroBanner !== 'object') return false
    return Boolean(
        subtitle.value ||
            title.value ||
            description.value ||
            desktopImageUrl.value ||
            mobileImageUrl.value
    )
})
</script>
