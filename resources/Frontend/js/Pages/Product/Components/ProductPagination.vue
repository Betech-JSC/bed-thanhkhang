<template>
    <div
        v-if="totalPages >= 1"
        class="flex items-center justify-between w-full h-[57px] pt-8 pb-2 text-sm leading-[17px]"
    >
        <!-- Trang trước -->
        <button
            type="button"
            @click="changePage(currentPage - 1, 'prev')"
            class="group inline-flex items-center gap-2 text-sm leading-[17px] text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors duration-200 active:scale-95 cursor-pointer select-none"
        >
            <svg class="w-4 h-4 text-current rotate-180 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 16 16" fill="currentColor">
                <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L9.94 8 6.22 4.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
            </svg>
            <span>{{ tt('Trang trước') }}</span>
        </button>

        <!-- Trang tiếp theo -->
        <button
            type="button"
            @click="changePage(currentPage + 1, 'next')"
            class="group inline-flex items-center gap-2 text-sm leading-[17px] text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors duration-200 active:scale-95 cursor-pointer select-none ml-auto"
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
    methods: {
        changePage(page, direction = 'next') {
            let nextPage = page
            if (page < 1) {
                nextPage = this.totalPages || 1
            } else if (page > this.totalPages) {
                nextPage = 1
            }
            this.$emit('change-page', nextPage, direction)
        },
    },
}
</script>
