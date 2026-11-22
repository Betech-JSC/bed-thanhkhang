<template>
    <section
        v-if="hasContent"
        id="why-choose-us-section"
        class="bg-[#FAFAFA] pt-8 md:pt-16 lg:pt-0 pb-5"
    >
        <!-- Top Title Row -->
        <div v-if="trust?.heading" class="app-container mb-6 md:mb-8">
            <div class="flex items-center gap-[10px]">
                <span class="w-3 h-3 rounded-[2px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                <h2 class="font-bold text-[16px] md:text-[18px] leading-[1.6] text-[#005EA4]">
                    {{ trust.heading }}
                </h2>
            </div>
        </div>

        <!-- Full-Bleed Trust Banner (Figma 595:15398 — 1440x578 Desktop) -->
        <div
            class="relative w-full overflow-hidden bg-[#00284B] min-h-[540px] lg:min-h-0 lg:aspect-[1440/578] flex items-center"
        >
            <!-- Background Photo -->
            <picture v-if="bannerUrl" class="absolute inset-0 block w-full h-full pointer-events-none select-none">
                <source
                    v-if="bannerMobileUrl && bannerMobileUrl !== bannerUrl"
                    media="(max-width: 767px)"
                    :srcset="bannerMobileUrl"
                />
                <img
                    :src="bannerUrl"
                    :alt="trust?.banner?.alt || trust?.heading || ''"
                    class="w-full h-full object-cover object-center"
                    loading="lazy"
                    decoding="async"
                />
            </picture>

            <!-- Dark Contrast Overlay (Figma 595:15400) -->
            <div class="absolute inset-0 bg-[#011229]/50 pointer-events-none" aria-hidden="true" />

            <!-- Desktop 5-Node Wave Diagram (lg+, when <= 5 pillars) -->
            <div
                v-if="useWaveLayout"
                class="hidden lg:flex items-center justify-center relative z-10 w-full"
            >
                <div class="relative w-[1152px] h-[260px] shrink-0 lg:scale-[0.84] xl:scale-100 origin-center">
                    <!-- 4 Concave Metaball Bridges -->
                    <div
                        v-if="trustItems.length >= 2"
                        class="absolute flex items-center justify-center left-[71.28px] top-[35.92px] w-[249.025px] h-[187.202px] pointer-events-none select-none"
                        aria-hidden="true"
                    >
                        <div class="flex-none rotate-[-24.58deg]">
                            <div class="relative w-[227.226px] h-[101.909px]">
                                <img
                                    src="/assets/svg/homepage/trust-bridge-1.svg"
                                    alt=""
                                    class="block w-full h-full max-w-none"
                                    aria-hidden="true"
                                />
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="trustItems.length >= 3"
                        class="absolute flex items-center justify-center left-[327px] top-[35.92px] w-[249.025px] h-[187.202px] pointer-events-none select-none"
                        aria-hidden="true"
                    >
                        <div class="flex-none -scale-y-100 rotate-[-155.42deg]">
                            <div class="relative w-[227.226px] h-[101.909px]">
                                <img
                                    src="/assets/svg/homepage/trust-bridge-2.svg"
                                    alt=""
                                    class="block w-full h-full max-w-none"
                                    aria-hidden="true"
                                />
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="trustItems.length >= 4"
                        class="absolute flex items-center justify-center left-[576px] top-[36px] w-[249.025px] h-[187.202px] pointer-events-none select-none"
                        aria-hidden="true"
                    >
                        <div class="flex-none rotate-[-24.58deg]">
                            <div class="relative w-[227.226px] h-[101.909px]">
                                <img
                                    src="/assets/svg/homepage/trust-bridge-3.svg"
                                    alt=""
                                    class="block w-full h-full max-w-none"
                                    aria-hidden="true"
                                />
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="trustItems.length >= 5"
                        class="absolute flex items-center justify-center left-[832px] top-[35.92px] w-[249.025px] h-[187.202px] pointer-events-none select-none"
                        aria-hidden="true"
                    >
                        <div class="flex-none -scale-y-100 rotate-[-155.42deg]">
                            <div class="relative w-[227.226px] h-[101.909px]">
                                <img
                                    src="/assets/svg/homepage/trust-bridge-2.svg"
                                    alt=""
                                    class="block w-full h-full max-w-none"
                                    aria-hidden="true"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Circular Icon Nodes + Text Boxes (all items <= 5) -->
                    <template v-for="(item, idx) in trustItems" :key="item.id || idx">
                        <!-- Circular Icon Node (140x140) -->
                        <div
                            class="absolute w-[140px] h-[140px]"
                            :class="DESKTOP_NODE_POSITIONS[idx].circleClass"
                        >
                            <img
                                :src="DESKTOP_NODE_POSITIONS[idx].ring"
                                alt=""
                                class="absolute inset-0 w-[140px] h-[140px] max-w-none pointer-events-none"
                                aria-hidden="true"
                            />
                            <div
                                class="absolute left-[10px] top-[10px] w-[120px] h-[120px] rounded-full bg-[#00284B] flex items-center justify-center overflow-hidden"
                            >
                                <img
                                    v-if="item.icon?.url"
                                    :src="item.icon.url"
                                    :alt="item.icon.alt || item.title || ''"
                                    width="60"
                                    height="60"
                                    class="w-[60px] h-[60px] object-contain"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                        </div>

                        <!-- Node Text Box -->
                        <div
                            class="absolute flex flex-col gap-[4px] leading-[1.6] break-words"
                            :class="DESKTOP_NODE_POSITIONS[idx].textClass"
                        >
                            <h3 class="font-bold text-[24px] leading-[1.6] text-[#FAFAFA]">
                                {{ item.title }}
                            </h3>
                            <p v-if="item.description" class="font-medium text-[14px] leading-[1.6] text-[#F8FAFC]">
                                {{ item.description }}
                            </p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Mobile, Tablet, or Desktop Extended Grid (when > 5 CMS pillars — zero silent truncation) -->
            <div
                :class="useWaveLayout ? 'lg:hidden' : ''"
                class="relative z-10 w-full py-10 md:py-12"
            >
                <div class="app-container">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        <article
                            v-for="(item, idx) in trustItems"
                            :key="item.id || idx"
                            class="flex items-start gap-4 rounded-[8px] bg-[#00284B]/65 backdrop-blur-[2px] border border-[#C9E0FF]/25 p-4"
                        >
                            <div
                                class="w-14 h-14 rounded-full bg-[#00284B] border-4 border-[#C9E0FF] flex items-center justify-center shrink-0"
                            >
                                <img
                                    v-if="item.icon?.url"
                                    :src="item.icon.url"
                                    :alt="item.icon.alt || item.title || ''"
                                    width="28"
                                    height="28"
                                    class="w-7 h-7 object-contain"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                            <div class="min-w-0 flex-1 flex flex-col gap-1">
                                <h3 class="font-bold text-[18px] leading-[1.4] text-[#FAFAFA]">
                                    {{ item.title }}
                                </h3>
                                <p v-if="item.description" class="font-medium text-[14px] leading-[1.6] text-[#F8FAFC]">
                                    {{ item.description }}
                                </p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const DESKTOP_NODE_POSITIONS = [
    {
        circleClass: 'left-0 top-[120px]',
        ring: '/assets/svg/homepage/trust-ring-outer.svg',
        textClass: 'left-0 top-[-12px] w-[197px] text-left',
    },
    {
        circleClass: 'left-[253px] top-0',
        ring: '/assets/svg/homepage/trust-ring-alt.svg',
        textClass: 'left-[225px] top-[164px] w-[205px] text-center',
    },
    {
        circleClass: 'left-[506px] top-[120px]',
        ring: '/assets/svg/homepage/trust-ring-outer.svg',
        textClass: 'left-[478px] top-[-34px] w-[197px] text-center',
    },
    {
        circleClass: 'left-[759px] top-0',
        ring: '/assets/svg/homepage/trust-ring-outer.svg',
        textClass: 'left-[722px] top-[164px] w-[221px] text-center',
    },
    {
        circleClass: 'left-[1012px] top-[120px]',
        ring: '/assets/svg/homepage/trust-ring-outer.svg',
        textClass: 'left-[955px] top-[-12px] w-[197px] text-right',
    },
]

const props = defineProps({
    trust: {
        type: Object,
        default: () => null,
    },
})

const trustItems = computed(() => {
    const list = props.trust?.items
    if (!Array.isArray(list)) return []
    return list.filter((item) => item && typeof item === 'object' && (item.title || item.description))
})

const useWaveLayout = computed(() => trustItems.value.length <= DESKTOP_NODE_POSITIONS.length)

const bannerUrl = computed(() => {
    const url = props.trust?.banner?.url
    return typeof url === 'string' && url.trim().length > 0 ? url : ''
})

const bannerMobileUrl = computed(() => {
    const url = props.trust?.banner?.mobile_url
    return typeof url === 'string' && url.trim().length > 0 ? url : ''
})

const hasContent = computed(() => {
    return Boolean(props.trust?.heading || trustItems.value.length > 0)
})
</script>
