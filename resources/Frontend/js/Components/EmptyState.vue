<template>
    <div
        v-if="hasAnyContent"
        class="w-full flex flex-col items-start gap-3 md:gap-4 rounded-[8px] p-6 sm:p-8 md:p-10"
        :class="
            isDark
                ? 'bg-white/[0.04] border border-[#C9E0FF]/20'
                : 'bg-white border border-[#E3E8EF] shadow-xs'
        "
    >
        <span
            class="block w-10 h-[3px] rounded-[2px] shrink-0"
            :class="isDark ? 'bg-[#009FFF]' : 'bg-[#005EA4]'"
            aria-hidden="true"
        />

        <component
            :is="resolvedHeadingTag"
            v-if="trimmedTitle"
            class="break-words"
            :class="[headingSizeClass, isDark ? 'text-[#FAFAFA]' : 'text-[#00284B]']"
        >
            {{ trimmedTitle }}
        </component>

        <p
            v-if="trimmedDescription"
            class="max-w-[640px] text-[14px] md:text-[16px] leading-[1.6] break-words"
            :class="isDark ? 'text-[#C9E0FF]' : 'text-[#4B5565]'"
        >
            {{ trimmedDescription }}
        </p>

        <div
            v-if="hasPrimaryAction || hasSecondaryAction"
            class="pt-1 flex flex-wrap items-center gap-3 md:gap-4"
        >
            <template v-if="hasPrimaryAction">
                <Link
                    v-if="isInertiaHref(trimmedPrimaryHref)"
                    :href="trimmedPrimaryHref"
                    :class="primaryButtonClass"
                >
                    {{ trimmedPrimaryText }}
                </Link>
                <a
                    v-else
                    :href="trimmedPrimaryHref"
                    :class="primaryButtonClass"
                >
                    {{ trimmedPrimaryText }}
                </a>
            </template>

            <template v-if="hasSecondaryAction">
                <Link
                    v-if="isInertiaHref(trimmedSecondaryHref)"
                    :href="trimmedSecondaryHref"
                    :class="secondaryButtonClass"
                >
                    {{ trimmedSecondaryText }}
                </Link>
                <a
                    v-else
                    :href="trimmedSecondaryHref"
                    :class="secondaryButtonClass"
                >
                    {{ trimmedSecondaryText }}
                </a>
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    primaryActionText: {
        type: String,
        default: '',
    },
    primaryActionHref: {
        type: String,
        default: '',
    },
    secondaryActionText: {
        type: String,
        default: '',
    },
    secondaryActionHref: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'light',
        validator: (v) => ['light', 'dark'].includes(v),
    },
    headingTag: {
        type: String,
        default: 'h2',
        validator: (v) => ['h1', 'h2', 'h3'].includes(v),
    },
})

const isDark = computed(() => props.variant === 'dark')

const resolvedHeadingTag = computed(() =>
    ['h1', 'h2', 'h3'].includes(props.headingTag) ? props.headingTag : 'h2'
)

const headingSizeClass = computed(() =>
    resolvedHeadingTag.value === 'h1'
        ? 'font-bold text-[26px] sm:text-[32px] lg:text-[40px] leading-[1.3] uppercase tracking-normal'
        : 'font-bold text-[18px] md:text-[22px] leading-[1.45]'
)

const trimmedTitle = computed(() => (typeof props.title === 'string' ? props.title.trim() : ''))
const trimmedDescription = computed(() =>
    typeof props.description === 'string' ? props.description.trim() : ''
)

function normalizeValidHref(href) {
    if (!href || typeof href !== 'string') return ''
    const trimmed = href.trim()
    if (!trimmed || trimmed === '#') return ''
    return trimmed
}

function isInertiaHref(href) {
    return Boolean(href && href.startsWith('/') && !href.startsWith('//'))
}

const trimmedPrimaryText = computed(() =>
    typeof props.primaryActionText === 'string' ? props.primaryActionText.trim() : ''
)
const trimmedPrimaryHref = computed(() => normalizeValidHref(props.primaryActionHref))
const hasPrimaryAction = computed(() =>
    Boolean(trimmedPrimaryText.value && trimmedPrimaryHref.value)
)

const trimmedSecondaryText = computed(() =>
    typeof props.secondaryActionText === 'string' ? props.secondaryActionText.trim() : ''
)
const trimmedSecondaryHref = computed(() => normalizeValidHref(props.secondaryActionHref))
const hasSecondaryAction = computed(() =>
    Boolean(trimmedSecondaryText.value && trimmedSecondaryHref.value)
)

const hasAnyContent = computed(() =>
    Boolean(
        trimmedTitle.value ||
            trimmedDescription.value ||
            hasPrimaryAction.value ||
            hasSecondaryAction.value
    )
)

const baseButtonClass =
    'min-h-[44px] px-5 md:px-6 py-2.5 rounded-[4px] inline-flex items-center justify-center font-medium text-[14px] md:text-[16px] leading-[1.6] transition-colors'

const primaryButtonClass = computed(() =>
    [
        baseButtonClass,
        'bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA]',
        isDark.value
            ? 'focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#00284B]'
            : 'focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2',
    ].join(' ')
)

const secondaryButtonClass = computed(() =>
    [
        baseButtonClass,
        isDark.value
            ? 'bg-[#C9E0FF] hover:bg-[#81BDFF] text-[#003967] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#00284B]'
            : 'bg-[#EEF2F6] hover:bg-[#E3E8EF] text-[#364152] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2',
    ].join(' ')
)
</script>
