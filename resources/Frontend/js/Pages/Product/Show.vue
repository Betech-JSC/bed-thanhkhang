<template>
    <div
        :class="[
            currentTitle ? 'bg-[#111322] text-[#FAFAFA]' : 'bg-[#FAFAFA] text-[#364152]',
            'min-h-screen font-sans antialiased selection:bg-[#2970FF] selection:text-white',
        ]"
    >
        <!-- SEO HEAD -->
        <Head>
            <title v-if="headTitle">{{ headTitle }}</title>
            <meta v-if="currentDescription" name="description" :content="currentDescription" />
            <meta v-if="currentTitle" property="og:title" :content="currentTitle" />
            <meta v-if="currentDescription" property="og:description" :content="currentDescription" />
            <meta v-if="ogImageUrl" property="og:image" :content="ogImageUrl" />
            <meta property="og:type" content="product" />
            <component
                v-if="productJsonLd"
                :is="'script'"
                type="application/ld+json"
                v-html="JSON.stringify(productJsonLd)"
            />
            <component
                v-if="breadcrumbJsonLd"
                :is="'script'"
                type="application/ld+json"
                v-html="JSON.stringify(breadcrumbJsonLd)"
            />
        </Head>

        <!-- EMPTY STATE KHI KHÔNG CÓ DỮ LIỆU SẢN PHẨM -->
        <section v-if="!currentTitle" class="bg-[#FAFAFA] py-12 md:py-20">
            <div class="app-container-narrow">
                <EmptyState
                    variant="light"
                    heading-tag="h1"
                    :title="tt('Sản phẩm không tồn tại hoặc đang được cập nhật')"
                    :description="tt('Quý khách vui lòng quay lại danh mục sản phẩm hoặc liên hệ trực tiếp để được đội ngũ kỹ sư hỗ trợ.')"
                    :primary-action-text="tt('Xem tất cả sản phẩm')"
                    :primary-action-href="productsIndexUrl"
                    :secondary-action-text="tt('Liên hệ tư vấn')"
                    :secondary-action-href="contactUrl"
                />
            </div>
        </section>

        <template v-else>
            <!-- 1. BREADCRUMBS & SECTION TỔNG QUAN SẢN PHẨM (FIGMA 2358:2979: 1440x713 px, padding 24px 144px 40px) -->
            <div class="app-container pt-6 pb-10">
                <!-- ĐƯỜNG DẪN BREADCRUMB (FIGMA 2358:2980) -->
                <nav
                    class="flex items-center gap-2.5 text-sm leading-[17px] text-[#B9C0D4] mb-6 overflow-x-auto scrollbar-none whitespace-nowrap"
                    :aria-label="tt('Breadcrumb')"
                >
                    <Link :href="homeUrl" class="hover:text-white transition-colors leading-[17px]">
                        {{ tt('Trang chủ') }}
                    </Link>
                    <img src="/images/pdp/chevron_right.svg" alt="" aria-hidden="true" class="w-3.5 h-3.5 opacity-60" />
                    <Link :href="productsIndexUrl" class="hover:text-white transition-colors leading-[17px]">
                        {{ tt('Sản phẩm') }}
                    </Link>
                    <template v-if="currentCategory">
                        <img src="/images/pdp/chevron_right.svg" alt="" aria-hidden="true" class="w-3.5 h-3.5 opacity-60" />
                        <Link :href="categoryUrl" class="hover:text-white transition-colors leading-[17px]">
                            {{ currentCategory }}
                        </Link>
                    </template>
                    <img src="/images/pdp/chevron_right.svg" alt="" aria-hidden="true" class="w-3.5 h-3.5 opacity-60" />
                    <span class="text-[#FAFAFA] font-normal leading-[17px] truncate max-w-[200px] sm:max-w-none" aria-current="page">
                        {{ currentTitle }}
                    </span>
                </nav>

                <!-- HÌNH ẢNH VÀ THÔNG TIN (FIGMA 2358:2986: 1152x600 px, gap 48px) -->
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-[48px] items-start justify-between">
                    <!-- KHỐI GALLERY HÌNH ẢNH BÊN TRÁI (FIGMA 2390:5336: 624x600 px) -->
                    <div class="w-full lg:w-[624px] shrink-0">
                        <ProductGallery
                            :images="productImages"
                            :product-title="currentTitle"
                        />
                    </div>

                    <!-- KHỐI THÔNG TIN SẢN PHẨM BÊN PHẢI (FIGMA 2358:3003: 480x473 px, NẰM TRỰC TIẾP TRÊN NỀN, KHÔNG ĐÓNG KHUNG CARD) -->
                    <div class="w-full lg:w-[480px] shrink-0 flex flex-col justify-between gap-6">
                        <!-- TÊN VÀ GIỚI THIỆU (FIGMA 2358:3004: gap 12px) -->
                        <div class="flex flex-col gap-3">
                            <!-- TÊN SẢN PHẨM (FIGMA 2358:3006: 38px Bold, color #FAFAFA, leading-[46px]) -->
                            <h1 class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] tracking-tight leading-tight lg:leading-[46px]">
                                {{ currentTitle }}
                            </h1>

                            <!-- PHÂN LOẠI BADGES (FIGMA 2358:3007: gap 8px) -->
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center h-[33px] px-3 py-2 rounded-[4px] text-xs font-normal leading-[17px] border border-[#5D6B98] text-[#84ADFF]">
                                    {{ tt('Chuẩn OEM / ISO') }}
                                </span>
                                <span class="inline-flex items-center justify-center h-[33px] px-3 py-2 rounded-[4px] text-xs font-normal leading-[17px] border border-[#5D6B98] text-[#84ADFF]">
                                    {{ tt('Cung ứng theo thông số') }}
                                </span>
                            </div>

                            <!-- GIỚI THIỆU NGẮN (FIGMA 2358:3012: 16px, color #B9C0D4, leading-[22px]) -->
                            <p v-if="currentDescription" class="text-base text-[#B9C0D4] leading-[22px]">
                                {{ currentDescription }}
                            </p>
                        </div>

                        <!-- LỰA CHỌN THAM KHẢO (FIGMA 2358:3013: border-t border-[#404968], py-5) -->
                        <VariantSelector
                            :material="productMaterial"
                            :color="productColor"
                            :customization="productCustomization"
                        />

                        <!-- YÊU CẦU TƯ VẤN (FIGMA 2358:3025) -->
                        <div class="flex flex-col gap-3">
                            <!-- NÚT DUY NHẤT THEO FIGMA: NHẬN TƯ VẤN SẢN PHẨM (FIGMA 2358:3027) -->
                            <button
                                type="button"
                                @click="handleRequestQuote"
                                class="w-full h-12 rounded-lg text-[#FAFAFA] font-medium text-base transition-colors duration-200 active:scale-[0.98] flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/30 hover:opacity-95"
                                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
                            >
                                {{ tt('Nhận tư vấn sản phẩm') }}
                            </button>

                            <!-- LƯU Ý TƯ VẤN -->
                            <p class="text-xs text-[#B9C0D4] leading-[19px]">
                                {{ tt('Chia sẻ mã linh kiện, bước xích và số lượng cần cung ứng. Thành Khang Tecsat sẽ tư vấn phương án kỹ thuật và báo giá B2B phù hợp.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. CHI TIẾT VÀ THÔNG SỐ (FIGMA 2358:3035: 1440x604 px, padding 0px 144px 64px) -->
            <section v-if="tabs.length > 0" v-scroll-reveal class="app-container pt-0 pb-16">
                <!-- ĐIỀU HƯỚNG NỘI DUNG TABS (FIGMA 2358:3036: height 55px, border-b 1px solid #404968, gap 40px) -->
                <div class="flex items-center gap-10 border-b border-[#404968] overflow-x-auto scrollbar-none pb-0 mb-10 h-[55px]">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        @click="selectTab(tab.key || tab.id)"
                        :class="[
                            'h-[55px] text-base leading-[19px] transition-colors duration-200 active:scale-[0.98] relative whitespace-nowrap focus:outline-none flex items-center',
                            activeTab === tab.id
                                ? 'font-semibold text-[#FAFAFA]'
                                : 'font-normal text-[#B9C0D4] hover:text-white',
                        ]"
                    >
                        {{ tab.name }}
                        <span
                            v-if="activeTab === tab.id"
                            class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#2970FF]"
                        ></span>
                    </button>
                </div>

                <!-- NỘI DUNG CHI TIẾT TABS (FIGMA 2358:3043: 1152x445 px, gap 48px, Simultaneous 1x1 Track Slide) -->
                <div :class="['relative overflow-x-clip', hasSpecs ? 'min-h-[445px]' : '']">
                    <Transition
                        enter-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform"
                        :enter-from-class="tabSlideDirection === 'next' ? 'translate-x-full' : '-translate-x-full'"
                        enter-to-class="translate-x-0"
                        leave-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform absolute inset-0 pointer-events-none"
                        leave-from-class="translate-x-0"
                        :leave-to-class="tabSlideDirection === 'next' ? '-translate-x-full' : 'translate-x-full'"
                    >
                        <!-- TAB 1: TỔNG QUAN (CÂU CHUYỆN SẢN PHẨM + BẢNG THÔNG SỐ KỸ THUẬT) -->
                        <div
                            v-if="activeTab === 'overview'"
                            key="overview"
                            :class="[
                                'w-full flex flex-col lg:flex-row gap-8 lg:gap-[48px] items-start justify-between',
                                hasSpecs ? 'min-h-[445px]' : '',
                            ]"
                        >
                            <!-- CÂU CHUYỆN SẢN PHẨM (FIGMA 2358:3044: 624x299 px, gap 24px) -->
                            <div
                                v-if="hasOverviewLeft"
                                :class="[
                                    'w-full flex flex-col gap-6',
                                    hasSpecs ? 'lg:w-[624px] shrink-0' : '',
                                ]"
                            >
                                <!-- GIỚI THIỆU (FIGMA 2358:3045) -->
                                <div v-if="storyTitle || storyIntro" class="flex flex-col gap-4">
                                    <h2 v-if="storyTitle" class="text-2xl font-bold text-[#FAFAFA] leading-[29px]">
                                        {{ storyTitle }}
                                    </h2>
                                    <p v-if="storyIntro" class="text-base text-[#B9C0D4] leading-[22px]">
                                        {{ storyIntro }}
                                    </p>
                                </div>

                                <!-- CÁC ĐẶC ĐIỂM SẢN PHẨM -->
                                <div
                                    v-if="feature1Title || feature1Desc || feature2Title || feature2Desc"
                                    class="flex flex-col gap-6"
                                >
                                    <!-- ĐẶC ĐIỂM 1 -->
                                    <div v-if="feature1Title || feature1Desc" class="flex flex-col gap-1">
                                        <h3 v-if="feature1Title" class="text-base font-semibold text-[#FAFAFA] leading-[22px]">
                                            {{ feature1Title }}
                                        </h3>
                                        <p v-if="feature1Desc" class="text-sm text-[#B9C0D4] leading-5">
                                            {{ feature1Desc }}
                                        </p>
                                    </div>

                                    <!-- ĐẶC ĐIỂM 2 -->
                                    <div v-if="feature2Title || feature2Desc" class="flex flex-col gap-1">
                                        <h3 v-if="feature2Title" class="text-base font-semibold text-[#FAFAFA] leading-[22px]">
                                            {{ feature2Title }}
                                        </h3>
                                        <p v-if="feature2Desc" class="text-sm text-[#B9C0D4] leading-5">
                                            {{ feature2Desc }}
                                        </p>
                                    </div>
                                </div>

                                <!-- BÀI VIẾT CHI TIẾT RICHTEXT TỪ CMS -->
                                <div
                                    v-if="hasDistinctContent"
                                    class="prose prose-invert max-w-none text-sm sm:text-base text-[#D0D5DD] leading-relaxed space-y-3"
                                    v-html="product.content"
                                ></div>
                            </div>

                            <!-- BẢNG THÔNG SỐ KỸ THUẬT EMBEDDED BÊN PHẢI (FIGMA 2358:3066: 480x445 px) -->
                            <div v-if="hasSpecs" class="w-full lg:w-[480px] shrink-0">
                                <SpecTable :specs="productSpecifications" />
                            </div>
                        </div>

                        <!-- TAB 2: THÔNG SỐ KỸ THUẬT MỞ RỘNG -->
                        <div
                            v-else-if="activeTab === 'specs'"
                            key="specs"
                            :class="[
                                'w-full flex flex-col lg:flex-row gap-8 lg:gap-[48px] items-start justify-between',
                                hasSpecs ? 'min-h-[445px]' : '',
                            ]"
                        >
                            <div
                                v-if="hasSpecsTabLeft"
                                :class="[
                                    'w-full space-y-6',
                                    hasSpecs ? 'lg:w-[624px] shrink-0' : '',
                                ]"
                            >
                                <div v-if="specsTabTitle || specsTabDesc" class="space-y-3">
                                    <h2 v-if="specsTabTitle" class="text-2xl font-bold text-[#FAFAFA]">{{ specsTabTitle }}</h2>
                                    <p v-if="specsTabDesc" class="text-base text-[#B9C0D4] leading-relaxed">
                                        {{ specsTabDesc }}
                                    </p>
                                </div>

                                <div
                                    v-if="specsTabContent"
                                    class="prose prose-invert max-w-none text-sm sm:text-base text-[#B9C0D4] leading-relaxed"
                                    v-html="specsTabContent"
                                ></div>

                                <div v-if="specsTabCards.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                                    <div
                                        v-for="(card, cIdx) in specsTabCards"
                                        :key="cIdx"
                                        class="p-5 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2"
                                    >
                                        <span v-if="card.label" class="text-xs font-semibold text-[#84ADFF] uppercase">{{ card.label }}</span>
                                        <h4 v-if="card.value" class="text-lg font-bold text-white">{{ card.value }}</h4>
                                        <p v-if="card.description" class="text-xs sm:text-sm text-[#B9C0D4] leading-relaxed">{{ card.description }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="hasSpecs" class="w-full lg:w-[480px] shrink-0">
                                <SpecTable :specs="productSpecifications" />
                            </div>
                        </div>

                        <!-- TAB 3: TUỲ CHỈNH SẢN PHẨM THEO YÊU CẦU -->
                        <div
                            v-else-if="activeTab === 'custom'"
                            key="custom"
                            class="w-full space-y-6"
                        >
                            <div v-if="customTabTitle || customTabDesc" class="max-w-3xl space-y-3">
                                <h2 v-if="customTabTitle" class="text-2xl font-bold text-[#FAFAFA]">{{ customTabTitle }}</h2>
                                <p v-if="customTabDesc" class="text-base text-[#B9C0D4] leading-relaxed">
                                    {{ customTabDesc }}
                                </p>
                            </div>

                            <div
                                v-if="customTabContent"
                                class="prose prose-invert max-w-none text-sm sm:text-base text-[#B9C0D4] leading-relaxed"
                                v-html="customTabContent"
                            ></div>

                            <div v-if="customTabSteps.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                                <div
                                    v-for="(step, sIdx) in customTabSteps"
                                    :key="sIdx"
                                    class="p-6 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2"
                                >
                                    <span v-if="step.step" class="text-xs font-semibold text-[#84ADFF]">{{ step.step }}</span>
                                    <h4 v-if="step.title" class="text-lg font-bold text-white">{{ step.title }}</h4>
                                    <p v-if="step.description" class="text-sm text-[#B9C0D4]">{{ step.description }}</p>
                                </div>
                            </div>

                            <div v-if="customTabCtaText" class="pt-2">
                                <button
                                    type="button"
                                    @click="handleRequestQuote"
                                    class="px-6 py-3 rounded-lg text-white font-medium text-sm flex items-center gap-2 transition-transform duration-150 hover:opacity-95 active:scale-[0.98]"
                                    style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
                                >
                                    {{ customTabCtaText }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </section>

            <!-- 3. SẢN PHẨM LIÊN QUAN (FIGMA 2358:3094) -->
            <div class="app-container">
                <RelatedProducts :products="related_products" />
            </div>

            <!-- 4. KẾT NỐI DỰ ÁN - LỜI MỜI SÁNG TẠO (FIGMA 2352:9972) -->
            <ProductCtaBanner />
        </template>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import ProductGallery from '@/Components/Product/ProductGallery.vue'
import VariantSelector from '@/Components/Product/VariantSelector.vue'
import SpecTable from '@/Components/Product/SpecTable.vue'
import RelatedProducts from '@/Components/Product/RelatedProducts.vue'
import EmptyState from '@/Components/EmptyState.vue'
import ProductCtaBanner from './Components/ProductCtaBanner.vue'

export default {
    name: 'ProductShow',
    components: {
        Link,
        ProductGallery,
        VariantSelector,
        SpecTable,
        RelatedProducts,
        EmptyState,
        ProductCtaBanner,
    },
    props: {
        product: {
            type: Object,
            default: () => ({}),
        },
        related_products: {
            type: Array,
            default: () => [],
        },
        seo: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            activeTab: 'overview',
            tabSlideDirection: 'next',
        }
    },
    computed: {
        currentLocale() {
            return this.$page?.props?.locale?.current || 'vi'
        },
        isEn() {
            return this.currentLocale === 'en'
        },
        globalConfig() {
            return this.$page?.props?.global || this.$page?.props?.configs || {}
        },
        brandName() {
            const g = this.globalConfig
            const raw =
                g.general_business_name ||
                g.general_site_name ||
                g.company_name ||
                g.site_name ||
                ''
            return typeof raw === 'string' ? raw.trim() : ''
        },
        siteOrigin() {
            if (typeof window !== 'undefined' && window.location?.origin) {
                return window.location.origin
            }
            const routeUrl = this.$page?.props?.route?.url
            if (typeof routeUrl === 'string' && /^https?:\/\//i.test(routeUrl)) {
                try {
                    return new URL(routeUrl).origin
                } catch (e) {
                    // Ignore invalid URL
                }
            }
            return ''
        },
        homeUrl() {
            return this.safeRoute('home', undefined, this.isEn ? '/en' : '/')
        },
        productsIndexUrl() {
            return this.safeRoute('products.index', undefined, this.isEn ? '/en/products' : '/san-pham')
        },
        contactUrl() {
            return this.safeRoute('contact', undefined, this.isEn ? '/en/contact' : '/lien-he')
        },
        currentTitle() {
            const raw = this.product?.title || this.product?.name || ''
            return typeof raw === 'string' ? raw.trim() : ''
        },
        headTitle() {
            if (this.currentTitle) {
                return this.brandName
                    ? `${this.currentTitle} - ${this.brandName}`
                    : this.currentTitle
            }
            const seoTitle = this.seo?.seo_meta_title || this.seo?.title || ''
            return typeof seoTitle === 'string' ? seoTitle.trim() : ''
        },
        currentCategoryObj() {
            if (this.product?.category && typeof this.product.category === 'object') {
                return this.product.category
            }
            if (Array.isArray(this.product?.categories) && this.product.categories.length > 0) {
                return this.product.categories[0]
            }
            return null
        },
        currentCategory() {
            const rawCat =
                this.currentCategoryObj?.title ||
                this.currentCategoryObj?.name ||
                this.product?.category_name ||
                ''
            return typeof rawCat === 'string' ? rawCat.trim() : ''
        },
        currentCategorySlug() {
            const slug =
                this.currentCategoryObj?.seo_slug ||
                this.currentCategoryObj?.slug ||
                this.product?.category_slug ||
                ''
            return typeof slug === 'string' ? slug.trim() : ''
        },
        categoryUrl() {
            if (this.currentCategorySlug) {
                return this.safeRoute(
                    'products.index',
                    { category: this.currentCategorySlug },
                    `${this.productsIndexUrl}?category=${encodeURIComponent(this.currentCategorySlug)}`
                )
            }
            return this.productsIndexUrl
        },
        currentDescription() {
            const raw = this.product?.description || this.product?.short_description || ''
            return typeof raw === 'string' ? raw.trim() : ''
        },
        pdpMetadata() {
            return this.product?.pdp_metadata || {}
        },
        productSpecifications() {
            const specs = this.product?.specifications || this.pdpMetadata?.specifications
            if (Array.isArray(specs) && specs.length > 0) {
                return specs
            }
            return []
        },
        hasSpecs() {
            return this.productSpecifications.length > 0
        },
        productMaterial() {
            const metaLabel = this.pdpMetadata?.material_label || this.pdpMetadata?.materialLabel
            if (typeof metaLabel === 'string' && metaLabel.trim()) {
                return metaLabel.trim()
            }
            if (typeof this.product?.material === 'string' && this.product.material.trim()) {
                const mat = this.product.material.trim()
                const tech = this.product.print_technology || this.pdpMetadata?.print_technology
                if (
                    typeof tech === 'string' &&
                    tech.trim() &&
                    !mat.toLowerCase().includes(tech.trim().toLowerCase()) &&
                    !mat.includes('·')
                ) {
                    return `${mat} · ${tech.trim()}`
                }
                return mat
            }
            return ''
        },
        productColor() {
            return this.pickText(
                this.product?.image_color_label,
                this.product?.color,
                this.pdpMetadata?.image_color_label,
                this.pdpMetadata?.imageColorLabel,
                this.pdpMetadata?.color
            )
        },
        productCustomization() {
            return this.pickText(
                this.product?.customization_label,
                this.product?.customization,
                this.pdpMetadata?.customization_label,
                this.pdpMetadata?.customizationLabel,
                this.pdpMetadata?.customization
            )
        },
        productImages() {
            const extractValid = (list) =>
                list.filter((item) => {
                    if (typeof item === 'string') return item.trim() !== ''
                    if (item && typeof item === 'object') {
                        const u = item.url || item.path || ''
                        return typeof u === 'string' && u.trim() !== ''
                    }
                    return false
                })

            if (Array.isArray(this.product?.images) && this.product.images.length > 0) {
                const validImages = extractValid(this.product.images)
                if (validImages.length > 0) return validImages
            }
            if (this.product?.image) {
                const validMain = extractValid([this.product.image])
                if (validMain.length > 0) return validMain
            }
            return []
        },
        ogImageUrl() {
            const first = this.productImages[0]
            if (!first) return ''
            if (typeof first === 'string') return first.trim()
            return String(first.url || first.path || '').trim()
        },
        storyTitle() {
            return this.pickText(
                this.product?.story_title,
                this.pdpMetadata?.story_title,
                this.pdpMetadata?.storyTitle
            )
        },
        storyIntro() {
            return this.pickText(
                this.product?.story_intro,
                this.pdpMetadata?.story_intro,
                this.pdpMetadata?.storyIntro,
                this.currentDescription
            )
        },
        feature1Title() {
            return this.pickText(
                this.product?.feature1_title,
                this.pdpMetadata?.feature1_title,
                this.pdpMetadata?.feature1Title
            )
        },
        feature1Desc() {
            return this.pickText(
                this.product?.feature1_desc,
                this.pdpMetadata?.feature1_desc,
                this.pdpMetadata?.feature1Desc
            )
        },
        feature2Title() {
            return this.pickText(
                this.product?.feature2_title,
                this.pdpMetadata?.feature2_title,
                this.pdpMetadata?.feature2Title
            )
        },
        feature2Desc() {
            return this.pickText(
                this.product?.feature2_desc,
                this.pdpMetadata?.feature2_desc,
                this.pdpMetadata?.feature2Desc
            )
        },
        hasDistinctContent() {
            const rawHtml = this.product?.content
            if (!rawHtml || typeof rawHtml !== 'string') return false
            const plain = rawHtml.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim()
            if (!plain) return false
            const introPlain = (this.storyIntro || '').replace(/\s+/g, ' ').trim()
            const descPlain = (this.currentDescription || '').replace(/\s+/g, ' ').trim()
            return plain !== introPlain && plain !== descPlain
        },
        hasOverviewLeft() {
            return Boolean(
                this.storyTitle ||
                    this.storyIntro ||
                    this.feature1Title ||
                    this.feature1Desc ||
                    this.feature2Title ||
                    this.feature2Desc ||
                    this.hasDistinctContent
            )
        },
        hasOverviewTab() {
            return this.hasOverviewLeft || this.hasSpecs
        },
        specsTabTitle() {
            return this.pickText(
                this.product?.specs_title,
                this.pdpMetadata?.specs_title,
                this.pdpMetadata?.specsTitle
            )
        },
        specsTabDesc() {
            return this.pickText(
                this.product?.specs_desc,
                this.pdpMetadata?.specs_desc,
                this.pdpMetadata?.specsDesc,
                this.pdpMetadata?.specs_description
            )
        },
        specsTabContent() {
            return this.pickText(
                this.product?.specs_content,
                this.pdpMetadata?.specs_content,
                this.pdpMetadata?.specsContent
            )
        },
        specsTabCards() {
            const customCards =
                this.product?.specs_cards ||
                this.pdpMetadata?.specs_cards ||
                this.pdpMetadata?.specsCards ||
                this.pdpMetadata?.manufacturing_info
            if (Array.isArray(customCards) && customCards.length > 0) {
                return customCards
                    .map((c) => {
                        if (!c || typeof c !== 'object') return null
                        return {
                            label: String(c.label || c.title || '').trim(),
                            value: String(c.value || c.heading || '').trim(),
                            description: String(c.description || c.desc || '').trim(),
                        }
                    })
                    .filter((c) => c && (c.label || c.value || c.description))
            }
            return []
        },
        hasSpecsTabLeft() {
            return Boolean(
                this.specsTabTitle ||
                    this.specsTabDesc ||
                    this.specsTabContent ||
                    this.specsTabCards.length > 0
            )
        },
        hasSpecsTab() {
            return this.hasSpecsTabLeft
        },
        customTabTitle() {
            return this.pickText(
                this.product?.custom_title,
                this.pdpMetadata?.custom_title,
                this.pdpMetadata?.customTitle
            )
        },
        customTabDesc() {
            return this.pickText(
                this.product?.custom_desc,
                this.pdpMetadata?.custom_desc,
                this.pdpMetadata?.customDesc,
                this.pdpMetadata?.custom_description
            )
        },
        customTabContent() {
            return this.pickText(
                this.product?.custom_content,
                this.pdpMetadata?.custom_content,
                this.pdpMetadata?.customContent
            )
        },
        customTabSteps() {
            const customSteps =
                this.product?.custom_steps ||
                this.pdpMetadata?.custom_steps ||
                this.pdpMetadata?.customSteps ||
                this.pdpMetadata?.care_instructions
            if (Array.isArray(customSteps) && customSteps.length > 0) {
                return customSteps
                    .map((s, idx) => {
                        if (!s || typeof s !== 'object') return null
                        return {
                            step: String(s.step || s.number || `${this.tt('Bước')} 0${idx + 1}`).trim(),
                            title: String(s.title || s.name || '').trim(),
                            description: String(s.description || s.desc || '').trim(),
                        }
                    })
                    .filter((s) => s && (s.title || s.description))
            }
            return []
        },
        customTabCtaText() {
            return this.pickText(
                this.product?.custom_cta_text,
                this.pdpMetadata?.custom_cta_text,
                this.pdpMetadata?.customCtaText
            )
        },
        hasCustomTab() {
            return Boolean(
                this.customTabTitle ||
                    this.customTabDesc ||
                    this.customTabContent ||
                    this.customTabSteps.length > 0 ||
                    this.customTabCtaText
            )
        },
        tabs() {
            const list = []
            if (this.hasOverviewTab) {
                list.push({ id: 'overview', key: 'overview', name: this.tt('Tổng quan') })
            }
            if (this.hasSpecsTab) {
                list.push({ id: 'specs', key: 'specs', name: this.tt('Thông số kỹ thuật') })
            }
            if (this.hasCustomTab) {
                list.push({ id: 'custom', key: 'custom', name: this.tt('Gia công theo bản vẽ') })
            }
            return list
        },
        productJsonLd() {
            if (!this.currentTitle) return null

            const images = Array.isArray(this.productImages)
                ? this.productImages
                      .map((img) => (typeof img === 'string' ? img : img?.url || img?.path || ''))
                      .filter(Boolean)
                : []

            const schema = {
                '@context': 'https://schema.org',
                '@type': 'Product',
                name: this.currentTitle,
                description: this.currentDescription,
            }

            if (images.length > 0) {
                schema.image = images
            }

            const sku =
                typeof this.product?.sku === 'string' && this.product.sku.trim()
                    ? this.product.sku.trim()
                    : ''
            if (sku) {
                schema.sku = sku
            }

            if (this.brandName) {
                schema.brand = {
                    '@type': 'Brand',
                    name: this.brandName,
                }
            }

            const rawPrice = this.product?.price
            const hasPrice =
                rawPrice !== null &&
                rawPrice !== undefined &&
                rawPrice !== '' &&
                !Number.isNaN(Number(rawPrice)) &&
                Number(rawPrice) > 0

            if (hasPrice) {
                const currentUrl =
                    typeof window !== 'undefined'
                        ? window.location.href
                        : this.$page?.props?.route?.url || ''
                const offer = {
                    '@type': 'Offer',
                    priceCurrency: 'VND',
                    price: String(Number(rawPrice)),
                    itemCondition: 'https://schema.org/NewCondition',
                    availability: 'https://schema.org/InStock',
                }
                if (currentUrl) {
                    offer.url = currentUrl
                }
                if (this.brandName) {
                    offer.seller = {
                        '@type': 'Organization',
                        name: this.brandName,
                    }
                }
                schema.offers = offer
            }

            return schema
        },
        breadcrumbJsonLd() {
            if (!this.currentTitle) return null

            const toAbsolute = (path) => {
                if (!path) return ''
                if (/^https?:\/\//i.test(path)) return path
                return this.siteOrigin
                    ? `${this.siteOrigin}${path.startsWith('/') ? path : `/${path}`}`
                    : path
            }

            const homeItem = toAbsolute(this.homeUrl)
            const productsItem = toAbsolute(this.productsIndexUrl)
            const currentUrl =
                typeof window !== 'undefined'
                    ? window.location.href
                    : this.$page?.props?.route?.url ||
                      toAbsolute(
                          this.product?.slug
                              ? `${this.productsIndexUrl}/${this.product.slug}`
                              : this.productsIndexUrl
                      )

            return {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                itemListElement: [
                    {
                        '@type': 'ListItem',
                        position: 1,
                        name: this.isEn ? 'Home' : 'Trang chủ',
                        item: homeItem,
                    },
                    {
                        '@type': 'ListItem',
                        position: 2,
                        name: this.isEn ? 'Products' : 'Sản phẩm',
                        item: productsItem,
                    },
                    {
                        '@type': 'ListItem',
                        position: 3,
                        name: this.currentTitle,
                        item: currentUrl,
                    },
                ],
            }
        },
    },
    watch: {
        tabs: {
            immediate: true,
            handler(nextTabs) {
                if (!Array.isArray(nextTabs) || nextTabs.length === 0) {
                    this.activeTab = ''
                    return
                }
                if (!nextTabs.some((t) => t.id === this.activeTab)) {
                    this.activeTab = nextTabs[0].id
                }
            },
        },
    },
    methods: {
        pickText(...values) {
            for (const val of values) {
                if (typeof val === 'string' && val.trim()) {
                    return val.trim()
                }
            }
            return ''
        },
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
        selectTab(tabKey) {
            const tabOrder = this.tabs.map((t) => t.id)
            const oldIndex = tabOrder.indexOf(this.activeTab)
            const newIndex = tabOrder.indexOf(tabKey)
            this.tabSlideDirection = newIndex >= oldIndex ? 'next' : 'prev'
            this.activeTab = tabKey
        },
        handleRequestQuote() {
            const contactUrl = this.contactUrl
            const queryParams = {}
            if (this.currentTitle) queryParams.product = this.currentTitle
            if (this.currentCategory) queryParams.category = this.currentCategory
            if (this.productMaterial) queryParams.material = this.productMaterial
            if (this.productColor) queryParams.color = this.productColor

            if (this.$inertia) {
                this.$inertia.visit(contactUrl, {
                    data: queryParams,
                })
            } else if (typeof window !== 'undefined') {
                const qs = new URLSearchParams(queryParams).toString()
                window.location.href = qs ? `${contactUrl}?${qs}` : contactUrl
            }
        },
    },
}
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
