<template>
    <div class="bg-[#FAFAFA] text-[#364152] min-h-screen overflow-x-clip selection:bg-primary selection:text-white">
        <!-- SEO Head -->
        <Head>
            <title>{{ pageTitle }}</title>
            <meta name="description" :content="pageDescription" />
            <meta property="og:title" :content="pageTitle" />
            <meta property="og:description" :content="pageDescription" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- 1. Hero Banner (Figma Node 979:30380 — CMS BANNER_PRODUCT_HERO) -->
        <ProductHeroBanner :hero-banner="heroBanner" />

        <!-- 2. Product Catalog Section (Figma Node 979:30392) -->
        <section class="w-full bg-[#FAFAFA] py-8 md:py-16 lg:pt-[48px] lg:pb-[80px]">
            <div class="app-container-narrow">
                <EmptyState
                    v-if="isEmptyCatalog"
                    variant="light"
                    :heading-tag="hasHeroBannerContent ? 'h2' : 'h1'"
                    :title="tt('Danh mục sản phẩm đang được cập nhật')"
                    :description="tt('Quý khách vui lòng quay lại sau hoặc liên hệ trực tiếp để được đội ngũ kỹ sư hỗ trợ thông tin linh kiện và báo giá.')"
                    :primary-action-text="tt('Liên hệ tư vấn')"
                    :primary-action-href="contactHref"
                />

                <div
                    v-else
                    class="flex flex-col lg:flex-row lg:justify-between gap-6 lg:gap-[32px] items-start"
                >
                    <!-- Block 2A: Desktop Sidebar Filter (Figma Node 979:30394 — 264px) -->
                    <aside
                        v-if="hasCategories"
                        class="hidden lg:block lg:w-[264px] shrink-0 lg:sticky lg:top-[96px]"
                    >
                        <ProductFilter
                            :categories="categories"
                            :selected-ids="selectedCategoryIds"
                            @update:selected-ids="handleSelectedIdsUpdate"
                        />
                    </aside>

                    <!-- Mobile Off-Canvas Filter Drawer (< lg) -->
                    <Teleport v-if="hasCategories" to="body">
                        <Transition
                            enter-active-class="transition-opacity duration-250 ease-out"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                            leave-active-class="transition-opacity duration-200 ease-in"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                        >
                            <div
                                v-if="isMobileFilterOpen"
                                class="fixed inset-0 z-[1040] lg:hidden bg-[#00284B]/50 backdrop-blur-[2px]"
                                aria-hidden="true"
                                @click="isMobileFilterOpen = false"
                            />
                        </Transition>

                        <Transition
                            enter-active-class="transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                            enter-from-class="-translate-x-full"
                            enter-to-class="translate-x-0"
                            leave-active-class="transition-transform duration-200 ease-[cubic-bezier(0.22,1,0.36,1)]"
                            leave-from-class="translate-x-0"
                            leave-to-class="-translate-x-full"
                        >
                            <aside
                                v-if="isMobileFilterOpen"
                                role="dialog"
                                aria-modal="true"
                                :aria-label="tt('Bộ lọc sản phẩm')"
                                class="fixed inset-y-0 left-0 z-[1050] lg:hidden w-[85vw] max-w-[320px] bg-[#FAFAFA] border-r border-[#E3E8EF] shadow-2xl h-full overflow-y-auto p-5"
                            >
                                <ProductFilter
                                    :categories="categories"
                                    :selected-ids="selectedCategoryIds"
                                    :show-mobile-header="true"
                                    @update:selected-ids="handleSelectedIdsUpdate"
                                    @close="isMobileFilterOpen = false"
                                />
                            </aside>
                        </Transition>
                    </Teleport>

                    <!-- Right Column: Product Results (Figma Node 979:30446 — 856px) -->
                    <div
                        id="product-catalog-section"
                        ref="productCatalogTop"
                        :class="[
                            'w-full min-w-0 flex flex-col gap-6',
                            hasCategories ? 'lg:w-[856px] lg:flex-1' : '',
                        ]"
                    >
                        <!-- Block 2B: Toolbar & Active Filter Chips (Figma Node 979:30447) -->
                        <div class="w-full flex flex-col gap-3">
                            <!-- Top Row: Result Count + Mobile Filter Button + Sort Dropdown (Figma 979:30448) -->
                            <div class="w-full min-h-[40px] flex flex-wrap items-center justify-between gap-3">
                                <!-- Result Count (Figma 979:30449) -->
                                <p class="text-[14px] leading-[22px] font-normal text-[#364152] font-sans">
                                    <template v-if="filteredProducts.length > 0">
                                        {{ tt('Hiển thị') }} {{ displayRangeStart }}–{{ displayRangeEnd }} {{ tt('trên') }} {{ filteredProducts.length }} {{ tt('sản phẩm') }}
                                    </template>
                                    <template v-else>
                                        {{ tt('Hiển thị') }} 0 {{ tt('sản phẩm') }}
                                    </template>
                                </p>

                                <!-- Controls: Mobile Filter Trigger + Sort Dropdown -->
                                <div class="flex items-center gap-2 sm:gap-3 ml-auto shrink-0">
                                    <!-- Mobile Filter Trigger (< lg) -->
                                    <button
                                        v-if="hasCategories"
                                        type="button"
                                        @click="isMobileFilterOpen = true"
                                        class="lg:hidden min-h-[44px] px-[13px] rounded-[4px] border border-[#CDD5DF] bg-white hover:bg-[#EEF2F6] inline-flex items-center gap-2 text-[14px] leading-[22px] font-medium text-[#364152] font-sans transition-colors cursor-pointer select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                                    >
                                        <img
                                            src="/assets/figma/san-pham/filter-lines.svg"
                                            alt=""
                                            aria-hidden="true"
                                            width="20"
                                            height="20"
                                            class="w-5 h-5 shrink-0"
                                        />
                                        <span>{{ tt('Bộ lọc') }}</span>
                                        <span
                                            v-if="selectedCategoryIds.length > 0"
                                            class="min-w-[18px] h-[18px] px-1 rounded-[2px] bg-[#005EA4] text-[#FAFAFA] text-[11px] leading-[18px] font-semibold text-center"
                                        >
                                            {{ selectedCategoryIds.length }}
                                        </span>
                                    </button>

                                    <!-- Sort Dropdown (Figma 979:30450) -->
                                    <div class="relative shrink-0" ref="sortDropdownRef">
                                        <button
                                            type="button"
                                            @click="isSortOpen = !isSortOpen"
                                            class="min-w-[141px] min-h-[44px] lg:min-h-0 lg:h-[40px] px-[13px] py-[9px] rounded-[4px] border border-[#CDD5DF] bg-white hover:bg-[#EEF2F6] inline-flex items-center justify-between gap-2 text-[14px] leading-[22px] font-medium text-[#364152] font-sans transition-colors cursor-pointer select-none whitespace-nowrap focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                                            aria-haspopup="listbox"
                                            :aria-expanded="isSortOpen"
                                        >
                                            <span>{{ sortButtonLabel }}</span>
                                            <img
                                                src="/assets/figma/san-pham/filter-lines.svg"
                                                alt=""
                                                aria-hidden="true"
                                                width="20"
                                                height="20"
                                                class="w-5 h-5 shrink-0"
                                            />
                                        </button>

                                        <Transition
                                            enter-active-class="transition-all duration-150 ease-out"
                                            enter-from-class="opacity-0 -translate-y-1"
                                            enter-to-class="opacity-100 translate-y-0"
                                            leave-active-class="transition-all duration-100 ease-in"
                                            leave-from-class="opacity-100 translate-y-0"
                                            leave-to-class="opacity-0 -translate-y-1"
                                        >
                                            <div
                                                v-if="isSortOpen"
                                                class="absolute right-0 mt-1.5 min-w-[180px] bg-white border border-[#CDD5DF] rounded-[4px] shadow-md py-1 z-30 overflow-hidden"
                                                role="listbox"
                                            >
                                                <button
                                                    v-for="opt in sortOptions"
                                                    :key="opt.value"
                                                    type="button"
                                                    @click="selectSort(opt.value)"
                                                    class="w-full min-h-[44px] lg:min-h-[40px] text-left px-3.5 py-2 text-[14px] leading-[22px] flex items-center justify-between gap-2 transition-colors cursor-pointer"
                                                    :class="
                                                        sortBy === opt.value
                                                            ? 'bg-[#EDF6FF] text-[#005EA4] font-semibold'
                                                            : 'text-[#364152] hover:bg-[#EEF2F6]'
                                                    "
                                                    role="option"
                                                    :aria-selected="sortBy === opt.value"
                                                >
                                                    <span>{{ opt.label }}</span>
                                                    <span
                                                        v-if="sortBy === opt.value"
                                                        class="w-1.5 h-1.5 rounded-full bg-[#005EA4] shrink-0"
                                                        aria-hidden="true"
                                                    />
                                                </button>
                                            </div>
                                        </Transition>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Filter Chips Row & Reset Button (Figma 979:30453) -->
                            <div
                                v-if="hasActiveFilters"
                                class="w-full flex flex-wrap items-center gap-2 select-none"
                            >
                                <!-- Reset All Button (Figma 979:30457) -->
                                <button
                                    type="button"
                                    @click="resetAllFilters"
                                    class="min-h-[44px] lg:min-h-0 lg:h-[40px] px-[13px] py-[9px] rounded-[4px] border border-[#CDD5DF] bg-transparent hover:bg-[#EEF2F6] inline-flex items-center justify-between gap-2 text-[14px] leading-[22px] font-medium text-[#364152] font-sans transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                                >
                                    <span>{{ tt('Đặt lại tất cả') }}</span>
                                    <img
                                        src="/assets/figma/san-pham/refresh-ccw.svg"
                                        alt=""
                                        aria-hidden="true"
                                        width="20"
                                        height="20"
                                        class="w-5 h-5 shrink-0"
                                    />
                                </button>

                                <!-- Active Search Keyword Chip -->
                                <div
                                    v-if="trimmedSearch"
                                    class="min-h-[44px] lg:min-h-0 lg:h-[38px] px-[14px] py-[8px] rounded-[4px] bg-[#E3E8EF] inline-flex items-center gap-2 text-[14px] leading-[22px] text-[#364152] font-sans max-w-full"
                                >
                                    <span class="truncate max-w-[220px]">
                                        {{ tt('Từ khoá:') }} "{{ trimmedSearch }}"
                                    </span>
                                    <button
                                        type="button"
                                        @click="clearSearch"
                                        class="min-w-[44px] min-h-[44px] -my-2 -mr-2 lg:min-w-0 lg:min-h-0 lg:w-5 lg:h-5 lg:my-0 lg:mr-0 inline-flex items-center justify-center rounded-[2px] hover:bg-[#CDD5DF]/70 transition-colors cursor-pointer shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                                        :aria-label="tt('Xoá tìm kiếm')"
                                        :title="tt('Xoá tìm kiếm')"
                                    >
                                        <img
                                            src="/assets/figma/san-pham/chip-close-x.svg"
                                            alt=""
                                            aria-hidden="true"
                                            width="20"
                                            height="20"
                                            class="w-5 h-5"
                                        />
                                    </button>
                                </div>

                                <!-- Active Category Chips (Figma 979:30454) -->
                                <div
                                    v-for="chip in activeCategoryChips"
                                    :key="chip.key"
                                    class="min-h-[44px] lg:min-h-0 lg:h-[38px] px-[14px] py-[8px] rounded-[4px] bg-[#E3E8EF] inline-flex items-center gap-2 text-[14px] leading-[22px] text-[#364152] font-sans max-w-full"
                                >
                                    <span class="truncate max-w-[240px]">{{ chip.label }}</span>
                                    <button
                                        type="button"
                                        @click="removeChip(chip)"
                                        class="min-w-[44px] min-h-[44px] -my-2 -mr-2 lg:min-w-0 lg:min-h-0 lg:w-5 lg:h-5 lg:my-0 lg:mr-0 inline-flex items-center justify-center rounded-[2px] hover:bg-[#CDD5DF]/70 transition-colors cursor-pointer shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                                        :aria-label="`${tt('Xoá bộ lọc')} ${chip.label}`"
                                        :title="tt('Xoá bộ lọc')"
                                    >
                                        <img
                                            src="/assets/figma/san-pham/chip-close-x.svg"
                                            alt=""
                                            aria-hidden="true"
                                            width="20"
                                            height="20"
                                            class="w-5 h-5"
                                        />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Block 2C: Product Grid (Figma 979:30460 — 3x3 Desktop, 2-col Mobile) -->
                        <div
                            v-if="paginatedProducts.length > 0"
                            class="w-full grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6 lg:gap-x-[32px] lg:gap-y-[24px]"
                        >
                            <ProductCard
                                v-for="(product, idx) in paginatedProducts"
                                :key="product.id || product.slug || idx"
                                :product="product"
                            />
                        </div>

                        <!-- Empty State (Re-use @/Components/EmptyState.vue variant="light") -->
                        <div v-else @click.capture="handleEmptyStateClick">
                            <EmptyState
                                variant="light"
                                heading-tag="h3"
                                :title="tt('Không tìm thấy sản phẩm phù hợp')"
                                :description="tt('Rất tiếc, không có sản phẩm nào đáp ứng các tiêu chí lọc hiện tại. Bạn vui lòng thử thay đổi từ khoá tìm kiếm hoặc đặt lại bộ lọc.')"
                                :primary-action-text="tt('Liên hệ tư vấn')"
                                :primary-action-href="contactHref"
                                :secondary-action-text="hasActiveFilters ? tt('Đặt lại tất cả') : ''"
                                :secondary-action-href="hasActiveFilters ? productsIndexHref : ''"
                            />
                        </div>

                        <!-- Block 2D: Pagination (Figma 979:30473) -->
                        <ProductPagination
                            v-if="filteredProducts.length > 0"
                            :current-page="currentPage"
                            :total-pages="totalPages"
                            @change-page="handlePageChange"
                        />
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
    name: 'ProductIndex',
    layout: MainLayout,
}
</script>

