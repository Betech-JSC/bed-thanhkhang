<template>
    <main class="min-h-screen bg-[#F9F9F9]">
        <Banner v-if="$page.props.data.banner.banner_prize" :image="$page.props.data.banner.banner_prize?.image?.url"
            :imageMobile="$page.props.data.banner.banner_prize?.image_mobile?.url"
            :alt="$page.props.data.banner.banner_prize.title">
            <div>
                <div class="space-y-3 max-w-[470px] w-full text-white">
                    <h1 class="display-2 uppercase font-display">
                        {{ $page.props.data.banner.banner_prize.title }}
                    </h1>
                    <p class="body-1 font-beau">
                        {{ $page.props.data.banner.banner_prize.description }}
                    </p>
                </div>
            </div>
        </Banner>

        <!-- Grid Background -->
        <div class="relative md:pt-28 py-12 xl:pt-36 md:pb-20">
            <div class="absolute inset-0">
                <img src="/assets/images/posts/bannerTop.png" loading="eager" class="object-fit h-auto w-full"
                    alt="background mask prizes" />
            </div>

            <div class="relative">
                <div class="container">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-8 mb-16"
                        data-aos="fade-up">
                        <div class="max-w-3xl space-y-3">
                            <h1 class="display-2 font-display font-bold text-gray-900 uppercase">
                                {{ tt('Giải thưởng từ học viên') }}
                            </h1>
                            <p class="body-1 text-gray-600 max-w-2xl">
                                Khám phá những dự án xuất sắc nhất từ các học viên năng động của Funky. Những giải
                                thưởng này là minh chứng cho sự sáng tạo và nỗ lực không ngừng trong kiến trúc và thiết
                                kế.
                            </p>
                        </div>

                        <div class="relative w-full lg:w-96 group">
                            <input v-model="searchQuery" type="text"
                                class="w-full pl-3 pr-12 py-3 rounded-xl border border-gray-300 bg-white/80 backdrop-blur-sm focus:outline-none focus:ring-4 focus:ring-red-fks/10 focus:border-red-fks transition-all duration-300"
                                :placeholder="tt('Tìm kiếm giải thưởng...')" />
                            <div
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-black-fks group-focus-within:text-red-fks duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="md:mt-16 mt-12 xl:mt-24">
                        <div v-if="filteredPrizes && filteredPrizes.length > 0"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-aos="fade-up">
                            <PrizeCard v-for="(prize, index) in filteredPrizes" :key="index" :prize="prize"
                                @select="openPrizeModal" />
                        </div>
                        <div v-else class="text-center py-20" data-aos="fade-up">
                            <p class="title-2 text-gray-400">{{ tt('Không tìm thấy giải thưởng nào phù hợp.') }}</p>
                        </div>
                    </div>
                    <div v-if="prizes.links && prizes.links.length > 3" class="mt-20 flex justify-center">
                        <Paginate :links="prizes.links" />
                    </div>
                </div>
            </div>
        </div>

        <PrizeModal :show="!!selectedPrize" :prize="selectedPrize || {}" @close="selectedPrize = null" />
    </main>
</template>

<script>
import PrizeCard from './Components/PrizeCard.vue'
import PrizeModal from './Components/PrizeModal.vue'
import Paginate from '@/Components/Paginate.vue'

export default {
    components: {
        PrizeCard,
        PrizeModal,
        Paginate,
    },
    props: ['prizes'],
    data() {
        return {
            searchQuery: '',
            selectedPrize: null,
        }
    },
    methods: {
        openPrizeModal(prize) {
            this.selectedPrize = prize
        },
    },
    computed: {
        allPrizes() {
            return this.prizes?.data || []
        },
        filteredPrizes() {
            const prizes = this.allPrizes
            if (!this.searchQuery) return prizes
            const query = this.searchQuery.toLowerCase()
            return prizes.filter(
                (p) =>
                    (p.title && p.title.toLowerCase().includes(query)) ||
                    (p.author && p.author.toLowerCase().includes(query))
            )
        },
        featuredPrize() {
            return this.filteredPrizes[0] || {}
        },
    },
}
</script>

<style scoped>
.container {
    max-width: 1440px;
}
</style>
