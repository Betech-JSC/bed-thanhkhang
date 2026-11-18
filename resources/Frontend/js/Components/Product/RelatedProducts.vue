<template>
    <section v-if="productList.length > 0" v-scroll-reveal class="w-full pt-4 pb-8 flex flex-col gap-6">
        <!-- TIÊU ĐỀ BỘ SƯU TẬP (FIGMA 2358:3095: 1152x29 px) -->
        <div>
            <h2 class="text-[24px] font-bold leading-[29px] text-[#FAFAFA] font-sans">
                {{ tt('Sản phẩm khác') }}
            </h2>
        </div>

        <!-- DANH SÁCH GỢI Ý (FIGMA 2358:3100: 1152x419 px, cards 273x419 px, gap 20px) -->
        <div class="relative group w-full max-w-[1152px] mx-auto">
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

            <!-- Smooth GPU Track Viewport -->
            <div
                ref="viewportRef"
                class="overflow-hidden w-full py-1"
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
                        v-for="(item, idx) in productList"
                        :key="item.id || idx"
                        :href="getProductUrl(item)"
                        class="group/card w-[273px] min-w-[273px] max-w-[273px] h-[419px] flex-shrink-0 rounded-[12px] overflow-hidden bg-[#D1D2D2] flex flex-col select-none block hover:shadow-2xl hover:shadow-primary/20 border border-white/5 cursor-pointer"
                    >
                        <!-- Hình ảnh sản phẩm cố định khung 273x352 (Figma image 15) -->
                        <div class="w-full h-[352px] bg-[#D1D2D2] relative overflow-hidden shrink-0">
                            <img
                                :src="item.image"
                                :alt="item.title"
                                width="273"
                                height="352"
                                class="w-full h-full object-cover select-none"
                                loading="lazy"
                                decoding="async"
                                @error="handleImageError($event, item)"
                            />
                            <!-- Subtle highlight sheen on hover -->
                            <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none z-10"></div>
                        </div>

                        <!-- Frame 263: Thông tin sản phẩm cố định 273x67 px (Figma I2358:3101;2283:6515) -->
                        <div class="w-full h-[67px] bg-[#30374F] relative overflow-hidden shrink-0">
                            <div class="w-full h-full px-6 py-3 flex flex-col justify-center gap-1">
                                <h3 class="text-base font-bold leading-[22px] text-[#F5F8FF] truncate">
                                    {{ tt(item.title) }}
                                </h3>
                                <p class="text-xs font-normal leading-[17px] text-[#B9C0D4] truncate">
                                    {{ tt(item.description) }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Navigation Next Button (Right) -->
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
    </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
    name: 'RelatedProducts',
    components: {
        Link,
    },
    props: {
        products: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            currentOffset: 0,
            touchStartX: 0,
        }
    },
    computed: {
        isEn() {
            return (this.$page?.props?.locale?.current || 'vi') === 'en'
        },
        productList() {
            if (this.products && Array.isArray(this.products) && this.products.length > 0) {
                return this.products.map((p, idx) => ({
                    id: p.id || idx,
                    title: p.title || p.name || '',
                    slug: p.slug || '',
                    image: p.image?.url || (typeof p.image === 'string' ? p.image : (p.thumbnail || '')),
                    description: p.subtitle || p.short_description || p.description || p.excerpt || '',
                })).filter(p => p.title);
            }
            return [];
        },
    },
    mounted() {
        this.preloadImages()
        if (typeof window !== 'undefined') {
            window.addEventListener('resize', this.handleResize, { passive: true })
        }
    },
    beforeUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('resize', this.handleResize)
        }
    },
    methods: {
        tt(s) {
            return typeof this.$root?.tt === 'function' ? this.$root.tt(s) : s
        },
        getProductUrl(item) {
            if (!item) return '/san-pham'
            const isEn = (this.$page?.props?.locale?.current || '').toLowerCase() === 'en'
            const prefix = isEn ? '/en/products' : '/san-pham'

            if (item.slug) {
                return `${prefix}/${item.slug}`
            }
            if (typeof item.url === 'string' && item.url) {
                try {
                    const parsed = new URL(item.url, typeof window !== 'undefined' ? window.location.origin : 'http://localhost')
                    return parsed.pathname
                } catch (e) {
                    return item.url
                }
            }
            if (item.url && typeof item.url === 'object') {
                const locKey = isEn ? 'EN' : 'VI'
                const matched = item.url[locKey] || Object.values(item.url)[0]
                if (matched && typeof matched === 'string') {
                    try {
                        const parsed = new URL(matched, typeof window !== 'undefined' ? window.location.origin : 'http://localhost')
                        return parsed.pathname
                    } catch (e) {
                        return matched
                    }
                }
            }
            if (item.id) {
                return `${prefix}/${item.id}`
            }
            return prefix
        },
        preloadImages() {
            if (typeof window === 'undefined') return
            this.productList.forEach((item) => {
                if (item && item.image) {
                    const img = new window.Image()
                    img.src = item.image
                }
            })
        },
        handleImageError(event, item) {
            if (!event || !event.target) return
            const fallback = '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp'
            if (event.target.src !== fallback && !event.target.src.endsWith(fallback)) {
                event.target.src = fallback
            }
        },
        getMaxOffset() {
            const viewport = this.$refs.viewportRef
            const totalItems = this.productList.length
            const viewportWidth = viewport ? viewport.clientWidth : 1152
            // 273px card width + 20px gap, minus last gap
            return Math.max(0, totalItems * 293 - 20 - viewportWidth)
        },
        handleResize() {
            const maxOffset = this.getMaxOffset()
            if (this.currentOffset > maxOffset) {
                this.currentOffset = maxOffset
            }
        },
        scrollNext() {
            const step = 293 // 273px card + 20px gap
            const maxOffset = this.getMaxOffset()
            if (maxOffset <= 0) return

            if (this.currentOffset >= maxOffset - 5) {
                this.currentOffset = 0
            } else {
                this.currentOffset = Math.min(maxOffset, this.currentOffset + step)
            }
        },
        scrollPrev() {
            const step = 293 // 273px card + 20px gap
            const maxOffset = this.getMaxOffset()
            if (maxOffset <= 0) return

            if (this.currentOffset <= 5) {
                this.currentOffset = maxOffset
            } else {
                this.currentOffset = Math.max(0, this.currentOffset - step)
            }
        },
        onTouchStart(e) {
            if (e.touches && e.touches.length > 0) {
                this.touchStartX = e.touches[0].clientX
            }
        },
        onTouchEnd(e) {
            if (!e.changedTouches || e.changedTouches.length === 0) return
            const deltaX = e.changedTouches[0].clientX - this.touchStartX
            if (Math.abs(deltaX) > 40) {
                if (deltaX < 0) {
                    this.scrollNext()
                } else {
                    this.scrollPrev()
                }
            }
        },
    },
}
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