<template>
    <section
        v-if="validCategories.length > 0 || hasCtaBanner"
        id="product-categories-section"
        class="bg-[#00284B] py-8 md:py-16 lg:pt-10 lg:pb-20"
    >
        <div class="app-container flex flex-col gap-6 md:gap-8">
            <!-- Section Header -->
            <div v-if="sectionHeading" class="flex items-center gap-[10px]">
                <span class="w-3 h-3 rounded-[2px] bg-[#FAFAFA] shrink-0" aria-hidden="true" />
                <h2 class="font-bold text-[16px] md:text-[18px] leading-[1.6] text-[#FAFAFA]">
                    {{ sectionHeading }}
                </h2>
            </div>

            <!-- Mobile Horizontal Swipeable Slider (< md: 2 cards visible + peek) / Tablet & Desktop Grid (md: 2-col, lg: 3-col exact Figma 595:15380) -->
            <div
                ref="sliderTrack"
                class="categories-slider-track flex md:grid md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6 overflow-x-auto md:overflow-visible -mx-4 px-4 md:mx-0 md:px-0 pb-2 md:pb-0 scroll-px-4"
                :class="[
                    isMouseDragging
                        ? 'cursor-grabbing select-none snap-none'
                        : 'snap-x snap-mandatory md:snap-none scroll-smooth cursor-grab md:cursor-auto',
                ]"
                @scroll.passive="onTrackScroll"
                @mousedown="onMouseDown"
                @click.capture="onTrackClickCapture"
                @dragstart="onDragStart"
            >
                <!-- Standard CMS Category Tiles -->
                <Link
                    v-for="cat in validCategories"
                    :key="cat.id || cat.slug"
                    :href="cat.url || productsIndexUrl"
                    draggable="false"
                    class="group shrink-0 snap-start w-[calc((100%-12px)/2.12)] md:w-auto flex flex-col bg-[#FAFAFA] rounded-[4px] overflow-hidden transition-transform duration-200 hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9E0FF]"
                >
                    <div class="relative w-full aspect-[368/308] bg-[#EEF2F6] overflow-hidden">
                        <img
                            v-if="cat.image?.url"
                            :src="cat.image.url"
                            :alt="cat.image.alt || cat.title || cat.name"
                            draggable="false"
                            class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                            loading="lazy"
                            decoding="async"
                            width="368"
                            height="308"
                        />
                    </div>

                    <div class="flex items-center justify-between gap-2 px-2.5 py-2.5 min-h-[58px] md:gap-3 md:px-4 md:py-3 md:min-h-[86px]">
                        <h3
                            class="flex-1 min-w-0 font-medium text-[13px] sm:text-[14px] md:text-[20px] leading-[1.35] md:leading-[1.6] text-[#364152] uppercase line-clamp-2"
                        >
                            {{ cat.title || cat.name }}
                        </h3>
                        <span
                            class="w-7 h-7 md:w-9 md:h-9 rounded-[4px] bg-[#005EA4] group-hover:bg-[#004D86] shadow-xs flex items-center justify-center shrink-0 transition-colors"
                            aria-hidden="true"
                        >
                            <img
                                src="/assets/svg/icons/chevron-right-white.svg"
                                alt=""
                                draggable="false"
                                width="20"
                                height="20"
                                class="w-4 h-4 md:w-5 md:h-5"
                            />
                        </span>
                    </div>
                </Link>

                <!-- Special Blue CTA Tile (Explore All Products) -->
                <template v-if="hasCtaBanner">
                    <Link
                        v-if="isInertiaLink(ctaLink)"
                        :href="ctaLink"
                        draggable="false"
                        class="group shrink-0 snap-start w-[calc((100%-12px)/2.12)] md:w-auto flex flex-col bg-[#005EA4] hover:bg-[#004D86] rounded-[4px] overflow-hidden transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9E0FF]"
                    >
                        <div class="relative w-full aspect-[368/308] bg-[#003967] overflow-hidden">
                            <img
                                v-if="ctaBanner.image?.url"
                                :src="ctaBanner.image.url"
                                :alt="ctaBanner.image.alt || ctaBanner.title"
                                draggable="false"
                                class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                                loading="lazy"
                                decoding="async"
                                width="368"
                                height="308"
                            />
                        </div>

                        <div class="flex items-center justify-between gap-2 px-2.5 py-2.5 min-h-[58px] md:gap-3 md:px-4 md:py-3 md:min-h-[86px]">
                            <h3
                                class="flex-1 min-w-0 font-medium text-[13px] sm:text-[14px] md:text-[20px] leading-[1.35] md:leading-[1.6] text-[#FAFAFA] uppercase line-clamp-2"
                            >
                                {{ ctaBanner.title }}
                            </h3>
                            <span
                                class="w-7 h-7 md:w-9 md:h-9 rounded-[4px] bg-[#C9E0FF] border border-[#81BDFF] group-hover:bg-[#81BDFF] shadow-xs flex items-center justify-center shrink-0 transition-colors"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/icons/chevron-right-dark.svg"
                                    alt=""
                                    draggable="false"
                                    width="20"
                                    height="20"
                                    class="w-4 h-4 md:w-5 md:h-5"
                                />
                            </span>
                        </div>
                    </Link>

                    <a
                        v-else
                        :href="ctaLink"
                        draggable="false"
                        class="group shrink-0 snap-start w-[calc((100%-12px)/2.12)] md:w-auto flex flex-col bg-[#005EA4] hover:bg-[#004D86] rounded-[4px] overflow-hidden transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9E0FF]"
                    >
                        <div class="relative w-full aspect-[368/308] bg-[#003967] overflow-hidden">
                            <img
                                v-if="ctaBanner.image?.url"
                                :src="ctaBanner.image.url"
                                :alt="ctaBanner.image.alt || ctaBanner.title"
                                draggable="false"
                                class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                                loading="lazy"
                                decoding="async"
                                width="368"
                                height="308"
                            />
                        </div>

                        <div class="flex items-center justify-between gap-2 px-2.5 py-2.5 min-h-[58px] md:gap-3 md:px-4 md:py-3 md:min-h-[86px]">
                            <h3
                                class="flex-1 min-w-0 font-medium text-[13px] sm:text-[14px] md:text-[20px] leading-[1.35] md:leading-[1.6] text-[#FAFAFA] uppercase line-clamp-2"
                            >
                                {{ ctaBanner.title }}
                            </h3>
                            <span
                                class="w-7 h-7 md:w-9 md:h-9 rounded-[4px] bg-[#C9E0FF] border border-[#81BDFF] group-hover:bg-[#81BDFF] shadow-xs flex items-center justify-center shrink-0 transition-colors"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/icons/chevron-right-dark.svg"
                                    alt=""
                                    draggable="false"
                                    width="20"
                                    height="20"
                                    class="w-4 h-4 md:w-5 md:h-5"
                                />
                            </span>
                        </div>
                    </a>
                </template>
            </div>

            <!-- Mobile Slide Pagination Indicator (md:hidden) -->
            <div
                v-if="mobileDotCount > 1"
                class="flex md:hidden items-center justify-center gap-1 -mt-3"
                role="tablist"
                :aria-label="sectionHeading"
            >
                <button
                    v-for="idx in mobileDotCount"
                    :key="idx"
                    type="button"
                    role="tab"
                    :aria-selected="activeSlideIndex === idx - 1"
                    :aria-label="`${sectionHeading} ${idx}`"
                    class="p-1.5 flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9E0FF] rounded-full"
                    @click="scrollToSlide(idx - 1)"
                >
                    <span
                        class="block h-1.5 rounded-full transition-all duration-200"
                        :class="
                            activeSlideIndex === idx - 1
                                ? 'w-5 bg-[#FAFAFA]'
                                : 'w-2 bg-[#C9E0FF]/40'
                        "
                    />
                </button>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance, onBeforeUnmount, ref } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    ctaBanner: {
        type: Object,
        default: null,
    },
    heading: {
        type: String,
        default: '',
    },
})

