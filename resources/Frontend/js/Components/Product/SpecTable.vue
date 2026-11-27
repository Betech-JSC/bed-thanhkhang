<template>
    <!-- BẢNG THÔNG SỐ & CẤU TẠO (Figma Node 1264:49515) -->
    <div class="w-full max-w-[560px] bg-white border border-[#E3E8EF] rounded-[4px] overflow-hidden">
        <div class="bg-[#00284B] px-4 md:px-[20px] py-[14px]">
            <h3 class="text-[#FAFAFA] text-[14px] font-bold leading-[1.6] uppercase">
                {{ resolvedTitle }}
            </h3>
        </div>

        <div v-if="specList.length > 0" class="w-full">
            <div
                v-for="(spec, idx) in specList"
                :key="`${spec.label}-${idx}`"
                :class="[
                    'px-4 md:px-[20px] py-[13px] grid grid-cols-2 gap-4 items-center text-[14px] leading-[1.6] border-b border-[#E3E8EF] last:border-b-0',
                    idx % 2 === 0 ? 'bg-white' : 'bg-[#F8FAFC]',
                ]"
            >
                <span class="text-[#4B5565] font-normal break-words">
                    {{ translateLabel(spec.label) }}
                </span>
                <span class="text-[#001932] font-bold text-right break-words">
                    {{ translateValue(spec.value) }}
                </span>
            </div>
        </div>

        <div
            v-else
            class="px-4 md:px-[20px] py-6 bg-white text-[14px] leading-[1.6] text-[#697586]"
        >
            {{ tt('Đang cập nhật thông số kỹ thuật cho sản phẩm này.') }}
        </div>
    </div>
</template>

<script>
import {
    resolveLocaleFromProps,
    translateSpecLabel,
    translateSpecValue,
} from '@/composables/useTranslate.js'

export default {
    name: 'SpecTable',
    props: {
        specifications: {
            type: Array,
            default: () => [],
        },
        specs: {
            type: Array,
            default: () => [],
        },
        title: {
            type: String,
            default: '',
        },
    },
    computed: {
        isEn() {
            return resolveLocaleFromProps(this.$page?.props, 'vi') === 'en'
        },
        resolvedTitle() {
            const trimmed = typeof this.title === 'string' ? this.title.trim() : ''
            return trimmed ? this.tt(trimmed) : this.tt('THÔNG SỐ & CẤU TẠO')
        },
        specList() {
            const source =
                Array.isArray(this.specifications) && this.specifications.length > 0
                    ? this.specifications
                    : Array.isArray(this.specs)
                    ? this.specs
                    : []

            return source
                .map((item) => {
                    if (!item || typeof item !== 'object') return null
                    const rawLabel = item.label ?? item.name ?? item.key ?? item.title ?? ''
                    const rawValue = item.value ?? item.val ?? item.content ?? ''
                    const label =
                        typeof rawLabel === 'string' || typeof rawLabel === 'number'
                            ? String(rawLabel).trim()
                            : ''
                    const value =
                        typeof rawValue === 'string' || typeof rawValue === 'number'
                            ? String(rawValue).trim()
                            : ''
                    if (!label || !value) return null
                    return { label, value }
                })
                .filter(Boolean)
        },
    },
    methods: {
        translateLabel(label) {
            return translateSpecLabel(label, (text) => this.tt(text))
        },
        translateValue(value) {
            return translateSpecValue(value, (text) => this.tt(text), this.isEn)
        },
    },
}
</script>
