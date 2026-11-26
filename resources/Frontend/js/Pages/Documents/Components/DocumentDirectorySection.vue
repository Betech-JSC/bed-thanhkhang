<template>
    <section
        id="documents-directory-section"
        aria-labelledby="documents-directory-heading"
        class="w-full bg-[#FAFAFA] py-8 md:py-16"
    >
        <div class="app-container-narrow flex flex-col gap-5 md:gap-8">
            <!-- Section Header (Figma 1264:49131 — Blue square on LEFT of heading) -->
            <div class="flex items-center gap-2 md:gap-[10px]">
                <span
                    class="w-[10px] h-[10px] md:w-3 md:h-3 rounded-[2px] bg-[#005EA4] shrink-0"
                    aria-hidden="true"
                />
                <h2
                    id="documents-directory-heading"
                    class="text-[16px] leading-[24px] md:text-[18px] md:leading-[29px] font-bold text-[#005EA4]"
                >
                    {{ tt('Thư viện tài liệu kỹ thuật tại Thành Khang') }}
                </h2>
            </div>

            <!-- Mode A (documents from CMS) & Mode B (product_categories fallback from DB) — Figma 1264:49136 -->
            <div
                v-if="directoryItems.length > 0"
                class="flex flex-col items-center gap-6 md:gap-[40px]"
            >
                <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6">
                    <component
                        :is="getWrapperComponent(item.wrapperType)"
                        v-for="item in directoryItems"
                        :key="item.key"
                        v-bind="item.wrapperAttrs"
                        :class="[CARD_BASE_CLASS, item.hasLink ? CARD_INTERACTIVE_CLASS : '']"
                    >
                        <!-- Left Square Thumbnail (Figma 1264:49138 — 119x119 Desktop, 161px card height) -->
                        <div
                            class="w-[88px] sm:w-[104px] md:w-[119px] aspect-square rounded-[4px] bg-[#F1F1F1] overflow-hidden shrink-0"
                        >
                            <img
                                v-if="item.imageUrl && !failedImageKeys[item.key]"
                                :src="item.imageUrl"
                                :alt="item.imageAlt"
                                width="119"
                                height="119"
                                loading="lazy"
                                decoding="async"
                                class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                                @error="handleImageError(item.key)"
                            />
                        </div>

                        <!-- Card Content -->
                        <div class="min-w-0 flex-1 self-stretch flex flex-col gap-1">
                            <h3
                                class="text-[14px] leading-[22px] font-bold text-[#00284B] uppercase transition-colors line-clamp-2 break-words"
                                :class="item.hasLink ? 'group-hover:text-[#005EA4]' : ''"
                            >
                                {{ item.title }}
                            </h3>

                            <p
                                v-if="item.description"
                                class="text-[13px] leading-[20px] font-normal text-[#4B5565] line-clamp-3 break-words"
                            >
                                {{ item.description }}
                            </p>
                        </div>
                    </component>
                </div>

                <!-- CTA Button (Figma 1329:53128) -->
                <Link
                    :href="documentsLibraryHref"
                    data-testid="documents-view-all-btn"
                    class="w-full sm:w-auto min-w-[184px] h-[46px] px-6 py-[10px] rounded-[4px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] text-[15px] md:text-[16px] leading-[26px] font-medium inline-flex items-center justify-center whitespace-nowrap transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                >
                    {{ tt('Xem tất cả tài liệu') }}
                </Link>
            </div>

            <!-- Mode C: Compact Empty State when both documents and categories are empty -->
            <EmptyState
                v-else
                variant="light"
                heading-tag="h3"
                :title="tt('Thư viện tài liệu kỹ thuật đang được cập nhật')"
                :description="tt('Vui lòng liên hệ đội ngũ kỹ sư Thành Khang Tecsat để nhận catalogue và data sheet thông số kỹ thuật mới nhất.')"
                :primary-action-text="tt('Liên hệ kỹ thuật')"
                :primary-action-href="contactHref"
            />
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance, reactive, watch } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'
import EmptyState from '@/Components/EmptyState.vue'

