<template>
    <section
        id="product-categories-section"
        class="bg-[#FAFAFA] py-10"
    >
        <div class="app-container-narrow flex flex-col gap-5 lg:gap-8">
            <!-- Section Header (Figma 1171:41530 / 1190:6393) -->
            <div v-if="sectionHeading" class="flex items-center gap-2 lg:gap-[10px]">
                <span class="w-[10px] h-[10px] lg:w-3 lg:h-3 rounded-[2px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                <h2 class="font-bold text-[16px] lg:text-[18px] leading-[24px] lg:leading-[1.6] text-[#005EA4]">
                    {{ sectionHeading }}
                </h2>
            </div>

            <!-- Empty State when CMS categories are empty (light variant on #FAFAFA surface) -->
            <EmptyState
                v-if="validCategories.length === 0"
                variant="light"
                heading-tag="h3"
                :title="emptyTitle"
                :description="emptyDescription"
                :primary-action-text="emptyPrimaryActionText"
                :primary-action-href="contactUrl"
            />

            <!-- Static 2-Col Mobile Grid (Figma 1190:6396) & 3-Col Desktop Grid (Figma 1171:41533 — 368x394px) -->
            <div
                v-else
                class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-6"
            >
                <!-- Standard CMS Category Tiles (Figma 1171:41535 & 1190:6398 — #C9E0FF surface) -->
                <Link
                    v-for="cat in validCategories"
                    :key="cat.id || cat.slug"
                    :href="cat.url || productsIndexUrl"
                    class="group flex flex-col bg-[#C9E0FF] rounded-[6px] lg:rounded-[8px] overflow-hidden transition-transform duration-200 hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                >
                    <div class="relative w-full aspect-[173/124] lg:aspect-[368/308] bg-[#FAFAFA] overflow-hidden">
                        <img
                            v-if="cat.image?.url"
                            :src="cat.image.url"
                            :alt="cat.image.alt || cat.title || cat.name"
                            class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                            loading="lazy"
                            decoding="async"
                            width="368"
                            height="308"
                        />
                    </div>

                    <div class="flex items-center justify-between gap-2 px-2.5 py-2 min-h-[70px] lg:gap-3 lg:px-4 lg:py-3 lg:h-[86px]">
                        <h3
                            class="flex-1 min-w-0 font-bold text-[14px] lg:text-[18px] leading-[20px] lg:leading-[27px] text-[#00284B] uppercase line-clamp-2 break-words"
                        >
                            {{ cat.title || cat.name }}
                        </h3>
                        <span
                            class="w-6 h-6 lg:w-9 lg:h-9 rounded-[4px] bg-[#005EA4] group-hover:bg-[#004D86] shadow-xs flex items-center justify-center shrink-0 transition-colors"
                            aria-hidden="true"
                        >
                            <img
                                src="/assets/svg/icons/chevron-right-white.svg"
                                alt=""
                                width="20"
                                height="20"
                                class="w-3.5 h-3.5 lg:w-5 lg:h-5"
                            />
                        </span>
                    </div>
                </Link>

                <!-- 6th Blue CTA Tile — Explore All Products (Figma 1171:41541 & 1190:6447) -->
                <template v-if="hasCtaBanner">
                    <Link
                        v-if="isInertiaLink(ctaLink)"
                        :href="ctaLink"
                        class="group flex flex-col bg-[#005EA4] hover:bg-[#004D86] rounded-[6px] lg:rounded-[8px] overflow-hidden transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                    >
                        <div class="relative w-full aspect-[173/124] lg:aspect-[368/308] bg-[#003967] overflow-hidden">
                            <img
                                v-if="ctaBanner.image?.url"
                                :src="ctaBanner.image.url"
                                :alt="ctaBanner.image.alt || ctaBanner.title"
                                class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                                loading="lazy"
                                decoding="async"
                                width="368"
                                height="308"
                            />
                        </div>

                        <div class="flex items-center justify-between gap-2 px-2.5 py-2 min-h-[70px] lg:gap-3 lg:px-4 lg:py-3 lg:h-[86px]">
                            <h3
                                class="flex-1 min-w-0 font-bold text-[14px] lg:text-[18px] leading-[20px] lg:leading-[27px] text-[#FAFAFA] uppercase line-clamp-2 break-words"
                            >
                                {{ ctaBanner.title }}
                            </h3>
                            <span
                                class="w-6 h-6 lg:w-9 lg:h-9 rounded-[4px] bg-[#C9E0FF] border border-[#81BDFF] group-hover:bg-[#81BDFF] shadow-xs flex items-center justify-center shrink-0 transition-colors"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/icons/chevron-right-dark.svg"
                                    alt=""
                                    width="20"
                                    height="20"
                                    class="w-3.5 h-3.5 lg:w-5 lg:h-5"
                                />
                            </span>
                        </div>
                    </Link>

                    <a
                        v-else
                        :href="ctaLink"
                        class="group flex flex-col bg-[#005EA4] hover:bg-[#004D86] rounded-[6px] lg:rounded-[8px] overflow-hidden transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                    >
                        <div class="relative w-full aspect-[173/124] lg:aspect-[368/308] bg-[#003967] overflow-hidden">
                            <img
                                v-if="ctaBanner.image?.url"
                                :src="ctaBanner.image.url"
                                :alt="ctaBanner.image.alt || ctaBanner.title"
                                class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                                loading="lazy"
                                decoding="async"
                                width="368"
                                height="308"
                            />
                        </div>

                        <div class="flex items-center justify-between gap-2 px-2.5 py-2 min-h-[70px] lg:gap-3 lg:px-4 lg:py-3 lg:h-[86px]">
                            <h3
                                class="flex-1 min-w-0 font-bold text-[14px] lg:text-[18px] leading-[20px] lg:leading-[27px] text-[#FAFAFA] uppercase line-clamp-2 break-words"
                            >
                                {{ ctaBanner.title }}
                            </h3>
                            <span
                                class="w-6 h-6 lg:w-9 lg:h-9 rounded-[4px] bg-[#C9E0FF] border border-[#81BDFF] group-hover:bg-[#81BDFF] shadow-xs flex items-center justify-center shrink-0 transition-colors"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/icons/chevron-right-dark.svg"
                                    alt=""
                                    width="20"
                                    height="20"
                                    class="w-3.5 h-3.5 lg:w-5 lg:h-5"
                                />
                            </span>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'
import EmptyState from '@/Components/EmptyState.vue'

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

const instance = getCurrentInstance()
const { translate: t, isEn } = useTranslate()

function safeRoute(name, params, fallback) {
    const proxy = instance?.proxy
    if (proxy && typeof proxy.route === 'function') {
        if (typeof proxy.routes === 'function') {
            const allRoutes = proxy.routes() || {}
            const localizedName =
                typeof proxy.routeLocaleName === 'function' ? proxy.routeLocaleName(name) : name
            if (!allRoutes[localizedName] && !allRoutes[name]) {
                return fallback
            }
        }
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

const contactUrl = computed(() =>
    safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he')
)

const emptyTitle = computed(() =>
    isEn.value ? 'Product categories are being updated' : 'Danh mục sản phẩm đang được cập nhật'
)

const emptyDescription = computed(() =>
    isEn.value
        ? 'Please send your part numbers (OEM SKU) or technical specifications to receive a direct engineering quotation.'
        : 'Quý khách hàng doanh nghiệp vui lòng gửi mã phụ tùng (SKU) hoặc thông số kỹ thuật để nhận báo giá trực tiếp từ đội ngũ kỹ sư.'
)

const emptyPrimaryActionText = computed(() =>
    isEn.value ? 'Send SKU for Quote' : 'Gửi mã phụ tùng nhận báo giá'
)

const hasCtaBanner = computed(() => {
    return Boolean(props.ctaBanner && typeof props.ctaBanner === 'object' && (props.ctaBanner.title || props.ctaBanner.image?.url))
})

const validCategories = computed(() => {
    if (!Array.isArray(props.categories)) return []
    const maxCategories = hasCtaBanner.value ? 5 : 6
    return props.categories
        .filter((cat) => cat && typeof cat === 'object' && (cat.title || cat.name))
        .slice(0, maxCategories)
})

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
</script>
