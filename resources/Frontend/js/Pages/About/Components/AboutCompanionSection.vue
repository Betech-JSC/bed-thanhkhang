<template>
    <section
        v-if="hasContent"
        id="about-companion-section"
        class="bg-[#F8FAFC] py-8 md:py-16 lg:py-[80px]"
    >
        <div class="app-container-narrow flex flex-col gap-6 md:gap-8">
            <!-- Section Header Block (Figma 1171:41961 / 1171:41962: Eyebrow 1171:41963 + Mobile 1190:6455 + 36px/50px H2 1171:41966, gap 12px) -->
            <div
                v-if="eyebrow || heading"
                class="flex flex-col items-start gap-3"
            >
                <div v-if="eyebrow" class="flex items-center gap-2 md:gap-[10px]">
                    <span class="w-[10px] h-[10px] md:w-3 md:h-3 rounded-[2px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                    <p class="font-bold text-[16px] leading-[24px] md:text-[18px] md:leading-[29px] text-[#005EA4]">
                        {{ eyebrow }}
                    </p>
                </div>

                <h2
                    v-if="heading"
                    class="text-[24px] leading-[34px] sm:text-[28px] sm:leading-[38px] lg:text-[36px] lg:leading-[50px] font-bold text-[#00284B] tracking-normal break-words"
                >
                    {{ heading }}
                </h2>
            </div>

            <!-- 2-Card Companion Grid (Figma 1171:41967 / 1171:41968..1171:41969: 1152px = 2x 560px + 32px gap) -->
            <div
                v-if="validCards.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8"
            >
                <article
                    v-for="card in validCards"
                    :key="card.key"
                    class="flex flex-col min-w-0"
                >
                    <!-- Top Card Image Box (Figma I1171:41968;754:20832: 560x220, rounded-4px) -->
                    <div class="aspect-[358/180] md:aspect-[560/220] w-full rounded-[4px] overflow-hidden bg-[#EEF2F6]">
                        <picture v-if="card.imageUrl" class="block w-full h-full">
                            <source
                                v-if="card.imageMobileUrl && card.imageMobileUrl !== card.imageUrl"
                                media="(max-width: 767px)"
                                :srcset="card.imageMobileUrl"
                            />
                            <img
                                :src="card.imageUrl"
                                :alt="card.imageAlt"
                                width="560"
                                height="220"
                                loading="lazy"
                                decoding="async"
                                class="w-full h-full object-cover object-center"
                                @error="markCardImageFailed(card.key)"
                            />
                        </picture>
                    </div>

                    <!-- Card Body (Figma I1171:41968;754:20833: padding 24px 24px 24px 0px, gap 24px) -->
                    <div class="pt-4 md:pt-6 md:pr-6 md:pb-6 flex flex-col items-start gap-4 md:gap-6 flex-1 justify-between">
                        <div
                            v-if="card.title || card.description"
                            class="flex flex-col gap-1"
                        >
                            <h3
                                v-if="card.title"
                                class="text-[20px] leading-[30px] md:text-[24px] md:leading-[38px] font-bold text-[#00284B] break-words"
                            >
                                {{ card.title }}
                            </h3>
                            <p
                                v-if="card.description"
                                class="text-[14px] leading-[22px] md:text-[16px] md:leading-[26px] font-normal text-[#364152] break-words"
                            >
                                {{ card.description }}
                            </p>
                        </div>

                        <Link
                            v-if="card.ctaText && card.ctaLink && isInertiaLink(card.ctaLink, card.target)"
                            :href="card.ctaLink"
                            class="inline-flex items-center justify-center min-h-[46px] px-[24px] py-[10px] rounded-[4px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] text-[16px] leading-[26px] font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                        >
                            {{ card.ctaText }}
                        </Link>
                        <a
                            v-else-if="card.ctaText && card.ctaLink"
                            :href="card.ctaLink"
                            :target="isExternalUrl(card.ctaLink) || card.target === '_blank' ? '_blank' : undefined"
                            :rel="isExternalUrl(card.ctaLink) || card.target === '_blank' ? 'noopener noreferrer' : undefined"
                            class="inline-flex items-center justify-center min-h-[46px] px-[24px] py-[10px] rounded-[4px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] text-[16px] leading-[26px] font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                        >
                            {{ card.ctaText }}
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, reactive, watch } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'