const CARD_BASE_CLASS =
    'group bg-white border border-[#E3E8EF] rounded-[8px] p-4 md:p-[20px] min-h-[124px] md:min-h-[161px] flex items-start gap-3 md:gap-4 transition-colors'
const CARD_INTERACTIVE_CLASS =
    'hover:border-[#005EA4] hover:shadow-xs focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]'

const DOCUMENT_FILE_EXT_RE = /\.(pdf|docx?|xlsx?|pptx?|zip|rar|7z|dwg|dxf|step|stp|iges|igs)(\?.*)?$/i

const props = defineProps({
    documents: {
        type: Array,
        default: () => [],
    },
    productCategories: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    documentsLibraryUrl: {
        type: String,
        default: '',
    },
})

const instance = getCurrentInstance()
const { isEn } = useTranslate()

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

const contactHref = computed(() =>
    safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he')
)

const productsIndexHref = computed(() =>
    safeRoute('products.index', undefined, isEn.value ? '/en/products' : '/san-pham')
)

const documentsLibraryHref = computed(() => {
    const fromProp = normalizeActionUrl(firstNonEmptyString(props.documentsLibraryUrl))
    if (fromProp) return fromProp
    return safeRoute(
        'documents.library',
        undefined,
        isEn.value ? '/en/documents/library' : '/tai-lieu/thu-vien'
    )
})

const failedImageKeys = reactive({})

watch(
    () => [props.documents, props.productCategories, props.categories],
    () => {
        Object.keys(failedImageKeys).forEach((key) => {
            delete failedImageKeys[key]
        })
    }
)

function handleImageError(key) {
    if (key !== undefined && key !== null) {
        failedImageKeys[key] = true
    }
}

function firstNonEmptyString(...values) {
    for (const val of values) {
        if (typeof val === 'string' && val.trim().length > 0) {
            return val.trim()
        }
    }
    return ''
}

