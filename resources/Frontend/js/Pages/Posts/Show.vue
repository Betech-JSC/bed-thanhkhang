<template>
    <Head>
        <title>{{ pageTitle }}</title>
        <meta v-if="pageDescription" name="description" :content="pageDescription" />
        <meta property="og:title" :content="pageTitle" />
        <meta v-if="pageDescription" property="og:description" :content="pageDescription" />
        <meta v-if="ogImage" property="og:image" :content="ogImage" />
        <meta property="og:type" content="article" />
    </Head>

    <div class="bg-surface text-ink-body">
        <!-- ======================================================== -->
        <!-- SECTION 1: ARTICLE HEADER (Figma 967:28778)               -->
        <!-- ======================================================== -->
        <section class="w-full bg-surface">
            <div class="app-container-narrow py-6 md:py-8 flex flex-col gap-4 md:gap-5">
                <!-- Breadcrumb (967:28779) -->
                <nav
                    :aria-label="tt('Breadcrumb')"
                    class="flex flex-wrap items-center gap-2 text-[14px] leading-[1.6]"
                >
                    <Link :href="homeHref" class="text-ink-subtle hover:text-primary transition-colors">
                        {{ tt('Trang chủ') }}
                    </Link>
                    <span class="text-ink-subtle" aria-hidden="true">/</span>
                    <Link :href="postsIndexHref" class="text-ink-subtle hover:text-primary transition-colors">
                        {{ tt('Tin tức') }}
                    </Link>
                    <template v-if="categoryTitle">
                        <span class="text-ink-subtle" aria-hidden="true">/</span>
                        <Link :href="postsIndexHref" class="text-ink-subtle hover:text-primary transition-colors">
                            {{ categoryTitle }}
                        </Link>
                    </template>
                    <template v-if="post.title">
                        <span class="text-ink-subtle" aria-hidden="true">/</span>
                        <span class="font-medium text-ink-body line-clamp-1" aria-current="page">
                            {{ post.title }}
                        </span>
                    </template>
                </nav>

                <!-- Article H1 (967:28788) -->
                <h1 class="text-[24px] sm:text-[28px] lg:text-h1-article font-bold leading-[1.35] text-dark max-w-[960px]">
                    {{ post.title }}
                </h1>

                <!-- Meta Row (967:28789) -->
                <div v-if="categoryTitle || formattedPublishedDate" class="flex flex-wrap items-center gap-3">
                    <span
                        v-if="categoryTitle"
                        class="bg-surface border border-badge-border rounded-[4px] px-3 py-1 text-[14px] font-medium text-ink-subtle"
                    >
                        {{ categoryTitle }}
                    </span>
                    <time
                        v-if="formattedPublishedDate"
                        :datetime="post.published_at"
                        class="text-[14px] font-medium text-ink-subtle"
                    >
                        {{ formattedPublishedDate }}
                    </time>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SECTION 2: ARTICLE BODY + SIDEBAR (Figma 967:28795)       -->
        <!-- ======================================================== -->
        <section class="w-full bg-surface">
            <div class="app-container-narrow py-6 md:py-12">
                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_336px] gap-8 lg:gap-12 items-start">
                    <!-- Left Content Column (967:28796) -->
                    <article class="flex flex-col gap-6 md:gap-7 min-w-0">
                        <!-- Cover Figure (967:28797) -->
                        <figure v-if="coverImage" class="w-full">
                            <div class="aspect-[768/420] w-full rounded-[8px] overflow-hidden bg-surface-gray-light">
                                <img
                                    :src="coverImage"
                                    :alt="post.image?.alt || post.title || ''"
                                    class="w-full h-full object-cover"
                                    loading="eager"
                                />
                            </div>
                            <figcaption
                                v-if="coverCaption"
                                class="text-[14px] italic leading-[1.6] text-ink-subtle mt-2.5"
                            >
                                {{ coverCaption }}
                            </figcaption>
                        </figure>

                        <!-- Lead Paragraph (967:28800) -->
                        <p v-if="post.description" class="text-[16px] lg:text-[18px] leading-[1.65] text-ink-dark">
                            {{ post.description }}
                        </p>

                        <!-- Article Rich HTML -->
                        <div
                            v-if="post.content"
                            ref="articleContentRef"
                            class="article-prose min-w-0"
                            v-html="post.content"
                        />

                        <!-- Article Tags & Share Bar (967:28876) -->
                        <div class="border-t border-gray-modern-200 pt-6 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-bold text-[14px] text-dark-slate">
                                    {{ tt('Danh mục:') }}
                                </span>
                                <Link
                                    v-for="cat in postCategories"
                                    :key="cat.slug || cat.id || cat.title"
                                    :href="postsIndexHref"
                                    class="bg-white border border-gray-modern-200 hover:border-primary hover:text-primary rounded-[6px] px-4 py-[9px] text-[14px] font-medium text-dark-slate transition-colors"
                                >
                                    {{ cat.title }}
                                </Link>
                                <Link
                                    v-if="postCategories.length === 0"
                                    :href="postsIndexHref"
                                    class="bg-white border border-gray-modern-200 hover:border-primary hover:text-primary rounded-[6px] px-4 py-[9px] text-[14px] font-medium text-dark-slate transition-colors"
                                >
                                    {{ tt('Tin tức') }}
                                </Link>
                            </div>

                            <button
                                type="button"
                                class="bg-white border border-gray-modern-200 hover:border-primary rounded-[6px] px-4 py-[9px] inline-flex items-center gap-2 text-[14px] font-medium text-dark-slate transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                @click="handleShare"
                            >
                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="w-[18px] h-[18px] shrink-0"
                                    aria-hidden="true"
                                >
                                    <circle cx="18" cy="5" r="3" />
                                    <circle cx="6" cy="12" r="3" />
                                    <circle cx="18" cy="19" r="3" />
                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
                                </svg>
                                <span>{{ shareCopied ? tt('Đã sao chép liên kết') : tt('Chia sẻ') }}</span>
                            </button>
                        </div>
                    </article>

                    <!-- Right Sidebar (967:28884) -->
                    <aside class="w-full lg:w-[336px] lg:sticky lg:top-24 flex flex-col gap-6">
                        <!-- Widget 1: Mục lục bài viết (967:28885) -->
                        <div
                            v-if="tocItems.length > 0"
                            class="bg-white border border-gray-modern-200 rounded-[8px] px-5 py-[22px] flex flex-col gap-3.5"
                        >
                            <h2 class="text-[16px] font-bold leading-[1.4] text-dark">
                                {{ tt('Mục lục bài viết') }}
                            </h2>
                            <nav :aria-label="tt('Mục lục bài viết')" class="flex flex-col gap-1">
                                <a
                                    v-for="item in tocItems"
                                    :key="item.id"
                                    :href="'#' + item.id"
                                    class="block py-2 pr-3 text-[14px] leading-[1.5] rounded-r-[4px] border-l-[3px] transition-colors"
                                    :class="[
                                        item.level === 3 ? 'pl-6' : 'pl-3',
                                        activeTocId === item.id
                                            ? 'bg-surface-100 border-primary text-primary font-bold'
                                            : 'border-transparent text-ink-body hover:text-primary hover:bg-surface-50',
                                    ]"
                                    @click.prevent="scrollToHeading(item.id)"
                                >
                                    {{ item.text }}
                                </a>
                            </nav>
                        </div>

                        <!-- Widget 2: Tư vấn kỹ thuật (967:28896) -->
                        <div class="bg-dark rounded-[8px] p-6 flex flex-col gap-6">
                            <div class="flex flex-col gap-2">
                                <h2 class="text-[18px] font-bold leading-[1.45] text-surface">
                                    {{ tt('Cần tra cứu mã xích phù hợp?') }}
                                </h2>
                                <p class="text-[14px] leading-[1.6] text-secondary">
                                    {{
                                        tt(
                                            'Đội ngũ kỹ sư Thành Khang Tecsat sẵn sàng hỗ trợ tra cứu thông số kỹ thuật, chọn đúng dòng xích tải và báo giá nhanh trong 2 giờ làm việc.'
                                        )
                                    }}
                                </p>
                            </div>
                            <Link
                                :href="contactHref"
                                class="w-full bg-primary hover:bg-primary-hover text-surface text-[14px] font-medium px-5 py-2 rounded-[4px] text-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                            >
                                {{ tt('Liên hệ tư vấn') }}
                            </Link>
                        </div>

                        <!-- Widget 3: Bài viết xem nhiều (967:28901) -->
                        <div
                            v-if="displayPopularPosts.length > 0"
                            class="bg-white border border-gray-modern-200 rounded-[8px] p-5 flex flex-col gap-3.5"
                        >
                            <h2 class="text-[16px] font-bold leading-[1.4] text-dark">
                                {{ tt('Bài viết xem nhiều') }}
                            </h2>
                            <div class="flex flex-col gap-4">
                                <Link
                                    v-for="(item, idx) in displayPopularPosts"
                                    :key="item.id || item.slug || idx"
                                    :href="postHref(item)"
                                    class="group flex items-start gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-[6px]"
                                >
                                    <div class="w-[76px] h-[60px] rounded-[6px] overflow-hidden bg-surface-gray-light shrink-0">
                                        <img
                                            :src="postImage(item)"
                                            :alt="item.image?.alt || item.title"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy"
                                        />
                                    </div>
                                    <div class="min-w-0 flex-1 flex flex-col gap-1">
                                        <h3
                                            class="text-[14px] font-bold leading-[1.45] text-ink-dark group-hover:text-primary transition-colors line-clamp-2"
                                        >
                                            {{ item.title }}
                                        </h3>
                                        <time
                                            v-if="formatDate(item.published_at)"
                                            :datetime="item.published_at"
                                            class="text-[12px] font-medium text-primary"
                                        >
                                            {{ formatDate(item.published_at) }}
                                        </time>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SECTION 3: BÀI VIẾT LIÊN QUAN (Figma 967:28918)           -->
        <!-- ======================================================== -->
        <section v-if="displayRelatedPosts.length > 0" class="w-full bg-surface">
            <div class="app-container-narrow py-10 lg:py-16 flex flex-col gap-6 lg:gap-8">
                <!-- Header Row (967:28919) -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-2.5">
                        <span class="block w-3 h-3 rounded-[2px] bg-primary shrink-0" aria-hidden="true" />
                        <h2 class="text-[18px] font-bold leading-[1.6] text-primary">
                            {{ tt('Bài viết liên quan') }}
                        </h2>
                    </div>
                    <Link
                        :href="postsIndexHref"
                        class="bg-secondary hover:bg-secondary-hover text-primary-active text-[14px] font-medium px-5 py-2 rounded-[4px] inline-flex items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                    >
                        {{ tt('Xem tất cả bài viết') }}
                    </Link>
                </div>

                <!-- 3-Column Related Cards Grid (967:28926) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <Link
                        v-for="(item, index) in displayRelatedPosts"
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
                                v-if="index < 2"
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
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SECTION 4: CTA TƯ VẤN KỸ THUẬT (Figma 967:28930)          -->
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
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick, getCurrentInstance } from 'vue'
import { usePage, Head, Link } from '@inertiajs/inertia-vue3'

