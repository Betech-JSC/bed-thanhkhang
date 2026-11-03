<template>
    <div
        v-if="totalPages >= 1"
        class="flex items-center justify-between w-full pt-8 pb-4 text-sm"
    >
        <!-- Trang trước -->
        <button
            type="button"
            @click="changePage(currentPage - 1)"
            :disabled="currentPage <= 1"
            class="inline-flex items-center gap-2 text-sm text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors disabled:opacity-40 disabled:cursor-not-allowed select-none"
        >
            <svg class="w-4 h-4 text-current rotate-180" viewBox="0 0 16 16" fill="currentColor">
                <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L9.94 8 6.22 4.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" />
            </svg>
            <span>Trang trước</span>
        </button>

        <!-- Trang tiếp theo -->
        <button
            type="button"
            @click="changePage(currentPage + 1)"
            :disabled="currentPage >= totalPages"
            class="inline-flex items-center gap-2 text-sm text-[#B9C0D4] hover:text-[#FAFAFA] transition-colors disabled:opacity-40 disabled:cursor-not-allowed select-none ml-auto"
        >
            <span>Trang tiếp theo</span>
            <svg class="w-4 h-4 text-current" viewBox="0 0 16 16" fill="currentColor">
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
        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.$emit('change-page', page)
            }
        },
    },
}
</script>
