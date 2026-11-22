<template>
    <section
        v-if="hasContent"
        id="about-intro-section"
        class="bg-surface py-8 md:py-16 lg:pt-20 lg:pb-10"
    >
        <div class="app-container flex flex-col gap-6 md:gap-8">
            <!-- Row 1 (Top Row): Eyebrow + Lead Heading | 2 Square/Portrait Photos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-stretch">
                <!-- Left Column -->
                <div class="flex flex-col justify-between gap-4 lg:gap-6">
                    <div v-if="about.eyebrow" class="flex items-center gap-[10px]">
                        <span class="w-3 h-3 rounded-[2px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                        <p class="font-bold text-[16px] md:text-[18px] leading-[1.6] text-[#005EA4]">
                            {{ about.eyebrow }}
                        </p>
                    </div>

                    <h2
                        v-if="about.lead_highlight || about.lead_rest"
                        class="font-bold text-[20px] md:text-[24px] leading-[1.55] md:leading-[1.6] tracking-normal"
                    >
                        <span v-if="about.lead_highlight" class="text-[#005EA4]">{{ about.lead_highlight }}</span>{{ about.lead_highlight && about.lead_rest ? ' ' : '' }}<span v-if="about.lead_rest" class="text-[#9AA4B2]">{{ about.lead_rest }}</span>
                    </h2>
                </div>

                <!-- Right Column: 2 Top Images (268x272) -->
                <div v-if="topImages.length > 0" class="grid grid-cols-2 gap-3 md:gap-6">
                    <div
                        v-for="(img, idx) in topImages"
                        :key="img.id || idx"
                        class="aspect-[268/272] w-full overflow-hidden rounded-[4px] bg-[#EEF2F6]"
                    >
                        <img
                            :src="img.url"
                            :alt="img.alt || about.eyebrow || ''"
                            class="w-full h-full object-cover object-center"
                            loading="lazy"
                            decoding="async"
                            width="268"
                            height="272"
                        />
                    </div>
                </div>
            </div>

            <!-- Row 2 (Bottom Row): Wide Photo (560x363) | Body Paragraphs + Commitment Quote + CTA -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-end">
                <!-- Left Column: Wide Photo -->
                <div
                    v-if="wideImage"
                    class="aspect-[560/363] w-full overflow-hidden rounded-[4px] bg-[#EEF2F6]"
                >
                    <img
                        :src="wideImage.url"
                        :alt="wideImage.alt || about.eyebrow || ''"
                        class="w-full h-full object-cover object-center"
                        loading="lazy"
                        decoding="async"
                        width="560"
                        height="363"
                    />
                </div>

                <!-- Right Column: Narrative & CTA -->
                <div class="flex flex-col items-start justify-end gap-6 md:gap-10">
                    <div
                        v-if="paragraphs.length > 0 || about.commitment_quote || about.commitment_sub"
                        class="space-y-4 text-[15px] md:text-[16px] leading-[1.6] font-normal text-[#364152]"
                    >
                        <p v-for="(paragraph, idx) in paragraphs" :key="idx">
                            {{ paragraph }}
                        </p>

                        <p
                            v-if="about.commitment_quote || about.commitment_sub"
                            class="font-bold text-[#005EA4]"
                        >
                            <template v-if="about.commitment_quote">{{ about.commitment_quote }}</template>
                            <br v-if="about.commitment_quote && about.commitment_sub" />
                            <template v-if="about.commitment_sub">{{ about.commitment_sub }}</template>
                        </p>
                    </div>

                    <Link
                        v-if="about.cta_text && about.cta_link && isInertiaLink(about.cta_link)"
                        :href="about.cta_link"
                        class="inline-flex items-center justify-center gap-2 px-6 py-[10px] rounded-[4px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] font-medium text-[15px] md:text-[16px] leading-[1.6] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                    >
                        {{ about.cta_text }}
                    </Link>
                    <a
                        v-else-if="about.cta_text && about.cta_link"
                        :href="about.cta_link"
                        class="inline-flex items-center justify-center gap-2 px-6 py-[10px] rounded-[4px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] font-medium text-[15px] md:text-[16px] leading-[1.6] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                    >
                        {{ about.cta_text }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'

const props = defineProps({
    about: {
        type: Object,
        default: null,
    },
})

const validImages = computed(() => {
    const list = props.about?.images
    if (!Array.isArray(list)) return []
    return list.filter((img) => img && typeof img.url === 'string' && img.url.trim().length > 0)
})

const topImages = computed(() => validImages.value.slice(0, 2))

const wideImage = computed(() => validImages.value[2] || null)

const paragraphs = computed(() => {
    const list = props.about?.paragraphs
    if (!Array.isArray(list)) return []
    return list
        .map((item) => (typeof item === 'string' ? item.trim() : ''))
        .filter((item) => item.length > 0)
})

const hasContent = computed(() => {
    if (!props.about || typeof props.about !== 'object') return false
    return Boolean(
        props.about.eyebrow ||
            props.about.lead_highlight ||
            props.about.lead_rest ||
            paragraphs.value.length > 0 ||
            props.about.commitment_quote ||
            props.about.commitment_sub ||
            validImages.value.length > 0
    )
})

function isInertiaLink(href) {
    if (!href || typeof href !== 'string') return false
    return href.startsWith('/') && !href.startsWith('//')
}
</script>
