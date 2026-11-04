<template>
    <Head>
        <title>{{ pageTitle }}</title>
        <meta name="description" :content="pageDescription" />
        <meta property="og:title" :content="pageTitle" />
        <meta property="og:description" :content="pageDescription" />
        <meta property="og:type" content="article" />
    </Head>

    <div class="bg-[#111322] min-h-screen text-white selection:bg-primary selection:text-white pt-6 sm:pt-10 pb-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1152px]">
            <!-- Header Section -->
            <div class="space-y-3 mb-8 sm:mb-12 border-b border-[#5D6B98]/30 pb-6 sm:pb-8">
                <span class="text-sm font-semibold tracking-wider text-[#84ADFF] uppercase select-none leading-[20px] block">
                    {{ tt('CHÍNH SÁCH & ĐIỀU KHOẢN') }}
                </span>
                <h1 class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] tracking-tight leading-tight lg:leading-[46px]">
                    {{ tt('Chính sách & Cam kết xưởng in 3D HH Maker') }}
                </h1>
                <p class="text-[#B9C0D4] text-sm sm:text-base leading-relaxed max-w-3xl">
                    {{ tt('Thông tin chi tiết về điều khoản dịch vụ, bảo vệ quyền sở hữu trí tuệ bản vẽ CAD và chính sách bảo hành in 3D.') }}
                </p>
            </div>

            <!-- Mobile Category Selector -->
            <div v-if="list_sidebar && list_sidebar.length" class="lg:hidden mb-6">
                <button
                    type="button"
                    @click="isOpenMobile = !isOpenMobile"
                    class="w-full bg-white/10 border border-[#5D6B98] backdrop-blur-[10px] rounded-xl px-4 py-3 flex items-center justify-between text-left text-[#FAFAFA] font-medium"
                >
                    <span class="truncate">{{ content ? tt(content.title) : tt('Danh mục chính sách') }}</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 transition-transform duration-200"
                        :class="{ 'rotate-180': isOpenMobile }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    v-show="isOpenMobile"
                    class="mt-2 bg-[#1A1C32] border border-[#5D6B98] rounded-xl p-2 space-y-1 shadow-xl"
                >
                    <Link
                        v-for="(policy, index) in list_sidebar"
                        :key="index"
                        :href="getPolicyUrl(policy)"
                        @click="isOpenMobile = false"
                        class="block px-3 py-2.5 rounded-lg text-sm transition-colors"
                        :class="checkActive(policy.slug)
                            ? 'bg-[#243971] text-white font-semibold border border-[#528BFF]'
                            : 'text-[#B9C0D4] hover:bg-white/5 hover:text-white'"
                    >
                        {{ tt(policy.title) }}
                    </Link>
                </div>
            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Desktop Sidebar Menu -->
                <aside
                    v-if="list_sidebar && list_sidebar.length"
                    class="hidden lg:block lg:col-span-4 xl:col-span-3 sticky top-24"
                >
                    <div class="bg-white/5 border border-[#5D6B98]/40 backdrop-blur-[10px] rounded-2xl p-4 sm:p-5 shadow-lg space-y-2">
                        <div class="text-xs font-semibold tracking-wider text-[#84ADFF] uppercase px-3 py-2">
                            {{ tt('Danh mục chính sách') }}
                        </div>
                        <nav class="space-y-1.5">
                            <Link
                                v-for="(policy, index) in list_sidebar"
                                :key="index"
                                :href="getPolicyUrl(policy)"
                                class="flex items-center px-3.5 py-3 rounded-xl text-sm font-medium transition-all duration-200 border"
                                :class="checkActive(policy.slug)
                                    ? 'bg-[#243971] text-[#FAFAFA] border-[#528BFF] shadow-[0_0_12px_rgba(82,139,255,0.35)] font-semibold'
                                    : 'border-transparent text-[#B9C0D4] hover:bg-white/5 hover:text-white'"
                            >
                                <span class="truncate">{{ tt(policy.title) }}</span>
                            </Link>
                        </nav>
                    </div>
                </aside>

                <!-- Content Area -->
                <section class="col-span-12 lg:col-span-8 xl:col-span-9">
                    <article
                        v-if="content"
                        class="bg-white/5 border border-[#5D6B98]/40 backdrop-blur-[10px] rounded-2xl p-6 sm:p-8 md:p-10 shadow-2xl space-y-6"
                    >
                        <header class="border-b border-[#5D6B98]/30 pb-4 space-y-2">
                            <h2 class="text-2xl sm:text-3xl font-bold text-[#FAFAFA] tracking-tight">
                                {{ tt(content.title) }}
                            </h2>
                            <div class="text-xs sm:text-sm text-[#84ADFF] flex items-center gap-2">
                                <span>{{ tt('Cập nhật lần cuối') }}:</span>
                                <span class="font-mono text-[#D1E0FF]">{{ content.formatted_updated_at || '11/2026' }}</span>
                            </div>
                        </header>

                        <div
                            v-html="content.content"
                            class="policy-prose"
                        />
                    </article>

                    <div
                        v-else
                        class="bg-white/5 border border-[#5D6B98]/40 rounded-2xl p-12 text-center text-[#B9C0D4]"
                    >
                        {{ tt('Không tìm thấy nội dung chính sách.') }}
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'

