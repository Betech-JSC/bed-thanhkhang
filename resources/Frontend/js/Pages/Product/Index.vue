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
        <section class="relative pt-10 pb-8 lg:pt-10 lg:pb-10 bg-[#111322]">
            <div class="app-container">
                <div class="flex flex-col gap-6">
                    <!-- Eyebrow (Figma 2346:703) -->
                    <span class="text-sm font-normal text-[#84ADFF] uppercase leading-5">
                        {{ tt('BỘ SƯU TẬP HH MAKER') }}
                    </span>

                    <!-- Tiêu đề chính (Figma 2346:705) -->
                    <h1 class="text-3xl sm:text-[38px] font-bold text-[#FAFAFA] leading-tight sm:leading-[46px] font-sans">
                        {{ tt('Ý TƯỞNG THÀNH HÌNH') }}
                    </h1>
                </div>
            </div>
        </section>

        <!-- 2. Main Catalog Content Area (Figma 2346:994 - Sidebar Layout) -->
        <section class="pb-[12px] bg-[#111322]">
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
                    <div class="w-full lg:w-[867px] shrink-0 min-w-0 space-y-6">
                        <!-- Top Toolbar (Figma 2352:9087) -->
                        <div class="min-h-[24px] sm:h-5 flex items-center justify-between gap-2 sm:gap-4 w-full">
                            <!-- Left: Breadcrumb (Figma 2352:9934) -->
                            <div class="flex items-center gap-1.5 sm:gap-2.5 text-xs sm:text-sm whitespace-nowrap min-w-0 overflow-hidden">
                                <!-- Mobile Filter Trigger -->
                                <button
                                    type="button"
                                    @click="isMobileFilterOpen = true"
                                    class="lg:hidden p-1 text-[#84ADFF] hover:text-white mr-1 shrink-0"
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
                                    {{ tt('Sản phẩm') }}
                                </span>
                            </div>

                            <!-- Right: Custom Sort Dropdown (Dark Tech style) -->
                            <div class="relative shrink-0" ref="sortDropdownRef">
                                <button
                                    type="button"
                                    @click="isSortOpen = !isSortOpen"
                                    class="bg-[#1A1F36] hover:bg-[#222842] border border-[#404968] hover:border-[#2970FF] rounded-[8px] px-3.5 py-1.5 text-sm text-[#FAFAFA] flex items-center gap-2 shadow-sm transition-colors duration-200 cursor-pointer select-none"
                                    :class="{ 'border-[#2970FF] ring-1 ring-[#2970FF]/40': isSortOpen }"
                                    aria-haspopup="listbox"
                                    :aria-expanded="isSortOpen"
                                >
                                    <svg class="w-3.5 h-3.5 text-[#84ADFF]" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                    </svg>
                                    <span class="font-medium text-xs sm:text-sm text-[#FAFAFA]">{{ currentSortLabel }}</span>
                                    <svg
                                        class="w-3.5 h-3.5 text-[#B9C0D4] transition-transform duration-200"
                                        :class="{ 'rotate-180 text-[#2970FF]': isSortOpen }"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <!-- Dropdown Menu Popover -->
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
                            </div>
                        </div>

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
                                    class="px-5 py-2.5 rounded-lg bg-[#2970FF] hover:bg-[#155EEF] text-white text-xs font-semibold shadow-md transition-colors duration-200 active:scale-95"
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
                ? '3D Printed Products Catalog - HH Maker | Ideas Brought To Life'
                : this.tt('Danh mục Sản phẩm in 3D - HH Maker | Bộ sưu tập ý tưởng thành hình')
        },
        pageDescription() {
            return this.currentLocale === 'en'
                ? 'Explore exquisite 3D printed products by HH Maker. Keychains, stationery, models, and custom manufacturing.'
                : this.tt('Khám phá bộ sưu tập sản phẩm in 3D tinh xảo từ HH Maker. Móc khoá, văn phòng phẩm, mô hình và gia công theo yêu cầu.')
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
        displayCategories() {
            const list = [{ id: 'all', name: this.tt('Tất cả sản phẩm'), slug: 'all' }];
            if (this.categories && this.categories.length > 0) {
                this.categories.forEach(c => {
                    const id = c.slug || c.id;
                    if (id !== 'all') {
                        list.push({
                            id: id,
                            slug: c.slug || c.id,
                            name: this.tt(c.name || c.title),
                        });
                    }
                });
            }
            return list;
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
                result = result.filter((p) => {
                    // Check if targetCat matches real DB categories
                    if (p.categories && Array.isArray(p.categories) && p.categories.some(c => c.id == targetCat || c.slug == targetCat)) {
                        return true;
                    }
                    if (p.categoryId === targetCat || p.category === targetCat) {
                        return true;
                    }
                    // Map DB slugs and aliases to mock/product properties
                    const catStr = (typeof p.category === 'string' ? p.category : (p.category?.title || p.category?.name || '')).toLowerCase();
                    if (targetCat === 'moc-khoa-phu-kien' || targetCat === 'keychains-accessories' || targetCat === 'keychains') {
                        return p.categoryId === 'keychains' || catStr.includes('móc khoá') || catStr.includes('móc khóa') || catStr.includes('keychain');
                    }
                    if (targetCat === 'trang-tri-khong-gian' || targetCat === 'space-decor') {
                        return p.purposeKey === 'space_decor' || p.categoryId === 'space_decor' || catStr.includes('không gian') || catStr.includes('space');
                    }
                    if (targetCat === 'trang-tri-do-dung' || targetCat === 'decor-lifestyle' || targetCat === 'stationery') {
                        return p.categoryId === 'stationery' || catStr.includes('đồ dùng') || catStr.includes('decor') || catStr.includes('stationery');
                    }
                    if (targetCat === 'gia-cong-theo-yeu-cau' || targetCat === 'custom-manufacturing' || targetCat === 'custom') {
                        return p.categoryId === 'custom' || catStr.includes('gia công') || catStr.includes('custom');
                    }
                    return false;
                });
            }

            // 3. Purposes filter
            if (this.filters.purposes.length > 0) {
                result = result.filter((p) => {
                    return this.filters.purposes.some(sel => {
                        if (sel === p.purpose) return true;
                        if (p.purposeKey === 'space_decor' && (sel === this.tt('Trang trí không gian') || sel === 'Space Decor')) return true;
                        if (p.purposeKey === 'daily_essentials' && (sel === this.tt('Đồ dùng hằng ngày') || sel === 'Daily Essentials')) return true;
                        if (p.purposeKey === 'personal_gifts' && (sel === this.tt('Quà tặng cá nhân') || sel === 'Personal Gifts')) return true;
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
            let searchVal = null;

            if (typeof window !== 'undefined' && window.location?.search) {
                const params = new URLSearchParams(window.location.search);
                categoryParam = params.get('category');
                searchVal = params.get('search') || params.get('keyword') || params.get('q');
            }

            if (!categoryParam && this.$page?.url && this.$page.url.includes('?')) {
                const urlParams = new URLSearchParams(this.$page.url.split('?')[1]);
                categoryParam = urlParams.get('category');
                if (!searchVal) {
                    searchVal = urlParams.get('search') || urlParams.get('keyword') || urlParams.get('q');
                }
            }

            if (!categoryParam) {
                categoryParam = this.$page?.props?.ziggy?.query?.category || this.$attrs?.route?.params?.category;
            }
            if (!searchVal) {
                searchVal = this.$page?.props?.ziggy?.query?.search || this.$attrs?.route?.params?.search;
            }

            if (categoryParam && typeof categoryParam === 'string' && categoryParam.trim()) {
                this.filters.category = categoryParam.trim();
            } else if (typeof window !== 'undefined' && (!window.location.search || window.location.search === '')) {
                this.filters.category = 'all';
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
            this.slideDirection = direction || (page >= this.currentPage ? 'next' : 'prev');
            this.currentPage = page;
            this.slideTick += 1;
        },
        resetFilters() {
            this.slideDirection = 'next';
            this.filters = {
                search: '',
                category: 'all',
                purposes: [],
                sortBy: 'popular',
            };
            this.currentPage = 1;
        },
    },
}
</script>
