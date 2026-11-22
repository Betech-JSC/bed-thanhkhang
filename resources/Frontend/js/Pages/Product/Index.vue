<template>
    <div class="bg-[#111322] text-[#FAFAFA] min-h-screen selection:bg-[#2970FF] selection:text-white">
        <!-- SEO Head -->
        <Head>
            <title>{{ pageTitle }}</title>
            <meta name="description" :content="pageDescription" />
            <meta property="og:title" :content="pageTitle" />
            <meta property="og:description" :content="pageDescription" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- 1. Header Banner & Intro (Figma 2346:702 - Mở đầu) -->
<section v-scroll-reveal class="relative pt-10 pb-8 lg:pt-10 lg:pb-10 bg-[#111322]">
            <div class="app-container">
                <div class="flex flex-col gap-6">
                    <!-- Eyebrow -->
                    <span class="text-sm font-normal text-[#84ADFF] uppercase leading-5">
                        {{ tt('DANH MỤC THÀNH KHANG TECSAT') }}
                    </span>

                    <!-- Tiêu đề chính -->
                    <h1 class="text-3xl sm:text-[38px] font-bold text-[#FAFAFA] leading-tight sm:leading-[46px] font-sans">
                        {{ tt('THIẾT BỊ & PHỤ TÙNG CÔNG NGHIỆP') }}
                    </h1>
                </div>
            </div>
        </section>

        <!-- 2. Main Catalog Content Area (Figma 2346:994 - Sidebar Layout) -->
<section v-scroll-reveal class="pb-[12px] bg-[#111322]">
            <div class="app-container">
                <div class="flex flex-col lg:flex-row lg:justify-between gap-8 lg:gap-[45px] items-start">
                    <!-- Left Sidebar (Desktop: Cố định 240px, Mobile: Offcanvas Drawer) -->
                    <aside class="hidden lg:block lg:w-[240px] shrink-0 sticky top-24">
                        <ProductFilter
                            :filters="filters"
                            :categories="displayCategories"
                            @update:filters="handleFilterUpdate"
                            @reset="resetFilters"
                        />
                    </aside>

                    <!-- Mobile Drawer for Filter -->
                    <Teleport to="body">
                        <!-- Backdrop mờ tại chỗ (với transition fade) -->
                        <Transition
                            enter-active-class="transition-opacity duration-300 ease-out"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                            leave-active-class="transition-opacity duration-200 ease-in"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                        >
                            <div
                                v-if="isMobileFilterOpen"
                                class="fixed inset-0 z-50 lg:hidden bg-black/75 backdrop-blur-sm"
                                @click="isMobileFilterOpen = false"
                            ></div>
                        </Transition>

                        <!-- Panel lọc trượt từ mép trái (với transition slide -translate-x-full -> translate-x-0) -->
                        <Transition
                            enter-active-class="transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                            enter-from-class="-translate-x-full"
                            enter-to-class="translate-x-0"
                            leave-active-class="transition-transform duration-200 ease-[cubic-bezier(0.22,1,0.36,1)]"
                            leave-from-class="translate-x-0"
                            leave-to-class="-translate-x-full"
                        >
                            <div
                                v-if="isMobileFilterOpen"
                                class="fixed inset-y-0 left-0 z-50 lg:hidden w-full max-w-xs bg-[#111322] h-full overflow-y-auto p-4 border-r border-[#30374F] shadow-2xl"
                            >
                                <ProductFilter
                                    :filters="filters"
                                    :categories="displayCategories"
                                    @update:filters="handleFilterUpdate"
                                    @reset="resetFilters"
                                    @close="isMobileFilterOpen = false"
                                />
                            </div>
                        </Transition>
                    </Teleport>

                    <!-- Right Column: Products Results (Figma 2346:1015) -->
                    <div id="product-catalog-section" ref="productCatalogTop" class="w-full lg:w-[867px] shrink-0 min-w-0 space-y-6">
                        <!-- Top Toolbar (Figma 2352:9087) -->
                        <div class="min-h-[24px] sm:h-5 flex items-center justify-between gap-2 sm:gap-4 w-full">
                            <!-- Left: Breadcrumb (Figma 2352:9934) -->
                            <div class="flex items-center gap-1.5 sm:gap-2.5 text-xs sm:text-sm whitespace-nowrap min-w-0 overflow-hidden">
                                <!-- Mobile Filter Trigger -->
                                <button
                                    type="button"
                                    @click="isMobileFilterOpen = true"
