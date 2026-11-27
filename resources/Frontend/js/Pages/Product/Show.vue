<template>
    <div class="bg-[#FAFAFA] text-[#364152] overflow-x-clip selection:bg-primary selection:text-white">
        <!-- SEO HEAD -->
        <Head>
            <title v-if="headTitle">{{ headTitle }}</title>
            <meta v-if="currentDescription" name="description" :content="currentDescription" />
            <meta v-if="currentTitle" property="og:title" :content="currentTitle" />
            <meta v-if="currentDescription" property="og:description" :content="currentDescription" />
            <meta v-if="ogImageUrl" property="og:image" :content="ogImageUrl" />
            <meta property="og:type" content="product" />
            <component
                v-if="productJsonLd"
                :is="'script'"
                type="application/ld+json"
                v-html="JSON.stringify(productJsonLd)"
            />
            <component
                v-if="breadcrumbJsonLd"
                :is="'script'"
                type="application/ld+json"
                v-html="JSON.stringify(breadcrumbJsonLd)"
            />
        </Head>

        <!-- EMPTY STATE KHI KHÔNG CÓ DỮ LIỆU SẢN PHẨM -->
        <section v-if="!currentTitle" class="bg-[#FAFAFA] py-8 md:py-16">
            <div class="app-container-narrow">
                <EmptyState
                    variant="light"
                    heading-tag="h1"
                    :title="tt('Sản phẩm không tồn tại hoặc đang được cập nhật')"
                    :description="tt('Quý khách vui lòng quay lại danh mục sản phẩm hoặc liên hệ trực tiếp để được đội ngũ kỹ sư hỗ trợ.')"
                    :primary-action-text="tt('Xem tất cả sản phẩm')"
                    :primary-action-href="productsIndexUrl"
                    :secondary-action-text="tt('Liên hệ tư vấn')"
                    :secondary-action-href="contactUrl"
                />
            </div>
        </section>

        <template v-else>
            <!-- SECTION 1: TỔNG QUAN SẢN PHẨM (Figma Node 1264:49294) -->
            <section class="bg-[#FAFAFA] pt-6 pb-8 md:pt-10 md:pb-12">
                <div class="app-container-narrow flex flex-col gap-6">
                    <!-- Breadcrumb -->
                    <nav
                        class="text-[14px] leading-[1.6] flex flex-wrap items-center gap-2 text-[#697586]"
                        :aria-label="tt('Breadcrumb')"
                    >
                        <Link
                            :href="homeUrl"
                            class="text-[#697586] hover:text-[#005EA4] transition-colors"
                        >
                            {{ tt('Trang chủ') }}
                        </Link>
                        <span class="select-none" aria-hidden="true">/</span>
                        <Link
                            :href="productsIndexUrl"
                            class="text-[#697586] hover:text-[#005EA4] transition-colors"
                        >
                            {{ tt('Sản phẩm') }}
                        </Link>
                        <template v-for="(crumb, idx) in breadcrumbCategories" :key="crumb.key || idx">
                            <span class="select-none" aria-hidden="true">/</span>
                            <Link
                                :href="crumb.href"
                                class="text-[#697586] hover:text-[#005EA4] transition-colors"
                            >
                                {{ crumb.title }}
                            </Link>
                        </template>
                        <span class="select-none" aria-hidden="true">/</span>
                        <span class="text-[#4B5565] font-medium break-words" aria-current="page">
                            {{ currentTitle }}
                        </span>
                    </nav>

                    <!-- Layout 2 cột: Gallery bên trái & Thông tin đặt hàng bên phải -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-[32px] items-start">
                        <!-- Cột trái: Thư viện ảnh giữ nguyên hàng thumbnail preview ngang ở dưới -->
                        <div class="w-full min-w-0">
                            <ProductGallery
                                :images="galleryImages"
                                :product-title="currentTitle"
                            />
                        </div>

                        <!-- Cột phải: Tiêu đề + Mô tả ngắn + Hộp thông số nhanh + CTA Báo giá -->
                        <div class="w-full min-w-0 flex flex-col gap-5 md:gap-6">
                            <div>
                                <h1 class="text-[20px] md:text-[24px] font-bold leading-[1.5] md:leading-[1.6] text-[#001932] break-words">
                                    {{ currentTitle }}
                                </h1>
                                <p
                                    v-if="currentDescription"
                                    class="text-[14px] md:text-[16px] font-medium leading-[1.6] text-[#001932] mt-1 break-words"
                                >
                                    {{ currentDescription }}
                                </p>
                            </div>

                            <!-- Hộp thông số nhanh -->
                            <div class="bg-[#EEF2F6] rounded-[4px] p-4 md:p-6 flex flex-col gap-4 md:gap-5 text-[14px] leading-[1.6]">
                                <!-- Nhóm trên: 2 thông số kỹ thuật ưu tiên + Mã sản phẩm -->
                                <div class="flex flex-col gap-2">
                                    <div
                                        v-for="(spec, idx) in highlightSpecs"
                                        :key="`highlight-spec-${idx}`"
                                        class="grid grid-cols-2 gap-4 md:gap-[40px] items-center"
                                    >
                                        <span class="text-[#4B5565] font-normal break-words">
                                            {{ translateLabel(spec.label) }}
                                        </span>
                                        <span class="text-[#005EA4] font-bold break-words">
                                            {{ translateValue(spec.value) }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 md:gap-[40px] items-center">
                                        <span class="text-[#4B5565] font-normal">
                                            {{ tt('Mã sản phẩm') }}
                                        </span>
                                        <span class="text-[#005EA4] font-bold break-words">
                                            {{ resolvedSku }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Nhóm dưới: 6 thông số mặc định/riêng của sản phẩm -->
                                <div class="border-t border-[#CDD5DF] pt-4 md:pt-5 grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-x-[40px] md:gap-y-2 text-[#4B5565]">
                                    <div class="space-y-2">
                                        <p class="break-words">
                                            {{ tt('Thương hiệu:') }} <strong class="font-bold">{{ resolvedBrand }}</strong>
                                        </p>
                                        <p class="break-words">
                                            {{ tt('Xuất xứ:') }} <strong class="font-bold">{{ resolvedOrigin }}</strong>
                                        </p>
                                        <p class="break-words">
                                            {{ tt('Bảo hành:') }} <strong class="font-bold">{{ resolvedWarranty }}</strong>
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="break-words">
                                            {{ tt('Tình trạng:') }} <strong class="font-bold">{{ resolvedCondition }}</strong>
                                        </p>
                                        <p class="break-words">
                                            {{ tt('Trạng thái:') }} <strong class="font-bold">{{ resolvedStockStatus }}</strong>
                                        </p>
                                        <p class="break-words">
                                            {{ tt('Hỗ trợ kỹ thuật:') }} <strong class="font-bold">{{ resolvedTechnicalSupport }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Nút CTA Yêu cầu báo giá -->
                            <button
                                type="button"
                                @click="handleRequestQuote"
                                class="w-full min-h-[46px] px-6 py-[10px] rounded-[4px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] text-[16px] font-medium leading-[1.6] transition-colors flex items-center justify-center cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                            >
                                {{ tt('Yêu cầu báo giá') }}
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: TRUST STRIP 4 CAM KẾT (Figma Node 1264:49352) -->
            <ProductTrustStrip />

            <!-- SECTION 3: 3 TABS CHI TIẾT SẢN PHẨM (Figma Nodes 1264:49358 / 49506 / 49681) -->
            <ProductDetailTabs :product="product" />

            <!-- SECTION 4: CÂU HỎI THƯỜNG GẶP (Figma Nodes 1264:49371 / 1264:49693) -->
            <ProductFaqSection :faqs="resolvedFaqs" />

            <!-- SECTION 5: SẢN PHẨM LIÊN QUAN (Figma Nodes 1264:49387 / 1264:49709) -->
            <RelatedProducts :products="relatedProducts" />

            <!-- SECTION 6: CTA TƯ VẤN KỸ THUẬT (Figma Node 1264:49400 — BANNER_GLOBAL_CTA) -->
            <ProductCtaBanner :cta-banner="ctaBanner" />
        </template>
    </div>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
    name: 'ProductShow',
    layout: MainLayout,
}
</script>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import ProductGallery from '@/Components/Product/ProductGallery.vue'
import ProductTrustStrip from './Components/ProductTrustStrip.vue'
import ProductDetailTabs from './Components/ProductDetailTabs.vue'
import ProductFaqSection from './Components/ProductFaqSection.vue'
import RelatedProducts from '@/Components/Product/RelatedProducts.vue'
import ProductCtaBanner from './Components/ProductCtaBanner.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { openQuoteModal } from '@/composables/useQuoteModal'
import {
    hasVietnameseDiacritics,
    translateSpecLabel,
    translateSpecValue,
    useTranslate,
} from '@/composables/useTranslate.js'

const props = defineProps({
    product: {
        type: Object,
        default: () => ({}),
    },
    related_products: {
        type: Array,
        default: () => [],
    },
    faqs: {
        type: Array,
        default: () => [],
    },
    ctaBanner: {
        type: [Object, Array],
        default: null,
    },
    pdpDefaults: {
        type: Object,
        default: () => ({}),
    },
    seo: {
        type: Object,
        default: null,
    },
})

const instance = getCurrentInstance()
const { translate, isEn, page, pageProps } = useTranslate()

function translateLabel(raw) {
    return translateSpecLabel(raw, translate)
}

function translateValue(raw) {
    return translateSpecValue(raw, translate, isEn.value)
}

function safeRoute(name, params, fallback = '/') {
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

function pickNonEmpty(...values) {
    for (const val of values) {
        if (typeof val === 'string' && val.trim() !== '') {
            return val.trim()
        }
        if (typeof val === 'number' && !Number.isNaN(val)) {
            return String(val)
        }
    }
    return ''
}

function toRelativeHref(rawUrl) {
    if (typeof rawUrl !== 'string' || rawUrl.trim() === '') return ''
    const trimmed = rawUrl.trim()
    if (trimmed.startsWith('/')) return trimmed
    if (/^https?:\/\//i.test(trimmed)) {
        try {
            const parsed = new URL(trimmed)
            return `${parsed.pathname}${parsed.search}${parsed.hash}`
        } catch (e) {
            return trimmed
        }
    }
    return trimmed
}

const globalConfig = computed(() => ({
    ...(pageProps.value?.global || {}),
    ...(pageProps.value?.configs || {}),
}))

const brandName = computed(() =>
    pickNonEmpty(
        globalConfig.value.general_business_name,
        globalConfig.value.general_site_name,
        globalConfig.value.company_name,
        globalConfig.value.site_name
    )
)

const siteOrigin = computed(() => {
    if (typeof window !== 'undefined' && window.location?.origin) {
        return window.location.origin
    }
    const routeUrl = pageProps.value?.route?.url
    if (typeof routeUrl === 'string' && /^https?:\/\//i.test(routeUrl)) {
        try {
            return new URL(routeUrl).origin
        } catch (e) {
            // Ignore invalid URL
        }
    }
    return ''
})

const homeUrl = computed(() => safeRoute('home', undefined, isEn.value ? '/en' : '/'))

const productsIndexUrl = computed(() =>
    safeRoute('products.index', undefined, isEn.value ? '/en/products' : '/san-pham')
)

const contactUrl = computed(() =>
    safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he')
)

const currentTitle = computed(() =>
    pickNonEmpty(props.product?.title, props.product?.name)
)

const currentDescription = computed(() =>
    pickNonEmpty(props.product?.description, props.product?.short_description)
)

const headTitle = computed(() => {
    const seoTitle = pickNonEmpty(props.seo?.seo_meta_title, props.seo?.title)
    if (seoTitle) return seoTitle
    if (currentTitle.value) {
        return brandName.value ? `${currentTitle.value} - ${brandName.value}` : currentTitle.value
    }
    return ''
})

const currentCategoryObj = computed(() => {
    if (props.product?.category && typeof props.product.category === 'object') {
        return props.product.category
    }
    if (Array.isArray(props.product?.categories) && props.product.categories.length > 0) {
        return props.product.categories[0]
    }
    return null
})

const currentCategory = computed(() =>
    pickNonEmpty(
        currentCategoryObj.value?.title,
        currentCategoryObj.value?.name,
        props.product?.category_name
    )
)

const currentCategorySlug = computed(() =>
    pickNonEmpty(
        currentCategoryObj.value?.seo_slug,
        currentCategoryObj.value?.slug,
        props.product?.category_slug
    )
)

const categoryUrl = computed(() => {
    if (currentCategorySlug.value) {
        return `${productsIndexUrl.value}?category=${encodeURIComponent(currentCategorySlug.value)}`
    }
    if (currentCategoryObj.value?.id) {
        return `${productsIndexUrl.value}?categories=${encodeURIComponent(currentCategoryObj.value.id)}`
    }
    return productsIndexUrl.value
})

const breadcrumbCategories = computed(() => {
    const rawCrumbs = Array.isArray(props.product?.breadcrumbs) ? props.product.breadcrumbs : []
    if (rawCrumbs.length > 0) {
        const rootSlug = pickNonEmpty(rawCrumbs[0]?.slug)
        return rawCrumbs
            .map((crumb, idx) => {
                if (!crumb || typeof crumb !== 'object') return null
                const title = pickNonEmpty(crumb.title, crumb.name)
                if (!title) return null

                const slug = pickNonEmpty(crumb.seo_slug, crumb.slug)
                let href = toRelativeHref(pickNonEmpty(crumb.url))
                if (!href) {
                    if (idx > 0 && rootSlug && slug) {
                        href = `${productsIndexUrl.value}?category=${encodeURIComponent(rootSlug)}&subcategory=${encodeURIComponent(slug)}`
                    } else if (slug) {
                        href = `${productsIndexUrl.value}?category=${encodeURIComponent(slug)}`
                    } else if (crumb.id) {
                        href = `${productsIndexUrl.value}?categories=${encodeURIComponent(crumb.id)}`
                    } else {
                        href = productsIndexUrl.value
                    }
                }

                return {
                    key: crumb.id || slug || idx,
                    title,
                    href,
                }
            })
            .filter(Boolean)
    }

    if (currentCategory.value) {
        return [
            {
                key: currentCategoryObj.value?.id || currentCategorySlug.value || 'category',
                title: currentCategory.value,
                href: categoryUrl.value,
            },
        ]
    }

    return []
})

const galleryImages = computed(() => {
    const sanitizeItemForLocale = (item) => {
        if (typeof item === 'string') {
            return item.trim() !== '' ? item : null
        }
        if (item && typeof item === 'object') {
            const u = item.url || item.path || ''
            if (typeof u !== 'string' || u.trim() === '') return null
            if (!isEn.value) return item

            const alt = typeof item.alt === 'string' ? item.alt.trim() : ''
            const title = typeof item.title === 'string' ? item.title.trim() : ''
            const needsCleanAlt = alt && (hasVietnameseDiacritics(alt) || (currentTitle.value && alt !== currentTitle.value))
            const needsCleanTitle = title && hasVietnameseDiacritics(title)
            if (!needsCleanAlt && !needsCleanTitle) return item

            return {
                ...item,
                alt: needsCleanAlt ? '' : item.alt,
                title: needsCleanTitle ? '' : item.title,
            }
        }
        return null
    }

    if (Array.isArray(props.product?.images) && props.product.images.length > 0) {
        const validImages = props.product.images.map(sanitizeItemForLocale).filter(Boolean)
        if (validImages.length > 0) return validImages
    }
    if (props.product?.image) {
        const validMain = [props.product.image].map(sanitizeItemForLocale).filter(Boolean)
        if (validMain.length > 0) return validMain
    }
    return []
})

const ogImageUrl = computed(() => {
    const first = galleryImages.value[0]
    if (!first) return ''
    if (typeof first === 'string') return first.trim()
    return String(first.url || first.path || '').trim()
})

const pdpMetadata = computed(() => props.product?.pdp_metadata || {})

const productSpecifications = computed(() => {
    const candidates = [props.product?.specifications, pdpMetadata.value?.specifications]
    for (const list of candidates) {
        if (Array.isArray(list) && list.length > 0) {
            return list
                .map((row) => {
                    if (!row || typeof row !== 'object') return null
                    const label = pickNonEmpty(row.label, row.name, row.title)
                    const value = pickNonEmpty(row.value, row.val, row.content)
                    if (!label || !value) return null
                    return { label, value }
                })
                .filter(Boolean)
        }
    }
    return []
})

const highlightSpecs = computed(() => {
    const filtered = productSpecifications.value.filter(
        (spec) => !/(mã sản phẩm|\bsku\b|part number)/i.test(spec.label)
    )
    if (filtered.length === 0) return []

    const picked = []
    const usedIndices = new Set()

    const pitchIndex = filtered.findIndex((spec) => /bước xích|pitch/i.test(spec.label))
    if (pitchIndex !== -1) {
        picked.push(filtered[pitchIndex])
        usedIndices.add(pitchIndex)
    }

    const tensileIndex = filtered.findIndex(
        (spec, idx) => !usedIndices.has(idx) && /độ bền kéo|lực kéo đứt|q\s*min/i.test(spec.label)
    )
    if (tensileIndex !== -1) {
        picked.push(filtered[tensileIndex])
        usedIndices.add(tensileIndex)
    }

    for (let i = 0; i < filtered.length && picked.length < 2; i += 1) {
        if (!usedIndices.has(i)) {
            picked.push(filtered[i])
            usedIndices.add(i)
        }
    }

    return picked.slice(0, 2)
})

const resolvedSku = computed(() => pickNonEmpty(props.product?.sku) || '—')

const resolvedBrand = computed(
    () =>
        pickNonEmpty(
            props.product?.brand_name,
            props.product?.brand?.title,
            props.product?.brand?.name,
            pdpMetadata.value?.brand_name,
            props.pdpDefaults?.brand
        ) || '—'
)

const resolvedOrigin = computed(
    () =>
        pickNonEmpty(
            props.product?.origin,
            pdpMetadata.value?.origin,
            props.pdpDefaults?.origin
        ) || '—'
)

const resolvedWarranty = computed(
    () =>
        pickNonEmpty(
            props.product?.warranty,
            pdpMetadata.value?.warranty,
            props.pdpDefaults?.warranty
        ) || '—'
)

const resolvedCondition = computed(
    () =>
        pickNonEmpty(
            props.product?.condition,
            pdpMetadata.value?.condition,
            props.pdpDefaults?.condition
        ) || '—'
)

const resolvedStockStatus = computed(
    () =>
        pickNonEmpty(
            props.product?.stock_status,
            pdpMetadata.value?.stock_status,
            props.pdpDefaults?.stock_status
        ) || '—'
)

const resolvedTechnicalSupport = computed(
    () =>
        pickNonEmpty(
            props.product?.technical_support,
            pdpMetadata.value?.technical_support,
            props.pdpDefaults?.technical_support
        ) || '—'
)

const resolvedFaqs = computed(() => {
    if (Array.isArray(props.faqs) && props.faqs.length > 0) {
        return props.faqs
    }
    const pageFaqs = pageProps.value?.data?.faqs
    return Array.isArray(pageFaqs) ? pageFaqs : []
})

const relatedProducts = computed(() => {
    if (Array.isArray(props.related_products) && props.related_products.length > 0) {
        return props.related_products
    }
    const pageRelated = pageProps.value?.data?.related_products
    return Array.isArray(pageRelated) ? pageRelated : []
})

const productJsonLd = computed(() => {
    if (!currentTitle.value) return null

    const images = galleryImages.value
        .map((img) => (typeof img === 'string' ? img : img?.url || img?.path || ''))
        .filter(Boolean)

    const schema = {
        '@context': 'https://schema.org',
        '@type': 'Product',
        name: currentTitle.value,
        description: currentDescription.value,
    }

    if (images.length > 0) {
        schema.image = images
    }

    const sku = pickNonEmpty(props.product?.sku)
    if (sku) {
        schema.sku = sku
    }

    const resolvedJsonBrand = resolvedBrand.value !== '—' ? resolvedBrand.value : brandName.value
    if (resolvedJsonBrand) {
        schema.brand = {
            '@type': 'Brand',
            name: resolvedJsonBrand,
        }
    }

    const rawPrice = props.product?.price
    const hasPrice =
        rawPrice !== null &&
        rawPrice !== undefined &&
        rawPrice !== '' &&
        !Number.isNaN(Number(rawPrice)) &&
        Number(rawPrice) > 0

    if (hasPrice) {
        const currentUrl =
            typeof window !== 'undefined'
                ? window.location.href
                : pageProps.value?.route?.url || ''
        const offer = {
            '@type': 'Offer',
            priceCurrency: 'VND',
            price: String(Number(rawPrice)),
            itemCondition: 'https://schema.org/NewCondition',
            availability: 'https://schema.org/InStock',
        }
        if (currentUrl) {
            offer.url = currentUrl
        }
        if (brandName.value) {
            offer.seller = {
                '@type': 'Organization',
                name: brandName.value,
            }
        }
        schema.offers = offer
    }

    return schema
})

const breadcrumbJsonLd = computed(() => {
    if (!currentTitle.value) return null

    const toAbsolute = (path) => {
        if (!path) return ''
        if (/^https?:\/\//i.test(path)) return path
        return siteOrigin.value
            ? `${siteOrigin.value}${path.startsWith('/') ? path : `/${path}`}`
            : path
    }

    const currentUrl =
        typeof window !== 'undefined'
            ? window.location.href
            : pageProps.value?.route?.url ||
              toAbsolute(
                  props.product?.slug
                      ? `${productsIndexUrl.value}/${props.product.slug}`
                      : productsIndexUrl.value
              )

    const elements = [
        {
            '@type': 'ListItem',
            position: 1,
            name: isEn.value ? 'Home' : 'Trang chủ',
            item: toAbsolute(homeUrl.value),
        },
        {
            '@type': 'ListItem',
            position: 2,
            name: isEn.value ? 'Products' : 'Sản phẩm',
            item: toAbsolute(productsIndexUrl.value),
        },
    ]

    breadcrumbCategories.value.forEach((crumb) => {
        elements.push({
            '@type': 'ListItem',
            position: elements.length + 1,
            name: crumb.title,
            item: toAbsolute(crumb.href),
        })
    })

    elements.push({
        '@type': 'ListItem',
        position: elements.length + 1,
        name: currentTitle.value,
        item: currentUrl,
    })

    return {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: elements,
    }
})

function handleRequestQuote() {
    openQuoteModal({
        source: 'product_detail',
        product: currentTitle.value,
        sku: pickNonEmpty(props.product?.sku),
        category: currentCategory.value,
    })
}
</script>