export default {
    name: 'PolicyPage',
    components: {
        Head,
        Link,
    },
    props: {
        list_sidebar: {
            type: Array,
            default: () => [],
        },
        content: {
            type: Object,
            default: () => null,
        },
        seo: {
            type: Object,
            default: () => null,
        },
    },
    data() {
        return {
            isOpenMobile: false,
        }
    },
    computed: {
        currentLocale() {
            return this.$inertia?.page?.props?.locale?.current || this.$page?.props?.locale?.current || 'vi'
        },
        pageTitle() {
            if (this.seo?.title) return this.seo.title
            const itemTitle = this.content ? this.tt(this.content.title) : this.tt('Chính sách & Điều khoản')
            return this.currentLocale === 'en'
                ? `${itemTitle} - HH Maker | 3D Printing Workshop`
                : `${itemTitle} - ${this.tt('HH Maker | Xưởng in 3D')}`
        },
        pageDescription() {
            if (this.seo?.description) return this.seo.description
            return this.currentLocale === 'en'
                ? 'Official service policies, warranty terms, and privacy commitments for 3D printing and custom modeling at HH Maker.'
                : this.tt('Thông tin chính sách dịch vụ, cam kết bảo mật và quy định bảo hành in 3D chuyên nghiệp tại HH Maker.')
        },
    },
    methods: {
        checkActive(slug) {
            const currentSlug = this.content?.slug || ''
            if (slug === currentSlug) return true
            const currentPath = (this.$page?.url || '').split('?')[0]
            return currentPath.endsWith(`/${slug}`)
        },
        getPolicyUrl(policy) {
            const slug = policy?.slug || ''
            if (typeof this.route === 'function') {
                try {
                    return this.route('policies.show', slug)
                } catch (e) {
                    // Fallback to localized URL
                }
            }
            return this.currentLocale === 'en' ? `/en/policies/${slug}` : `/chinh-sach/${slug}`
        },
    },
}
</script>

<style lang="scss">
.policy-prose {
    color: #D1E0FF;
    line-height: 1.75;
    font-size: 1rem;

    h3 {
        color: #FAFAFA;
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 1.75rem;
        margin-bottom: 0.75rem;
        font-family: inherit;
        border-left: 3px solid #2970FF;
        padding-left: 0.75rem;
    }

    h4 {
        color: #FAFAFA;
        font-size: 1.1rem;
        font-weight: 600;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
    }

    p {
        color: #B9C0D4;
        margin-bottom: 1rem;
        font-size: 0.975rem;
    }

    ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
        color: #B9C0D4;

        li {
            margin-bottom: 0.5rem;
            color: #B9C0D4;
        }
    }

    ol {
        list-style-type: decimal;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
        color: #B9C0D4;

        li {
            margin-bottom: 0.5rem;
            color: #B9C0D4;
        }
    }

    strong {
        color: #FAFAFA;
        font-weight: 600;
    }
}
</style>
