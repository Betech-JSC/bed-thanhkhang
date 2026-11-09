<template>
    <section v-if="displayItems.length > 0" v-scroll-reveal id="featured-carousel" class="pt-6 md:pt-10 pb-10 md:pb-16">
        <div class="app-container space-y-8 md:space-y-10">
            <!-- Section Header (Figma Node 2261:5504) -->
            <div class="text-center">
                <h2
                    class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] uppercase tracking-tight leading-[46px] font-sans"
                >
                    {{ tt(title) }}
                </h2>
            </div>

            <!-- Carousel Wrapper (Figma Node 2261:5505 - 1116px = 4 cards 264px + 3 gaps 20px) -->
            <div class="relative group">
                <!-- Navigation Prev Button (Left) -->
                <button
                    type="button"
                    @click="scrollPrev"
                    class="absolute -left-3 sm:-left-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-[#111322]/90 border border-white/20 text-white hover:bg-[#2970FF] hover:border-[#2970FF] flex items-center justify-center transition-colors duration-200 active:scale-95 shadow-xl backdrop-blur-sm cursor-pointer"
                    aria-label="Previous Slide"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Smooth GPU Track Viewport (Figma Node 2261:5507: gap 20px, cards 264x419, Fixed Outer Card Frames) -->
                <div
                    ref="viewportRef"
                    class="overflow-hidden w-full py-2"
                    @touchstart.passive="onTouchStart"
                    @touchend.passive="onTouchEnd"
                >
                    <div
                        ref="trackRef"
                        class="flex items-center gap-5 will-change-transform"
                        :style="{
                            transform: `translateX(-${currentOffset}px)`,
                            transition: 'transform 450ms cubic-bezier(0.22, 1, 0.36, 1)'
                        }"
                    >
                        <Link
                            v-for="(item, idx) in displayItems"
                            :key="item.id || idx"
                            :href="item.url || '/san-pham'"
                            class="w-[264px] min-w-[264px] max-w-[264px] h-[419px] flex-shrink-0 rounded-[12px] overflow-hidden flex flex-col justify-between hover:shadow-2xl hover:shadow-primary/20 border border-white/5 cursor-pointer bg-[#182238] select-none group/card block"
                        >
                            <!-- Top Image: Fixed 264x335 Frame -->
                            <div class="w-full h-[335px] relative overflow-hidden bg-[#0A0D14] shrink-0">
                                <img
                                    :src="item.image"
                                    :alt="item.name"
                                    class="w-full h-full object-cover object-center select-none"
                                    loading="eager"
                                    decoding="sync"
                                />
                                <!-- Subtle highlight sheen on hover -->
                                <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none z-10"></div>
                            </div>

                            <!-- Bottom Footer: Locked 264x84px Dark Slate Card (Figma Node 2261:5510: height 84px, bg #30374F) -->
                            <div class="w-full h-[84px] bg-[#30374F] relative overflow-hidden shrink-0">
                                <div class="w-full h-full px-6 py-3 flex flex-col justify-center space-y-1">
                                    <h3 class="text-base font-bold text-[#F5F8FF] truncate leading-[22px]">
                                        {{ tt(item.name) }}
                                    </h3>
                                    <p class="text-xs text-[#B9C0D4] truncate font-normal leading-[17px]">
                                        {{ tt(item.description) }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Navigation Next Button (Right) (Figma Node 2261:5526 / 2261:5527) -->
                <button
                    type="button"
                    @click="scrollNext"
                    class="absolute -right-3 sm:-right-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-[#111322]/90 border border-white/20 text-white hover:bg-[#2970FF] hover:border-[#2970FF] flex items-center justify-center transition-colors duration-200 active:scale-95 shadow-xl backdrop-blur-sm cursor-pointer"
                    aria-label="Next Slide"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';

const tt = (s) => s;

export default {
    name: 'FeaturedProductsCarousel',
    components: {
        Link,
    },
    props: {
        title: {
            type: String,
            default: tt('SẢN PHẨM NỔI BẬT'),
        },
        items: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            currentOffset: 0,
            touchStartX: 0,
        };
    },
    computed: {
        displayItems() {
            if (this.items && Array.isArray(this.items) && this.items.length > 0) {
                return this.items.map((it, idx) => ({
                    id: it.id || `feat-${idx}`,
                    name: it.name || it.title || '',
                    description: it.description || it.short_description || '',
                    image: it.image?.url ? it.image.url : (typeof it.image === 'string' ? it.image : (it.thumbnail?.url || it.thumbnail || '')),
                    url: this.resolveProductUrl(it),
                })).filter(it => it.name);
            }
            return [];
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
        resolveProductUrl(it) {
            if (!it) return '/san-pham';
            const isEn = (this.$page?.props?.locale?.current || '').toLowerCase() === 'en';
            const prefix = isEn ? '/en/products' : '/san-pham';

            if (it.slug) {
                return `${prefix}/${it.slug}`;
            }
            if (typeof it.url === 'string' && it.url) {
                try {
                    const parsed = new URL(it.url, typeof window !== 'undefined' ? window.location.origin : 'http://localhost');
                    return parsed.pathname;
                } catch (e) {
                    return it.url;
                }
            }
            if (it.url && typeof it.url === 'object') {
                const locKey = isEn ? 'EN' : 'VI';
                const matched = it.url[locKey] || Object.values(it.url)[0];
                if (matched && typeof matched === 'string') {
                    try {
                        const parsed = new URL(matched, typeof window !== 'undefined' ? window.location.origin : 'http://localhost');
                        return parsed.pathname;
                    } catch (e) {
                        return matched;
                    }
                }
            }
            if (it.id) {
                return `${prefix}/${it.id}`;
            }
            return prefix;
        },
        preloadImages() {
            if (typeof window === 'undefined') return;
            this.displayItems.forEach((item) => {
                if (item && item.image) {
                    const img = new window.Image();
                    img.src = item.image;
                }
            });
        },
        getMaxOffset() {
            const viewport = this.$refs.viewportRef;
            const totalItems = this.displayItems.length;
            const viewportWidth = viewport ? viewport.clientWidth : 1152;
            return Math.max(0, totalItems * 284 - 20 - viewportWidth);
        },
        handleResize() {
            const maxOffset = this.getMaxOffset();
            if (this.currentOffset > maxOffset) {
                this.currentOffset = maxOffset;
            }
        },
        scrollNext() {
            const step = 284;
            const maxOffset = this.getMaxOffset();
            if (maxOffset <= 0) return;

            if (this.currentOffset >= maxOffset - 5) {
                this.currentOffset = 0;
            } else {
                this.currentOffset = Math.min(maxOffset, this.currentOffset + step);
            }
        },
        scrollPrev() {
            const step = 284;
            const maxOffset = this.getMaxOffset();
            if (maxOffset <= 0) return;

            if (this.currentOffset <= 5) {
                this.currentOffset = maxOffset;
            } else {
                this.currentOffset = Math.max(0, this.currentOffset - step);
            }
        },
        onTouchStart(e) {
            if (e.touches && e.touches.length > 0) {
                this.touchStartX = e.touches[0].clientX;
            }
        },
        onTouchEnd(e) {
            if (!e.changedTouches || e.changedTouches.length === 0) return;
            const deltaX = e.changedTouches[0].clientX - this.touchStartX;
            if (Math.abs(deltaX) > 40) {
                if (deltaX < 0) {
                    this.scrollNext();
                } else {
                    this.scrollPrev();
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