<script setup>
import { computed, getCurrentInstance, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useTranslate } from '@/composables/useTranslate.js'
import ProductHeroBanner from './Components/ProductHeroBanner.vue'
import ProductFilter from './Components/ProductFilter.vue'
import ProductCard from './Components/ProductCard.vue'
import ProductPagination from './Components/ProductPagination.vue'
import EmptyState from '@/Components/EmptyState.vue'

const PAGE_SIZE = 9

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    heroBanner: {
        type: Object,
        default: null,
    },
    seo: {
        type: Object,
        default: null,
    },
})

const instance = getCurrentInstance()
const { translate, currentLocale, isEn, page } = useTranslate()

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

const productsIndexHref = computed(() =>
    safeRoute('products.index', undefined, isEn.value ? '/en/products' : '/san-pham')
)

const contactHref = computed(() =>
    safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he')
)

const pageTitle = computed(() => {
    const seoTitle = props.seo?.seo_meta_title || props.seo?.title
    if (seoTitle) return seoTitle
    return isEn.value
        ? 'Industrial Equipment & Escalator Parts Catalog - Thành Khang Tecsat'
        : translate('Danh mục Sản phẩm & Phụ tùng Công nghiệp - Thành Khang Tecsat')
})

const pageDescription = computed(() => {
    const seoDesc = props.seo?.seo_meta_description || props.seo?.description
    if (seoDesc) return seoDesc
    return isEn.value
        ? 'Explore industrial chains, escalator spare parts, car parking elevator chains, and baggage conveyor systems by Thành Khang Tecsat.'
        : translate('Khám phá danh mục linh kiện thang cuốn, thang nâng ô tô, xích tải công nghiệp Universal Chains và hệ thống băng tải từ Thành Khang Tecsat.')
})

