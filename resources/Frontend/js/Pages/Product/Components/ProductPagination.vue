<template>
    <div
        v-if="totalPages >= 1"
        class="flex items-start justify-between w-full h-[57px] pt-[32px] pb-[8px] text-[14px] font-normal leading-[17px]"
    >
        <!-- Trang trước (Figma 2701:5376: 102px x 17px, gap 8px) -->
        <button
            type="button"
            :disabled="currentPage <= 1"
            @click="changePage(currentPage - 1, 'prev')"
            class="group inline-flex items-center gap-[8px] h-[17px] text-[14px] font-normal leading-[17px] text-[#B9C0D4] transition-colors duration-200 select-none shrink-0"
            :class="[
                currentPage <= 1
                    ? 'cursor-default'
                    : 'hover:text-[#FAFAFA] active:scale-95 cursor-pointer'
            ]"
            :aria-disabled="currentPage <= 1"
        >
            <svg
                class="w-[16px] h-[16px] text-[#EFF1F5] group-hover:text-[#FAFAFA] transition-transform duration-200 shrink-0"
                :class="{ 'group-hover:-translate-x-0.5': currentPage > 1 }"
                viewBox="0 0 32 32"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M20 24L12 16L20 8"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
            <span>{{ tt('Trang trước') }}</span>
        </button>

        <!-- Trang tiếp theo (Figma 2701:5379: 125px x 17px, gap 8px) -->
        <button
            type="button"
            :disabled="currentPage >= totalPages"
            @click="changePage(currentPage + 1, 'next')"
            class="group inline-flex items-center gap-[8px] h-[17px] text-[14px] font-normal leading-[17px] text-[#B9C0D4] transition-colors duration-200 select-none shrink-0"
            :class="[
                currentPage >= totalPages
                    ? 'cursor-default'
                    : 'hover:text-[#FAFAFA] active:scale-95 cursor-pointer'
            ]"
            :aria-disabled="currentPage >= totalPages"
        >
            <span>{{ tt('Trang tiếp theo') }}</span>
            <svg
                class="w-[16px] h-[16px] text-[#EFF1F5] group-hover:text-[#FAFAFA] transition-transform duration-200 shrink-0"
                :class="{ 'group-hover:translate-x-0.5': currentPage < totalPages }"
                viewBox="0 0 32 32"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M12 24L20 16L12 8"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
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
