<template>
    <section id="products-section" class="py-12 md:py-20 bg-dark-bg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1240px]">
            <!-- Section Header (Figma Node 2261:5565) -->
            <div v-if="title" class="text-center mb-10 sm:mb-14">
                <h2
                    class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] uppercase tracking-tight leading-[46px] font-sans"
                >
                    {{ title }}
                </h2>
            </div>

            <!-- 3 Category Carousel Rows (Figma Node 2261:5577 - Frame 238) -->
            <div class="space-y-12 sm:space-y-16">
                <div
                    v-for="(category, rIdx) in displayCategories"
                    :key="category.id"
                    class="space-y-5"
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
                                class="w-8 h-8 text-white/60 hover:text-white flex items-center justify-center transition-colors cursor-pointer select-none active:scale-95"
                                aria-label="Previous Slide"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click="scrollRow(rIdx, 'next')"
                                class="w-8 h-8 text-white/60 hover:text-white flex items-center justify-center transition-colors cursor-pointer select-none active:scale-95"
                                aria-label="Next Slide"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Scrollable Product Track (Figma Node 2261:5595 - Frame 253: 264x419 cards, gap 20px) -->
                    <div class="relative group">
                        <div
                            :ref="el => { if (el) rowTracks[rIdx] = el }"
                            class="flex items-center gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-0.5"
                        >
                            <div
                                v-for="(item, index) in category.items"
                                :key="item.id || index"
                                class="w-[264px] min-w-[264px] max-w-[264px] h-[419px] flex-shrink-0 rounded-[12px] overflow-hidden flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-primary/20 border border-white/5 cursor-pointer bg-[#182238] select-none group/card"
                            >
                                <!-- Top: Product Image Area (Figma: height 335px, bg #D1D2D2) -->
                                <div class="w-full h-[335px] relative overflow-hidden bg-[#D1D2D2] flex items-center justify-center">
                                    <!-- Best Choice Badge (Figma Node 2261:5598: 97x25px, bg #00359E, rounded 4px) -->
                                    <div
                                        v-if="item.isBestChoice"
                                        class="absolute top-3 left-3 z-10 w-[97px] h-[25px] bg-[#00359E] text-[#F5F8FF] text-[12px] font-medium leading-[17px] rounded-[4px] shadow-md flex items-center justify-center select-none"
                                    >
                                        Best choice
                                    </div>

                                    <!-- Product Image -->
                                    <img
                                        :src="item.image"
                                        :alt="item.title"
                                        class="w-full h-full object-cover object-center group-hover/card:scale-105 transition-transform duration-500 ease-out select-none"
                                        loading="lazy"
                                    />

                                    <!-- Subtle Sheen Overlay on Hover -->
                                    <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                                </div>

                                <!-- Bottom: Dark Slate Card Footer (Figma Node 2261:5600: height 84px, bg #30374F) -->
                                <div class="h-[84px] bg-[#30374F] px-6 py-3 flex flex-col justify-center space-y-1">
                                    <h4 class="text-base font-bold text-[#F5F8FF] truncate font-sans leading-[22px]">
                                        {{ tt(item.title) }}
                                    </h4>
                                    <p class="text-xs text-[#B9C0D4] truncate font-normal leading-[17px]">
                                        {{ tt(item.description) }}
                                    </p>
                                </div>
                            </div>
                        </div>
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
            rowTracks: {},
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
    methods: {
        scrollRow(rIdx, direction) {
            const track = this.rowTracks[rIdx];
            if (!track) return;
            const scrollAmount = 284; // 264px card width + 20px gap
            if (direction === 'next') {
                if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
                    track.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                }
            } else {
                if (track.scrollLeft <= 10) {
                    track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' });
                } else {
                    track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                }
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