// ---------------------------------------------------------------------------
// 3-Tier Category Tree Indexer (L0 -> L1 -> L2 from props.categories)
// ---------------------------------------------------------------------------
function extractRawChildren(node) {
    if (!node || typeof node !== 'object') return []
    if (Array.isArray(node.children) && node.children.length > 0) return node.children
    if (Array.isArray(node.nodes) && node.nodes.length > 0) return node.nodes
    if (Array.isArray(node.subcategories) && node.subcategories.length > 0) return node.subcategories
    return []
}

const categoryIndex = computed(() => {
    const nodeById = new Map()
    const nodeBySlug = new Map()
    const roots = []

    function buildNode(raw, level, parentId, rootId) {
        if (!raw || typeof raw !== 'object') return null
        const id = raw.id
        const title = String(raw.title || raw.name || '').trim()
        if (!id || !title) return null

        const resolvedId = Number.isNaN(Number(id)) ? String(id) : Number(id)
        const resolvedRootId = level === 0 ? resolvedId : rootId
        const slug = String(raw.slug || raw.seo_slug || resolvedId).trim()
        const rawChildren = extractRawChildren(raw)
        const children = rawChildren
            .map((child) => buildNode(child, level + 1, resolvedId, resolvedRootId))
            .filter(Boolean)

        const leafIds =
            children.length > 0 ? children.flatMap((c) => c.leafIds) : [resolvedId]
        const descendantIds = [resolvedId, ...children.flatMap((c) => c.descendantIds)]
        const descendantSlugs = [
            slug.toLowerCase(),
            ...children.flatMap((c) => c.descendantSlugs),
        ]

        const entry = {
            id: resolvedId,
            slug,
            title,
            level,
            parentId,
            rootId: resolvedRootId,
            children,
            leafIds,
            descendantIds,
            descendantSlugs,
        }

        nodeById.set(String(resolvedId), entry)
        if (slug) {
            nodeBySlug.set(slug.toLowerCase(), entry)
        }
        return entry
    }

    if (Array.isArray(props.categories)) {
        props.categories.forEach((rawRoot) => {
            const built = buildNode(rawRoot, 0, null, null)
            if (built) roots.push(built)
        })
    }

    return { roots, nodeById, nodeBySlug }
})

