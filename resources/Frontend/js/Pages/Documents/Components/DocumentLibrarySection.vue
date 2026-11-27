<template>
    <section
        id="documents-library-section"
        ref="sectionRootRef"
        aria-labelledby="documents-library-heading"
        class="w-full bg-white py-8 md:py-16 lg:py-[80px]"
    >
        <div class="app-container-narrow flex flex-col gap-6">
            <!-- Empty Database State (0 documents & 0 document categories in CMS) -->
            <template v-if="isEmptyLibrary">
                <div class="flex items-center gap-2 md:gap-[10px]">
                    <span
                        class="w-[10px] h-[10px] md:w-3 md:h-3 rounded-[2px] bg-[#005EA4] shrink-0"
                        aria-hidden="true"
                    />
                    <component
                        :is="hasHero ? 'h2' : 'h1'"
                        id="documents-library-heading"
                        class="text-[16px] leading-[24px] md:text-[18px] md:leading-[29px] font-bold text-[#005EA4]"
                    >
                        {{ tt('Thư viện tài liệu kỹ thuật tại Thành Khang') }}
                    </component>
                </div>

                <EmptyState
                    variant="light"
                    heading-tag="h3"
                    :title="tt('Thư viện tài liệu kỹ thuật đang được cập nhật')"
                    :description="tt('Vui lòng liên hệ đội ngũ kỹ sư Thành Khang để nhận catalogue và data sheet thông số kỹ thuật mới nhất.')"
                    :primary-action-text="tt('Liên hệ kỹ thuật')"
                    :primary-action-href="contactHref"
                />
            </template>

            <!-- Populated Library View (Figma 1264:49202) -->
            <template v-else>
                <div class="w-full flex flex-col gap-5 md:gap-8">
                    <!-- Top Header + Search Bar + Category Filter Tabs (Figma 1329:53137) -->
                    <div class="w-full flex flex-col gap-4 md:gap-6">
                        <!-- Top Bar: Section Eyebrow Heading (Left) + Search Input (Right) — Figma 1264:49203 -->
                        <div class="w-full flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-center gap-2 md:gap-[10px] min-w-0">
                                <span
                                    class="w-[10px] h-[10px] md:w-3 md:h-3 rounded-[2px] bg-[#005EA4] shrink-0"
                                    aria-hidden="true"
                                />
                                <component
                                    :is="hasHero ? 'h2' : 'h1'"
                                    id="documents-library-heading"
                                    class="text-[16px] leading-[24px] md:text-[18px] md:leading-[29px] font-bold text-[#005EA4]"
                                >
                                    {{ tt('Thư viện tài liệu kỹ thuật tại Thành Khang') }}
                                </component>
                            </div>

                            <!-- Search Input (Figma 1264:49208 — 296x42px, #CDD5DF border, rounded-[6px]) -->
                            <form
                                role="search"
                                class="w-full md:w-[296px] shrink-0"
                                @submit.prevent="submitSearchNow"
                            >
                                <label for="documents-library-search" class="sr-only">
                                    {{ tt('Tìm kiếm tài liệu, mã xích,...') }}
                                </label>
                                <div
                                    class="w-full h-[42px] bg-white border border-[#CDD5DF] rounded-[6px] pl-[15px] pr-3 flex items-center gap-[10px] focus-within:border-[#005EA4] focus-within:ring-1 focus-within:ring-[#005EA4]/30 transition-colors"
                                >
                                    <!-- Figma 1264:49209 (search-sm) -->
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 36 36"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-[18px] h-[18px] text-[#697586] shrink-0"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M31.5 31.5L22.5001 22.5M25.5 15C25.5 20.799 20.799 25.5 15 25.5C9.20101 25.5 4.5 20.799 4.5 15C4.5 9.20101 9.20101 4.5 15 4.5C20.799 4.5 25.5 9.20101 25.5 15Z"
                                            stroke="currentColor"
                                            stroke-width="4"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>

                                    <input
                                        id="documents-library-search"
                                        v-model="searchQuery"
                                        type="search"
                                        :placeholder="tt('Tìm kiếm tài liệu, mã xích,...')"
                                        class="search-input-clean w-full min-w-0 bg-transparent text-base lg:text-[14px] leading-[22px] text-[#00284B] placeholder:text-[#9AA4B2] focus:outline-none"
                                        @input="handleSearchInput"
                                    />

                                    <button
                                        v-if="searchQuery.length > 0"
                                        type="button"
                                        :aria-label="tt('Xóa từ khóa')"
                                        class="w-11 h-11 -mr-2 rounded-[4px] inline-flex items-center justify-center text-[#697586] hover:text-[#00284B] hover:bg-[#EEF2F6] transition-colors shrink-0 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                                        @click="clearSearch"
                                    >
                                        <svg
                                            width="14"
                                            height="14"
                                            viewBox="0 0 16 16"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="w-3.5 h-3.5"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M12 4L4 12M4 4L12 12"
                                                stroke="currentColor"
                                                stroke-width="1.75"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Category Filter Tabs (Figma 1264:49211) -->
                        <div
                            role="group"
                            :aria-label="tt('Danh mục tài liệu')"
                            class="w-full flex items-center flex-wrap gap-2"
                        >
                            <button
                                type="button"
                                :aria-pressed="!activeCategorySlug"
                                data-testid="document-tab-all"
                                :class="[
                                    TAB_BASE_CLASS,
                                    !activeCategorySlug ? TAB_ACTIVE_CLASS : TAB_INACTIVE_CLASS,
                                ]"
                                @click="selectCategory('')"
                            >
                                {{ `${tt('Tất cả')} (${totalDocumentsCount})` }}
                            </button>

                            <button
                                v-for="cat in normalizedCategories"
                                :key="cat.key"
                                type="button"
                                :aria-pressed="activeCategorySlug === cat.slug"
                                :data-testid="`document-tab-${cat.slug}`"
                                :class="[
                                    TAB_BASE_CLASS,
                                    activeCategorySlug === cat.slug
                                        ? TAB_ACTIVE_CLASS
                                        : TAB_INACTIVE_CLASS,
                                ]"
                                @click="selectCategory(cat.slug)"
                            >
                                {{ cat.title }}
                            </button>
                        </div>
                    </div>

                    <!-- 2-Column PDF Download Grid (Figma 1264:49226) -->
                    <div
                        v-if="normalizedDocuments.length > 0"
                        class="w-full grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-x-6 md:gap-y-3"
                    >
                        <template v-for="doc in normalizedDocuments" :key="doc.key">
                            <a
                                v-if="doc.hasFile && doc.downloadUrl"
                                :href="doc.downloadUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                download
                                :aria-label="`${tt('Tải xuống tài liệu')}: ${doc.title}`"
                                :class="CARD_CLASS"
                            >
                                <span
                                    class="min-w-0 flex-1 text-[14px] leading-[22px] font-bold text-[#00284B] group-hover:text-[#005EA4] line-clamp-2 break-words transition-colors"
                                >
                                    {{ doc.title }}
                                </span>

                                <span
                                    class="w-[42px] h-[42px] rounded-[8px] bg-[#EEF2F6] group-hover:bg-[#E0ECF8] shrink-0 flex items-center justify-center text-[#697586] group-hover:text-[#005EA4] transition-colors"
                                    aria-hidden="true"
                                >
                                    <!-- Figma 1006:2626 (download-01) -->
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 40 40"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            d="M35 25V27C35 29.8003 35 31.2004 34.455 32.27C33.9757 33.2108 33.2108 33.9757 32.27 34.455C31.2004 35 29.8003 35 27 35H13C10.1997 35 8.79961 35 7.73005 34.455C6.78924 33.9757 6.02433 33.2108 5.54497 32.27C5 31.2004 5 29.8003 5 27V25M11.6667 16.6667L20 25L28.3333 16.6667M20 25V5"
                                            stroke="currentColor"
                                            stroke-width="4"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>
                            </a>

                            <Link
                                v-else
                                :href="requestDocumentHref(doc)"
                                :aria-label="`${tt('Yêu cầu tài liệu kỹ thuật')}: ${doc.title}`"
                                :class="CARD_CLASS"
                            >
                                <span
                                    class="min-w-0 flex-1 text-[14px] leading-[22px] font-bold text-[#00284B] group-hover:text-[#005EA4] line-clamp-2 break-words transition-colors"
                                >
                                    {{ doc.title }}
                                </span>

                                <span
                                    class="w-[42px] h-[42px] rounded-[8px] bg-[#EEF2F6] group-hover:bg-[#E0ECF8] shrink-0 flex items-center justify-center text-[#697586] group-hover:text-[#005EA4] transition-colors"
                                    aria-hidden="true"
                                >
                                    <!-- Figma 1006:2626 (download-01) -->
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 40 40"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            d="M35 25V27C35 29.8003 35 31.2004 34.455 32.27C33.9757 33.2108 33.2108 33.9757 32.27 34.455C31.2004 35 29.8003 35 27 35H13C10.1997 35 8.79961 35 7.73005 34.455C6.78924 33.9757 6.02433 33.2108 5.54497 32.27C5 31.2004 5 29.8003 5 27V25M11.6667 16.6667L20 25L28.3333 16.6667M20 25V5"
                                            stroke="currentColor"
                                            stroke-width="4"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>
                            </Link>
                        </template>
                    </div>

                    <!-- Filtered / Searched Empty State -->
                    <EmptyState
                        v-else
                        variant="light"
                        heading-tag="h3"
                        :title="tt('Không tìm thấy tài liệu phù hợp')"
                        :description="tt('Không có tài liệu nào khớp với danh mục hoặc từ khóa tìm kiếm hiện tại. Vui lòng thử lại hoặc đặt lại bộ lọc.')"
                        :primary-action-text="tt('Xóa bộ lọc')"
                        :primary-action-href="libraryBaseUrl"
                        :secondary-action-text="tt('Liên hệ kỹ thuật')"
                        :secondary-action-href="contactHref"
                    />
                </div>

                <!-- Pagination Bar (Figma 1264:49242) -->
                <nav
                    v-if="lastPage > 1 && normalizedDocuments.length > 0"
                    :aria-label="tt('Phân trang tài liệu')"
                    class="w-full pt-3 flex items-center justify-center"
                >
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <!-- Previous Page Button (Figma 1329:53149) -->
                        <button
                            type="button"
                            :disabled="currentPage <= 1"
                            :aria-label="tt('Trang trước')"
                            :class="[
                                PAGINATION_ARROW_CLASS,
                                currentPage <= 1
                                    ? 'opacity-50 cursor-not-allowed text-[#9AA4B2]'
                                    : 'text-[#364152] hover:border-[#005EA4] hover:text-[#005EA4] cursor-pointer',
                            ]"
                            @click="changePage(currentPage - 1)"
                        >
                            <!-- Figma 972:29139 (chevron-left) -->
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 36 36"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-[18px] h-[18px]"
                                aria-hidden="true"
                            >
                                <path
                                    d="M22.5 27L13.5 18L22.5 9"
                                    stroke="currentColor"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>

                        <!-- Page Numbers (Figma 1264:49243..49246) -->
                        <template v-for="(item, index) in pagesToShow" :key="`${item}-${index}`">
                            <span
                                v-if="item === '...'"
                                class="min-w-[44px] min-h-[44px] md:w-[40px] md:h-[40px] md:min-w-0 md:min-h-0 bg-white border border-[#E3E8EF] rounded-[4px] inline-flex items-center justify-center text-[16px] leading-[26px] font-medium text-[#364152] select-none cursor-default"
                                aria-hidden="true"
                            >
                                ...
                            </span>
                            <button
                                v-else
                                type="button"
                                :aria-current="item === currentPage ? 'page' : undefined"
                                :aria-label="`${tt('Trang')} ${item}`"
                                :class="[
                                    PAGINATION_NUMBER_CLASS,
                                    item === currentPage
                                        ? 'bg-[#005EA4] border-[#005EA4] text-[#FAFAFA] cursor-default'
                                        : 'bg-white border-[#E3E8EF] text-[#364152] hover:border-[#005EA4] hover:text-[#005EA4] cursor-pointer',
                                ]"
                                @click="changePage(item)"
                            >
                                {{ item }}
                            </button>
                        </template>

                        <!-- Next Page Button (Figma 1329:53145) -->
                        <button
                            type="button"
                            :disabled="currentPage >= lastPage"
                            :aria-label="tt('Trang tiếp theo')"
                            :class="[
                                PAGINATION_ARROW_CLASS,
                                currentPage >= lastPage
                                    ? 'opacity-50 cursor-not-allowed text-[#9AA4B2]'
                                    : 'text-[#364152] hover:border-[#005EA4] hover:text-[#005EA4] cursor-pointer',
                            ]"
                            @click="changePage(currentPage + 1)"
                        >
                            <!-- Figma 972:29125 (chevron-right) -->
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 36 36"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-[18px] h-[18px]"
                                aria-hidden="true"
                            >
                                <path
                                    d="M13.5 27L22.5 18L13.5 9"
                                    stroke="currentColor"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>
                    </div>
                </nav>
            </template>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance, onBeforeUnmount, ref, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { Link } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'
