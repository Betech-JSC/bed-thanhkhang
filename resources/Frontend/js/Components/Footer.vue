<template>
    <footer id="footer-section" class="w-full bg-[#FAFAFA] text-[#4B5565] antialiased">
        <!-- Footer Top Content (Aligned 100% flush to .app-container-narrow 1152px — bg #FAFAFA, pt 40px, pb 60px) -->
        <div class="app-container-narrow pt-9 pb-8 lg:pt-[40px] lg:pb-[60px]">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 lg:gap-10 xl:gap-[60px]">
                <!-- Left Brand Column + CTA (w-[360px] on 1440px matching Figma 217:3855, Logo 203x68) -->
                <div class="w-full lg:w-[300px] xl:w-[360px] shrink-0 flex flex-col gap-3 lg:gap-[20px]">
                    <Link
                        v-if="resolvedLogoUrl"
                        :href="safeRoute('home', undefined, currentLocale === 'en' ? '/en' : '/')"
                        class="inline-block w-fit rounded-cta focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
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

                    <div v-if="companyName || companyDescription" class="space-y-1.5 lg:space-y-[4px]">
                        <h3
                            v-if="companyName"
                            class="uppercase text-[14px] leading-[1.6] lg:text-[20px] lg:leading-[1.6] font-bold text-[#00284B]"
                        >
                            {{ companyName }}
                        </h3>
                        <p
                            v-if="companyDescription"
                            class="text-[13px] leading-[1.6] font-normal lg:text-[14px] lg:leading-[1.6] lg:font-medium text-[#4B5565]"
                        >
                            {{ companyDescription }}
                        </p>
                    </div>

                    <!-- Footer Primary CTA (Desktop >= lg only, hidden on Mobile per Figma 1314:52197) -->
                    <div class="pt-1 hidden lg:flex flex-wrap items-center gap-[12px]">
                        <button
                            type="button"
                            @click="handleOpenQuoteModal"
                            class="h-[38px] px-[20px] py-[8px] inline-flex items-center justify-center whitespace-nowrap rounded-[4px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] text-[14px] font-medium leading-[1.6] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2 cursor-pointer"
                        >
                            {{ tt('Nhận báo giá') }}
                        </button>
                        <a
                            v-if="displayHotline"
                            :href="'tel:' + cleanHotline"
                            class="h-[38px] px-[20px] py-[8px] inline-flex items-center justify-center whitespace-nowrap rounded-[4px] bg-[#C9E0FF] hover:bg-[#81BDFF] text-[#003967] text-[14px] font-medium leading-[1.6] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                        >
                            {{ tt('Gọi hotline') }}
                        </a>
                    </div>
                </div>

                <!-- Right 3 Columns (732px on 1440px: 212px + 20px + 212px + 20px + 268px; total 360 + 60 + 732 = 1152px — Inter on headings & Col 1-2 links) -->
                <div class="w-full lg:flex-1 grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-[212px_212px_268px] gap-[20px] lg:gap-6 xl:gap-x-[20px]">
                    <!-- Column 1: Danh mục sản phẩm (Inter 700 16px/1.4 heading, Inter 500 14px/20px links) -->
                    <div
                        v-if="footerCategoryLinks && footerCategoryLinks.length"
                        class="col-span-1 space-y-3 lg:space-y-[24px] min-w-0 font-inter"
                    >
                        <h4 class="font-inter text-[14px] leading-[1.4] lg:text-[16px] lg:leading-[1.4] font-bold text-[#00284B]">
                            {{ tt('Danh mục sản phẩm') }}
                        </h4>
                        <ul class="font-inter space-y-2.5 lg:space-y-[16px] text-[13px] leading-[20px] lg:text-[14px] lg:leading-[20px] font-normal lg:font-medium">
                            <li
                                v-for="(item, index) in footerCategoryLinks"
                                :key="index"
                            >
                                <Link
                                    :href="item.href"
                                    class="text-[#4B5565] hover:text-[#005EA4] transition-colors focus:outline-none focus-visible:underline block"
                                >
                                    {{ item.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 2: Chính sách (Inter 700 16px/1.4 heading, Inter 500 14px/20px links) -->
                    <div v-if="policyLinks.length > 0" class="col-span-1 space-y-3 lg:space-y-[24px] min-w-0 font-inter">
                        <h4 class="font-inter text-[14px] leading-[1.4] lg:text-[16px] lg:leading-[1.4] font-bold text-[#00284B]">
                            {{ tt('Chính sách') }}
                        </h4>
                        <ul class="font-inter space-y-2.5 lg:space-y-[16px] text-[13px] leading-[20px] lg:text-[14px] lg:leading-[20px] font-normal lg:font-medium">
                            <li v-for="(item, index) in policyLinks" :key="index">
                                <Link
                                    :href="item.url"
                                    class="text-[#4B5565] hover:text-[#005EA4] transition-colors focus:outline-none focus-visible:underline block"
                                >
                                    {{ item.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 3: Thông tin liên hệ (Inter 700 16px/1.4 heading; Open Sans 500 14px/1.6 items) -->
                    <div class="col-span-1 space-y-3 lg:space-y-[24px] min-w-0">
                        <h4 class="font-inter text-[14px] leading-[1.4] lg:text-[16px] lg:leading-[1.4] font-bold text-[#00284B]">
                            {{ tt('Thông tin liên hệ') }}
                        </h4>
                        <div class="font-sans space-y-[12px] text-[13px] leading-[1.6] text-[#364152] lg:text-[14px] lg:leading-[1.6] lg:font-medium lg:text-[#4B5565]">
                            <a
                                v-if="displayHotline"
                                :href="'tel:' + cleanHotline"
                                class="flex items-center gap-2.5 lg:gap-[8px] hover:text-[#005EA4] transition-colors focus:outline-none focus-visible:underline"
                            >
                                <img
                                    src="/assets/svg/icons/footer-phone.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-[18px] h-[18px] lg:w-6 lg:h-6 shrink-0"
                                />
                                <span class="break-words min-w-0">{{ tt('Hotline') }}: {{ displayHotline }}</span>
                            </a>

                            <a
                                v-if="displayEmail"
                                :href="'mailto:' + displayEmail"
                                class="flex items-center gap-2.5 lg:gap-[8px] hover:text-[#005EA4] transition-colors focus:outline-none focus-visible:underline"
                            >
                                <img
                                    src="/assets/svg/icons/footer-mail.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-[18px] h-[18px] lg:w-6 lg:h-6 shrink-0"
                                />
                                <span class="break-all sm:break-words min-w-0">{{ tt('Email') }}: {{ displayEmail }}</span>
                            </a>

                            <div v-if="taxCode" class="flex items-center gap-2.5 lg:gap-[8px]">
                                <img
                                    src="/assets/svg/icons/footer-building.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-[18px] h-[18px] lg:w-6 lg:h-6 shrink-0"
                                />
                                <p class="break-words min-w-0">{{ tt('Mã số thuế') }}: {{ taxCode }}</p>
                            </div>

                            <div v-for="row in addressRows" :key="row.label" class="flex items-start gap-2.5 lg:gap-[8px]">
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

        <!-- Bottom Bar (Figma 217:3899: exact 51px #00284B, flush with .app-container-narrow) -->
        <div class="w-full bg-[#00284B] min-h-[48px] lg:h-[51px] flex items-center">
            <div class="app-container-narrow py-[12px] flex flex-row items-center justify-between gap-3">
                <p
                    v-if="copyrightBrandName"
                    class="py-[4px] text-[12px] font-normal leading-[1.6] text-[#C9E0FF] min-w-0 truncate"
                >
                    &copy; Copyright {{ currentYear }} {{ copyrightBrandName }}
                </p>
                <span v-else />

                <div v-if="facebookHref || linkedinHref" class="flex items-center gap-1 lg:gap-[24px] shrink-0">
                    <a
                        v-if="facebookHref"
                        :href="facebookHref"
                        :target="isExternalUrl(facebookHref) ? '_blank' : undefined"
                        :rel="isExternalUrl(facebookHref) ? 'noopener noreferrer' : undefined"
                        class="min-w-[44px] min-h-[44px] -my-3 lg:min-w-0 lg:min-h-0 lg:my-0 lg:w-5 lg:h-5 inline-flex items-center justify-center rounded-cta hover:opacity-80 transition-opacity focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9E0FF] focus-visible:ring-offset-2 focus-visible:ring-offset-[#00284B]"
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
                        class="min-w-[44px] min-h-[44px] -my-3 lg:min-w-0 lg:min-h-0 lg:my-0 lg:w-5 lg:h-5 inline-flex items-center justify-center rounded-cta hover:opacity-80 transition-opacity focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9E0FF] focus-visible:ring-offset-2 focus-visible:ring-offset-[#00284B]"
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
import { openQuoteModal } from '@/composables/useQuoteModal'

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
            const pageUrl = String(this.$page?.url || '').split('?')[0].split('#')[0]
            const urlLocale = pageUrl === '/en' || pageUrl.startsWith('/en/') ? 'en' : ''
            const raw =
                this.$page?.props?.locale?.current ||
                urlLocale ||
                this.$inertia?.page?.props?.locale?.current ||
                'vi'
            return String(raw).toLowerCase()
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
        contactHref() {
            const isEn = this.currentLocale === 'en'
            return this.safeRoute('contact', undefined, isEn ? '/en/contact' : '/lien-he')
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
        handleOpenQuoteModal() {
            openQuoteModal({ source: 'footer_cta' })
        },
    },
}
</script>