function findNodeInSubtree(rootNode, query) {
    if (!rootNode || !query) return null
    const q = String(query).trim().toLowerCase()
    if (String(rootNode.id).toLowerCase() === q || rootNode.slug.toLowerCase() === q) {
        return rootNode
    }
    for (const child of rootNode.children || []) {
        const hit = findNodeInSubtree(child, q)
        if (hit) return hit
    }
    return null
}

function findCategoryNode(identifier, preferredRoot = null) {
    if (!identifier) return null
    const key = String(identifier).trim().toLowerCase()
    if (!key) return null

    if (preferredRoot) {
        const scoped = findNodeInSubtree(preferredRoot, key)
        if (scoped) return scoped
    }

    return (
        categoryIndex.value.nodeBySlug.get(key) ||
        categoryIndex.value.nodeById.get(String(identifier).trim()) ||
        null
    )
}

// ---------------------------------------------------------------------------
// Reactive Filter, Sort & Pagination State
// ---------------------------------------------------------------------------
const selectedCategoryIds = ref([])
const unmatchedCategorySlug = ref('')
const unmatchedSubcategorySlug = ref('')
const searchKeyword = ref('')
const sortBy = ref('popular')
const currentPage = ref(1)
const isMobileFilterOpen = ref(false)
const isSortOpen = ref(false)
const sortDropdownRef = ref(null)
const productCatalogTop = ref(null)

const trimmedSearch = computed(() => String(searchKeyword.value || '').trim())

