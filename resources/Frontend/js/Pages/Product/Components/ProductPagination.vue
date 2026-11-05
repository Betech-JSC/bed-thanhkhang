<template>
    <div
        v-if="totalPages >= 1"
        class="flex items-center justify-between w-full h-[57px] pt-8 pb-2 text-sm leading-[17px]"
    >
        <!-- Trang trước -->
        <button
            type="button"
            :disabled="currentPage <= 1"
            @click="changePage(currentPage - 1, 'prev')"
            class="group inline-flex items-center gap-2 text-sm leading-[17px] transition-colors duration-200 select-none shrink-0"
            :class="[
                currentPage <= 1
                    ? 'opacity-40 cursor-not-allowed pointer-events-none text-[#B9C0D4]'
                    : 'text-[#B9C0D4] hover:text-[#FAFAFA] active:scale-95 cursor-pointer'
            ]"
            :aria-disabled="currentPage <= 1"
        >
            <svg class="w-4 h-4 text-current rotate-180 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 16 16" fill="currentColor">
                <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L9.94 8 6.22 4.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
            </svg>
            <span>{{ tt('Trang trước') }}</span>
        </button>

        <!-- Danh sách số trang ở giữa (Numbered Page Buttons) -->
        <div class="flex items-center gap-1.5 sm:gap-2">
            <template v-for="item in visiblePages" :key="item.type === 'page' ? `page-${item.value}` : item.key">
                <!-- Nút số trang -->
                <button
                    v-if="item.type === 'page'"
                    type="button"
                    :disabled="item.value === currentPage"
                    @click="changePage(item.value, item.value >= currentPage ? 'next' : 'prev')"
                    class="w-8 h-8 rounded-lg text-sm font-medium flex items-center justify-center transition-all select-none"
                    :class="[
                        item.value === currentPage
                            ? 'bg-[#2970FF] text-white shadow-[0_0_12px_rgba(41,112,255,0.4)] cursor-default'
                            : 'text-[#B9C0D4] hover:text-white hover:bg-[#202533] border border-transparent hover:border-[#2E3545] cursor-pointer'
                    ]"
                    :aria-current="item.value === currentPage ? 'page' : undefined"
                >
                    {{ item.value }}
                </button>

                <!-- Dấu ba chấm -->
                <span
                    v-else
                    class="w-8 h-8 flex items-center justify-center text-[#B9C0D4] select-none text-sm"
                >
                    ...
                </span>
            </template>
        </div>

        <!-- Trang tiếp theo -->
        <button
            type="button"
            :disabled="currentPage >= totalPages"
            @click="changePage(currentPage + 1, 'next')"
            class="group inline-flex items-center gap-2 text-sm leading-[17px] transition-colors duration-200 select-none shrink-0"
            :class="[
                currentPage >= totalPages
                    ? 'opacity-40 cursor-not-allowed pointer-events-none text-[#B9C0D4]'
                    : 'text-[#B9C0D4] hover:text-[#FAFAFA] active:scale-95 cursor-pointer'
            ]"
            :aria-disabled="currentPage >= totalPages"
        >
            <span>{{ tt('Trang tiếp theo') }}</span>
            <svg class="w-4 h-4 text-current transition-transform duration-200 group-hover:translate-x-0.5" viewBox="0 0 16 16" fill="currentColor">
                <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L9.94 8 6.22 4.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</template>

<script>
export default {
    name: 'ProductPagination',
    props: {
        currentPage: {
            type: Number,
            required: true,
        },
        totalPages: {
            type: Number,
            required: true,
        },
    },
    emits: ['change-page'],
    computed: {
        visiblePages() {
            const total = this.totalPages
            const current = this.currentPage

            if (total <= 7) {
                return Array.from({ length: total }, (_, i) => ({
                    type: 'page',
                    value: i + 1,
                }))
            }

            const items = []

            if (current <= 4) {
                for (let i = 1; i <= 5; i++) {
                    items.push({ type: 'page', value: i })
                }
                items.push({ type: 'ellipsis', key: 'dots-end' })
                items.push({ type: 'page', value: total })
            } else if (current >= total - 3) {
                items.push({ type: 'page', value: 1 })
                items.push({ type: 'ellipsis', key: 'dots-start' })
                for (let i = total - 4; i <= total; i++) {
                    items.push({ type: 'page', value: i })
                }
            } else {
                items.push({ type: 'page', value: 1 })
                items.push({ type: 'ellipsis', key: 'dots-start' })
                items.push({ type: 'page', value: current - 1 })
                items.push({ type: 'page', value: current })
                items.push({ type: 'page', value: current + 1 })
                items.push({ type: 'ellipsis', key: 'dots-end' })
                items.push({ type: 'page', value: total })
            }

            return items
        },
    },
    methods: {
        changePage(page, direction = 'next') {
            if (page < 1 || page > this.totalPages || page === this.currentPage) {
                return
            }
            this.$emit('change-page', page, direction)
        },
    },
}
</script>