function stripHtml(value) {
    if (typeof value !== 'string') return ''
    return value
        .replace(/<[^>]*>/g, ' ')
        .replace(/&nbsp;/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

function normalizeLocalOriginUrl(url, relativePrefixPattern) {
    if (typeof url !== 'string') return ''
    const trimmed = url.trim()
    if (!trimmed) return ''
    if (trimmed.includes('/static/assets/')) {
        return trimmed.replace(/^https?:\/\/[^/]+/, '').replace('/static/', '/')
    }
    if (/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?(\/|$)/i.test(trimmed)) {
        return trimmed.replace(/^https?:\/\/[^/]+/i, '') || '/'
    }
    if (relativePrefixPattern.test(trimmed)) {
        return `/${trimmed}`
    }
    return trimmed
}

function normalizeImageUrl(url) {
    return normalizeLocalOriginUrl(url, /^(assets|images)\//i)
}

function normalizeActionUrl(url) {
    if (typeof url !== 'string' || url.trim() === '#') return ''
    return normalizeLocalOriginUrl(url, /^(assets|images|storage)\//i)
}

function isInertiaHref(href) {
    return Boolean(href && href.startsWith('/') && !href.startsWith('//'))
}

function getWrapperComponent(wrapperType) {
    if (wrapperType === 'inertia') return Link
    if (wrapperType === 'download' || wrapperType === 'external') return 'a'
    return 'article'
}

const normalizedDocuments = computed(() => {
    if (!Array.isArray(props.documents)) return []

    return props.documents
        .map((doc, idx) => {
            if (!doc || typeof doc !== 'object') return null

            const title = firstNonEmptyString(doc.title, doc.name)
            if (!title) return null

            const cleanDescription = stripHtml(
                firstNonEmptyString(doc.description, doc.summary, doc.excerpt)
            )

            const rawImage =
                typeof doc.image === 'string'
                    ? doc.image
                    : doc.image?.url || doc.thumbnail?.url || doc.thumbnail || doc.icon?.url || ''
            const imageUrl = normalizeImageUrl(rawImage)
            const imageAlt = firstNonEmptyString(
                typeof doc.image === 'object' ? doc.image?.alt : '',
                title
            )

            const explicitDownloadUrl = normalizeActionUrl(
                firstNonEmptyString(doc.download_url, doc.file_url, doc.download)
            )
            const candidateLink = normalizeActionUrl(
                firstNonEmptyString(doc.detail_url, doc.url, doc.link)
            )

            const downloadUrl =
                explicitDownloadUrl ||
                (DOCUMENT_FILE_EXT_RE.test(candidateLink) ? candidateLink : '')
            const detailUrl = !downloadUrl ? candidateLink : ''

            let wrapperType = 'static'
            let wrapperAttrs = {}

            if (downloadUrl) {
                wrapperType = 'download'
                wrapperAttrs = {
                    href: downloadUrl,
                    target: '_blank',
                    rel: 'noopener noreferrer',
                }
            } else if (detailUrl) {
                if (isInertiaHref(detailUrl)) {
                    wrapperType = 'inertia'
                    wrapperAttrs = { href: detailUrl }
                } else {
                    wrapperType = 'external'
                    wrapperAttrs = {
                        href: detailUrl,
                        target: '_blank',
                        rel: 'noopener noreferrer',
                    }
                }
            }

            return {
                key: `doc-${doc.id ?? idx}-${doc.slug ?? title}`,
                id: doc.id ?? idx,
                title,
                description: cleanDescription,
                imageUrl,
                imageAlt,
                downloadUrl,
                detailUrl,
                hasLink: wrapperType !== 'static',
                wrapperType,
                wrapperAttrs,
            }
        })
        .filter(Boolean)
})

const rawCategories = computed(() => {
    if (Array.isArray(props.productCategories) && props.productCategories.length > 0) {
        return props.productCategories
    }
    if (Array.isArray(props.categories) && props.categories.length > 0) {
        return props.categories
    }
    return []
})

const categoryCards = computed(() => {
    return rawCategories.value
        .map((cat, idx) => {
            if (!cat || typeof cat !== 'object') return null

            const title = firstNonEmptyString(cat.title, cat.name)
            if (!title) return null

            const slug = firstNonEmptyString(cat.slug)
            const rawLibraryUrl = normalizeActionUrl(firstNonEmptyString(cat.library_url))
            const rawUrl = normalizeActionUrl(firstNonEmptyString(cat.url))
            const href =
                rawLibraryUrl ||
                rawUrl ||
                (slug
                    ? `${documentsLibraryHref.value}?category=${encodeURIComponent(slug)}`
                    : documentsLibraryHref.value)

            const rawDesc = stripHtml(firstNonEmptyString(cat.description))
            const childrenSummary = Array.isArray(cat.children)
                ? cat.children
                      .map((child) => firstNonEmptyString(child?.title, child?.name))
                      .filter(Boolean)
                      .slice(0, 3)
                      .join(', ')
                : ''
            const description = rawDesc || childrenSummary

            const rawImage = typeof cat.image === 'string' ? cat.image : cat.image?.url || ''
            const imageUrl = normalizeImageUrl(rawImage)
            const imageAlt = firstNonEmptyString(
                typeof cat.image === 'object' ? cat.image?.alt : '',
                title
            )

            const isInternal = isInertiaHref(href)

            return {
                key: `cat-${cat.id ?? idx}-${slug || title}`,
                id: cat.id ?? idx,
                title,
                description,
                href,
                imageUrl,
                imageAlt,
                hasLink: true,
                wrapperType: isInternal ? 'inertia' : 'external',
                wrapperAttrs: isInternal
                    ? { href }
                    : { href, target: '_blank', rel: 'noopener noreferrer' },
            }
        })
        .filter(Boolean)
})

const directoryItems = computed(() => {
    if (normalizedDocuments.value.length > 0) {
        return normalizedDocuments.value
    }
    return categoryCards.value
})
</script>
