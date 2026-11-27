<template>
    <Head>
        <title>{{ pageTitle }}</title>
        <meta name="description" :content="pageDescription" />
        <meta property="og:title" :content="pageTitle" />
        <meta property="og:description" :content="pageDescription" />
        <meta property="og:type" content="website" />
    </Head>

    <div class="bg-surface text-ink-body">
        <!-- ======================================================== -->
        <!-- HERO BANNER — Tin tức (Figma 908:25360)                   -->
        <!-- ======================================================== -->
        <section class="relative w-full overflow-hidden bg-dark min-h-[220px] lg:min-h-[252px] flex items-center">
            <img
                src="/assets/images/posts/tin-tuc-hero.webp"
                alt=""
                aria-hidden="true"
                width="1920"
                height="336"
                class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none select-none"
                loading="eager"
            />
            <div class="hero-scrim absolute inset-0 pointer-events-none" aria-hidden="true" />
            <div class="app-container-narrow relative z-10 py-10 lg:py-0">
                <div class="flex flex-col gap-6 max-w-[580px]">
                    <nav :aria-label="tt('Breadcrumb')" class="flex items-center gap-2 text-[14px]">
                        <Link :href="homeHref" class="text-gray-modern-300 hover:text-white transition-colors">
                            {{ tt('Trang chủ') }}
                        </Link>
                        <span class="text-gray-modern-300" aria-hidden="true">/</span>
                        <span class="font-medium text-surface" aria-current="page">
                            {{ tt('Tin tức & Kiến thức') }}
                        </span>
                    </nav>
                    <div class="flex flex-col gap-2">
                        <h1 class="text-h1-page-mobile lg:text-h1-page font-bold text-surface uppercase">
                            {{ tt('TIN TỨC & KIẾN THỨC') }}
                        </h1>
                        <p class="text-[14px] leading-[1.6] text-gray-modern-200 max-w-[400px]">
                            {{
                                tt(
                                    'Cập nhật kiến thức truyền động công nghiệp, hướng dẫn bảo trì xích tải – thang cuốn và chia sẻ kinh nghiệm thực tế từ đội ngũ kỹ sư Thành Khang.'
                                )
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- FEATURED ARTICLE SECTION (Figma 882:2250)                 -->
        <!-- ======================================================== -->
        <section v-if="featuredPost" class="w-full bg-surface-100 py-8 md:py-10">
            <div class="app-container-narrow flex flex-col gap-5">
                <div class="flex items-center gap-2.5">
                    <span class="block w-3 h-3 rounded-[2px] bg-primary shrink-0" aria-hidden="true" />
                    <h2 class="text-[18px] font-bold leading-[1.6] text-primary">
                        {{ tt('Bài viết nổi bật') }}
                    </h2>
                </div>

                <article
                    class="bg-surface border border-gray-modern-200 rounded-[8px] shadow-card-featured overflow-hidden grid grid-cols-1 lg:grid-cols-[588px_minmax(0,1fr)]"
                >
                    <Link
                        :href="postHref(featuredPost)"
                        class="relative block aspect-[16/10] lg:aspect-auto lg:h-[400px] overflow-hidden bg-surface-gray-light group"
                    >
                        <img
                            :src="postImage(featuredPost)"
                            :alt="featuredPost.image?.alt || featuredPost.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            loading="eager"
                        />
                        <span
                            class="absolute top-5 left-5 bg-primary-info text-surface text-[14px] font-medium px-3 py-1 rounded-[4px]"
                        >
                            {{ tt('Bài viết mới') }}
                        </span>
                    </Link>

                    <div class="p-5 sm:p-8 lg:p-10 flex flex-col justify-between gap-6">
                        <div class="flex flex-col gap-[14px]">
                            <time
                                v-if="formatDate(featuredPost.published_at)"
                                :datetime="featuredPost.published_at"
                                class="text-[14px] font-medium text-ink-subtle"
                            >
                                {{ formatDate(featuredPost.published_at) }}
                            </time>
                            <h3 class="text-[20px] lg:text-[24px] font-bold leading-[1.4] text-dark line-clamp-3">
                                <Link :href="postHref(featuredPost)" class="hover:text-primary transition-colors">
                                    {{ featuredPost.title }}
                                </Link>
                            </h3>
                            <p
                                v-if="featuredPost.description"
                                class="text-[15px] lg:text-[16px] leading-[1.6] text-dark-slate line-clamp-3"
                            >
                                {{ featuredPost.description }}
                            </p>
                        </div>

                        <Link
                            :href="postHref(featuredPost)"
                            class="bg-primary hover:bg-primary-hover text-surface text-[14px] font-medium px-5 py-2 rounded-[4px] inline-flex items-center justify-center w-fit transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                        >
                            {{ tt('Đọc bài viết') }}
                        </Link>
                    </div>
                </article>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- BLOG LISTING SECTION (Figma 882:2287)                     -->
        <!-- ======================================================== -->
        <section ref="listingSectionRef" class="w-full bg-surface pt-8 pb-12 md:pt-10 md:pb-20">
            <div class="app-container-narrow flex flex-col gap-6 md:gap-8">
                <div class="flex flex-col gap-5">
                    <!-- Heading Row -->
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="block w-3 h-3 rounded-[2px] bg-primary shrink-0" aria-hidden="true" />
                            <h2 class="text-[18px] font-bold leading-[1.6] text-primary">
                                {{ tt('Tất cả bài viết') }}
                            </h2>
                        </div>
                        <p class="text-[14px] text-ink-subtle">
                            <template v-if="filteredResources.length > 0">
                                {{ tt('Hiển thị') }} {{ rangeStart }}–{{ rangeEnd }} {{ tt('trên') }}
                                {{ filteredResources.length }} {{ tt('bài viết') }}
                            </template>
                            <template v-else> 0 {{ tt('bài viết') }} </template>
                        </p>
                    </div>

                    <!-- Filter & Search Bar -->
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div
                            class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 lg:pb-0 lg:flex-wrap"
                            role="tablist"
                            :aria-label="tt('Danh mục bài viết')"
                        >
                            <button
                                type="button"
                                role="tab"
                                :aria-selected="selectedFilter === 'all'"
                                class="px-4 py-[9px] rounded-[6px] text-[14px] whitespace-nowrap cursor-pointer transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                :class="
                                    selectedFilter === 'all'
                                        ? 'bg-primary border border-primary text-surface font-bold'
                                        : 'bg-white border border-gray-modern-200 text-dark-slate font-medium hover:border-primary hover:text-primary'
                                "
                                @click="applyFilter('all')"
                            >
                                {{ tt('Tất cả') }} ({{ safePosts.length }})
                            </button>
                            <button
                                v-for="cat in uniqueCategories"
                                :key="cat.slug || cat.id"
                                type="button"
                                role="tab"
                                :aria-selected="selectedFilter === cat.slug"
                                class="px-4 py-[9px] rounded-[6px] text-[14px] whitespace-nowrap cursor-pointer transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                :class="
                                    selectedFilter === cat.slug
                                        ? 'bg-primary border border-primary text-surface font-bold'
                                        : 'bg-white border border-gray-modern-200 text-dark-slate font-medium hover:border-primary hover:text-primary'
                                "
                                @click="applyFilter(cat.slug)"
                            >
                                {{ cat.title }}
                            </button>
                        </div>

                        <div
                            class="bg-white border border-gray-modern-300 focus-within:border-primary rounded-[6px] h-[42px] px-[14px] flex items-center gap-2.5 w-full lg:w-[296px] shrink-0 transition-colors"
                        >
                            <img
                                src="/assets/svg/icons/header-search.svg"
                                alt=""
                                aria-hidden="true"
                                width="18"
                                height="18"
                                class="w-[18px] h-[18px] shrink-0"
                            />
                            <input
                                v-model.trim="searchText"
                                type="search"
                                :aria-label="tt('Tìm kiếm bài viết...')"
                                :placeholder="tt('Tìm kiếm bài viết...')"
                                class="w-full bg-transparent text-[14px] text-dark placeholder:text-gray-modern-400 outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Blog Cards Grid -->
                <div
                    v-if="paginatedResources.length > 0"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-x-8 lg:gap-y-10"
                >
                    <Link
                        v-for="(item, index) in paginatedResources"
                        :key="item.id || item.slug || index"
                        :href="postHref(item)"
                        class="group flex flex-col h-full focus:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-[8px]"
                    >
                        <div class="relative aspect-[363/240] w-full rounded-[8px] overflow-hidden bg-surface-gray-light">
                            <img
                                :src="postImage(item)"
                                :alt="item.image?.alt || item.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            />
                            <span
                                v-if="isNewPost(index)"
                                class="absolute top-4 left-4 bg-primary text-surface text-[14px] font-medium px-3 py-1 rounded-[4px]"
                            >
                                {{ tt('Mới') }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-col justify-between gap-5 flex-1">
                            <div>
                                <h3
                                    class="text-[18px] lg:text-[20px] font-bold leading-[1.6] text-dark group-hover:text-primary transition-colors line-clamp-2 lg:min-h-[64px]"
                                >
                                    {{ item.title }}
                                </h3>
                                <p
                                    v-if="item.description"
                                    class="mt-1 text-[14px] lg:text-[16px] leading-[1.6] text-ink-subtle line-clamp-3"
                                >
                                    {{ item.description }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between gap-2 mt-auto">
                                <span
                                    v-if="postCategoryTitle(item)"
                                    class="bg-surface border border-gray-modern-300 rounded-[4px] px-3 py-1 text-[14px] font-medium text-ink-subtle truncate max-w-[65%]"
                                >
                                    {{ postCategoryTitle(item) }}
                                </span>
                                <time
                                    v-if="formatDate(item.published_at)"
                                    :datetime="item.published_at"
                                    class="text-[14px] font-medium text-ink-subtle whitespace-nowrap ml-auto"
                                >
                                    {{ formatDate(item.published_at) }}
                                </time>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="bg-white border border-gray-modern-200 rounded-[8px] px-6 py-12 text-center flex flex-col items-center gap-4"
                >
                    <p class="text-[16px] font-medium text-dark">
                        {{ tt('Không tìm thấy bài viết nào phù hợp với tiêu chí lọc hiện tại.') }}
                    </p>
                    <button
                        v-if="selectedFilter !== 'all' || searchText"
                        type="button"
                        class="bg-primary hover:bg-primary-hover text-surface text-[14px] font-medium px-5 py-2 rounded-[4px] inline-flex items-center justify-center transition-colors cursor-pointer"
                        @click="resetFilters"
                    >
                        {{ tt('Đặt lại bộ lọc') }}
                    </button>
                </div>

                <!-- Pagination -->
                <nav
                    v-if="totalPages > 1"
                    :aria-label="tt('Phân trang')"
                    class="flex items-center justify-center flex-wrap gap-2 pt-3"
                >
                    <template v-for="(item, idx) in paginationItems" :key="'pg-' + idx + '-' + item">
                        <span
                            v-if="item === '...'"
                            class="w-10 h-10 rounded-[4px] border border-gray-modern-200 bg-white flex items-center justify-center text-[16px] font-medium text-dark-slate select-none"
                            aria-hidden="true"
                        >
                            &hellip;
                        </span>
                        <button
                            v-else
                            type="button"
                            :aria-current="currentPage === item ? 'page' : undefined"
                            class="w-10 h-10 rounded-[4px] border text-[16px] font-medium flex items-center justify-center transition-colors cursor-pointer"
                            :class="
                                currentPage === item
                                    ? 'bg-primary border-primary text-surface'
                                    : 'bg-white border-gray-modern-200 text-dark-slate hover:border-primary hover:text-primary'
                            "
                            @click="changePage(item)"
                        >
                            {{ item }}
                        </button>
                    </template>
                </nav>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- CTA TƯ VẤN KỸ THUẬT SECTION (Figma 882:2404)              -->
        <!-- ======================================================== -->
        <section class="bg-surface pb-10 lg:pb-20">
            <div
                class="relative w-full overflow-hidden bg-dark py-12 md:py-16 lg:min-h-[362px] flex items-center justify-center"
            >
                <div class="cta-texture cta-texture--left pointer-events-none select-none" aria-hidden="true">
                    <div class="cta-texture__inner cta-texture__inner--left">
                        <img src="/assets/images/posts/cta-chain.webp" alt="" width="1584" height="672" loading="lazy" />
                    </div>
                </div>
                <div class="cta-texture cta-texture--right pointer-events-none select-none" aria-hidden="true">
                    <div class="cta-texture__inner cta-texture__inner--right">
                        <img src="/assets/images/posts/cta-chain.webp" alt="" width="1584" height="672" loading="lazy" />
                    </div>
                </div>

                <div
                    class="relative z-10 max-w-[600px] w-full mx-auto px-4 text-center flex flex-col items-center gap-6 lg:gap-8"
                >
                    <div class="flex flex-col items-center">
                        <h2
                            class="text-[22px] lg:text-[28px] font-bold leading-[1.6] text-surface uppercase lg:whitespace-nowrap"
                        >
                            {{ tt('CẦN TƯ VẤN HOẶC BÁO GIÁ NHANH ?') }}
                        </h2>
                        <p class="text-[14px] lg:text-[16px] font-medium leading-[1.6] text-gray-modern-400">
                            {{ tt('Gửi yêu cầu và chúng tôi sẽ phản hồi trong vòng 2 giờ làm việc') }}
                        </p>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-3 sm:gap-4 w-full max-w-[531px]"
                        :class="contactPhone ? 'sm:grid-cols-2' : 'sm:max-w-[280px]'"
                    >
                        <Link
                            :href="contactHref"
                            class="bg-surface hover:bg-white text-primary text-[16px] lg:text-[18px] font-medium h-[49px] px-6 py-2.5 rounded-[4px] inline-flex items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                        >
                            {{ tt('Yêu cầu báo giá') }}
                        </Link>
                        <a
                            v-if="contactPhone"
                            :href="'tel:' + cleanPhone"
                            class="bg-transparent hover:bg-surface/10 border border-surface text-surface text-[16px] lg:text-[18px] font-medium h-[49px] px-6 py-2.5 rounded-[4px] inline-flex items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                        >
                            {{ tt('Gọi Hotline') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, computed, watch, getCurrentInstance } from 'vue'
import { usePage, Head, Link } from '@inertiajs/inertia-vue3'

const props = defineProps({
    posts: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    seo: { type: Object, default: () => null },
})

const instance = getCurrentInstance()
const trans = (text) => (typeof instance?.proxy?.tt === 'function' ? instance.proxy.tt(text) : text)
const page = usePage()
const isEn = computed(() => (page.props.value?.locale?.current || 'vi') === 'en')

const globalConfig = computed(() => page.props.value?.global || {})
const siteConfigs = computed(() => page.props.value?.configs || {})

const safeRoute = (name, params, fallback) => {
    const proxy = instance?.proxy
    try {
        if (proxy && typeof proxy.route === 'function' && typeof proxy.routes === 'function') {
            const all = proxy.routes() || {}
            const localized = typeof proxy.routeLocaleName === 'function' ? proxy.routeLocaleName(name) : name
            if (all[localized] || all[name]) {
                const url = proxy.route(name, params)
                if (url && url !== '#') return url
            }
        }
    } catch (e) {
        // fall through to fallback
    }
    return fallback
}

const homeHref = computed(() => safeRoute('home', undefined, isEn.value ? '/en' : '/'))
const contactHref = computed(() => safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he'))

const contactPhone = computed(
    () =>
        globalConfig.value.hotline ||
        siteConfigs.value.hotline ||
        globalConfig.value.general_phone ||
        siteConfigs.value.general_phone ||
        ''
)
const cleanPhone = computed(() => String(contactPhone.value || '').replace(/[^\d+]/g, ''))

const pageTitle = computed(
    () =>
        props.seo?.title ||
        (isEn.value
            ? 'News & Technical Insights - Thanh Khang'
            : trans('Tin tức & Kiến thức Kỹ thuật - Thành Khang'))
)
const pageDescription = computed(
    () =>
        props.seo?.description ||
        (isEn.value
            ? 'Explore technical guides, escalator maintenance knowledge, and industrial automation news from Thanh Khang.'
            : trans(
                  'Cập nhật kiến thức truyền động công nghiệp, hướng dẫn bảo trì xích tải – thang cuốn và chia sẻ kinh nghiệm thực tế từ đội ngũ kỹ sư Thành Khang.'
              ))
)

// ---------- Posts & Filtering ----------
const safePosts = computed(() => (Array.isArray(props.posts) ? props.posts : []).filter(Boolean))

const featuredPost = computed(() => {
    if (!safePosts.value.length) return null
    return safePosts.value.find((post) => Boolean(post.is_featured)) || safePosts.value[0]
})

const uniqueCategories = computed(() => {
    const seen = new Map()
    safePosts.value.forEach((post) => {
        const list = Array.isArray(post.categories) && post.categories.length
            ? post.categories
            : post.category
              ? [post.category]
              : []
        list.forEach((cat) => {
            if (!cat || !cat.title) return
            const key = cat.slug || String(cat.id || cat.title)
            if (!seen.has(key)) {
                seen.set(key, {
                    id: cat.id ?? key,
                    slug: cat.slug || key,
                    title: cat.title,
                })
            }
        })
    })
    return Array.from(seen.values())
})

const selectedFilter = ref('all')
const searchText = ref('')
const currentPage = ref(1)
const perPage = 6
const listingSectionRef = ref(null)

watch(searchText, () => {
    currentPage.value = 1
})

const normalizeText = (str) =>
    String(str || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')

const filteredResources = computed(() => {
    let list = safePosts.value

    if (selectedFilter.value !== 'all') {
        list = list.filter((post) => {
            const cats = Array.isArray(post.categories) ? post.categories : []
            if (cats.some((c) => (c?.slug || String(c?.id || c?.title)) === selectedFilter.value)) {
                return true
            }
            if (post.category) {
                const catKey = post.category.slug || String(post.category.id || post.category.title)
                return catKey === selectedFilter.value
            }
            return false
        })
    }

    const rawQuery = searchText.value.trim().toLowerCase()
    if (rawQuery) {
        const normQuery = normalizeText(rawQuery)
        list = list.filter((post) => {
            const catNames = (Array.isArray(post.categories) ? post.categories : [])
                .map((c) => c?.title || '')
                .join(' ')
            const haystack = [post.title, post.description, post.category?.title, catNames].filter(Boolean).join(' ')
            return haystack.toLowerCase().includes(rawQuery) || normalizeText(haystack).includes(normQuery)
        })
    }

    return list
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredResources.value.length / perPage)))

watch(totalPages, (maxPage) => {
    if (currentPage.value > maxPage) {
        currentPage.value = maxPage
    }
})

const paginatedResources = computed(() => {
    const start = (currentPage.value - 1) * perPage
    return filteredResources.value.slice(start, start + perPage)
})

const rangeStart = computed(() =>
    filteredResources.value.length === 0 ? 0 : (currentPage.value - 1) * perPage + 1
)
const rangeEnd = computed(() => Math.min(currentPage.value * perPage, filteredResources.value.length))

const paginationItems = computed(() => {
    const total = totalPages.value
    const current = currentPage.value
    if (total <= 7) {
        return Array.from({ length: total }, (_, i) => i + 1)
    }

    const items = [1]
    if (current > 3) items.push('...')

    const start = Math.max(2, current - 1)
    const end = Math.min(total - 1, current + 1)
    for (let i = start; i <= end; i++) {
        items.push(i)
    }

    if (current < total - 2) items.push('...')
    items.push(total)
    return items
})

const applyFilter = (slug) => {
    selectedFilter.value = slug
    currentPage.value = 1
}

const resetFilters = () => {
    selectedFilter.value = 'all'
    searchText.value = ''
    currentPage.value = 1
}

const changePage = (pageNum) => {
    if (pageNum < 1 || pageNum > totalPages.value || pageNum === currentPage.value) return
    currentPage.value = pageNum
    if (listingSectionRef.value && typeof listingSectionRef.value.scrollIntoView === 'function') {
        listingSectionRef.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
}

const isNewPost = (index) =>
    currentPage.value === 1 && selectedFilter.value === 'all' && !searchText.value && index < 2

// ---------- Post Helpers ----------
const postImage = (post) => {
    if (!post) return '/assets/images/posts/tin-tuc-hero.webp'
    if (typeof post.image === 'string' && post.image.trim()) return post.image
    if (post.image?.url) return post.image.url
    return '/assets/images/posts/tin-tuc-hero.webp'
}

const postHref = (post) => {
    const slug = post?.slug || ''
    const fallback = post?.url || (isEn.value ? `/en/posts/${slug}` : `/tin-tuc/${slug}`)
    if (!slug) return fallback
    return safeRoute('posts.show', { slug }, fallback)
}

const postCategoryTitle = (post) => post?.category?.title || post?.categories?.[0]?.title || ''

const EN_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

const formatDate = (raw) => {
    if (!raw) return ''
    const str = String(raw).trim()
    const isoMatch = str.match(/^(\d{4})-(\d{1,2})-(\d{1,2})/)
    let year, month, day

    if (isoMatch) {
        year = Number(isoMatch[1])
        month = Number(isoMatch[2])
        day = Number(isoMatch[3])
    } else {
        const dmyMatch = str.match(/^(\d{1,2})[/-](\d{1,2})[/-](\d{4})/)
        if (dmyMatch) {
            day = Number(dmyMatch[1])
            month = Number(dmyMatch[2])
            year = Number(dmyMatch[3])
        } else {
            const parsed = new Date(str)
            if (Number.isNaN(parsed.getTime())) return str
            day = parsed.getDate()
            month = parsed.getMonth() + 1
            year = parsed.getFullYear()
        }
    }

    if (!year || month < 1 || month > 12 || day < 1 || day > 31) return str
    const dd = String(day).padStart(2, '0')
    const mm = String(month).padStart(2, '0')

    if (isEn.value) {
        return `${dd} ${EN_MONTHS[month - 1]}, ${year}`
    }
    return `${dd} Th${mm}, ${year}`
}
</script>

<style scoped>
.hero-scrim {
    background: linear-gradient(
        180deg,
        rgba(1, 16, 30, 0.74) 0%,
        rgba(1, 16, 30, 0.66) 55%,
        rgba(1, 16, 30, 0.76) 100%
    );
}
@media (min-width: 768px) {
    .hero-scrim {
        background: linear-gradient(
            90deg,
            rgba(1, 16, 30, 0.92) 0%,
            rgba(1, 16, 30, 0.86) 22%,
            rgba(1, 16, 30, 0.72) 40%,
            rgba(1, 16, 30, 0.35) 54%,
            rgba(1, 16, 30, 0) 65%
        );
    }
}
.cta-texture {
    display: none;
    position: absolute;
    align-items: center;
    justify-content: center;
}
.cta-texture--left {
    left: -268.4px;
    top: 85.11px;
    width: 816.817px;
    height: 415.251px;
}
.cta-texture--right {
    right: -290.7px;
    top: -129px;
    width: 835.703px;
    height: 424.852px;
}
.cta-texture__inner {
    position: relative;
    flex: none;
}
.cta-texture__inner img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    max-width: none;
    object-fit: cover;
}
.cta-texture__inner--left {
    width: 779.966px;
    height: 259.989px;
    transform: rotate(-168.1deg) scaleY(-1);
}
.cta-texture__inner--right {
    width: 798px;
    height: 266px;
    transform: rotate(11.9deg) scaleY(-1);
}
@media (min-width: 1056px) {
    .cta-texture--right {
        display: flex;
    }
}
@media (min-width: 1088px) {
    .cta-texture--left {
        display: flex;
    }
}
</style>
