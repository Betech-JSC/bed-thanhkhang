<template>
    <section
        v-if="normalizedProducts.length > 0"
        aria-labelledby="pdp-related-heading"
        class="bg-[#FAFAFA] pb-8 md:pb-16 lg:pb-[80px]"
    >
        <div class="app-container-narrow flex flex-col gap-6 md:gap-8">
            <!-- Section Header (Figma Nodes 1264:49388 / 1264:49710) -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2 lg:gap-[10px] min-w-0">
                    <span
                        class="w-[10px] h-[10px] lg:w-3 lg:h-3 rounded-[2px] bg-[#005EA4] shrink-0"
                        aria-hidden="true"
                    />
                    <h2
                        id="pdp-related-heading"
                        class="font-bold text-[16px] lg:text-[18px] leading-[24px] lg:leading-[1.6] text-[#005EA4] break-words"
                    >
                        {{ tt('Sản phẩm liên quan') }}
                    </h2>
                </div>

                <!-- Carousel Prev / Next Controls -->
                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="button"
                        :disabled="!canPrev"
                        :aria-disabled="!canPrev"
                        :aria-label="tt('Sản phẩm trước')"
                        :class="[
                            'w-8 h-8 rounded-[6px] bg-[#EEF2F6] flex items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]',
                            canPrev
                                ? 'hover:bg-[#E3E8EF] active:bg-[#CDD5DF] cursor-pointer'
                                : 'cursor-not-allowed',
                        ]"
                        @click="scrollPrev"
                    >
                        <img
                            src="/assets/svg/icons/product-chevron-left.svg"
                            alt=""
                            aria-hidden="true"
                            width="16"
                            height="16"
                            :class="['w-4 h-4 shrink-0 transition-opacity', canPrev ? '' : 'opacity-45']"
                        />
                    </button>

                    <button
                        type="button"
                        :disabled="!canNext"
                        :aria-disabled="!canNext"
                        :aria-label="tt('Sản phẩm tiếp theo')"
                        :class="[
                            'w-8 h-8 rounded-[6px] bg-[#EEF2F6] flex items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]',
                            canNext
                                ? 'hover:bg-[#E3E8EF] active:bg-[#CDD5DF] cursor-pointer'
                                : 'cursor-not-allowed',
                        ]"
                        @click="scrollNext"
                    >
                        <img
                            src="/assets/svg/icons/product-chevron-right.svg"
                            alt=""
                            aria-hidden="true"
                            width="16"
                            height="16"
                            :class="['w-4 h-4 shrink-0 transition-opacity', canNext ? '' : 'opacity-45']"
                        />
                    </button>
                </div>
            </div>

            <!-- Horizontal GPU Translate-X Carousel Viewport (4 col Desktop / 3 col Tablet / 2 col Mobile) -->
            <div
                class="related-products-viewport overflow-hidden w-full py-1 -my-1"
                @touchstart.passive="onTouchStart"
                @touchend.passive="onTouchEnd"
            >
                <div
                    class="flex w-full transition-transform duration-300 ease-out will-change-transform"
                    :style="trackStyle"
                >
                    <div
                        v-for="(item, idx) in normalizedProducts"
                        :key="item.id ?? item.slug ?? idx"
                        class="related-product-slide shrink-0 flex min-w-0"
                    >
                        <ProductCard :product="item" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import ProductCard from '@/Pages/Product/Components/ProductCard.vue'

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
})

const normalizedProducts = computed(() => {
    if (!Array.isArray(props.products)) return []
    return props.products
        .filter((item) => {
            if (!item || typeof item !== 'object') return false
            const title = String(item.title || item.name || '').trim()
            return title !== ''
        })
        .slice(0, 8)
})

const visibleCount = ref(4)
const slideIndex = ref(0)
let touchStartX = 0
let touchStartY = 0

function syncVisibleCount() {
    if (typeof window === 'undefined') return
    if (window.matchMedia('(min-width: 1024px)').matches) {
        visibleCount.value = 4
    } else if (window.matchMedia('(min-width: 768px)').matches) {
        visibleCount.value = 3
    } else {
        visibleCount.value = 2
    }
}

const maxIndex = computed(() => Math.max(0, normalizedProducts.value.length - visibleCount.value))
const canPrev = computed(() => slideIndex.value > 0)
const canNext = computed(() => slideIndex.value < maxIndex.value)

watch(maxIndex, (nextMax) => {
    if (slideIndex.value > nextMax) {
        slideIndex.value = nextMax
    }
})

watch(
    () => normalizedProducts.value.map((p, idx) => p.id ?? p.slug ?? idx).join(','),
    () => {
        slideIndex.value = 0
    }
)

const trackStyle = computed(() => ({
    gap: 'var(--card-gap)',
    transform:
        slideIndex.value === 0
            ? 'translateX(0px)'
            : `translateX(calc(-${slideIndex.value} * (100% + var(--card-gap)) / var(--cards-per-view)))`,
}))

function scrollPrev() {
    if (!canPrev.value) return
    slideIndex.value = Math.max(0, slideIndex.value - 1)
}

function scrollNext() {
    if (!canNext.value) return
    slideIndex.value = Math.min(maxIndex.value, slideIndex.value + 1)
}

function onTouchStart(event) {
    if (!event.touches || event.touches.length === 0) return
    touchStartX = event.touches[0].clientX
    touchStartY = event.touches[0].clientY
}

function onTouchEnd(event) {
    if (!event.changedTouches || event.changedTouches.length === 0) return
    const deltaX = event.changedTouches[0].clientX - touchStartX
    const deltaY = event.changedTouches[0].clientY - touchStartY
    if (Math.abs(deltaX) > 40 && Math.abs(deltaX) > Math.abs(deltaY)) {
        if (deltaX < 0) {
            scrollNext()
        } else {
            scrollPrev()
        }
    }
}

onMounted(() => {
    syncVisibleCount()
    if (typeof window !== 'undefined') {
        window.addEventListener('resize', syncVisibleCount, { passive: true })
    }
})

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('resize', syncVisibleCount)
    }
})
</script>

<style scoped>
.related-products-viewport {
    --cards-per-view: 2;
    --card-gap: 12px;
}

@media (min-width: 768px) {
    .related-products-viewport {
        --cards-per-view: 3;
        --card-gap: 24px;
    }
}

@media (min-width: 1024px) {
    .related-products-viewport {
        --cards-per-view: 4;
        --card-gap: 32px;
    }
}

.related-product-slide {
    width: calc((100% - (var(--cards-per-view) - 1) * var(--card-gap)) / var(--cards-per-view));
}
</style>
