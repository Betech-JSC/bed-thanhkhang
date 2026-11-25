<template>
    <!-- Desktop Top Info Bar (Figma 595:15540 — 46px, #00284B) -->
    <div v-if="companyName || contactEmail || contactPhone" class="hidden lg:block w-full h-[46px] bg-dark text-ink-light antialiased">
        <div class="app-container h-full flex items-center justify-between gap-6">
            <p v-if="companyName" class="text-[14px] font-normal leading-[1.4] text-ink-light truncate">
                {{ companyName }}
            </p>
            <div class="flex items-center gap-6 shrink-0 text-[14px] font-normal leading-[1.4] text-ink-light">
                <a
                    v-if="contactEmail"
                    :href="'mailto:' + contactEmail"
                    class="inline-flex items-center gap-2 text-ink-light hover:text-secondary transition-colors focus:outline-none focus-visible:underline"
                >
                    <img
                        src="/assets/svg/icons/header-mail.svg"
                        alt=""
                        aria-hidden="true"
                        width="18"
                        height="18"
                        class="w-[18px] h-[18px] shrink-0"
                    />
                    <span>{{ tt('Email') }}: {{ contactEmail }}</span>
                </a>
                <a
                    v-if="contactPhone"
                    :href="'tel:' + cleanPhone"
                    class="inline-flex items-center gap-2 text-ink-light hover:text-secondary transition-colors focus:outline-none focus-visible:underline"
                >
                    <img
                        src="/assets/svg/icons/header-phone.svg"
                        alt=""
                        aria-hidden="true"
                        width="18"
                        height="18"
                        class="w-[18px] h-[18px] shrink-0"
                    />
                    <span>{{ tt('Hotline') }}: {{ contactPhone }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Sticky Header Bar (Figma 595:15540 — 72px Desktop / 64px Mobile, #FAFAFA) -->
    <header
        ref="headerRoot"
        class="sticky top-0 z-[1000] w-full bg-surface border-b antialiased transition-all duration-200"
        :class="isScrolled ? 'border-gray-200 shadow-xs bg-surface/95 backdrop-blur-md' : 'border-gray-200/60'"
        @mouseleave="closeDesktopDropdown"
    >
        <div class="app-container h-[64px] lg:h-[72px] flex items-center justify-between gap-4 lg:gap-6">
            <!-- Brand Logo (Desktop 120x40, CMS general_logo with local fallback) -->
            <Link
                :href="safeRoute('home', undefined, '/')"
                class="inline-flex items-center shrink-0 min-w-0 max-w-[140px] rounded-cta focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                :aria-label="companyName || tt('Trang chủ')"
            >
                <img
                    v-if="resolvedLogoUrl"
                    :src="resolvedLogoUrl"
                    :alt="companyName || 'Thanh Khang Tecsat'"
                    width="120"
                    height="40"
                    class="h-[34px] sm:h-[38px] lg:h-[40px] w-auto max-w-[140px] object-contain"
                    @error="onLogoError"
                />
                <span
                    v-else-if="companyName"
                    class="font-bold text-[15px] lg:text-[17px] tracking-tight text-dark uppercase truncate max-w-[140px]"
                >
                    {{ companyName }}
                </span>
            </Link>

            <!-- Desktop Navigation (5 items per Figma 595:15540) -->
            <nav
                class="hidden lg:flex items-stretch self-stretch gap-5 xl:gap-8"
                :aria-label="tt('Điều hướng chính')"
            >
                <template v-for="(menu, index) in menus" :key="index">
                    <div
                        class="relative flex items-center self-stretch"
                        @mouseenter="menu.hasDropdown ? openDesktopDropdown(menu.type) : closeDesktopDropdown()"
                    >
                        <!-- Figma 979:30014 — 72px item, inactive Gray modern/500, active Main blue/600 + 3px underline -->
                        <div
                            class="inline-flex items-center gap-1 h-full border-b-[3px] transition-colors"
                            :class="isMenuActive(menu) ? 'border-primary-600' : 'border-transparent'"
                        >
                            <Link
                                :href="menu.href"
                                class="py-2 text-[14px] font-bold uppercase leading-[1.6] tracking-normal whitespace-nowrap transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-cta"
                                :class="
                                    isMenuActive(menu)
                                        ? 'text-primary-600'
                                        : 'text-ink-subtle hover:text-primary'
                                "
                                @click="closeDesktopDropdown"
                            >
                                {{ menu.title }}
                            </Link>
                            <button
                                v-if="menu.hasDropdown"
                                type="button"
                                class="inline-flex items-center justify-center w-6 h-6 rounded-cta hover:bg-surface-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors cursor-pointer shrink-0"
                                :aria-label="tt('Mở danh mục sản phẩm')"
                                :aria-expanded="activeDropdown === menu.type"
                                aria-haspopup="true"
                                @click.stop="toggleDesktopDropdown(menu.type)"
                            >
                                <img
                                    src="/assets/svg/icons/header-chevron-down.svg"
                                    alt=""
                                    aria-hidden="true"
                                    width="24"
                                    height="24"
                                    class="w-6 h-6 transition-transform duration-200"
                                    :class="activeDropdown === menu.type ? 'rotate-180' : ''"
                                />
                            </button>
                        </div>

                        <!-- Products Dropdown Menu (Light High-Trust Surface, CMS product_categories) -->
                        <transition name="dropdown-fade">
                            <div
                                v-if="menu.hasDropdown"
                                v-show="activeDropdown === menu.type"
                                class="absolute top-full left-0 pt-2 z-[1010]"
                            >
                                <div class="min-w-[260px] w-[280px] bg-white border border-gray-200 rounded-lg shadow-lg p-2 flex flex-col gap-0.5">
                                    <div
                                        v-for="cat in productCategories"
                                        :key="cat.id || cat.slug"
                                        class="relative group/sub"
                                    >
                                        <Link
                                            :href="cat.url"
                                            class="px-3 py-2.5 rounded-[6px] text-[14px] font-medium text-dark hover:text-primary hover:bg-surface-100 flex items-center justify-between gap-2 transition-colors"
                                            @click="closeDesktopDropdown"
                                        >
                                            <span class="truncate">{{ cat.title }}</span>
                                            <img
                                                v-if="cat.children && cat.children.length > 0"
                                                src="/assets/svg/icons/header-chevron-down.svg"
                                                alt=""
                                                aria-hidden="true"
                                                width="16"
                                                height="16"
                                                class="w-4 h-4 -rotate-90 shrink-0 opacity-70"
                                            />
                                        </Link>

                                        <!-- Level 2 Flyout Subcategories -->
                                        <div
                                            v-if="cat.children && cat.children.length > 0"
                                            class="absolute left-full -top-1 pl-2 hidden group-hover/sub:block z-[1020]"
                                        >
                                            <div class="min-w-[240px] w-[260px] bg-white border border-gray-200 rounded-lg shadow-lg p-2 flex flex-col gap-0.5">
                                                <Link
                                                    v-for="child in cat.children"
                                                    :key="child.id || child.slug"
                                                    :href="child.url"
                                                    class="px-3 py-2 rounded-[6px] text-[14px] font-normal text-ink-body hover:text-primary hover:bg-surface-100 truncate transition-colors"
                                                    @click="closeDesktopDropdown"
                                                >
                                                    {{ child.title }}
                                                </Link>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        :class="productCategories.length > 0 ? 'pt-1.5 mt-1 border-t border-gray-200' : ''"
                                    >
                                        <Link
                                            :href="safeRoute('products.index', undefined, '/san-pham')"
                                            class="px-3 py-2 rounded-[6px] text-[13px] font-semibold text-primary hover:bg-surface-100 flex items-center justify-between transition-colors"
                                            @click="closeDesktopDropdown"
                                        >
                                            <span>{{ tt('Xem tất cả sản phẩm') }}</span>
                                            <span aria-hidden="true">&rarr;</span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </template>
            </nav>

            <!-- Desktop Right Actions: Search + 2 CTAs (Figma 595:15540) -->
            <div class="hidden lg:flex items-center gap-4 xl:gap-8 shrink-0">
                <button
                    type="button"
                    @click="isSearchOpen = true"
                    class="w-9 h-9 inline-flex items-center justify-center rounded-cta hover:bg-surface-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors cursor-pointer shrink-0"
                    :title="tt('Tìm kiếm (Ctrl+K)')"
                    :aria-label="tt('Tìm kiếm')"
                >
                    <img
                        src="/assets/svg/icons/header-search.svg"
                        alt=""
                        aria-hidden="true"
                        width="20"
                        height="20"
                        class="w-5 h-5 object-contain"
                    />
                </button>

                <div class="flex items-center gap-3 shrink-0">
                    <Link
                        :href="safeRoute('contact', undefined, '/lien-he')"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-cta bg-primary hover:bg-primary-hover active:bg-primary-active text-surface text-[14px] font-medium leading-[1.4] px-4 xl:px-5 py-2 shadow-xs transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                    >
                        {{ tt('Nhận báo giá') }}
                    </Link>
                    <a
                        v-if="contactPhone"
                        :href="'tel:' + cleanPhone"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-cta bg-secondary hover:bg-secondary-hover text-primary-active text-[14px] font-medium leading-[1.4] px-4 xl:px-5 py-2 shadow-xs transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                    >
                        {{ tt('Gọi hotline') }}
                    </a>
                </div>
            </div>

            <!-- Mobile Controls (< lg, 375px optimized with >= 44px touch targets) -->
            <div class="flex items-center gap-1 lg:hidden shrink-0">
                <button
                    type="button"
                    @click="isSearchOpen = true"
                    class="w-11 h-11 inline-flex items-center justify-center rounded-cta hover:bg-surface-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors cursor-pointer"
                    :title="tt('Tìm kiếm')"
                    :aria-label="tt('Tìm kiếm')"
                >
                    <img
                        src="/assets/svg/icons/header-search.svg"
                        alt=""
                        aria-hidden="true"
                        width="20"
                        height="20"
                        class="w-5 h-5 object-contain"
                    />
                </button>

                <a
                    v-if="contactPhone"
                    :href="'tel:' + cleanPhone"
                    class="w-11 h-11 inline-flex items-center justify-center rounded-cta bg-secondary hover:bg-secondary-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors"
                    :title="tt('Gọi hotline') + ': ' + contactPhone"
                    :aria-label="tt('Gọi hotline') + ' ' + contactPhone"
                >
                    <img
                        src="/assets/svg/icons/footer-phone.svg"
                        alt=""
                        aria-hidden="true"
                        width="20"
                        height="20"
                        class="w-5 h-5 object-contain"
                    />
                </a>

                <button
                    type="button"
                    data-testid="mobile-menu-toggle"
                    @click="openDrawer"
                    class="w-11 h-11 inline-flex items-center justify-center rounded-cta text-dark hover:bg-surface-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors cursor-pointer"
                    :aria-label="tt('Mở danh mục điều hướng')"
                    :aria-expanded="isDrawerOpen"
                    aria-controls="mobile-drawer"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Slide-in Drawer -->
    <Teleport to="body">
        <Transition name="drawer-fade">
            <div
                v-if="isDrawerOpen"
                class="fixed inset-0 z-[1040] bg-dark/50 backdrop-blur-[2px]"
                aria-hidden="true"
                @click="closeDrawer"
            />
        </Transition>

        <Transition name="drawer-slide">
            <aside
                v-if="isDrawerOpen"
                id="mobile-drawer"
                data-testid="mobile-drawer"
                class="fixed top-0 right-0 bottom-0 z-[1050] w-[85vw] max-w-[340px] bg-white border-l border-gray-200 flex flex-col justify-between overflow-y-auto shadow-2xl"
                role="dialog"
                aria-modal="true"
                :aria-label="tt('Menu di động')"
            >
                <div class="p-5 space-y-5">
                    <!-- Drawer Header: Logo + Close Button -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <Link
                            :href="safeRoute('home', undefined, '/')"
                            @click="closeDrawer"
                            class="inline-flex items-center shrink-0 min-w-0 max-w-[200px]"
                        >
                            <img
                                v-if="resolvedLogoUrl"
                                :src="resolvedLogoUrl"
                                :alt="companyName || 'Thanh Khang Tecsat'"
                                width="108"
                                height="36"
                                class="h-9 w-auto max-w-[140px] object-contain"
                                @error="onLogoError"
                            />
                            <span
                                v-else-if="companyName"
                                class="font-bold text-[14px] tracking-tight text-dark uppercase truncate"
                            >
                                {{ companyName }}
                            </span>
                        </Link>
                        <button
                            type="button"
                            data-testid="mobile-menu-close"
                            @click="closeDrawer"
                            class="w-11 h-11 inline-flex items-center justify-center rounded-cta text-dark hover:bg-surface-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors cursor-pointer"
                            :aria-label="tt('Đóng menu')"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Drawer Navigation Links (Touch target >= 44px) -->
                    <nav class="space-y-1" :aria-label="tt('Điều hướng di động')">
                        <template v-for="(menu, index) in menus" :key="index">
                            <div v-if="menu.hasDropdown" class="space-y-1">
                                <div
                                    class="flex items-center justify-between min-h-[44px] rounded-[6px] transition-colors"
                                    :class="
                                        isMenuActive(menu)
                                            ? 'bg-secondary/40 text-primary'
                                            : 'text-dark hover:bg-surface-100'
                                    "
                                >
                                    <Link
                                        :href="menu.href"
                                        @click="closeDrawer"
                                        class="flex-1 px-3 py-2.5 text-[14px] font-bold uppercase leading-[1.4]"
                                    >
                                        {{ menu.title }}
                                    </Link>
                                    <button
                                        type="button"
                                        @click="isProductAccordionOpen = !isProductAccordionOpen"
                                        class="w-11 h-11 inline-flex items-center justify-center rounded-[6px] hover:bg-surface-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary cursor-pointer shrink-0"
                                        :aria-label="tt('Mở rộng danh mục sản phẩm')"
                                        :aria-expanded="isProductAccordionOpen"
                                    >
                                        <img
                                            src="/assets/svg/icons/header-chevron-down.svg"
                                            alt=""
                                            aria-hidden="true"
                                            width="20"
                                            height="20"
                                            class="w-5 h-5 transition-transform duration-200"
                                            :class="isProductAccordionOpen ? 'rotate-180' : ''"
                                        />
                                    </button>
                                </div>

                                <transition name="accordion">
                                    <div
                                        v-show="isProductAccordionOpen"
                                        class="pl-3 ml-3 border-l border-gray-200 space-y-1 py-1"
                                    >
                                        <div
                                            v-for="cat in productCategories"
                                            :key="cat.id || cat.slug"
                                            class="space-y-1"
                                        >
                                            <div class="flex items-center justify-between min-h-[44px] rounded-[6px] hover:bg-surface-100">
                                                <Link
                                                    :href="cat.url"
                                                    @click="closeDrawer"
                                                    class="flex-1 px-3 py-2 text-[14px] font-medium text-ink-body hover:text-primary truncate"
                                                >
                                                    {{ cat.title }}
                                                </Link>
                                                <button
                                                    v-if="cat.children && cat.children.length > 0"
                                                    type="button"
                                                    @click.stop="toggleMobileCategory(cat.id || cat.slug)"
                                                    class="w-10 h-10 inline-flex items-center justify-center rounded-[6px] hover:bg-surface-100 shrink-0 cursor-pointer"
                                                    :aria-label="tt('Mở rộng danh mục con')"
                                                    :aria-expanded="isMobileCategoryExpanded(cat.id || cat.slug)"
                                                >
                                                    <img
                                                        src="/assets/svg/icons/header-chevron-down.svg"
                                                        alt=""
                                                        aria-hidden="true"
                                                        width="16"
                                                        height="16"
                                                        class="w-4 h-4 transition-transform duration-200"
                                                        :class="isMobileCategoryExpanded(cat.id || cat.slug) ? 'rotate-180' : ''"
                                                    />
                                                </button>
                                            </div>

                                            <transition name="accordion">
                                                <div
                                                    v-if="cat.children && cat.children.length > 0"
                                                    v-show="isMobileCategoryExpanded(cat.id || cat.slug)"
                                                    class="pl-3 ml-3 border-l border-gray-200 space-y-0.5 py-0.5"
                                                >
                                                    <Link
                                                        v-for="child in cat.children"
                                                        :key="child.id || child.slug"
                                                        :href="child.url"
                                                        @click="closeDrawer"
                                                        class="min-h-[40px] px-3 py-2 rounded-[6px] flex items-center text-[13px] text-ink-subtle hover:text-primary hover:bg-surface-100 truncate"
                                                    >
                                                        {{ child.title }}
                                                    </Link>
                                                </div>
                                            </transition>
                                        </div>

                                        <Link
                                            :href="safeRoute('products.index', undefined, '/san-pham')"
                                            @click="closeDrawer"
                                            class="min-h-[44px] px-3 py-2 rounded-[6px] flex items-center justify-between text-[13px] font-semibold text-primary hover:bg-surface-100"
                                        >
                                            <span>{{ tt('Xem tất cả sản phẩm') }}</span>
                                            <span aria-hidden="true">&rarr;</span>
                                        </Link>
                                    </div>
                                </transition>
                            </div>

                            <Link
                                v-else
                                :href="menu.href"
                                @click="closeDrawer"
                                class="min-h-[44px] px-3 py-2.5 rounded-[6px] flex items-center text-[14px] font-bold uppercase leading-[1.4] transition-colors"
                                :class="
                                    isMenuActive(menu)
                                        ? 'bg-secondary/40 text-primary'
                                        : 'text-dark hover:text-primary hover:bg-surface-100'
                                "
                            >
                                {{ menu.title }}
                            </Link>
                        </template>
                    </nav>
                </div>

                <!-- Drawer Bottom: CTAs + Contact Info from CMS -->
                <div class="p-5 pt-4 border-t border-gray-200 bg-surface-50 space-y-4">
                    <div class="flex flex-col gap-2.5">
                        <Link
                            :href="safeRoute('contact', undefined, '/lien-he')"
                            @click="closeDrawer"
                            class="w-full min-h-[44px] inline-flex items-center justify-center rounded-cta bg-primary hover:bg-primary-hover active:bg-primary-active text-surface text-[14px] font-medium shadow-xs transition-colors"
                        >
                            {{ tt('Nhận báo giá') }}
                        </Link>
                        <a
                            v-if="contactPhone"
                            :href="'tel:' + cleanPhone"
                            @click="closeDrawer"
                            class="w-full min-h-[44px] inline-flex items-center justify-center rounded-cta bg-secondary hover:bg-secondary-hover text-primary-active text-[14px] font-medium shadow-xs transition-colors"
                        >
                            {{ tt('Gọi hotline') }}
                        </a>
                    </div>

                    <div class="space-y-2 pt-1 text-[13px] text-ink-body">
                        <a
                            v-if="contactPhone"
                            :href="'tel:' + cleanPhone"
                            class="flex items-center gap-2.5 text-ink-body hover:text-primary transition-colors"
                        >
                            <img
                                src="/assets/svg/icons/footer-phone.svg"
                                alt=""
                                aria-hidden="true"
                                width="18"
                                height="18"
                                class="w-[18px] h-[18px] shrink-0"
                            />
                            <span>{{ contactPhone }}</span>
                        </a>
                        <a
                            v-if="contactEmail"
                            :href="'mailto:' + contactEmail"
                            class="flex items-center gap-2.5 text-ink-body hover:text-primary transition-colors break-all"
                        >
                            <img
                                src="/assets/svg/icons/footer-mail.svg"
                                alt=""
                                aria-hidden="true"
                                width="18"
                                height="18"
                                class="w-[18px] h-[18px] shrink-0"
                            />
                            <span>{{ contactEmail }}</span>
                        </a>
                    </div>
                </div>
            </aside>
        </Transition>
    </Teleport>

    <!-- Search Modal -->
    <Teleport to="body">
        <SearchModal :is-open="isSearchOpen" @close="isSearchOpen = false" />
    </Teleport>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import SearchModal from '@/Components/SearchModal.vue'

const DEFAULT_LOGO_URL = '/assets/images/logo/logo-thanhkhang.png'

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
            activeDropdown: null,
            isScrolled: false,
            isDrawerOpen: false,
            isSearchOpen: false,
            isProductAccordionOpen: false,
            expandedMobileCategoryIds: [],
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
        companyName() {
            return (
                this.siteConfig.general_business_name ||
                this.siteConfig.company_name ||
                this.siteConfig.general_site_name ||
                ''
            )
        },
        contactEmail() {
            return this.siteConfig.general_email || this.siteConfig.email || ''
        },
        contactPhone() {
            return this.siteConfig.hotline || this.siteConfig.general_phone || this.siteConfig.phone || ''
        },
        cleanPhone() {
            return String(this.contactPhone || '').replace(/[^\d+]/g, '')
        },
        cmsLogoUrl() {
            const raw = this.siteConfig.general_logo || this.siteConfig.logo || ''
            return typeof raw === 'string' ? raw.trim() : ''
        },
        resolvedLogoUrl() {
            if (this.logoFallbackStage >= 2) return ''
            if (this.logoFallbackStage === 0 && this.cmsLogoUrl) {
                return this.cmsLogoUrl
            }
            return DEFAULT_LOGO_URL
        },
        productCategories() {
            const rawCategories =
                this.$page?.props?.product_categories || this.$page?.props?.data?.product_categories || []

            if (!Array.isArray(rawCategories) || rawCategories.length === 0) {
                return []
            }

            const baseProductsUrl = this.safeRoute(
                'products.index',
                undefined,
                this.currentLocale === 'en' ? '/en/products' : '/san-pham'
            )
            const normalizeCategoryUrl = (cat, parentSlug = '') => {
                if (parentSlug && cat.slug) {
                    return `${baseProductsUrl}?category=${encodeURIComponent(parentSlug)}&subcategory=${encodeURIComponent(cat.slug)}`
                }
                return cat.url || (cat.slug ? `${baseProductsUrl}?category=${encodeURIComponent(cat.slug)}` : baseProductsUrl)
            }

            const hasFlatChildren = rawCategories.some((c) => c && Number(c.parent_id) > 0)

            if (!hasFlatChildren) {
                return rawCategories
                    .filter((cat) => cat && (cat.title || cat.name))
                    .map((cat) => ({
                        id: cat.id,
                        title: cat.title || cat.name,
                        slug: cat.slug,
                        url: normalizeCategoryUrl(cat),
                        children: (cat.children || cat.subcategories || cat.nodes || [])
                            .filter((child) => child && (child.title || child.name))
                            .map((child) => ({
                                id: child.id,
                                title: child.title || child.name,
                                slug: child.slug,
                                url: normalizeCategoryUrl(child, cat.slug),
                            })),
                    }))
            }

            const parentMap = new Map()
            const childItems = []

            rawCategories.forEach((item) => {
                if (!item || !(item.title || item.name)) return
                const parentId = Number(item.parent_id || 0)
                if (parentId === 0) {
                    parentMap.set(item.id, {
                        id: item.id,
                        title: item.title || item.name,
                        slug: item.slug,
                        url: normalizeCategoryUrl(item),
                        children: [...(item.children || item.subcategories || item.nodes || [])],
                    })
                } else {
                    childItems.push(item)
                }
            })

            childItems.forEach((child) => {
                const parentId = Number(child.parent_id)
                if (parentMap.has(parentId)) {
                    const parent = parentMap.get(parentId)
                    if (!parent.children.some((c) => c.id === child.id)) {
                        parent.children.push(child)
                    }
                }
            })

            return Array.from(parentMap.values()).map((cat) => ({
                ...cat,
                children: (cat.children || [])
                    .filter((child) => child && (child.title || child.name))
                    .map((child) => ({
                        id: child.id,
                        title: child.title || child.name,
                        slug: child.slug,
                        url: normalizeCategoryUrl(child, cat.slug),
                    })),
            }))
        },
        menus() {
            const isEn = this.currentLocale === 'en'
            const processHref = this.safeRoute('process.index', undefined, isEn ? '/en/process' : '/quy-trinh')

            return [
                {
                    title: this.tt('VỀ CHÚNG TÔI'),
                    href: this.safeRoute('histories.index', undefined, isEn ? '/en/about-us' : '/gioi-thieu'),
                    type: 'about',
                    hasDropdown: false,
                },
                {
                    title: this.tt('SẢN PHẨM'),
                    href: this.safeRoute('products.index', undefined, isEn ? '/en/products' : '/san-pham'),
                    type: 'products',
                    hasDropdown: true,
                },
                {
                    title: String(this.tt('Quy trình') || (isEn ? 'PROCESS' : 'QUY TRÌNH')).toUpperCase(),
                    href: processHref,
                    type: 'process',
                    hasDropdown: false,
                },
                {
                    title: this.tt('TIN TỨC'),
                    href: this.safeRoute('posts', undefined, isEn ? '/en/posts' : '/tin-tuc'),
                    type: 'posts',
                    hasDropdown: false,
                },
                {
                    title: this.tt('LIÊN HỆ'),
                    href: this.safeRoute('contact', undefined, isEn ? '/en/contact' : '/lien-he'),
                    type: 'contact',
                    hasDropdown: false,
                },
            ]
        },
    },
    watch: {
        cmsLogoUrl() {
            this.logoFallbackStage = 0
        },
        '$page.url': function () {
            this.closeDrawer()
            this.closeDesktopDropdown()
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
        onLogoError() {
            if (this.logoFallbackStage === 0 && this.cmsLogoUrl && this.cmsLogoUrl !== DEFAULT_LOGO_URL) {
                this.logoFallbackStage = 1
            } else {
                this.logoFallbackStage = 2
            }
        },
        isMenuActive(menu) {
            if (!menu || !menu.href) return false

            const path = typeof window !== 'undefined' ? window.location.pathname : this.$page?.url || ''
            const cleanPath = path.split('?')[0].split('#')[0].replace(/\/+$/, '') || '/'
            const component = (this.$page?.component || '').toLowerCase()

            if (menu.type === 'about') {
                return (
                    component.includes('about') ||
                    component.includes('histor') ||
                    cleanPath.startsWith('/gioi-thieu') ||
                    cleanPath.startsWith('/en/about')
                )
            }
            if (menu.type === 'products') {
                return (
                    component.includes('product') ||
                    cleanPath.startsWith('/san-pham') ||
                    cleanPath.startsWith('/en/products')
                )
            }
            if (menu.type === 'process') {
                return (
                    component.includes('process') ||
                    cleanPath.startsWith('/quy-trinh') ||
                    cleanPath.startsWith('/en/process')
                )
            }
            if (menu.type === 'posts') {
                return (
                    component.includes('post') ||
                    cleanPath.startsWith('/tin-tuc') ||
                    cleanPath.startsWith('/en/posts')
                )
            }
            if (menu.type === 'contact') {
                return (
                    component.includes('contact') ||
                    cleanPath.startsWith('/lien-he') ||
                    cleanPath.startsWith('/en/contact')
                )
            }

            return false
        },
        openDesktopDropdown(type) {
            this.activeDropdown = type
        },
        toggleDesktopDropdown(type) {
            this.activeDropdown = this.activeDropdown === type ? null : type
        },
        closeDesktopDropdown() {
            this.activeDropdown = null
        },
        openDrawer() {
            this.isDrawerOpen = true
            if (typeof document !== 'undefined') {
                document.body.classList.add('overflow-hidden')
            }
        },
        closeDrawer() {
            this.isDrawerOpen = false
            this.expandedMobileCategoryIds = []
            if (typeof document !== 'undefined') {
                document.body.classList.remove('overflow-hidden')
            }
        },
        toggleMobileCategory(id) {
            const idx = this.expandedMobileCategoryIds.indexOf(id)
            if (idx > -1) {
                this.expandedMobileCategoryIds.splice(idx, 1)
            } else {
                this.expandedMobileCategoryIds.push(id)
            }
        },
        isMobileCategoryExpanded(id) {
            return this.expandedMobileCategoryIds.includes(id)
        },
        handleScroll() {
            if (typeof window === 'undefined') return
            this.isScrolled = window.scrollY > 46
        },
        handleClickOutside(e) {
            if (!this.activeDropdown) return
            const root = this.$refs.headerRoot
            if (root && !root.contains(e.target)) {
                this.closeDesktopDropdown()
            }
        },
        handleKeydown(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
                e.preventDefault()
                this.isSearchOpen = !this.isSearchOpen
                return
            }
            if (e.key === 'Escape') {
                if (this.isSearchOpen) this.isSearchOpen = false
                if (this.isDrawerOpen) this.closeDrawer()
                if (this.activeDropdown) this.closeDesktopDropdown()
            }
        },
    },
    mounted() {
        if (typeof window !== 'undefined') {
            this.handleScroll()
            window.addEventListener('scroll', this.handleScroll, { passive: true })
            window.addEventListener('keydown', this.handleKeydown)
            document.addEventListener('click', this.handleClickOutside)
        }
    },
    beforeUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('scroll', this.handleScroll)
            window.removeEventListener('keydown', this.handleKeydown)
            document.removeEventListener('click', this.handleClickOutside)
            document.body.classList.remove('overflow-hidden')
        }
    },
}
</script>

<style lang="scss">
body {
    --header-height-sm: 64px;
    --header-height-md: 64px;
    --header-height-lg: 72px;
    --header-height-xl: 72px;
    --header-total-lg: 118px;
}
</style>

<style lang="scss" scoped>
.drawer-fade-enter-active,
.drawer-fade-leave-active {
    transition: opacity 0.25s ease;
}
.drawer-fade-enter-from,
.drawer-fade-leave-to {
    opacity: 0;
}

.drawer-slide-enter-active,
.drawer-slide-leave-active {
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-slide-enter-from,
.drawer-slide-leave-to {
    transform: translateX(100%);
}

.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
    transition: opacity 0.18s ease, transform 0.18s ease;
}
.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

.accordion-enter-active,
.accordion-leave-active {
    transition: opacity 0.2s ease, max-height 0.25s ease;
    max-height: 480px;
    overflow: hidden;
}
.accordion-enter-from,
.accordion-leave-to {
    opacity: 0;
    max-height: 0;
}
</style>
