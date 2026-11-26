<template>
    <footer id="footer-section" class="w-full bg-surface text-ink-body border-t border-gray-200 antialiased">
        <!-- Footer Top Content (Figma Desktop 979:32406 / 217:3913 & Mobile 1193:6561 — bg #FAFAFA, 1280px inner) -->
        <div class="app-container-wide pt-9 pb-8 lg:pt-[40px] lg:pb-[60px]">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 lg:gap-12 xl:gap-[120px]">
                <!-- Left Brand Column (Mobile 1193:6563: Logo 138x46; Desktop 217:3855: w-[360px], Logo 203x68) -->
                <div class="w-full lg:w-[360px] shrink-0 space-y-3 lg:space-y-5">
                    <Link
                        v-if="resolvedLogoUrl"
                        :href="safeRoute('home', undefined, currentLocale === 'en' ? '/en' : '/')"
                        class="inline-block rounded-cta focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                        :aria-label="companyName || tt('Trang chủ')"
                    >
                        <img
                            :src="resolvedLogoUrl"
                            :alt="companyName || ''"
                            width="203"
                            height="68"
                            class="w-[138px] h-[46px] lg:w-[203px] lg:h-[68px] object-contain object-left"
                            @error="onLogoError"
                        />
                    </Link>

                    <div v-if="companyName || companyDescription" class="space-y-1.5 lg:space-y-1">
                        <h3
                            v-if="companyName"
                            class="uppercase text-[14px] leading-[21px] lg:text-[20px] lg:leading-[32px] font-bold text-dark"
                        >
                            {{ companyName }}
                        </h3>
                        <p
                            v-if="companyDescription"
                            class="text-[13px] leading-[21px] font-normal lg:text-[14px] lg:leading-[22px] lg:font-medium text-ink-body"
                        >
                            {{ companyDescription }}
                        </p>
                    </div>
                </div>

                <!-- Right 3 Columns (Mobile 1193:6567: 2-col grid + 1px divider + full-width Contact; Desktop 217:3866: 3 equal ~267px cols) -->
                <div class="w-full lg:max-w-[800px] flex-1 grid grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-6 lg:gap-8">
                    <!-- Column 1: Danh mục sản phẩm (Figma 217:3867 & 1193:6568 — 100% Dynamic from CMS product_categories) -->
                    <div
                        v-if="footerCategoryLinks && footerCategoryLinks.length"
                        class="col-span-1 space-y-3 lg:space-y-6 min-w-0"
                    >
                        <h4 class="font-inter text-[14px] leading-[20px] lg:text-[16px] lg:leading-[22px] font-bold text-dark">
                            {{ tt('Danh mục sản phẩm') }}
                        </h4>
                        <ul class="space-y-2.5 lg:space-y-4 font-inter text-[13px] leading-[20px] lg:text-[14px] lg:leading-[20px] font-normal lg:font-medium">
                            <li
                                v-for="(item, index) in footerCategoryLinks"
                                :key="index"
                            >
                                <Link
                                    :href="item.href"
                                    class="text-ink-body hover:text-primary transition-colors focus:outline-none focus-visible:underline block"
                                >
                                    {{ item.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 2: Chính sách (Figma 217:3875 & 1193:6576 — 100% Dynamic from CMS policies) -->
                    <div v-if="policyLinks.length > 0" class="col-span-1 space-y-3 lg:space-y-6 min-w-0">
                        <h4 class="font-inter text-[14px] leading-[20px] lg:text-[16px] lg:leading-[22px] font-bold text-dark">
                            {{ tt('Chính sách') }}
                        </h4>
                        <ul class="space-y-2.5 lg:space-y-4 font-inter text-[13px] leading-[20px] lg:text-[14px] lg:leading-[20px] font-normal lg:font-medium">
                            <li v-for="(item, index) in policyLinks" :key="index">
                                <Link
                                    :href="item.url"
                                    class="text-ink-body hover:text-primary transition-colors focus:outline-none focus-visible:underline block"
                                >
                                    {{ item.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Mobile Horizontal Divider (Figma 1193:6585 — 1px #E3E8EF) -->
                    <div
                        v-if="(footerCategoryLinks && footerCategoryLinks.length) || policyLinks.length > 0"
                        class="col-span-2 h-px bg-[#E3E8EF] lg:hidden"
                        aria-hidden="true"
                    />

                    <!-- Column 3: Thông tin liên hệ (Figma 217:3884 & 1193:6586 — 5 CMS rows including Mã số thuế with building-07) -->
                    <div class="col-span-2 lg:col-span-1 space-y-3 lg:space-y-6 min-w-0">
                        <h4 class="text-[14px] leading-[20px] lg:text-[16px] lg:leading-[22px] font-bold text-dark">
                            {{ tt('Thông tin liên hệ') }}
                        </h4>
                        <div class="space-y-3 text-[13px] leading-[20px] text-dark-slate lg:text-[14px] lg:leading-[22px] lg:font-medium lg:text-ink-body">
                            <a
                                v-if="displayHotline"
                                :href="'tel:' + cleanHotline"
                                class="flex items-start gap-2.5 lg:gap-2 hover:text-primary transition-colors focus:outline-none focus-visible:underline"
                            >
                                <img
                                    src="/assets/svg/icons/footer-phone.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-[18px] h-[18px] lg:w-6 lg:h-6 shrink-0 mt-0.5 lg:mt-0"
                                />
                                <span class="break-words min-w-0">{{ tt('Hotline') }}: {{ displayHotline }}</span>
                            </a>

                            <a
                                v-if="displayEmail"
                                :href="'mailto:' + displayEmail"
                                class="flex items-start gap-2.5 lg:gap-2 hover:text-primary transition-colors focus:outline-none focus-visible:underline"
                            >
                                <img
                                    src="/assets/svg/icons/footer-mail.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-[18px] h-[18px] lg:w-6 lg:h-6 shrink-0 mt-0.5 lg:mt-0"
                                />
                                <span class="break-all sm:break-words min-w-0">{{ tt('Email') }}: {{ displayEmail }}</span>
                            </a>

                            <div v-if="taxCode" class="flex items-start gap-2.5 lg:gap-2">
                                <img
                                    src="/assets/svg/icons/footer-building.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-[18px] h-[18px] lg:w-6 lg:h-6 shrink-0 mt-0.5 lg:mt-0"
                                />
                                <p class="break-words min-w-0">{{ tt('Mã số thuế') }}: {{ taxCode }}</p>
                            </div>

                            <div v-for="row in addressRows" :key="row.label" class="flex items-start gap-2.5 lg:gap-2">
                                <img
                                    src="/assets/svg/icons/footer-pin.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-[18px] h-[18px] lg:w-6 lg:h-6 shrink-0 mt-0.5 lg:mt-0"
                                />
                                <p class="break-words min-w-0">{{ row.label }}: {{ row.value }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar (Figma Desktop 217:3899: 51px #00284B; Mobile 1193:6613: 48px #00284B horizontal row) -->
        <div class="w-full bg-dark min-h-[48px] lg:min-h-[51px] flex items-center">
            <div class="app-container-wide py-3 flex flex-row items-center justify-between gap-3">
                <p
                    v-if="copyrightBrandName"
                    class="text-[12px] font-normal leading-[18px] lg:leading-[19px] text-secondary min-w-0 truncate"
                >
                    &copy; Copyright {{ currentYear }} {{ copyrightBrandName }}
                </p>
                <span v-else />

                <div v-if="facebookHref || linkedinHref" class="flex items-center gap-4 lg:gap-6 shrink-0">
                    <a
                        v-if="facebookHref"
                        :href="facebookHref"
                        :target="isExternalUrl(facebookHref) ? '_blank' : undefined"
                        :rel="isExternalUrl(facebookHref) ? 'noopener noreferrer' : undefined"
                        class="w-7 h-7 lg:w-8 lg:h-8 inline-flex items-center justify-center rounded-cta hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
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
                        class="w-7 h-7 lg:w-8 lg:h-8 inline-flex items-center justify-center rounded-cta hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
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
            if (this.logoFallbackStage > 0) return ''
            return this.cmsLogoUrl
        },
        companyName() {
            return (
                this.siteConfig.general_business_name ||
                this.siteConfig.company_name ||
                this.siteConfig.general_site_name ||
                ''
            )
        },
        copyrightBrandName() {
            return (
                (typeof this.siteConfig.copyright_name === 'string' && this.siteConfig.copyright_name.trim()) ||
                (typeof this.siteConfig.general_site_name === 'string' && this.siteConfig.general_site_name.trim()) ||
                (typeof this.companyName === 'string' && this.companyName.trim()) ||
                ''
            )
        },
        companyDescription() {
            return (
                this.siteConfig.general_description ||
                this.siteConfig.description ||
                this.siteConfig.company_description ||
                this.siteConfig.general_site_name ||
                this.siteConfig.site_name ||
                ''
            )
        },
        taxCode() {
            return this.siteConfig.tax_code || this.siteConfig.general_tax_code || this.siteConfig.mst || ''
        },
        footerCategoryLinks() {
            const raw = Array.isArray(this.siteConfig?.product_categories)
                ? this.siteConfig.product_categories
                : this.$page?.props?.product_categories || this.$page?.props?.data?.product_categories || []
            if (!Array.isArray(raw) || raw.length === 0) return []
            const base = this.safeRoute(
                'products.index',
                undefined,
                this.currentLocale === 'en' ? '/en/products' : '/san-pham'
            )
            return raw
                .filter((cat) => cat && (cat.title || cat.name) && !(Number(cat.parent_id) > 0))
                .slice(0, 6)
                .map((cat) => ({
                    title: cat.title || cat.name,
                    href:
                        (typeof cat.url === 'string' && cat.url.trim()) ||
                        (cat.slug ? `${base}?category=${encodeURIComponent(cat.slug)}` : base),
                }))
        },
        displayHotline() {
            return (
                this.hotline ||
                this.siteConfig.hotline ||
                this.siteConfig.general_phone ||
                this.siteConfig.phone ||
                ''
            )
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
        addressRows() {
            return [
                { label: this.tt('Trụ sở'), value: this.displayAddress },
                { label: this.tt('VPGD & Showroom'), value: this.displayShowroomAddress },
            ].filter((row) => row.value)
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
        policyLinks() {
            const cmsPolicies =
                this.$page?.props?.configs?.policies || this.$page?.props?.data?.policies || []

            if (!Array.isArray(cmsPolicies) || cmsPolicies.length === 0) {
                return []
            }

            return cmsPolicies
                .filter((item) => item && (item.title || item.name))
                .map((item) => ({
                    title: item.title || item.name,
                    url:
                        (typeof item.url === 'string' && item.url.trim()) ||
                        this.getPolicyUrl(item.seo_slug || item.slug),
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
            this.logoFallbackStage = 1
        },
    },
}
</script>
