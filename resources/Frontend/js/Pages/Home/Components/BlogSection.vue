<template>
    <section
        v-if="displayPosts.length > 0"
        id="blog-section"
        class="bg-[#FAFAFA] py-8 md:py-16 lg:py-0"
    >
        <div class="app-container flex flex-col gap-6">
            <!-- Section Header (Figma 595:15484) -->
            <div class="flex items-center gap-[10px]">
                <span class="w-3 h-3 rounded-[2px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                <h2 class="font-bold text-[16px] md:text-[18px] leading-[1.6] text-[#005EA4]">
                    {{ sectionTitle }}
                </h2>
            </div>

            <!-- Cards + CTA Wrapper (Figma 595:15489) -->
            <div class="flex flex-col gap-8 md:gap-12 lg:gap-[64px]">
                <!-- 3-Column Blog Grid (Figma 595:15490) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <article
                        v-for="post in displayPosts"
                        :key="post.id || post.slug"
                        class="flex flex-col"
                    >
                        <Link
                            :href="resolvePostUrl(post)"
                            class="group flex flex-col gap-3 h-full focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]/40 rounded-[8px]"
                        >
                            <!-- Thumbnail Box (368x350) -->
                            <div class="relative w-full aspect-[368/350] bg-[#F1F1F1] rounded-[8px] overflow-hidden">
                                <img
                                    v-if="post.image?.url"
                                    :src="post.image.url"
                                    :alt="post.image.alt || post.title || ''"
                                    class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                                    loading="lazy"
                                    decoding="async"
                                    width="368"
                                    height="350"
                                />

                                <!-- CMS-Driven "New" Badge -->
                                <span
                                    v-if="post.is_featured || post.is_new"
                                    class="absolute top-[20px] left-[20px] z-10 inline-flex items-center justify-center bg-[#0075E7] text-[#FAFAFA] font-medium text-[14px] leading-[1.6] px-[12px] py-[4px] rounded-[4px]"
                                >
                                    {{ badgeLabel }}
                                </span>
                            </div>

                            <!-- Card Content -->
                            <div class="flex flex-col gap-3 flex-1">
                                <div class="flex flex-col gap-1">
                                    <h3
                                        class="font-bold text-[18px] md:text-[20px] leading-[1.6] text-[#00284B] group-hover:text-[#005EA4] transition-colors line-clamp-2 md:min-h-[64px] break-words"
                                    >
                                        {{ post.title }}
                                    </h3>
                                    <p
                                        v-if="post.description"
                                        class="font-normal text-[14px] md:text-[16px] leading-[1.6] text-[#697586] line-clamp-3 break-words"
                                    >
                                        {{ post.description }}
                                    </p>
                                </div>

                                <time
                                    v-if="formatPostDate(post)"
                                    :datetime="post.published_at || undefined"
                                    class="font-medium text-[14px] leading-[1.6] text-[#005EA4] mt-auto"
                                >
                                    {{ formatPostDate(post) }}
                                </time>
                            </div>
                        </Link>
                    </article>
                </div>

                <!-- Bottom Centered CTA Button (Figma 595:15494 / 595:15495) -->
                <div class="flex justify-center">
                    <Link
                        :href="postsIndexUrl"
                        class="inline-flex items-center justify-center gap-2 bg-[#005EA4] hover:bg-[#004D86] text-[#FAFAFA] font-medium text-[16px] leading-[1.6] px-[24px] py-[10px] rounded-[4px] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]/40"
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
import { Link, usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
    posts: {
        type: Array,
        default: () => [],
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

const sectionTitle = computed(() => t('Tin tức'))
const badgeLabel = computed(() => t('Mới'))
const postsIndexUrl = computed(() =>
    safeRoute('posts', undefined, isEn.value ? '/en/posts' : '/tin-tuc')
)
const ctaLabel = computed(() => t('Xem thêm bài viết'))

const validPosts = computed(() => {
    if (!Array.isArray(props.posts)) return []
    return props.posts.filter((item) => item && typeof item === 'object' && (item.title || item.slug))
})

const displayPosts = computed(() => validPosts.value.slice(0, 3))

function formatPostDate(post) {
    if (!post) return ''
    if (typeof post.formatted_date === 'string' && post.formatted_date.trim().length > 0) {
        return post.formatted_date.trim()
    }
    if (!post.published_at) return ''
    const raw = String(post.published_at).trim()
    const normalized = raw.includes(' ') && !raw.includes('T') ? raw.replace(' ', 'T') : raw
    const parsed = new Date(normalized)
    if (Number.isNaN(parsed.getTime())) return ''
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