const sortOptions = computed(() => [
    { value: 'popular', label: translate('Nổi bật nhất') },
    { value: 'newest', label: translate('Mới nhất') },
    { value: 'name_asc', label: translate('Tên A-Z') },
    { value: 'name_desc', label: translate('Tên Z-A') },
])

const currentSortLabel = computed(() => {
    const found = sortOptions.value.find((opt) => opt.value === sortBy.value)
    return found ? found.label : translate('Nổi bật nhất')
})

const sortButtonLabel = computed(() => {
    return sortBy.value === 'popular' ? translate('Sắp xếp theo') : currentSortLabel.value
})

function extractQueryString(urlStr) {
    if (typeof urlStr === 'string' && urlStr.includes('?')) {
        return urlStr.split('?')[1].split('#')[0]
    }
    return ''
}

function initFiltersFromUrl(explicitUrl = null) {
    const pageUrl =
        explicitUrl ??
        (typeof page.url === 'string' ? page.url : page.url?.value) ??
        ''
    let queryStr = extractQueryString(pageUrl)

    if (!queryStr && typeof window !== 'undefined' && window.location?.search) {
        queryStr = window.location.search.replace(/^\?/, '')
    }

    const params = new URLSearchParams(queryStr)
    const searchParam = params.get('search') || params.get('keyword') || params.get('q') || ''
    const categoriesParam = params.get('categories') || ''
    const categoryParam = params.get('category') || ''
    const subcategoryParam = params.get('subcategory') || ''
    const sortParam = params.get('sort') || ''
    const pageParam = parseInt(params.get('page') || '1', 10)

    searchKeyword.value = searchParam.trim()
    unmatchedCategorySlug.value = ''
    unmatchedSubcategorySlug.value = ''

    const nextIds = new Set()

    if (categoriesParam.trim()) {
        categoriesParam
            .split(',')
            .map((s) => s.trim())
            .filter(Boolean)
            .forEach((token) => {
                const node = findCategoryNode(token)
                if (node) {
                    node.leafIds.forEach((id) => nextIds.add(id))
                }
            })
    } else if (subcategoryParam.trim()) {
        const rootNode = categoryParam.trim() ? findCategoryNode(categoryParam.trim()) : null
        const subNode = findCategoryNode(subcategoryParam.trim(), rootNode)
        if (subNode) {
            subNode.leafIds.forEach((id) => nextIds.add(id))
        } else {
            unmatchedCategorySlug.value = categoryParam.trim()
            unmatchedSubcategorySlug.value = subcategoryParam.trim()
        }
    } else if (categoryParam.trim() && categoryParam.trim().toLowerCase() !== 'all') {
        const tokens = categoryParam
            .split(',')
            .map((s) => s.trim())
            .filter(Boolean)
        let matchedAny = false
        tokens.forEach((token) => {
            const node = findCategoryNode(token)
            if (node) {
                matchedAny = true
                node.leafIds.forEach((id) => nextIds.add(id))
            }
        })
        if (!matchedAny) {
            unmatchedCategorySlug.value = categoryParam.trim()
        }
    }

    selectedCategoryIds.value = Array.from(nextIds)

    const validSortValues = new Set(['popular', 'newest', 'name_asc', 'name_desc'])
    sortBy.value = validSortValues.has(sortParam) ? sortParam : 'popular'
    currentPage.value = Number.isFinite(pageParam) && pageParam > 0 ? pageParam : 1
}

initFiltersFromUrl()

function syncBrowserUrl() {
    if (typeof window === 'undefined') return

    const params = new URLSearchParams()
    if (trimmedSearch.value) {
        params.set('search', trimmedSearch.value)
    }

    const selSet = new Set(selectedCategoryIds.value.map(String))
    if (selSet.size > 0) {
        // Check if selection cleanly represents a single L0, single L1, or single L2
        let matchedSingle = false
        for (const root of categoryIndex.value.roots) {
            if (
                root.leafIds.length === selSet.size &&
                root.leafIds.every((id) => selSet.has(String(id)))
            ) {
                params.set('category', root.slug)
                matchedSingle = true
                break
            }
            for (const l1 of root.children) {
                if (
                    l1.leafIds.length === selSet.size &&
                    l1.leafIds.every((id) => selSet.has(String(id)))
                ) {
                    params.set('category', root.slug)
                    params.set('subcategory', l1.slug)
                    matchedSingle = true
                    break
                }
                for (const l2 of l1.children) {
                    if (selSet.size === 1 && selSet.has(String(l2.id))) {
                        params.set('category', root.slug)
                        params.set('subcategory', l2.slug)
                        matchedSingle = true
                        break
                    }
                }
                if (matchedSingle) break
            }
            if (matchedSingle) break
        }
        if (!matchedSingle) {
            params.set('categories', selectedCategoryIds.value.join(','))
        }
    } else if (unmatchedCategorySlug.value || unmatchedSubcategorySlug.value) {
        if (unmatchedCategorySlug.value) params.set('category', unmatchedCategorySlug.value)
        if (unmatchedSubcategorySlug.value) params.set('subcategory', unmatchedSubcategorySlug.value)
    }

    if (sortBy.value !== 'popular') {
        params.set('sort', sortBy.value)
    }
    if (currentPage.value > 1) {
        params.set('page', String(currentPage.value))
    }

    const basePath = productsIndexHref.value.split('?')[0] || (isEn.value ? '/en/products' : '/san-pham')
    const queryStr = params.toString()
    const nextUrl = queryStr ? `${basePath}?${queryStr}` : basePath

    try {
        window.history.replaceState(window.history.state, '', nextUrl)
    } catch (e) {
        // Ignore history API restriction errors
    }
}

