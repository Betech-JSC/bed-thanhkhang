<template>
    <section
        v-if="hasContent"
        id="about-story-section"
        class="bg-surface py-8 md:py-16 lg:pt-[80px] lg:pb-[115px]"
    >
        <div class="app-container-narrow flex flex-col gap-6 md:gap-8">
            <!-- Section Eyebrow Row (Figma 1171:41929 Desktop 12x12 gap 10px / Mobile 1190:6455 10x10 gap 8px + 18px/29px bold #005EA4) -->
            <div v-if="eyebrow" class="flex items-center gap-2 md:gap-[10px]">
                <span class="w-[10px] h-[10px] md:w-3 md:h-3 rounded-[2px] bg-[#005EA4] shrink-0" aria-hidden="true" />
                <p class="font-bold text-[16px] leading-[24px] md:text-[18px] md:leading-[29px] text-[#005EA4]">
                    {{ eyebrow }}
                </p>
            </div>

            <!-- 2-Column Content Block (Figma 1171:41932: 1152px = Left 493px Collage + 32px gap + Right 627px Copy) -->
            <div
                v-if="validImages.length > 0 || heading || paragraphs.length > 0 || commitment"
                class="flex flex-col lg:flex-row items-start justify-between gap-6 lg:gap-8"
            >
                <!-- Left Column: 3-Image Collage (Figma 1171:41933 — 493x442) -->
                <div
                    v-if="validImages.length > 0"
                    class="w-full lg:w-[493px] shrink-0 flex flex-col gap-3 md:gap-4"
                >
                    <!-- Top Wide Photo (Figma 1171:41934 — 493x280) -->
                    <div
                        v-if="featuredImage"
                        class="w-full aspect-[358/210] md:aspect-[493/280] rounded-[4px] overflow-hidden bg-[#EEF2F6]"
                    >
                        <img
                            :src="featuredImage.url"
                            :alt="featuredImage.alt || headingPlain || eyebrow"
                            width="493"
                            height="280"
                            loading="lazy"
                            decoding="async"
                            class="w-full h-full object-cover object-center"
                            @error="markImageFailed(featuredImage.key)"
                        />
                    </div>

                    <!-- Bottom 2 Detail Photos (Figma 1171:41935 — 2x 239x146, gap 16px) -->
                    <div
                        v-if="secondaryImages.length > 0"
                        class="grid grid-cols-2 gap-3 md:gap-4"
                    >
                        <div
                            v-for="img in secondaryImages"
                            :key="img.key"
                            class="w-full aspect-[173/110] md:aspect-[239/146] rounded-[4px] overflow-hidden bg-[#EEF2F6]"
                        >
                            <img
                                :src="img.url"
                                :alt="img.alt || headingPlain || eyebrow"
                                width="239"
                                height="146"
                                loading="lazy"
                                decoding="async"
                                class="w-full h-full object-cover object-center"
                                @error="markImageFailed(img.key)"
                            />
                        </div>
                    </div>
                </div>

                <!-- Right Column: Brand Story Copy (Figma 1171:41938 — 627x444) -->
                <div
                    v-if="heading || paragraphs.length > 0 || commitment"
                    class="w-full flex-1 min-w-0 lg:max-w-[627px] flex flex-col gap-5 md:gap-8"
                >
                    <!-- Story Heading H2 (Figma 1171:41939: 36px/50px 700 #00284B, preserves 2-line break) -->
                    <h2
                        v-if="heading"
                        class="font-bold text-[24px] leading-[34px] sm:text-[28px] sm:leading-[38px] md:text-[32px] md:leading-[44px] lg:text-[36px] lg:leading-[50px] text-[#00284B] tracking-normal whitespace-pre-line break-words"
                    >
                        {{ heading }}
                    </h2>

                    <!-- Paragraphs & Commitment Tagline (Figma 1171:41940: gap 24px) -->
                    <div
                        v-if="paragraphs.length > 0 || commitment"
                        class="flex flex-col gap-4 md:gap-6"
                    >
                        <p
                            v-for="(paragraph, idx) in paragraphs"
                            :key="idx"
                            class="font-normal text-[15px] leading-[24px] md:text-[16px] md:leading-[26px] text-[#364152] break-words"
                        >
                            {{ paragraph }}
                        </p>

                        <!-- Highlighted Commitment Tagline (Figma 1171:41944: 20px/32px 700 #005EA4) -->
                        <p
                            v-if="commitment"
                            class="font-bold text-[16px] leading-[26px] sm:text-[18px] sm:leading-[28px] lg:text-[20px] lg:leading-[32px] text-[#005EA4] break-words"
                        >
                            {{ commitment }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, reactive, watch } from 'vue'

const props = defineProps({
    storySection: {
        type: Object,
        default: null,
    },
})

const failedImageKeys = reactive({})

watch(
    () => props.storySection?.images,
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

function markImageFailed(key) {
    if (key !== undefined && key !== null) {
        failedImageKeys[key] = true
    }
}

const eyebrow = computed(() => {
    const val = props.storySection?.eyebrow
    return typeof val === 'string' ? val.trim() : ''
})

const heading = computed(() => {
    const val = props.storySection?.heading
    return typeof val === 'string' ? val.trim() : ''
})

const headingPlain = computed(() => heading.value.replace(/\s+/g, ' ').trim())

const paragraphs = computed(() => {
    const rawList = props.storySection?.paragraphs
    if (Array.isArray(rawList)) {
        return rawList
            .map((item) => (typeof item === 'string' ? item.trim() : ''))
            .filter((item) => item.length > 0)
    }
    return [
        props.storySection?.paragraph_1,
        props.storySection?.paragraph_2,
        props.storySection?.paragraph_3,
    ]
        .map((item) => (typeof item === 'string' ? item.trim() : ''))
        .filter((item) => item.length > 0)
})

const commitment = computed(() => {
    const val = props.storySection?.commitment
    return typeof val === 'string' ? val.trim() : ''
})

const validImages = computed(() => {
    const rawList = props.storySection?.images
    if (!Array.isArray(rawList)) return []
    return rawList
        .map((item, index) => {
            if (!item || typeof item !== 'object') return null
            const rawUrl =
                typeof item.url === 'string'
                    ? item.url
                    : typeof item.image === 'string'
                      ? item.image
                      : item.image?.url
            const url = normalizeImageUrl(rawUrl)
            const key = item.id ?? `${index}-${url}`
            if (!url || failedImageKeys[key]) return null
            const alt =
                (typeof item.alt === 'string' && item.alt.trim()) ||
                (typeof item.image?.alt === 'string' && item.image.alt.trim()) ||
                (typeof item.title === 'string' && item.title.trim()) ||
                ''
            return {
                id: item.id ?? null,
                key,
                url,
                alt,
            }
        })
        .filter(Boolean)
})

const featuredImage = computed(() => validImages.value[0] || null)

const secondaryImages = computed(() => validImages.value.slice(1, 3))

const hasContent = computed(() => {
    if (!props.storySection || typeof props.storySection !== 'object') return false
    return Boolean(
        eyebrow.value ||
            heading.value ||
            paragraphs.value.length > 0 ||
            commitment.value ||
            validImages.value.length > 0
    )
})
</script>
