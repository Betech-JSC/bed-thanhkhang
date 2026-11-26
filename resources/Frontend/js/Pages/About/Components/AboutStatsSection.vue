<template>
    <section
        v-if="validStats.length > 0"
        id="about-stats-section"
        class="bg-[#F8FAFC] py-8 md:py-[40px]"
    >
        <div class="app-container-narrow">
            <!-- 4-Column Trust Metrics Grid (Figma 1171:41956 / 1171:41957..1171:41960: 1152px = 4x 264px + 3x 32px gap) -->
            <dl class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 lg:gap-8">
                <div
                    v-for="item in validStats"
                    :key="item.key"
                    class="flex flex-col items-center justify-between h-full text-center gap-2 md:gap-3 min-w-0"
                >
                    <!-- Top Description Label (Figma I1171:41957;749:20797: 16px/26px 400 #364152) -->
                    <dt
                        v-if="item.label"
                        class="text-[13px] sm:text-[14px] md:text-[16px] leading-[20px] sm:leading-[22px] md:leading-[26px] font-normal text-[#364152] break-words"
                    >
                        {{ item.label }}
                    </dt>

                    <!-- Bottom Highlight Value (Figma I1171:41957;749:20796: 36px/50px 700 #005EA4) -->
                    <dd
                        v-if="item.value"
                        class="text-[24px] sm:text-[28px] lg:text-[36px] leading-[1.35] lg:leading-[50px] font-bold text-[#005EA4] break-words"
                    >
                        {{ item.value }}
                    </dd>
                </div>
            </dl>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    statsItems: {
        type: Array,
        default: () => [],
    },
})

const validStats = computed(() => {
    if (!Array.isArray(props.statsItems)) return []
    return props.statsItems
        .map((item, idx) => {
            if (!item || typeof item !== 'object') return null
            const label = String(item.label ?? item.subtitle ?? item.description ?? '').trim()
            const value = String(item.value ?? item.title ?? '').trim()
            if (!label && !value) return null
            return {
                id: item.id ?? null,
                key: item.id ?? `${idx}-${label}-${value}`,
                label,
                value,
            }
        })
        .filter(Boolean)
})
</script>