const props = defineProps({
    post: { type: Object, default: () => ({}) },
    related_posts: { type: Array, default: () => [] },
    popular_posts: { type: Array, default: () => [] },
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
const postsIndexHref = computed(() => safeRoute('posts', undefined, isEn.value ? '/en/posts' : '/tin-tuc'))
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

// ---------- SEO & Cover ----------
const coverImage = computed(() => {
    const img = props.post?.image
    if (typeof img === 'string' && img.trim()) return img.trim()
    if (img && typeof img.url === 'string' && img.url.trim()) return img.url.trim()
    return ''
})

const coverCaption = computed(() => {
    const img = props.post?.image
    if (!img || typeof img !== 'object') return ''
    if (typeof img.caption === 'string' && img.caption.trim()) return img.caption.trim()
    if (typeof img.alt === 'string' && img.alt.trim() && img.alt.trim() !== String(props.post?.title || '').trim()) {
        return img.alt.trim()
    }
    return ''
})

const pageTitle = computed(
    () => props.seo?.title || props.post?.title || trans('Tin tức & Kiến thức Kỹ thuật - Thành Khang Tecsat')
)
const pageDescription = computed(() => props.seo?.description || props.post?.description || '')
const ogImage = computed(() => {
    const seoImg = props.seo?.image
    if (typeof seoImg === 'string' && seoImg.trim()) return seoImg.trim()
    if (seoImg && typeof seoImg.url === 'string' && seoImg.url.trim()) return seoImg.url.trim()
    return coverImage.value
})

// ---------- Categories & Related/Popular Posts ----------
const postCategories = computed(() => {
    const seen = new Map()
    const list =
        Array.isArray(props.post?.categories) && props.post.categories.length > 0
            ? props.post.categories
            : props.post?.category
              ? [props.post.category]
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
    return Array.from(seen.values())
})

const categoryTitle = computed(() => postCategories.value[0]?.title || '')

const displayRelatedPosts = computed(() =>
    (Array.isArray(props.related_posts) ? props.related_posts : []).filter(Boolean).slice(0, 3)
)

const displayPopularPosts = computed(() => {
    const popular = (Array.isArray(props.popular_posts) ? props.popular_posts : []).filter(Boolean)
    if (popular.length > 0) return popular.slice(0, 3)
    return displayRelatedPosts.value.slice(0, 3)
})

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

const formattedPublishedDate = computed(() => formatDate(props.post?.published_at))

// ---------- Dynamic Table of Contents & Content Enhancement ----------
const articleContentRef = ref(null)
const tocItems = ref([])
const activeTocId = ref('')
let headingElements = []

const slugifyHeading = (text, fallbackIndex) => {
    const base = String(text || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .slice(0, 60)
    return base ? `section-${base}` : `section-${fallbackIndex + 1}`
}

const updateActiveHeading = () => {
    if (!headingElements.length || typeof window === 'undefined') return
    const offset = 120
    let currentId = headingElements[0].id
    for (let i = 0; i < headingElements.length; i++) {
        const rect = headingElements[i].getBoundingClientRect()
        if (rect.top <= offset) {
            currentId = headingElements[i].id
        } else {
            break
        }
    }
    activeTocId.value = currentId
}

const buildTocAndEnhanceContent = async () => {
    await nextTick()
    const container = articleContentRef.value
    if (!container) {
        tocItems.value = []
        headingElements = []
        activeTocId.value = ''
        return
    }

    // Wrap bare tables for horizontal scroll on mobile
    const tables = container.querySelectorAll('table')
    tables.forEach((table) => {
        if (table.parentElement && !table.parentElement.classList.contains('article-table-wrap')) {
            const wrapper = document.createElement('div')
            wrapper.className = 'article-table-wrap'
            table.parentNode.insertBefore(wrapper, table)
            wrapper.appendChild(table)
        }
    })

    // Extract h2, h3 headings for TOC
    const nodes = Array.from(container.querySelectorAll('h2, h3'))
    const usedIds = new Set()
    const items = []
    const validElements = []

    nodes.forEach((el, idx) => {
        const text = (el.textContent || '').trim()
        if (!text) return
        let id = el.getAttribute('id')
        if (!id || usedIds.has(id)) {
            let candidate = slugifyHeading(text, idx)
            let suffix = 2
            while (usedIds.has(candidate)) {
                candidate = `${slugifyHeading(text, idx)}-${suffix}`
                suffix++
            }
            id = candidate
            el.setAttribute('id', id)
        }
        usedIds.add(id)
        items.push({
            id,
            text,
            level: el.tagName.toLowerCase() === 'h3' ? 3 : 2,
        })
        validElements.push(el)
    })

    tocItems.value = items
    headingElements = validElements
    activeTocId.value = items[0]?.id || ''
    updateActiveHeading()
}

const scrollToHeading = (id) => {
    if (typeof document === 'undefined') return
    const target = document.getElementById(id)
    if (!target) return
    activeTocId.value = id
    const top = target.getBoundingClientRect().top + window.scrollY - 96
    window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' })
}

// ---------- Share Action ----------
const shareCopied = ref(false)
let shareResetTimer = null

const triggerCopiedFeedback = () => {
    shareCopied.value = true
    if (shareResetTimer) clearTimeout(shareResetTimer)
    shareResetTimer = setTimeout(() => {
        shareCopied.value = false
    }, 2500)
}

const handleShare = async () => {
    if (typeof window === 'undefined') return
    const shareUrl = window.location.href

    try {
        if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
            await navigator.clipboard.writeText(shareUrl)
            triggerCopiedFeedback()
            return
        }
    } catch (e) {
        // Fallback below
    }

    if (navigator.share && typeof navigator.share === 'function') {
        try {
            await navigator.share({
                title: pageTitle.value,
                text: pageDescription.value,
                url: shareUrl,
            })
            return
        } catch (e) {
            // User cancelled or share failed; fall through to legacy copy
        }
    }

    try {
        const textarea = document.createElement('textarea')
        textarea.value = shareUrl
        textarea.setAttribute('readonly', '')
        textarea.style.position = 'fixed'
        textarea.style.opacity = '0'
        document.body.appendChild(textarea)
        textarea.select()
        document.execCommand('copy')
        document.body.removeChild(textarea)
        triggerCopiedFeedback()
    } catch (e) {
        // Ignore copy failure
    }
}

watch(
    () => props.post?.content,
    () => {
        buildTocAndEnhanceContent()
    }
)

onMounted(() => {
    buildTocAndEnhanceContent()
    window.addEventListener('scroll', updateActiveHeading, { passive: true })
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateActiveHeading)
    if (shareResetTimer) clearTimeout(shareResetTimer)
})
</script>

<style scoped>
/* ========================================================= */
/* Article Rich HTML Prose Styling (Figma 967:28751)         */
/* ========================================================= */
.article-prose {
    color: var(--color-gray-600, #4b5565);
    font-size: 15px;
    line-height: 1.6;
    word-break: break-word;
}

@media (min-width: 1024px) {
    .article-prose {
        font-size: 16px;
    }
}

.article-prose :deep(h2) {
    font-size: 20px;
    line-height: 1.4;
    font-weight: 700;
    color: var(--color-dark, #00284b);
    margin-top: 2rem;
    margin-bottom: 0.875rem;
    scroll-margin-top: 6rem;
}

@media (min-width: 1024px) {
    .article-prose :deep(h2) {
        font-size: 24px;
    }
}

.article-prose :deep(h2:first-child) {
    margin-top: 0;
}

.article-prose :deep(h3) {
    font-size: 18px;
    line-height: 1.45;
    font-weight: 700;
    color: var(--color-dark, #00284b);
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    scroll-margin-top: 6rem;
}

@media (min-width: 1024px) {
    .article-prose :deep(h3) {
        font-size: 20px;
    }
}

.article-prose :deep(h4) {
    font-size: 16px;
    line-height: 1.5;
    font-weight: 700;
    color: var(--color-dark, #00284b);
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}

@media (min-width: 1024px) {
    .article-prose :deep(h4) {
        font-size: 18px;
    }
}

.article-prose :deep(p) {
    font-size: 15px;
    line-height: 1.6;
    color: var(--color-gray-600, #4b5565);
    margin-bottom: 1rem;
}

@media (min-width: 1024px) {
    .article-prose :deep(p) {
        font-size: 16px;
    }
}

.article-prose :deep(p:last-child) {
    margin-bottom: 0;
}

.article-prose :deep(strong),
.article-prose :deep(b) {
    color: var(--color-gray-800, #202939);
    font-weight: 700;
}

.article-prose :deep(a) {
    color: var(--color-primary, #005ea4);
    text-decoration: underline;
    text-underline-offset: 2px;
    transition: color 0.2s ease;
}

.article-prose :deep(a:hover) {
    color: var(--color-primary-hover, #004d86);
}

.article-prose :deep(ul) {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.article-prose :deep(ol) {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.article-prose :deep(li) {
    margin-bottom: 0.5rem;
    color: var(--color-gray-600, #4b5565);
    line-height: 1.6;
}

.article-prose :deep(li:last-child) {
    margin-bottom: 0;
}

.article-prose :deep(blockquote) {
    background-color: var(--color-surface-100, #eef2f6);
    border-left: 5px solid var(--color-primary, #005ea4);
    border-radius: 0 8px 8px 0;
    padding: 1rem 1.25rem;
    margin: 1.5rem 0;
    color: var(--color-dark-slate, #364152);
    font-size: 15px;
    line-height: 1.65;
}

.article-prose :deep(blockquote p) {
    color: inherit;
    margin-bottom: 0.5rem;
}

.article-prose :deep(blockquote p:last-child) {
    margin-bottom: 0;
}

.article-prose :deep(.article-table-wrap) {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 1.5rem 0;
    border-radius: 8px;
    border: 1px solid #e3e8ef;
    background-color: #ffffff;
}

.article-prose :deep(table) {
    width: 100%;
    min-width: 520px;
    border-collapse: collapse;
    font-size: 14px;
    line-height: 1.5;
    margin: 0;
}

.article-prose :deep(thead) {
    background-color: var(--color-dark, #00284b);
    color: var(--color-surface, #fafafa);
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.02em;
}

.article-prose :deep(thead th) {
    padding: 0.875rem 1rem;
    text-align: left;
    font-weight: 700;
    color: var(--color-surface, #fafafa);
    border-bottom: 1px solid #e3e8ef;
}

.article-prose :deep(tbody td) {
    padding: 0.875rem 1rem;
    color: var(--color-dark-slate, #364152);
    border-bottom: 1px solid #e3e8ef;
    vertical-align: top;
}

.article-prose :deep(tbody tr:nth-child(odd)) {
    background-color: #ffffff;
}

.article-prose :deep(tbody tr:nth-child(even)) {
    background-color: #f8fafc;
}

.article-prose :deep(tbody tr:last-child td) {
    border-bottom: 0;
}

.article-prose :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1.5rem 0;
    display: block;
}

.article-prose :deep(figure) {
    margin: 1.5rem 0;
}

.article-prose :deep(figure img) {
    margin: 0;
}

.article-prose :deep(figcaption) {
    font-size: 14px;
    font-style: italic;
    line-height: 1.6;
    color: var(--color-gray-500, #697586);
    margin-top: 0.625rem;
}

.article-prose :deep(hr) {
    border: 0;
    border-top: 1px solid #e3e8ef;
    margin: 1.75rem 0;
}

/* ========================================================= */
/* CTA Texture (matches Posts/Index.vue)                     */
/* ========================================================= */
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
