<template>
    <div class="w-full flex flex-col-reverse md:flex-row gap-5 items-start">
        <!-- Thumbnail Column (Figma 2390:5337, Desktop: vertical 140px, Mobile: horizontal) -->
        <div
            v-if="galleryList.length > 0"
            class="flex md:flex-col gap-3 md:gap-5 overflow-x-auto md:overflow-y-auto w-full md:w-[120px] lg:w-[140px] shrink-0 pb-2 md:pb-0 scrollbar-none"
        >
            <button
                v-for="(item, index) in galleryList"
                :key="index"
                type="button"
                @click="selectImage(index)"
                :class="[
                    'relative w-20 h-24 md:w-full md:h-[135px] lg:h-[187px] rounded-lg overflow-hidden border-2 transition-all duration-200 shrink-0 group focus:outline-none',
                    currentIndex === index
                        ? 'border-[#2970FF] ring-2 ring-[#2970FF]/30 shadow-lg shadow-blue-500/20'
                        : 'border-[#30374F] hover:border-slate-500 opacity-70 hover:opacity-100',
                ]"
                :aria-label="tt('Xem góc ảnh ') + (index + 1)"
            >
                <img
                    v-if="!failedUrls[item.thumb || item.url]"
                    :src="item.thumb || item.url"
                    :alt="item.alt ? tt(item.alt) : (tt('Góc nhìn ') + (index + 1))"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    loading="lazy"
                    @error="handleImageError(item.thumb || item.url)"
                />
                <div
                    v-else
                    class="w-full h-full bg-[#1C2238] flex items-center justify-center"
                    aria-hidden="true"
                >
                    <span class="w-6 h-[2px] rounded bg-[#404968]"></span>
                </div>
                <span
                    v-if="currentIndex === index"
                    class="absolute inset-0 bg-blue-500/10 pointer-events-none"
                ></span>
            </button>
        </div>

        <!-- Main Product Image Frame (Figma 2390:5344: 464x600 px, rounded-[12px], bg-[#D1D2D2]) -->
        <div
            :class="[
                'relative flex-1 w-full h-[360px] sm:h-[460px] md:h-[520px] lg:h-[600px] rounded-[12px] overflow-hidden group',
                galleryList.length > 0 ? 'lg:w-[464px]' : '',
                hasActiveMainImage ? 'bg-[#D1D2D2]' : 'bg-[#1C2238] border border-[#30374F]',
            ]"
        >
            <!-- Image Container with Zoom effect on hover -->
            <div
                v-if="hasActiveMainImage"
                class="relative w-full h-full flex items-center justify-center overflow-hidden cursor-zoom-in"
                @mousemove="handleMouseMove"
                @mouseleave="handleMouseLeave"
                @click="toggleFullscreen"
            >
                <img
                    :src="currentImage.url"
                    :alt="currentImage.alt ? tt(currentImage.alt) : tt('Ảnh sản phẩm chi tiết')"
                    :style="zoomStyle"
                    class="w-full h-full object-cover transition-transform duration-200 select-none will-change-transform"
                    @error="handleImageError(currentImage.url)"
                />
            </div>
            <!-- Neutral empty state frame when product has no images -->
            <div
                v-else
                class="w-full h-full flex flex-col items-center justify-center gap-3 p-6 text-center"
            >
                <span class="w-12 h-[3px] rounded-[2px] bg-[#404968]" aria-hidden="true"></span>
                <span class="text-sm text-[#9AA4B2] font-sans">
                    {{ tt('Hình ảnh sản phẩm đang được cập nhật') }}
                </span>
            </div>

            <!-- Navigation Chevrons on Mobile -->
            <button
                v-if="galleryList.length > 1"
                type="button"
                @click.stop="prevImage"
                class="md:hidden absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/60 text-white flex items-center justify-center backdrop-blur-md"
                :aria-label="tt('Ảnh trước')"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button
                v-if="galleryList.length > 1"
                type="button"
                @click.stop="nextImage"
                class="md:hidden absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/60 text-white flex items-center justify-center backdrop-blur-md"
                :aria-label="tt('Ảnh tiếp theo')"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Lightbox Fullscreen Modal -->
        <Teleport to="body">
            <div
                v-if="isFullscreen && hasActiveMainImage"
                class="fixed inset-0 z-50 bg-black/95 backdrop-blur-lg flex items-center justify-center p-4"
                @click="toggleFullscreen"
            >
                <button
                    type="button"
                    class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                    @click.stop="toggleFullscreen"
                    :aria-label="tt('Đóng phóng to')"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="max-w-4xl max-h-[85vh] flex items-center justify-center" @click.stop>
                    <img
                        :src="currentImage.url"
                        :alt="currentImage.alt ? tt(currentImage.alt) : tt('Ảnh lớn')"
                        class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl"
                    />
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script>
export default {
    name: 'ProductGallery',
    props: {
        images: {
            type: Array,
            default: () => [],
        },
        productTitle: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            currentIndex: 0,
            isZoomed: false,
            zoomOriginX: 50,
            zoomOriginY: 50,
            isFullscreen: false,
            failedUrls: {},
        }
    },
    watch: {
        images() {
            this.currentIndex = 0
            this.failedUrls = {}
            this.isFullscreen = false
        },
    },
    computed: {
        galleryList() {
            if (!Array.isArray(this.images) || this.images.length === 0) {
                return []
            }

            const title = this.productTitle ? String(this.productTitle).trim() : ''
            return this.images
                .map((item, idx) => {
                    if (typeof item === 'string') {
                        const url = item.trim()
                        if (!url) return null
                        return {
                            url,
                            thumb: url,
                            alt: title
                                ? `${title} - ${this.tt('Góc nhìn ')}${idx + 1}`
                                : `${this.tt('Góc nhìn ')}${idx + 1}`,
                        }
                    }
                    if (item && typeof item === 'object') {
                        const url = String(item.url || item.path || '').trim()
                        if (!url) return null
                        const thumb = String(item.thumb || url).trim()
                        return {
                            url,
                            thumb,
                            alt:
                                item.alt ||
                                item.title ||
                                (title
                                    ? `${title} - ${this.tt('Góc nhìn ')}${idx + 1}`
                                    : `${this.tt('Góc nhìn ')}${idx + 1}`),
                            tag: item.tag || null,
                        }
                    }
                    return null
                })
                .filter(Boolean)
        },
        currentImage() {
            return this.galleryList[this.currentIndex] || this.galleryList[0] || {}
        },
        hasActiveMainImage() {
            const url = this.currentImage?.url
            return Boolean(url && !this.failedUrls[url])
        },
        zoomStyle() {
            if (!this.isZoomed) {
                return {
                    transform: 'scale(1)',
                    transformOrigin: 'center center',
                }
            }
            return {
                transform: 'scale(1.45)',
                transformOrigin: `${this.zoomOriginX}% ${this.zoomOriginY}%`,
            }
        },
    },
    methods: {
        handleImageError(url) {
            if (!url) return
            this.failedUrls = { ...this.failedUrls, [url]: true }
        },
        selectImage(index) {
            this.currentIndex = index
        },
        prevImage() {
            if (this.galleryList.length === 0) return
            this.currentIndex =
                this.currentIndex === 0 ? this.galleryList.length - 1 : this.currentIndex - 1
        },
        nextImage() {
            if (this.galleryList.length === 0) return
            this.currentIndex =
                this.currentIndex === this.galleryList.length - 1 ? 0 : this.currentIndex + 1
        },
        handleMouseMove(e) {
            const rect = e.currentTarget.getBoundingClientRect()
            const x = ((e.clientX - rect.left) / rect.width) * 100
            const y = ((e.clientY - rect.top) / rect.height) * 100
            this.zoomOriginX = Math.max(0, Math.min(100, x))
            this.zoomOriginY = Math.max(0, Math.min(100, y))
            this.isZoomed = true
        },
        handleMouseLeave() {
            this.isZoomed = false
        },
        toggleFullscreen() {
            if (!this.hasActiveMainImage) return
            this.isFullscreen = !this.isFullscreen
        },
    },
}
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
