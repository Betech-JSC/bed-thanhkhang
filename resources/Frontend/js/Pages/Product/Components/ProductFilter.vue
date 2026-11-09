<template>
    <div class="w-full lg:w-[240px] space-y-3">
        <!-- Main Filter Card (Figma 2701:5303 - Cao 464px, bg #30374F, r 12px) -->
        <div class="bg-[#30374F] rounded-[12px] p-4 flex flex-col gap-4 shadow-lg min-h-[464px]">
            <!-- Header on mobile with close button -->
            <div class="flex items-center justify-between pb-2 border-b border-[#404968] lg:hidden">
                <span class="text-sm font-bold text-white uppercase tracking-wider">{{ tt('Bộ lọc sản phẩm') }}</span>
                <button
                    type="button"
                    @click="$emit('close')"
                    class="p-1 text-[#B9C0D4] hover:text-white rounded transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Title: DANH MỤC SẢN PHẨM (Figma 2701:5304) -->
            <h4 class="text-sm font-semibold text-[#B9C0D4] uppercase leading-[21px]">
                {{ tt('DANH MỤC SẢN PHẨM') }}
            </h4>

            <!-- 1. Search Box (Figma 2701:5305 - h 46px, bg #111322, r 8px) -->
            <div class="relative w-full h-[46px] bg-[#111322] border border-[#5D6B98] rounded-lg px-[13px] flex items-center justify-between gap-2 focus-within:border-[#84ADFF] transition-colors">
                <input
                    type="text"
                    :value="filters.search"
                    @input="updateFilter('search', $event.target.value)"
                    :placeholder="tt('Tìm kiếm sản phẩm…')"
                    class="w-full bg-transparent text-sm leading-5 text-[#FAFAFA] placeholder-[#B9C0D4] focus:outline-none pr-2"
                />
                <!-- Clear Search button or Search Icon on the RIGHT -->
                <button
                    v-if="filters.search"
                    type="button"
                    @click="updateFilter('search', '')"
                    class="text-[#B9C0D4] hover:text-white shrink-0 ml-1"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <svg
                    v-else
                    class="w-5 h-5 text-[#B9C0D4] shrink-0 pointer-events-none"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- 2. Danh mục sản phẩm (Figma 2701:5309 - 4 mục chuẩn, h 44px, có chevron xổ ra) -->
            <div class="flex flex-col gap-1">
                <div
                    v-for="cat in categoryTree"
                    :key="cat.id"
                    class="flex flex-col"
                >
                    <!-- Category Item Row -->
                    <div
                        @click="handleCategoryClick(cat)"
                        class="w-full h-[44px] px-3 rounded-lg text-sm leading-5 flex items-center justify-between transition-colors duration-200 text-left cursor-pointer select-none group"
                        :class="
                            isCategorySelected(cat)
                                ? 'bg-[#404968] text-[#FAFAFA] font-medium'
                                : 'text-[#EFF1F5] hover:bg-[#404968]/50 hover:text-white'
                        "
                    >
                        <span class="truncate pr-2">{{ cat.name }}</span>

                        <!-- Chevron Icon ở đằng sau các mục có danh mục con (Figma 2701:5314, 5317, 5320) -->
                        <button
                            v-if="cat.hasChevron"
                            type="button"
                            @click.stop="toggleCategoryExpand(cat.id)"
                            class="w-6 h-6 flex items-center justify-center shrink-0 text-[#EFF1F5] group-hover:text-white transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                            :class="{ 'rotate-90': isCategoryExpanded(cat.id) }"
                            :aria-expanded="isCategoryExpanded(cat.id)"
                            :title="tt('Mở rộng danh mục con')"
                        >
                            <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor">
                                <path d="M6 12L10 8L6 4" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Subcategories Accordion (Xổ ra các mục con mượt 60fps bằng CSS Grid 0fr <-> 1fr) -->
                    <div
                        v-if="cat.hasChevron && cat.subcategories && cat.subcategories.length"
                        class="subcategory-accordion-grid"
                        :class="{ 'is-expanded': isCategoryExpanded(cat.id) }"
                        :aria-hidden="!isCategoryExpanded(cat.id)"
                    >
                        <div class="overflow-hidden min-h-0">
                            <div class="flex flex-col gap-1 pl-4 pr-1 py-1 my-0.5">
                                <button
                                    v-for="sub in cat.subcategories"
                                    :key="sub.id"
                                    type="button"
                                    :tabindex="isCategoryExpanded(cat.id) ? 0 : -1"
                                    @click="selectSubcategory(cat, sub.id)"
                                    class="w-full h-8 px-3 rounded-md text-xs leading-4 flex items-center justify-between text-left transition-colors duration-150 cursor-pointer select-none"
                                    :class="
                                        isSubcategorySelected(sub.id)
                                            ? 'bg-[#2970FF]/25 text-[#84ADFF] font-medium border border-[#2970FF]/50 shadow-sm'
                                            : 'text-[#B9C0D4] hover:bg-[#404968]/40 hover:text-[#FAFAFA]'
                                    "
                                >
                                    <span class="truncate">{{ sub.name }}</span>
                                    <span
                                        v-if="isSubcategorySelected(sub.id)"
                                        class="w-1.5 h-1.5 rounded-full bg-[#84ADFF] shrink-0"
                                    ></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Lọc theo nhu cầu (Figma 2701:5321 - gap 12px, checkbox TRƯỚC label) -->
            <div class="pt-2 flex flex-col gap-3">
                <h4 class="text-sm font-semibold text-[#B9C0D4] uppercase leading-[21px]">
                    {{ tt('LỌC THEO NHU CẦU') }}
                </h4>
                <div class="flex flex-col gap-3">
                    <button
                        v-for="purpose in purposes"
                        :key="purpose"
                        type="button"
                        @click="togglePurpose(purpose)"
                        class="w-full h-5 px-3 flex items-center gap-3 text-left group cursor-pointer focus:outline-none select-none"
                    >
                        <!-- Checkbox nằm TRƯỚC (bên trái label), kích thước 16x16, bo góc 4px (Figma 2701:5325, 5328, 5331) -->
                        <div
                            class="w-4 h-4 rounded-[4px] border flex items-center justify-center transition-colors shrink-0"
                            :class="
                                isPurposeSelected(purpose)
                                    ? 'bg-[#2970FF] border-[#2970FF]'
                                    : 'border-[#5D6B98] bg-transparent group-hover:border-[#EFF1F5]'
                            "
                        >
                            <svg
                                v-if="isPurposeSelected(purpose)"
                                class="w-3 h-3 text-white"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>

                        <!-- Label text nằm SAU (bên phải checkbox), cách checkbox đúng 12px (gap-3) -->
                        <span
                            class="text-sm leading-5 transition-colors"
                            :class="
                                isPurposeSelected(purpose)
                                    ? 'text-[#FAFAFA] font-medium'
                                    : 'text-[#EFF1F5] group-hover:text-white'
                            "
                        >
                            {{ purpose }}
                        </span>
                    </button>
                </div>
            </div>
            <!-- Đã XÓA nút "Đặt lại bộ lọc" bên sidebar theo đúng yêu cầu feedback của User -->
        </div>

        <!-- Custom Request Box (Figma 2701:5333 - Cao 156px, bg #30374F, r 12px) -->
        <div class="bg-[#30374F] rounded-[12px] p-4 flex flex-col justify-between h-[156px] shadow-lg">
            <div class="flex flex-col gap-3">
                <h4 class="text-sm font-medium text-[#FAFAFA] leading-[20px]">
                    {{ tt('Tuỳ chỉnh sản phẩm') }}
                </h4>
                <p class="text-[13px] text-[#B9C0D4] leading-[18px]">
                    {{ tt('Màu sắc, kích thước và cá nhân hoá được tư vấn theo nhu cầu ?') }}
                </p>
            </div>
            <Link
                :href="route('contact')"
                class="w-full h-10 rounded-lg text-[#F5F8FF] text-base font-medium flex items-center justify-center transition-colors duration-200 active:scale-[0.98] shadow-md hover:shadow-lg hover:opacity-95 select-none"
                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
            >
                {{ tt('Liên hệ tư vấn') }}
            </Link>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ProductFilter',
    props: {
        filters: {
            type: Object,
            required: true,
        },
        categories: {
            type: Array,
            default: () => [],
        },
    },
    emits: ['update:filters', 'reset', 'close'],
    data() {
        return {
            // Danh sách các danh mục đang mở rộng mục con
            expandedCategories: [],
        }
    },
    computed: {
        purposes() {
            return [
                this.tt('Trang trí không gian'),
                this.tt('Đồ dùng hằng ngày'),
                this.tt('Quà tặng cá nhân'),
            ]
        },
        defaultCategoryTree() {
            return [
                {
                    id: 'all',
                    name: this.tt('Tất cả sản phẩm'),
                    slug: 'all',
                    hasChevron: false,
                    subcategories: [],
                },
                {
                    id: 'moc-khoa',
                    name: this.tt('Móc khoá'),
                    slug: 'moc-khoa-phu-kien',
                    hasChevron: true,
                    subcategories: [
                        { id: 'moc-khoa-co-khi', slug: 'moc-khoa-co-khi', name: this.tt('Móc khoá cơ khí') },
                        { id: 'moc-khoa-the-thao', slug: 'moc-khoa-the-thao', name: this.tt('Móc khoá thể thao & phụ kiện') },
                    ],
                },
                {
                    id: 'van-phong-pham',
                    name: this.tt('Văn phòng phẩm'),
                    slug: 'trang-tri-do-dung',
                    hasChevron: true,
                    subcategories: [
                        { id: 'ly', slug: 'ly', name: this.tt('Ly') },
                        { id: 'lo-cam-hoa-de-ban', slug: 'lo-cam-hoa-de-ban', name: this.tt('Lọ cắm hoa để bàn') },
                        { id: 'den', slug: 'den', name: this.tt('Đèn') },
                    ],
                },
                {
                    id: 'gia-cong-theo-yeu-cau',
                    name: this.tt('Gia công theo yêu cầu'),
                    slug: 'gia-cong-theo-yeu-cau',
                    hasChevron: true,
                    subcategories: [
                        { id: 'mo-hinh-resin', slug: 'mo-hinh-resin', name: this.tt('Mô hình Resin & Khớp cử động') },
                        { id: 'linh-kien-carbon', slug: 'linh-kien-carbon', name: this.tt('Linh kiện cơ khí & Carbon Fiber') },
                    ],
                },
            ]
        },
        categoryTree() {
            if (!Array.isArray(this.categories) || this.categories.length === 0) {
                return this.defaultCategoryTree
            }

            // Nếu prop categories đã được chuẩn hóa sẵn từ Product/Index.vue (có mục 'all' và subcategories)
            if (this.categories[0]?.id === 'all' && Array.isArray(this.categories[0]?.subcategories)) {
                return this.categories
            }

            // Dựng cây danh mục động từ dữ liệu CMS (hỗ trợ cả cấu trúc lồng children/nodes và danh sách phẳng parent_id/default_parent)
            const rawList = this.categories.filter(Boolean)
            const childMap = {}
            const roots = []

            rawList.forEach((item) => {
                const parentId = item.parent_id || item.default_parent?.id || 0
                if (parentId && Number(parentId) > 0) {
                    if (!childMap[parentId]) childMap[parentId] = []
                    childMap[parentId].push(item)
                } else {
                    const slug = String(item.slug || '').toLowerCase()
                    if (slug !== 'trang-tri-khong-gian' && slug !== 'space-decor') {
                        roots.push(item)
                    }
                }
            })

            if (roots.length === 0) {
                return this.defaultCategoryTree
            }

            const tree = [this.defaultCategoryTree[0]]

            roots.forEach((root) => {
                const slug = String(root.slug || root.id || '').toLowerCase()
                const rawTitle = String(root.title || root.name || '').trim()

                let fallback = null
                if (slug.includes('moc-khoa') || slug.includes('keychain') || rawTitle.toLowerCase().includes('móc khoá') || rawTitle.toLowerCase().includes('móc khóa')) {
                    fallback = this.defaultCategoryTree[1]
                } else if (slug.includes('trang-tri-do-dung') || slug.includes('van-phong-pham') || slug.includes('stationery') || rawTitle.toLowerCase().includes('đồ dùng') || rawTitle.toLowerCase().includes('văn phòng')) {
                    fallback = this.defaultCategoryTree[2]
                } else if (slug.includes('gia-cong') || slug.includes('custom') || rawTitle.toLowerCase().includes('gia công')) {
                    fallback = this.defaultCategoryTree[3]
                }

                const rawChildren =
                    (Array.isArray(root.subcategories) && root.subcategories.length > 0 && root.subcategories) ||
                    (Array.isArray(root.children) && root.children.length > 0 && root.children) ||
                    (Array.isArray(root.nodes) && root.nodes.length > 0 && root.nodes) ||
                    childMap[root.id] ||
                    []

                const mappedSubs =
                    rawChildren.length > 0
                        ? rawChildren.map((sub) => ({
                              id: sub.slug || String(sub.id),
                              rawId: sub.id,
                              slug: sub.slug || String(sub.id),
                              name: this.tt(sub.title || sub.name || ''),
                          }))
                        : fallback
                        ? fallback.subcategories
                        : []

                let displayName = rawTitle ? this.tt(rawTitle) : fallback?.name || ''
                if (rawTitle === 'Móc khoá & Phụ kiện' || rawTitle === 'Móc khóa & Phụ kiện') {
                    displayName = this.tt('Móc khoá')
                } else if (rawTitle === 'Trang trí & Đồ dùng') {
                    displayName = this.tt('Văn phòng phẩm')
                }

                tree.push({
                    id: fallback ? fallback.id : root.slug || String(root.id),
                    rawId: root.id,
                    name: displayName,
                    slug: root.slug || fallback?.slug || String(root.id),
                    hasChevron: mappedSubs.length > 0,
                    subcategories: mappedSubs,
                })
            })

            return tree
        },
    },
    watch: {
        'filters.category': {
            immediate: true,
            handler(val) {
                if (val && val !== 'all') {
                    // Tự động mở rộng danh mục nếu category được chọn
                    const match = this.categoryTree.find(c => c.id === val || c.slug === val)
                    if (match && match.hasChevron && !this.expandedCategories.includes(match.id)) {
                        this.expandedCategories.push(match.id)
                    }
                }
            }
        }
    },
    methods: {
        isCategorySelected(cat) {
            const current = this.filters.category
            if (!current || current === 'all') return cat.id === 'all' || cat.slug === 'all'
            return (
                current === cat.id ||
                current === cat.slug ||
                (cat.rawId !== undefined && String(current) === String(cat.rawId))
            )
        },
        isCategoryExpanded(catId) {
            return this.expandedCategories.includes(catId)
        },
        toggleCategoryExpand(catId) {
            const idx = this.expandedCategories.indexOf(catId)
            if (idx > -1) {
                this.expandedCategories.splice(idx, 1)
            } else {
                this.expandedCategories.push(catId)
            }
        },
        isSubcategorySelected(subId) {
            return this.filters.subcategory === subId
        },
        isPurposeSelected(purpose) {
            if (!this.filters.purposes) return false
            return this.filters.purposes.includes(purpose)
        },
        updateFilter(key, value) {
            this.$emit('update:filters', {
                ...this.filters,
                [key]: value,
            })
        },
        handleCategoryClick(cat) {
            if (cat.id === 'all') {
                this.expandedCategories = []
                this.$emit('update:filters', {
                    ...this.filters,
                    category: 'all',
                    subcategory: null,
                })
                return
            }

            // Điều khiển đóng/mở accordion danh mục mượt mà khi click vào hàng danh mục
            if (cat.hasChevron) {
                if (this.isCategorySelected(cat) && !this.filters.subcategory) {
                    this.toggleCategoryExpand(cat.id)
                } else {
                    this.expandedCategories = [cat.id]
                }
            }

            this.$emit('update:filters', {
                ...this.filters,
                category: cat.id,
                subcategory: null, // Xoá subcategory cụ thể khi chọn danh mục cha
            })
        },
        selectSubcategory(parentCat, subId) {
            // Khi chọn subcategory, đảm bảo parent category cũng được kích hoạt
            this.$emit('update:filters', {
                ...this.filters,
                category: parentCat.id,
                subcategory: this.filters.subcategory === subId ? null : subId,
            })
        },
        togglePurpose(purpose) {
            const arr = [...(this.filters.purposes || [])]
            const idx = arr.indexOf(purpose)
            if (idx > -1) {
                arr.splice(idx, 1)
            } else {
                arr.push(purpose)
            }
            this.updateFilter('purposes', arr)
        },
        resetFilters() {
            this.expandedCategories = []
            this.$emit('reset')
        },
    },
}
</script>

<style scoped>
.subcategory-accordion-grid {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 280ms cubic-bezier(0.22, 1, 0.36, 1);
}

.subcategory-accordion-grid.is-expanded {
    grid-template-rows: 1fr;
}
</style>