import EmptyState from '@/Components/EmptyState.vue'

const TAB_BASE_CLASS =
    'min-h-[38px] md:h-[40px] px-4 rounded-[6px] text-[14px] leading-[22px] font-medium inline-flex items-center justify-center whitespace-nowrap transition-colors cursor-pointer select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]'
const TAB_ACTIVE_CLASS = 'bg-[#005EA4] text-[#FAFAFA] border border-[#005EA4]'
const TAB_INACTIVE_CLASS =
    'bg-white border border-[#E3E8EF] text-[#4B5565] hover:border-[#005EA4] hover:text-[#005EA4]'

const CARD_CLASS =
    'group bg-white border border-[#E3E8EF] hover:border-[#005EA4] hover:shadow-xs rounded-[10px] min-h-[78px] px-[19px] py-[17px] flex items-center justify-between gap-3 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]'

const PAGINATION_ARROW_CLASS =
    'min-w-[44px] min-h-[44px] md:w-[40px] md:h-[40px] md:min-w-0 md:min-h-0 rounded-[6px] border border-[#E3E8EF] bg-white inline-flex items-center justify-center transition-colors select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]'

const PAGINATION_NUMBER_CLASS =
    'min-w-[44px] min-h-[44px] md:w-[40px] md:h-[40px] md:min-w-0 md:min-h-0 rounded-[4px] border inline-flex items-center justify-center text-[16px] leading-[26px] font-medium transition-colors select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]'

