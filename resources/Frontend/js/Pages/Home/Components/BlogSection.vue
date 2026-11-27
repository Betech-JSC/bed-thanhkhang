<template>
    <section
        v-if="displayPosts.length > 0"
        id="blog-section"
        class="bg-[#FAFAFA] py-10 md:py-12 lg:py-0"
    >
        <div class="app-container-narrow flex flex-col gap-6">
            <!-- Section Header (Figma 1171:41650 / 1191:6475) -->
            <div class="flex items-center gap-2 lg:gap-[10px]">
                <span class="w-[10px] h-[10px] lg:w-3 lg:h-3 rounded-[2px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                <h2 class="font-bold text-[16px] lg:text-[18px] leading-[24px] lg:leading-[1.6] text-[#005EA4]">
                    {{ sectionTitle }}
                </h2>
            </div>

            <!-- Cards + CTA Wrapper (Figma 1171:41655 — 64px desktop gap to CTA; 24px mobile gap) -->
            <div class="flex flex-col gap-6 md:gap-[64px]">
                <!-- 3-Column Blog Grid (Figma 1171:41656 — 1152px inner, gap-8 -> 363px Card) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <article
                        v-for="post in displayPosts"
                        :key="post.id || post.slug"
                        class="flex flex-col"
                    >
                        <Link
                            :href="resolvePostUrl(post)"
                            class="group flex flex-col gap-3 h-full focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]/40 rounded-[8px]"
                        >
                            <!-- Thumbnail Box (358x240 Mobile Figma 1191:6479, 363x240 Desktop Figma 184:1338) -->
                            <div class="relative w-full aspect-[358/240] md:aspect-[363/240] bg-[#F1F1F1] rounded-[8px] overflow-hidden">
                                <img
                                    v-if="post.image?.url"
                                    :src="post.image.url"
                                    :alt="post.image.alt || post.title || ''"
                                    class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                                    loading="lazy"
                                    decoding="async"
                                    width="363"
                                    height="240"
                                />

                                <!-- CMS-Driven "New" Badge (Figma 1264:48173 top-4 left-4 bg-[#005EA4] "Mới") -->
                                <span
                                    v-if="post.is_featured || post.is_new"
                                    class="absolute top-4 left-4 z-10 inline-flex items-center justify-center bg-[#005EA4] text-[#FAFAFA] font-medium text-[13px] lg:text-[14px] leading-[1.6] px-[12px] py-[4px] rounded-[4px]"
                                >
                                    {{ badgeLabel }}
                                </span>
                            </div>

                            <!-- Card Content (Figma 1264:48173 — text stack + 20px gap + meta row) -->
                            <div class="flex flex-col gap-4 lg:gap-[20px] flex-1">
                                <div class="flex flex-col gap-[4px]">
                                    <h3
                                        class="font-bold text-[18px] lg:text-[20px] leading-[1.6] text-[#00284B] group-hover:text-[#005EA4] transition-colors line-clamp-2 lg:min-h-[64px] break-words"
                                    >
                                        {{ post.title }}
                                    </h3>
                                    <p
                                        v-if="post.description"
                                        class="font-normal text-[14px] lg:text-[16px] leading-[1.6] text-[#697586] line-clamp-3 lg:min-h-[76.8px] break-words"
                                    >
                                        {{ post.description }}
                                    </p>
                                </div>

                                <!-- Meta Row: Category Pill Left + Published Date Right (Figma 1264:48173) -->
                                <div class="flex items-center justify-between gap-3 mt-auto">
                                    <span
                                        v-if="postCategoryTitle(post)"
                                        class="inline-flex items-center px-[12px] py-[4px] rounded-[4px] bg-[#FAFAFA] border border-[#CDD5DF] font-medium text-[13px] lg:text-[14px] leading-[1.6] text-[#697586] truncate max-w-[65%]"
                                    >
                                        {{ postCategoryTitle(post) }}
                                    </span>

                                    <time
                                        v-if="formatPostDate(post)"
                                        :datetime="post.published_at || undefined"
                                        class="font-medium text-[13px] lg:text-[14px] leading-[1.6] text-[#697586] ml-auto shrink-0"
                                    >
                                        {{ formatPostDate(post) }}
                                    </time>
                                </div>
                            </div>
                        </Link>
                    </article>
                </div>

                <!-- Bottom Centered CTA Button (Figma 1264:48176 — 203x49px, 18px/1.6) -->
                <div class="flex justify-center">
                    <Link
                        :href="postsIndexUrl"
                        class="w-full md:w-auto h-[49px] md:min-w-[203px] inline-flex items-center justify-center gap-2 bg-[#005EA4] hover:bg-[#004D86] text-[#FAFAFA] font-medium text-[18px] leading-[1.6] px-[24px] py-[10px] rounded-[4px] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]/40"
                    >
                        {{ ctaLabel }}
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'

const props = defineProps({
    posts: {
        type: Array,
        default: () => [],
    },
})

const instance = getCurrentInstance()
const { translate: t, currentLocale, isEn } = useTranslate()

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

const sectionTitle = computed(() => t('Tin tức'))
const badgeLabel = computed(() => t('Mới'))
const postsIndexUrl = computed(() =>
    safeRoute('posts', undefined, isEn.value ? '/en/posts' : '/tin-tuc')
)
const ctaLabel = computed(() => t('Xem thêm bài viết'))

function postCategoryTitle(post) {
    if (!post || typeof post !== 'object') return ''
    const cat = post.category
    if (cat && typeof cat === 'object') {
        return String(cat.title || cat.name || '').trim()
    }
    if (typeof post.category_title === 'string') {
        return post.category_title.trim()
    }
    return ''
}

const validPosts = computed(() => {
    if (!Array.isArray(props.posts)) return []
    return props.posts.filter((item) => item && typeof item === 'object' && (item.title || item.slug))
})

const displayPosts = computed(() => validPosts.value.slice(0, 3))

function formatPostDate(post) {
    if (!post) return ''
    const rawFormatted = typeof post.formatted_date === 'string' ? post.formatted_date.trim() : ''
    if (!isEn.value && /^\d{1,2}\s+Th\d{1,2},\s+\d{4}$/.test(rawFormatted)) {
        return rawFormatted
    }
    if (isEn.value && rawFormatted.length > 0) {
        return rawFormatted
    }
    const sourceDate = post.published_at || rawFormatted
    if (!sourceDate) return ''
    const raw = String(sourceDate).trim()
    const normalized = raw.includes(' ') && !raw.includes('T') && /^\d{4}-\d{2}-\d{2}/.test(raw)
        ? raw.replace(' ', 'T')
        : raw
    const parsed = new Date(normalized)
    if (Number.isNaN(parsed.getTime())) return rawFormatted
    if (!isEn.value) {
        const day = String(parsed.getDate()).padStart(2, '0')
        const month = parsed.getMonth() + 1
        const year = parsed.getFullYear()
        return `${day} Th${month}, ${year}`
    }
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    }).format(parsed)
}

function resolvePostUrl(post) {
    if (!post) return postsIndexUrl.value
    const rawUrl = typeof post.url === 'string' ? post.url.trim() : ''
    if (rawUrl) {
        if (rawUrl.startsWith('/') && !rawUrl.startsWith('//')) {
            return rawUrl
        }
        try {
            const parsed = new URL(rawUrl)
            if (parsed.pathname) {
                return parsed.pathname + (parsed.search || '')
            }
        } catch (e) {
            // Fall through to slug fallback
        }
    }
    if (post.slug) {
        return safeRoute('posts.show', { slug: post.slug }, `${postsIndexUrl.value}/${post.slug}`)
    }
    return postsIndexUrl.value
}
</script>
