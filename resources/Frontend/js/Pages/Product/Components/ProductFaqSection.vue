<template>
    <section
        v-if="normalizedFaqs.length > 0"
        aria-labelledby="pdp-faq-heading"
        class="bg-[#FAFAFA] pb-8 md:pb-16 lg:pb-[80px]"
    >
        <div class="app-container-narrow flex flex-col gap-6 md:gap-8">
            <!-- Section Header (Figma Node 1264:49372 — 12x12 #005EA4 Kicker + 18px Bold #005EA4 Title) -->
            <div class="flex items-center gap-2 lg:gap-[10px]">
                <span
                    class="w-[10px] h-[10px] lg:w-3 lg:h-3 rounded-[2px] bg-[#005EA4] shrink-0"
                    aria-hidden="true"
                />
                <h2
                    id="pdp-faq-heading"
                    class="font-bold text-[16px] lg:text-[18px] leading-[24px] lg:leading-[1.6] text-[#005EA4]"
                >
                    {{ tt('Câu hỏi thường gặp') }}
                </h2>
            </div>

            <!-- FAQ Accordion List (Figma Nodes 1264:49371 / 1264:49693 — 14px vertical gap) -->
            <div class="flex flex-col gap-[14px]">
                <div
                    v-for="(faq, index) in normalizedFaqs"
                    :key="faq.id ?? index"
                    class="bg-white rounded-[8px] shadow-xs transition-colors duration-200"
                    :class="
                        activeFaq === index
                            ? 'border-[1.5px] border-[#005EA4]'
                            : 'border border-[#E3E8EF] hover:border-[#CDD5DF]'
                    "
                >
                    <button
                        :id="`pdp-faq-q-${index}`"
                        type="button"
                        class="w-full flex items-center justify-between gap-4 px-4 py-4 md:px-[25.5px] md:py-[20px] text-left cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] rounded-[8px]"
                        :aria-expanded="activeFaq === index"
                        :aria-controls="`pdp-faq-a-${index}`"
                        @click="toggleFaq(index)"
                    >
                        <span
                            class="text-[15px] md:text-[16px] font-bold leading-[1.6] break-words transition-colors"
                            :class="activeFaq === index ? 'text-[#005EA4]' : 'text-[#00284B]'"
                        >
                            {{ faq.question }}
                        </span>

                        <span
                            class="w-8 h-8 rounded-[6px] inline-flex items-center justify-center shrink-0 text-[#697586]"
                            aria-hidden="true"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 18 18"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-[18px] h-[18px]"
                            >
                                <path
                                    d="M2.5 9H15.5"
                                    stroke="currentColor"
                                    stroke-width="1.75"
                                    stroke-linecap="round"
                                />
                                <path
                                    d="M9 2.5V15.5"
                                    stroke="currentColor"
                                    stroke-width="1.75"
                                    stroke-linecap="round"
                                    class="origin-center transition-transform duration-200 ease-out"
                                    :class="activeFaq === index ? 'scale-y-0' : 'scale-y-100'"
                                />
                            </svg>
                        </span>
                    </button>

                    <div
                        :id="`pdp-faq-a-${index}`"
                        role="region"
                        :aria-labelledby="`pdp-faq-q-${index}`"
                        :aria-hidden="activeFaq !== index"
                        class="faq-accordion-grid"
                        :class="{ 'is-expanded': activeFaq === index }"
                    >
                        <div class="overflow-hidden min-h-0">
                            <div class="px-4 pb-4 md:px-[25.5px] md:pb-[20px]">
                                <p
                                    class="text-[14px] leading-[1.6] text-[#4B5565] max-w-[800px] whitespace-pre-line break-words"
                                >
                                    {{ faq.answer }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    faqs: {
        type: Array,
        default: () => [],
    },
})

function pickText(...values) {
    for (const val of values) {
        if (typeof val === 'string' && val.trim() !== '') {
            return val.trim()
        }
        if (typeof val === 'number' && !Number.isNaN(val)) {
            return String(val)
        }
    }
    return ''
}

const normalizedFaqs = computed(() => {
    if (!Array.isArray(props.faqs)) return []

    return props.faqs
        .map((item, idx) => {
            if (!item || typeof item !== 'object') return null
            const question = pickText(item.question, item.title)
            const answer = pickText(item.answer, item.content, item.description)
            if (!question || !answer) return null

            return {
                id: item.id ?? idx,
                question,
                answer,
            }
        })
        .filter(Boolean)
})

const activeFaq = ref(0)

watch(
    () => normalizedFaqs.value.length,
    (len, prevLen) => {
        if (len === 0) {
            activeFaq.value = null
            return
        }
        if (prevLen === 0 || (activeFaq.value !== null && activeFaq.value >= len)) {
            activeFaq.value = 0
        }
    },
    { immediate: true }
)

function toggleFaq(index) {
    activeFaq.value = activeFaq.value === index ? null : index
}
</script>

<style scoped>
.faq-accordion-grid {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 300ms cubic-bezier(0.22, 1, 0.36, 1);
}

.faq-accordion-grid.is-expanded {
    grid-template-rows: 1fr;
}
</style>
