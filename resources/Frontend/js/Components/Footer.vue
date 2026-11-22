<template>
    <footer id="footer-section" class="w-full bg-surface text-ink-body border-t border-gray-200 antialiased">
        <!-- Footer Top Content (Figma 595:15535 — bg #FAFAFA, pt-10 pb-[60px]) -->
        <div class="app-container-wide pt-10 pb-12 lg:pb-[60px]">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-10 lg:gap-12 xl:gap-16">
                <!-- Left Brand Column (lg:w-[360px], Logo 203x68) -->
                <div class="w-full lg:w-[360px] shrink-0 space-y-4">
                    <Link
                        v-if="resolvedLogoUrl"
                        :href="safeRoute('home', undefined, '/')"
                        class="inline-block rounded-cta focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                        :aria-label="companyName || tt('Trang chủ')"
                    >
                        <img
                            :src="resolvedLogoUrl"
                            :alt="companyName || 'Thanh Khang Tecsat'"
                            width="203"
                            height="68"
                            class="h-[52px] sm:h-[60px] lg:h-[68px] w-auto max-w-[240px] object-contain"
                            @error="onLogoError"
                        />
                    </Link>

                    <div v-if="companyName || companyDescription" class="space-y-2.5">
                        <h3 v-if="companyName" class="text-[18px] lg:text-[20px] font-bold leading-[1.4] text-dark">
                            {{ companyName }}
                        </h3>
                        <p
                            v-if="companyDescription"
                            class="text-[14px] font-medium leading-[1.6] text-ink-body"
                        >
                            {{ companyDescription }}
                        </p>
                    </div>
                </div>

                <!-- Right 3 Columns (Trang chủ | Chính sách | Thông tin liên hệ) -->
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-8 xl:gap-12">
                    <!-- Column 1: Trang chủ -->
                    <div class="lg:col-span-3 space-y-4">
                        <h4 class="text-[16px] font-bold leading-[1.5] text-dark">
                            {{ tt('Trang chủ') }}
                        </h4>
                        <ul class="space-y-3 text-[14px] font-medium leading-[1.6]">
                            <li v-for="(item, index) in navLinks" :key="index">
                                <Link
                                    :href="item.href"
                                    class="text-ink-body hover:text-primary transition-colors focus:outline-none focus-visible:underline"
                                >
                                    {{ item.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 2: Chính sách (100% Dynamic from $page.props.data.policies — zero fake fallback) -->
                    <div v-if="policyLinks.length > 0" class="lg:col-span-4 space-y-4">
                        <h4 class="text-[16px] font-bold leading-[1.5] text-dark">
                            {{ tt('Chính sách') }}
                        </h4>
                        <ul class="space-y-3 text-[14px] font-medium leading-[1.6]">
                            <li v-for="(item, index) in policyLinks" :key="index">
                                <Link
                                    :href="item.url"
                                    class="text-ink-body hover:text-primary transition-colors focus:outline-none focus-visible:underline"
                                >
                                    {{ item.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 3: Thông tin liên hệ (Bound to $page.props.configs || $page.props.global) -->
                    <div class="sm:col-span-2 lg:col-span-5 space-y-4">
                        <h4 class="text-[16px] font-bold leading-[1.5] text-dark">
                            {{ tt('Thông tin liên hệ') }}
                        </h4>
                        <div class="space-y-3.5 text-[14px] font-medium leading-[1.6] text-ink-body">
                            <a
                                v-if="displayHotline"
                                :href="'tel:' + cleanHotline"
                                class="flex items-start gap-3 text-ink-body hover:text-primary transition-colors focus:outline-none focus-visible:underline"
                            >
                                <img
                                    src="/assets/svg/icons/footer-phone.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-6 h-6 shrink-0 mt-0.5"
                                />
                                <span class="break-words">{{ displayHotline }}</span>
                            </a>

                            <a
                                v-if="displayEmail"
                                :href="'mailto:' + displayEmail"
                                class="flex items-start gap-3 text-ink-body hover:text-primary transition-colors focus:outline-none focus-visible:underline"
                            >
                                <img
                                    src="/assets/svg/icons/footer-mail.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-6 h-6 shrink-0 mt-0.5"
                                />
                                <span class="break-all sm:break-words">{{ displayEmail }}</span>
                            </a>

                            <div v-if="displayAddress || displayShowroomAddress" class="flex items-start gap-3">
                                <img
                                    src="/assets/svg/icons/footer-pin.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-6 h-6 shrink-0 mt-0.5"
                                />
                                <div class="space-y-1.5 min-w-0">
                                    <p v-if="displayAddress" class="break-words">
                                        <span class="font-semibold text-dark">{{ tt('Trụ sở') }}:</span>
                                        {{ displayAddress }}
                                    </p>
                                    <p v-if="displayShowroomAddress" class="break-words">
                                        <span class="font-semibold text-dark">{{ tt('VPGD/Showroom') }}:</span>
                                        {{ displayShowroomAddress }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar (Figma 595:15535 — bg #00284B, py-3) -->
        <div class="w-full bg-dark py-3">
            <div class="app-container-wide flex flex-col sm:flex-row items-center justify-between gap-3">
                <p
                    v-if="companyName"
                    class="text-[12px] font-normal leading-[1.6] text-secondary text-center sm:text-left"
                >
                    &copy; Copyright {{ currentYear }} {{ companyName }}
                </p>

                <div v-if="facebookHref || linkedinHref" class="flex items-center gap-4">
                    <a
                        v-if="facebookHref"
                        :href="facebookHref"
                        :target="isExternalUrl(facebookHref) ? '_blank' : undefined"
                        :rel="isExternalUrl(facebookHref) ? 'noopener noreferrer' : undefined"
                        class="w-8 h-8 inline-flex items-center justify-center rounded-cta hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                        aria-label="Facebook"
                    >
                        <img
                            src="/assets/svg/icons/footer-facebook.svg"
                            alt=""
                            aria-hidden="true"
                            width="20"
                            height="20"
                            class="w-5 h-5 object-contain"
                        />
                    </a>

                    <a
                        v-if="linkedinHref"
                        :href="linkedinHref"
                        :target="isExternalUrl(linkedinHref) ? '_blank' : undefined"
                        :rel="isExternalUrl(linkedinHref) ? 'noopener noreferrer' : undefined"
                        class="w-8 h-8 inline-flex items-center justify-center rounded-cta hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                        aria-label="LinkedIn"
                    >
                        <img
                            src="/assets/svg/icons/footer-linkedin.svg"
                            alt=""
                            aria-hidden="true"
                            width="20"
                            height="20"
                            class="w-5 h-5 object-contain"
                        />
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

const DEFAULT_LOGO_URL = '/assets/images/logo/logo-thanhkhang.png'

export default {
    name: 'Footer',
    components: {
        Link,
    },
    props: {
        address: {
            type: String,
            default: '',
        },
        hotline: {
            type: String,
            default: '',
        },
        email: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            currentYear: new Date().getFullYear(),
            logoFallbackStage: 0,
        }
    },
    computed: {
        currentLocale() {
            return this.$inertia?.page?.props?.locale?.current || this.$page?.props?.locale?.current || 'vi'
        },
        siteConfig() {
            return {
                ...(this.$page?.props?.global || {}),
                ...(this.$page?.props?.configs || {}),
            }
        },
        cmsLogoUrl() {
            const raw = this.siteConfig.general_logo || this.siteConfig.logo || ''
            return typeof raw === 'string' ? raw.trim() : ''
        },
        resolvedLogoUrl() {
            if (this.logoFallbackStage >= 2) return ''
            if (this.logoFallbackStage === 0 && this.cmsLogoUrl) {
                return this.cmsLogoUrl
            }
            return DEFAULT_LOGO_URL
        },
        companyName() {
            return (
                this.siteConfig.general_business_name ||
                this.siteConfig.company_name ||
                this.siteConfig.general_site_name ||
                ''
            )
        },
        companyDescription() {
            return (
                this.siteConfig.general_description ||
                this.siteConfig.company_description ||
                this.siteConfig.general_site_name ||
                this.siteConfig.site_name ||
                ''
            )
        },
        displayHotline() {
            return this.hotline || this.siteConfig.hotline || this.siteConfig.general_phone || this.siteConfig.phone || ''
        },
        cleanHotline() {
            return String(this.displayHotline || '').replace(/[^\d+]/g, '')
        },
        displayEmail() {
            return this.email || this.siteConfig.general_email || this.siteConfig.email || ''
        },
        displayAddress() {
            return this.address || this.siteConfig.general_address || this.siteConfig.address || ''
        },
        displayShowroomAddress() {
            const showroom = this.siteConfig.showroom_address || ''
            if (!showroom || showroom === this.displayAddress) return ''
            return showroom
        },
        facebookHref() {
            return (
                this.siteConfig.general_facebook ||
                this.siteConfig.facebook ||
                this.siteConfig.social_facebook ||
                ''
            )
        },
        linkedinHref() {
            return (
                this.siteConfig.general_linkedin ||
                this.siteConfig.linkedin ||
                this.siteConfig.social_linkedin ||
                ''
            )
        },
        navLinks() {
            const isEn = this.currentLocale === 'en'

            return [
                {
                    title: this.tt('Sản phẩm'),
                    href: this.safeRoute('products.index', undefined, isEn ? '/en/products' : '/san-pham'),
                },
                {
                    title: this.tt('Giới thiệu'),
                    href: this.safeRoute('histories.index', undefined, isEn ? '/en/about-us' : '/gioi-thieu'),
                },
                {
                    title: this.tt('Quy trình'),
                    href: this.safeRoute('process.index', undefined, isEn ? '/en/process' : '/quy-trinh'),
                },
                {
                    title: this.tt('Tin tức'),
                    href: this.safeRoute('posts', undefined, isEn ? '/en/posts' : '/tin-tuc'),
                },
                {
                    title: this.tt('Liên hệ'),
                    href: this.safeRoute('contact', undefined, isEn ? '/en/contact' : '/lien-he'),
                },
            ]
        },
        policyLinks() {
            const cmsPolicies = this.$page?.props?.data?.policies || []

            if (!Array.isArray(cmsPolicies) || cmsPolicies.length === 0) {
                return []
            }

            return cmsPolicies
                .filter((item) => item && (item.title || item.name))
                .map((item) => ({
                    title: item.title || item.name,
                    url: item.url || this.getPolicyUrl(item.seo_slug || item.slug),
                }))
        },
    },
    watch: {
        cmsLogoUrl() {
            this.logoFallbackStage = 0
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
        getPolicyUrl(slug) {
            if (slug) {
                return this.safeRoute(
                    'policies.show',
                    slug,
                    this.currentLocale === 'en' ? `/en/policies/${slug}` : `/chinh-sach/${slug}`
                )
            }
            return this.safeRoute('policies.index', undefined, '/chinh-sach')
        },
        isExternalUrl(url) {
            return typeof url === 'string' && /^https?:\/\//i.test(url)
        },
        onLogoError() {
            if (this.logoFallbackStage === 0 && this.cmsLogoUrl && this.cmsLogoUrl !== DEFAULT_LOGO_URL) {
                this.logoFallbackStage = 1
            } else {
                this.logoFallbackStage = 2
            }
        },
    },
}
</script>
