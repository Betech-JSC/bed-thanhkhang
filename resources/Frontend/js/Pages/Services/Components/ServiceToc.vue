<template>
    <div v-if="displayItems.length > 0" class="sticky top-20 lg:top-28 z-20 lg:z-auto lg:self-start shrink-0">
        <!-- Desktop Sticky Sidebar (Figma Node 2367:2178 - 248px) -->
        <aside class="hidden lg:block w-[248px] shrink-0 sticky top-28 self-start select-none pt-2">
            <h3 class="text-[13px] font-semibold text-[#B9C0D4] uppercase tracking-wider mb-6 font-sans leading-4">
                {{ tt('TRONG BÀI VIẾT NÀY') }}
            </h3>
            <nav class="flex flex-col font-sans" aria-label="Table of contents">
                <button
                    v-for="item in displayItems"
                    :key="item.id"
                    type="button"
                    @click="onItemClick(item.id)"
                    :class="[
                        'w-full text-left py-[13px] pl-4 pr-2 flex items-start gap-3 transition-colors duration-200 text-[14px] leading-[21px] font-normal focus:outline-none',
                        activeId === item.id
                            ? 'border-l-2 border-[#2970FF] text-[#84ADFF]'
                            : 'border-l border-[#404968] text-[#B9C0D4] hover:text-[#FAFAFA] hover:border-[#84ADFF]'
                    ]"
                >
                    <span
                        :class="[
                            'text-[14px] leading-[21px] font-normal shrink-0 select-none',
                            activeId === item.id ? 'text-[#84ADFF]' : 'text-[#B9C0D4]'
                        ]"
                    >
                        {{ item.number }}
                    </span>
                    <span class="text-[14px] leading-[21px] font-normal truncate">
                        {{ tt(item.title) }}
                    </span>
                </button>
            </nav>
        </aside>

        <!-- Mobile & Tablet Horizontal Sticky Nav -->
        <div class="lg:hidden sticky top-20 z-20 mb-8 py-2.5 bg-[#111322]/95 backdrop-blur-md border-y border-[#404968]/70">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 px-1">
                <button
                    v-for="item in displayItems"
                    :key="item.id"
                    type="button"
                    @click="onItemClick(item.id)"
                    :class="[
                        'shrink-0 text-xs sm:text-sm px-3.5 py-1.5 rounded-full font-medium transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap focus:outline-none',
                        activeId === item.id
                            ? 'bg-[#2970FF] text-white shadow-sm'
                            : 'bg-[#182238] border border-[#404968] text-[#B9C0D4] hover:text-white'
                    ]"
                >
                    <span class="opacity-75 text-[12px] font-mono">{{ item.number }}</span>
                    <span>{{ tt(item.shortTitle || item.title) }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    activeId: {
        type: String,
        default: 'section-01',
    },
})

const displayItems = computed(() =>
    Array.isArray(props.items) && props.items.length > 0 ? props.items : []
)

const emit = defineEmits(['select'])

const onItemClick = (id) => {
    emit('select', id)
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
