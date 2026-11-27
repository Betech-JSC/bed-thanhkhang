<template>
    <section
        v-if="hasContent"
        id="process-section"
        class="relative w-full bg-[#FAFAFA] overflow-x-clip pt-10 lg:pt-0"
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

        <!-- Desktop Full-Bleed Left Visual Panel (Figma 1264:48128 — exact 739px at 1440px = calc(50% + 19px), crisp portrait ratio, zero 100vw scrollbar drift) -->
        <div
            class="hidden lg:block absolute inset-y-0 left-0 w-[calc(50%+19px)] bg-[#00284B] rounded-tr-[8px] rounded-br-[8px] overflow-hidden pointer-events-none select-none"
            aria-hidden="true"
        >
            <picture v-if="bannerUrl" class="block w-full h-full">
                <img
                    :src="bannerUrl"
                    :alt="process?.banner?.alt || process?.heading || ''"
                    class="w-full h-full object-cover object-center"
                    loading="lazy"
                    decoding="async"
                />
            </picture>
            <div
                class="absolute inset-0 bg-gradient-to-r from-[#001427]/50 via-[#001427]/30 to-[#001427]/15"
                aria-hidden="true"
            />
        </div>

        <!-- Main 2-Column Layout Container aligned 100% flush with .app-container-narrow (1152px inner, 954px Desktop — Figma 1264:48123) -->
        <div class="relative z-10 w-full lg:max-w-[1216px] lg:mx-auto lg:px-8 lg:min-h-[954px] flex flex-col lg:flex-row lg:items-stretch lg:justify-between lg:gap-10">
            <!-- Left Column: Vertically Centered Intro Copy (Mobile carries full-bleed sprocket banner; Desktop overlays left visual panel) -->
            <div
                class="relative w-full lg:w-[420px] xl:w-[488px] lg:shrink-0 bg-[#00284B] lg:bg-transparent min-h-[500px] aspect-[390/500] sm:aspect-auto sm:min-h-[420px] lg:aspect-auto lg:min-h-0 flex flex-col justify-start pt-8 pb-8 px-4 md:px-8 lg:justify-center lg:px-0 lg:py-[80px]"
            >
                <!-- Mobile/Tablet Background Image & Scrim Layer -->
                <div class="lg:hidden absolute inset-0 overflow-hidden pointer-events-none select-none">
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
                        class="absolute inset-0 bg-gradient-to-b from-[#001427]/55 via-[#001427]/45 to-[#001427]/65"
                        aria-hidden="true"
                    />
                </div>

                <!-- Vertically Centered Sticky Intro Block (centered in left column at rest & sticky centered on scroll) -->
                <div class="relative z-10 w-full max-w-[488px] lg:sticky lg:top-[300px] flex flex-col gap-2 lg:gap-0">
                    <h2
                        v-if="process?.heading"
                        class="font-bold text-[20px] sm:text-[24px] lg:text-[28px] leading-[1.4] lg:leading-[1.6] lg:min-h-[100px] text-[#FAFAFA] uppercase break-words"
                    >
                        {{ process.heading }}
                    </h2>
                    <p
                        v-if="process?.description"
                        class="font-normal lg:font-medium text-[13px] sm:text-[14px] lg:text-[16px] leading-[1.6] text-[#E3E8EF] break-words"
                    >
                        {{ process.description }}
                    </p>
                </div>
            </div>

            <!-- Right Column: Sticky Stacking Process Step Cards (493x166 Desktop Figma 1264:48134..48158, 358x120 Mobile Figma 1191:6453) -->
            <div
                class="relative overflow-hidden lg:overflow-visible w-full lg:w-[460px] xl:w-[493px] lg:shrink-0 bg-[#F8FAFC] lg:bg-transparent flex flex-col justify-center lg:justify-start pt-6 pb-9 px-4 md:px-8 md:py-12 lg:px-0 lg:pt-[80px] lg:pb-[90px]"
            >
                <!-- Mobile Decorative Wave Background Layer (Figma 1314:52102) -->
                <div
                    class="lg:hidden pointer-events-none select-none absolute inset-0 z-0 opacity-85"
                    aria-hidden="true"
                    style="mask-image: url('/assets/svg/homepage/process-wave-mask.svg'); -webkit-mask-image: url('/assets/svg/homepage/process-wave-mask.svg'); mask-repeat: no-repeat; -webkit-mask-repeat: no-repeat; mask-size: 100% 100%; -webkit-mask-size: 100% 100%; mask-position: center; -webkit-mask-position: center;"
                >
                    <img
                        src="/assets/images/homepage/process-wave-bg.png"
                        alt=""
                        class="w-full h-full object-fill"
                        loading="lazy"
                        decoding="async"
                    />
                </div>

                <ol
                    v-if="displaySteps.length > 0"
                    class="relative z-10 w-full flex flex-col gap-3 md:gap-5 lg:gap-[40px]"
                >
                    <li
                        v-for="(step, idx) in displaySteps"
                        :key="step.id || idx"
                        :class="[
                            'w-full lg:sticky rounded-[8px] lg:rounded-[12px] [filter:drop-shadow(2px_2px_3px_rgba(0,0,0,0.25))] py-[17px] pl-5 pr-[18px] lg:pl-[40px] lg:pr-[24px] lg:py-[24px] min-h-[120px] lg:min-h-[166px] flex items-center lg:items-start gap-4 lg:gap-[32px] text-[#C9E0FF] transition-transform duration-300',
                            STEP_BG_CLASSES[Math.min(idx, STEP_BG_CLASSES.length - 1)],
                            STEP_STICKY_CLASSES[Math.min(idx, STEP_STICKY_CLASSES.length - 1)],
                        ]"
                    >
                        <span
                            class="font-bold text-[44px] lg:text-[64px] leading-[48px] lg:leading-[1.6] text-[#C9E0FF] shrink-0 w-[32px] lg:w-[37px] text-center select-none"
                        >
                            {{ step.number || String(idx + 1) }}
                        </span>
                        <div class="flex-1 min-w-0 flex flex-col gap-1 justify-center">
                            <h3 class="font-bold text-[16px] lg:text-[20px] leading-[1.6] text-[#FAFAFA] lg:text-[#C9E0FF] break-words">
                                {{ step.title }}
                            </h3>
                            <p
                                v-if="step.description"
                                class="font-normal text-[13px] lg:text-[16px] leading-[1.6] text-[#E3E8EF] lg:text-[#C9E0FF] break-words"
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

const STEP_STICKY_CLASSES = [
    'lg:top-[112px] z-[1]',
    'lg:top-[132px] z-[2]',
    'lg:top-[152px] z-[3]',
    'lg:top-[172px] z-[4]',
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
