<template>
    <section v-scroll-reveal class="w-full pt-4 pb-8 flex flex-col gap-6">
        <!-- TIÊU ĐỀ BỘ SƯU TẬP + ĐIỀU HƯỚNG (FIGMA 2358:3095: 1152x29 px) -->
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-[24px] font-bold leading-[29px] text-[#FAFAFA] font-sans">
                {{ tt('Sản phẩm khác') }}
            </h2>

            <!-- Navigation Arrows -->
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    @click="scrollPrev"
                    class="w-8 h-8 rounded-full border border-white/20 text-white/80 hover:text-white hover:border-[#2970FF] hover:bg-[#2970FF]/20 flex items-center justify-center transition-colors duration-200 cursor-pointer shadow-sm active:scale-95"
                    aria-label="Previous Slide"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="scrollNext"
                    class="w-8 h-8 rounded-full border border-white/20 text-white/80 hover:text-white hover:border-[#2970FF] hover:bg-[#2970FF]/20 flex items-center justify-center transition-colors duration-200 cursor-pointer shadow-sm active:scale-95"
                    aria-label="Next Slide"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- DANH SÁCH GỢI Ý (FIGMA 2358:3100: 1152x419 px, 4 cards 273x419 px, gap 20px) -->
        <div class="relative group w-full max-w-[1152px] mx-auto">
            <!-- Synchronized Left Prev Button -->
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

            <div
                ref="viewportRef"
                class="overflow-hidden w-full py-1"
                @touchstart.passive="onTouchStart"
                @touchend.passive="onTouchEnd"
            >
                <div
                    ref="trackRef"
                    class="flex items-center gap-5 w-full"
                >
                    <Link
                        v-for="(slotItem, slotIndex) in visibleProducts"
                        :key="slotIndex"
                        :href="slotItem.slug ? route('products.show', { slug: slotItem.slug }) : route('products.index')"
                        class="group/card w-[273px] min-w-[273px] max-w-[273px] h-[419px] flex-shrink-0 rounded-[12px] overflow-hidden bg-[#D1D2D2] flex flex-col select-none"
                    >
                        <!-- Hình ảnh sản phẩm cố định khung 273x352 (Figma image 15) + Inner Horizontal Slide Transition -->
                        <div class="w-full h-[352px] bg-[#D1D2D2] relative overflow-hidden shrink-0">
                            <Transition
                                enter-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform"
                                :enter-from-class="slideDirection === 'next' ? 'translate-x-full' : '-translate-x-full'"
                                enter-to-class="translate-x-0"
                                leave-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform absolute inset-0 pointer-events-none"
                                leave-from-class="translate-x-0"
                                :leave-to-class="slideDirection === 'next' ? '-translate-x-full' : 'translate-x-full'"
                            >
                                <div
                                    :key="`${currentIndex}-${slotIndex}`"
                                    class="w-full h-full relative overflow-hidden"
                                >
                                    <img
                                        :src="slotItem.image"
                                        :alt="slotItem.title"
                                        class="w-full h-full object-cover select-none"
                                        loading="eager"
                                        decoding="sync"
                                    />
                                </div>
                            </Transition>
                        </div>

                        <!-- Frame 263: Thông tin sản phẩm cố định 273x67 px (Figma I2358:3101;2283:6515) -->
                        <div class="w-full h-[67px] bg-[#30374F] relative overflow-hidden shrink-0">
                            <Transition
                                enter-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform"
                                :enter-from-class="slideDirection === 'next' ? 'translate-x-full' : '-translate-x-full'"
                                enter-to-class="translate-x-0"
                                leave-active-class="transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform absolute inset-0 pointer-events-none"
                                leave-from-class="translate-x-0"
                                :leave-to-class="slideDirection === 'next' ? '-translate-x-full' : 'translate-x-full'"
                            >
                                <div
                                    :key="`${currentIndex}-${slotIndex}`"
                                    class="w-full h-full px-6 py-3 flex flex-col justify-center gap-1"
                                >
                                    <h3 class="text-base font-bold leading-[22px] text-[#F5F8FF] truncate">
                                        {{ tt(slotItem.title) }}
                                    </h3>
                                    <p class="text-xs font-normal leading-[17px] text-[#B9C0D4] truncate">
                                        {{ tt(slotItem.description) }}
                                    </p>
                                </div>
                            </Transition>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Synchronized Right Next Button -->
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
export default {
    name: 'RelatedProducts',
    props: {
        products: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            currentIndex: 0,
            currentOffset: 0,
            slideDirection: 'next',
            touchStartX: 0,
        }
    },
    computed: {
        isEn() {
            return (this.$page?.props?.locale?.current || 'vi') === 'en'
        },
        baseItems() {
            const defaultSubtitles = [
                'Mô hình trưng bày · In theo yêu cầu',
                'Trang trí góc làm việc · Tuỳ chọn màu',
                'Mô hình sưu tầm · In theo yêu cầu',
                'Mô hình trang trí · Tuỳ chọn màu',
            ]
            if (this.products && this.products.length > 0) {
                return this.products.map((p, idx) => ({
                    id: p.id || idx,
                    title: p.title || p.name || (this.isEn ? '3D Printed Product' : this.tt('Sản phẩm in 3D')),
                    slug: p.slug || '',
                    image: p.image?.url || p.thumbnail || p.cover || `/images/pdp/related_${(idx % 4) + 1}.png`,
                    description: p.subtitle || p.short_description || p.description || p.excerpt || defaultSubtitles[idx % 4],
                }))
            }

            if (this.isEn) {
                return [
                    {
                        id: 1,
                        title: 'Astronaut',
                        slug: 'mo-hinh-phi-hanh-gia',
                        image: '/images/pdp/related_1.png',
                        description: 'Display Model · Custom 3D Print',
                    },
                    {
                        id: 2,
                        title: 'Geometric Fox',
                        slug: 'chau-cay-da-giac-mini',
                        image: '/images/pdp/related_2.png',
                        description: 'Desk Decor · Custom Colors',
                    },
                    {
                        id: 3,
                        title: 'Mini Robot',
                        slug: 'de-do-dien-thoai-mecha',
                        image: '/images/pdp/related_3.png',
                        description: 'Collectible Model · Custom 3D Print',
                    },
                    {
                        id: 4,
                        title: 'Geometric Elephant',
                        slug: 'robot-khop-cu-dong',
                        image: '/images/pdp/related_4.png',
                        description: 'Decor Model · Custom Colors',
                    },
                ]
            }

            // Fallback to Figma related cards (2358:3100)
            return [
                {
                    id: 1,
                    title: this.tt('Phi hành gia'),
                    slug: 'mo-hinh-phi-hanh-gia',
                    image: '/images/pdp/related_1.png',
                    description: this.tt('Mô hình trưng bày · In theo yêu cầu'),
                },
                {
                    id: 2,
                    title: this.tt('Cáo hình học'),
                    slug: 'chau-cay-da-giac-mini',
                    image: '/images/pdp/related_2.png',
                    description: this.tt('Trang trí góc làm việc · Tuỳ chọn màu'),
                },
                {
                    id: 3,
                    title: this.tt('Rô-bốt mini'),
                    slug: 'de-do-dien-thoai-mecha',
                    image: '/images/pdp/related_3.png',
                    description: this.tt('Mô hình sưu tầm · In theo yêu cầu'),
                },
                {
                    id: 4,
                    title: this.tt('Voi hình học'),
                    slug: 'robot-khop-cu-dong',
                    image: '/images/pdp/related_4.png',
                    description: this.tt('Mô hình trang trí · Tuỳ chọn màu'),
                },
            ]
        },
        productList() {
            const list = this.baseItems
            if (list.length > 0 && list.length < 6) {
                return [...list, ...list.slice(0, 6 - list.length)]
            }
            return list
        },
        visibleProducts() {
            const list = this.productList
            if (!list || list.length === 0) return []
            const total = list.length
            const cards = []
            for (let i = 0; i < 4; i++) {
                const itemIndex = (this.currentIndex + i) % total
                cards.push({
                    ...list[itemIndex],
                    slotIndex: i,
                    itemIndex,
                })
            }
            return cards
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
        preloadImages() {
            if (typeof window === 'undefined') return
            this.productList.forEach(item => {
                if (item && item.image) {
                    const img = new window.Image()
                    img.src = item.image
                }
            })
        },
        getMaxOffset() {
            const viewport = this.$refs.viewportRef
            const totalItems = this.productList.length
            const trackWidth = totalItems > 0 ? totalItems * 273 + (totalItems - 1) * 20 : 0
            const viewportWidth = viewport ? viewport.clientWidth : 1152
            return Math.max(0, trackWidth - viewportWidth)
        },
        handleResize() {
            const maxOffset = this.getMaxOffset()
            if (this.currentOffset > maxOffset) {
                this.currentOffset = maxOffset
            }
        },
        scrollNext() {
            this.slideDirection = 'next'
            const total = this.productList.length || 1
            this.currentIndex = (this.currentIndex + 1) % total
            const step = 293 // 273px card + 20px gap
            const maxOffset = this.getMaxOffset()
            if (maxOffset <= 0) {
                const track = this.$refs.trackRef
                if (track && track.scrollBy) {
                    track.scrollBy({ left: step, behavior: 'smooth' })
                }
                return
            }
            if (this.currentOffset >= maxOffset - 5) {
                this.currentOffset = 0
            } else {
                this.currentOffset = Math.min(maxOffset, this.currentOffset + step)
            }
        },
        scrollPrev() {
            this.slideDirection = 'prev'
            const total = this.productList.length || 1
            this.currentIndex = (this.currentIndex - 1 + total) % total
            const step = 293
            const maxOffset = this.getMaxOffset()
            if (maxOffset <= 0) {
                const track = this.$refs.trackRef
                if (track && track.scrollBy) {
                    track.scrollBy({ left: -step, behavior: 'smooth' })
                }
                return
            }
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
