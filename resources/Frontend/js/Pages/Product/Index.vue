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
            <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-[144px]">
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
            <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-[144px]">
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
                    <Transition
                        enter-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"
                        enter-from-class="-translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"
                        leave-from-class="translate-x-0"
                        leave-to-class="-translate-x-full"
                    >
                        <div
                            v-if="isMobileFilterOpen"
                            class="fixed inset-0 z-50 lg:hidden flex"
                        >
                            <!-- Backdrop -->
                            <div
                                class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-opacity duration-300"
                                @click="isMobileFilterOpen = false"
                            ></div>

                            <!-- Offcanvas Slide-in -->
                            <div class="relative w-full max-w-xs bg-[#111322] h-full overflow-y-auto p-4 z-10 border-r border-[#30374F] transition-transform duration-300 ease-out">
                                <ProductFilter
                                    :filters="filters"
                                    :categories="displayCategories"
                                    @update:filters="handleFilterUpdate"
                                    @reset="resetFilters"
                                    @close="isMobileFilterOpen = false"
                                />
                            </div>
                        </div>
                    </Transition>

                    <!-- Right Column: Products Results (Figma 2346:1015) -->
                    <div class="w-full lg:w-[867px] shrink-0 min-w-0 space-y-6">
                        <!-- Top Toolbar (Figma 2352:9087) -->
                        <div class="h-5 flex items-center justify-between gap-4 w-full">
                            <!-- Left: Breadcrumb (Figma 2352:9934) -->
                            <div class="flex items-center gap-2.5 text-sm">
                                <!-- Mobile Filter Trigger -->
                                <button
                                    type="button"
                                    @click="isMobileFilterOpen = true"
                                    class="lg:hidden p-1 text-[#84ADFF] hover:text-white mr-1"
                                    :title="tt('Mở bộ lọc')"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                </button>

                                <Link :href="route('home')" class="text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors leading-[17px]">
                                    {{ tt('Trang chủ') }}
                                </Link>
                                <svg class="w-3.5 h-3.5 text-[#B9C0D4]" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L9.94 8 6.22 4.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-[#FAFAFA] leading-[17px]">
                                    {{ tt('Sản phẩm') }} ({{ filteredProducts.length }} {{ tt('sản phẩm') }})
                                </span>
                            </div>

                            <!-- Right: Custom Sort Dropdown (Dark Tech style) -->
                            <div class="relative" ref="sortDropdownRef">
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
                        <div class="w-full">
                            <div
                                v-if="paginatedProducts.length > 0"
                                class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                            >
                                <ProductCard
                                    v-for="(product, idx) in paginatedProducts"
                                    :key="idx"
                                    :product="product"
                                    :slide-direction="slideDirection"
                                    :slide-key="`${currentPage}-${slideTick}-${product.id}-${filters.category}-${filters.purposes.join(',')}-${filters.search}-${filters.sortBy}`"
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
            // 16 Realistic Production Mock Products (Khớp 100% 16 thẻ trên Figma artboard 2346:669)
            rawMockProducts: [
                {
                    id: 1,
                    slug: 'iron-man-mark-85-resin-8k',
                    categoryId: 'custom',
                    purposeKey: 'space_decor',
                    isBestChoice: true,
                    image: '/assets/images/products/figma-product-1.png',
                    name_vi: 'Mô hình Iron Man Mark 85 - Resin 8K Chi Tiết Cao',
                    description_vi: 'Mô hình giáp Iron Man tỉ lệ 1:10, độ chi tiết sắc nét từng đường rãnh cơ khí với công nghệ SLA quang hóa.',
                    name_en: 'Iron Man Mark 85 Model - High Detail 8K Resin',
                    description_en: '1:10 scale Iron Man armor model with crisp mechanical grooves produced using photochemical SLA technology.',
                },
                {
                    id: 2,
                    slug: 'khung-vo-robot-carbon-fiber',
                    categoryId: 'custom',
                    purposeKey: 'daily_essentials',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-2.png',
                    name_vi: 'Khung Vỏ Cơ Khí Robot Mini - Carbon Fiber Kháng Lực',
                    description_vi: 'Khung robot gia cường sợi carbon chịu lực va đập vượt trội, kháng uốn và ổn định kích thước tuyệt đối.',
                    name_en: 'Mini Robot Mechanical Chassis - Impact Resistant Carbon Fiber',
                    description_en: 'Carbon fiber reinforced robot chassis offering outstanding impact resistance, flexural strength, and dimensional stability.',
                },
                {
                    id: 3,
                    slug: 'hop-dung-danh-thiep-cyberpunk',
                    categoryId: 'stationery',
                    purposeKey: 'daily_essentials',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-3.png',
                    name_vi: 'Hộp Đựng Danh Thiếp & Bút Cyberpunk Hexagon',
                    description_vi: 'Khay bút tổ ong đa giác phong cách Futuristic, tối ưu không gian làm việc công nghệ cao.',
                    name_en: 'Cyberpunk Hexagon Business Card & Pen Holder',
                    description_en: 'Futuristic honeycomb polygon organizer tray, optimizing high-tech desktop workspaces.',
                },
                {
                    id: 4,
                    slug: 'moc-khoa-turbine-phan-luc',
                    categoryId: 'keychains',
                    purposeKey: 'personal_gifts',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-4.png',
                    name_vi: 'Móc Khoá Turbine Phản Lực Có Cánh Quạt Xoay',
                    description_vi: 'Chi tiết cánh quạt turbine khí động học có thể thổi quay mượt mà, sơn phủ titan ánh kim bắt mắt.',
                    name_en: 'Jet Turbine Keychain with Spinning Fan Blades',
                    description_en: 'Aerodynamic turbine blades that spin smoothly when blown, finished with an eye-catching metallic titanium coating.',
                },
                {
                    id: 5,
                    slug: 'gia-do-tai-nghe-voronoi',
                    categoryId: 'stationery',
                    purposeKey: 'space_decor',
                    isBestChoice: true,
                    image: '/assets/images/products/figma-product-5.png',
                    name_vi: 'Giá Đỡ Tai Nghe Headphone Geometrical Voronoi',
                    description_vi: 'Giá đỡ tai nghe dạng lưới cấu trúc bionic Voronoi siêu bền, tôn lên vẻ thẩm mỹ cho góc làm việc.',
                    name_en: 'Voronoi Geometrical Headphone Stand',
                    description_en: 'Ultra-durable bionic Voronoi lattice headphone holder, elevating desktop aesthetics.',
                },
                {
                    id: 6,
                    slug: 'cum-banh-rang-hanh-tinh',
                    categoryId: 'custom',
                    purposeKey: 'daily_essentials',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-6.png',
                    name_vi: 'Bộ Cụm Bánh Răng Hành Tinh Planetary Gearbox',
                    description_vi: 'Hộp số hành tinh tỉ số truyền 1:5, khớp răng chuẩn dung sai cơ khí, xoay êm ái linh hoạt.',
                    name_en: 'Planetary Gearbox Assembly Set',
                    description_en: '1:5 gear reduction planetary gearbox with precise mechanical tolerances and smooth rotation.',
                },
                {
                    id: 7,
                    slug: 'tuong-cyber-wolf-low-poly',
                    categoryId: 'stationery',
                    purposeKey: 'space_decor',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-7.png',
                    name_vi: 'Tượng Điêu Khắc Nghệ Thuật Low-Poly Cyber Wolf',
                    description_vi: 'Tượng sói phong cách đa giác góc cạnh, bề mặt được xử lý mịn màng và phủ lớp nano bảo vệ bền bỉ.',
                    name_en: 'Low-Poly Cyber Wolf Art Sculpture',
                    description_en: 'Geometric faceted wolf sculpture featuring smooth surfaces and protective nano-coating.',
                },
                {
                    id: 8,
                    slug: 'moc-khoa-dong-co-v8-piston',
                    categoryId: 'keychains',
                    purposeKey: 'personal_gifts',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-8.png',
                    name_vi: 'Móc Khoá Động Cơ V8 Piston Chuyển Động',
                    description_vi: 'Mô phỏng trục khuỷu và piston động cơ V8 xe đua cơ bắp chuyển động tịnh tiến khi vặn núm xoay.',
                    name_en: 'Moving Piston V8 Engine Keychain',
                    description_en: 'Simulates crankshaft and reciprocating pistons of a muscle car V8 engine when rotating the dial.',
                },
                {
                    id: 9,
                    slug: 'den-ngu-mat-trang-lithophane',
                    categoryId: 'stationery',
                    purposeKey: 'space_decor',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-9.png',
                    name_vi: 'Đèn Ngủ Mặt Trăng 3D Lithophane Kèm Đế Gỗ',
                    description_vi: 'Quả cầu địa hình bề mặt mặt trăng với công nghệ thấu quang Lithophane, phát sáng dịu nhẹ ấm áp.',
                    name_en: '3D Lithophane Moon Night Lamp with Wooden Base',
                    description_en: 'Moon surface topography sphere with Lithophane light-transmitting technology, emitting soft warm ambient glow.',
                },
                {
                    id: 10,
                    slug: 'khop-noi-cau-bionic-sls',
                    categoryId: 'custom',
                    purposeKey: 'daily_essentials',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-10.png',
                    name_vi: 'Khớp Nối Cầu Đa Hướng Ball-Joint Bionic',
                    description_vi: 'Khớp nối linh hoạt in nguyên khối không cần support, độ bền cơ học và chịu mài mòn chuẩn công nghiệp.',
                    name_en: 'Bionic Multi-Directional Ball-Joint',
                    description_en: 'Monolithic print-in-place flexible joint with zero support material, offering industrial-grade wear resistance.',
                },
                {
                    id: 11,
                    slug: 'mo-hinh-tau-vu-tru-discovery',
                    categoryId: 'stationery',
                    purposeKey: 'space_decor',
                    isBestChoice: true,
                    image: '/assets/images/products/figma-product-11.png',
                    name_vi: 'Mô Hình Tàu Vũ Trụ Discovery 3D Kèm Chân Đế Led',
                    description_vi: 'Mô hình phi thuyền không gian viễn tưởng tỉ mỉ tới từng tấm khiên nhiệt, buồng lái hoàn thiện trong suốt.',
                    name_en: 'Discovery 3D Spacecraft Model with LED Base',
                    description_en: 'Sci-fi spaceship model meticulously crafted down to heat shields and transparent cockpit canopy.',
                },
                {
                    id: 12,
                    slug: 'khay-to-chuc-day-cap-modular',
                    categoryId: 'stationery',
                    purposeKey: 'daily_essentials',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-12.png',
                    name_vi: 'Khay Tổ Chức Dây Cáp & Phụ Kiện Nam Châm Modular',
                    description_vi: 'Khay cáp bàn làm việc ghép nối nam châm Neodymium linh hoạt, gom gọn mọi loại dây sạc gọn gàng.',
                    name_en: 'Modular Magnetic Cable & Accessories Organizer',
                    description_en: 'Flexible desktop cable tray connected with Neodymium magnets, organizing all charging cables neatly.',
                },
                {
                    id: 13,
                    slug: 'moc-khoa-vo-lang-f1-quick-release',
                    categoryId: 'keychains',
                    purposeKey: 'personal_gifts',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-13.png',
                    name_vi: 'Móc Khoá Vô Lăng F1 Quick Release Thể Thao',
                    description_vi: 'Mô phỏng vô lăng xe đua F1 với cơ chế tháo lắp khớp bi cực kỳ chân thực, kim loại phối nhựa dẻo cao cấp.',
                    name_en: 'F1 Quick Release Racing Steering Wheel Keychain',
                    description_en: 'Replicates F1 race car steering wheel with authentic ball-lock quick-release mechanism.',
                },
                {
                    id: 14,
                    slug: 'den-ban-led-hexagon-to-ong',
                    categoryId: 'stationery',
                    purposeKey: 'space_decor',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-14.png',
                    name_vi: 'Đèn Bàn LED Hexagon Tổ Ong Chạm Cảm Ứng',
                    description_vi: 'Các mô-đun đèn lục giác ghép nối tự do theo sở thích, điều khiển ánh sáng bằng cảm ứng bề mặt.',
                    name_en: 'Touch-Sensitive Hexagonal Honeycomb LED Lamp',
                    description_en: 'Modular hexagon light modules freely assembled and operated via capacitive surface touch controls.',
                },
                {
                    id: 15,
                    slug: 'vo-case-ban-phim-co-custom',
                    categoryId: 'custom',
                    purposeKey: 'daily_essentials',
                    isBestChoice: false,
                    image: '/assets/images/products/figma-product-15.png',
                    name_vi: 'Vỏ Case Bàn Phím Cơ Custom In 3D Đa Sắc',
                    description_vi: 'Vỏ phím cơ layout 75% cấu trúc gasket mount tối ưu âm gõ, chịu nhiệt và chống biến dạng xuất sắc.',
                    name_en: 'Custom 3D Printed Multi-Color Mechanical Keyboard Case',
                    description_en: '75% layout keyboard case with gasket-mount structure for acoustic optimization and thermal resistance.',
                },
                {
                    id: 16,
                    slug: 'moc-khoa-trai-tim-co-khi',
                    categoryId: 'keychains',
                    purposeKey: 'personal_gifts',
                    isBestChoice: true,
                    image: '/assets/images/products/figma-product-16.png',
                    name_vi: 'Móc Khoá Trái Tim Cơ Khí Bánh Răng Lồng Nhau',
                    description_vi: 'Trái tim cơ khí độc đáo với cụm bánh răng lồng ghép chuyển động ăn khớp nhịp nhàng khi xoay chốt.',
                    name_en: 'Interlocking Gear Mechanical Heart Keychain',
                    description_en: 'Unique mechanical heart with interlocking gears that mesh smoothly as the pin turns.',
                },
            ],
        }
    },
    computed: {
        currentLocale() {
            return this.$page?.props?.locale?.current || 'vi'
        },
        pageTitle() {
            return this.currentLocale === 'en'
                ? '3D Printed Products Catalog - HH Maker | Ideas Brought To Life'
                : 'Danh mục Sản phẩm in 3D - HH Maker | Bộ sưu tập ý tưởng thành hình'
        },
        pageDescription() {
            return this.currentLocale === 'en'
                ? 'Explore exquisite 3D printed products by HH Maker. Keychains, stationery, models, and custom manufacturing.'
                : 'Khám phá bộ sưu tập sản phẩm in 3D tinh xảo từ HH Maker. Móc khoá, văn phòng phẩm, mô hình và gia công theo yêu cầu.'
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
        activeMockProducts() {
            const isEn = this.currentLocale === 'en';
            return this.rawMockProducts.map(p => {
                const name = isEn ? p.name_en : p.name_vi;
                const desc = isEn ? p.description_en : p.description_vi;
                let category = 'Gia công theo yêu cầu';
                if (p.categoryId === 'keychains') category = isEn ? 'Keychains' : 'Móc khoá';
                else if (p.categoryId === 'stationery') category = isEn ? 'Stationery' : 'Văn phòng phẩm';
                else if (p.categoryId === 'custom') category = isEn ? 'Custom Manufacturing' : 'Gia công theo yêu cầu';

                let purpose = 'Trang trí không gian';
                if (p.purposeKey === 'space_decor') purpose = isEn ? 'Space Decor' : 'Trang trí không gian';
                else if (p.purposeKey === 'daily_essentials') purpose = isEn ? 'Daily Essentials' : 'Đồ dùng hằng ngày';
                else if (p.purposeKey === 'personal_gifts') purpose = isEn ? 'Personal Gifts' : 'Quà tặng cá nhân';

                return {
                    id: p.id,
                    slug: p.slug,
                    name: name,
                    title: name,
                    category: category,
                    categoryId: p.categoryId,
                    purpose: purpose,
                    purposeKey: p.purposeKey,
                    isBestChoice: p.isBestChoice,
                    image: p.image,
                    description: desc,
                };
            });
        },
        allProducts() {
            const currentMocks = this.activeMockProducts;
            if (this.products && this.products.length > 0) {
                if (this.products.length >= 16) {
                    return this.products;
                }
                const isEn = this.currentLocale === 'en';
                const enrichedReal = this.products.map(p => {
                    const matchedMock = currentMocks.find(m => m.id === p.id || m.slug === p.slug);
                    return {
                        ...matchedMock,
                        ...p,
                        image: p.image?.url ? p.image : (matchedMock?.image || '/assets/images/products/figma-sample-1.png'),
                        category: p.category || matchedMock?.category || (isEn ? 'Custom Manufacturing' : 'Gia công theo yêu cầu'),
                        categoryId: p.categoryId || matchedMock?.categoryId || 'custom',
                        purpose: p.purpose || matchedMock?.purpose || (isEn ? 'Space Decor' : 'Trang trí không gian'),
                        isBestChoice: p.isBestChoice !== undefined ? p.isBestChoice : (matchedMock?.isBestChoice ?? true),
                    };
                });
                const realIds = new Set(enrichedReal.map(p => p.id));
                const remainingMocks = currentMocks.filter(m => !realIds.has(m.id));
                return [...enrichedReal, ...remainingMocks];
            }
            return currentMocks;
        },
        displayCategories() {
            if (this.categories && this.categories.length > 0) {
                const mapped = this.categories.map(c => ({
                    id: c.id,
                    slug: c.slug,
                    name: this.tt(c.name || c.title),
                }));
                if (!mapped.some(c => c.id === 'all' || c.slug === 'all')) {
                    return [{ id: 'all', name: this.tt('Tất cả sản phẩm'), slug: 'all' }, ...mapped];
                }
                return mapped;
            }
            return [
                { id: 'all', name: this.tt('Tất cả sản phẩm'), slug: 'all' },
                { id: 'keychains', name: this.tt('Móc khoá'), slug: 'keychains' },
                { id: 'stationery', name: this.tt('Đồ dùng / Trang trí'), slug: 'stationery' },
                { id: 'custom', name: this.tt('Gia công theo yêu cầu'), slug: 'custom' },
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
                    if (targetCat === 'keychains') return p.categoryId === 'keychains' || p.category?.includes('Móc khoá') || p.category?.includes('Keychain');
                    if (targetCat === 'stationery') return p.categoryId === 'stationery' || p.category?.includes('Văn phòng') || p.category?.includes('Stationery') || p.category?.includes('Đồ dùng') || p.category?.includes('Decor');
                    if (targetCat === 'custom') return p.categoryId === 'custom' || p.category?.includes('Gia công') || p.category?.includes('Custom');
                    return p.categoryId === targetCat || p.category === targetCat || (p.categories && p.categories.some(c => c.id == targetCat || c.slug == targetCat));
                });
            }

            // 3. Purposes filter
            if (this.filters.purposes.length > 0) {
                result = result.filter((p) => {
                    return this.filters.purposes.some(sel => {
                        if (sel === p.purpose) return true;
                        if (p.purposeKey === 'space_decor' && (sel === 'Trang trí không gian' || sel === 'Space Decor')) return true;
                        if (p.purposeKey === 'daily_essentials' && (sel === 'Đồ dùng hằng ngày' || sel === 'Daily Essentials')) return true;
                        if (p.purposeKey === 'personal_gifts' && (sel === 'Quà tặng cá nhân' || sel === 'Personal Gifts')) return true;
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
            if (typeof window !== 'undefined') {
                const params = new URLSearchParams(window.location.search);
                const searchVal = params.get('search') || params.get('keyword') || params.get('q');
                if (searchVal && searchVal.trim()) {
                    this.filters.search = searchVal.trim();
                    return;
                }
            }
            // Check Ziggy or route params
            const ziggySearch = this.$page?.props?.ziggy?.query?.search || this.$attrs?.route?.params?.search;
            if (ziggySearch && typeof ziggySearch === 'string' && ziggySearch.trim()) {
                this.filters.search = ziggySearch.trim();
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
