<template>
    <section
        v-if="hasContent"
        id="about-mission-vision-section"
        class="relative w-full overflow-hidden bg-[#001427] aspect-[375/560] md:aspect-[1440/460] md:min-h-[360px] xl:min-h-0 flex flex-col"
    >
        <!-- Full-Bleed Clean Globe Background Banner (Figma 1171:41945 — anchored bottom) -->
        <picture
            v-if="desktopBannerUrl"
            class="absolute inset-0 block w-full h-full pointer-events-none select-none"
        >
            <source
                v-if="mobileBannerUrl && mobileBannerUrl !== desktopBannerUrl"
                media="(max-width: 767px)"
                :srcset="mobileBannerUrl"
            />
            <img
                :src="desktopBannerUrl"
                :alt="bannerAlt"
                width="1440"
                height="460"
                loading="lazy"
                decoding="async"
                class="w-full h-full object-cover object-bottom select-none"
                @error="bannerFailed = true"
            />
        </picture>

        <!-- Mobile Readability Scrim over Copy Region (WCAG AA contrast while preserving bottom globe glow) -->
        <div
            class="md:hidden absolute inset-0 bg-[linear-gradient(180deg,rgba(0,11,29,0.82)_0%,rgba(0,11,29,0.72)_76%,rgba(0,11,29,0.18)_100%)] pointer-events-none"
            aria-hidden="true"
        />

        <!-- Foreground 2-Column Content (Figma 1171:41946 & 1171:41951: 1152px = 516px + 120px gap + 516px) -->
        <div class="relative z-10 app-container-narrow py-8 md:py-12 lg:pt-[64px] lg:pb-6">
            <div
                v-if="validItems.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12 lg:gap-[120px]"
            >
                <article
                    v-for="item in validItems"
                    :key="item.key"
                    class="flex flex-col items-start gap-3 md:gap-4 min-w-0"
                >
                    <!-- Eyebrow Label (Figma 1171:41947 / 1171:41952: 18px/29px 700 uppercase gradient #009FFF -> #C9E0FF) -->
                    <p
                        v-if="item.subtitle"
                        class="w-full font-bold text-[16px] leading-[26px] md:text-[18px] md:leading-[29px] uppercase bg-[linear-gradient(90deg,#009FFF_0%,#C9E0FF_100%)] bg-clip-text text-transparent"
                    >
                        {{ item.subtitle }}
                    </p>

                    <!-- Statement & Explanation Group (Figma 1171:41948 / 1171:41953: gap 12px) -->
                    <div
                        v-if="item.title || item.description"
                        class="w-full flex flex-col items-start gap-2 md:gap-3"
                    >
                        <h2
                            v-if="item.title"
                            class="font-bold text-[20px] leading-[30px] md:text-[24px] md:leading-[38px] text-[#FAFAFA] tracking-normal break-words"
                        >
                            {{ item.title }}
                        </h2>
                        <p
                            v-if="item.description"
                            class="font-normal text-[14px] leading-[22px] md:text-[16px] md:leading-[26px] text-[#CDD5DF] break-words"
                        >
                            {{ item.description }}
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    missionVisionSection: {
        type: Object,
        default: null,
    },
})

const bannerFailed = ref(false)

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

function cleanText(val) {
    if (typeof val !== 'string') return ''
    return val.replace(/\u2028/g, ' ').trim()
}

const rawDesktopBannerUrl = computed(() => {
    const banner = props.missionVisionSection?.banner
    if (!banner) return ''
    if (typeof banner === 'string') return normalizeImageUrl(banner)
    return normalizeImageUrl(banner.url || banner.image?.url)
})

const rawMobileBannerUrl = computed(() => {
    const banner = props.missionVisionSection?.banner
    if (!banner || typeof banner !== 'object') return ''
    return normalizeImageUrl(banner.mobile_url || banner.image_mobile?.url)
})

watch([rawDesktopBannerUrl, rawMobileBannerUrl], () => {
    bannerFailed.value = false
})

const desktopBannerUrl = computed(() => {
    if (bannerFailed.value) return ''
    return rawDesktopBannerUrl.value || rawMobileBannerUrl.value
})

const mobileBannerUrl = computed(() => {
    if (bannerFailed.value) return ''
    return rawMobileBannerUrl.value || rawDesktopBannerUrl.value
})

const bannerAlt = computed(() => {
    const alt = props.missionVisionSection?.banner?.alt
    return typeof alt === 'string' ? alt.trim() : ''
})

const validItems = computed(() => {
    const list = props.missionVisionSection?.items
    if (!Array.isArray(list)) return []
    return list
        .map((item, idx) => {
            if (!item || typeof item !== 'object') return null
            const subtitle = cleanText(item.subtitle)
            const title = cleanText(item.title)
            const description = cleanText(item.description)
            if (!subtitle && !title && !description) return null
            return {
                id: item.id ?? null,
                key: item.id ?? `${idx}-${title || subtitle}`,
                subtitle,
                title,
                description,
            }
        })
        .filter(Boolean)
})

const hasContent = computed(() => {
    if (!props.missionVisionSection || typeof props.missionVisionSection !== 'object') return false
    return Boolean(validItems.value.length > 0 || desktopBannerUrl.value)
})
</script>