const page = usePage()
const instance = getCurrentInstance()

const sliderTrack = ref(null)
const activeSlideIndex = ref(0)
const isMouseDragging = ref(false)

let dragStartX = 0
let dragStartScrollLeft = 0
let hasDraggedBeyondThreshold = false
let suppressNextClick = false
let dragResetTimer = null

const currentLocale = computed(() => {
    const loc = page.props.value?.locale
    if (typeof loc === 'string') return loc
    return loc?.current || 'vi'
})

const isEn = computed(() => currentLocale.value === 'en')

function t(text) {
    const fn = instance?.proxy?.tt
    return typeof fn === 'function' ? fn.call(instance.proxy, text) : text
}

function safeRoute(name, params, fallback) {
    const proxy = instance?.proxy
    if (proxy && typeof proxy.route === 'function') {
        try {
            const url = proxy.route(name, params)
            if (url && url !== '#') return url
        } catch (e) {
            // Fall through to localized fallback
        }
    }
    return fallback
}

const productsIndexUrl = computed(() =>
    safeRoute('products.index', undefined, isEn.value ? '/en/products' : '/san-pham')
)

const validCategories = computed(() => {
    if (!Array.isArray(props.categories)) return []
    return props.categories.filter((cat) => cat && typeof cat === 'object' && (cat.title || cat.name))
})

