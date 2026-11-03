<template>
    <div class="w-full lg:w-[240px] space-y-3">
        <!-- Main Filter Card (Figma 2346:996 - Cao ~464px) -->
        <div class="bg-[#30374F] rounded-[12px] p-4 flex flex-col gap-4 shadow-lg min-h-[464px]">
            <!-- Header on mobile with close button -->
            <div class="flex items-center justify-between pb-2 border-b border-[#404968] lg:hidden">
                <span class="text-sm font-bold text-white uppercase tracking-wider">Bộ lọc sản phẩm</span>
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

            <!-- Title: Danh mục sản phẩm (Figma 2346:997) -->
            <h4 class="text-sm font-semibold text-[#B9C0D4] uppercase tracking-wide leading-[21px]">
                DANH MỤC SẢN PHẨM
            </h4>

            <!-- 1. Search Box (Figma 2380:1524) -->
            <div class="relative w-full h-[46px] bg-[#111322] border border-[#5D6B98] rounded-lg px-3.5 flex items-center justify-between focus-within:border-[#84ADFF] transition-colors">
                <input
                    type="text"
                    :value="filters.search"
                    @input="updateFilter('search', $event.target.value)"
                    placeholder="Tìm kiếm sản phẩm…"
                    class="w-full bg-transparent text-sm text-[#FAFAFA] placeholder-[#B9C0D4] focus:outline-none pr-2"
                />
                <!-- Search Icon on the RIGHT -->
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

            <!-- 2. Danh mục sản phẩm (Figma 2346:708 - 4 mục chuẩn) -->
            <div class="flex flex-col gap-1">
                <button
                    v-for="cat in defaultCategories"
                    :key="cat.id"
                    type="button"
                    @click="selectCategory(cat.id)"
                    class="w-full h-[44px] px-3 rounded-lg text-sm flex items-center justify-between transition-colors duration-150 text-left"
                    :class="
                        filters.category === cat.id
                            ? 'bg-[#404968] text-[#FAFAFA] font-medium'
                            : 'text-[#EFF1F5] hover:bg-[#404968]/50 hover:text-white'
                    "
                >
                    <span>{{ cat.name }}</span>
                    <svg
                        v-if="filters.category !== cat.id"
                        class="w-4 h-4 text-[#B9C0D4]"
                        viewBox="0 0 16 16"
                        fill="currentColor"
                    >
                        <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L9.94 8 6.22 4.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- 3. Lọc theo nhu cầu (Figma 2390:1536 - Text bên trái, Checkbox bên phải justify-between) -->
            <div class="pt-2 flex flex-col gap-2.5">
                <h4 class="text-sm font-semibold text-[#B9C0D4] uppercase tracking-wide leading-[21px]">
                    LỌC THEO NHU CẦU
                </h4>
                <div class="flex flex-col gap-2">
                    <button
                        v-for="purpose in purposes"
                        :key="purpose"
                        type="button"
                        @click="togglePurpose(purpose)"
                        class="w-full flex items-center justify-between py-1 text-left group cursor-pointer focus:outline-none select-none"
                    >
                        <!-- Label text bên TRÁI (Figma 2390:1538) -->
                        <span
                            class="text-sm transition-colors"
                            :class="
                                filters.purposes.includes(purpose)
                                    ? 'text-[#FAFAFA] font-medium'
                                    : 'text-[#EFF1F5] group-hover:text-white'
                            "
                        >
                            {{ purpose }}
                        </span>

                        <!-- Checkbox 16x16px bên PHẢI (Figma 2390:1539) -->
                        <div
                            class="w-4 h-4 rounded-[4px] flex items-center justify-center shrink-0 transition-all duration-150"
                            :class="
                                filters.purposes.includes(purpose)
                                    ? 'bg-[#2970FF] border border-[#2970FF]'
                                    : 'border border-[#EFF1F5] bg-transparent group-hover:border-[#84ADFF]'
                            "
                        >
                            <svg
                                v-if="filters.purposes.includes(purpose)"
                                class="w-2.5 h-2.5 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="3"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Reset Filters if active -->
            <div v-if="hasActiveFilters" class="pt-1">
                <button
                    type="button"
                    @click="resetFilters"
                    class="text-xs text-[#84ADFF] hover:underline"
                >
                    Đặt lại bộ lọc
                </button>
            </div>
        </div>

        <!-- Custom Request Box (Figma 2346:998 - Cao 156px) -->
        <div class="bg-[#30374F] rounded-[12px] p-4 flex flex-col justify-between h-[156px] shadow-lg">
            <div class="space-y-1">
                <h4 class="text-sm font-medium text-[#FAFAFA] leading-[20px]">
                    Tuỳ chỉnh sản phẩm
                </h4>
                <p class="text-[13px] text-[#B9C0D4] leading-[18px]">
                    Màu sắc, kích thước và cá nhân hoá được tư vấn theo nhu cầu ?
                </p>
            </div>
            <Link
                href="/lien-he"
                class="w-full h-10 rounded-lg text-[#F5F8FF] text-base font-medium flex items-center justify-center transition-all duration-200 shadow-md hover:shadow-lg hover:opacity-95 select-none"
                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
            >
                Liên hệ tư vấn
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
            defaultCategories: [
                { id: 'all', name: 'Tất cả sản phẩm' },
                { id: 'keychains', name: 'Móc khoá' },
                { id: 'stationery', name: 'Văn phòng phẩm' },
                { id: 'custom', name: 'Gia công theo yêu cầu' },
            ],
            purposes: [
                'Trang trí không gian',
                'Đồ dùng hằng ngày',
                'Quà tặng cá nhân',
            ],
        }
    },
    computed: {
        hasActiveFilters() {
            return (
                (this.filters.search && this.filters.search.trim() !== '') ||
                (this.filters.category && this.filters.category !== 'all') ||
                (this.filters.purposes && this.filters.purposes.length > 0)
            )
        },
    },
    methods: {
        updateFilter(key, value) {
            this.$emit('update:filters', {
                ...this.filters,
                [key]: value,
            })
        },
        selectCategory(catId) {
            this.updateFilter('category', catId)
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
            this.$emit('reset')
        },
    },
}
</script>
