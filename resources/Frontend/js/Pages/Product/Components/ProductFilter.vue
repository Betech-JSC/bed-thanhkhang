<template>
    <div class="w-full lg:w-[240px] space-y-3">
        <!-- Main Filter Card (Figma 2346:996 - Cao 464px) -->
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

            <!-- Title: Danh mục sản phẩm (Figma 2346:997) -->
            <h4 class="text-sm font-semibold text-[#B9C0D4] uppercase leading-[21px]">
                {{ tt('DANH MỤC SẢN PHẨM') }}
            </h4>

            <!-- 1. Search Box (Figma 2380:1524) -->
            <div class="relative w-full h-[46px] bg-[#111322] border border-[#5D6B98] rounded-lg px-[13px] flex items-center justify-between gap-2 focus-within:border-[#84ADFF] transition-colors">
                <input
                    type="text"
                    :value="filters.search"
                    @input="updateFilter('search', $event.target.value)"
                    :placeholder="tt('Tìm kiếm sản phẩm…')"
                    class="w-full bg-transparent text-sm leading-5 text-[#FAFAFA] placeholder-[#B9C0D4] focus:outline-none pr-2"
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

            <!-- 2. Danh mục sản phẩm (Động từ Backend + fallback Figma 2346:708 - 4 mục chuẩn, h 188px) -->
            <div class="flex flex-col gap-1">
                <button
                    v-for="cat in displayCategories"
                    :key="cat.id"
                    type="button"
                    @click="selectCategory(cat.id)"
                    class="w-full h-[44px] px-3.5 rounded-lg text-sm leading-5 font-normal flex items-center transition-colors duration-150 text-left"
                    :class="
                        isCategorySelected(cat)
                            ? 'bg-[#404968] text-[#FAFAFA] font-medium'
                            : 'text-[#EFF1F5] hover:bg-[#404968]/50 hover:text-white'
                    "
                >
                    <span>{{ cat.name }}</span>
                </button>
            </div>

            <!-- 3. Lọc theo nhu cầu (Figma 2390:1536 - gap 16px, h 121px) -->
            <div class="pt-2 flex flex-col gap-4">
                <h4 class="text-sm font-semibold text-[#B9C0D4] uppercase leading-[21px]">
                    {{ tt('LỌC THEO NHU CẦU') }}
                </h4>
                <div class="flex flex-col gap-3">
                    <button
                        v-for="purpose in purposes"
                        :key="purpose"
                        type="button"
                        @click="togglePurpose(purpose)"
                        class="w-full h-[20px] px-3 flex items-center justify-between gap-3 text-left group cursor-pointer focus:outline-none select-none"
                    >
                        <!-- Label text bên TRÁI (Figma 2390:1538) -->
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

                        <!-- Custom Checkbox bên PHẢI (Figma 2390:1539) -->
                        <div
                            class="w-[18px] h-[18px] rounded border flex items-center justify-center transition-colors shrink-0"
                            :class="
                                isPurposeSelected(purpose)
                                    ? 'bg-[#2970FF] border-[#2970FF]'
                                    : 'border-[#5D6B98] bg-transparent group-hover:border-[#84ADFF]'
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
                    </button>
                </div>
            </div>

            <!-- Nút Reset Filter nếu đang lọc -->
            <div v-if="hasActiveFilters" class="pt-2 border-t border-[#404968]">
                <button
                    type="button"
                    @click="resetFilters"
                    class="w-full py-2 text-xs font-semibold text-[#84ADFF] hover:text-white transition-colors flex items-center justify-center gap-1.5"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>{{ tt('Đặt lại bộ lọc') }}</span>
                </button>
            </div>
        </div>

        <!-- Custom Request Box (Figma 2346:998 - Cao 156px) -->
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
    computed: {
        purposes() {
            return [
                this.tt('Trang trí không gian'),
                this.tt('Đồ dùng hằng ngày'),
                this.tt('Quà tặng cá nhân'),
            ]
        },
        displayCategories() {
            const list = [{ id: 'all', name: this.tt('Tất cả sản phẩm'), slug: 'all' }];
            if (this.categories && this.categories.length > 0) {
                this.categories.forEach(c => {
                    const id = c.slug || c.id;
                    if (id !== 'all') {
                        list.push({
                            id: id,
                            name: this.tt(c.title || c.name),
                            slug: c.slug || c.id,
                        });
                    }
                });
            }
            return list;
        },
        hasActiveFilters() {
            return (
                (this.filters.search && this.filters.search.trim() !== '') ||
                (this.filters.category && this.filters.category !== 'all') ||
                (this.filters.purposes && this.filters.purposes.length > 0)
            )
        },
    },
    methods: {
        isCategorySelected(cat) {
            const current = this.filters.category;
            if (!current || current === 'all') return cat.id === 'all' || cat.slug === 'all';
            return current === cat.id || current === cat.slug;
        },
        isPurposeSelected(purpose) {
            if (!this.filters.purposes) return false;
            return this.filters.purposes.includes(purpose);
        },
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