// ---------------------------------------------------------------------------
// Active Filter Chips (Grouped cleanly by L1 when all L2 children are checked)
// ---------------------------------------------------------------------------
const activeCategoryChips = computed(() => {
    const chips = []
    const selSet = new Set(selectedCategoryIds.value.map(String))

    if (selSet.size > 0) {
        const consumedIds = new Set()

        for (const root of categoryIndex.value.roots) {
            if (root.children.length === 0) {
                if (selSet.has(String(root.id))) {
                    chips.push({
                        key: `cat-${root.id}`,
                        label: root.title,
                        removeIds: [root.id],
                    })
                    consumedIds.add(String(root.id))
                }
                continue
            }

            for (const l1 of root.children) {
                if (l1.children.length > 0) {
                    const l2Ids = l1.children.map((c) => c.id)
                    const allSelected = l2Ids.every((id) => selSet.has(String(id)))
                    if (allSelected) {
                        chips.push({
                            key: `l1-${l1.id}`,
                            label: l1.title,
                            removeIds: l2Ids,
                        })
                        l2Ids.forEach((id) => consumedIds.add(String(id)))
                    } else {
                        l1.children.forEach((l2) => {
                            if (selSet.has(String(l2.id))) {
                                chips.push({
                                    key: `l2-${l2.id}`,
                                    label: l2.title,
                                    removeIds: [l2.id],
                                })
                                consumedIds.add(String(l2.id))
                            }
                        })
                    }
                } else if (selSet.has(String(l1.id))) {
                    chips.push({
                        key: `l1-${l1.id}`,
                        label: l1.title,
                        removeIds: [l1.id],
                    })
                    consumedIds.add(String(l1.id))
                }
            }
        }

        // Fallback for any selected ID outside normalized tree
        selectedCategoryIds.value.forEach((id) => {
            if (!consumedIds.has(String(id))) {
                const node = categoryIndex.value.nodeById.get(String(id))
                chips.push({
                    key: `cat-${id}`,
                    label: node ? node.title : String(id),
                    removeIds: [id],
                })
            }
        })
    } else if (unmatchedSubcategorySlug.value) {
        chips.push({
            key: `unmatched-sub-${unmatchedSubcategorySlug.value}`,
            label: unmatchedSubcategorySlug.value,
            isUnmatched: true,
        })
    } else if (unmatchedCategorySlug.value) {
        chips.push({
            key: `unmatched-cat-${unmatchedCategorySlug.value}`,
            label: unmatchedCategorySlug.value,
            isUnmatched: true,
        })
    }

    return chips
})

const allProducts = computed(() => (Array.isArray(props.products) ? props.products : []))

function hasBannerMediaUrl(raw) {
    const candidate = typeof raw === 'string' ? raw : raw?.url
    return typeof candidate === 'string' && candidate.trim() !== ''
}

const hasHeroBannerContent = computed(() => {
    const hb = props.heroBanner
    if (!hb || typeof hb !== 'object') return false
    const hasTitle = typeof hb.title === 'string' && hb.title.trim() !== ''
    const hasDesc = typeof hb.description === 'string' && hb.description.trim() !== ''
    const hasDesktopImg =
        hasBannerMediaUrl(hb.image) ||
        (typeof hb.image_url === 'string' && hb.image_url.trim() !== '')
    const hasMobileImg = hasBannerMediaUrl(hb.image_mobile)
    return Boolean(hasTitle || hasDesc || hasDesktopImg || hasMobileImg)
})

const hasCategories = computed(() => categoryIndex.value.roots.length > 0)

const hasActiveFilters = computed(() => {
    return (
        trimmedSearch.value !== '' ||
        selectedCategoryIds.value.length > 0 ||
        unmatchedCategorySlug.value !== '' ||
        unmatchedSubcategorySlug.value !== ''
    )
})

const isEmptyCatalog = computed(() => allProducts.value.length === 0 && !hasActiveFilters.value)

watch(hasCategories, (has) => {
    if (!has) {
        isMobileFilterOpen.value = false
    }
})

