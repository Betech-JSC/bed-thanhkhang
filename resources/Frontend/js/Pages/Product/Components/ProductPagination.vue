<template>
    <nav
        v-if="totalPages > 1"
        :aria-label="translate('Phân trang sản phẩm')"
        class="w-full pt-2 flex items-center justify-center"
    >
        <div class="flex flex-wrap items-center justify-center gap-2">
            <template v-for="(item, index) in pagesToShow" :key="`${item}-${index}`">
                <span
                    v-if="item === '...'"
                    class="w-11 h-11 sm:w-[40px] sm:h-[40px] bg-[#FFFFFF] border border-[#E3E8EF] rounded-[4px] inline-flex items-center justify-center text-[16px] leading-[26px] font-medium text-[#364152] font-sans select-none cursor-default"
                    aria-hidden="true"
                >
                    ...
                </span>
                <button
                    v-else
                    type="button"
                    @click="changePage(item)"
                    class="w-11 h-11 sm:w-[40px] sm:h-[40px] rounded-[4px] border inline-flex items-center justify-center text-[16px] leading-[26px] font-medium font-sans transition-colors select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                    :class="
                        item === currentPage
                            ? 'bg-[#005EA4] border-[#005EA4] text-[#FAFAFA] cursor-default'
                            : 'bg-[#FFFFFF] border-[#E3E8EF] text-[#364152] hover:border-[#005EA4]/50 hover:text-[#00284B] cursor-pointer'
                    "
                    :aria-current="item === currentPage ? 'page' : undefined"
                    :aria-label="`${translate('Trang')} ${item}`"
                >
                    {{ item }}
                </button>
            </template>
        </div>
    </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useTranslate } from '@/composables/useTranslate.js'

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
})

const emit = defineEmits(['change-page'])
const { translate } = useTranslate()

const pagesToShow = computed(() => {
    const total = Math.max(1, Number(props.totalPages) || 1)
    const current = Math.min(Math.max(1, Number(props.currentPage) || 1), total)

    if (total <= 6) {
        return Array.from({ length: total }, (_, idx) => idx + 1)
    }

    if (current <= 3) {
        return [1, 2, 3, 4, '...', total]
    }

    if (current >= total - 2) {
        return [1, '...', total - 3, total - 2, total - 1, total]
    }

    return [1, '...', current - 1, current, current + 1, '...', total]
})

function changePage(page) {
    if (page < 1 || page > props.totalPages || page === props.currentPage) {
        return
    }
    emit('change-page', page)
}
</script>
