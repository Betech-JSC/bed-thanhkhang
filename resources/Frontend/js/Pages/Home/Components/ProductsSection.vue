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

            <!-- Static 2-Col Mobile Grid (Figma 1190:6396) & 3-Col Desktop Grid (Figma 1264:48049 — 368x394px, gap 24px) -->
            <div
                v-else
                class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-[24px]"
            >
                <!-- Tiles 1–5: Standard CMS Category Tiles (Figma 1264:48051..48056 — #C9E0FF fixed bg, image-only zoom on hover, locked 394px height) -->
                <Link
                    v-for="cat in validCategories"
                    :key="cat.id || cat.slug"
                    :href="cat.url || productsIndexUrl"
                    class="group flex flex-col lg:h-[394px] bg-[#C9E0FF] rounded-[4px] overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                >
                    <div class="relative w-full aspect-[173/124] lg:aspect-auto lg:h-[308px] shrink-0 bg-[#FAFAFA] overflow-hidden">
                        <img
                            v-if="cat.image?.url"
                            :src="cat.image.url"
                            :alt="cat.image.alt || cat.title || cat.name"
                            class="w-full h-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-105 will-change-transform"
                            loading="lazy"
                            decoding="async"
                            width="368"
                            height="308"
                        />
                    </div>

                    <div class="flex items-center justify-between gap-2 px-2.5 py-2 min-h-[70px] lg:min-h-0 lg:gap-[10px] lg:px-[16px] lg:py-0 lg:h-[64px] lg:mt-[12px] lg:mb-[10px]">
                        <h3
                            class="flex-1 min-w-0 font-medium text-[14px] lg:text-[20px] leading-[1.6] text-[#364152] uppercase break-words"
                        >
                            <span
                                v-for="(line, lineIdx) in splitTileTitle(cat.title || cat.name)"
                                :key="lineIdx"
                                class="block"
                            >{{ line }}</span>
                        </h3>
                        <span
                            class="w-6 h-6 lg:w-[36px] lg:h-[36px] rounded-[4px] bg-[#005EA4] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] flex items-center justify-center shrink-0"
                            aria-hidden="true"
                        >
                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 20 20"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-3.5 h-3.5 lg:w-5 lg:h-5 text-[#FAFAFA]"
                            >
                                <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                </Link>

                <!-- 6th CTA Tile — Explore All Products: default #C9E0FF matching tiles 1-5, transitions to #005EA4 ONLY on hover + image-only zoom, locked 394px height -->
                <template v-if="hasCtaBanner">
                    <Link
                        v-if="isInertiaLink(ctaLink)"
                        :href="ctaLink"
                        class="group flex flex-col lg:h-[394px] bg-[#C9E0FF] hover:bg-[#005EA4] rounded-[4px] overflow-hidden transition-colors duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                    >
                        <div class="relative w-full aspect-[173/124] lg:aspect-auto lg:h-[308px] shrink-0 bg-[#FAFAFA] overflow-hidden">
                            <img
                                v-if="ctaBanner.image?.url"
                                :src="ctaBanner.image.url"
                                :alt="ctaBanner.image.alt || ctaBanner.title"
                                class="w-full h-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-105 will-change-transform"
                                loading="lazy"
                                decoding="async"
                                width="368"
                                height="308"
                            />
                        </div>

                        <div class="flex items-center justify-between gap-2 px-2.5 py-2 min-h-[70px] lg:min-h-0 lg:gap-[10px] lg:px-[16px] lg:py-0 lg:h-[64px] lg:mt-[12px] lg:mb-[10px]">
                            <h3
                                class="flex-1 min-w-0 font-medium text-[14px] lg:text-[20px] leading-[1.6] text-[#364152] group-hover:text-[#FAFAFA] uppercase break-words transition-colors duration-300"
                            >
                                <span
                                    v-for="(line, lineIdx) in splitTileTitle(ctaBanner.title)"
                                    :key="lineIdx"
                                    class="block"
                                >{{ line }}</span>
                            </h3>
                            <span
                                class="w-6 h-6 lg:w-[36px] lg:h-[36px] rounded-[4px] bg-[#005EA4] border border-transparent group-hover:bg-[#C9E0FF] group-hover:border-[#81BDFF] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] flex items-center justify-center shrink-0 transition-colors duration-300"
                                aria-hidden="true"
                            >
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-3.5 h-3.5 lg:w-5 lg:h-5 text-[#FAFAFA] group-hover:text-[#003967] transition-colors duration-300"
                                >
                                    <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </Link>

                    <a
                        v-else
                        :href="ctaLink"
                        class="group flex flex-col lg:h-[394px] bg-[#C9E0FF] hover:bg-[#005EA4] rounded-[4px] overflow-hidden transition-colors duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                    >
                        <div class="relative w-full aspect-[173/124] lg:aspect-auto lg:h-[308px] shrink-0 bg-[#FAFAFA] overflow-hidden">
                            <img
                                v-if="ctaBanner.image?.url"
                                :src="ctaBanner.image.url"
                                :alt="ctaBanner.image.alt || ctaBanner.title"
                                class="w-full h-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-105 will-change-transform"
                                loading="lazy"
                                decoding="async"
                                width="368"
                                height="308"
                            />
                        </div>

                        <div class="flex items-center justify-between gap-2 px-2.5 py-2 min-h-[70px] lg:min-h-0 lg:gap-[10px] lg:px-[16px] lg:py-0 lg:h-[64px] lg:mt-[12px] lg:mb-[10px]">
                            <h3
                                class="flex-1 min-w-0 font-medium text-[14px] lg:text-[20px] leading-[1.6] text-[#364152] group-hover:text-[#FAFAFA] uppercase break-words transition-colors duration-300"
                            >
                                <span
                                    v-for="(line, lineIdx) in splitTileTitle(ctaBanner.title)"
                                    :key="lineIdx"
                                    class="block"
                                >{{ line }}</span>
                            </h3>
                            <span
                                class="w-6 h-6 lg:w-[36px] lg:h-[36px] rounded-[4px] bg-[#005EA4] border border-transparent group-hover:bg-[#C9E0FF] group-hover:border-[#81BDFF] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] flex items-center justify-center shrink-0 transition-colors duration-300"
                                aria-hidden="true"
                            >
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-3.5 h-3.5 lg:w-5 lg:h-5 text-[#FAFAFA] group-hover:text-[#003967] transition-colors duration-300"
                                >
                                    <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
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

function splitTileTitle(rawTitle) {
    const clean = String(rawTitle || '').trim()
    if (!clean) return []
    if (clean.includes('\n')) {
        return clean.split('\n').map((part) => part.trim()).filter(Boolean)
    }

    const prefixPatterns = [
        /^(khám phá toàn bộ)\s+(.+)$/i,
        /^(linh kiện)\s+(.+)$/i,
        /^(hệ thống)\s+(.+)$/i,
        /^(giải pháp)\s+(.+)$/i,
        /^(explore all)\s+(.+)$/i,
    ]

    for (const pattern of prefixPatterns) {
        const match = clean.match(pattern)
        if (match && match[1] && match[2]) {
            return [match[1].trim(), match[2].trim()]
        }
    }

    const words = clean.split(/\s+/)
    if (words.length >= 4) {
        const mid = Math.ceil(words.length / 2)
        return [words.slice(0, mid).join(' '), words.slice(mid).join(' ')]
    }

    return [clean]
}
</script>