const hasCtaBanner = computed(() => {
    return Boolean(props.ctaBanner && typeof props.ctaBanner === 'object' && (props.ctaBanner.title || props.ctaBanner.image?.url))
})

const totalTiles = computed(() => validCategories.value.length + (hasCtaBanner.value ? 1 : 0))

// Mobile shows 2 cards + peek, so reachable scroll stops = totalTiles - 1 (hidden when <= 2 tiles)
const mobileDotCount = computed(() => (totalTiles.value > 2 ? totalTiles.value - 1 : 0))

const sectionHeading = computed(() => {
    return props.ctaBanner?.subtitle || props.heading || t('Danh mục sản phẩm')
})

const ctaLink = computed(() => {
    return props.ctaBanner?.link || props.ctaBanner?.primary_cta_link || productsIndexUrl.value
})

function isInertiaLink(href) {
    if (!href || typeof href !== 'string') return false
    return href.startsWith('/') && !href.startsWith('//')
}

function onTrackScroll() {
    const el = sliderTrack.value
    const count = mobileDotCount.value
    if (!el || count <= 1) return

    const maxScroll = el.scrollWidth - el.clientWidth
    if (maxScroll <= 1) {
        activeSlideIndex.value = 0
        return
    }

    const ratio = Math.min(1, Math.max(0, el.scrollLeft / maxScroll))
    activeSlideIndex.value = Math.round(ratio * (count - 1))
}

function scrollToSlide(index) {
    const el = sliderTrack.value
    const count = mobileDotCount.value
    if (!el || count <= 1) return

    const clampedIndex = Math.min(count - 1, Math.max(0, index))
    const maxScroll = Math.max(0, el.scrollWidth - el.clientWidth)

    if (clampedIndex === count - 1) {
        el.scrollTo({ left: maxScroll, behavior: 'smooth' })
        activeSlideIndex.value = clampedIndex
        return
    }

    const firstCard = el.firstElementChild
    const stepWidth = firstCard ? firstCard.getBoundingClientRect().width + 12 : maxScroll / (count - 1)
    const targetLeft = Math.min(maxScroll, Math.round(clampedIndex * stepWidth))
    el.scrollTo({ left: targetLeft, behavior: 'smooth' })
    activeSlideIndex.value = clampedIndex
}

function onDragStart(event) {
    const el = sliderTrack.value
    if (el && el.scrollWidth > el.clientWidth + 1) {
        event.preventDefault()
    }
}

function onWindowMouseMove(event) {
    const el = sliderTrack.value
    if (!isMouseDragging.value || !el) return

    const dx = event.pageX - dragStartX
    if (Math.abs(dx) > 6) {
        hasDraggedBeyondThreshold = true
        suppressNextClick = true
    }
    el.scrollLeft = dragStartScrollLeft - dx
}

function stopMouseDrag() {
    if (!isMouseDragging.value) return
    isMouseDragging.value = false

    if (typeof window !== 'undefined') {
        window.removeEventListener('mousemove', onWindowMouseMove)
        window.removeEventListener('mouseup', stopMouseDrag)
        window.removeEventListener('blur', stopMouseDrag)
    }

    if (hasDraggedBeyondThreshold) {
        if (dragResetTimer) clearTimeout(dragResetTimer)
        dragResetTimer = setTimeout(() => {
            suppressNextClick = false
            hasDraggedBeyondThreshold = false
        }, 50)
    } else {
        suppressNextClick = false
    }
}

function onMouseDown(event) {
    if (event.button !== 0) return
    const el = sliderTrack.value
    if (!el || el.scrollWidth <= el.clientWidth + 1) return

    isMouseDragging.value = true
    dragStartX = event.pageX
    dragStartScrollLeft = el.scrollLeft
    hasDraggedBeyondThreshold = false
    suppressNextClick = false

    if (typeof window !== 'undefined') {
        window.addEventListener('mousemove', onWindowMouseMove)
        window.addEventListener('mouseup', stopMouseDrag)
        window.addEventListener('blur', stopMouseDrag)
    }
}

function onTrackClickCapture(event) {
    if (!suppressNextClick) return
    event.preventDefault()
    event.stopPropagation()
    suppressNextClick = false
    hasDraggedBeyondThreshold = false
}

onBeforeUnmount(() => {
    stopMouseDrag()
    if (dragResetTimer) {
        clearTimeout(dragResetTimer)
        dragResetTimer = null
    }
})
</script>

<style scoped>
.categories-slider-track {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.categories-slider-track::-webkit-scrollbar {
    display: none;
}
</style>
