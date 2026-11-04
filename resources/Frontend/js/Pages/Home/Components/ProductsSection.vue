<template>
    <section v-scroll-reveal id="products-section" class="py-12 md:py-20 bg-dark-bg">
        <div class="max-w-[1180px] mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header (Figma Node 2261:5565) -->
            <div v-if="title" class="text-center mb-10 sm:mb-14">
                <h2
                    class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] uppercase tracking-tight leading-[46px] font-sans"
                >
                    {{ tt(title) }}
                </h2>
            </div>

            <!-- 3 Category Carousel Rows (Figma Node 2261:5577 - Frame 238) -->
            <div class="space-y-12 sm:space-y-16">
                <div
                    v-for="(category, rIdx) in displayCategories"
                    :key="category.id"
                    class="space-y-5 max-w-[1116px] mx-auto"
                >
                    <!-- Row Header: Category Name + Prev/Next Controls (Figma Node 2261:5580) -->
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-xl sm:text-2xl font-bold text-[#FAFAFA] font-sans">
                            {{ tt(category.name) }}
                        </h3>

                        <!-- Navigation Arrows (Figma Node 2261:5592) -->
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="scrollRow(rIdx, 'prev')"
                                class="w-8 h-8 rounded-full border border-white/20 text-white/80 hover:text-white hover:border-primary hover:bg-primary/20 flex items-center justify-center transition-colors duration-200 cursor-pointer shadow-sm active:scale-95"
                                aria-label="Previous Slide"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click="scrollRow(rIdx, 'next')"
                                class="w-8 h-8 rounded-full border border-white/20 text-white/80 hover:text-white hover:border-primary hover:bg-primary/20 flex items-center justify-center transition-colors duration-200 cursor-pointer shadow-sm active:scale-95"
                                aria-label="Next Slide"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Smooth GPU Product Track (Figma Node 2261:5595 - Frame 253: 264x419 cards, gap 20px, 1116px = 4 cards) -->
                    <div class="relative group max-w-[1116px] mx-auto">
                        <!-- Synchronized Left Prev Button -->
                        <button
                            type="button"
                            @click="scrollRow(rIdx, 'prev')"
                            class="absolute -left-3 sm:-left-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-[#111322]/90 border border-white/20 text-white hover:bg-[#2970FF] hover:border-[#2970FF] flex items-center justify-center transition-colors duration-200 active:scale-95 shadow-xl backdrop-blur-sm cursor-pointer"
                            aria-label="Previous Slide"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <div
                            :ref="el => { if (el) rowViewports[rIdx] = el }"
                            class="overflow-hidden w-full py-2"
                            @touchstart.passive="e => onRowTouchStart(rIdx, e)"
                            @touchend.passive="e => onRowTouchEnd(rIdx, e)"
                        >
                            <div
                                :ref="el => { if (el) rowTracks[rIdx] = el }"
                                class="flex items-center gap-5 w-full"
                            >
                                <div
                                    v-for="(slotItem, slotIndex) in getVisibleRowItems(category, rIdx)"
                                    :key="slotIndex"
                                    class="w-[264px] min-w-[264px] max-w-[264px] h-[419px] flex-shrink-0 rounded-[12px] overflow-hidden flex flex-col justify-between hover:shadow-2xl hover:shadow-primary/20 border border-white/5 cursor-pointer bg-[#182238] select-none group/card"
                                >
                                    <!-- Top: Fixed 264x335 Product Image Frame with Inner Horizontal Slide Transition -->
                                    <div class="w-full h-[335px] relative overflow-hidden bg-[#D1D2D2] shrink-0">
                                        <!-- Best Choice Badge (Figma Node 2261:5598: 97x25px, bg #00359E, rounded 4px) -->
                                        <div
                                            v-if="slotIndex === 0 && category.hasBestChoice"
                                            class="absolute top-3 left-3 z-20 w-[97px] h-[25px] bg-[#00359E] text-[#F5F8FF] text-[12px] font-medium leading-[17px] rounded-[4px] shadow-md flex items-center justify-center select-none pointer-events-none"
                                        >
                                            {{ tt('Best choice') }}
                                        </div>

                                        <Transition
                                            enter-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform"
                                            :enter-from-class="(rowDirections[rIdx] || slideDirection) === 'next' ? 'translate-x-full' : '-translate-x-full'"
                                            enter-to-class="translate-x-0"
                                            leave-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform absolute inset-0 pointer-events-none"
                                            leave-from-class="translate-x-0"
                                            :leave-to-class="(rowDirections[rIdx] || slideDirection) === 'next' ? '-translate-x-full' : 'translate-x-full'"
                                        >
                                            <div
                                                :key="`${rowIndices[rIdx] || 0}-${slotIndex}`"
                                                class="w-full h-full relative overflow-hidden"
                                            >
                                                <img
                                                    :src="slotItem.image"
                                                    :alt="slotItem.title"
                                                    class="w-full h-full object-cover object-center select-none"
                                                    loading="eager"
                                                    decoding="sync"
                                                />
                                            </div>
                                        </Transition>

                                        <!-- Subtle Sheen Overlay on Hover -->
                                        <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none z-10"></div>
                                    </div>

                                    <!-- Bottom: Locked 264x84px Dark Slate Card Footer (Figma Node 2261:5600: height 84px, bg #30374F) -->
                                    <div class="w-full h-[84px] bg-[#30374F] relative overflow-hidden shrink-0">
                                        <Transition
                                            enter-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform"
                                            :enter-from-class="(rowDirections[rIdx] || slideDirection) === 'next' ? 'translate-x-full' : '-translate-x-full'"
                                            enter-to-class="translate-x-0"
                                            leave-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform absolute inset-0 pointer-events-none"
                                            leave-from-class="translate-x-0"
                                            :leave-to-class="(rowDirections[rIdx] || slideDirection) === 'next' ? '-translate-x-full' : 'translate-x-full'"
                                        >
                                            <div
                                                :key="`${rowIndices[rIdx] || 0}-${slotIndex}`"
                                                class="w-full h-full px-6 py-3 flex flex-col justify-center space-y-1"
                                            >
                                                <h4 class="text-base font-bold text-[#F5F8FF] truncate font-sans leading-[22px]">
                                                    {{ tt(slotItem.title) }}
                                                </h4>
                                                <p class="text-xs text-[#B9C0D4] truncate font-normal leading-[17px]">
                                                    {{ tt(slotItem.description) }}
                                                </p>
                                            </div>
                                        </Transition>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Synchronized Right Next Button -->
                        <button
                            type="button"
                            @click="scrollRow(rIdx, 'next')"
                            class="absolute -right-3 sm:-right-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-[#111322]/90 border border-white/20 text-white hover:bg-[#2970FF] hover:border-[#2970FF] flex items-center justify-center transition-colors duration-200 active:scale-95 shadow-xl backdrop-blur-sm cursor-pointer"
                            aria-label="Next Slide"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Row CTA Button: "Xem thêm sản phẩm khác" (Figma Node 2261:5613 - Frame 260: 239x48px, gradient 90deg #2970FF to #00359E) -->
                    <div class="flex justify-center pt-2 sm:pt-4">
                        <Link
                            :href="($page?.props?.locale?.current === 'en') ? (category.url ? category.url.replace('/san-pham', '/en/products') : '/en/products') : (category.url || '/san-pham')"
                            class="w-[239px] h-[48px] rounded-[8px] text-[#FFFFFF] text-base font-semibold inline-flex items-center justify-center hover:opacity-90 transition-opacity shadow-md cursor-pointer active:scale-95"
                            style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
                        >
                            {{ tt('Xem thêm sản phẩm khác') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'ProductsSection',
    props: {
        title: {
            type: String,
            default: 'SẢN PHẨM NỔI BẬT',
        },
        products: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            slideDirection: 'next',
            rowTracks: {},
            rowViewports: {},
            rowIndices: { 0: 0, 1: 0, 2: 0 },
            rowDirections: { 0: 'next', 1: 'next', 2: 'next' },
            rowOffsets: { 0: 0, 1: 0, 2: 0 },
            rowTouchStartX: {},
            categoryRows: [
                {
                    id: 'keychains',
                    name: 'Móc khoá',
                    keywords: ['khoa', 'keychain', 'moc'],
                    hasBestChoice: true,
                    url: '/san-pham?category=moc-khoa',
                    defaultItems: [
                        {
                            id: 'kc-1',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/mockhoa-toho.png',
                            isBestChoice: true,
                            url: '/san-pham',
                        },
                        {
                            id: 'kc-2',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/mockhoa-co4la.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'kc-3',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/mockhoa-melacung.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'kc-4',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/mockhoa-cavang.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'kc-5',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/mockhoa-toho.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                    ],
                },
                {
                    id: 'office',
                    name: 'Đồ dùng/ Đồ trang trí văn phòng',
                    keywords: ['decor', 'van-phong', 'office', 'trang-tri'],
                    hasBestChoice: true,
                    url: '/san-pham?category=van-phong',
                    defaultItems: [
                        {
                            id: 'of-1',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/decor-denngu.png',
                            isBestChoice: true,
                            url: '/san-pham',
                        },
                        {
                            id: 'of-2',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/decor-ongpudding.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'of-3',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/decor-giane.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'of-4',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/decor-giatainghe.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'of-5',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/decor-denngu.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                    ],
                },
                {
                    id: 'custom',
                    name: 'Gia công theo yêu cầu',
                    keywords: ['custom', 'gia-cong', 'yeu-cau'],
                    hasBestChoice: false,
                    url: '/san-pham?category=gia-cong',
                    defaultItems: [
                        {
                            id: 'cs-1',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/giacong-emily.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'cs-2',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/giacong-tuongmat.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'cs-3',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/giacong-hopuno.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'cs-4',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/giacong-opbatlua.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                        {
                            id: 'cs-5',
                            title: 'Tên sản phẩm',
                            description: 'This awesome element has more variants and fully',
                            image: '/assets/images/homepage/products/giacong-emily.png',
                            isBestChoice: false,
                            url: '/san-pham',
                        },
                    ],
                },
            ],
        };
    },
    computed: {
        displayCategories() {
            return this.categoryRows.map((cat, rIdx) => {
                let items = [];
                if (this.products && this.products.length > 0) {
                    const matched = this.products.filter(p => {
                        const catSlug = (p.category?.slug || p.category_slug || p.category || '').toLowerCase();
                        const catName = (p.category?.name || '').toLowerCase();
                        return (
                            catSlug.includes(cat.id) ||
                            cat.keywords.some(k => catSlug.includes(k) || catName.includes(k))
                        );
                    });

                    if (matched.length > 0) {
                        items = matched.map((p, idx) => ({
                            id: p.id || `p-${rIdx}-${idx}`,
                            title: p.title || p.name || 'Tên sản phẩm',
                            description:
                                p.description ||
                                p.short_description ||
                                'This awesome element has more variants and fully',
                            image:
                                p.thumbnail?.url ||
                                p.image?.url ||
                                p.thumbnail ||
                                cat.defaultItems[idx % cat.defaultItems.length].image,
                            isBestChoice: idx === 0 && cat.hasBestChoice,
                            url: p.slug ? `/san-pham/${p.slug}` : '/san-pham',
                        }));
                    }
                }

                if (items.length < 5) {
                    const existingImages = new Set(items.map(i => i.image));
                    const remainingDefaults = cat.defaultItems.filter(d => !existingImages.has(d.image));
                    items = [...items, ...remainingDefaults].slice(0, 5);
                    if (items.length < 5) {
                        items = cat.defaultItems;
                    }
                }

                return {
                    ...cat,
                    items,
                };
            });
        },
    },
    mounted() {
        this.preloadImages();
        if (typeof window !== 'undefined') {
            window.addEventListener('resize', this.handleResize, { passive: true });
        }
    },
    beforeUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('resize', this.handleResize);
        }
    },
    methods: {
        preloadImages() {
            if (typeof window === 'undefined') return;
            this.displayCategories.forEach(cat => {
                (cat.items || []).forEach(item => {
                    if (item && item.image) {
                        const img = new window.Image();
                        img.src = item.image;
                    }
                });
            });
        },
        getVisibleRowItems(category, rIdx) {
            const list = (category && category.items) || [];
            if (list.length === 0) return [];
            const total = list.length;
            const startIdx = this.rowIndices[rIdx] || 0;
            const cards = [];
            for (let i = 0; i < 4; i++) {
                const itemIndex = (startIdx + i) % total;
                cards.push({
                    ...list[itemIndex],
                    slotIndex: i,
                    itemIndex,
                });
            }
            return cards;
        },
        getRowMaxOffset(rIdx) {
            const viewport = this.rowViewports[rIdx];
            const cat = this.displayCategories[rIdx];
            const totalItems = cat && cat.items ? cat.items.length : 0;
            const trackWidth = totalItems > 0 ? totalItems * 264 + (totalItems - 1) * 20 : 0;
            const viewportWidth = viewport ? viewport.clientWidth : 1116;
            return Math.max(0, trackWidth - viewportWidth);
        },
        handleResize() {
            this.displayCategories.forEach((_, rIdx) => {
                const maxOffset = this.getRowMaxOffset(rIdx);
                const cur = this.rowOffsets[rIdx] || 0;
                if (cur > maxOffset) {
                    this.rowOffsets = { ...this.rowOffsets, [rIdx]: maxOffset };
                }
            });
        },
        scrollRow(rIdx, direction) {
            const dir = direction === 'prev' ? 'prev' : 'next';
            this.slideDirection = dir;
            this.rowDirections = { ...this.rowDirections, [rIdx]: dir };
            const cat = this.displayCategories[rIdx];
            const total = (cat && cat.items ? cat.items.length : 0) || 1;
            const curIdx = this.rowIndices[rIdx] || 0;
            const nextIdx = dir === 'next' ? (curIdx + 1) % total : (curIdx - 1 + total) % total;
            this.rowIndices = { ...this.rowIndices, [rIdx]: nextIdx };

            const step = 284; // 264px card width + 20px gap
            const maxOffset = this.getRowMaxOffset(rIdx);
            if (maxOffset <= 0) {
                const track = this.rowTracks[rIdx];
                if (track && track.scrollBy) {
                    track.scrollBy({ left: direction === 'next' ? step : -step, behavior: 'smooth' });
                }
                return;
            }
            const current = this.rowOffsets[rIdx] || 0;
            let nextOffset = current;
            if (direction === 'next') {
                if (current >= maxOffset - 5) {
                    nextOffset = 0;
                } else {
                    nextOffset = Math.min(maxOffset, current + step);
                }
            } else {
                if (current <= 5) {
                    nextOffset = maxOffset;
                } else {
                    nextOffset = Math.max(0, current - step);
                }
            }
            this.rowOffsets = { ...this.rowOffsets, [rIdx]: nextOffset };
        },
        onRowTouchStart(rIdx, e) {
            if (e.touches && e.touches.length > 0) {
                this.rowTouchStartX[rIdx] = e.touches[0].clientX;
            }
        },
        onRowTouchEnd(rIdx, e) {
            if (!e.changedTouches || e.changedTouches.length === 0) return;
            const startX = this.rowTouchStartX[rIdx] || 0;
            const deltaX = e.changedTouches[0].clientX - startX;
            if (Math.abs(deltaX) > 40) {
                this.scrollRow(rIdx, deltaX < 0 ? 'next' : 'prev');
            }
        },
    },
};
</script>
<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}
.no-scrollbar {
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}
</style>