class="lg:hidden p-1 text-[#84ADFF] hover:text-white mr-1 shrink-0 transition-colors duration-200 active:scale-95"
                                    :title="tt('Mở bộ lọc')"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                </button>

                                <Link :href="route('home')" class="text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors leading-[17px] shrink-0 whitespace-nowrap">
                                    {{ tt('Trang chủ') }}
                                </Link>
                                <svg class="w-3.5 h-3.5 text-[#B9C0D4] shrink-0" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L9.94 8 6.22 4.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-[#FAFAFA] leading-[17px] truncate whitespace-nowrap">
                                    {{ tt('Sản phẩm') }} ({{ filteredProducts.length }} {{ tt('sản phẩm') }})
                                </span>
                            </div>

                            <!-- Right: Custom Sort Dropdown (Minimal clean text + chevron matching user spec) -->
                            <div class="relative shrink-0" ref="sortDropdownRef">
                                <button
                                    type="button"
                                    @click="isSortOpen = !isSortOpen"
                                    class="group inline-flex items-center gap-1.5 text-xs sm:text-sm text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors duration-200 cursor-pointer select-none py-1 focus:outline-none"
                                    aria-haspopup="listbox"
                                    :aria-expanded="isSortOpen"
                                >
                                    <span class="font-normal">{{ currentSortLabel }}</span>
                                    <svg
                                        class="w-3.5 h-3.5 text-[#B9C0D4] group-hover:text-[#FAFAFA] transition-transform duration-200 shrink-0"
                                        :class="{ 'rotate-180': isSortOpen }"
                                        viewBox="0 0 16 16"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="m4 6 4 4 4-4" />
                                    </svg>
                                </button>

                                <!-- Dropdown Menu Popover -->
                                <Transition
                                    enter-active-class="transition-transform duration-200 ease-out"
                                    enter-from-class="translate-y-1"
                                    enter-to-class="translate-y-0"
                                    leave-active-class="transition-transform duration-150 ease-in"
                                    leave-from-class="translate-y-0"
                                    leave-to-class="translate-y-1"
                                >
                                    <div
                                        v-if="isSortOpen"
                                        class="absolute right-0 mt-1.5 w-40 bg-[#171B30] border border-[#404968] rounded-[8px] shadow-2xl py-1 z-30 overflow-hidden"
                                        role="listbox"
                                    >
                                        <button
                                            v-for="opt in sortOptions"
                                            :key="opt.value"
                                            type="button"
                                            @click="selectSort(opt.value)"
                                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm flex items-center justify-between transition-colors cursor-pointer"
                                            :class="[
                                                filters.sortBy === opt.value
                                                    ? 'bg-[#2970FF] text-white font-semibold'
                                                    : 'text-[#B9C0D4] hover:bg-[#222842] hover:text-white'
                                            ]"
                                            role="option"
                                            :aria-selected="filters.sortBy === opt.value"
                                        >
                                            <span>{{ opt.label }}</span>
                                            <svg
                                                v-if="filters.sortBy === opt.value"
                                                class="w-3.5 h-3.5 text-white"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </Transition>
                            </div>
                        </div>

                        <!-- Active Filter Chips Row & Reset Filter Button (Figma Feedback requirement) -->
                        <Transition
                            enter-active-class="transition-all duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition-all duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-1"
                        >
                            <div
                                v-if="hasActiveFilters"
                                class="flex flex-wrap items-center gap-2 pt-1 pb-1 select-none"
                            >
                                <span class="text-xs text-[#B9C0D4] mr-0.5">{{ tt('Đang lọc:') }}</span>

                                <!-- Chip: Tìm kiếm -->
                                <div
                                    v-if="filters.search && filters.search.trim()"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#1A1F36] border border-[#404968] text-xs text-[#EFF1F5] shadow-sm"
                                >
                                    <span class="text-[#84ADFF]">{{ tt('Từ khoá:') }}</span>
                                    <span class="max-w-[120px] truncate">"{{ filters.search.trim() }}"</span>
                                    <button
                                        type="button"
                                        @click="clearSearch"
                                        class="text-[#B9C0D4] hover:text-white p-0.5 rounded-full hover:bg-white/10 transition-colors ml-0.5 cursor-pointer"
                                        :title="tt('Xoá tìm kiếm')"
                                    >
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Chip: Danh mục chính -->
                                <div
                                    v-if="activeCategoryLabel"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#1A1F36] border border-[#404968] text-xs text-[#EFF1F5] shadow-sm"
                                >
                                    <span class="text-[#84ADFF]">{{ tt('Danh mục:') }}</span>
                                    <span>{{ activeCategoryLabel }}</span>
                                    <button
                                        type="button"
                                        @click="clearCategory"
                                        class="text-[#B9C0D4] hover:text-white p-0.5 rounded-full hover:bg-white/10 transition-colors ml-0.5 cursor-pointer"
                                        :title="tt('Xoá danh mục')"
                                    >
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Chip: Danh mục con (Subcategory) -->
                                <div
                                    v-if="activeSubcategoryLabel"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#1A1F36] border border-[#2970FF]/50 text-xs text-[#84ADFF] shadow-sm"
                                >
                                    <span class="text-[#B9C0D4]">{{ tt('Mục con:') }}</span>
                                    <span class="font-medium">{{ activeSubcategoryLabel }}</span>
                                    <button
                                        type="button"
                                        @click="clearSubcategory"
                                        class="text-[#84ADFF] hover:text-white p-0.5 rounded-full hover:bg-white/10 transition-colors ml-0.5 cursor-pointer"
                                        :title="tt('Xoá mục con')"
                                    >
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Chip: Nhu cầu (Purposes) -->
                                <div
                                    v-for="p in filters.purposes"
                                    :key="p"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#1A1F36] border border-[#404968] text-xs text-[#EFF1F5] shadow-sm"
                                >
                                    <span>{{ p }}</span>
                                    <button
                                        type="button"
                                        @click="removePurpose(p)"
                                        class="text-[#B9C0D4] hover:text-white p-0.5 rounded-full hover:bg-white/10 transition-colors ml-0.5 cursor-pointer"
                                        :title="tt('Bỏ chọn nhu cầu này')"
                                    >
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Nút/link Đặt lại bộ lọc (Không làm button ở đây quá nổi bật, bám sát styling Figma) -->
                                <button
                                    type="button"
                                    @click="resetFilters"
                                    class="inline-flex items-center gap-1.5 px-2 py-1 text-xs text-[#84ADFF] hover:text-white hover:underline transition-colors cursor-pointer select-none ml-1"
                                >
                                    <svg class="w-3 h-3 fill-none stroke-current" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>{{ tt('Đặt lại bộ lọc') }}</span>
                                </button>
                            </div>
                        </Transition>

                        <!-- Products Grid (Figma 2346:717 - 3 cột chuẩn, Fixed Outer Card Frames + Inner Card Slide Transition) -->
                        <div class="w-full min-h-[600px]">
                            <div
                                v-if="paginatedProducts.length > 0"
                                class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                            >
                                <ProductCard
                                    v-for="(product, idx) in paginatedProducts"
                                    :key="product.id || idx"
                                    :product="product"
                                />
                            </div>

                            <!-- Empty State -->
                            <div
                                v-else
                                class="w-full py-16 px-4 text-center rounded-2xl bg-[#30374F] space-y-4"
                            >
                                <div class="w-16 h-16 mx-auto rounded-full bg-[#111322] flex items-center justify-center text-[#84ADFF]">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-[#FAFAFA]">{{ tt('Không tìm thấy sản phẩm phù hợp') }}</h3>
                                <p class="text-sm text-[#B9C0D4] max-w-md mx-auto">
                                    {{ tt('Rất tiếc, không có sản phẩm nào đáp ứng các tiêu chí lọc hiện tại. Bạn vui lòng thử thay đổi từ khoá tìm kiếm hoặc đặt lại bộ lọc.') }}
                                </p>
                                <button
                                    type="button"
                                    @click="resetFilters"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#1E2338] hover:bg-[#2A314D] border border-[#404968] hover:border-[#84ADFF] text-[#FAFAFA] text-xs font-medium transition-colors shadow-sm cursor-pointer select-none"
                                >
                                    {{ tt('Đặt lại bộ lọc') }}
                                </button>
                            </div>
                        </div>

                        <!-- Pagination (Figma 2352:9091) -->
                        <ProductPagination
                            :current-page="currentPage"
                            :totalPages="totalPages"
                            @change-page="handlePageChange"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Bottom CTA Banner (Figma 2346:740 & 2346:741 - Kết nối dự án / Lời mời sáng tạo) -->
        <ProductCtaBanner />
    </div>
</template>

<script>
import ProductCard from './Components/ProductCard.vue'
import ProductFilter from './Components/ProductFilter.vue'
import ProductPagination from './Components/ProductPagination.vue'
import ProductCtaBanner from './Components/ProductCtaBanner.vue'

export default {
    name: 'ProductIndex',
    components: {
        ProductCard,
        ProductFilter,
        ProductPagination,
        ProductCtaBanner,
    },
    props: {
        products: {
            type: Array,
            default: () => [],
        },
        categories: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            isMobileFilterOpen: false,
            currentPage: 1,
            slideTick: 0,
            slideDirection: 'next',
            pageSize: 16,
            isSortOpen: false,
            filters: {
                search: '',
                category: 'all',
                subcategory: null,
                purposes: [],
                sortBy: 'popular',
            },
        };
    },
    computed: {
        currentLocale() {
            return this.$page?.props?.locale?.current || 'vi'
        },
        pageTitle() {
            return this.currentLocale === 'en'
                ? 'Industrial Equipment & Escalator Parts Catalog - Thành Khang Tecsat'
                : this.tt('Danh mục Sản phẩm & Phụ tùng Công nghiệp - Thành Khang Tecsat')
        },
        pageDescription() {
            return this.currentLocale === 'en'
                ? 'Explore industrial chains, escalator spare parts, car parking elevator chains, and baggage conveyor systems by Thành Khang Tecsat.'
                : this.tt('Khám phá danh mục linh kiện thang cuốn, thang nâng ô tô, xích tải công nghiệp Universal Chains và hệ thống băng tải từ Thành Khang Tecsat.')
        },
        sortOptions() {
            return [
                { value: 'popular', label: this.tt('Nổi bật nhất') },
                { value: 'newest', label: this.tt('Mới nhất') },
                { value: 'name_asc', label: this.tt('Tên A-Z') },
            ]
        },
        currentSortLabel() {
            const found = this.sortOptions.find(o => o.value === this.filters.sortBy);
            return found ? found.label : this.tt('Nổi bật nhất');
        },
        allProducts() {
            if (this.products && Array.isArray(this.products) && this.products.length > 0) {
                return this.products;
            }
            return [];
        },
        defaultCategories() {
            return [
                {
                    id: 'all',
                    name: this.tt('Tất cả sản phẩm'),
                    slug: 'all',
                    hasChevron: false,
                    subcategories: [],
                },
                {
                    id: 'linh-kien-thang-cuon',
                    name: this.tt('Linh kiện thang cuốn'),
                    slug: 'linh-kien-thang-cuon',
                    hasChevron: true,
                    subcategories: [
                        { id: 'xich-bac-thang-cuon', slug: 'xich-bac-thang-cuon', name: this.tt('Xích bậc thang cuốn') },
                        { id: 'xich-bang-tai-hanh-khach', slug: 'xich-bang-tai-hanh-khach', name: this.tt('Xích băng tải hành khách') },
                        { id: 'xich-dan-dong-chinh-thang-cuon', slug: 'xich-dan-dong-chinh-thang-cuon', name: this.tt('Xích dẫn động chính') },
                    ],
                },
                {
                    id: 'linh-kien-thang-nang-o-to',
                    name: this.tt('Linh kiện thang nâng ô tô'),
                    slug: 'linh-kien-thang-nang-o-to',
                    hasChevron: true,
                    subcategories: [
                        { id: 'xich-bai-do-xe-tieu-chuan-lt', slug: 'xich-bai-do-xe-tieu-chuan-lt', name: this.tt('Xích bãi đỗ xe tiêu chuẩn LT') },
                        { id: 'xich-bai-do-xe-tuan-hoan-dung', slug: 'xich-bai-do-xe-tuan-hoan-dung', name: this.tt('Xích tuần hoàn đứng') },
                    ],
                },
                {
                    id: 'he-thong-bang-tai-hanh-ly',
                    name: this.tt('Hệ thống băng tải hành lý'),
                    slug: 'he-thong-bang-tai-hanh-ly',
                    hasChevron: false,
                    subcategories: [],
                },
                {
                    id: 'linh-kien-day-chuyen-dong-goi',
                    name: this.tt('Linh kiện dây chuyền đóng gói'),
                    slug: 'linh-kien-day-chuyen-dong-goi',
                    hasChevron: false,
                    subcategories: [],
                },
            ];
        },
        displayCategories() {
            if (!Array.isArray(this.categories) || this.categories.length === 0) {
                return this.defaultCategories;
            }

            const rawList = this.categories.filter(Boolean);
            const childMap = {};
            const roots = [];

            rawList.forEach((item) => {
                const parentId = item.parent_id || item.default_parent?.id || 0;
                if (parentId && Number(parentId) > 0) {
                    if (!childMap[parentId]) childMap[parentId] = [];
                    childMap[parentId].push(item);
                } else {
                    const slug = String(item.slug || '').toLowerCase();
                    if (slug !== 'trang-tri-khong-gian' && slug !== 'space-decor') {
                        roots.push(item);
                    }
                }
            });

            if (roots.length === 0) {
                return this.defaultCategories;
            }

            const tree = [this.defaultCategories[0]];

            roots.forEach((root) => {
                const slug = String(root.slug || root.id || '').toLowerCase();
                const rawTitle = String(root.title || root.name || '').trim();

                let fallback = null;
                if (slug.includes('thang-cuon') || slug.includes('escalator') || rawTitle.toLowerCase().includes('thang cuốn')) {
                    fallback = this.defaultCategories[1];
                } else if (slug.includes('thang-nang') || slug.includes('parking') || rawTitle.toLowerCase().includes('thang nâng')) {
                    fallback = this.defaultCategories[2];
                } else if (slug.includes('bang-tai') || slug.includes('baggage') || rawTitle.toLowerCase().includes('băng tải')) {
                    fallback = this.defaultCategories[3];
                } else if (slug.includes('dong-goi') || slug.includes('packaging') || rawTitle.toLowerCase().includes('đóng gói')) {
                    fallback = this.defaultCategories[4];
                }

                const rawChildren =
                    (Array.isArray(root.subcategories) && root.subcategories.length > 0 && root.subcategories) ||
                    (Array.isArray(root.children) && root.children.length > 0 && root.children) ||
                    (Array.isArray(root.nodes) && root.nodes.length > 0 && root.nodes) ||
                    childMap[root.id] ||
                    [];

                const mappedSubs =
                    rawChildren.length > 0
                        ? rawChildren.map((sub) => ({
                              id: sub.slug || String(sub.id),
                              rawId: sub.id,
                              slug: sub.slug || String(sub.id),
                              name: this.tt(sub.title || sub.name || ''),
                          }))
                        : fallback
                        ? fallback.subcategories
                        : [];

                let displayName = rawTitle ? this.tt(rawTitle) : fallback?.name || '';
                if (rawTitle === 'Móc khoá & Phụ kiện' || rawTitle === 'Móc khóa & Phụ kiện') {
                    displayName = this.tt('Móc khoá');
                } else if (rawTitle === 'Trang trí & Đồ dùng') {
                    displayName = this.tt('Văn phòng phẩm');
                }

                tree.push({
                    id: fallback ? fallback.id : root.slug || String(root.id),
                    rawId: root.id,
                    name: displayName,
                    slug: root.slug || fallback?.slug || String(root.id),
                    hasChevron: mappedSubs.length > 0,
                    subcategories: mappedSubs,
                });
            });

            return tree;
        },
        hasActiveFilters() {
            return (
                (this.filters.search && this.filters.search.trim() !== '') ||
                (this.filters.category && this.filters.category !== 'all') ||
                (this.filters.subcategory && this.filters.subcategory !== null) ||
                (this.filters.purposes && this.filters.purposes.length > 0)
            );
        },
        activeCategoryLabel() {
            if (!this.filters.category || this.filters.category === 'all') return null;
            const found = this.displayCategories.find(c => c.id === this.filters.category || c.slug === this.filters.category);
            return found ? found.name : this.filters.category;
        },
        activeSubcategoryLabel() {
            if (!this.filters.subcategory) return null;
            const targetSub = String(this.filters.subcategory).toLowerCase();
            for (const cat of this.displayCategories) {
                if (Array.isArray(cat.subcategories)) {
                    const found = cat.subcategories.find(
                        (s) =>
                            String(s.id || '').toLowerCase() === targetSub ||
                            String(s.slug || '').toLowerCase() === targetSub ||
                            (s.rawId !== undefined && String(s.rawId) === String(this.filters.subcategory))
                    );
                    if (found) return found.name;
                }
            }
            return this.filters.subcategory;
        },
        filteredProducts() {
            let result = [...this.allProducts];

            // 1. Search filter
            if (this.filters.search.trim()) {
                const q = this.filters.search.toLowerCase().trim();
                result = result.filter(
                    (p) =>
                        (p.name && p.name.toLowerCase().includes(q)) ||
                        (p.title && p.title.toLowerCase().includes(q)) ||
                        (p.description && p.description.toLowerCase().includes(q))
                );
            }

            // 2. Category filter
            if (this.filters.category && this.filters.category !== 'all') {
                const targetCat = this.filters.category;
                const selectedCatObj = this.displayCategories.find(
                    (c) => c.id === targetCat || c.slug === targetCat || (c.rawId !== undefined && String(c.rawId) === String(targetCat))
                );
                const validKeys = new Set([String(targetCat).toLowerCase()]);
                if (selectedCatObj) {
                    if (selectedCatObj.id) validKeys.add(String(selectedCatObj.id).toLowerCase());
                    if (selectedCatObj.slug) validKeys.add(String(selectedCatObj.slug).toLowerCase());
                    if (selectedCatObj.rawId !== undefined) validKeys.add(String(selectedCatObj.rawId).toLowerCase());
                    if (Array.isArray(selectedCatObj.subcategories)) {
                        selectedCatObj.subcategories.forEach((s) => {
                            if (s.id) validKeys.add(String(s.id).toLowerCase());
                            if (s.slug) validKeys.add(String(s.slug).toLowerCase());
                            if (s.rawId !== undefined) validKeys.add(String(s.rawId).toLowerCase());
                        });
                    }
                }

                result = result.filter((p) => {
                    if (
                        p.categories &&
                        Array.isArray(p.categories) &&
                        p.categories.some(
                            (c) =>
                                validKeys.has(String(c.id || '').toLowerCase()) ||
                                validKeys.has(String(c.slug || '').toLowerCase()) ||
                                validKeys.has(String(c.parent_id || '').toLowerCase()) ||
                                validKeys.has(String(c.default_parent?.id || '').toLowerCase()) ||
                                validKeys.has(String(c.default_parent?.slug || '').toLowerCase())
                        )
                    ) {
                        return true;
                    }
                    if (p.categoryId === targetCat || p.category === targetCat) {
                        return true;
                    }
                    const catStr = (typeof p.category === 'string' ? p.category : (p.category?.title || p.category?.name || '')).toLowerCase();
                    if (targetCat === 'linh-kien-thang-cuon' || targetCat.includes('thang-cuon')) {
                        return p.categoryId === 'linh-kien-thang-cuon' || catStr.includes('thang cuốn') || catStr.includes('escalator');
                    }
                    if (targetCat === 'linh-kien-thang-nang-o-to' || targetCat.includes('thang-nang')) {
                        return p.categoryId === 'linh-kien-thang-nang-o-to' || catStr.includes('thang nâng') || catStr.includes('ô tô') || catStr.includes('parking');
                    }
                    if (targetCat === 'he-thong-bang-tai-hanh-ly' || targetCat.includes('bang-tai')) {
                        return p.categoryId === 'he-thong-bang-tai-hanh-ly' || catStr.includes('băng tải') || catStr.includes('hành lý') || catStr.includes('conveyor');
                    }
                    if (targetCat === 'linh-kien-day-chuyen-dong-goi' || targetCat.includes('dong-goi')) {
                        return p.categoryId === 'linh-kien-day-chuyen-dong-goi' || catStr.includes('đóng gói') || catStr.includes('packaging');
                    }
                    return false;
                });
            }

            // 2.5 Subcategory filter
            if (this.filters.subcategory) {
                const sub = String(this.filters.subcategory).toLowerCase();
                let subObj = null;
                for (const cat of this.displayCategories) {
                    if (Array.isArray(cat.subcategories)) {
                        const found = cat.subcategories.find(
                            (s) => String(s.id).toLowerCase() === sub || String(s.slug || '').toLowerCase() === sub || String(s.rawId || '') === sub
                        );
                        if (found) {
                            subObj = found;
                            break;
                        }
                    }
                }

                result = result.filter((p) => {
                    // Kiểm tra nếu sản phẩm đã được gán trực tiếp vào danh mục con trong CMS
                    if (
                        p.categories &&
                        Array.isArray(p.categories) &&
                        p.categories.some(
                            (c) =>
                                String(c.slug || '').toLowerCase() === sub ||
                                String(c.id || '').toLowerCase() === sub ||
                                (subObj?.rawId !== undefined && String(c.id) === String(subObj.rawId)) ||
                                (subObj?.slug && String(c.slug || '').toLowerCase() === String(subObj.slug).toLowerCase())
                        )
                    ) {
                        return true;
                    }

                    const text = ((p.name || '') + ' ' + (p.title || '') + ' ' + (p.description || '') + ' ' + (p.slug || '')).toLowerCase();
                    if (sub.includes('bac-thang')) {
                        return text.includes('bậc') || text.includes('step') || text.includes('thương mại') || text.includes('công cộng');
                    }
                    if (sub.includes('bang-tai-hanh-khach')) {
                        return text.includes('hành khách') || text.includes('walkway');
                    }
                    if (sub.includes('dan-dong')) {
                        return text.includes('dẫn động') || text.includes('drive');
                    }
                    if (sub.includes('bai-do-xe') || sub.includes('lt')) {
                        return text.includes('bãi đỗ') || text.includes('lt') || text.includes('parking');
                    }
                    if (sub.includes('tuan-hoan')) {
                        return text.includes('tuần hoàn') || text.includes('vertical');
                    }
                    if (sub.includes('chot-rong')) {
                        return text.includes('chốt rỗng') || text.includes('hollow');
                    }
                    if (subObj?.name && text.includes(subObj.name.toLowerCase())) {
                        return true;
                    }
                    return text.includes(sub);
                });
            }

            // 3. Purposes filter
            if (this.filters.purposes.length > 0) {
                result = result.filter((p) => {
                    const catTitles = Array.isArray(p.categories) ? p.categories.map(c => (c.title || c.name || c.slug || '').toLowerCase()).join(' ') : '';
                    const catStr = (typeof p.category === 'string' ? p.category : (p.category?.title || p.category?.name || '')).toLowerCase();
                    const text = ((p.name || '') + ' ' + (p.title || '') + ' ' + (p.description || '')).toLowerCase();

                    return this.filters.purposes.some(sel => {
                        const s = (sel || '').toLowerCase();
                        if (sel === p.purpose) return true;
                        if (p.purposeKey === 'space_decor' && (s.includes('không gian') || s.includes('space'))) return true;
                        if (p.purposeKey === 'daily_essentials' && (s.includes('hằng ngày') || s.includes('daily'))) return true;
                        if (p.purposeKey === 'personal_gifts' && (s.includes('quà tặng') || s.includes('gift'))) return true;

                        // Check via categories & content fallback
                        if (s.includes('không gian') || s.includes('space')) {
                            return catTitles.includes('không gian') || catStr.includes('không gian') || text.includes('không gian') || text.includes('trang trí');
                        }
                        if (s.includes('hằng ngày') || s.includes('đồ dùng') || s.includes('daily')) {
                            return catTitles.includes('đồ dùng') || catStr.includes('đồ dùng') || text.includes('đồ dùng') || text.includes('bàn làm việc');
                        }
                        if (s.includes('quà tặng') || s.includes('cá nhân') || s.includes('gift')) {
                            return catTitles.includes('phụ kiện') || catStr.includes('móc khoá') || text.includes('quà tặng') || text.includes('móc khoá');
                        }
                        return false;
                    });
                });
            }

            // 4. Sorting
            if (this.filters.sortBy === 'newest') {
                result.sort((a, b) => b.id - a.id);
            } else if (this.filters.sortBy === 'popular') {
                result.sort((a, b) => a.id - b.id);
            } else if (this.filters.sortBy === 'name_asc') {
                result.sort((a, b) => (a.name || a.title || '').localeCompare(b.name || b.title || ''));
            }

            return result;
        },
        totalPages() {
            return Math.ceil(this.filteredProducts.length / this.pageSize) || 1;
        },
        paginatedProducts() {
            const start = (this.currentPage - 1) * this.pageSize;
            return this.filteredProducts.slice(start, start + this.pageSize);
        },
    },
    watch: {
        isMobileFilterOpen(val) {
            if (typeof document !== 'undefined') {
                if (val) {
                    document.body.classList.add('overflow-hidden');
                } else {
                    document.body.classList.remove('overflow-hidden');
                }
            }
        },
        filters: {
            deep: true,
            handler() {
                this.slideDirection = 'next';
                this.currentPage = 1;
            },
        },
        '$page.url': {
            handler() {
                this.initSearchFromUrl();
            },
        },
    },
    created() {
        this.initSearchFromUrl();
    },
    mounted() {
        this.preloadProductImages();
        this.initSearchFromUrl();
        if (typeof document !== 'undefined') {
            document.addEventListener('click', this.handleDocumentClick);
        }
    },
    beforeUnmount() {
        if (typeof document !== 'undefined') {
            document.removeEventListener('click', this.handleDocumentClick);
            document.body.classList.remove('overflow-hidden');
        }
    },
    methods: {
        preloadProductImages() {
            if (typeof window === 'undefined') return;
            this.allProducts.forEach(p => {
                const src = typeof p.image === 'string' ? p.image : p.image?.url;
                if (src) {
                    const img = new window.Image();
                    img.src = src;
                }
            });
        },
        initSearchFromUrl() {
            let categoryParam = null;
            let subcategoryParam = null;
            let searchVal = null;

            if (typeof window !== 'undefined' && window.location?.search) {
                const params = new URLSearchParams(window.location.search);
                categoryParam = params.get('category');
                subcategoryParam = params.get('subcategory');
                searchVal = params.get('search') || params.get('keyword') || params.get('q');
            }

            if ((!categoryParam || !subcategoryParam || !searchVal) && this.$page?.url && this.$page.url.includes('?')) {
                const urlParams = new URLSearchParams(this.$page.url.split('?')[1]);
                if (!categoryParam) {
                    categoryParam = urlParams.get('category');
                }
                if (!subcategoryParam) {
                    subcategoryParam = urlParams.get('subcategory');
                }
                if (!searchVal) {
                    searchVal = urlParams.get('search') || urlParams.get('keyword') || urlParams.get('q');
                }
            }

            if (!categoryParam) {
                categoryParam =
                    this.$page?.props?.route?.query?.category ||
                    this.$page?.props?.ziggy?.query?.category ||
                    this.$attrs?.route?.params?.category;
            }
            if (!subcategoryParam) {
                subcategoryParam =
                    this.$page?.props?.route?.query?.subcategory ||
                    this.$page?.props?.ziggy?.query?.subcategory ||
                    this.$attrs?.route?.params?.subcategory;
            }
            if (!searchVal) {
                searchVal =
                    this.$page?.props?.route?.query?.search ||
                    this.$page?.props?.ziggy?.query?.search ||
                    this.$attrs?.route?.params?.search;
            }

            if (categoryParam && typeof categoryParam === 'string' && categoryParam.trim()) {
                this.filters.category = categoryParam.trim();
            } else if (typeof window !== 'undefined' && (!window.location.search || window.location.search === '')) {
                this.filters.category = 'all';
            }

            if (subcategoryParam && typeof subcategoryParam === 'string' && subcategoryParam.trim()) {
                this.filters.subcategory = subcategoryParam.trim();
            } else {
                this.filters.subcategory = null;
            }

            if (searchVal && typeof searchVal === 'string' && searchVal.trim()) {
                this.filters.search = searchVal.trim();
            }
        },
        handleDocumentClick(e) {
            if (this.$refs.sortDropdownRef && !this.$refs.sortDropdownRef.contains(e.target)) {
                this.isSortOpen = false;
            }
        },
        selectSort(value) {
            this.filters.sortBy = value;
            this.isSortOpen = false;
        },
        handleFilterUpdate(newFilters) {
            this.slideDirection = 'next';
            this.filters = newFilters;
        },
        handlePageChange(page, direction) {
            if (page < 1 || page > this.totalPages || page === this.currentPage) {
                return;
            }
            this.slideDirection = direction || (page >= this.currentPage ? 'next' : 'prev');
            this.currentPage = page;
            this.slideTick += 1;
            this.$nextTick(() => {
                if (typeof window !== 'undefined') {
                    const el = this.$refs.productCatalogTop || document.getElementById('product-catalog-section');
                    if (el) {
                        const rect = el.getBoundingClientRect();
                        const targetTop = Math.max(0, window.pageYOffset + rect.top - 100);
                        window.scrollTo({ top: targetTop, behavior: 'smooth' });
                    }
                }
            });
        },
        clearSearch() {
            this.filters.search = '';
        },
        clearCategory() {
            this.filters.category = 'all';
            this.filters.subcategory = null;
        },
        clearSubcategory() {
            this.filters.subcategory = null;
        },
        removePurpose(purpose) {
            const idx = this.filters.purposes.indexOf(purpose);
            if (idx > -1) {
                this.filters.purposes.splice(idx, 1);
            }
        },
        resetFilters() {
            this.slideDirection = 'next';
            this.filters = {
                search: '',
                category: 'all',
                subcategory: null,
                purposes: [],
                sortBy: 'popular',
            };
            this.currentPage = 1;
        },
    },
}
</script>
