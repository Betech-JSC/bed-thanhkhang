<template>
    <div class="w-full flex flex-col gap-4">
        <!-- Main Product Image Frame -->
        <div
            class="aspect-[4/3] md:aspect-[560/413] w-full relative overflow-hidden bg-white border border-[#E3E8EF] rounded-[4px] group select-none"
        >
            <!-- Directional Horizontal Slide Track (Zero white-out flash) -->
            <div
                v-if="galleryList.length > 0"
                class="w-full h-full flex transition-transform duration-300 ease-out will-change-transform"
                :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
            >
                <div
                    v-for="(item, idx) in galleryList"
                    :key="`${item.url}-${idx}`"
                    class="w-full h-full shrink-0 overflow-hidden relative flex items-center justify-center bg-white"
                    :class="hasSlideImage(item) ? 'cursor-zoom-in' : ''"
                    @mousemove="idx === currentIndex && hasSlideImage(item) ? handleMouseMove($event) : null"
                    @mouseleave="handleMouseLeave"
                    @click="idx === currentIndex && hasSlideImage(item) ? toggleFullscreen() : null"
                >
                    <img
                        v-if="hasSlideImage(item)"
                        :src="item.url"
                        :alt="item.alt"
                        :style="idx === currentIndex ? zoomStyle : undefined"
                        class="w-full h-full object-contain transition-transform duration-200 select-none will-change-transform"
                        :loading="idx === 0 ? 'eager' : 'lazy'"
                        decoding="async"
                        @error="handleImageError(item.url)"
                    />
                    <div
                        v-else
                        class="w-full h-full bg-[#EEF2F6] flex flex-col items-center justify-center gap-3 p-6 text-center"
                    >
                        <span class="w-10 h-[3px] rounded-[2px] bg-[#CDD5DF]" aria-hidden="true"></span>
                        <span class="text-sm text-[#697586] font-sans">
                            {{ tt('Hình ảnh sản phẩm đang được cập nhật') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Empty Gallery Fallback -->
            <div
                v-else
                class="w-full h-full bg-[#EEF2F6] flex flex-col items-center justify-center gap-3 p-6 text-center"
            >
                <span class="w-12 h-[3px] rounded-[2px] bg-[#CDD5DF]" aria-hidden="true"></span>
                <span class="text-sm text-[#697586] font-sans">
                    {{ tt('Hình ảnh sản phẩm đang được cập nhật') }}
                </span>
            </div>

            <!-- Navigation Chevrons -->
            <button
                v-if="galleryList.length > 1"
                type="button"
                @click.stop="prevImage"
                class="absolute left-3 md:left-6 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-[6px] bg-[#EEF2F6] hover:bg-[#E3E8EF] active:bg-[#CDD5DF] flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                :aria-label="tt('Ảnh trước')"
            >
                <img
                    src="/assets/svg/icons/product-chevron-left.svg"
                    alt=""
                    aria-hidden="true"
                    width="16"
                    height="16"
                    class="w-4 h-4 shrink-0"
                />
            </button>

            <button
                v-if="galleryList.length > 1"
                type="button"
                @click.stop="nextImage"
                class="absolute right-3 md:right-6 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-[6px] bg-[#EEF2F6] hover:bg-[#E3E8EF] active:bg-[#CDD5DF] flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                :aria-label="tt('Ảnh tiếp theo')"
            >
                <img
                    src="/assets/svg/icons/product-chevron-right.svg"
                    alt=""
                    aria-hidden="true"
                    width="16"
                    height="16"
                    class="w-4 h-4 shrink-0"
                />
            </button>
        </div>

        <!-- Horizontal Thumbnail Preview Row Below -->
        <div
            v-if="galleryList.length > 0"
            class="flex items-center gap-3 md:gap-4 overflow-x-auto pb-1 scrollbar-none"
            role="group"
            :aria-label="tt('Ảnh thu nhỏ sản phẩm')"
        >
            <button
                v-for="(item, index) in galleryList"
                :key="`thumb-${item.thumb}-${index}`"
                type="button"
                @click="selectImage(index)"
                :class="[
                    'w-[84px] h-[66px] md:w-[96px] md:h-[76px] shrink-0 rounded-[4px] bg-white overflow-hidden transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]',
                    currentIndex === index
                        ? 'border-2 border-[#005EA4]'
                        : 'border border-[#E3E8EF] hover:border-[#9AA4B2]',
                ]"
                :aria-label="tt('Xem góc ảnh ') + (index + 1)"
                :aria-pressed="currentIndex === index"
            >
                <img
                    v-if="!failedUrls[item.thumb || item.url]"
                    :src="item.thumb || item.url"
                    :alt="item.alt"
                    class="w-full h-full object-contain p-1 select-none"
                    loading="lazy"
                    decoding="async"
                    @error="handleImageError(item.thumb || item.url)"
                />
                <div
                    v-else
                    class="w-full h-full bg-[#EEF2F6] flex items-center justify-center"
                    aria-hidden="true"
                >
                    <span class="w-5 h-[2px] rounded-[2px] bg-[#CDD5DF]"></span>
                </div>
            </button>
        </div>

        <!-- Lightbox Fullscreen Modal -->
        <Teleport to="body">
            <div
                v-if="isFullscreen && hasActiveMainImage"
                class="fixed inset-0 z-[9999] bg-black/95 backdrop-blur-lg flex items-center justify-center p-4 md:p-8"
                role="dialog"
                aria-modal="true"
                :aria-label="currentImage.alt || tt('Ảnh lớn')"
                @click="toggleFullscreen"
            >
                <button
                    type="button"
                    class="absolute top-4 right-4 md:top-6 md:right-6 z-20 w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    @click.stop="toggleFullscreen"
                    :aria-label="tt('Đóng phóng to')"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <button
                    v-if="galleryList.length > 1"
                    type="button"
                    class="absolute left-3 md:left-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    @click.stop="prevImage"
                    :aria-label="tt('Ảnh trước')"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button
                    v-if="galleryList.length > 1"
                    type="button"
                    class="absolute right-3 md:right-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    @click.stop="nextImage"
                    :aria-label="tt('Ảnh tiếp theo')"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div
                    class="w-full max-w-4xl max-h-[85vh] overflow-hidden"
                    @click.stop
                >
                    <div
                        class="w-full flex items-center transition-transform duration-300 ease-out will-change-transform"
                        :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
                    >
                        <div
                            v-for="(item, idx) in galleryList"
                            :key="`lightbox-${item.url}-${idx}`"
                            class="w-full shrink-0 flex items-center justify-center px-2"
                        >
                            <img
                                v-if="hasSlideImage(item)"
                                :src="item.url"
                                :alt="item.alt || tt('Ảnh lớn')"
                                class="max-w-full max-h-[85vh] object-contain rounded-[4px] shadow-2xl select-none"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script>
function normalizeImageUrl(url) {
    if (!url || typeof url !== 'string') return ''
    let trimmed = url.trim()
    if (!trimmed) return ''
    trimmed = trimmed.replace(/\/static\/\/?(assets\/|images\/)/g, '/$1').replace(/\/static\/\//g, '/')
    if (/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\/(assets|images)\//i.test(trimmed)) {
        return trimmed.replace(/^https?:\/\/[^/]+/i, '')
    }
    return trimmed
}

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
            this.isZoomed = false
            this.failedUrls = {}
            this.isFullscreen = false
        },
        isFullscreen(open) {
            if (typeof window === 'undefined') return
            if (open) {
                window.addEventListener('keydown', this.handleKeydown)
            } else {
                window.removeEventListener('keydown', this.handleKeydown)
            }
        },
    },
    beforeUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('keydown', this.handleKeydown)
        }
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
                        const url = normalizeImageUrl(item)
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
                        const url = normalizeImageUrl(String(item.url || item.path || ''))
                        if (!url) return null
                        const thumb = normalizeImageUrl(String(item.thumb || '')) || url
                        const rawAlt =
                            typeof item.alt === 'string' && item.alt.trim()
                                ? item.alt.trim()
                                : typeof item.title === 'string' && item.title.trim()
                                ? item.title.trim()
                                : ''
                        return {
                            url,
                            thumb,
                            alt:
                                rawAlt ||
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
        hasSlideImage(item) {
            const url = item?.url
            return Boolean(url && !this.failedUrls[url])
        },
        handleImageError(url) {
            if (!url) return
            this.failedUrls = { ...this.failedUrls, [url]: true }
        },
        selectImage(index) {
            this.isZoomed = false
            this.currentIndex = index
        },
        prevImage() {
            if (this.galleryList.length === 0) return
            this.isZoomed = false
            this.currentIndex =
                this.currentIndex === 0 ? this.galleryList.length - 1 : this.currentIndex - 1
        },
        nextImage() {
            if (this.galleryList.length === 0) return
            this.isZoomed = false
            this.currentIndex =
                this.currentIndex === this.galleryList.length - 1 ? 0 : this.currentIndex + 1
        },
        handleMouseMove(e) {
            const rect = e.currentTarget.getBoundingClientRect()
            if (!rect.width || !rect.height) return
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
        handleKeydown(event) {
            if (!this.isFullscreen) return
            if (event.key === 'Escape') {
                this.isFullscreen = false
            } else if (event.key === 'ArrowLeft') {
                this.prevImage()
            } else if (event.key === 'ArrowRight') {
                this.nextImage()
            }
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
