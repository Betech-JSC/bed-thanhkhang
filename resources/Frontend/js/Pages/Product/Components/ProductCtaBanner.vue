<template>
    <!-- Outer Spacing Wrapper (Figma 1264:49400 — 362px banner + 80px bottom breathing room before Footer) -->
    <div
        v-if="hasContent"
        class="w-full bg-[#FAFAFA] pb-8 md:pb-16 lg:pb-[80px]"
    >
        <section
            id="product-cta-section"
            :aria-labelledby="ctaHeading ? 'product-cta-heading' : undefined"
            class="aspect-[375/340] min-h-[320px] md:aspect-[1440/362] md:min-h-[300px] xl:min-h-0 w-full relative overflow-hidden bg-[#00284B] flex items-center"
        >
            <!-- CMS Background Banner Image (Desktop 1440x362 & Mobile 750x680) -->
            <template v-if="desktopBannerImageUrl">
                <picture class="absolute inset-0 block w-full h-full">
                    <source
                        v-if="mobileBannerImageUrl && mobileBannerImageUrl !== desktopBannerImageUrl"
                        media="(max-width: 767px)"
                        :srcset="mobileBannerImageUrl"
                    />
                    <img
                        :src="desktopBannerImageUrl"
                        :alt="bannerImageAlt"
                        width="1440"
                        height="362"
                        loading="lazy"
                        decoding="async"
                        class="w-full h-full object-cover object-center select-none pointer-events-none"
                        @error="handleBannerImageError"
                    />
                </picture>
                <div
                    class="absolute inset-0 bg-gradient-to-b from-[#00284B]/10 via-[#00284B]/30 to-[#00284B]/10 pointer-events-none"
                    aria-hidden="true"
                />
            </template>

            <!-- Fallback Chain Decorations when banner image is missing or fails to load -->
            <template v-else-if="!chainImageFailed">
                <div
                    class="hidden md:block pointer-events-none select-none absolute left-0 bottom-0 w-[26.8%] h-[55%] overflow-hidden z-0 opacity-35 lg:opacity-80"
                    aria-hidden="true"
                >
                    <img
                        :src="CHAIN_DECO_IMAGE"
                        alt=""
                        width="1584"
                        height="672"
                        loading="lazy"
                        decoding="async"
                        class="absolute max-w-none w-[215%] h-auto left-[-55%] top-[-16%] rotate-[14deg]"
                        @error="chainImageFailed = true"
                    />
                </div>

                <div
                    class="hidden md:block pointer-events-none select-none absolute right-0 top-0 w-[26.8%] h-[55%] overflow-hidden z-0 opacity-35 lg:opacity-80"
                    aria-hidden="true"
                >
                    <img
                        :src="CHAIN_DECO_IMAGE"
                        alt=""
                        width="1584"
                        height="672"
                        loading="lazy"
                        decoding="async"
                        class="absolute max-w-none w-[220%] h-auto right-[-64%] bottom-[-10%] rotate-[194deg]"
                        @error="chainImageFailed = true"
                    />
                </div>

                <img
                    :src="CHAIN_DECO_IMAGE"
                    alt=""
                    aria-hidden="true"
                    width="1584"
                    height="672"
                    loading="lazy"
                    decoding="async"
                    class="md:hidden pointer-events-none select-none absolute -left-14 -bottom-10 w-[210px] h-auto rotate-[14deg] opacity-20 z-0"
                    @error="chainImageFailed = true"
                />
                <img
                    :src="CHAIN_DECO_IMAGE"
                    alt=""
                    aria-hidden="true"
                    width="1584"
                    height="672"
                    loading="lazy"
                    decoding="async"
                    class="md:hidden pointer-events-none select-none absolute -right-14 -top-10 w-[210px] h-auto rotate-[194deg] opacity-20 z-0"
                    @error="chainImageFailed = true"
                />
            </template>

            <!-- Center Content (Figma 1264:49400 — #FAFAFA heading & #9AA4B2 subtitle, 2 CTA buttons) -->
            <div
                v-if="ctaHeading || ctaSubheading || (primaryText && primaryHref) || (secondaryText && secondaryHref)"
                class="app-container-narrow relative z-10 py-8 md:py-12"
            >
                <div class="max-w-[580px] mx-auto text-center flex flex-col items-center gap-6 md:gap-8">
                    <div
                        v-if="ctaHeading || ctaSubheading"
                        class="flex flex-col items-center gap-1 md:gap-0"
                    >
                        <h2
                            v-if="ctaHeading"
                            id="product-cta-heading"
                            class="text-[20px] leading-[28px] md:text-[28px] md:leading-[45px] font-bold text-[#FAFAFA] uppercase break-words"
                        >
                            {{ ctaHeading }}
                        </h2>
                        <p
                            v-if="ctaSubheading"
                            class="text-[14px] leading-[22px] md:text-[16px] md:leading-[26px] font-medium text-[#9AA4B2] break-words"
                        >
                            {{ ctaSubheading }}
                        </p>
                    </div>

                    <!-- 2 CTA Buttons: Primary white (#FAFAFA / text #005EA4) & Secondary outline (#FAFAFA) -->
                    <div
                        v-if="(primaryText && primaryHref) || (secondaryText && secondaryHref)"
                        class="w-full max-w-[531px] grid grid-cols-1 gap-3 md:gap-4"
                        :class="
                            primaryText && primaryHref && secondaryText && secondaryHref
                                ? 'sm:grid-cols-2'
                                : 'sm:max-w-[260px]'
                        "
                    >
                        <template v-if="primaryText && primaryHref">
                            <Link
                                v-if="isInertiaHref(primaryHref, primaryTarget)"
                                :href="primaryHref"
                                :class="PRIMARY_BTN_CLASS"
                            >
                                {{ primaryText }}
                            </Link>
                            <a
                                v-else
                                :href="primaryHref"
                                :target="shouldOpenBlank(primaryHref, primaryTarget) ? '_blank' : undefined"
                                :rel="shouldOpenBlank(primaryHref, primaryTarget) ? 'noopener noreferrer' : undefined"
                                :class="PRIMARY_BTN_CLASS"
                            >
                                {{ primaryText }}
                            </a>
                        </template>

                        <template v-if="secondaryText && secondaryHref">
                            <Link
                                v-if="isInertiaHref(secondaryHref, secondaryTarget)"
                                :href="secondaryHref"
                                :class="SECONDARY_BTN_CLASS"
                            >
                                {{ secondaryText }}
                            </Link>
                            <a
                                v-else
                                :href="secondaryHref"
                                :target="shouldOpenBlank(secondaryHref, secondaryTarget) ? '_blank' : undefined"
                                :rel="shouldOpenBlank(secondaryHref, secondaryTarget) ? 'noopener noreferrer' : undefined"
                                :class="SECONDARY_BTN_CLASS"
                            >
                                {{ secondaryText }}
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, getCurrentInstance, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'

const CHAIN_DECO_IMAGE = '/assets/images/homepage/contact-chain-deco.png'

const PRIMARY_BTN_CLASS =
    'bg-[#FAFAFA] hover:bg-[#EEF2F6] active:bg-[#E3E8EF] text-[#005EA4] min-h-[46px] md:h-[49px] px-4 rounded-[4px] text-[15px] md:text-[18px] leading-[24px] md:leading-[29px] font-medium inline-flex items-center justify-center whitespace-nowrap transition-colors shadow-xs focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FAFAFA] focus-visible:ring-offset-2 focus-visible:ring-offset-[#00284B]'

const SECONDARY_BTN_CLASS =
    'border border-[#FAFAFA] bg-transparent hover:bg-white/10 active:bg-white/15 text-[#FAFAFA] min-h-[46px] md:h-[49px] px-4 rounded-[4px] text-[15px] md:text-[18px] leading-[24px] md:leading-[29px] font-medium inline-flex items-center justify-center whitespace-nowrap transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FAFAFA] focus-visible:ring-offset-2 focus-visible:ring-offset-[#00284B]'

const props = defineProps({
    ctaBanner: {
        type: [Object, Array],
        default: null,
    },
    banner: {
        type: [Object, Array],
        default: null,
    },
})

const page = usePage()
const instance = getCurrentInstance()

const pageProps = computed(() => page.props?.value ?? page.props ?? {})

const currentLocale = computed(() => {
    const loc = pageProps.value?.locale
    if (typeof loc === 'string') return loc
    return loc?.current || 'vi'
})

const isEn = computed(() => currentLocale.value === 'en')

function resolveBannerObject(raw) {
    if (Array.isArray(raw)) {
        return raw.length > 0 ? raw[0] : null
    }
    if (raw && typeof raw === 'object') {
        if (Array.isArray(raw.sliders)) {
            return raw.sliders.length > 0 ? raw.sliders[0] : null
        }
        return raw
    }
    return null
}

const resolvedBanner = computed(
    () =>
        resolveBannerObject(props.ctaBanner) ||
        resolveBannerObject(props.banner) ||
        resolveBannerObject(pageProps.value?.globalCtaBanner) ||
        resolveBannerObject(pageProps.value?.data?.banner?.BANNER_GLOBAL_CTA) ||
        null
)

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

function firstNonEmptyString(...values) {
    for (const val of values) {
        if (typeof val === 'string' && val.trim().length > 0) {
            return val.trim()
        }
    }
    return ''
}

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

function normalizeActionUrl(url) {
    if (typeof url !== 'string') return ''
    const trimmed = url.trim()
    if (!trimmed || trimmed === '#') return ''
    if (trimmed.includes('/static/assets/')) {
        return trimmed.replace(/^https?:\/\/[^/]+/, '').replace('/static/', '/')
    }
    if (/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?(\/|$)/i.test(trimmed)) {
        return trimmed.replace(/^https?:\/\/[^/]+/i, '') || '/'
    }
    return trimmed
}

function isDirectProtocolHref(href) {
    return typeof href === 'string' && /^(tel|mailto):/i.test(href)
}

function isInertiaHref(href, target) {
    if (!href || typeof href !== 'string') return false
    if (target === '_blank' || isDirectProtocolHref(href)) return false
    return href.startsWith('/') && !href.startsWith('//')
}

function shouldOpenBlank(href, target) {
    if (!href || isDirectProtocolHref(href)) return false
    if (target === '_blank') return true
    return typeof href === 'string' && /^https?:\/\//i.test(href)
}

const bannerImageFailed = ref(false)
const chainImageFailed = ref(false)

const rawDesktopBannerUrl = computed(() => {
    const raw = resolvedBanner.value?.image
    const candidate =
        typeof raw === 'string' ? raw : raw?.url || raw?.path || resolvedBanner.value?.image_url || ''
    return normalizeImageUrl(candidate)
})

const rawMobileBannerUrl = computed(() => {
    const raw = resolvedBanner.value?.image_mobile
    return normalizeImageUrl(typeof raw === 'string' ? raw : raw?.url || raw?.path || '')
})

watch([rawDesktopBannerUrl, rawMobileBannerUrl], () => {
    bannerImageFailed.value = false
})

const desktopBannerImageUrl = computed(() => {
    if (bannerImageFailed.value) return ''
    return rawDesktopBannerUrl.value || rawMobileBannerUrl.value
})

const mobileBannerImageUrl = computed(() => {
    if (bannerImageFailed.value) return ''
    return rawMobileBannerUrl.value || rawDesktopBannerUrl.value
})

function handleBannerImageError() {
    bannerImageFailed.value = true
}

const siteConfig = computed(() => ({
    ...(pageProps.value?.global || {}),
    ...(pageProps.value?.configs || {}),
}))

const rawPhone = computed(() =>
    firstNonEmptyString(
        siteConfig.value.hotline,
        siteConfig.value.general_phone,
        siteConfig.value.phone
    )
)

const cleanPhone = computed(() => rawPhone.value.replace(/[^\d+]/g, ''))

const contactHref = computed(() =>
    safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he')
)

const ctaHeading = computed(() => firstNonEmptyString(resolvedBanner.value?.title))

const ctaSubheading = computed(() => firstNonEmptyString(resolvedBanner.value?.description))

const bannerImageAlt = computed(() =>
    firstNonEmptyString(
        typeof resolvedBanner.value?.image === 'object' ? resolvedBanner.value?.image?.alt : '',
        typeof resolvedBanner.value?.image_mobile === 'object'
            ? resolvedBanner.value?.image_mobile?.alt
            : '',
        ctaHeading.value.replace(/\s+/g, ' ')
    )
)

const primaryText = computed(() => firstNonEmptyString(resolvedBanner.value?.primary_cta_text))

const primaryTarget = computed(() => firstNonEmptyString(resolvedBanner.value?.target))

const primaryHref = computed(() => {
    if (!primaryText.value) return ''
    const custom = normalizeActionUrl(
        firstNonEmptyString(resolvedBanner.value?.primary_cta_link, resolvedBanner.value?.link)
    )
    if (custom) return custom
    if (/hotline|gọi|call/i.test(primaryText.value) && cleanPhone.value) {
        return `tel:${cleanPhone.value}`
    }
    return contactHref.value
})

const secondaryText = computed(() => firstNonEmptyString(resolvedBanner.value?.secondary_cta_text))

const secondaryTarget = computed(() =>
    firstNonEmptyString(resolvedBanner.value?.secondary_target, resolvedBanner.value?.target)
)

const secondaryHref = computed(() => {
    if (!secondaryText.value) return ''
    const custom = normalizeActionUrl(firstNonEmptyString(resolvedBanner.value?.secondary_cta_link))
    if (custom) return custom
    if (/zalo/i.test(secondaryText.value)) {
        const customZalo = normalizeActionUrl(firstNonEmptyString(siteConfig.value.zalo))
        if (customZalo) return customZalo
        if (cleanPhone.value) return `https://zalo.me/${cleanPhone.value.replace(/^\+/, '')}`
    }
    if (cleanPhone.value) return `tel:${cleanPhone.value}`
    return contactHref.value
})

const hasContent = computed(() => {
    if (!resolvedBanner.value || typeof resolvedBanner.value !== 'object') return false
    return Boolean(
        ctaHeading.value ||
            ctaSubheading.value ||
            (primaryText.value && primaryHref.value) ||
            (secondaryText.value && secondaryHref.value) ||
            desktopBannerImageUrl.value ||
            mobileBannerImageUrl.value
    )
})
</script>