// ---------------------------------------------------------------------------
// Filtering & Sorting Pipeline (100% Real Props Data, L0 -> L1 -> L2 Traversal)
// ---------------------------------------------------------------------------
function categoryMatchesAllowed(catObj, allowedIdSet, allowedSlugSet, ancestorAllowedIdSet) {
    if (!catObj || typeof catObj !== 'object') return false
    if (catObj.id !== undefined && allowedIdSet.has(String(catObj.id))) return true
    if (catObj.slug && allowedSlugSet.has(String(catObj.slug).toLowerCase())) return true
    if (catObj.parent_id && ancestorAllowedIdSet.has(String(catObj.parent_id))) return true

    let parentCursor = catObj.default_parent
    while (parentCursor && typeof parentCursor === 'object') {
        if (parentCursor.id !== undefined && ancestorAllowedIdSet.has(String(parentCursor.id))) {
            return true
        }
        if (parentCursor.slug && allowedSlugSet.has(String(parentCursor.slug).toLowerCase())) {
            return true
        }
        parentCursor = parentCursor.default_parent
    }
    return false
}

const filteredProducts = computed(() => {
    const source = allProducts.value
    let result = source.map((item, idx) => ({ item, idx }))

    // 1. Search filter (title, name, sku, slug, material, description, category titles)
    if (trimmedSearch.value) {
        const q = trimmedSearch.value.toLowerCase()
        result = result.filter(({ item }) => {
            const catText = Array.isArray(item.categories)
                ? item.categories
                      .map((c) => `${c?.title || ''} ${c?.name || ''} ${c?.slug || ''}`)
                      .join(' ')
                : ''
            const haystack = [
                item.title,
                item.name,
                item.sku,
                item.slug,
                item.material,
                item.description,
                catText,
            ]
                .filter(Boolean)
                .join(' ')
                .toLowerCase()
            return haystack.includes(q)
        })
    }

    // 2. 3-Tier Category Filter (L0 -> L1 -> L2 traversal)
    if (selectedCategoryIds.value.length > 0) {
        const selSet = new Set(selectedCategoryIds.value.map(String))
        const allowedIdSet = new Set(selSet)
        const allowedSlugSet = new Set()
        const ancestorAllowedIdSet = new Set()

        selectedCategoryIds.value.forEach((id) => {
            const node = categoryIndex.value.nodeById.get(String(id))
            if (node) {
                node.descendantIds.forEach((dId) => allowedIdSet.add(String(dId)))
                node.descendantSlugs.forEach((dSlug) => allowedSlugSet.add(dSlug))
            }
        })

        // Include L1 / L0 parent IDs ONLY when ALL of their children are selected
        for (const root of categoryIndex.value.roots) {
            let rootAllSelected = root.children.length > 0
            for (const l1 of root.children) {
                if (l1.children.length > 0) {
                    const l1All = l1.children.every((l2) => selSet.has(String(l2.id)))
                    if (l1All) {
                        allowedIdSet.add(String(l1.id))
                        allowedSlugSet.add(l1.slug.toLowerCase())
                        ancestorAllowedIdSet.add(String(l1.id))
                    } else {
                        rootAllSelected = false
                    }
                } else if (!selSet.has(String(l1.id))) {
                    rootAllSelected = false
                }
            }
            if (rootAllSelected) {
                allowedIdSet.add(String(root.id))
                allowedSlugSet.add(root.slug.toLowerCase())
                ancestorAllowedIdSet.add(String(root.id))
            }
        }

        result = result.filter(({ item }) => {
            if (Array.isArray(item.categories) && item.categories.length > 0) {
                return item.categories.some((c) =>
                    categoryMatchesAllowed(c, allowedIdSet, allowedSlugSet, ancestorAllowedIdSet)
                )
            }
            if (item.category && typeof item.category === 'object') {
                return categoryMatchesAllowed(
                    item.category,
                    allowedIdSet,
                    allowedSlugSet,
                    ancestorAllowedIdSet
                )
            }
            return false
        })
    } else if (unmatchedSubcategorySlug.value || unmatchedCategorySlug.value) {
        const targetSlug = (unmatchedSubcategorySlug.value || unmatchedCategorySlug.value).toLowerCase()
        result = result.filter(({ item }) => {
            if (!Array.isArray(item.categories)) return false
            return item.categories.some(
                (c) =>
                    String(c?.slug || '').toLowerCase() === targetSlug ||
                    String(c?.default_parent?.slug || '').toLowerCase() === targetSlug ||
                    String(c?.default_parent?.default_parent?.slug || '').toLowerCase() === targetSlug
            )
        })
    }

    // 3. Sorting
    if (sortBy.value === 'newest') {
        result.sort((a, b) => Number(b.item.id || 0) - Number(a.item.id || 0))
    } else if (sortBy.value === 'name_asc') {
        result.sort((a, b) =>
            String(a.item.title || a.item.name || '').localeCompare(
                String(b.item.title || b.item.name || ''),
                currentLocale.value
            )
        )
    } else if (sortBy.value === 'name_desc') {
        result.sort((a, b) =>
            String(b.item.title || b.item.name || '').localeCompare(
                String(a.item.title || a.item.name || ''),
                currentLocale.value
            )
        )
    } else {
        // popular: featured first while preserving CMS position order
        result.sort(
            (a, b) =>
                (b.item.is_featured ? 1 : 0) - (a.item.is_featured ? 1 : 0) || a.idx - b.idx
        )
    }

    return result.map((entry) => entry.item)
})

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(filteredProducts.value.length / PAGE_SIZE))
})

