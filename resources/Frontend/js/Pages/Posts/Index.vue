<template>
    <main class="bg-gray-warm-100 lg:pb-20 pb-10">
        <section class="">
            <div class="h-[280px] md:h-[400px] xl:h-[560px] container">
                <div class="relative">
                    <img
                        src="/assets/images/posts/bannerTop.png"
                        loading="eager"
                        class="object-fit h-full w-full"
                        :alt="tt('')"
                    />
                    <div class="absolute lg:top-[160px] top-[100px] space-y-3 w-full grid grid-cols-12 gap-2">
                        <div class="col-span-full md:col-span-9">
                            <div>
                                <h1 class="display-2 text-[#18191E] font-bold font-display">Funky Blog</h1>
                                <p class="body-1 font-sans font-normal text-[#18191E]">
                                    Nơi chia sẻ các tin tức, thông tin mới nhất từ Funky
                                </p>
                            </div>
                        </div>
                        <div class="col-span-full md:col-span-3">
                            <div
                                class="flex justify-between items-center py-[10px] px-[1rem] bg-white border border-gray-300 rounded-[8px]"
                            >
                                <input
                                    ref="inputSearch"
                                    v-model="searchText"
                                    @keyup.enter="handleSearch"
                                    type="text"
                                    class="outline-none ring-0"
                                    placeholder="Tìm kiếm"
                                />
                                <div>
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M17.5 17.5L13.875 13.875M15.8333 9.16667C15.8333 12.8486 12.8486 15.8333 9.16667 15.8333C5.48477 15.8333 2.5 12.8486 2.5 9.16667C2.5 5.48477 5.48477 2.5 9.16667 2.5C12.8486 2.5 15.8333 5.48477 15.8333 9.16667Z"
                                            stroke="#44403C"
                                            stroke-width="1.66667"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="container md:mt-[-13%] relative z-2 lg:space-y-8 space-y-6">
            <div class="lg:space-y-6 space-y-4">
                <div
                    :class="[
                        'sticky top-[80px] z-10 transition-colors duration-300 px-2',
                        isScrolled ? 'bg-white shadow-sm' : '',
                    ]"
                >
                    <div class="py-4 space-y-3">
                        <div class="flex items-center gap-x-3 flex-wrap">
                            <div
                                class="w-fit rounded-[40px] px-[10px] h-[26px] cursor-pointer title-2 font-display text-[#18191E] font-bold"
                                :class="{
                                    'bg-[#CBFF00]': selectedFilter === 'everything',
                                    'bg-gray-warm-100': selectedFilter !== 'everything',
                                }"
                                @click="applyFilter('everything')"
                            >
                                Everything
                            </div>
                            <div
                                v-for="(tag, index) in uniqueCategories"
                                :key="index"
                                class="w-fit rounded-[40px] px-[10px] h-[26px] cursor-pointer title-2 font-display text-[#18191E] font-bold"
                                :class="{
                                    'bg-[#CBFF00]': selectedFilter === tag.slug,
                                    'bg-transparent': selectedFilter !== tag.slug,
                                }"
                                @click="applyFilter(tag.slug)"
                            >
                                {{ tag.title }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 lg:gap-x-8 gap-y-4">
                    <div
                        v-for="(item, index) in paginatedResources"
                        :key="index"
                        class="w-full rounded-2xl border border-gray-warm-300 bg-white p-3 lg:col-span-1 col-span-full"
                    >
                        <CardBlog :item="item" />
                    </div>
                </div>
            </div>
            <Pagination
                :total="filteredResources.length"
                :perPage="perPage"
                :currentPage="currentPage"
                @page-changed="changePage"
            />
        </section>
    </main>
</template>
<script>
import JamBreadcrumb from '@/Components/Jam/Breadcrumb.vue'
import Pagination from '@/Components/Paginate.vue'
import axios from 'axios'

export default {
    components: { JamBreadcrumb, Pagination },

    props: ['posts', 'courses'],

    data() {
        return {
            isScrolled: false,
            isLoading: false,
            searchText: this.$attrs.keyword || '',
            instantSearch: [],
            recently_search: this.$page.props.data.recently_search || [],

            currentPage: 1,
            perPage: 30,
            breadcrumbs: [{ title: this.tt('Tin tức') }],
            selectedFilter: 'everything',
        }
    },

    watch: {
        '$attrs.keyword'(newVal) {
            if (this.isShowroomPage) return
            if (!newVal) {
                this.searchText = ''
            } else {
                this.searchText = newVal
            }
        },
        searchText(newVal) {
            if (newVal && (this.recently_search.includes(newVal) || this.keywords.includes(newVal))) {
                return
            }

            this.searchProduct()
        },
        '$page.url': function (newURL) {
            this.instantSearch = []
            if (newURL !== this.route('search', { keyword: this.searchText })) {
                this.searchText = ''
            }
        },
    },

    mounted() {
        window.addEventListener('scroll', this.handleScroll)
    },
    beforeDestroy() {
        window.removeEventListener('scroll', this.handleScroll)
    },

    computed: {
        keywords() {
            return this.$page.props.data.keywords || []
        },
        filteredResources() {
            if (this.selectedFilter === 'everything') {
                return this.posts
            }
            return this.posts.filter((resource) =>
                resource.categories?.some((category) => category?.slug === this.selectedFilter)
            )
        },
        paginatedResources() {
            const start = (this.currentPage - 1) * this.perPage
            const end = start + this.perPage
            return this.filteredResources.slice(start, end)
        },
        uniqueCategories() {
            const allCategories = this.posts.flatMap((post) => post.categories)
            const uniqueCategories = []

            allCategories.forEach((category) => {
                if (!uniqueCategories.some((c) => c.id === category.id)) {
                    uniqueCategories.push(category)
                }
            })

            return uniqueCategories
        },
        totalPages() {
            return Math.ceil(this.filteredResources.length / this.perPage)
        },
    },
    methods: {
        handleScroll() {
            this.isScrolled = window.scrollY > 50 // Tùy chỉnh ngưỡng
        },
        handleSearch() {
            if (this.searchText.trim() === '' || this.isLoading) return
            this.instantSearch = []
            this.isLoading = true

            if (typeof fbq !== 'undefined') {
                fbq('track', 'Search', {
                    search_string: this.searchText,
                })
            }
            if (typeof ttq !== 'undefined') {
                ttq.track('Search', {
                    search_string: this.searchText,
                })
            }
            const BASE_URL = this.route('search')
            this.$inertia.visit(BASE_URL, {
                preserveScroll: true,
                data: {
                    keyword: this.searchText,
                },
                onFinish: () => {
                    this.isLoading = false
                },
            })
        },

        async searchProduct() {
            if (this.isLoading) {
                this.instantSearch = []
                return
            }

            if (this.isLoadingSearch) return
            this.isLoadingSearch = true

            if (this.searchText.trim() === '') {
                this.instantSearch = []
            } else {
                const { data } = await axios.get(this.route('instant_search', { keyword: this.searchText }))
                this.instantSearch = data?.data?.products || []
            }

            this.isLoadingSearch = false
        },

        applyFilter(tag) {
            this.selectedFilter = tag
            this.currentPage = 1
        },
        changePage(page) {
            if (page > this.totalPages) {
                this.currentPage = 1
            } else if (page < 1) {
                this.currentPage = this.totalPages
            } else {
                this.currentPage = page
            }
        },
    },
}
</script>