const props = defineProps({
    documents: {
        type: Array,
        default: () => [],
    },
    documentCategories: {
        type: Array,
        default: () => [],
    },
    totalDocumentsCount: {
        type: Number,
        default: 0,
    },
    pagination: {
        type: Object,
        default: () => ({
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0,
        }),
    },
    initialFilters: {
        type: Object,
        default: () => ({
            category: '',
            search: '',
            page: 1,
        }),
    },
    hasHero: {
        type: Boolean,
        default: true,
    },
})

const instance = getCurrentInstance()
const { isEn } = useTranslate()
const sectionRootRef = ref(null)

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

const libraryBaseUrl = computed(() =>
    safeRoute('documents.library', undefined, isEn.value ? '/en/documents/library' : '/tai-lieu/thu-vien')
)

const contactHref = computed(() =>
    safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he')
)

const activeCategorySlug = ref(
    typeof props.initialFilters?.category === 'string' ? props.initialFilters.category.trim() : ''
)
const searchQuery = ref(
    typeof props.initialFilters?.search === 'string' ? props.initialFilters.search : ''
)

watch(
    () => props.initialFilters,
    (next) => {
        const nextCategory = typeof next?.category === 'string' ? next.category.trim() : ''
        const nextSearch = typeof next?.search === 'string' ? next.search : ''
        if (activeCategorySlug.value !== nextCategory) {
            activeCategorySlug.value = nextCategory
        }
        if (searchQuery.value !== nextSearch) {
            searchQuery.value = nextSearch
        }
    },
    { deep: true }
)

