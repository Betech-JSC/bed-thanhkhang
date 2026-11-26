<template>
    <section
        v-if="hasCtaData"
        v-scroll-reveal
        class="pt-8 pb-12 lg:pt-[40px] lg:pb-[80px] bg-[#111322]"
    >
        <div class="app-container">
            <div
                class="relative w-full min-h-[183px] lg:h-[183px] rounded-[16px] overflow-hidden p-8 lg:p-[40px] shadow-2xl flex flex-col lg:flex-row lg:items-center justify-between gap-6"
                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
            >
                <!-- Content Area (Figma 2346:742) -->
                <div
                    v-if="title || description"
                    class="relative z-10 w-full lg:w-[817px] lg:max-w-[817px] flex flex-col gap-3"
                >
                    <h3
                        v-if="title"
                        class="text-2xl sm:text-3xl lg:text-[32px] font-bold text-[#FAFAFA] tracking-normal leading-tight lg:leading-[37px] font-sans"
                    >
                        {{ title }}
                    </h3>
                    <p
                        v-if="description"
                        class="text-sm sm:text-base lg:text-[16px] text-[#FAFAFA] leading-relaxed lg:leading-[26px] font-normal"
                    >
                        {{ description }}
                    </p>
                </div>

                <!-- CTA Single Action Button (Figma 2346:745 - 2346:749) -->
                <div v-if="ctaText && ctaLink" class="relative z-10 shrink-0">
                    <Link
                        :href="ctaLink"
                        class="w-full sm:w-[175px] h-[40px] rounded-[8px] bg-[#F5F8FF] hover:bg-white text-[#111322] text-base font-medium leading-6 inline-flex items-center justify-center transition-colors duration-200 active:scale-[0.98] shadow-md hover:shadow-lg select-none"
                    >
                        {{ ctaText }}
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
    name: 'ProductCtaBanner',
    components: {
        Link,
    },
    props: {
        banner: {
            type: Object,
            default: () => null,
        },
    },
    computed: {
        isEn() {
            return (this.$page?.props?.locale?.current || 'vi') === 'en'
        },
        ctaData() {
            const candidate =
                this.banner ||
                this.$page?.props?.globalCtaBanner ||
                this.$page?.props?.data?.banner?.BANNER_GLOBAL_CTA ||
                null
            if (Array.isArray(candidate)) {
                return candidate.length > 0 ? candidate[0] : null
            }
            if (candidate && typeof candidate === 'object') {
                if (Array.isArray(candidate.sliders)) {
                    return candidate.sliders.length > 0 ? candidate.sliders[0] : null
                }
                return candidate
            }
            return null
        },
        title() {
            const val = this.ctaData?.title
            return typeof val === 'string' && val.trim() ? val.trim() : ''
        },
        description() {
            const val = this.ctaData?.description
            return typeof val === 'string' && val.trim() ? val.trim() : ''
        },
        ctaText() {
            const val = this.ctaData?.primary_cta_text
            return typeof val === 'string' && val.trim() ? val.trim() : ''
        },
        ctaLink() {
            const raw = this.ctaData?.primary_cta_link || this.ctaData?.link
            const trimmed = typeof raw === 'string' ? raw.trim() : ''
            if (trimmed && trimmed !== '#') return trimmed
            return this.safeRoute('contact', undefined, this.isEn ? '/en/contact' : '/lien-he')
        },
        hasCtaData() {
            if (!this.ctaData || typeof this.ctaData !== 'object') return false
            return Boolean(this.title || this.description || this.ctaText)
        },
    },
    methods: {
        safeRoute(name, params, fallback = '/') {
            if (typeof this.route === 'function' && typeof this.routes === 'function') {
                const allRoutes = this.routes() || {}
                const localizedName =
                    typeof this.routeLocaleName === 'function' ? this.routeLocaleName(name) : name
                if (allRoutes[localizedName] || allRoutes[name]) {
                    try {
                        const url = this.route(name, params)
                        if (url && url !== '#') return url
                    } catch (e) {
                        // Fall through to fallback
                    }
                }
            }
            return fallback
        },
    },
}
</script>
