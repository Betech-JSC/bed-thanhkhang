<template>
    <section id="featured-carousel" class="pt-6 md:pt-10 pb-10 md:pb-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 space-y-8 md:space-y-10">
            <!-- Section Header (Figma Node 2261:5504) -->
            <div class="text-center">
                <h2
                    class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] uppercase tracking-tight leading-[46px] font-sans"
                >
                    {{ tt(title) }}
                </h2>
            </div>

            <!-- Carousel Wrapper (Figma Node 2261:5505) -->
            <div class="relative group">
                <!-- Navigation Prev Button (Left) -->
                <button
                    v-show="canScrollLeft"
                    type="button"
                    @click="scrollPrev"
                    class="absolute -left-3 sm:-left-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-[#111322]/80 border border-white/20 text-white hover:bg-primary hover:border-primary flex items-center justify-center transition-all duration-200 shadow-xl backdrop-blur-sm"
                    aria-label="Previous Slide"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Scrollable Track (Figma Node 2261:5507: gap 20px, cards 264x419) -->
                <div
                    ref="trackRef"
                    @scroll="checkScroll"
                    class="flex items-center gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1"
                >
                    <div
                        v-for="(item, index) in displayItems"
                        :key="index"
                        class="w-[264px] h-[419px] flex-shrink-0 rounded-[12px] overflow-hidden flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-primary/20 border border-white/5 cursor-pointer bg-[#182238]"
                    >
                        <!-- Top Image: RMIT Certificate Illuminated Frame -->
                        <div class="w-full h-[335px] relative overflow-hidden bg-[#0A0D14] flex items-center justify-center">
                            <img
                                :src="item.image"
                                :alt="item.name"
                                class="w-full h-full object-cover object-center scale-100 hover:scale-105 transition-transform duration-500 ease-out select-none"
                                loading="lazy"
                            />
                            <!-- Subtle highlight sheen on hover -->
                            <div class="absolute inset-0 bg-primary/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        </div>

                        <!-- Bottom Footer: Dark Slate Card (Figma Node 2261:5510: height 84px, bg #30374F) -->
                        <div class="h-[84px] bg-[#30374F] px-6 py-3 flex flex-col justify-center space-y-1">
                            <h3 class="text-base font-bold text-[#F5F8FF] truncate leading-[22px]">
                                {{ tt(item.name) }}
                            </h3>
                            <p class="text-xs text-[#B9C0D4] truncate font-normal leading-[17px]">
                                {{ tt(item.description) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Next Button (Right) (Figma Node 2261:5526 / 2261:5527) -->
                <button
                    type="button"
                    @click="scrollNext"
                    class="absolute -right-3 sm:-right-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-[#111322]/80 border border-white/20 text-white hover:bg-primary hover:border-primary flex items-center justify-center transition-all duration-200 shadow-xl backdrop-blur-sm"
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
export default {
    name: 'FeaturedProductsCarousel',
    props: {
        title: {
            type: String,
            default: 'SẢN PHẨM NỔI BẬT',
        },
        items: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            canScrollLeft: false,
            defaultItems: [
                {
                    name: 'Tên sản phẩm',
                    description: 'This awesome element has more variants and fully',
                    image: '/assets/images/homepage/product-sample-warm.png',
                },
                {
                    name: 'Tên sản phẩm',
                    description: 'This awesome element has more variants and fully',
                    image: '/assets/images/homepage/product-sample-cool.png',
                },
                {
                    name: 'Tên sản phẩm',
                    description: 'This awesome element has more variants and fully',
                    image: '/assets/images/homepage/product-sample-warm.png',
                },
                {
                    name: 'Tên sản phẩm',
                    description: 'This awesome element has more variants and fully',
                    image: '/assets/images/homepage/product-sample-cool.png',
                },
                {
                    name: 'Tên sản phẩm',
                    description: 'This awesome element has more variants and fully',
                    image: '/assets/images/homepage/product-sample-warm.png',
                },
                {
                    name: 'Tên sản phẩm',
                    description: 'This awesome element has more variants and fully',
                    image: '/assets/images/homepage/product-sample-cool.png',
                },
            ],
        };
    },
    computed: {
        displayItems() {
            if (this.items && this.items.length > 0) {
                return this.items.map((it, idx) => ({
                    name: it.name || it.title || 'Tên sản phẩm',
                    description: it.description || 'This awesome element has more variants and fully',
                    image: it.image || it.thumbnail || (idx % 2 === 0 ? '/assets/images/homepage/product-sample-warm.png' : '/assets/images/homepage/product-sample-cool.png'),
                }));
            }
            return this.defaultItems;
        },
    },
    mounted() {
        this.checkScroll();
    },
    methods: {
        scrollNext() {
            const track = this.$refs.trackRef;
            if (!track) return;
            const scrollAmount = 284; // 264px card + 20px gap
            if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
                // Loop back to start
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
            setTimeout(this.checkScroll, 350);
        },
        scrollPrev() {
            const track = this.$refs.trackRef;
            if (!track) return;
            const scrollAmount = 284;
            track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            setTimeout(this.checkScroll, 350);
        },
        checkScroll() {
            const track = this.$refs.trackRef;
            if (!track) return;
            this.canScrollLeft = track.scrollLeft > 10;
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