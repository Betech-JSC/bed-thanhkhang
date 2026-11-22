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

            <!-- Responsive Product Categories Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                <!-- Standard CMS Category Tiles -->
                <Link
                    v-for="cat in validCategories"
                    :key="cat.id || cat.slug"
                    :href="cat.url || productsIndexUrl"
                    class="group flex flex-col bg-[#FAFAFA] rounded-[4px] overflow-hidden transition-transform duration-200 hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9E0FF]"
                >
                    <div class="relative w-full aspect-[368/308] bg-[#EEF2F6] overflow-hidden">
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

                    <div class="flex items-center justify-between gap-3 px-4 py-3 min-h-[76px] md:min-h-[86px]">
                        <h3
                            class="flex-1 min-w-0 font-medium text-[16px] md:text-[20px] leading-[1.5] md:leading-[1.6] text-[#364152] uppercase line-clamp-2"
                        >
                            {{ cat.title || cat.name }}
                        </h3>
                        <span
                            class="w-9 h-9 rounded-[4px] bg-[#005EA4] group-hover:bg-[#004D86] shadow-xs flex items-center justify-center shrink-0 transition-colors"
                            aria-hidden="true"
                        >
                            <img
                                src="/assets/svg/icons/chevron-right-white.svg"
                                alt=""
                                width="20"
                                height="20"
                                class="w-5 h-5"
                            />
                        </span>
                    </div>
                </Link>

                <!-- Special Blue CTA Tile (Explore All Products) -->
                <template v-if="hasCtaBanner">
                    <Link
                        v-if="isInertiaLink(ctaLink)"
                        :href="ctaLink"
                        class="group flex flex-col bg-[#005EA4] hover:bg-[#004D86] rounded-[4px] overflow-hidden transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9E0FF]"
                    >
                        <div class="relative w-full aspect-[368/308] bg-[#003967] overflow-hidden">
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

                        <div class="flex items-center justify-between gap-3 px-4 py-3 min-h-[76px] md:min-h-[86px]">
                            <h3
                                class="flex-1 min-w-0 font-medium text-[16px] md:text-[20px] leading-[1.5] md:leading-[1.6] text-[#FAFAFA] uppercase line-clamp-2"
                            >
                                {{ ctaBanner.title }}
                            </h3>
                            <span
                                class="w-9 h-9 rounded-[4px] bg-[#C9E0FF] border border-[#81BDFF] group-hover:bg-[#81BDFF] shadow-xs flex items-center justify-center shrink-0 transition-colors"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/icons/chevron-right-dark.svg"
                                    alt=""
                                    width="20"
                                    height="20"
                                    class="w-5 h-5"
                                />
                            </span>
                        </div>
                    </Link>

                    <a
                        v-else
                        :href="ctaLink"
                        class="group flex flex-col bg-[#005EA4] hover:bg-[#004D86] rounded-[4px] overflow-hidden transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9E0FF]"
                    >
                        <div class="relative w-full aspect-[368/308] bg-[#003967] overflow-hidden">
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

                        <div class="flex items-center justify-between gap-3 px-4 py-3 min-h-[76px] md:min-h-[86px]">
                            <h3
                                class="flex-1 min-w-0 font-medium text-[16px] md:text-[20px] leading-[1.5] md:leading-[1.6] text-[#FAFAFA] uppercase line-clamp-2"
                            >
                                {{ ctaBanner.title }}
                            </h3>
                            <span
                                class="w-9 h-9 rounded-[4px] bg-[#C9E0FF] border border-[#81BDFF] group-hover:bg-[#81BDFF] shadow-xs flex items-center justify-center shrink-0 transition-colors"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/icons/chevron-right-dark.svg"
                                    alt=""
                                    width="20"
                                    height="20"
                                    class="w-5 h-5"
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
