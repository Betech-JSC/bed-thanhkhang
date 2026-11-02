<template>
    <div
        class="flex items-center mx-auto space-x-2 py-2 px-4 rounded-2xl border border-gray-warm-300 bg-gray-warm-100 w-fit"
    >
        <button
            :disabled="currentPage === 1"
            @click="changePage(currentPage - 1)"
            class="label-2 font-beau text-gray-warm-700 px-[14px] py-2 border border-gray-warm-300 rounded-lg"
        >
            Previous
        </button>

        <button
            v-for="(page, index) in pagesToShow"
            :key="index"
            :disabled="page === '...'"
            @click="page !== '...' && changePage(page)"
            :class="{
                'bg-[#2F49D9] text-white rounded-[20px]': currentPage === page,
                'bg-transparent text-gray-warm-500': currentPage !== page,
                'cursor-default': page === '...',
            }"
            class="w-10 h-10 flex items-center justify-center text-[14px] font-medium"
        >
            {{ page }}
        </button>

        <button
            :disabled="currentPage === totalPages"
            @click="changePage(currentPage + 1)"
            class="label-2 font-beau text-gray-warm-700 px-[14px] py-2 border border-gray-warm-300 rounded-lg"
        >
            Next
        </button>
    </div>
</template>

<script>
export default {
    props: {
        total: Number,
        perPage: Number,
        currentPage: Number,
    },
    computed: {
        totalPages() {
            return Math.ceil(this.total / this.perPage)
        },
        pagesToShow() {
            const pages = []
            const total = this.totalPages
            const current = this.currentPage

            if (total <= 7) {
                for (let i = 1; i <= total; i++) {
                    pages.push(i)
                }
            } else {
                pages.push(1)

                if (current > 4) {
                    pages.push('...')
                }

                const startPage = Math.max(2, current - 1)
                const endPage = Math.min(total - 1, current + 1)

                for (let i = startPage; i <= endPage; i++) {
                    pages.push(i)
                }

                if (current < total - 3) {
                    pages.push('...')
                }

                pages.push(total)
            }

            return pages
        },
    },
    methods: {
        changePage(page) {
            if (page !== this.currentPage && page >= 1 && page <= this.totalPages) {
                this.$emit('page-changed', page)
                // Scroll lên đầu trang
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth', // hoặc 'auto' nếu bạn không muốn hiệu ứng mượt
                })
            }
        },
    },
}
</script>
