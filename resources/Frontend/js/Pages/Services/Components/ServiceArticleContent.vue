<template>
    <article class="w-full max-w-[840px] space-y-10 font-sans text-left">
<!-- Mục 01: Khi nào bạn cần...? (Figma Node 2367:2197) -->
        <section v-if="section01 && section01.title" v-scroll-reveal id="section-01" class="scroll-mt-32 space-y-4">
            <h2 class="text-2xl sm:text-[28px] font-bold text-[#FAFAFA] leading-tight sm:leading-[36px]">
                {{ tt(section01.title) }}
            </h2>
            <div class="space-y-4 text-base text-[#B9C0D4] leading-[26px]">
                <p v-for="(paragraph, pIdx) in section01.paragraphs" :key="pIdx">
                    {{ tt(paragraph) }}
                </p>
            </div>
        </section>

<!-- Mục 02: Quy trình các bước (Figma Node 2367:2201) -->
        <section v-if="resolvedSteps.length > 0 || (section02 && section02.title)" v-scroll-reveal id="section-02" class="scroll-mt-32 space-y-5">
            <div class="space-y-3">
                <h2 class="text-2xl sm:text-[28px] font-bold text-[#FAFAFA] leading-tight sm:leading-[36px]">
                    {{ tt(section02.title) }}
                </h2>
                <p v-if="section02.intro" class="text-base text-[#B9C0D4] leading-[28px]">
                    {{ tt(section02.intro) }}
                </p>
            </div>

            <!-- 4 Bước quy trình (Figma Node 2367:2204) -->
            <div class="space-y-0">
                <div
                    v-for="step in resolvedSteps"
                    :key="step.number"
                    class="border-t border-[#404968] py-5 sm:py-[20px] flex items-start gap-4 sm:gap-5"
                >
                    <!-- Số bước badge (40x40px, bg #30374F, text #84ADFF, bo góc 8px) -->
                    <div class="w-10 h-10 rounded-[8px] bg-[#30374F] flex items-center justify-center shrink-0 mt-0.5 select-none">
                        <span class="text-[15px] font-semibold text-[#84ADFF] leading-[18px]">
                            {{ step.number }}
                        </span>
                    </div>

                    <!-- Nội dung bước -->
                    <div class="space-y-2 flex-grow">
                        <h3 class="text-lg sm:text-[19px] font-semibold text-[#FAFAFA] leading-[27px]">
                            {{ tt(step.title) }}
                        </h3>
                        <p class="text-base text-[#B9C0D4] leading-[28px]">
                            {{ tt(step.description) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

<!-- Mục 03: Danh mục chuẩn bị / Thông số kỹ thuật (Figma Node 2367:2229) -->
        <section v-if="resolvedChecklistItems.length > 0 || (section03 && section03.title)" v-scroll-reveal id="section-03" class="scroll-mt-32 space-y-5">
            <div class="space-y-3">
                <h2 class="text-2xl sm:text-[28px] font-bold text-[#FAFAFA] leading-tight sm:leading-[36px]">
                    {{ tt(section03.title) }}
                </h2>
                <p v-if="section03.intro" class="text-base text-[#B9C0D4] leading-[28px]">
                    {{ tt(section03.intro) }}
                </p>
            </div>

            <!-- Card Checklist Thông Số Kỹ Thuật (Figma Node 2367:2232 - bg #171B30, bo góc 12px, padding 28px) -->
            <div class="w-full rounded-[12px] bg-[#171B30] p-6 sm:p-7 space-y-5">
                <div
                    v-for="(item, index) in resolvedChecklistItems"
                    :key="index"
                    class="flex items-start gap-3 sm:gap-3.5"
                >
                    <!-- Checkmark "✓" (16px text, color #84ADFF) -->
                    <span class="text-[16px] font-bold text-[#84ADFF] leading-[24px] shrink-0 select-none">
                        ✓
                    </span>

                    <!-- Detail -->
                    <div class="space-y-1.5 flex-grow">
                        <h4 class="text-[16px] font-semibold text-[#FAFAFA] leading-[24px]">
                            {{ tt(item.title) }}
                        </h4>
                        <p class="text-[15px] text-[#B9C0D4] leading-[26px]">
                            {{ tt(item.description) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

<!-- Mục 04: Hoàn thiện & Tiêu chuẩn chế tác (Figma Node 2367:2253) -->
        <section v-if="section04 && section04.title" v-scroll-reveal id="section-04" class="scroll-mt-32 space-y-4">
            <h2 class="text-2xl sm:text-[28px] font-bold text-[#FAFAFA] leading-tight sm:leading-[36px]">
                {{ tt(section04.title) }}
            </h2>
            <p v-if="section04.paragraph" class="text-base text-[#B9C0D4] leading-[28px]">
                {{ tt(section04.paragraph) }}
            </p>

            <!-- Callout Lưu Ý (Figma Node 2367:2256 - viền trái #2970FF 2px, padding-left 20px) -->
            <div v-if="section04.callout" class="border-l-2 border-[#2970FF] pl-5 py-2">
                <p class="text-base text-[#B2CCFF] leading-[28px]">
                    {{ tt(section04.callout) }}
                </p>
            </div>
        </section>

        <!-- Dynamic CMS Content (nếu backend có nội dung nhập thêm từ admin) -->
        <section v-if="hasDynamicContent" class="pt-6 border-t border-[#404968]">
            <h3 class="text-xl font-bold text-[#FAFAFA] mb-4">
                {{ tt('Thông tin chi tiết bổ sung') }}
            </h3>
            <div
                class="prose prose-invert max-w-none text-[#B9C0D4] leading-relaxed"
                v-html="dynamicHtml"
            ></div>
        </section>
    </article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    service: {
        type: Object,
        default: () => ({}),
    },
    sections: {
        type: Object,
        default: () => ({}),
    },
    steps: {
        type: Array,
        default: () => [],
    },
    checklistItems: {
        type: Array,
        default: () => [],
    },
    dynamicHtml: {
        type: String,
        default: '',
    },
})

const hasDynamicContent = computed(() => {
    return !!props.dynamicHtml && props.dynamicHtml.trim().length > 0
})

const section01 = computed(() => props.sections?.section_01 || props.service?.sections?.section_01 || null)
const section02 = computed(() => props.sections?.section_02 || props.service?.sections?.section_02 || null)
const section03 = computed(() => props.sections?.section_03 || props.service?.sections?.section_03 || null)
const section04 = computed(() => props.sections?.section_04 || props.service?.sections?.section_04 || null)

const resolvedSteps = computed(() => {
    if (Array.isArray(props.steps) && props.steps.length) return props.steps
    if (Array.isArray(props.service?.steps) && props.service.steps.length) return props.service.steps
    return []
})

const resolvedChecklistItems = computed(() => {
    if (Array.isArray(props.checklistItems) && props.checklistItems.length) return props.checklistItems
    if (Array.isArray(props.service?.checklist_items) && props.service.checklist_items.length) return props.service.checklist_items
    if (Array.isArray(props.service?.checklistItems) && props.service.checklistItems.length) return props.service.checklistItems
    return []
})
</script>

<style scoped>
:deep(.prose) {
    color: #b9c0d4;
}
:deep(.prose h2),
:deep(.prose h3),
:deep(.prose h4) {
    color: #fafafa;
    font-weight: 600;
}
:deep(.prose a) {
    color: #84adff;
    text-decoration: underline;
}
:deep(.prose strong) {
    color: #ffffff;
}
:deep(.prose ul) {
    list-style-type: disc;
    padding-left: 1.25rem;
}
:deep(.prose ol) {
    list-style-type: decimal;
    padding-left: 1.25rem;
}
</style>