const props = defineProps({
    companionSection: {
        type: Object,
        default: null,
    },
})

const failedImageKeys = reactive({})

watch(
    () => props.companionSection?.cards,
    () => {
        Object.keys(failedImageKeys).forEach((k) => {
            delete failedImageKeys[k]
        })
    }
)

function normalizeImageUrl(url) {
    if (!url || typeof url !== 'string') return ''
    const trimmed = url.trim()
    if (!trimmed) return ''
    if (trimmed.includes('/static/assets/')) {
        return trimmed.replace(/^https?:\/\/[^/]+/, '').replace('/static/', '/')
    }
    if (/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\/(assets|images)\//i.test(trimmed)) {
        return trimmed.replace(/^https?:\/\/[^/]+/i, '')
    }
    return trimmed
}

function markCardImageFailed(key) {
    if (key !== undefined && key !== null) {
        failedImageKeys[key] = true
    }
}

function isExternalUrl(href) {
    return typeof href === 'string' && /^https?:\/\//i.test(href)
}

function isInertiaLink(href, target) {
    if (!href || typeof href !== 'string') return false
    if (target === '_blank') return false
    return href.startsWith('/') && !href.startsWith('//')
}

const eyebrow = computed(() => {
    const val = props.companionSection?.eyebrow
    return typeof val === 'string' ? val.trim() : ''
})

const heading = computed(() => {
    const val = props.companionSection?.heading
    return typeof val === 'string' ? val.trim() : ''
})

const validCards = computed(() => {
    const list = props.companionSection?.cards
    if (!Array.isArray(list)) return []
    return list
        .map((card, idx) => {
            if (!card || typeof card !== 'object') return null
            const title = typeof card.title === 'string' ? card.title.trim() : ''
            const description = typeof card.description === 'string' ? card.description.trim() : ''
            const ctaText = String(card.cta_text ?? card.primary_cta_text ?? '').trim()
            const rawLink = String(card.cta_link ?? card.primary_cta_link ?? card.link ?? '').trim()
            const ctaLink = rawLink && rawLink !== '#' ? rawLink : ''

            const key = card.id ?? `${idx}-${title}`
            const rawDesktop =
                typeof card.image === 'string'
                    ? card.image
                    : card.image?.url || (typeof card.url === 'string' ? card.url : '')
            const rawMobile =
                typeof card.image_mobile === 'string'
                    ? card.image_mobile
                    : card.image_mobile?.url || ''
            const imageUrl = failedImageKeys[key] ? '' : normalizeImageUrl(rawDesktop) || normalizeImageUrl(rawMobile)
            const imageMobileUrl = failedImageKeys[key] ? '' : normalizeImageUrl(rawMobile) || imageUrl
            const imageAlt =
                (typeof card.image?.alt === 'string' && card.image.alt.trim()) ||
                (typeof card.alt === 'string' && card.alt.trim()) ||
                title

            if (!title && !description && !imageUrl && !(ctaText && ctaLink)) return null

            return {
                id: card.id ?? null,
                key,
                title,
                description,
                ctaText,
                ctaLink,
                target: typeof card.target === 'string' ? card.target : '_self',
                imageUrl,
                imageMobileUrl,
                imageAlt,
            }
        })
        .filter(Boolean)
})

const hasContent = computed(() => {
    if (!props.companionSection || typeof props.companionSection !== 'object') return false
    return Boolean(eyebrow.value || heading.value || validCards.value.length > 0)
})
</script>
