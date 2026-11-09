<template>
    <section v-if="displayCategories.length > 0" v-scroll-reveal id="products-section" class="py-12 md:py-20 bg-dark-bg">
        <div class="app-container">
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
                    <div class="relative group">
                        <div
                            :ref="el => { if (el) rowViewports[rIdx] = el }"
                            class="overflow-hidden w-full py-2"
                            @touchstart.passive="e => onRowTouchStart(rIdx, e)"
                            @touchend.passive="e => onRowTouchEnd(rIdx, e)"
                        >
                            <div
                                :ref="el => { if (el) rowTracks[rIdx] = el }"
                                class="flex items-center gap-5 will-change-transform"
                                :style="{
                                    transform: `translateX(-${rowOffsets[rIdx] || 0}px)`,
                                    transition: 'transform 450ms cubic-bezier(0.22, 1, 0.36, 1)'
                                }"
                            >
                                <Link
                                    v-for="(item, idx) in category.items"
                                    :key="item.id || idx"
                                    :href="item.url || '/san-pham'"
                                    class="w-[264px] min-w-[264px] max-w-[264px] h-[419px] flex-shrink-0 rounded-[12px] overflow-hidden flex flex-col justify-between hover:shadow-2xl hover:shadow-primary/20 border border-white/5 cursor-pointer bg-[#182238] select-none group/card block"
                                >
                                    <!-- Top: Fixed 264x335 Product Image Frame -->
                                    <div class="w-full h-[335px] relative overflow-hidden bg-[#D1D2D2] shrink-0">
                                        <!-- Best Choice Badge (Figma Node 2261:5598: 97x25px, bg #00359E, rounded 4px) -->
                                        <div
                                            v-if="idx === 0 && category.hasBestChoice"
                                            class="absolute top-3 left-3 z-20 w-[97px] h-[25px] bg-[#00359E] text-[#F5F8FF] text-[12px] font-medium leading-[17px] rounded-[4px] shadow-md flex items-center justify-center select-none pointer-events-none"
                                        >
                                            {{ tt('Best choice') }}
                                        </div>

                                        <img
                                            :src="item.image"
                                            :alt="item.title"
                                            class="w-full h-full object-cover object-center select-none"
                                            loading="eager"
                                            decoding="sync"
                                        />

                                        <!-- Subtle Sheen Overlay on Hover -->
                                        <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none z-10"></div>
                                    </div>

                                    <!-- Bottom: Locked 264x84px Dark Slate Card Footer (Figma Node 2261:5600: height 84px, bg #30374F) -->
                                    <div class="w-full h-[84px] bg-[#30374F] relative overflow-hidden shrink-0">
                                        <div class="w-full h-full px-6 py-3 flex flex-col justify-center space-y-1">
                                            <h4 class="text-base font-bold text-[#F5F8FF] truncate font-sans leading-[22px]">
                                                {{ tt(item.title) }}
                                            </h4>
                                            <p class="text-xs text-[#B9C0D4] truncate font-normal leading-[17px]">
                                                {{ tt(item.description) }}
                                            </p>
                                        </div>
                                    </div>
                                </Link>
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
import { Link } from '@inertiajs/inertia-vue3';

const tt = (s) => s;

export default {
    name: 'ProductsSection',
    components: {
        Link,
    },
    props: {
        title: {
            type: String,
            default: tt('SẢN PHẨM NỔI BẬT'),
        },
        categories: {
            type: Array,
            default: () => [],
        },
        products: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            rowTracks: {},
            rowViewports: {},
            rowOffsets: { 0: 0, 1: 0, 2: 0 },
            rowTouchStartX: {},
            categoryRows: [
                {
                    id: 'keychains',
                    name: tt('Móc khoá'),
                    keywords: ['khoa', 'keychain', 'moc', 'moc-khoa', 'phu-kien'],
                    hasBestChoice: true,
                    url: '/san-pham?category=moc-khoa',
                },
                {
                    id: 'office',
                    name: tt('Đồ dùng/ Đồ trang trí văn phòng'),
                    keywords: ['decor', 'van-phong', 'office', 'trang-tri', 'do-dung', 'khong-gian'],
                    hasBestChoice: true,
                    url: '/san-pham?category=van-phong',
                },
                {
                    id: 'custom',
                    name: tt('Gia công theo yêu cầu'),
                    keywords: ['custom', 'gia-cong', 'yeu-cau'],
                    hasBestChoice: false,
                    url: '/san-pham?category=gia-cong',
                },
            ],
        };
    },
    computed: {
        displayCategories() {
            let baseCategories = [];
            if (this.categories && this.categories.length > 0) {
                baseCategories = this.categories.filter(c => c.slug !== 'all' && c.id !== 'all').map(c => ({
                    id: c.slug || c.id,
                    name: this.tt(c.name || c.title),
                    url: `/san-pham?category=${c.slug || c.id}`,
                    hasBestChoice: c.hasBestChoice ?? true,
                    keywords: [String(c.slug || c.id).toLowerCase()],
                    items: c.items || [],
                }));
            } else {
                baseCategories = this.categoryRows;
            }

            return baseCategories.map((cat, rIdx) => {
                let items = [];

                // 1. Ưu tiên lấy sản phẩm đã load sẵn theo quan hệ danh mục từ Backend
                if (cat.items && Array.isArray(cat.items) && cat.items.length > 0) {
                    items = cat.items.map((p, idx) => ({
                        id: p.id || `p-${rIdx}-${idx}`,
                        title: p.title || p.name || '',
                        description: p.description || p.short_description || '',
                        image: p.thumbnail?.url || p.image?.url || (typeof p.image === 'string' ? p.image : (p.thumbnail || '')),
                        isBestChoice: idx === 0 && cat.hasBestChoice,
                        url: this.resolveProductUrl(p),
                    }));
                } else if (this.products && this.products.length > 0) {
                    // 2. Fallback lọc từ this.products nếu Backend không truyền items trong category
                    const catId = String(cat.id).toLowerCase();
                    const matched = this.products.filter(p => {
                        if (!p.categories || !Array.isArray(p.categories) || p.categories.length === 0) {
                            const pCatId = String(p.category_id || p.categoryId || '').toLowerCase();
                            const catSlug = String(p.category?.slug || p.category_slug || (typeof p.category === 'string' ? p.category : '')).toLowerCase();
                            const catName = String(p.category?.name || p.category?.title || '').toLowerCase();
                            if (pCatId === catId || catSlug === catId) return true;
                            if (cat.keywords && cat.keywords.some(k => catSlug.includes(k) || catName.includes(k))) return true;
                            return false;
                        }

                        return p.categories.some(c => {
                            const cId = String(c.id).toLowerCase();
                            const cSlug = String(c.slug || '').toLowerCase();
                            const cTitle = String(c.title || c.name || '').toLowerCase();
                            if (cId === catId || cSlug === catId) return true;
                            if (cat.keywords && cat.keywords.some(k => cSlug.includes(k) || cTitle.includes(k))) return true;
                            return false;
                        });
                    });

                    items = matched.map((p, idx) => ({
                        id: p.id || `p-${rIdx}-${idx}`,
                        title: p.title || p.name || '',
                        description: p.description || p.short_description || '',
                        image: p.thumbnail?.url || p.image?.url || (typeof p.image === 'string' ? p.image : (p.thumbnail || '')),
                        isBestChoice: idx === 0 && cat.hasBestChoice,
                        url: this.resolveProductUrl(p),
                    }));
                }

                return {
                    ...cat,
                    items,
                };
            }).filter(cat => cat.items.length > 0);
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
        tt(s) {
            return this.$root?.tt ? this.$root.tt(s) : tt(s);
        },
        resolveProductUrl(p) {
            if (!p) return '/san-pham';
            const isEn = (this.$page?.props?.locale?.current || '').toLowerCase() === 'en';
            const prefix = isEn ? '/en/products' : '/san-pham';

            if (p.slug) {
                return `${prefix}/${p.slug}`;
            }
            if (typeof p.url === 'string' && p.url) {
                try {
                    const parsed = new URL(p.url, typeof window !== 'undefined' ? window.location.origin : 'http://localhost');
                    return parsed.pathname;
                } catch (e) {
                    return p.url;
                }
            }
            if (p.url && typeof p.url === 'object') {
                const locKey = isEn ? 'EN' : 'VI';
                const matched = p.url[locKey] || Object.values(p.url)[0];
                if (matched && typeof matched === 'string') {
                    try {
                        const parsed = new URL(matched, typeof window !== 'undefined' ? window.location.origin : 'http://localhost');
                        return parsed.pathname;
                    } catch (e) {
                        return matched;
                    }
                }
            }
            if (p.id) {
                return `${prefix}/${p.id}`;
            }
            return prefix;
        },
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
        getRowMaxOffset(rIdx) {
            const viewport = this.rowViewports[rIdx];
            const cat = this.displayCategories[rIdx];
            const totalItems = cat && cat.items ? cat.items.length : 0;
            const viewportWidth = viewport ? viewport.clientWidth : 1152;
            return Math.max(0, totalItems * 284 - 20 - viewportWidth);
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
            const step = 284;
            const maxOffset = this.getRowMaxOffset(rIdx);
            if (maxOffset <= 0) return;

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
