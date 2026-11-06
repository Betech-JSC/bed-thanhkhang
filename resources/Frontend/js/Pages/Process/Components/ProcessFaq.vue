<template>
    <!-- SECTION 4: FAQ Section (Figma Node 2334:8455, 1440x621px, padding: 80px 144px 120px) -->
    <section v-if="faqs.length > 0" v-scroll-reveal class="relative w-full pt-12 pb-20 lg:pt-20 lg:pb-[120px] overflow-hidden">
        <!-- Container max-width 1152px -->
        <div class="app-container space-y-8">
            <!-- Section Header (Figma Node 2334:8456, gap: 16px) -->
            <div class="space-y-4">
                <span class="font-normal text-[14px] leading-[20px] text-[#84ADFF] uppercase select-none block">
                    {{ tt('CÂU HỎI THƯỜNG GẶP') }}
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-[32px] font-bold text-[#FAFAFA] leading-[37px] sm:leading-[37px] lg:leading-[37px]">
                    {{ tt('Giải đáp trước khi liên hệ') }}
                </h2>
            </div>

            <!-- FAQ Accordion List (Figma Node 2390:5367, gap: 16px) -->
            <div class="space-y-4">
                <div
                    v-for="(faq, index) in faqs"
                    :key="index"
                    class="rounded-xl border border-[#5D6B98] bg-white/20 backdrop-blur-[10px] transition-colors duration-200 overflow-hidden"
                    :class="activeFaq === index ? 'border-white/60 bg-white/[0.25]' : 'hover:border-white/40 hover:bg-white/[0.22]'"
                >
                    <!-- Question Button (Figma padding: 24px, Collapsed height: 72px, Expanded gap: 16px) -->
                    <button
                        type="button"
                        @click="toggleFaq(index)"
                        class="w-full text-left px-5 py-5 sm:px-6 sm:py-[23px] flex items-center justify-between gap-4 cursor-pointer select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-[#84ADFF]"
                        :aria-expanded="activeFaq === index"
                    >
                        <span class="text-base sm:text-lg font-medium text-[#FAFAFA] leading-[22px] sm:leading-[22px]">
                            {{ tt(faq.question) }}
                        </span>

                        <!-- Indicator Plus / Minus (Figma Node 2399:1430 / 2399:1436: 24x24px) -->
                        <div
                            class="w-6 h-6 shrink-0 flex items-center justify-center text-[#FCFCFC] transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                            :class="activeFaq === index ? 'rotate-180' : 'rotate-0'"
                        >
                            <svg
                                class="w-6 h-6"
                                viewBox="0 0 48 48"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M24 10V38"
                                    stroke="#FCFCFC"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="origin-center transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                                    :class="activeFaq === index ? 'rotate-90 scale-y-0' : 'rotate-0 scale-y-100'"
                                />
                                <path
                                    d="M10 24H38"
                                    stroke="#FCFCFC"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </div>
                    </button>

                    <!-- Expandable Answer (Figma Node 2399:1432, 16px / 26px / #FCFCFC, Smooth 60fps CSS Grid 0fr <-> 1fr) -->
                    <div
                        class="faq-accordion-grid"
                        :class="{ 'is-expanded': activeFaq === index }"
                        :aria-hidden="activeFaq !== index"
                    >
                        <div class="overflow-hidden min-h-0">
                            <div
                                class="px-5 pb-5 sm:px-6 sm:pb-[23px] -mt-1 sm:-mt-[7px] text-sm sm:text-base text-[#FCFCFC] leading-relaxed lg:leading-[26px]"
                            >
                                <p>{{ tt(faq.answer) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    faqs: {
        type: Array,
        default: () => [],
    },
})

const activeFaq = ref(null) // Mặc định đóng tất cả câu hỏi khi vào trang

const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index
}

const faqs = computed(() =>
    Array.isArray(props.faqs) && props.faqs.length
        ? props.faqs.map((item) => ({
              question: item.question || item.title || '',
              answer: item.answer || item.content || item.description || '',
          }))
        : []
)
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
