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

        <!-- Main 2-Column Layout Container (1440x954 Desktop — Figma 1171:41607; 390x816 Mobile — Figma 1191:6449) -->
        <div class="relative z-10 w-full flex flex-col lg:flex-row lg:items-stretch lg:min-h-[954px]">
            <!-- Left Column: Full-Bleed Sprocket Photo Banner + Intro Copy -->
            <div
                class="relative w-full lg:w-[51.32%] lg:shrink-0 bg-[#00284B] lg:rounded-tr-[8px] lg:rounded-br-[8px] aspect-[390/240] sm:aspect-[16/9] lg:aspect-auto lg:min-h-[954px]"
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
                        class="absolute inset-0 bg-gradient-to-b from-[#001427]/65 via-[#001427]/35 to-[#001427]/55 lg:from-[#001427]/30 lg:via-transparent lg:to-transparent"
                        aria-hidden="true"
                    />
                </div>

                <!-- Intro Text Container aligned to .app-container-narrow left margin (1152px inner width -> 144px margin at 1440px) -->
                <div
                    class="relative z-10 w-full h-full flex flex-col justify-center lg:justify-start px-4 py-6 md:px-8 md:py-10 lg:py-[80px] lg:pl-[max(2rem,calc((100vw-1152px)/2))] lg:pr-10"
                >
                    <div class="w-full max-w-[488px] lg:sticky lg:top-[104px] flex flex-col gap-2 lg:gap-0">
                        <h2
                            v-if="process?.heading"
                            class="font-bold text-[20px] sm:text-[24px] lg:text-[28px] leading-[28px] lg:leading-[1.6] lg:min-h-[100px] text-[#FAFAFA] uppercase break-words"
                        >
                            {{ process.heading }}
                        </h2>
                        <p
                            v-if="process?.description"
                            class="font-normal lg:font-medium text-[13px] sm:text-[14px] lg:text-[16px] leading-[20px] lg:leading-[1.6] text-[#E3E8EF] line-clamp-4 lg:line-clamp-none break-words"
                        >
                            {{ process.description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Stacked Process Step Cards (493x166 Desktop Figma 1171:41617, 358x120 Mobile Figma 1191:6453) -->
            <div
                class="w-full lg:flex-1 bg-[#F8FAFC] lg:bg-transparent flex flex-col justify-center pt-6 pb-9 px-4 md:px-8 md:py-12 lg:py-[22px] lg:pl-[64px] lg:pr-[max(2rem,calc((100vw-1152px)/2))]"
            >
                <ol
                    v-if="displaySteps.length > 0"
                    class="w-full flex flex-col gap-3 md:gap-5 lg:gap-[20px]"
                >
                    <li
                        v-for="(step, idx) in displaySteps"
                        :key="step.id || idx"
                        :class="[
                            'w-full rounded-[10px] lg:rounded-[12px] shadow-[2px_2px_6px_0px_rgba(0,0,0,0.25)] py-[17px] pl-5 pr-[18px] lg:pl-[40px] lg:pr-[24px] lg:py-[24px] min-h-[120px] lg:min-h-[166px] flex items-center lg:items-start gap-4 lg:gap-[32px] text-[#C9E0FF]',
                            STEP_BG_CLASSES[Math.min(idx, STEP_BG_CLASSES.length - 1)],
                        ]"
                    >
                        <span
                            class="font-bold text-[44px] lg:text-[64px] leading-[48px] lg:leading-[1.6] text-[#C9E0FF] shrink-0 w-[32px] lg:w-[42px] text-center select-none"
                        >
                            {{ step.number || String(idx + 1) }}
                        </span>
                        <div class="flex-1 min-w-0 flex flex-col gap-1 justify-center">
                            <h3 class="font-bold text-[16px] lg:text-[20px] leading-[22px] lg:leading-[1.6] text-[#FAFAFA] lg:text-[#C9E0FF] break-words">
                                {{ step.title }}
                            </h3>
                            <p
                                v-if="step.description"
                                class="font-normal text-[13px] lg:text-[16px] leading-[20px] lg:leading-[1.6] text-[#E3E8EF] lg:text-[#C9E0FF] line-clamp-3 lg:line-clamp-none break-words"
                            >
                                {{ step.description }}
                            </p>
                        </div>
                    </li>
                </ol>

                <EmptyState
                    v-else
                    variant="light"
                    heading-tag="h3"
                    :title="emptyProcessTitle"
                    :description="emptyProcessDescription"
                    :primary-action-text="emptyProcessActionText"
                    :primary-action-href="contactUrl"
                />
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import EmptyState from '@/Components/EmptyState.vue'

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

const page = usePage()
const instance = getCurrentInstance()

const currentLocale = computed(() => {
    const rawProps = page.props?.value ?? page.props ?? {}
    const loc = rawProps?.locale
    if (typeof loc === 'string') return loc
    return loc?.current || 'vi'
})

const isEn = computed(() => currentLocale.value === 'en')

function safeRoute(name, params, fallback) {
    const proxy = instance?.proxy
    if (proxy && typeof proxy.route === 'function') {
        if (typeof proxy.routes === 'function') {
            const allRoutes = proxy.routes() || {}
            const localizedName =
                typeof proxy.routeLocaleName === 'function' ? proxy.routeLocaleName(name) : name
            if (!allRoutes[localizedName] && !allRoutes[name]) {
                return fallback
            }
        }
        try {
            const url = proxy.route(name, params)
            if (url && url !== '#') return url
        } catch (e) {
            // Fall through to localized fallback
        }
    }
    return fallback
}

const contactUrl = computed(() =>
    safeRoute('contact', undefined, isEn.value ? '/en/contact' : '/lien-he')
)

const emptyProcessTitle = computed(() =>
    isEn.value ? 'Cooperation process is being updated' : 'Quy trình hợp tác đang được cập nhật'
)

const emptyProcessDescription = computed(() =>
    isEn.value
        ? 'Contact our engineering team directly for technical consultation and quotation support.'
        : 'Quý khách vui lòng liên hệ trực tiếp đội ngũ kỹ sư để được tư vấn kỹ thuật và báo giá nhanh chóng.'
)

const emptyProcessActionText = computed(() =>
    isEn.value ? 'Contact for Consultation' : 'Liên hệ tư vấn'
)

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
    return displaySteps.value.length > 0
})
</script>