let searchDebounceTimer = null

function clearSearchDebounce() {
    if (searchDebounceTimer !== null) {
        clearTimeout(searchDebounceTimer)
        searchDebounceTimer = null
    }
}

onBeforeUnmount(() => {
    clearSearchDebounce()
})

function normalizeActionUrl(url) {
    if (typeof url !== 'string') return ''
    const trimmed = url.trim()
    if (!trimmed || trimmed === '#') return ''
    if (trimmed.includes('/static/assets/')) {
        return trimmed.replace(/^https?:\/\/[^/]+/, '').replace('/static/', '/')
    }
    if (/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?(\/|$)/i.test(trimmed)) {
        return trimmed.replace(/^https?:\/\/[^/]+/i, '') || '/'
    }
    if (/^(assets|images|storage)\//i.test(trimmed)) {
        return `/${trimmed}`
    }
    return trimmed
}

const normalizedCategories = computed(() => {
    if (!Array.isArray(props.documentCategories)) return []
    return props.documentCategories
        .map((cat, idx) => {
            if (!cat || typeof cat !== 'object') return null
            const title = typeof cat.title === 'string' ? cat.title.trim() : ''
            const slug = typeof cat.slug === 'string' ? cat.slug.trim() : ''
            if (!title || !slug) return null
            return {
                key: `doc-cat-${cat.id ?? idx}-${slug}`,
                id: cat.id ?? idx,
                title,
                slug,
                documentsCount: Number(cat.documents_count) || 0,
            }
        })
        .filter(Boolean)
})