const paginatedProducts = computed(() => {
    const safePage = Math.min(Math.max(1, currentPage.value), totalPages.value)
    const start = (safePage - 1) * PAGE_SIZE
    return filteredProducts.value.slice(start, start + PAGE_SIZE)
})

const displayRangeStart = computed(() => {
    if (filteredProducts.value.length === 0) return 0
    const safePage = Math.min(Math.max(1, currentPage.value), totalPages.value)
    return (safePage - 1) * PAGE_SIZE + 1
})

const displayRangeEnd = computed(() => {
    if (filteredProducts.value.length === 0) return 0
    return Math.min(displayRangeStart.value + paginatedProducts.value.length - 1, filteredProducts.value.length)
})

// ---------------------------------------------------------------------------
// Interaction Handlers
// ---------------------------------------------------------------------------
function handleSelectedIdsUpdate(nextIds) {
    unmatchedCategorySlug.value = ''
    unmatchedSubcategorySlug.value = ''
    selectedCategoryIds.value = Array.isArray(nextIds) ? nextIds : []
    currentPage.value = 1
    syncBrowserUrl()
}

function removeChip(chip) {
    if (chip.isUnmatched) {
        unmatchedCategorySlug.value = ''
        unmatchedSubcategorySlug.value = ''
    } else if (Array.isArray(chip.removeIds)) {
        const removeSet = new Set(chip.removeIds.map(String))
        selectedCategoryIds.value = selectedCategoryIds.value.filter(
            (id) => !removeSet.has(String(id))
        )
    }
    currentPage.value = 1
    syncBrowserUrl()
}

function clearSearch() {
    searchKeyword.value = ''
    currentPage.value = 1
    syncBrowserUrl()
}

function resetAllFilters() {
    searchKeyword.value = ''
    selectedCategoryIds.value = []
    unmatchedCategorySlug.value = ''
    unmatchedSubcategorySlug.value = ''
    sortBy.value = 'popular'
    currentPage.value = 1
    syncBrowserUrl()
}

function selectSort(value) {
    sortBy.value = value
    isSortOpen.value = false
    currentPage.value = 1
    syncBrowserUrl()
}

function handlePageChange(nextPage) {
    if (nextPage < 1 || nextPage > totalPages.value || nextPage === currentPage.value) return
    currentPage.value = nextPage
    syncBrowserUrl()

    if (typeof window !== 'undefined') {
        const el = productCatalogTop.value || document.getElementById('product-catalog-section')
        if (el) {
            const rect = el.getBoundingClientRect()
            const targetTop = Math.max(0, window.pageYOffset + rect.top - 104)
            window.scrollTo({ top: targetTop, behavior: 'smooth' })
        }
    }
}

function handleEmptyStateClick(e) {
    const anchor = e.target?.closest?.('a')
    if (!anchor) return
    const href = anchor.getAttribute('href') || ''
    const baseProducts = productsIndexHref.value.split('?')[0]
    if (href === productsIndexHref.value || href === baseProducts) {
        e.preventDefault()
        e.stopPropagation()
        resetAllFilters()
    }
}

function handleDocumentClick(e) {
    if (sortDropdownRef.value && !sortDropdownRef.value.contains(e.target)) {
        isSortOpen.value = false
    }
}

function handleKeydown(e) {
    if (e.key === 'Escape') {
        if (isSortOpen.value) isSortOpen.value = false
        if (isMobileFilterOpen.value) isMobileFilterOpen.value = false
    }
}

watch(isMobileFilterOpen, (open) => {
    if (typeof document !== 'undefined') {
        document.body.classList.toggle('overflow-hidden', open)
    }
})

watch(totalPages, (nextTotal) => {
    if (currentPage.value > nextTotal) {
        currentPage.value = nextTotal
    }
})

watch(
    () => page.url?.value ?? page.url,
    (newUrl) => {
        initFiltersFromUrl(typeof newUrl === 'string' ? newUrl : null)
    }
)

let removeInertiaListener = null

onMounted(() => {
    if (typeof document !== 'undefined') {
        document.addEventListener('click', handleDocumentClick)
        document.addEventListener('keydown', handleKeydown)
    }
    if (typeof Inertia?.on === 'function') {
        removeInertiaListener = Inertia.on('navigate', (event) => {
            const nextUrl = event?.detail?.page?.url
            if (typeof nextUrl === 'string') {
                initFiltersFromUrl(nextUrl)
            }
        })
    }
})

onBeforeUnmount(() => {
    if (typeof document !== 'undefined') {
        document.removeEventListener('click', handleDocumentClick)
        document.removeEventListener('keydown', handleKeydown)
        document.body.classList.remove('overflow-hidden')
    }
    if (typeof removeInertiaListener === 'function') {
        removeInertiaListener()
    }
})
</script>
