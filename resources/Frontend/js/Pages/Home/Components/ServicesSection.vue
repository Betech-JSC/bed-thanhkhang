<template>
    <section
        v-if="hasContent"
        id="why-choose-us-section"
        class="bg-[#FAFAFA] pt-3 pb-0 md:pt-12 lg:pt-0 lg:pb-5"
    >
        <!-- Top Title Row (Figma 1171:41561 / 1314:52051 — 1152px narrow container) -->
        <div v-if="trust?.heading" class="app-container-narrow mb-4 md:mb-6 lg:mb-8">
            <div class="flex items-start gap-2 lg:gap-[10px]">
                <span class="mt-[7px] lg:mt-[9px] w-[10px] h-[10px] lg:w-3 lg:h-3 rounded-[2px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                <h2 class="font-bold text-[16px] lg:text-[18px] leading-[24px] lg:leading-[1.6] text-[#005EA4]">
                    {{ trust.heading }}
                </h2>
            </div>
        </div>

        <!-- Full-Bleed Trust Banner (Figma 1264:48080 — locked 578px Desktop; Figma 1314:52054 — min-h 611px Mobile) -->
        <div
            v-if="trustItems.length > 0"
            class="relative w-full overflow-hidden bg-[#00284B] min-h-[611px] lg:min-h-0 lg:h-[578px] flex items-center"
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

            <template v-if="useWaveLayout">
                <!-- Desktop 5-Node Wave Diagram (lg+, when <= 5 pillars) -->
                <div
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

                <!-- Mobile / Tablet Vertical 5-Node Metaball Wave Timeline (lg:hidden, when <= 5 pillars — Figma 1314:52054) -->
                <div
                    class="lg:hidden relative z-10 w-full py-9 md:py-10"
                >
                    <div class="app-container-narrow">
                        <div class="flex flex-col">
                            <article
                                v-for="(item, idx) in trustItems"
                                :key="item.id || idx"
                                class="flex items-stretch gap-4"
                            >
                                <!-- Left Timeline Spine: #C9E0FF Circle + Concave Metaball Bridge -->
                                <div class="w-[70px] shrink-0 flex flex-col items-center">
                                    <div
                                        class="relative z-10 w-[70px] h-[70px] rounded-full bg-[#C9E0FF] flex items-center justify-center shrink-0"
                                    >
                                        <div
                                            class="w-[56px] h-[56px] rounded-full bg-[#00284B] flex items-center justify-center overflow-hidden"
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
                                    </div>

                                    <!-- Concave Vertical Metaball Bridge (guarantees >= 28px gap + 18px circle overlap) -->
                                    <div
                                        v-if="idx < trustItems.length - 1"
                                        class="relative z-0 flex-1 w-[58px] min-h-[28px] pointer-events-none select-none"
                                        aria-hidden="true"
                                    >
                                        <svg
                                            viewBox="0 0 58 100"
                                            preserveAspectRatio="none"
                                            class="absolute inset-x-0 -top-[18px] w-full h-[calc(100%+36px)] block"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M6 0L52 0C52 14 36 27 36 50C36 73 52 86 52 100L6 100C6 86 22 73 22 50C22 27 6 14 6 0Z"
                                                fill="#C9E0FF"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Right Borderless Content on Dark Banner -->
                                <div
                                    class="min-w-0 flex-1 pt-1"
                                    :class="idx < trustItems.length - 1 ? 'pb-9' : 'pb-0'"
                                >
                                    <h3 class="font-bold text-[16px] leading-[1.4] text-[#FAFAFA]">
                                        {{ item.title }}
                                    </h3>
                                    <p v-if="item.description" class="mt-1 font-normal text-[13px] leading-[20px] text-[#E3E8EF]">
                                        {{ item.description }}
                                    </p>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Fallback Extended Grid (only when CMS items > 5) -->
            <div
                v-else
                class="relative z-10 w-full py-7 md:py-10"
            >
                <div class="app-container-narrow">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-5">
                        <article
                            v-for="(item, idx) in trustItems"
                            :key="item.id || idx"
                            class="min-h-[92px] flex items-center gap-3.5 rounded-[10px] bg-[#00284B]/[0.68] backdrop-blur-[2px] border border-[#C9E0FF]/[0.28] px-[15px] py-[13px]"
                        >
                            <div
                                class="w-[60px] h-[60px] rounded-full bg-[#C9E0FF]/25 p-[5px] flex items-center justify-center shrink-0"
                            >
                                <div
                                    class="w-[50px] h-[50px] rounded-full bg-[#00284B] border-2 border-[#C9E0FF] flex items-center justify-center overflow-hidden"
                                >
                                    <img
                                        v-if="item.icon?.url"
                                        :src="item.icon.url"
                                        :alt="item.icon.alt || item.title || ''"
                                        width="24"
                                        height="24"
                                        class="w-6 h-6 object-contain"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                </div>
                            </div>
                            <div class="min-w-0 flex-1 flex flex-col gap-0.5">
                                <h3 class="font-bold text-[16px] leading-[1.6] text-[#FAFAFA]">
                                    {{ item.title }}
                                </h3>
                                <p v-if="item.description" class="font-normal text-[13px] leading-[20px] text-[#E3E8EF]">
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
    return trustItems.value.length > 0
})
</script>
