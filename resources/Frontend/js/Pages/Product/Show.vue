<template>
    <div class="bg-[#111322] min-h-screen text-[#FAFAFA] font-sans antialiased selection:bg-[#2970FF] selection:text-white">
        <!-- SEO HEAD -->
        <Head>
            <title>{{ currentTitle }} - Thành Khang Tecsat</title>
            <meta name="description" :content="currentDescription" />
            <meta property="og:title" :content="currentTitle" />
            <meta property="og:description" :content="currentDescription" />
            <meta property="og:image" :content="productImages[0]?.url || '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp'" />
            <meta property="og:type" content="product" />
            <component
                :is="'script'"
                type="application/ld+json"
                v-html="JSON.stringify(productJsonLd)"
            />
            <component
                :is="'script'"
                type="application/ld+json"
                v-html="JSON.stringify(breadcrumbJsonLd)"
            />
        </Head>

        <!-- 1. BREADCRUMBS & SECTION TỔNG QUAN SẢN PHẨM (FIGMA 2358:2979: 1440x713 px, padding 24px 144px 40px) -->
        <div class="app-container pt-6 pb-10">
            <!-- ĐƯỜNG DẪN BREADCRUMB (FIGMA 2358:2980) -->
            <nav class="flex items-center gap-2.5 text-sm leading-[17px] text-[#B9C0D4] mb-6 overflow-x-auto scrollbar-none whitespace-nowrap" aria-label="Breadcrumb">
                <Link :href="route('home')" class="hover:text-white transition-colors leading-[17px]">
                    {{ tt('Trang chủ') }}
                </Link>
                <img src="/images/pdp/chevron_right.svg" alt=">" class="w-3.5 h-3.5 opacity-60" />
                <Link :href="route('products.index')" class="hover:text-white transition-colors leading-[17px]">
                    {{ tt('Sản phẩm') }}
                </Link>
                <img src="/images/pdp/chevron_right.svg" alt=">" class="w-3.5 h-3.5 opacity-60" />
                <Link :href="categoryUrl" class="hover:text-white transition-colors leading-[17px]">
                    {{ currentCategory }}
                </Link>
                <img src="/images/pdp/chevron_right.svg" alt=">" class="w-3.5 h-3.5 opacity-60" />
                <span class="text-[#FAFAFA] font-normal leading-[17px] truncate max-w-[200px] sm:max-w-none">
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
                        <p class="text-base text-[#B9C0D4] leading-[22px]">
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
<section v-scroll-reveal class="app-container pt-0 pb-16">
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
            <div class="relative min-h-[445px] overflow-x-hidden">
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
                        class="w-full min-h-[445px] flex flex-col lg:flex-row gap-8 lg:gap-[48px] items-start justify-between"
                    >
                        <!-- CÂU CHUYỆN SẢN PHẨM (FIGMA 2358:3044: 624x299 px, gap 24px) -->
                        <div class="w-full lg:w-[624px] shrink-0 flex flex-col gap-6">
                            <!-- GIỚI THIỆU (FIGMA 2358:3045) -->
                            <div class="flex flex-col gap-4">
                                <h2 class="text-2xl font-bold text-[#FAFAFA] leading-[29px]">
                                    {{ storyTitle }}
                                </h2>
                                <p class="text-base text-[#B9C0D4] leading-[22px]">
                                    {{ storyIntro }}
                                </p>
                            </div>

                            <!-- CÁC ĐẶC ĐIỂM SẢN PHẨM -->
                            <div v-if="feature1Title || feature2Title" class="flex flex-col gap-6">
                                <!-- ĐẶC ĐIỂM 1 -->
                                <div v-if="feature1Title" class="flex flex-col gap-1">
                                    <h3 class="text-base font-semibold text-[#FAFAFA] leading-[22px]">
                                        {{ feature1Title }}
                                    </h3>
                                    <p class="text-sm text-[#B9C0D4] leading-5">
                                        {{ feature1Desc }}
                                    </p>
                                </div>

                                <!-- ĐẶC ĐIỂM 2 -->
                                <div v-if="feature2Title" class="flex flex-col gap-1">
                                    <h3 class="text-base font-semibold text-[#FAFAFA] leading-[22px]">
                                        {{ feature2Title }}
                                    </h3>
                                    <p class="text-sm text-[#B9C0D4] leading-5">
                                        {{ feature2Desc }}
                                    </p>
                                </div>
                            </div>

                            <!-- FALLBACK BÀI VIẾT CHI TIẾT RICHTEXT TỪ CMS (KHI CHƯA CÓ FEATURE 1 & 2) -->
                            <div
                                v-else-if="hasDistinctContent"
                                class="prose prose-invert max-w-none text-sm sm:text-base text-[#D0D5DD] leading-relaxed space-y-3"
                                v-html="product.content"
                            ></div>
                        </div>

                        <!-- BẢNG THÔNG SỐ KỸ THUẬT EMBEDDED BÊN PHẢI (FIGMA 2358:3066: 480x445 px) -->
                        <div class="w-full lg:w-[480px] shrink-0">
                            <SpecTable :specs="productSpecifications" />
                        </div>
                    </div>

                    <!-- TAB 2: THÔNG SỐ KỸ THUẬT MỞ RỘNG -->
                    <div
                        v-else-if="activeTab === 'specs'"
                        key="specs"
                        class="w-full min-h-[445px] flex flex-col lg:flex-row gap-8 lg:gap-[48px] items-start justify-between"
                    >
                        <div class="w-full lg:w-[624px] shrink-0 space-y-6">
                            <div class="space-y-3">
                                <h2 class="text-2xl font-bold text-[#FAFAFA]">{{ specsTabTitle }}</h2>
                                <p class="text-base text-[#B9C0D4] leading-relaxed">
                                    {{ specsTabDesc }}
                                </p>
                            </div>

                            <div
                                v-if="specsTabContent"
                                class="prose prose-invert max-w-none text-sm sm:text-base text-[#B9C0D4] leading-relaxed"
                                v-html="specsTabContent"
                            ></div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                                <div
                                    v-for="(card, cIdx) in specsTabCards"
                                    :key="cIdx"
                                    class="p-5 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2"
                                >
                                    <span class="text-xs font-semibold text-[#84ADFF] uppercase">{{ card.label }}</span>
                                    <h4 class="text-lg font-bold text-white">{{ card.value }}</h4>
                                    <p class="text-xs sm:text-sm text-[#B9C0D4] leading-relaxed">{{ card.description }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full lg:w-[480px] shrink-0">
                            <SpecTable :specs="productSpecifications" />
                        </div>
                    </div>

                    <!-- TAB 3: TUỲ CHỈNH SẢN PHẨM THEO YÊU CẦU -->
                    <div
                        v-else-if="activeTab === 'custom'"
                        key="custom"
                        class="w-full min-h-[445px] space-y-6"
                    >
                        <div class="max-w-3xl space-y-3">
                            <h2 class="text-2xl font-bold text-[#FAFAFA]">{{ customTabTitle }}</h2>
                            <p class="text-base text-[#B9C0D4] leading-relaxed">
                                {{ customTabDesc }}
                            </p>
                        </div>

                        <div
                            v-if="customTabContent"
                            class="prose prose-invert max-w-none text-sm sm:text-base text-[#B9C0D4] leading-relaxed"
                            v-html="customTabContent"
                        ></div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                            <div
                                v-for="(step, sIdx) in customTabSteps"
                                :key="sIdx"
                                class="p-6 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2"
                            >
                                <span class="text-xs font-semibold text-[#84ADFF]">{{ step.step }}</span>
                                <h4 class="text-lg font-bold text-white">{{ step.title }}</h4>
                                <p class="text-sm text-[#B9C0D4]">{{ step.description }}</p>
                            </div>
                        </div>

                        <div class="pt-2">
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
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import ProductGallery from '@/Components/Product/ProductGallery.vue'
import VariantSelector from '@/Components/Product/VariantSelector.vue'
import SpecTable from '@/Components/Product/SpecTable.vue'
import RelatedProducts from '@/Components/Product/RelatedProducts.vue'
import ProductCtaBanner from './Components/ProductCtaBanner.vue'
import { openQuoteModal } from '@/composables/useQuoteModal'

export default {
    name: 'ProductShow',
    components: {
        Link,
        ProductGallery,
        VariantSelector,
        SpecTable,
        RelatedProducts,
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
        tabs() {
            return [
                { id: 'overview', key: 'overview', name: this.tt('Tổng quan') },
                { id: 'specs', key: 'specs', name: this.tt('Thông số kỹ thuật') },
                { id: 'custom', key: 'custom', name: this.tt('Gia công theo bản vẽ') },
            ]
        },
        currentTitle() {
            return this.product?.title || this.product?.name || ''
        },
        currentCategory() {
            const rawCat = this.product?.category?.title || this.product?.category?.name || this.product?.category_name;
            if (rawCat) {
                return this.tt(rawCat)
            }
            return this.isEn ? 'Escalator Components' : this.tt('Linh kiện thang cuốn')
        },
        currentCategorySlug() {
            if (this.product?.category?.slug) {
                return this.product.category.slug;
            }
            if (this.product?.category_slug) {
                return this.product.category_slug;
            }
            const cat = (this.currentCategory || '').toLowerCase();
            if (cat.includes('thang cuốn') || cat.includes('escalator')) {
                return 'linh-kien-thang-cuon';
            }
            if (cat.includes('thang nâng') || cat.includes('ô tô') || cat.includes('parking')) {
                return 'linh-kien-thang-nang-o-to';
            }
            if (cat.includes('băng tải') || cat.includes('hành lý') || cat.includes('conveyor')) {
                return 'he-thong-bang-tai-hanh-ly';
            }
            if (cat.includes('đóng gói') || cat.includes('packaging')) {
                return 'linh-kien-day-chuyen-dong-goi';
            }
            return 'all';
        },
        categoryUrl() {
            if (this.currentCategorySlug && this.currentCategorySlug !== 'all') {
                return this.route('products.index', { category: this.currentCategorySlug });
            }
            return this.route('products.index');
        },
        currentDescription() {
            return this.product?.description || this.product?.short_description || ''
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
        productMaterial() {
            if (this.pdpMetadata?.material_label || this.pdpMetadata?.materialLabel) {
                return this.pdpMetadata.material_label || this.pdpMetadata.materialLabel
            }
            if (this.product?.material) {
                const mat = this.product.material
                const tech = this.product.print_technology || this.pdpMetadata?.print_technology
                if (tech && !mat.toLowerCase().includes(tech.toLowerCase()) && !mat.includes('·')) {
                    return `${mat} · ${tech}`
                }
                return mat
            }
            return this.tt('Thép hợp kim 40Cr · Tiêu chuẩn ISO/DIN')
        },
        productColor() {
            return (
                this.product?.image_color_label ||
                this.product?.color ||
                this.pdpMetadata?.image_color_label ||
                this.pdpMetadata?.imageColorLabel ||
                this.pdpMetadata?.color ||
                this.tt('Cam')
            )
        },
        productCustomization() {
            return (
                this.product?.customization ||
                this.pdpMetadata?.customization ||
                this.pdpMetadata?.customizable_label ||
                this.pdpMetadata?.customizableLabel ||
                this.tt('Màu sắc · Kích thước · Số lượng')
            )
        },
        productImages() {
            if (this.product?.images && this.product.images.length > 0) {
                return this.product.images
            }
            if (this.product?.image) {
                return [this.product.image]
            }
            return []
        },
        storyTitle() {
            return (
                this.product?.story_title ||
                this.pdpMetadata?.story_title ||
                this.pdpMetadata?.storyTitle ||
                this.tt('Độ bền chuẩn công nghiệp cho vận hành liên tục.')
            )
        },
        storyIntro() {
            return (
                this.product?.story_intro ||
                this.pdpMetadata?.story_intro ||
                this.pdpMetadata?.storyIntro ||
                this.currentDescription
            )
        },
        feature1Title() {
            return (
                this.product?.feature1_title ||
                this.pdpMetadata?.feature1_title ||
                this.pdpMetadata?.feature1Title ||
                this.tt('Chịu tải cao & chống mài mòn vượt trội')
            )
        },
        feature1Desc() {
            return (
                this.product?.feature1_desc ||
                this.pdpMetadata?.feature1_desc ||
                this.pdpMetadata?.feature1Desc ||
                this.tt('Các chi tiết trục chốt, con lăn và má xích được gia công chính xác, nhiệt luyện tôi cao tần nhằm giảm độ dãn dài và tăng tuổi thọ vận hành.')
            )
        },
        feature2Title() {
            return (
                this.product?.feature2_title ||
                this.pdpMetadata?.feature2_title ||
                this.pdpMetadata?.feature2Title ||
                this.tt('Tùy chỉnh theo thông số bản vẽ OEM')
            )
        },
        feature2Desc() {
            return (
                this.product?.feature2_desc ||
                this.pdpMetadata?.feature2_desc ||
                this.pdpMetadata?.feature2Desc ||
                this.tt('Bạn có thể trao đổi với kỹ sư Thành Khang Tecsat để chọn bước xích, vật liệu thép hợp kim / Inox 304-316 hoặc gia công khớp nối theo bản vẽ riêng.')
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
        specsTabTitle() {
            return (
                this.product?.specs_title ||
                this.pdpMetadata?.specs_title ||
                this.pdpMetadata?.specsTitle ||
                this.tt('Tiêu chuẩn kỹ thuật & Kiểm định công nghiệp')
            )
        },
        specsTabDesc() {
            return (
                this.product?.specs_desc ||
                this.pdpMetadata?.specs_desc ||
                this.pdpMetadata?.specsDesc ||
                this.pdpMetadata?.specs_description ||
                this.tt('Mỗi lô thiết bị & linh kiện do Thành Khang Tecsat cung ứng đều được kiểm định thông số tải trọng, dung sai kích thước và kèm đầy đủ chứng chỉ CO/CQ.')
            )
        },
        specsTabContent() {
            return (
                this.product?.specs_content ||
                this.pdpMetadata?.specs_content ||
                this.pdpMetadata?.specsContent ||
                ''
            )
        },
        specsTabCards() {
            const customCards = this.product?.specs_cards || this.pdpMetadata?.specs_cards || this.pdpMetadata?.specsCards
            if (Array.isArray(customCards) && customCards.length > 0) {
                return customCards.map((c) => ({
                    label: this.tt(c.label || c.title || ''),
                    value: this.tt(c.value || c.heading || ''),
                    description: this.tt(c.description || c.desc || ''),
                }))
            }
            return [
                {
                    label: this.tt('Dung sai gia công'),
                    value: '±0.05 mm',
                    description: this.tt('Đảm bảo ăn khớp chính xác tuyệt đối với nhông truyền động và rãnh dẫn hướng.'),
                },
                {
                    label: this.tt('Tiêu chuẩn quốc tế'),
                    value: 'EN 115 / ISO / DIN',
                    description: this.tt('Đáp ứng các yêu cầu an toàn khắt khe cho thang cuốn thương mại & công nghiệp.'),
                },
                {
                    label: this.tt('Năng lực cung ứng'),
                    value: this.tt('Sẵn kho & Đặt OEM'),
                    description: this.tt('Hỗ trợ cấp lẻ thay thế bảo trì định kỳ hoặc cung ứng số lượng lớn cho dự án.'),
                },
            ]
        },
        customTabTitle() {
            return (
                this.product?.custom_title ||
                this.pdpMetadata?.custom_title ||
                this.pdpMetadata?.customTitle ||
                this.tt('Quy trình đặt hàng & gia công theo thông số OEM')
            )
        },
        customTabDesc() {
            return (
                this.product?.custom_desc ||
                this.pdpMetadata?.custom_desc ||
                this.pdpMetadata?.customDesc ||
                this.pdpMetadata?.custom_description ||
                this.tt('Thành Khang Tecsat hỗ trợ đặt hàng theo bước xích, tải trọng kéo đứt, vật liệu chống ăn mòn (Inox 304/316) hoặc phụ kiện gá lắp theo bản vẽ kỹ thuật riêng.')
            )
        },
        customTabContent() {
            return (
                this.product?.custom_content ||
                this.pdpMetadata?.custom_content ||
                this.pdpMetadata?.customContent ||
                ''
            )
        },
        customTabSteps() {
            const customSteps = this.product?.custom_steps || this.pdpMetadata?.custom_steps || this.pdpMetadata?.customSteps
            if (Array.isArray(customSteps) && customSteps.length > 0) {
                return customSteps.map((s, idx) => ({
                    step: this.tt(s.step || s.number || `${this.tt('Bước')} 0${idx + 1}`),
                    title: this.tt(s.title || s.name || ''),
                    description: this.tt(s.description || s.desc || ''),
                }))
            }
            return [
                {
                    step: this.tt('Bước 01'),
                    title: this.tt('Gửi thông số & bản vẽ'),
                    description: this.tt('Chia sẻ mã linh kiện OEM, thông số bước xích hoặc bản vẽ CAD cho Thành Khang Tecsat.'),
                },
                {
                    step: this.tt('Bước 02'),
                    title: this.tt('Đối chiếu kỹ thuật & Báo giá'),
                    description: this.tt('Đội ngũ kỹ sư kiểm tra tiêu chuẩn lắp đặt, tư vấn vật liệu phù hợp và gửi báo giá B2B.'),
                },
                {
                    step: this.tt('Bước 03'),
                    title: this.tt('Kiểm định CO/CQ & Giao hàng'),
                    description: this.tt('Hoàn tất kiểm định chất lượng, đóng gói quy cách công nghiệp và bàn giao tận công trình.'),
                },
            ]
        },
        customTabCtaText() {
            return (
                this.product?.custom_cta_text ||
                this.pdpMetadata?.custom_cta_text ||
                this.pdpMetadata?.customCtaText ||
                this.tt('Trao đổi yêu cầu tùy chỉnh với kỹ sư')
            )
        },
        productJsonLd() {
            const rawPrice = this.product?.price || this.product?.min_price || 250000
            const numericPrice = String(rawPrice).replace(/[^0-9]/g, '') || '250000'
            const currentUrl = typeof window !== 'undefined' ? window.location.href : 'https://dvktthanhkhang.vn/san-pham'
            const images = Array.isArray(this.productImages)
                ? this.productImages.map((img) => img.url).filter(Boolean)
                : []

            return {
                '@context': 'https://schema.org',
                '@type': 'Product',
                name: this.currentTitle,
                image: images.length > 0 ? images : ['https://dvktthanhkhang.vn/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp'],
                description: this.currentDescription,
                sku: this.product?.sku || `TK-PRD-${this.product?.id || 1}`,
                brand: {
                    '@type': 'Brand',
                    name: 'Thành Khang Tecsat',
                },
                offers: {
                    '@type': 'Offer',
                    url: currentUrl,
                    priceCurrency: 'VND',
                    price: numericPrice,
                    priceValidUntil: '2028-12-31',
                    itemCondition: 'https://schema.org/NewCondition',
                    availability: 'https://schema.org/InStock',
                    seller: {
                        '@type': 'Organization',
                        name: 'Thành Khang Tecsat',
                    },
                },
            }
        },
        breadcrumbJsonLd() {
            const homeUrl = this.isEn ? 'https://dvktthanhkhang.vn/en' : 'https://dvktthanhkhang.vn'
            const productsUrl = this.isEn ? 'https://dvktthanhkhang.vn/en/products' : 'https://dvktthanhkhang.vn/san-pham'
            const currentUrl = typeof window !== 'undefined' ? window.location.href : `${productsUrl}/${this.product?.slug || ''}`

            return {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                itemListElement: [
                    {
                        '@type': 'ListItem',
                        position: 1,
                        name: this.isEn ? 'Home' : 'Trang chủ',
                        item: homeUrl,
                    },
                    {
                        '@type': 'ListItem',
                        position: 2,
                        name: this.isEn ? 'Products' : 'Sản phẩm',
                        item: productsUrl,
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
    methods: {
        selectTab(tabKey) {
            const tabOrder = ['overview', 'specs', 'custom']
            const oldIndex = tabOrder.indexOf(this.activeTab)
            const newIndex = tabOrder.indexOf(tabKey)
            this.tabSlideDirection = newIndex >= oldIndex ? 'next' : 'prev'
            this.activeTab = tabKey
        },
        handleRequestQuote() {
            openQuoteModal({
                source: 'product_detail',
                product: this.currentTitle,
                category: this.currentCategory,
                material: this.productMaterial,
                color: this.productColor,
            })
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
