<template>
    <!-- Desktop Top Info Bar (Figma 979:32773 / 979:32412 — 46px, #00284B, 1152px inner) -->
    <div
        v-if="companyName || contactEmail || contactPhone"
        class="hidden lg:block w-full h-[46px] bg-dark text-surface-light antialiased"
    >
        <div class="app-container-narrow h-full flex items-center justify-between gap-6">
            <p v-if="companyName" class="text-[14px] font-normal leading-[22px] text-surface-light truncate">
                {{ companyName }}
            </p>
            <span v-else />
            <div class="flex items-center gap-8 shrink-0 text-[14px] font-normal leading-[22px] text-surface-light">
                <a
                    v-if="contactEmail"
                    :href="'mailto:' + contactEmail"
                    class="inline-flex items-center gap-2 text-surface-light hover:text-secondary transition-colors focus:outline-none focus-visible:underline"
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
                    class="inline-flex items-center gap-2 text-surface-light hover:text-secondary transition-colors focus:outline-none focus-visible:underline"
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

    <!-- Mobile Top Info Bar (Figma 1190:6337 — 31px, #00284B, px-4) -->
    <div
        v-if="contactPhone || contactEmail"
        class="lg:hidden w-full h-[31px] bg-dark text-surface-light px-4 flex items-center justify-between gap-3 antialiased"
    >
        <a
            v-if="contactPhone"
            :href="'tel:' + cleanPhone"
            class="inline-flex items-center gap-1.5 shrink-0 text-[12px] font-normal leading-[17px] text-surface-light hover:text-secondary transition-colors focus:outline-none focus-visible:underline"
        >
            <img
                src="/assets/svg/icons/header-phone.svg"
                alt=""
                aria-hidden="true"
                width="14"
                height="14"
                class="w-[14px] h-[14px] shrink-0"
            />
            <span>{{ contactPhone }}</span>
        </a>
        <span v-else />

        <a
            v-if="contactEmail"
            :href="'mailto:' + contactEmail"
            class="inline-flex items-center gap-1.5 min-w-0 text-[12px] font-normal leading-[17px] text-surface-light hover:text-secondary transition-colors focus:outline-none focus-visible:underline"
        >
            <img
                src="/assets/svg/icons/header-mail.svg"
                alt=""
                aria-hidden="true"
                width="14"
                height="14"
                class="w-[14px] h-[14px] shrink-0"
            />
            <span class="truncate">{{ contactEmail }}</span>
        </a>
    </div>

    <!-- Main Sticky Header Bar (Figma Desktop 979:32782: 72px #FFFFFF; Mobile 1190:6346: 61px #FFFFFF border-b #E3E8EF) -->
    <header
        ref="headerRoot"
        class="sticky top-0 z-[1000] w-full bg-white antialiased transition-shadow duration-200 border-b"
        :class="
            isScrolled
                ? 'border-gray-200 shadow-xs bg-white/95 backdrop-blur-md'
                : 'border-gray-200 lg:border-transparent'
        "
        @mouseleave="closeDesktopDropdown"
    >
        <div class="app-container-narrow relative h-[61px] lg:h-[72px] flex items-center justify-between gap-3 lg:gap-6">
            <!-- Brand Logo (Desktop 120x40, Mobile 96x32, CMS general_logo) -->
            <Link
                :href="safeRoute('home', undefined, currentLocale === 'en' ? '/en' : '/')"
                class="inline-flex items-center shrink-0 min-w-0 rounded-cta focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                :aria-label="companyName || tt('Trang chủ')"
            >
                <img
                    v-if="resolvedLogoUrl"
                    :src="resolvedLogoUrl"
                    :alt="companyName || ''"
                    width="120"
                    height="40"
                    class="w-[96px] h-[32px] lg:w-[120px] lg:h-[40px] object-contain"
                    @error="onLogoError"
                />
                <span
                    v-else-if="companyName"
                    class="font-bold text-[14px] lg:text-[16px] tracking-tight text-dark uppercase truncate max-w-[140px]"
                >
                    {{ companyName }}
                </span>
            </Link>

            <!-- Desktop Navigation (Dynamic items from $page.props.configs.menus — Figma 979:32785) -->
            <nav
                v-if="menus.length > 0"
                class="hidden lg:flex items-stretch self-stretch gap-5 xl:gap-8"
                :aria-label="tt('Điều hướng chính')"
            >
                <template v-for="(menu, index) in menus" :key="index">
                    <div
                        class="relative flex items-center self-stretch"
                        @mouseenter="
                            menu.hasDropdown
                                ? openDesktopDropdown(menu.type)
                                : closeDesktopDropdown()
                        "
                    >
                        <!-- Figma 979:29410 — 72px item, default #697586, hover/active #004D86 + 3px bottom bar -->
                        <div
                            class="group/navitem inline-flex items-center gap-1 h-full border-b-[3px] transition-colors"
                            :class="
                                isMenuHighlighted(menu)
                                    ? 'border-primary-600'
                                    : 'border-transparent hover:border-primary-600'
                            "
                        >
                            <Link
                                :href="menu.href"
                                class="py-2 text-[14px] font-bold uppercase leading-[22px] tracking-normal whitespace-nowrap transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-cta"
                                :class="
                                    isMenuHighlighted(menu)
                                        ? 'text-primary-600'
                                        : 'text-ink-subtle group-hover/navitem:text-primary-600'
                                "
                                @click="closeDesktopDropdown"
                            >
                                {{ menu.title }}
                            </Link>
                            <button
                                v-if="menu.hasDropdown"
                                type="button"
                                class="inline-flex items-center justify-center w-5 h-5 rounded-cta focus:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors cursor-pointer shrink-0"
                                :aria-label="tt('Mở danh mục sản phẩm')"
                                :aria-expanded="activeDropdown === menu.type"
                                aria-haspopup="true"
                                @click.stop="toggleDesktopDropdown(menu.type)"
                            >
                                <img
                                    :src="
                                        isMenuHighlighted(menu)
                                            ? '/assets/svg/icons/header-chevron-down-active.svg'
                                            : '/assets/svg/icons/header-chevron-down.svg'
                                    "
                                    alt=""
                                    aria-hidden="true"
                                    width="20"
                                    height="20"
                                    class="w-5 h-5 transition-transform duration-200"
                                    :class="activeDropdown === menu.type ? 'rotate-180' : ''"
                                />
                            </button>
                        </div>
                    </div>
                </template>
            </nav>

            <!-- Desktop Right Actions: Search + 2 CTAs (Figma 979:32791) -->
            <div class="hidden lg:flex items-center gap-5 xl:gap-8 shrink-0">
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
                        :href="contactHref"
                        class="h-[38px] px-5 inline-flex items-center justify-center whitespace-nowrap rounded-cta bg-primary hover:bg-primary-hover active:bg-primary-active text-surface text-[14px] font-medium leading-[22px] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                    >
                        {{ tt('Nhận báo giá') }}
                    </Link>
                    <a
                        v-if="contactPhone"
                        :href="'tel:' + cleanPhone"
                        class="h-[38px] px-5 inline-flex items-center justify-center whitespace-nowrap rounded-cta bg-secondary hover:bg-secondary-hover text-primary-active text-[14px] font-medium leading-[22px] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                    >
                        {{ tt('Gọi hotline') }}
                    </a>
                </div>
            </div>

            <!-- Mobile Main Nav Controls (< lg, Figma 1190:6348: Search 36x36 + CTA 100x33 + Hamburger 36x36) -->
            <div class="flex items-center gap-2 lg:hidden shrink-0">
                <button
                    type="button"
                    @click="isSearchOpen = true"
                    class="w-9 h-9 inline-flex items-center justify-center rounded-cta bg-white hover:bg-surface-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors cursor-pointer shrink-0"
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

                <Link
                    :href="contactHref"
                    class="h-[33px] px-3 inline-flex items-center justify-center whitespace-nowrap rounded-cta bg-primary hover:bg-primary-hover active:bg-primary-active text-surface text-[12px] font-medium leading-[17px] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary shrink-0"
                >
                    {{ tt('Nhận báo giá') }}
                </Link>

                <button
                    type="button"
                    data-testid="mobile-menu-toggle"
                    @click="openDrawer"
                    class="w-9 h-9 inline-flex items-center justify-center rounded-cta bg-surface-100 hover:bg-surface-200 text-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors cursor-pointer shrink-0"
                    :aria-label="tt('Mở danh mục điều hướng')"
                    :aria-expanded="isDrawerOpen"
                    aria-controls="mobile-drawer"
                >
                    <img
                        src="/assets/svg/icons/header-menu.svg"
                        alt=""
                        aria-hidden="true"
                        width="22"
                        height="22"
                        class="w-[22px] h-[22px] object-contain"
                    />
                </button>
            </div>

            <!-- Desktop Products 2-Region Mega-Menu (Figma 856:24212, viewport-clamped zero horizontal overflow) -->
            <transition name="dropdown-fade">
                <div
                    v-if="hasProductsMenu"
                    v-show="activeDropdown === 'products'"
                    data-testid="desktop-mega-menu"
                    class="hidden lg:block absolute top-full left-8 xl:left-[180px] pt-2 z-[1010] max-w-[calc(100vw-64px)]"
                >
                    <div
                        class="bg-surface rounded-cta shadow-dropdown border border-gray-200/80 min-h-[396px] flex overflow-hidden max-w-full"
                    >
                        <!-- Left Column: Root Categories (340px #EEF2F6 + Bottom CTA) -->
                        <div
                            class="w-[280px] xl:w-[340px] shrink-0 bg-surface-100 p-5 flex flex-col justify-between gap-6"
                        >
                            <div class="flex flex-col gap-3">
                                <Link
                                    v-for="(cat, idx) in productCategories"
                                    :key="cat.id || cat.slug || idx"
                                    :href="cat.url"
                                    class="min-h-[46px] px-5 xl:px-6 py-3 rounded-cta text-[14px] leading-[22px] font-bold text-dark flex items-center justify-between gap-2 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                    :class="
                                        activeRootCategoryIndex === idx
                                            ? 'bg-surface-300'
                                            : 'bg-surface-100 hover:bg-surface-200'
                                    "
                                    @mouseenter="activeRootCategoryIndex = idx"
                                    @focus="activeRootCategoryIndex = idx"
                                    @click="closeDesktopDropdown"
                                >
                                    <span class="truncate">{{ cat.title }}</span>
                                    <img
                                        src="/assets/svg/icons/header-chevron-right.svg"
                                        alt=""
                                        aria-hidden="true"
                                        width="20"
                                        height="20"
                                        class="w-5 h-5 shrink-0"
                                    />
                                </Link>
                            </div>

                            <Link
                                :href="allProductsHref"
                                class="h-[38px] w-full rounded-cta bg-primary hover:bg-primary-hover active:bg-primary-active text-surface text-[14px] leading-[22px] font-medium inline-flex items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                @click="closeDesktopDropdown"
                            >
                                {{ tt('Xem tất cả sản phẩm') }}
                            </Link>
                        </div>

                        <!-- Right Region: Level 2 Groups & Level 3 Leaf Categories (#FAFAFA) -->
                        <div
                            v-if="activeRootCategory"
                            class="bg-surface p-5 xl:pl-10 xl:pr-5 xl:py-5 min-w-0 overflow-y-auto max-h-[70vh]"
                            :class="
                                activeLevel2Groups.length >= 2
                                    ? 'w-[540px] xl:w-[632px]'
                                    : 'w-[300px] xl:w-[338px]'
                            "
                        >
                            <div
                                v-if="activeLevel2Groups.length > 0"
                                :class="
                                    activeLevel2Groups.length >= 2
                                        ? 'grid grid-cols-2 gap-x-3 xl:gap-x-4 gap-y-6 items-start content-start'
                                        : 'flex flex-col gap-5'
                                "
                            >
                                <div
                                    v-for="sub in activeLevel2Groups"
                                    :key="sub.id || sub.slug"
                                    class="min-w-0 flex flex-col gap-2"
                                >
                                    <Link
                                        :href="sub.url"
                                        class="px-4 xl:px-6 py-0.5 text-[14px] leading-[22px] font-bold text-primary hover:text-primary-sub-hover transition-colors block rounded-cta focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                        @click="closeDesktopDropdown"
                                    >
                                        {{ sub.title }}
                                    </Link>

                                    <div
                                        v-if="sub.children && sub.children.length > 0"
                                        class="flex flex-col"
                                    >
                                        <Link
                                            v-for="leaf in sub.children"
                                            :key="leaf.id || leaf.slug"
                                            :href="leaf.url"
                                            class="min-h-[42px] xl:min-h-[46px] px-4 xl:px-6 py-2.5 rounded-cta text-[14px] leading-[22px] font-medium text-ink-subtle bg-surface hover:bg-surface-100 hover:text-dark active:bg-surface-200 flex items-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                            @click="closeDesktopDropdown"
                                        >
                                            <span class="line-clamp-2">{{ leaf.title }}</span>
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="flex flex-col gap-3 px-4 xl:px-6 py-2">
                                <Link
                                    :href="activeRootCategory.url"
                                    class="text-[14px] leading-[22px] font-bold text-primary hover:text-primary-sub-hover transition-colors"
                                    @click="closeDesktopDropdown"
                                >
                                    {{ activeRootCategory.title }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
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
                class="fixed top-0 right-0 bottom-0 z-[1050] w-[85vw] max-w-[340px] bg-white border-l border-gray-200 flex flex-col justify-between overflow-y-auto shadow-2xl antialiased"
                role="dialog"
                aria-modal="true"
                :aria-label="tt('Menu di động')"
            >
                <div class="p-5 space-y-5">
                    <!-- Drawer Header: Logo + Close Button -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <Link
                            :href="safeRoute('home', undefined, currentLocale === 'en' ? '/en' : '/')"
                            @click="closeDrawer"
                            class="inline-flex items-center shrink-0 min-w-0 max-w-[200px]"
                            :aria-label="companyName || tt('Trang chủ')"
                        >
                            <img
                                v-if="resolvedLogoUrl"
                                :src="resolvedLogoUrl"
                                :alt="companyName || ''"
                                width="96"
                                height="32"
                                class="h-8 w-auto max-w-[140px] object-contain"
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

                    <!-- Drawer Navigation Links (Touch target >= 44px, 3-level product category accordion) -->
                    <nav v-if="menus.length > 0" class="space-y-1" :aria-label="tt('Điều hướng di động')">
                        <template v-for="(menu, index) in menus" :key="index">
                            <div v-if="menu.hasDropdown" class="space-y-1">
                                <div
                                    class="flex items-center justify-between min-h-[44px] rounded-[6px] transition-colors"
                                    :class="
                                        isMenuActive(menu)
                                            ? 'bg-secondary/40 text-primary-600'
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
                                        v-if="productCategories.length > 0"
                                        type="button"
                                        @click="isProductAccordionOpen = !isProductAccordionOpen"
                                        class="w-11 h-11 inline-flex items-center justify-center rounded-[6px] hover:bg-surface-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary cursor-pointer shrink-0"
                                        :aria-label="tt('Mở rộng danh mục sản phẩm')"
                                        :aria-expanded="isProductAccordionOpen"
                                    >
                                        <img
                                            :src="
                                                isProductAccordionOpen
                                                    ? '/assets/svg/icons/header-chevron-down-active.svg'
                                                    : '/assets/svg/icons/header-chevron-down.svg'
                                            "
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
                                                    class="flex-1 px-3 py-2 text-[14px] font-semibold text-dark hover:text-primary truncate"
                                                >
                                                    {{ cat.title }}
                                                </Link>
                                                <button
                                                    v-if="cat.children && cat.children.length > 0"
                                                    type="button"
                                                    @click.stop="toggleMobileCategory('cat-' + (cat.id || cat.slug))"
                                                    class="w-10 h-10 inline-flex items-center justify-center rounded-[6px] hover:bg-surface-200 shrink-0 cursor-pointer"
                                                    :aria-label="tt('Mở rộng danh mục con')"
                                                    :aria-expanded="isMobileCategoryExpanded('cat-' + (cat.id || cat.slug))"
                                                >
                                                    <img
                                                        src="/assets/svg/icons/header-chevron-down.svg"
                                                        alt=""
                                                        aria-hidden="true"
                                                        width="16"
                                                        height="16"
                                                        class="w-4 h-4 transition-transform duration-200"
                                                        :class="isMobileCategoryExpanded('cat-' + (cat.id || cat.slug)) ? 'rotate-180' : ''"
                                                    />
                                                </button>
                                            </div>

                                            <transition name="accordion">
                                                <div
                                                    v-if="cat.children && cat.children.length > 0"
                                                    v-show="isMobileCategoryExpanded('cat-' + (cat.id || cat.slug))"
                                                    class="pl-3 ml-3 border-l border-gray-200 space-y-1 py-0.5"
                                                >
                                                    <div
                                                        v-for="child in cat.children"
                                                        :key="child.id || child.slug"
                                                        class="space-y-0.5"
                                                    >
                                                        <div class="flex items-center justify-between min-h-[40px] rounded-[6px] hover:bg-surface-100">
                                                            <Link
                                                                :href="child.url"
                                                                @click="closeDrawer"
                                                                class="flex-1 px-3 py-2 text-[13px] font-bold text-primary hover:text-primary-sub-hover truncate"
                                                            >
                                                                {{ child.title }}
                                                            </Link>
                                                            <button
                                                                v-if="child.children && child.children.length > 0"
                                                                type="button"
                                                                @click.stop="toggleMobileCategory('sub-' + (child.id || child.slug))"
                                                                class="w-9 h-9 inline-flex items-center justify-center rounded-[6px] hover:bg-surface-200 shrink-0 cursor-pointer"
                                                                :aria-label="tt('Mở rộng danh mục con')"
                                                                :aria-expanded="isMobileCategoryExpanded('sub-' + (child.id || child.slug))"
                                                            >
                                                                <img
                                                                    src="/assets/svg/icons/header-chevron-down.svg"
                                                                    alt=""
                                                                    aria-hidden="true"
                                                                    width="14"
                                                                    height="14"
                                                                    class="w-3.5 h-3.5 transition-transform duration-200"
                                                                    :class="isMobileCategoryExpanded('sub-' + (child.id || child.slug)) ? 'rotate-180' : ''"
                                                                />
                                                            </button>
                                                        </div>

                                                        <div
                                                            v-if="
                                                                child.children &&
                                                                child.children.length > 0 &&
                                                                isMobileCategoryExpanded('sub-' + (child.id || child.slug))
                                                            "
                                                            class="pl-3 ml-3 border-l border-gray-200 space-y-0.5 py-0.5"
                                                        >
                                                            <Link
                                                                v-for="leaf in child.children"
                                                                :key="leaf.id || leaf.slug"
                                                                :href="leaf.url"
                                                                @click="closeDrawer"
                                                                class="min-h-[38px] px-3 py-1.5 rounded-[6px] flex items-center text-[13px] font-medium text-ink-subtle hover:text-dark hover:bg-surface-100"
                                                            >
                                                                <span class="truncate">{{ leaf.title }}</span>
                                                            </Link>
                                                        </div>
                                                    </div>
                                                </div>
                                            </transition>
                                        </div>

                                        <Link
                                            :href="allProductsHref"
                                            @click="closeDrawer"
                                            class="min-h-[44px] px-3 py-2 rounded-[6px] flex items-center justify-between text-[13px] font-semibold text-primary hover:bg-surface-100"
                                        >
                                            <span>{{ tt('Xem tất cả sản phẩm') }}</span>
                                            <img
                                                src="/assets/svg/icons/header-chevron-right.svg"
                                                alt=""
                                                aria-hidden="true"
                                                width="16"
                                                height="16"
                                                class="w-4 h-4 shrink-0"
                                            />
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
                                        ? 'bg-secondary/40 text-primary-600'
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
                            :href="contactHref"
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
            activeRootCategoryIndex: 0,
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
        contactHref() {
            const isEn = this.currentLocale === 'en'
            return this.safeRoute('contact', undefined, isEn ? '/en/contact' : '/lien-he')
        },
        allProductsHref() {
            const isEn = this.currentLocale === 'en'
            return this.safeRoute('products.index', undefined, isEn ? '/en/products' : '/san-pham')
        },
        cmsLogoUrl() {
            const raw = this.siteConfig.general_logo || this.siteConfig.logo || ''
            return typeof raw === 'string' ? raw.trim() : ''
        },
        resolvedLogoUrl() {
            if (this.logoFallbackStage > 0) return ''
            return this.cmsLogoUrl
        },
        productCategories() {
            const rawCategories =
                this.$page?.props?.configs?.product_categories ||
                this.$page?.props?.product_categories ||
                this.$page?.props?.data?.product_categories ||
                []

            if (!Array.isArray(rawCategories) || rawCategories.length === 0) {
                return []
            }

            const baseProductsUrl = this.allProductsHref
            const buildCategoryUrl = (item, rootSlug = '') => {
                if (!item) return baseProductsUrl
                if (typeof item.url === 'string' && item.url.trim() !== '') {
                    return item.url.trim()
                }
                const slug = item.slug ? String(item.slug).trim() : ''
                if (rootSlug && slug && rootSlug !== slug) {
                    return `${baseProductsUrl}?category=${encodeURIComponent(rootSlug)}&subcategory=${encodeURIComponent(slug)}`
                }
                if (slug) {
                    return `${baseProductsUrl}?category=${encodeURIComponent(slug)}`
                }
                return baseProductsUrl
            }

            const extractRawChildren = (node) =>
                (Array.isArray(node?.children) && node.children.length > 0 && node.children) ||
                (Array.isArray(node?.subcategories) && node.subcategories.length > 0 && node.subcategories) ||
                (Array.isArray(node?.nodes) && node.nodes.length > 0 && node.nodes) ||
                []

            const hasFlatChildren = rawCategories.some((c) => c && Number(c.parent_id) > 0)
            let roots = []

            if (!hasFlatChildren) {
                roots = rawCategories.filter((cat) => cat && (cat.title || cat.name))
            } else {
                const nodeMap = new Map()
                rawCategories.forEach((item) => {
                    if (!item || !(item.title || item.name)) return
                    nodeMap.set(item.id, {
                        ...item,
                        children: [...extractRawChildren(item)],
                    })
                })
                nodeMap.forEach((item) => {
                    const parentId = Number(item.parent_id || 0)
                    if (parentId === 0) {
                        roots.push(item)
                    } else if (nodeMap.has(parentId)) {
                        const parent = nodeMap.get(parentId)
                        if (!parent.children.some((c) => c.id === item.id)) {
                            parent.children.push(item)
                        }
                    }
                })
            }

            return roots.map((root) => {
                const rootSlug = root.slug || ''
                const level2Raw = extractRawChildren(root)
                const level2 = level2Raw
                    .filter((sub) => sub && (sub.title || sub.name))
                    .map((sub) => {
                        const level3Raw = extractRawChildren(sub)
                        const level3 = level3Raw
                            .filter((leaf) => leaf && (leaf.title || leaf.name))
                            .map((leaf) => ({
                                id: leaf.id,
                                title: leaf.title || leaf.name,
                                slug: leaf.slug,
                                url: buildCategoryUrl(leaf, rootSlug),
                            }))

                        return {
                            id: sub.id,
                            title: sub.title || sub.name,
                            slug: sub.slug,
                            url: buildCategoryUrl(sub, rootSlug),
                            children: level3,
                        }
                    })

                return {
                    id: root.id,
                    title: root.title || root.name,
                    slug: rootSlug,
                    url: buildCategoryUrl(root),
                    children: level2,
                }
            })
        },
        activeRootCategory() {
            if (!this.productCategories.length) return null
            return this.productCategories[this.activeRootCategoryIndex] || this.productCategories[0] || null
        },
        activeLevel2Groups() {
            return this.activeRootCategory?.children || []
        },
        menus() {
            const cmsMenus = this.siteConfig?.menus
            if (!Array.isArray(cmsMenus) || cmsMenus.length === 0) {
                return []
            }

            return cmsMenus
                .filter((item) => item && (item.title || item.label))
                .map((item) => {
                    const rawType = String(item.type || '').toLowerCase()
                    const href =
                        (typeof item.href === 'string' && item.href.trim()) ||
                        (typeof item.url === 'string' && item.url.trim()) ||
                        (item.route_name ? this.safeRoute(item.route_name, undefined, '/') : '/')
                    const isProducts = Boolean(item.hasDropdown || rawType === 'products')
                    return {
                        title: String(item.title || item.label).trim(),
                        href,
                        type: rawType || (isProducts ? 'products' : ''),
                        hasDropdown: isProducts && this.productCategories.length > 0,
                    }
                })
        },
        hasProductsMenu() {
            return this.productCategories.length > 0 && this.menus.some((m) => m.hasDropdown)
        },
    },
    watch: {
        cmsLogoUrl() {
            this.logoFallbackStage = 0
        },
        productCategories(nextList) {
            if (!Array.isArray(nextList) || this.activeRootCategoryIndex >= nextList.length) {
                this.activeRootCategoryIndex = 0
            }
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
            this.logoFallbackStage = 1
        },
        isMenuHighlighted(menu) {
            if (!menu) return false
            if (menu.hasDropdown && this.activeDropdown === menu.type) return true
            return this.isMenuActive(menu)
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
            if (menu.type === 'documents') {
                return (
                    component.includes('document') ||
                    cleanPath.startsWith('/tai-lieu') ||
                    cleanPath.startsWith('/en/documents')
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

            const menuPath = String(menu.href || '').split('?')[0].replace(/^https?:\/\/[^/]+/, '').replace(/\/+$/, '') || '/'
            return menuPath !== '/' && cleanPath.startsWith(menuPath)
        },
        openDesktopDropdown(type) {
            if (this.activeDropdown !== type) {
                this.activeRootCategoryIndex = 0
            }
            this.activeDropdown = type
        },
        toggleDesktopDropdown(type) {
            if (this.activeDropdown === type) {
                this.activeDropdown = null
            } else {
                this.activeRootCategoryIndex = 0
                this.activeDropdown = type
            }
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
            const threshold = window.innerWidth >= 1024 ? 46 : 31
            this.isScrolled = window.scrollY > threshold
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
    --header-height-sm: 61px;
    --header-height-md: 61px;
    --header-height-lg: 72px;
    --header-height-xl: 72px;
    --header-total-sm: 92px;
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
    max-height: 640px;
    overflow: hidden;
}
.accordion-enter-from,
.accordion-leave-to {
    opacity: 0;
    max-height: 0;
}
</style>
