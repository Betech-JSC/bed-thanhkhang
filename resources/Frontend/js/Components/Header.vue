<template>
    <header
        class="fixed top-0 inset-x-0 z-[1000] bg-[#111322] antialiased transition-colors duration-200"
        @mouseleave="menuSelected = null"
    >
        <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20 h-[64px] lg:h-[111px] lg:py-8 flex items-center justify-between">
            <!-- Brand Logo HH Maker (Node 2352:10264) -->
            <div class="flex items-center shrink-0">
                <Link :href="route('home')" class="inline-flex items-center">
                    <img
                        src="/assets/images/header/figma-header-logo.svg"
                        alt="HH Maker Logo"
                        class="h-8 lg:h-[47px] w-auto lg:w-[139px] object-contain"
                    />
                </Link>
            </div>

            <!-- Desktop Navigation Menu 5 Tabs & Right Actions Grouped (Node 2352:10717) -->
            <div class="hidden lg:flex items-center gap-8 xl:gap-[64px]">
                <!-- Desktop Navigation Menu 5 Tabs (Frame 261 / Node 2352:10269) -->
                <nav class="flex items-center gap-5 xl:gap-6 h-[47px]">
                    <template v-for="(menu, index) in menus" :key="index">
                        <div
                            v-if="menu && menu.title"
                            class="relative h-[47px] flex items-center"
                            @mouseenter="setMenuSelected(menu)"
                            @mouseover="setMenuSelected(menu)"
                            @mouseleave="setMenuSelected(null)"
                        >
                            <Link
                                :href="menu.slug || '#'"
                                class="h-[47px] text-base font-semibold leading-6 transition-colors duration-200 flex items-center gap-1.5 border-b-2"
                                :class="isMenuActive(menu)
                                    ? 'text-[#2970FF] border-[#2970FF]'
                                    : 'text-[#FAFAFA] border-transparent hover:text-[#2970FF]'"
                                @click="menuSelected = null"
                            >
                                <span>{{ menu.title }}</span>
                                <!-- Chevron-down 20px for tab Sản phẩm (Node 2352:10273) -->
                                <svg
                                    v-if="menu.type === 'products' || (menu.subMenu && menu.subMenu.length > 0)"
                                    class="w-5 h-5 transition-transform duration-200 shrink-0"
                                    :class="(menuSelected === menu || (menuSelected && menuSelected.type === menu.type)) ? 'rotate-180' : ''"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M5 7.5L10 12.5L15 7.5"
                                        stroke="currentColor"
                                        stroke-width="1.67"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </Link>

                            <!-- Submenu Dropdown Desktop with Hover Bridge (Dark Tech) -->
                            <transition name="dropdown-fade">
                                <div
                                    v-show="(menuSelected === menu || (menuSelected && menuSelected.type === menu.type)) && menu.subMenu && menu.subMenu.length > 0"
                                    class="absolute top-full left-0 pt-2 z-[1010]"
                                >
                                    <div class="min-w-[240px] w-[280px] bg-[#1A1F36]/95 backdrop-blur-md border border-[#30374F] rounded-xl shadow-[0_16px_40px_rgba(0,0,0,0.6)] p-2 flex flex-col gap-1">
                                        <!-- Category Items -->
                                        <Link
                                            v-for="(sub, subIdx) in menu.subMenu.filter(s => !s.isAll)"
                                            :key="subIdx"
                                            :href="sub.url || sub.slug"
                                            class="px-3 py-2.5 rounded-lg text-sm font-medium text-[#B9C0D4] hover:text-[#FAFAFA] hover:bg-[#222842] flex items-center justify-between transition-colors group relative"
                                            @click="menuSelected = null"
                                        >
                                            <span class="truncate">{{ sub.title }}</span>
                                            <svg
                                                class="w-4 h-4 text-[#84ADFF] opacity-0 group-hover:opacity-100 transition-opacity shrink-0 ml-2"
                                                viewBox="0 0 16 16"
                                                fill="currentColor"
                                            >
                                                <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L9.94 8 6.22 4.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>

                                        <!-- Divider & View All Link -->
                                        <div class="pt-1 mt-1 border-t border-[#30374F]">
                                            <Link
                                                :href="route('products.index')"
                                                class="px-3 py-2 rounded-lg text-xs font-semibold text-[#84ADFF] hover:text-[#FAFAFA] hover:bg-[#222842] flex items-center justify-between transition-colors"
                                                @click="menuSelected = null"
                                            >
                                                <span>{{ tt('Xem tất cả sản phẩm') }}</span>
                                                <svg
                                                    class="w-4 h-4 shrink-0"
                                                    viewBox="0 0 16 16"
                                                    fill="none"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        d="M3.33334 8H12.6667M8 3.33334L12.6667 8L8 12.6667"
                                                        stroke="currentColor"
                                                        stroke-width="1.5"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </template>
                </nav>

                <!-- Right Actions Desktop (Node 2380:1132 / Frame 1000005802): Search, Lang Switcher, CTA -->
                <div class="flex items-center gap-8 xl:gap-10 shrink-0">
                    <!-- 1. Icon Tìm kiếm (Node 2352:10286) -->
                    <button
                        type="button"
                        @click="isSearchOpen = true"
                        class="w-6 h-6 flex items-center justify-center hover:opacity-80 transition-opacity shrink-0 cursor-pointer"
                        :title="tt('Tìm kiếm (Ctrl+K)')"
                        :aria-label="tt('Tìm kiếm')"
                    >
                        <img
                            src="/assets/images/header/figma-search-icon.svg"
                            :alt="tt('Tìm kiếm')"
                            class="w-6 h-6 object-contain"
                        />
                    </button>

                    <!-- 2. Language Switcher (Node 2380:1477) -->
                    <div class="w-[96px] h-[34px] border border-[#5D6B98] rounded-[8px] flex items-center justify-between p-1 shrink-0 select-none">
                        <template v-if="currentLocale === 'vi'">
                            <button
                                type="button"
                                @click="switchLocale('vi')"
                                class="bg-[#FAFAFA] text-[#111322] w-[36px] h-[24px] rounded-[4px] text-xs font-semibold flex items-center justify-center shadow-xs cursor-default"
                                :title="tt('Tiếng Việt')"
                            >
                                VIE
                            </button>
                            <button
                                type="button"
                                @click="switchLocale('en')"
                                class="text-white text-sm font-semibold pr-2 flex items-center justify-center hover:text-[#FAFAFA]/80 transition-colors"
                                title="English"
                            >
                                ENG
                            </button>
                        </template>
                        <template v-else>
                            <button
                                type="button"
                                @click="switchLocale('vi')"
                                class="text-white text-sm font-semibold pl-2 flex items-center justify-center hover:text-[#FAFAFA]/80 transition-colors"
                                :title="tt('Tiếng Việt')"
                            >
                                VIE
                            </button>
                            <button
                                type="button"
                                @click="switchLocale('en')"
                                class="bg-[#FAFAFA] text-[#111322] w-[42px] h-[24px] rounded-[4px] text-xs font-semibold flex items-center justify-center shadow-xs cursor-default"
                                title="English"
                            >
                                ENG
                            </button>
                        </template>
                    </div>

                    <!-- 3. Nút CTA Liên hệ ngay (Node 2352:10291) -->
                    <Link
                        :href="route('contact')"
                        class="w-[137px] h-[40px] rounded-[8px] flex items-center justify-center text-base font-medium text-[#F5F8FF] shadow-xs hover:opacity-90 transition-opacity shrink-0"
                        style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
                    >
                        {{ tt('Liên hệ ngay') }}
                    </Link>
                </div>
            </div>

            <!-- Mobile Controls (< 1024px) -->
            <div class="flex items-center gap-3 lg:hidden shrink-0">
                <button
                    type="button"
                    @click="isSearchOpen = true"
                    class="w-6 h-6 flex items-center justify-center hover:opacity-80 transition-opacity cursor-pointer"
                    :title="tt('Tìm kiếm')"
                    :aria-label="tt('Tìm kiếm')"
                >
                    <img
                        src="/assets/images/header/figma-search-icon.svg"
                        :alt="tt('Tìm kiếm')"
                        class="w-5 h-5 object-contain"
                    />
                </button>

                <button
                    type="button"
                    @click="openDrawer"
                    class="p-2 rounded-xl text-secondary hover:text-white hover:bg-dark-surface transition-colors focus:outline-none"
                    :aria-label="tt('Mở danh mục điều hướng')"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Slide-over Drawer with Teleport to body -->
        <Teleport to="body">
            <!-- Backdrop Overlay -->
            <Transition name="drawer-fade">
                <div
                    v-if="isDrawerOpen"
                    class="fixed inset-0 z-[1040] bg-black/70 backdrop-blur-sm"
                    @click="closeDrawer"
                />
            </Transition>

            <!-- Slide-over Drawer Panel -->
            <Transition name="drawer-slide">
                <aside
                    v-if="isDrawerOpen"
                    class="fixed top-0 right-0 bottom-0 z-[1050] w-[85%] max-w-[360px] bg-dark-bg border-l border-dark-border p-6 flex flex-col justify-between overflow-y-auto shadow-2xl"
                    role="dialog"
                    aria-modal="true"
                    :aria-label="tt('Menu di động')"
                >
                    <div class="space-y-6">
                        <!-- Drawer Top Header: Logo + Close X Button -->
                        <div class="flex items-center justify-between pb-4 border-b border-dark-border">
                            <Link href="/" @click="closeDrawer" class="inline-block">
                                <img
                                    src="/assets/images/header/logo.png"
                                    alt="HH Maker Logo"
                                    class="h-8 w-auto object-contain"
                                />
                            </Link>
                            <button
                                type="button"
                                @click="closeDrawer"
                                class="p-2 rounded-xl text-secondary hover:text-white hover:bg-dark-surface transition-colors"
                                :aria-label="tt('Đóng menu')"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Drawer Navigation Links (touch target >= 44px) -->
                        <nav class="space-y-1">
                            <template v-for="(menu, index) in menus" :key="index">
                                <!-- Tab Sản phẩm with Accordion -->
                                <div v-if="menu.type === 'products'" class="space-y-1">
                                    <button
                                        type="button"
                                        @click="isProductAccordionOpen = !isProductAccordionOpen"
                                        class="w-full min-h-[44px] px-3.5 py-3 rounded-xl flex items-center justify-between text-base font-medium transition-colors text-left cursor-pointer"
                                        :class="isMenuActive(menu) ? 'bg-primary/10 text-primary font-semibold' : 'text-secondary hover:text-white hover:bg-dark-surface'"
                                        :aria-expanded="isProductAccordionOpen"
                                    >
                                        <span>{{ menu.title }}</span>
                                        <svg
                                            class="w-5 h-5 transition-transform duration-200 shrink-0 text-[#B9C0D4]"
                                            :class="isProductAccordionOpen ? 'rotate-180 text-primary' : ''"
                                            viewBox="0 0 20 20"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M5 7.5L10 12.5L15 7.5"
                                                stroke="currentColor"
                                                stroke-width="1.67"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </button>

                                    <!-- Accordion Submenu Items -->
                                    <transition name="accordion">
                                        <div
                                            v-show="isProductAccordionOpen"
                                            class="pl-4 pr-1 py-1 space-y-1 border-l border-[#30374F] ml-3"
                                        >
                                            <Link
                                                v-for="(sub, subIdx) in menu.subMenu"
                                                :key="subIdx"
                                                :href="sub.url || sub.slug"
                                                @click="closeDrawer"
                                                class="min-h-[44px] px-3 py-2.5 rounded-lg flex items-center justify-between text-sm font-normal transition-colors"
                                                :class="sub.isAll
                                                    ? 'text-[#84ADFF] font-semibold hover:text-white hover:bg-dark-surface'
                                                    : 'text-[#B9C0D4] hover:text-white hover:bg-dark-surface'"
                                            >
                                                <span>{{ sub.title }}</span>
                                                <svg
                                                    v-if="sub.isAll"
                                                    class="w-4 h-4 shrink-0"
                                                    viewBox="0 0 16 16"
                                                    fill="none"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        d="M3.33334 8H12.6667M8 3.33334L12.6667 8L8 12.6667"
                                                        stroke="currentColor"
                                                        stroke-width="1.5"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>
                                            </Link>
                                        </div>
                                    </transition>
                                </div>

                                <!-- Other Tabs -->
                                <Link
                                    v-else
                                    :href="menu.slug || '#'"
                                    @click="closeDrawer"
                                    class="min-h-[44px] px-3.5 py-3 rounded-xl flex items-center justify-between text-base font-medium transition-colors"
                                    :class="isMenuActive(menu) ? 'bg-primary/10 text-primary font-semibold' : 'text-secondary hover:text-white hover:bg-dark-surface'"
                                >
                                    <span>{{ menu.title }}</span>
                                    <span v-if="isMenuActive(menu)" class="w-2 h-2 rounded-full bg-primary"></span>
                                </Link>
                            </template>
                        </nav>
                    </div>

                    <!-- Drawer Bottom Section: Language Capsule & Contact info -->
                    <div class="pt-6 border-t border-dark-border space-y-5">
                        <!-- Capsule Language Switcher in Mobile Drawer (Node 2380:1477) -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-muted font-medium">{{ tt('Ngôn ngữ') }}:</span>
                            <div class="w-[96px] h-[34px] border border-[#5D6B98] rounded-[8px] flex items-center justify-between p-1 shrink-0 select-none bg-dark-surface">
                                <template v-if="currentLocale === 'vi'">
                                    <button
                                        type="button"
                                        @click="switchLocale('vi')"
                                        class="bg-[#FAFAFA] text-[#111322] w-[36px] h-[24px] rounded-[4px] text-xs font-semibold flex items-center justify-center shadow-xs cursor-default"
                                        :title="tt('Tiếng Việt')"
                                    >
                                        VIE
                                    </button>
                                    <button
                                        type="button"
                                        @click="switchLocale('en')"
                                        class="text-white text-sm font-semibold pr-2 flex items-center justify-center hover:text-[#FAFAFA]/80 transition-colors"
                                        title="English"
                                    >
                                        ENG
                                    </button>
                                </template>
                                <template v-else>
                                    <button
                                        type="button"
                                        @click="switchLocale('vi')"
                                        class="text-white text-sm font-semibold pl-2 flex items-center justify-center hover:text-[#FAFAFA]/80 transition-colors"
                                        :title="tt('Tiếng Việt')"
                                    >
                                        VIE
                                    </button>
                                    <button
                                        type="button"
                                        @click="switchLocale('en')"
                                        class="bg-[#FAFAFA] text-[#111322] w-[42px] h-[24px] rounded-[4px] text-xs font-semibold flex items-center justify-center shadow-xs cursor-default"
                                        title="English"
                                    >
                                        ENG
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Quick Contact info -->
                        <div class="space-y-3 pt-1">
                            <Link
                                :href="route('contact')"
                                @click="closeDrawer"
                                class="w-full min-h-[44px] flex items-center justify-center rounded-[8px] text-white font-medium text-sm transition-all shadow-xs"
                                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
                            >
                                {{ tt('Liên hệ ngay') }}
                            </Link>
                            <div class="text-xs text-muted space-y-1.5">
                                <p>
                                    <strong class="text-secondary">Hotline:</strong>
                                    <a :href="'tel:' + ($page.props.global?.general_phone || '0968 000 000').replace(/\s+/g, '')" class="ml-1 text-secondary hover:text-primary">
                                        {{ $page.props.global?.general_phone || '0968 000 000' }}
                                    </a>
                                </p>
                                <p>
                                    <strong class="text-secondary">Email:</strong>
                                    <a :href="'mailto:' + ($page.props.global?.general_email || 'contact@hhmaker.vn')" class="ml-1 text-secondary hover:text-primary">
                                        {{ $page.props.global?.general_email || 'contact@hhmaker.vn' }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>
            </Transition>
        </Teleport>

        <!-- Dark Tech Search Modal Teleport to body -->
        <Teleport to="body">
            <SearchModal :is-open="isSearchOpen" @close="isSearchOpen = false" />
        </Teleport>
    </header>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import SearchModal from '@/Components/SearchModal.vue'

export default {
    name: 'Header',
    components: {
        Link,
        SearchModal,
    },
    props: {
        fullPath: {
            type: String,
            default: '',
        },
        fullRoute: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            menuSelected: null,
            isDrawerOpen: false,
            isSearchOpen: false,
            isProductAccordionOpen: false,
        }
    },
    computed: {
        currentLocale() {
            return this.$page.props.locale?.current || 'vi'
        },
        productCategories() {
            const fromProps = this.$page?.props?.product_categories || this.$page?.props?.data?.product_categories
            if (fromProps && fromProps.length > 0) {
                return fromProps
            }
            return [
                { id: 'trang-tri-khong-gian', title: this.tt('Trang trí không gian'), slug: 'trang-tri-khong-gian' },
                { id: 'moc-khoa-phu-kien', title: this.tt('Móc khoá & Phụ kiện'), slug: 'moc-khoa-phu-kien' },
                { id: 'trang-tri-do-dung', title: this.tt('Trang trí & Đồ dùng'), slug: 'trang-tri-do-dung' },
                { id: 'gia-cong-theo-yeu-cau', title: this.tt('Gia công theo yêu cầu'), slug: 'gia-cong-theo-yeu-cau' },
            ]
        },
        menus() {
            const categories = this.productCategories
            const productSubMenu = [
                {
                    title: this.tt('Xem tất cả sản phẩm'),
                    slug: this.route('products.index'),
                    url: this.route('products.index'),
                    isAll: true,
                },
                ...categories.map(cat => ({
                    id: cat.id,
                    title: cat.title,
                    slug: cat.url || (this.route('products.index') + '?category=' + cat.slug),
                    url: cat.url || (this.route('products.index') + '?category=' + cat.slug),
                    categorySlug: cat.slug,
                    isAll: false,
                })),
            ]

            return [
                {
                    title: this.tt('Trang chủ'),
                    slug: this.route('home'),
                    type: 'home',
                    subMenu: [],
                },
                {
                    title: this.tt('Sản phẩm'),
                    slug: this.route('products.index'),
                    type: 'products',
                    subMenu: productSubMenu,
                },
                {
                    title: this.tt('Dịch vụ'),
                    slug: this.route('services.index'),
                    type: 'services',
                    subMenu: [],
                },
                {
                    title: this.tt('Giới thiệu'),
                    slug: this.route('histories.index'),
                    type: 'histories',
                    subMenu: [],
                },
                {
                    title: this.tt('Quy trình'),
                    slug: this.route('process.index'),
                    type: 'quy-trinh',
                    subMenu: [],
                },
            ]
        },
    },
    methods: {
        isMenuActive(menu) {
            if (!menu || !menu.slug) return false

            const path = typeof window !== 'undefined' ? window.location.pathname : this.$page?.url || ''
            const cleanPath = path.split('?')[0].split('#')[0]
            const normalizedPath = cleanPath.replace(/\/+$/, '') || '/'

            const rawInertia = (this.$page?.url || '').split('?')[0].split('#')[0]
            const inertiaUrl = rawInertia.replace(/\/+$/, '') || '/'

            const component = this.$page?.component || ''

            // Kiểm tra trạng thái Trang chủ chính xác 100%
            const isHome =
                normalizedPath === '/' ||
                normalizedPath === '/vi' ||
                normalizedPath === '/en' ||
                inertiaUrl === '/' ||
                inertiaUrl === '/vi' ||
                inertiaUrl === '/en' ||
                component === 'Home' ||
                component === 'Home/Index'

            // Nếu đang ở Trang chủ: CHỈ duy nhất tab 'home' được active
            if (isHome) {
                return menu.type === 'home'
            }

            // Nếu không ở Trang chủ: tab 'home' không bao giờ active
            if (menu.type === 'home') {
                return false
            }

            // Xử lý các tab chuyên biệt khi không ở trang chủ
            if (menu.type === 'products') {
                return (
                    component.toLowerCase().includes('product') ||
                    normalizedPath === '/san-pham' ||
                    normalizedPath.startsWith('/san-pham/') ||
                    normalizedPath === '/products' ||
                    normalizedPath.startsWith('/products/') ||
                    normalizedPath === '/en/products' ||
                    normalizedPath.startsWith('/en/products/') ||
                    inertiaUrl === '/san-pham' ||
                    inertiaUrl.startsWith('/san-pham/') ||
                    inertiaUrl === '/products' ||
                    inertiaUrl.startsWith('/products/') ||
                    inertiaUrl === '/en/products' ||
                    inertiaUrl.startsWith('/en/products/')
                )
            }

            if (menu.type === 'services') {
                return (
                    component.toLowerCase().includes('service') ||
                    normalizedPath === '/dich-vu' ||
                    normalizedPath.startsWith('/dich-vu/') ||
                    normalizedPath === '/services' ||
                    normalizedPath.startsWith('/services/') ||
                    normalizedPath === '/en/services' ||
                    normalizedPath.startsWith('/en/services/') ||
                    inertiaUrl === '/dich-vu' ||
                    inertiaUrl.startsWith('/dich-vu/') ||
                    inertiaUrl === '/services' ||
                    inertiaUrl.startsWith('/services/') ||
                    inertiaUrl === '/en/services' ||
                    inertiaUrl.startsWith('/en/services/')
                )
            }

            if (menu.type === 'histories') {
                return (
                    component.toLowerCase().includes('histor') ||
                    component.toLowerCase().includes('about') ||
                    normalizedPath === '/gioi-thieu' ||
                    normalizedPath.startsWith('/gioi-thieu/') ||
                    normalizedPath === '/ve-chung-toi' ||
                    normalizedPath.startsWith('/ve-chung-toi/') ||
                    normalizedPath === '/cot-moc-phat-trien' ||
                    normalizedPath.startsWith('/cot-moc-phat-trien/') ||
                    normalizedPath === '/about-us' ||
                    normalizedPath.startsWith('/about') ||
                    normalizedPath === '/en/about-us' ||
                    normalizedPath.startsWith('/en/about') ||
                    inertiaUrl === '/gioi-thieu' ||
                    inertiaUrl.startsWith('/gioi-thieu/') ||
                    inertiaUrl === '/ve-chung-toi' ||
                    inertiaUrl.startsWith('/ve-chung-toi/') ||
                    inertiaUrl === '/cot-moc-phat-trien' ||
                    inertiaUrl.startsWith('/cot-moc-phat-trien/') ||
                    inertiaUrl === '/about-us' ||
                    inertiaUrl.startsWith('/about') ||
                    inertiaUrl === '/en/about-us' ||
                    inertiaUrl.startsWith('/en/about')
                )
            }

            if (menu.type === 'quy-trinh') {
                return (
                    component.toLowerCase().includes('process') ||
                    normalizedPath === '/quy-trinh' ||
                    normalizedPath.startsWith('/quy-trinh/') ||
                    normalizedPath === '/process' ||
                    normalizedPath.startsWith('/process/') ||
                    normalizedPath === '/en/process' ||
                    normalizedPath.startsWith('/en/process/') ||
                    inertiaUrl === '/quy-trinh' ||
                    inertiaUrl.startsWith('/quy-trinh/') ||
                    inertiaUrl === '/process' ||
                    inertiaUrl.startsWith('/process/') ||
                    inertiaUrl === '/en/process' ||
                    inertiaUrl.startsWith('/en/process/')
                )
            }

            // Fallback so khớp đường dẫn tuyệt đối/tương đối (bỏ qua trang chủ "/")
            let menuPath = menu.slug
            try {
                if (menuPath.startsWith('http://') || menuPath.startsWith('https://')) {
                    menuPath = new URL(menuPath).pathname
                }
            } catch (e) {
                // fallback to original slug
            }
            const cleanMenuPath = menuPath.split('?')[0].split('#')[0]
            const normalizedMenuPath = cleanMenuPath.replace(/\/+$/, '') || '/'

            if (normalizedMenuPath === '/' || normalizedMenuPath === '') {
                return false
            }

            return (
                normalizedPath === normalizedMenuPath ||
                normalizedPath.startsWith(normalizedMenuPath + '/') ||
                inertiaUrl === normalizedMenuPath ||
                inertiaUrl.startsWith(normalizedMenuPath + '/')
            )
        },
        setMenuSelected(item) {
            this.menuSelected = item
        },
        openDrawer() {
            this.isDrawerOpen = true
            if (typeof document !== 'undefined') {
                document.body.classList.add('overflow-hidden')
            }
        },
        closeDrawer() {
            this.isDrawerOpen = false
            if (typeof document !== 'undefined') {
                document.body.classList.remove('overflow-hidden')
            }
        },
        switchLocale(lang) {
            if (this.currentLocale === lang) return

            this.closeDrawer()

            // 1. Try resolving using current route name and Ziggy
            const currentRouteName = this.$page?.props?.route?.name || ''
            if (currentRouteName) {
                const baseRouteName = currentRouteName.replace(/^(vi|en)\./, '')
                const targetRouteName = `${lang}.${baseRouteName}`
                if (this.routes && this.routes()[targetRouteName]) {
                    const routeParams = this.$page?.props?.route?.params || {}
                    const routeQuery = this.$page?.props?.route?.query || {}
                    const allParams = { ...routeParams, ...routeQuery }
                    try {
                        const targetUrl = this.route(targetRouteName, allParams)
                        if (targetUrl && targetUrl !== '#') {
                            if (this.$inertia) {
                                this.$inertia.visit(targetUrl)
                            } else if (typeof window !== 'undefined') {
                                window.location.href = targetUrl
                            }
                            return
                        }
                    } catch (e) {
                        // fallback to path replacement
                    }
                }
            }

            // 2. Intelligent path translation mapping
            let currentUrl = typeof window !== 'undefined'
                ? window.location.pathname + window.location.search
                : (this.$page?.url || '/')
            const [rawPath, rawQuery] = currentUrl.split('?')
            const cleanPath = (rawPath || '/').split('#')[0]
            const queryString = rawQuery ? `?${rawQuery}` : ''

            const viToEnPrefixes = [
                { from: '/san-pham', to: '/products' },
                { from: '/dich-vu', to: '/services' },
                { from: '/gioi-thieu', to: '/about-us' },
                { from: '/ve-chung-toi', to: '/about-us' },
                { from: '/cot-moc-phat-trien', to: '/about-us' },
                { from: '/quy-trinh', to: '/process' },
                { from: '/lien-he', to: '/contact' },
                { from: '/tim-kiem', to: '/search' },
                { from: '/tin-tuc', to: '/posts' },
                { from: '/chinh-sach', to: '/policies' },
            ]

            const enToViPrefixes = [
                { from: '/products', to: '/san-pham' },
                { from: '/services', to: '/dich-vu' },
                { from: '/about-us', to: '/gioi-thieu' },
                { from: '/about', to: '/gioi-thieu' },
                { from: '/histories', to: '/gioi-thieu' },
                { from: '/process', to: '/quy-trinh' },
                { from: '/contact', to: '/lien-he' },
                { from: '/search', to: '/tim-kiem' },
                { from: '/posts', to: '/tin-tuc' },
                { from: '/policies', to: '/chinh-sach' },
            ]

            let newPath = '/'
            if (lang === 'en') {
                if (cleanPath === '/' || cleanPath === '/vi') {
                    newPath = '/en'
                } else {
                    const stripped = cleanPath.replace(/^\/vi/, '') || '/'
                    let translated = stripped
                    for (const p of viToEnPrefixes) {
                        if (stripped === p.from) {
                            translated = p.to
                            break
                        } else if (stripped.startsWith(p.from + '/')) {
                            translated = p.to + stripped.slice(p.from.length)
                            break
                        }
                    }
                    newPath = '/en' + (translated.startsWith('/') ? translated : '/' + translated)
                }
            } else {
                // Switching to 'vi'
                const stripped = cleanPath.replace(/^\/en/, '') || '/'
                let translated = stripped
                for (const p of enToViPrefixes) {
                    if (stripped === p.from) {
                        translated = p.to
                        break
                    } else if (stripped.startsWith(p.from + '/')) {
                        translated = p.to + stripped.slice(p.from.length)
                        break
                    }
                }
                newPath = translated.startsWith('/') ? translated : '/' + translated
            }

            if (queryString) {
                newPath += queryString
            }

            if (this.$inertia) {
                this.$inertia.visit(newPath)
            } else if (typeof window !== 'undefined') {
                window.location.href = newPath
            }
        },

        handleKeydown(e) {
            // Phím tắt Ctrl+K hoặc Cmd+K mở modal tìm kiếm
            if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
                e.preventDefault()
                this.isSearchOpen = !this.isSearchOpen
                return
            }
            if (e.key === 'Escape') {
                if (this.isSearchOpen) {
                    this.isSearchOpen = false
                }
                if (this.isDrawerOpen) {
                    this.closeDrawer()
                }
            }
        },
    },
    mounted() {
        if (typeof window !== 'undefined') {
            window.addEventListener('keydown', this.handleKeydown)
        }
    },
    beforeUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('keydown', this.handleKeydown)
            document.body.classList.remove('overflow-hidden')
        }
    },
}
</script>

<style lang="scss">
body {
    --header-height-sm: 64px;
    --header-height-md: 64px;
    --header-height-lg: 111px;
    --header-height-xl: 111px;
}
</style>

<style lang="scss" scoped>
/* Drawer Fade Backdrop */
.drawer-fade-enter-active,
.drawer-fade-leave-active {
    transition: opacity 0.3s ease;
}
.drawer-fade-enter-from,
.drawer-fade-leave-to {
    opacity: 0;
}

/* Drawer Slide Panel */
.drawer-slide-enter-active,
.drawer-slide-leave-active {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-slide-enter-from,
.drawer-slide-leave-to {
    transform: translateX(100%);
}

/* Dropdown Fade with Hover Bridge */
.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

/* Accordion Transition for Mobile Drawer */
.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    max-height: 500px;
    overflow: hidden;
}
.accordion-enter-from,
.accordion-leave-to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-6px);
}
</style>
