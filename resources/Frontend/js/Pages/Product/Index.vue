<template>
    <div class="bg-[#111322] text-[#FAFAFA] min-h-screen overflow-x-hidden selection:bg-[#2970FF] selection:text-white">
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
            <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-[144px]">
                <div class="flex flex-col gap-6">
                    <!-- Eyebrow (Figma 2346:703) -->
                    <span class="text-sm font-normal text-[#84ADFF] tracking-wider uppercase leading-5">
                        BỘ SƯU TẬP HH MAKER
                    </span>

                    <!-- Tiêu đề chính (Figma 2346:705) -->
                    <h1 class="text-3xl sm:text-[38px] font-bold text-[#FAFAFA] leading-tight sm:leading-[46px] font-sans">
                        Ý TƯỞNG THÀNH HÌNH
                    </h1>
                </div>
            </div>
        </section>

        <!-- 2. Main Catalog Content Area (Figma 2346:994 - Sidebar Layout) -->
        <section class="pb-12 bg-[#111322]">
            <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-[144px]">
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
                    <!-- Left Sidebar (Desktop: Cố định 240px, Mobile: Offcanvas Drawer) -->
                    <aside class="hidden lg:block w-[240px] shrink-0 sticky top-24">
                        <ProductFilter
                            :filters="filters"
                            :categories="categories"
                            @update:filters="handleFilterUpdate"
                            @reset="resetFilters"
                        />
                    </aside>

                    <!-- Mobile Drawer for Filter -->
                    <div
                        v-if="isMobileFilterOpen"
                        class="fixed inset-0 z-50 lg:hidden flex"
                    >
                        <!-- Backdrop -->
                        <div
                            class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-opacity"
                            @click="isMobileFilterOpen = false"
                        ></div>

                        <!-- Offcanvas Slide-in -->
                        <div class="relative w-full max-w-xs bg-[#111322] h-full overflow-y-auto p-4 z-10 border-r border-[#30374F]">
                            <ProductFilter
                                :filters="filters"
                                :categories="categories"
                                @update:filters="handleFilterUpdate"
                                @reset="resetFilters"
                                @close="isMobileFilterOpen = false"
                            />
                        </div>
                    </div>

                    <!-- Right Column: Products Results (Figma 2346:1015) -->
                    <div class="flex-1 w-full min-w-0 space-y-6">
                        <!-- Top Toolbar (Figma 2352:9087) -->
                        <div class="h-6 flex items-center justify-between gap-4 w-full">
                            <!-- Left: Breadcrumb (Figma 2352:9934) -->
                            <div class="flex items-center gap-2.5 text-sm">
                                <!-- Mobile Filter Trigger -->
                                <button
                                    type="button"
                                    @click="isMobileFilterOpen = true"
                                    class="lg:hidden p-1 text-[#84ADFF] hover:text-white mr-1"
                                    title="Mở bộ lọc"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                </button>

                                <Link href="/" class="text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors leading-[17px]">
                                    Trang chủ
                                </Link>
                                <svg class="w-3.5 h-3.5 text-[#B9C0D4]" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L9.94 8 6.22 4.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-[#FAFAFA] leading-[17px]">
                                    Sản phẩm ({{ filteredProducts.length }} sản phẩm)
                                </span>
                            </div>

                            <!-- Right: Dropdown Sắp xếp (Figma 2380:1530) -->
                            <div class="relative flex items-center gap-1">
                                <label for="sort-select" class="sr-only">Sắp xếp</label>
                                <select
                                    id="sort-select"
                                    v-model="filters.sortBy"
                                    class="bg-transparent border-none text-sm text-[#FAFAFA] focus:outline-none appearance-none cursor-pointer pr-6 py-0 font-medium"
                                >
                                    <option value="popular" class="bg-[#1e2438] text-white">Sắp xếp: Nổi bật nhất</option>
                                    <option value="newest" class="bg-[#1e2438] text-white">Sắp xếp: Mới nhất</option>
                                    <option value="name_asc" class="bg-[#1e2438] text-white">Sắp xếp: Tên A-Z</option>
                                </select>
                                <svg class="w-4 h-4 text-[#FAFAFA] absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <!-- Products Grid (Figma 2346:717 - 3 cột chuẩn) -->
                        <div
                            v-if="paginatedProducts.length > 0"
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 transition-all duration-300"
                        >
                            <ProductCard
                                v-for="product in paginatedProducts"
                                :key="product.id"
                                :product="product"
                            />
                        </div>

                        <!-- Empty State -->
                        <div
                            v-else
                            class="py-16 px-4 text-center rounded-2xl bg-[#30374F] space-y-4"
                        >
                            <div class="w-16 h-16 mx-auto rounded-full bg-[#111322] flex items-center justify-center text-[#84ADFF]">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-[#FAFAFA]">Không tìm thấy sản phẩm phù hợp</h3>
                            <p class="text-sm text-[#B9C0D4] max-w-md mx-auto">
                                Rất tiếc, không có sản phẩm nào đáp ứng các tiêu chí lọc hiện tại. Bạn vui lòng thử thay đổi từ khoá tìm kiếm hoặc đặt lại bộ lọc.
                            </p>
                            <button
                                type="button"
                                @click="resetFilters"
                                class="px-5 py-2.5 rounded-lg bg-[#2970FF] hover:bg-[#155EEF] text-white text-xs font-semibold shadow-md transition-all duration-200"
                            >
                                Đặt lại bộ lọc
                            </button>
                        </div>

                        <!-- Pagination (Figma 2352:9091) -->
                        <ProductPagination
                            :current-page="currentPage"
                            :totalPages="totalPages"
                            @change-page="handlePageChange"
                        />
                    </div>
                </div>

                <!-- 3. Bottom CTA Banner (Figma 2346:741 - Lời mời sáng tạo) -->
                <ProductCtaBanner />
            </div>
        </section>
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
    },
    data() {
        return {
            pageTitle: 'Danh mục Sản phẩm in 3D - HH Maker | Bộ sưu tập ý tưởng thành hình',
            pageDescription: 'Khám phá bộ sưu tập sản phẩm in 3D tinh xảo từ HH Maker. Móc khoá, văn phòng phẩm, mô hình và gia công theo yêu cầu.',
            isMobileFilterOpen: false,
            currentPage: 1,
            pageSize: 16,
            filters: {
                search: '',
                category: 'all',
                purposes: [],
                sortBy: 'popular',
            },
                        // 16 Realistic Production Mock Products (Khớp 100% 16 thẻ trên Figma artboard 2346:669)
            mockProducts: [
                {
                    id: 1,
                    slug: 'iron-man-mark-85-resin-8k',
                    name: 'Mô hình Iron Man Mark 85 - Resin 8K Chi Tiết Cao',
                    title: 'Mô hình Iron Man Mark 85 - Resin 8K Chi Tiết Cao',
                    category: 'Gia công theo yêu cầu',
                    categoryId: 'custom',
                    isBestChoice: true,
                    image: '/assets/images/products/figma-product-1.png',
                    purpose: 'Trang trí không gian',
                    description: 'Mô hình giáp Iron Man tỉ lệ 1:10, độ chi tiết sắc nét từng đường rãnh cơ khí với công nghệ SLA quang hóa.',
                },
                {
                    id: 2,
                    slug: 'khung-vo-robot-carbon-fiber',
                    name: 'Khung Vỏ Cơ Khí Robot Mini - Carbon Fiber Kháng Lực',
                    title: 'Khung Vỏ Cơ Khí Robot Mini - Carbon Fiber Kháng Lực',
                    category: 'Gia công theo yêu cầu',
                    categoryId: 'custom',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-2.png',
                    purpose: 'Đồ dùng hằng ngày',
                    description: 'Khung robot gia cường sợi carbon chịu lực va đập vượt trội, kháng uốn và ổn định kích thước tuyệt đối.',
                },
                {
                    id: 3,
                    slug: 'hop-dung-danh-thiep-cyberpunk',
                    name: 'Hộp Đựng Danh Thiếp & Bút Cyberpunk Hexagon',
                    title: 'Hộp Đựng Danh Thiếp & Bút Cyberpunk Hexagon',
                    category: 'Văn phòng phẩm',
                    categoryId: 'stationery',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-3.png',
                    purpose: 'Đồ dùng hằng ngày',
                    description: 'Khay bút tổ ong đa giác phong cách Futuristic, tối ưu không gian làm việc công nghệ cao.',
                },
                {
                    id: 4,
                    slug: 'moc-khoa-turbine-phan-luc',
                    name: 'Móc Khoá Turbine Phản Lực Có Cánh Quạt Xoay',
                    title: 'Móc Khoá Turbine Phản Lực Có Cánh Quạt Xoay',
                    category: 'Móc khoá',
                    categoryId: 'keychains',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-4.png',
                    purpose: 'Quà tặng cá nhân',
                    description: 'Chi tiết cánh quạt turbine khí động học có thể thổi quay mượt mà, sơn phủ titan ánh kim bắt mắt.',
                },
                {
                    id: 5,
                    slug: 'gia-do-tai-nghe-voronoi',
                    name: 'Giá Đỡ Tai Nghe Headphone Geometrical Voronoi',
                    title: 'Giá Đỡ Tai Nghe Headphone Geometrical Voronoi',
                    category: 'Văn phòng phẩm',
                    categoryId: 'stationery',
                    isBestChoice: true,
                    image: '/assets/images/products/figma-product-5.png',
                    purpose: 'Trang trí không gian',
                    description: 'Giá đỡ tai nghe dạng lưới cấu trúc bionic Voronoi siêu bền, tôn lên vẻ thẩm mỹ cho góc làm việc.',
                },
                {
                    id: 6,
                    slug: 'cum-banh-rang-hanh-tinh',
                    name: 'Bộ Cụm Bánh Răng Hành Tinh Planetary Gearbox',
                    title: 'Bộ Cụm Bánh Răng Hành Tinh Planetary Gearbox',
                    category: 'Gia công theo yêu cầu',
                    categoryId: 'custom',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-6.png',
                    purpose: 'Đồ dùng hằng ngày',
                    description: 'Hộp số hành tinh tỉ số truyền 1:5, khớp răng chuẩn dung sai cơ khí, xoay êm ái linh hoạt.',
                },
                {
                    id: 7,
                    slug: 'tuong-cyber-wolf-low-poly',
                    name: 'Tượng Điêu Khắc Nghệ Thuật Low-Poly Cyber Wolf',
                    title: 'Tượng Điêu Khắc Nghệ Thuật Low-Poly Cyber Wolf',
                    category: 'Văn phòng phẩm',
                    categoryId: 'stationery',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-7.png',
                    purpose: 'Trang trí không gian',
                    description: 'Tượng sói phong cách đa giác góc cạnh, bề mặt được xử lý mịn màng và phủ lớp nano bảo vệ bền bỉ.',
                },
                {
                    id: 8,
                    slug: 'moc-khoa-dong-co-v8-piston',
                    name: 'Móc Khoá Động Cơ V8 Piston Chuyển Động',
                    title: 'Móc Khoá Động Cơ V8 Piston Chuyển Động',
                    category: 'Móc khoá',
                    categoryId: 'keychains',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-8.png',
                    purpose: 'Quà tặng cá nhân',
                    description: 'Mô phỏng trục khuỷu và piston động cơ V8 xe đua cơ bắp chuyển động tịnh tiến khi vặn núm xoay.',
                },
                {
                    id: 9,
                    slug: 'den-ngu-mat-trang-lithophane',
                    name: 'Đèn Ngủ Mặt Trăng 3D Lithophane Kèm Đế Gỗ',
                    title: 'Đèn Ngủ Mặt Trăng 3D Lithophane Kèm Đế Gỗ',
                    category: 'Văn phòng phẩm',
                    categoryId: 'stationery',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-9.png',
                    purpose: 'Trang trí không gian',
                    description: 'Quả cầu địa hình bề mặt mặt trăng với công nghệ thấu quang Lithophane, phát sáng dịu nhẹ ấm áp.',
                },
                {
                    id: 10,
                    slug: 'khop-noi-cau-bionic-sls',
                    name: 'Khớp Nối Cầu Đa Hướng Ball-Joint Bionic',
                    title: 'Khớp Nối Cầu Đa Hướng Ball-Joint Bionic',
                    category: 'Gia công theo yêu cầu',
                    categoryId: 'custom',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-10.png',
                    purpose: 'Đồ dùng hằng ngày',
                    description: 'Khớp nối linh hoạt in nguyên khối không cần support, độ bền cơ học và chịu mài mòn chuẩn công nghiệp.',
                },
                {
                    id: 11,
                    slug: 'mo-hinh-tau-vu-tru-discovery',
                    name: 'Mô Hình Tàu Vũ Trụ Discovery 3D Kèm Chân Đế Led',
                    title: 'Mô Hình Tàu Vũ Trụ Discovery 3D Kèm Chân Đế Led',
                    category: 'Văn phòng phẩm',
                    categoryId: 'stationery',
                    isBestChoice: true,
                    image: '/assets/images/products/figma-product-11.png',
                    purpose: 'Trang trí không gian',
                    description: 'Mô hình phi thuyền không gian viễn tưởng tỉ mỉ tới từng tấm khiên nhiệt, buồng lái hoàn thiện trong suốt.',
                },
                {
                    id: 12,
                    slug: 'khay-to-chuc-day-cap-modular',
                    name: 'Khay Tổ Chức Dây Cáp & Phụ Kiện Nam Châm Modular',
                    title: 'Khay Tổ Chức Dây Cáp & Phụ Kiện Nam Châm Modular',
                    category: 'Văn phòng phẩm',
                    categoryId: 'stationery',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-12.png',
                    purpose: 'Đồ dùng hằng ngày',
                    description: 'Khay cáp bàn làm việc ghép nối nam châm Neodymium linh hoạt, gom gọn mọi loại dây sạc gọn gàng.',
                },
                {
                    id: 13,
                    slug: 'moc-khoa-vo-lang-f1-quick-release',
                    name: 'Móc Khoá Vô Lăng F1 Quick Release Thể Thao',
                    title: 'Móc Khoá Vô Lăng F1 Quick Release Thể Thao',
                    category: 'Móc khoá',
                    categoryId: 'keychains',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-13.png',
                    purpose: 'Quà tặng cá nhân',
                    description: 'Mô phỏng vô lăng xe đua F1 với cơ chế tháo lắp khớp bi cực kỳ chân thực, kim loại phối nhựa dẻo cao cấp.',
                },
                {
                    id: 14,
                    slug: 'den-ban-led-hexagon-to-ong',
                    name: 'Đèn Bàn LED Hexagon Tổ Ong Chạm Cảm Ứng',
                    title: 'Đèn Bàn LED Hexagon Tổ Ong Chạm Cảm Ứng',
                    category: 'Văn phòng phẩm',
                    categoryId: 'stationery',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-14.png',
                    purpose: 'Trang trí không gian',
                    description: 'Các mô-đun đèn lục giác ghép nối tự do theo sở thích, điều khiển ánh sáng bằng cảm ứng bề mặt.',
                },
                {
                    id: 15,
                    slug: 'vo-case-ban-phim-co-custom',
                    name: 'Vỏ Case Bàn Phím Cơ Custom In 3D Đa Sắc',
                    title: 'Vỏ Case Bàn Phím Cơ Custom In 3D Đa Sắc',
                    category: 'Gia công theo yêu cầu',
                    categoryId: 'custom',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-15.png',
                    purpose: 'Đồ dùng hằng ngày',
                    description: 'Vỏ phím cơ layout 75% cấu trúc gasket mount tối ưu âm gõ, chịu nhiệt và chống biến dạng xuất sắc.',
                },
                {
                    id: 16,
                    slug: 'moc-khoa-trai-tim-co-khi',
                    name: 'Móc Khoá Trái Tim Cơ Khí Bánh Răng Lồng Nhau',
                    title: 'Móc Khoá Trái Tim Cơ Khí Bánh Răng Lồng Nhau',
                    category: 'Móc khoá',
                    categoryId: 'keychains',
                    isBestChoice: true,
                    image: '/assets/images/products/figma-product-16.png',
                    purpose: 'Quà tặng cá nhân',
                    description: 'Trái tim cơ khí độc đáo với cụm bánh răng lồng ghép chuyển động ăn khớp nhịp nhàng khi xoay chốt.',
                },
            ],
        }
    },
    computed: {
        allProducts() {
            if (this.products && this.products.length > 0) {
                if (this.products.length >= 16) {
                    return this.products;
                }
                const enrichedReal = this.products.map(p => {
                    const matchedMock = this.mockProducts.find(m => m.id === p.id || m.slug === p.slug);
                    return {
                        ...matchedMock,
                        ...p,
                        image: p.image?.url ? p.image : (matchedMock?.image || '/assets/images/products/figma-sample-1.png'),
                        category: p.category || matchedMock?.category || 'Gia công theo yêu cầu',
                        categoryId: p.categoryId || matchedMock?.categoryId || 'custom',
                        purpose: p.purpose || matchedMock?.purpose || 'Trang trí không gian',
                        isBestChoice: p.isBestChoice !== undefined ? p.isBestChoice : (matchedMock?.isBestChoice ?? true),
                    };
                });
                const realIds = new Set(enrichedReal.map(p => p.id));
                const remainingMocks = this.mockProducts.filter(m => !realIds.has(m.id));
                return [...enrichedReal, ...remainingMocks];
            }
            return this.mockProducts;
        },
        categories() {
            return [
                { id: 'all', name: 'Tất cả sản phẩm' },
                { id: 'keychains', name: 'Móc khoá' },
                { id: 'stationery', name: 'Văn phòng phẩm' },
                { id: 'custom', name: 'Gia công theo yêu cầu' },
            ];
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
                    if (targetCat === 'keychains') return p.category?.includes('Móc khoá') || p.categoryId === 'keychains';
                    if (targetCat === 'stationery') return p.category?.includes('Văn phòng') || p.categoryId === 'stationery';
                    if (targetCat === 'custom') return p.category?.includes('Gia công') || p.categoryId === 'custom';
                    return p.categoryId === targetCat || p.category === targetCat;
                });
            }

            // 3. Purposes filter
            if (this.filters.purposes.length > 0) {
                result = result.filter((p) => this.filters.purposes.includes(p.purpose));
            }

            // 4. Sorting
            if (this.filters.sortBy === 'newest') {
                result.sort((a, b) => b.id - a.id);
            } else if (this.filters.sortBy === 'popular') {
                result.sort((a, b) => (b.isBestChoice ? 1 : 0) - (a.isBestChoice ? 1 : 0));
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
        filters: {
            deep: true,
            handler() {
                this.currentPage = 1;
            },
        },
    },
    methods: {
        handleFilterUpdate(newFilters) {
            this.filters = newFilters;
        },
        handlePageChange(page) {
            this.currentPage = page;
        },
        resetFilters() {
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
