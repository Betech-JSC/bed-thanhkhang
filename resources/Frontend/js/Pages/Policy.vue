<template>
    <Head>
        <title>{{ pageTitle }}</title>
        <meta name="description" :content="pageDescription" />
        <meta property="og:title" :content="pageTitle" />
        <meta property="og:description" :content="pageDescription" />
        <meta property="og:type" content="article" />
    </Head>

    <div class="bg-[#FAFAFA] min-h-screen text-[#364152] selection:bg-[#005EA4] selection:text-white pt-6 sm:pt-10 pb-16 lg:pb-20">
        <div class="app-container-narrow">
            <!-- Header Section -->
            <div class="bg-white border border-[#E3E8EF] rounded-[12px] p-6 sm:p-8 shadow-xs space-y-3 mb-6 sm:mb-8">
                <div class="flex items-center gap-2.5">
                    <span class="block w-2.5 h-2.5 rounded-[2px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                    <span class="text-xs sm:text-sm font-bold tracking-wider text-[#005EA4] uppercase select-none leading-[20px]">
                        {{ tt('CHÍNH SÁCH & ĐIỀU KHOẢN') }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-[36px] font-bold text-[#00284B] tracking-tight leading-tight lg:leading-[46px]">
                    {{ tt('Chính sách & Cam kết Thành Khang') }}
                </h1>
                <p class="text-[#4B5565] text-sm sm:text-base leading-relaxed max-w-3xl">
                    {{ tt('Thông tin chi tiết về điều khoản bán hàng, bảo mật thông số bản vẽ kỹ thuật và chính sách bảo hành thiết bị công nghiệp.') }}
                </p>
            </div>

            <!-- Mobile Category Selector -->
            <div v-if="list_sidebar && list_sidebar.length" class="lg:hidden mb-6">
                <button
                    id="policy-mobile-selector-btn"
                    type="button"
                    @click="isOpenMobile = !isOpenMobile"
                    :aria-expanded="isOpenMobile"
                    aria-controls="policy-mobile-menu"
                    class="w-full min-h-[44px] bg-white border border-[#E3E8EF] hover:border-[#005EA4] rounded-[10px] px-4 py-2.5 flex items-center justify-between gap-3 text-left text-[#00284B] font-semibold shadow-xs transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                >
                    <span class="truncate">{{ content ? tt(content.title) : tt('Danh mục chính sách') }}</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-[#697586] shrink-0 transition-transform duration-200"
                        :class="{ 'rotate-180': isOpenMobile }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    id="policy-mobile-menu"
                    v-show="isOpenMobile"
                    class="mt-2 bg-white border border-[#E3E8EF] rounded-[10px] p-2 space-y-1 shadow-md"
                >
                    <Link
                        v-for="(policy, index) in list_sidebar"
                        :key="index"
                        :href="getPolicyUrl(policy)"
                        :aria-current="checkActive(policy.slug) ? 'page' : undefined"
                        @click="isOpenMobile = false"
                        class="min-h-[44px] flex items-center px-3.5 py-2.5 rounded-[8px] text-sm border transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                        :class="checkActive(policy.slug)
                            ? 'bg-[#EDF6FF] border-[#005EA4] text-[#005EA4] font-semibold'
                            : 'border-transparent text-[#4B5565] hover:bg-[#F8FAFC] hover:text-[#00284B]'"
                    >
                        {{ tt(policy.title) }}
                    </Link>
                </div>
            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                <!-- Desktop Sidebar Menu -->
                <aside
                    v-if="list_sidebar && list_sidebar.length"
                    class="hidden lg:block lg:col-span-4 xl:col-span-3 sticky top-24"
                >
                    <div class="bg-white border border-[#E3E8EF] rounded-[12px] p-4 sm:p-5 shadow-xs space-y-2">
                        <div class="text-xs font-bold tracking-wider text-[#005EA4] uppercase px-3 py-2 flex items-center gap-2">
                            <span class="block w-2 h-2 rounded-[1px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                            <span>{{ tt('Danh mục chính sách') }}</span>
                        </div>
                        <nav :aria-label="tt('Danh mục chính sách')" class="space-y-1.5">
                            <Link
                                v-for="(policy, index) in list_sidebar"
                                :key="index"
                                :href="getPolicyUrl(policy)"
                                :aria-current="checkActive(policy.slug) ? 'page' : undefined"
                                class="flex items-center px-3.5 py-3 rounded-[8px] text-sm font-medium transition-colors border focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                                :class="checkActive(policy.slug)
                                    ? 'bg-[#EDF6FF] border-[#005EA4] text-[#005EA4] font-semibold'
                                    : 'border-transparent text-[#4B5565] hover:bg-[#F8FAFC] hover:text-[#00284B]'"
                            >
                                <span class="truncate">{{ tt(policy.title) }}</span>
                            </Link>
                        </nav>
                    </div>
                </aside>

                <!-- Content Area -->
                <section class="col-span-12 lg:col-span-8 xl:col-span-9 min-w-0">
                    <article
                        v-if="content"
                        class="bg-white border border-[#E3E8EF] rounded-[12px] p-6 sm:p-8 md:p-10 shadow-xs space-y-6"
                    >
                        <header class="border-b border-[#E3E8EF] pb-4 space-y-2">
                            <h2 class="text-2xl sm:text-3xl font-bold text-[#00284B] tracking-tight">
                                {{ tt(content.title) }}
                            </h2>
                        </header>

                        <div
                            v-html="content.content"
                            class="policy-prose"
                        />
                    </article>

                    <div
                        v-else
                        class="bg-white border border-[#E3E8EF] rounded-[12px] p-12 text-center text-[#4B5565] shadow-xs"
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
                ? `${itemTitle} - Thành Khang | Industrial Equipment & Escalator Parts`
                : `${itemTitle} - ${this.tt('Thành Khang | Thiết bị công nghiệp')}`
        },
        pageDescription() {
            if (this.seo?.description) return this.seo.description
            return this.currentLocale === 'en'
                ? 'Official sales policies, warranty terms, and technical data confidentiality commitments at Thành Khang.'
                : this.tt('Thông tin chính sách bán hàng, cam kết bảo mật hồ sơ kỹ thuật và quy định bảo hành thiết bị tại Thành Khang.')
        },
    },
    methods: {
        checkActive(slug) {
            if (!slug) return false
            const currentSlug = this.content?.slug || ''
            if (slug === currentSlug) return true
            if (this.content?.slug_vi && slug === this.content.slug_vi) return true
            if (this.content?.slug_en && slug === this.content.slug_en) return true
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

<style lang="scss" scoped>
.policy-prose {
    color: #364152;
    line-height: 1.75;
    font-size: 1rem;
}

.policy-prose :deep(h2),
.policy-prose :deep(h3) {
    color: #00284B;
    font-size: 1.25rem;
    font-weight: 700;
    margin-top: 1.75rem;
    margin-bottom: 0.75rem;
    font-family: inherit;
    border-left: 3px solid #005EA4;
    padding-left: 0.75rem;
}

.policy-prose :deep(h4) {
    color: #00284B;
    font-size: 1.1rem;
    font-weight: 600;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}

.policy-prose :deep(p) {
    color: #4B5565;
    margin-bottom: 1rem;
    font-size: 0.975rem;
}

.policy-prose :deep(p:last-child) {
    margin-bottom: 0;
}

.policy-prose :deep(ul) {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
    color: #4B5565;
}

.policy-prose :deep(ol) {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
    color: #4B5565;
}

.policy-prose :deep(li) {
    margin-bottom: 0.5rem;
    color: #4B5565;
}

.policy-prose :deep(strong),
.policy-prose :deep(b) {
    color: #00284B;
    font-weight: 600;
}

.policy-prose :deep(a) {
    color: #005EA4;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.policy-prose :deep(a:hover) {
    color: #004D86;
}

.policy-prose :deep(blockquote) {
    background-color: #EDF6FF;
    border-left: 4px solid #005EA4;
    border-radius: 0 8px 8px 0;
    padding: 1rem 1.25rem;
    margin: 1.25rem 0;
    color: #364152;
}

.policy-prose :deep(table) {
    width: 100%;
    border-collapse: collapse;
    margin: 1.25rem 0;
    font-size: 0.925rem;
}

.policy-prose :deep(th) {
    background-color: #EEF2F6;
    color: #00284B;
    font-weight: 600;
    text-align: left;
    padding: 0.75rem 1rem;
    border: 1px solid #E3E8EF;
}

.policy-prose :deep(td) {
    color: #364152;
    padding: 0.75rem 1rem;
    border: 1px solid #E3E8EF;
    vertical-align: top;
}
</style>