const normalizedDocuments = computed(() => {
    if (!Array.isArray(props.documents)) return []
    return props.documents
        .map((doc, idx) => {
            if (!doc || typeof doc !== 'object') return null
            const title = typeof doc.title === 'string' ? doc.title.trim() : ''
            if (!title) return null
            const slug = typeof doc.slug === 'string' ? doc.slug.trim() : ''
            const rawUrl = doc.download_url || doc.file_url || ''
            const downloadUrl = normalizeActionUrl(rawUrl)
            const hasFile = Boolean(doc.has_file && downloadUrl)

            return {
                key: `doc-lib-${doc.id ?? idx}-${slug || title}`,
                id: doc.id ?? idx,
                title,
                slug,
                downloadUrl,
                hasFile,
            }
        })
        .filter(Boolean)
})

const isEmptyLibrary = computed(
    () => Number(props.totalDocumentsCount) <= 0 && normalizedCategories.value.length === 0
)

const currentPage = computed(() => Math.max(1, Number(props.pagination?.current_page) || 1))
const lastPage = computed(() => Math.max(1, Number(props.pagination?.last_page) || 1))

const pagesToShow = computed(() => {
    const total = lastPage.value
    const current = Math.min(currentPage.value, total)

    if (total <= 7) {
        return Array.from({ length: total }, (_, idx) => idx + 1)
    }

    if (current <= 3) {
        return [1, 2, 3, 4, '...', total]
    }

    if (current >= total - 2) {
        return [1, '...', total - 3, total - 2, total - 1, total]
    }

    return [1, '...', current - 1, current, current + 1, '...', total]
})

function requestDocumentHref(doc) {
    const param = String(doc?.slug || doc?.title || '').trim()
    if (!param) return contactHref.value
    const separator = contactHref.value.includes('?') ? '&' : '?'
    return `${contactHref.value}${separator}document=${encodeURIComponent(param)}`
}

function buildFilterParams({ category, search, page }) {
    const params = {}
    const cleanCategory = typeof category === 'string' ? category.trim() : ''
    const cleanSearch = typeof search === 'string' ? search.trim() : ''
    const cleanPage = Number(page) || 1

    if (cleanCategory) {
        params.category = cleanCategory
    }
    if (cleanSearch) {
        params.search = cleanSearch
    }
    if (cleanPage > 1) {
        params.page = cleanPage
    }
    return params
}

function navigateWithFilters(overrides = {}) {
    clearSearchDebounce()
    const nextCategory =
        overrides.category !== undefined ? overrides.category : activeCategorySlug.value
    const nextSearch = overrides.search !== undefined ? overrides.search : searchQuery.value
    const nextPage = overrides.page !== undefined ? overrides.page : 1

    const params = buildFilterParams({
        category: nextCategory,
        search: nextSearch,
        page: nextPage,
    })

    Inertia.get(libraryBaseUrl.value, params, {
        preserveState: true,
        preserveScroll: true,
    })
}

function selectCategory(slug) {
    const cleanSlug = typeof slug === 'string' ? slug.trim() : ''
    if (activeCategorySlug.value === cleanSlug && currentPage.value === 1) {
        return
    }
    activeCategorySlug.value = cleanSlug
    navigateWithFilters({
        category: cleanSlug,
        search: searchQuery.value,
        page: 1,
    })
}

function handleSearchInput() {
    clearSearchDebounce()
    searchDebounceTimer = setTimeout(() => {
        navigateWithFilters({
            category: activeCategorySlug.value,
            search: searchQuery.value,
            page: 1,
        })
    }, 300)
}

function submitSearchNow() {
    navigateWithFilters({
        category: activeCategorySlug.value,
        search: searchQuery.value,
        page: 1,
    })
}

function clearSearch() {
    searchQuery.value = ''
    navigateWithFilters({
        category: activeCategorySlug.value,
        search: '',
        page: 1,
    })
}

function changePage(page) {
    if (page < 1 || page > lastPage.value || page === currentPage.value) {
        return
    }
    navigateWithFilters({
        category: activeCategorySlug.value,
        search: searchQuery.value,
        page,
    })
}
</script>

<style scoped>
.search-input-clean::-webkit-search-decoration,
.search-input-clean::-webkit-search-cancel-button,
.search-input-clean::-webkit-search-results-button,
.search-input-clean::-webkit-search-results-decoration {
    -webkit-appearance: none;
    appearance: none;
}
</style>
