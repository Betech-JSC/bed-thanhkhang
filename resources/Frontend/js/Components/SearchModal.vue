<template>
    <Transition name="search-modal-fade">
        <div
            v-if="isOpen"
            class="fixed inset-0 bg-[#0B0D17]/80 backdrop-blur-md z-[1050] flex items-start justify-center pt-16 sm:pt-24 px-4 overflow-y-auto"
            @click.self="close"
            role="dialog"
            aria-modal="true"
            aria-label="Tìm kiếm sản phẩm"
        >
            <!-- Modal Dialog Container -->
            <div
                class="w-full max-w-2xl bg-[#111322] border border-[#23273F] rounded-2xl shadow-2xl overflow-hidden flex flex-col my-auto sm:my-0 transition-all transform duration-200"
                @click.stop
            >
                <!-- Top Input Bar -->
                <div class="relative flex items-center px-4 sm:px-5 border-b border-[#23273F] bg-[#16192B]/90">
                    <!-- Search Icon -->
                    <div class="text-[#84ADFF] shrink-0 mr-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Search Input -->
                    <input
                        ref="searchInput"
                        v-model="keyword"
                        type="text"
                        class="w-full bg-transparent text-[#FAFAFA] placeholder-[#5D6B98] text-base sm:text-lg focus:outline-none py-4 sm:py-4.5 pr-2 font-normal"
                        :placeholder="tt('Tìm kiếm sản phẩm 3D, vật liệu, dịch vụ...')"
                        @keydown.esc="close"
                        @keydown.enter="handleEnterKey"
                    />

                    <!-- Actions in Input: Clear button and ESC Badge -->
                    <div class="flex items-center gap-2 shrink-0 ml-2">
                        <!-- Clear input button -->
                        <button
                            v-if="keyword"
                            type="button"
                            @click="clearKeyword"
                            class="w-7 h-7 rounded-full flex items-center justify-center text-[#B9C0D4] hover:text-white hover:bg-[#23273F] transition-colors"
                            title="Xóa từ khóa"
                            aria-label="Xóa từ khóa"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- ESC Badge -->
                        <button
                            type="button"
                            @click="close"
                            class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium text-[#84ADFF] bg-[#1A1F36] border border-[#30374F] hover:bg-[#23273F] transition-colors"
                            title="Đóng cửa sổ (ESC)"
                        >
                            ESC
                        </button>
                    </div>
                </div>

                <!-- Modal Content Body -->
                <div class="p-4 sm:p-5 max-h-[60vh] sm:max-h-[460px] overflow-y-auto custom-scrollbar space-y-4">
                    <!-- 1. Trạng thái Đang tải (Dark Tech Skeleton Loading) -->
                    <div v-if="isLoading" class="space-y-3 py-1">
                        <div
                            v-for="n in 3"
                            :key="n"
                            class="animate-pulse flex items-center gap-3.5 p-3 rounded-xl bg-[#16192B]/60 border border-[#23273F]/60"
                        >
                            <div class="w-12 h-12 rounded-lg bg-[#23273F] shrink-0"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-[#23273F] rounded w-3/5"></div>
                                <div class="h-3 bg-[#1A1F36] rounded w-2/5"></div>
                            </div>
                            <div class="w-16 h-4 bg-[#23273F] rounded shrink-0"></div>
                        </div>
                    </div>

                    <!-- 2. Trạng thái Chưa gõ từ khóa: Gợi ý từ khóa & Sản phẩm nổi bật -->
                    <div v-else-if="!keyword.trim()" class="space-y-5">
                        <!-- Gợi ý từ khóa phổ biến -->
                        <div>
                            <div class="flex items-center gap-2 mb-2.5">
                                <svg class="w-4 h-4 text-[#84ADFF]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span class="text-xs font-semibold text-[#84ADFF] uppercase tracking-wider">
                                    {{ tt('Gợi ý tìm kiếm phổ biến') }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="(tag, index) in popularTags"
                                    :key="index"
                                    type="button"
                                    @click="selectTag(tag.keyword)"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium text-[#B9C0D4] bg-[#16192B] border border-[#23273F] hover:border-[#2970FF] hover:text-[#FAFAFA] hover:bg-[#1E233D] transition-all duration-200 cursor-pointer"
                                >
                                    {{ tag.label }}
                                </button>
                            </div>
                        </div>

                        <!-- Sản phẩm đề xuất ban đầu -->
                        <div v-if="results.length > 0">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-xs font-semibold text-[#84ADFF] uppercase tracking-wider">
                                    {{ tt('Sản phẩm nổi bật') }}
                                </span>
                                <span class="text-xs text-[#5D6B98]">
                                    {{ results.length }} {{ tt('sản phẩm') }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div
                                    v-for="product in results"
                                    :key="product.id"
                                    @click="goToProduct(product)"
                                    class="group flex items-center justify-between p-3 rounded-xl bg-[#16192B]/80 hover:bg-[#1C2038] border border-[#23273F] hover:border-[#2970FF] transition-all duration-200 cursor-pointer shadow-sm hover:shadow-[0_0_15px_rgba(41,112,255,0.15)]"
                                >
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <!-- Thumbnail -->
                                        <div class="w-12 h-12 rounded-lg bg-[#111322] border border-[#2A3150] overflow-hidden shrink-0 flex items-center justify-center">
                                            <img
                                                v-if="product.image && (product.image.url || product.image)"
                                                :src="product.image.url || product.image"
                                                :alt="product.name || product.title"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                loading="lazy"
                                            />
                                            <svg v-else class="w-6 h-6 text-[#404968]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>

                                        <!-- Meta Info -->
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-medium text-[#FAFAFA] group-hover:text-[#84ADFF] transition-colors truncate">
                                                {{ product.name || product.title }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-0.5 text-xs text-[#84ADFF]/80">
                                                <span v-if="product.category_name || (product.category && (product.category.name || product.category.title))" class="truncate max-w-[140px]">
                                                    {{ product.category_name || (product.category && (product.category.name || product.category.title)) }}
                                                </span>
                                                <span v-if="(product.category_name || product.category) && (product.material || product.print_technology)" class="text-[#404968]">•</span>
                                                <span v-if="product.material || product.print_technology" class="text-[#B9C0D4] truncate max-w-[130px]">
                                                    {{ product.material || product.print_technology }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CTA -->
                                    <div class="text-right shrink-0 pl-3">
                                        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#84ADFF] group-hover:text-[#528BFF] group-hover:translate-x-0.5 transition-all">
                                            <span>{{ tt('Xem chi tiết') }}</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Trạng thái Có kết quả tìm kiếm -->
                    <div v-else-if="results.length > 0" class="space-y-3">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-xs font-semibold text-[#84ADFF] uppercase tracking-wider">
                                {{ tt('Kết quả sản phẩm') }} ({{ results.length }})
                            </span>
                            <span class="text-xs text-[#B9C0D4]">
                                {{ tt('Tìm thấy cho') }} "<span class="text-white font-medium">{{ keyword }}</span>"
                            </span>
                        </div>

                        <div class="space-y-2">
                            <div
                                v-for="product in results"
                                :key="product.id"
                                @click="goToProduct(product)"
                                class="group flex items-center justify-between p-3 rounded-xl bg-[#16192B]/80 hover:bg-[#1C2038] border border-[#23273F] hover:border-[#2970FF] transition-all duration-200 cursor-pointer shadow-sm hover:shadow-[0_0_15px_rgba(41,112,255,0.15)]"
                            >
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <!-- Thumbnail -->
                                    <div class="w-12 h-12 rounded-lg bg-[#111322] border border-[#2A3150] overflow-hidden shrink-0 flex items-center justify-center">
                                        <img
                                            v-if="product.image && (product.image.url || product.image)"
                                            :src="product.image.url || product.image"
                                            :alt="product.name || product.title"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy"
                                        />
                                        <svg v-else class="w-6 h-6 text-[#404968]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>

                                    <!-- Meta Info -->
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-medium text-[#FAFAFA] group-hover:text-[#84ADFF] transition-colors truncate">
                                            {{ product.name || product.title }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-0.5 text-xs text-[#84ADFF]/80">
                                            <span v-if="product.category_name || (product.category && (product.category.name || product.category.title))" class="truncate max-w-[140px]">
                                                {{ product.category_name || (product.category && (product.category.name || product.category.title)) }}
                                            </span>
                                            <span v-if="(product.category_name || product.category) && (product.material || product.print_technology)" class="text-[#404968]">•</span>
                                            <span v-if="product.material || product.print_technology" class="text-[#B9C0D4] truncate max-w-[130px]">
                                                {{ product.material || product.print_technology }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- CTA -->
                                <div class="text-right shrink-0 pl-3">
                                    <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#84ADFF] group-hover:text-[#528BFF] group-hover:translate-x-0.5 transition-all">
                                        <span>{{ tt('Xem chi tiết') }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Trạng thái Không tìm thấy kết quả -->
                    <div v-else class="py-10 text-center space-y-3">
                        <div class="w-14 h-14 mx-auto rounded-full bg-[#16192B] border border-[#23273F] flex items-center justify-center text-[#5D6B98]">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-medium text-[#FAFAFA]">
                            {{ tt('Không tìm thấy sản phẩm phù hợp') }}
                        </h4>
                        <p class="text-xs sm:text-sm text-[#B9C0D4] max-w-sm mx-auto">
                            {{ tt('Không tìm thấy sản phẩm nào khớp với từ khóa') }} "<span class="text-[#84ADFF]">{{ keyword }}</span>". {{ tt('Vui lòng thử từ khóa khác hoặc duyệt bộ sưu tập.') }}
                        </p>
                    </div>
                </div>

                <!-- Footer Bar -->
                <div class="px-4 sm:px-5 py-3.5 bg-[#0E101D] border-t border-[#23273F] flex items-center justify-between text-xs text-[#B9C0D4]">
                    <!-- Nút Xem tất cả kết quả -->
                    <button
                        v-if="keyword.trim()"
                        type="button"
                        @click="viewAllResults"
                        class="text-[#84ADFF] hover:text-white font-medium flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>{{ tt('Xem tất cả kết quả cho') }} "<span class="text-white">{{ keyword }}</span>"</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                    <button
                        v-else
                        type="button"
                        @click="viewAllResults"
                        class="text-[#84ADFF] hover:text-white font-medium flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>{{ tt('Duyệt toàn bộ danh mục sản phẩm') }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>

                    <!-- Phím tắt hướng dẫn -->
                    <div class="hidden sm:flex items-center gap-3 text-[#5D6B98] text-[11px]">
                        <span class="flex items-center gap-1">
                            <kbd class="px-1.5 py-0.5 rounded bg-[#16192B] border border-[#23273F] text-[#84ADFF] font-mono text-[10px]">ESC</kbd>
                            {{ tt('để đóng') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <kbd class="px-1.5 py-0.5 rounded bg-[#16192B] border border-[#23273F] text-[#84ADFF] font-mono text-[10px]">↵</kbd>
                            {{ tt('để chọn') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script>
import axios from 'axios'

export default {
    name: 'SearchModal',
    props: {
        isOpen: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['close'],
    data() {
        return {
            keyword: '',
            isLoading: false,
            results: [],
            debounceTimer: null,
            popularTags: [
                { label: 'In 3D SLA', keyword: 'SLA' },
                { label: 'Nhựa PLA+', keyword: 'PLA' },
                { label: 'In Resin 8K', keyword: 'Resin' },
                { label: 'Carbon Fiber', keyword: 'Carbon Fiber' },
                { label: 'Móc khoá F1', keyword: 'Móc khoá' },
                { label: 'Khung robot', keyword: 'Robot' },
                { label: 'Voronoi', keyword: 'Voronoi' },
                { label: 'Thiết kế 3D', keyword: 'Thiết kế' },
                { label: 'Quà tặng lưu niệm', keyword: 'Quà tặng' },
            ],
        }
    },
    computed: {
        currentLocale() {
            return this.$page?.props?.locale?.current || 'vi'
        },
    },
    watch: {
        isOpen(val) {
            if (val) {
                this.$nextTick(() => {
                    this.$refs.searchInput?.focus()
                })
                if (typeof document !== 'undefined') {
                    document.body.classList.add('overflow-hidden')
                }
                // Nếu chưa có kết quả nào, nạp sản phẩm gợi ý ban đầu
                if (this.results.length === 0) {
                    this.fetchResults('')
                }
            } else {
                if (typeof document !== 'undefined') {
                    document.body.classList.remove('overflow-hidden')
                }
            }
        },
        keyword(newVal) {
            clearTimeout(this.debounceTimer)
            this.debounceTimer = setTimeout(() => {
                this.fetchResults(newVal)
            }, 250)
        },
    },
    beforeUnmount() {
        if (typeof document !== 'undefined') {
            document.body.classList.remove('overflow-hidden')
        }
    },
    methods: {
        close() {
            this.$emit('close')
        },
        clearKeyword() {
            this.keyword = ''
            this.$nextTick(() => {
                this.$refs.searchInput?.focus()
            })
            this.fetchResults('')
        },
        selectTag(tagKeyword) {
            this.keyword = tagKeyword
            this.fetchResults(tagKeyword)
        },
        async fetchResults(searchTerm) {
            this.isLoading = true
            try {
                let apiUrl = '/search-v2'
                try {
                    apiUrl = this.route('api.search')
                } catch (e) {
                    apiUrl = '/search-v2'
                }

                const response = await axios.get(apiUrl, {
                    params: {
                        keyword: searchTerm ? searchTerm.trim() : '',
                        limit: 8,
                    },
                })

                if (response.data && response.data.success) {
                    this.results = response.data.products || []
                } else if (Array.isArray(response.data?.products)) {
                    this.results = response.data.products
                } else {
                    this.results = []
                }
            } catch (err) {
                console.error('SearchModal fetch error:', err)
                this.results = []
            } finally {
                this.isLoading = false
            }
        },
        formatPrice(val) {
            if (!val || isNaN(val) || Number(val) <= 0) return ''
            return new Intl.NumberFormat('vi-VN').format(val) + ' đ'
        },
        getProductUrl(product) {
            if (product.slug) {
                try {
                    return this.route('products.show', { slug: product.slug })
                } catch (e) {
                    const locale = this.currentLocale || 'vi'
                    return locale === 'en' ? `/en/products/${product.slug}` : `/san-pham/${product.slug}`
                }
            }
            return '/san-pham'
        },
        goToProduct(product) {
            const url = this.getProductUrl(product)
            this.close()
            if (this.$inertia) {
                this.$inertia.visit(url)
            } else if (typeof window !== 'undefined') {
                window.location.href = url
            }
        },
        viewAllResults() {
            const kw = this.keyword.trim()
            this.close()
            let targetUrl = '/san-pham'
            try {
                targetUrl = this.route('products.index', kw ? { search: kw } : {})
            } catch (e) {
                targetUrl = kw ? `/san-pham?search=${encodeURIComponent(kw)}` : '/san-pham'
            }

            if (this.$inertia) {
                this.$inertia.visit(targetUrl)
            } else if (typeof window !== 'undefined') {
                window.location.href = targetUrl
            }
        },
        handleEnterKey() {
            if (this.results.length > 0 && this.keyword.trim()) {
                // Nhấn Enter khi có kết quả: mở trang chi tiết sản phẩm đầu tiên
                this.goToProduct(this.results[0])
            } else {
                this.viewAllResults()
            }
        },
    },
}
</script>

<style scoped>
/* Modal Fade Transitions */
.search-modal-fade-enter-active,
.search-modal-fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.search-modal-fade-enter-from,
.search-modal-fade-leave-to {
    opacity: 0;
    transform: scale(0.97);
}

/* Custom Scrollbar for Dark Tech Modal */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #111322;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #23273F;
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #2970FF;
}
</style>
