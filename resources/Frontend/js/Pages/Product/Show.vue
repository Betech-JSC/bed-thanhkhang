<template>
    <div class="bg-[#111322] min-h-screen text-[#FAFAFA] font-sans antialiased selection:bg-[#2970FF] selection:text-white">
        <!-- SEO HEAD -->
        <Head>
            <title>{{ currentTitle }} - HH Maker</title>
            <meta name="description" :content="currentDescription" />
            <meta property="og:title" :content="currentTitle" />
            <meta property="og:description" :content="currentDescription" />
            <meta property="og:image" :content="productImages[0]?.url || '/images/pdp/product_main.png'" />
            <meta property="og:type" content="product" />
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
                                {{ tt('Mô hình in 3D') }}
                            </span>
                            <span class="inline-flex items-center justify-center h-[33px] px-3 py-2 rounded-[4px] text-xs font-normal leading-[17px] border border-[#5D6B98] text-[#84ADFF]">
                                {{ tt('In theo yêu cầu') }}
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

                        <!-- LƯU Ý TƯ VẤN (FIGMA 2358:3031) -->
                        <p class="text-xs text-[#B9C0D4] leading-[19px]">
                            {{ tt('Chia sẻ màu sắc, kích thước và số lượng mong muốn. HH Maker sẽ tư vấn phương án và báo giá phù hợp.') }}
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

            <!-- NỘI DUNG CHI TIẾT TABS (FIGMA 2358:3043: 1152x445 px, gap 48px, Locked Height + Simultaneous 1x1 Track Slide) -->
            <div class="relative min-h-[445px] lg:h-[445px] overflow-hidden">
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
                            </div>

                        <!-- BẢNG THÔNG SỐ KỸ THUẬT EMBEDDED BÊN PHẢI (FIGMA 2358:3066: 480x445 px) -->
                        <div class="w-full lg:w-[480px] shrink-0">
                            <SpecTable :specs="product?.specifications || product?.pdp_metadata?.specifications || []" />
                        </div>
                    </div>

                    <!-- TAB 2: THÔNG SỐ IN 3D MỞ RỘNG -->
                    <div
                        v-else-if="activeTab === 'specs'"
                        key="specs"
                        class="w-full min-h-[445px] space-y-6"
                    >
                        <div class="max-w-3xl space-y-3">
                            <h2 class="text-2xl font-bold text-[#FAFAFA]">{{ tt('Tiêu chuẩn kỹ thuật công nghệ in 3D FDM & Resin') }}</h2>
                            <p class="text-base text-[#B9C0D4] leading-relaxed">
                                {{ tt('Mỗi mô hình xuất xưởng tại xưởng in HH Maker đều trải qua quy trình kiểm soát chất lượng (QC) nghiêm ngặt từ khâu tối ưu lát cắt (slicing) đến khâu hoàn thiện bề mặt.') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                            <div class="p-6 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2">
                                <span class="text-xs font-semibold text-[#84ADFF] uppercase">{{ tt('Độ chính xác') }}</span>
                                <h4 class="text-xl font-bold text-white">±0.15 mm</h4>
                                <p class="text-sm text-[#B9C0D4]">{{ tt('Dung sai kích thước cơ học chuẩn xác, các khớp nối và bề mặt liền mạch.') }}</p>
                            </div>
                            <div class="p-6 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2">
                                <span class="text-xs font-semibold text-[#84ADFF] uppercase">{{ tt('Độ phân giải lớp') }}</span>
                                <h4 class="text-xl font-bold text-white">0.10 - 0.28 mm</h4>
                                <p class="text-sm text-[#B9C0D4]">{{ tt('Lựa chọn độ mịn cao phù hợp tượng nghệ thuật decor hoặc tối ưu thời gian.') }}</p>
                            </div>
                            <div class="p-6 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2">
                                <span class="text-xs font-semibold text-[#84ADFF] uppercase">{{ tt('Năng lực sản xuất') }}</span>
                                <h4 class="text-xl font-bold text-white">{{ tt('Hơn 50+ máy in') }}</h4>
                                <p class="text-sm text-[#B9C0D4]">{{ tt('Đáp ứng từ mẫu đơn chiếc cá nhân đến đơn hàng số lượng lớn cho sự kiện.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: TUỲ CHỈNH SẢN PHẨM THEO YÊU CẦU -->
                    <div
                        v-else-if="activeTab === 'custom'"
                        key="custom"
                        class="w-full min-h-[445px] space-y-6"
                    >
                        <div class="max-w-3xl space-y-3">
                            <h2 class="text-2xl font-bold text-[#FAFAFA]">{{ tt('Quy trình đặt in tùy chỉnh theo yêu cầu') }}</h2>
                            <p class="text-base text-[#B9C0D4] leading-relaxed">
                                {{ tt('HH Maker cung cấp dịch vụ in 3D cá nhân hóa toàn diện. Bạn có thể điều chỉnh tỉ lệ scale, phối màu sợi in đặc biệt hoặc khắc logo thương hiệu riêng.') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                            <div class="p-6 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2">
                                <span class="text-xs font-semibold text-[#84ADFF]">{{ tt('Bước 01') }}</span>
                                <h4 class="text-lg font-bold text-white">{{ tt('Gửi yêu cầu & file mẫu') }}</h4>
                                <p class="text-sm text-[#B9C0D4]">{{ tt('Chia sẻ ý tưởng, link mô hình 3D hoặc kích thước mong muốn cho HH Maker.') }}</p>
                            </div>
                            <div class="p-6 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2">
                                <span class="text-xs font-semibold text-[#84ADFF]">{{ tt('Bước 02') }}</span>
                                <h4 class="text-lg font-bold text-white">{{ tt('Tư vấn kỹ thuật & Báo giá') }}</h4>
                                <p class="text-sm text-[#B9C0D4]">{{ tt('Đội ngũ kỹ sư thẩm định độ dày thành vách, tư vấn vật liệu và gửi báo giá chi tiết.') }}</p>
                            </div>
                            <div class="p-6 rounded-xl bg-[#141C2E] border border-[#30374F] space-y-2">
                                <span class="text-xs font-semibold text-[#84ADFF]">{{ tt('Bước 03') }}</span>
                                <h4 class="text-lg font-bold text-white">{{ tt('Gia công in & Giao hàng') }}</h4>
                                <p class="text-sm text-[#B9C0D4]">{{ tt('Tiến hành in, xử lý hậu kỳ tỉ mỉ, đóng gói chống sốc an toàn và vận chuyển tận nơi.') }}</p>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button
                                type="button"
                                @click="handleRequestQuote"
                                class="px-6 py-3 rounded-lg text-white font-medium text-sm flex items-center gap-2 transition-transform duration-150 hover:opacity-95 active:scale-[0.98]"
                                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
                            >
                                {{ tt('Trao đổi yêu cầu tùy chỉnh với kỹ sư') }}
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

        <!-- NOTIFICATION TOAST -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-transform duration-200 ease-out"
                enter-from-class="translate-y-3"
                enter-to-class="translate-y-0"
                leave-active-class="transition-transform duration-150 ease-in"
                leave-from-class="translate-y-0"
                leave-to-class="translate-y-3"
            >
                <div
                    v-if="toastMessage"
                    class="fixed bottom-6 right-6 z-50 py-3 px-5 rounded-xl bg-[#1E2B45] text-white border border-[#2970FF] shadow-2xl flex items-center gap-3"
                >
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <span class="text-sm font-medium">{{ toastMessage }}</span>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import ProductGallery from '@/Components/Product/ProductGallery.vue'
import VariantSelector from '@/Components/Product/VariantSelector.vue'
import SpecTable from '@/Components/Product/SpecTable.vue'
import RelatedProducts from '@/Components/Product/RelatedProducts.vue'
import ProductCtaBanner from './Components/ProductCtaBanner.vue'

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
            toastMessage: '',
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
                { id: 'specs', key: 'specs', name: this.tt('Thông số in 3D') },
                { id: 'custom', key: 'custom', name: this.tt('Tuỳ chỉnh sản phẩm') },
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
            return this.isEn ? 'Space Decor' : this.tt('Trang trí không gian')
        },
        currentCategorySlug() {
            if (this.product?.category?.slug) {
                return this.product.category.slug;
            }
            if (this.product?.category_slug) {
                return this.product.category_slug;
            }
            const cat = (this.currentCategory || '').toLowerCase();
            if (cat.includes('không gian') || cat.includes('space')) {
                return 'trang-tri-khong-gian';
            }
            if (cat.includes('móc khoá') || cat.includes('móc khóa') || cat.includes('keychain') || cat.includes('phụ kiện') || cat.includes('accessories')) {
                return 'moc-khoa-phu-kien';
            }
            if (cat.includes('đồ dùng') || cat.includes('lifestyle') || cat.includes('stationery') || cat.includes('văn phòng phẩm') || cat.includes('decor')) {
                return 'trang-tri-do-dung';
            }
            if (cat.includes('gia công') || cat.includes('custom') || cat.includes('manufacturing')) {
                return 'gia-cong-theo-yeu-cau';
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
        productMaterial() {
            return this.product?.material || this.tt('Nhựa PLA · Công nghệ FDM')
        },
        productColor() {
            return this.product?.color || this.tt('Cam')
        },
        productCustomization() {
            return this.product?.customization || this.tt('Màu sắc · Kích thước · Số lượng')
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
            return this.product?.story_title || this.product?.pdp_metadata?.story_title || (this.currentTitle ? `${this.currentTitle}` : '')
        },
        storyIntro() {
            return this.product?.story_intro || this.product?.pdp_metadata?.story_intro || this.currentDescription
        },
        feature1Title() {
            return this.product?.feature1_title || this.product?.pdp_metadata?.feature1_title || ''
        },
        feature1Desc() {
            return this.product?.feature1_desc || this.product?.pdp_metadata?.feature1_desc || ''
        },
        feature2Title() {
            return this.product?.feature2_title || this.product?.pdp_metadata?.feature2_title || ''
        },
        feature2Desc() {
            return this.product?.feature2_desc || this.product?.pdp_metadata?.feature2_desc || '';
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
            const redirectToast = this.isEn
                ? `Redirecting to consultation page for "${this.currentTitle}"...`
                : `${this.tt('Đang chuyển hướng đến trang liên hệ tư vấn sản phẩm')} "${this.currentTitle}"...`
            this.showToast(redirectToast)

            if (typeof window !== 'undefined') {
                setTimeout(() => {
                    const contactUrl = this.route('contact')
                    if (this.$inertia) {
                        this.$inertia.visit(contactUrl, {
                            data: {
                                product: this.currentTitle,
                                category: this.currentCategory,
                                material: this.productMaterial,
                                color: this.productColor,
                            },
                        })
                    } else {
                        window.location.href = contactUrl
                    }
                }, 800)
            }
        },
        showToast(msg) {
            this.toastMessage = msg
            setTimeout(() => {
                this.toastMessage = ''
            }, 3000)
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
