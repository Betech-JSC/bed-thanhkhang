<template>
    <section
        v-if="hasContent"
        id="process-section"
        class="relative w-full bg-[#FAFAFA] overflow-x-clip"
    >
        <!-- Right-Side Decorative Wave Background Layer (Desktop lg+, Figma 595:15444) -->
        <div
            class="hidden lg:block pointer-events-none select-none absolute inset-y-0 right-0 w-full max-w-[1440px]"
            aria-hidden="true"
            style="mask-image: url('/assets/svg/homepage/process-wave-mask.svg'); -webkit-mask-image: url('/assets/svg/homepage/process-wave-mask.svg'); mask-repeat: no-repeat; -webkit-mask-repeat: no-repeat; mask-size: 1440px 954px; -webkit-mask-size: 1440px 954px; mask-position: right top; -webkit-mask-position: right top;"
        >
            <img
                src="/assets/images/homepage/process-wave-bg.png"
                alt=""
                class="w-full h-full object-cover object-right"
                loading="lazy"
                decoding="async"
            />
        </div>

        <!-- Main 2-Column Layout Container (1440x954 Desktop) -->
        <div class="relative z-10 w-full flex flex-col lg:flex-row lg:items-stretch lg:min-h-[954px]">
            <!-- Left Column: Full-Bleed Sprocket Photo Banner + Intro Copy -->
            <div
                class="relative w-full lg:w-[51.32%] lg:shrink-0 bg-[#00284B] lg:rounded-tr-[8px] lg:rounded-br-[8px] aspect-[375/340] sm:aspect-[16/10] lg:aspect-auto lg:min-h-[954px]"
            >
                <!-- Clipped Background Image & Scrim Layer (preserves lg:sticky on foreground text) -->
                <div class="absolute inset-0 overflow-hidden lg:rounded-tr-[8px] lg:rounded-br-[8px] pointer-events-none select-none">
                    <picture v-if="bannerUrl" class="block w-full h-full">
                        <source
                            v-if="bannerMobileUrl && bannerMobileUrl !== bannerUrl"
                            media="(max-width: 767px)"
                            :srcset="bannerMobileUrl"
                        />
                        <img
                            :src="bannerUrl"
                            :alt="process?.banner?.alt || process?.heading || ''"
                            class="w-full h-full object-cover object-center"
                            loading="lazy"
                            decoding="async"
                        />
                    </picture>

                    <div
                        class="absolute inset-0 bg-gradient-to-b from-[#001427]/55 via-[#001427]/20 to-transparent lg:from-[#001427]/30 lg:via-transparent"
                        aria-hidden="true"
                    />
                </div>

                <!-- Intro Text Container aligned to .app-container left margin (1280px inner width) -->
                <div
                    class="relative z-10 w-full h-full flex flex-col justify-start px-4 py-8 md:px-8 md:py-12 lg:py-[80px] lg:pl-[max(2rem,calc((100vw-1280px)/2))] lg:pr-10"
                >
                    <div class="w-full max-w-[488px] lg:sticky lg:top-[104px] flex flex-col gap-3 lg:gap-0">
                        <h2
                            v-if="process?.heading"
                            class="font-bold text-[22px] sm:text-[26px] lg:text-[28px] leading-[1.45] lg:leading-[1.6] lg:min-h-[100px] text-[#FAFAFA] uppercase break-words"
                        >
                            {{ process.heading }}
                        </h2>
                        <p
                            v-if="process?.description"
                            class="font-medium text-[14px] md:text-[16px] leading-[1.6] text-[#E3E8EF] break-words"
                        >
                            {{ process.description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: 4 Stacked Process Step Cards -->
            <div
                class="w-full lg:flex-1 flex flex-col justify-center lg:justify-start px-4 py-8 md:px-8 md:py-12 lg:pt-[80px] lg:pb-[90px] lg:pl-[64px] lg:pr-[max(2rem,calc((100vw-1280px)/2))]"
            >
                <ol
                    v-if="displaySteps.length > 0"
                    class="w-full flex flex-col gap-4 md:gap-6 lg:gap-[40px]"
                >
                    <li
                        v-for="(step, idx) in displaySteps"
                        :key="step.id || idx"
                        :class="[
                            'w-full rounded-[12px] shadow-[2px_2px_3px_rgba(0,0,0,0.25)] px-5 py-5 md:pl-[40px] md:pr-[24px] md:py-[24px] min-h-[140px] md:min-h-[166px] flex items-start gap-5 md:gap-[32px] text-[#C9E0FF]',
                            STEP_BG_CLASSES[Math.min(idx, STEP_BG_CLASSES.length - 1)],
                        ]"
                    >
                        <span
                            class="font-bold text-[44px] sm:text-[56px] lg:text-[64px] leading-[1.1] lg:leading-[1.6] text-[#C9E0FF] shrink-0 w-[36px] md:w-[42px] text-center select-none"
                        >
                            {{ step.number || String(idx + 1) }}
                        </span>
                        <div class="flex-1 min-w-0 flex flex-col gap-1 justify-center">
                            <h3 class="font-bold text-[18px] md:text-[20px] leading-[1.5] md:leading-[1.6] text-[#C9E0FF] break-words">
                                {{ step.title }}
                            </h3>
                            <p
                                v-if="step.description"
                                class="font-normal text-[14px] md:text-[16px] leading-[1.6] text-[#C9E0FF] break-words"
                            >
                                {{ step.description }}
                            </p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const STEP_BG_CLASSES = [
    'bg-[#005EA4]',
    'bg-[#004D86]',
    'bg-[#003967]',
    'bg-[#00284B]',
]

const props = defineProps({
    process: {
        type: Object,
        default: () => null,
    },
})

const displaySteps = computed(() => {
    const list = props.process?.steps
    if (!Array.isArray(list)) return []
    return list.filter((step) => step && typeof step === 'object' && (step.title || step.description))
})

const bannerUrl = computed(() => {
    const url = props.process?.banner?.url
    return typeof url === 'string' && url.trim().length > 0 ? url : ''
})

const bannerMobileUrl = computed(() => {
    const url = props.process?.banner?.mobile_url
    return typeof url === 'string' && url.trim().length > 0 ? url : ''
})

const hasContent = computed(() => {
    return Boolean(props.process?.heading || props.process?.description || displaySteps.value.length > 0)
})
</script>
