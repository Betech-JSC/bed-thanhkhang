<template>
    <Link
        :href="productUrl"
        class="group flex flex-col w-full h-full bg-white border border-[#E3E8EF] rounded-[4px] overflow-hidden transition-all duration-200 hover:border-[#005EA4]/45 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] select-none"
    >
        <!-- Product Image Frame (Figma 979:30462: 262x185 -> aspect-[262/185]) -->
        <div class="relative w-full aspect-[262/185] bg-white overflow-hidden shrink-0">
            <img
                v-if="productImageSrc && !imageFailed"
                :src="productImageSrc"
                :alt="productImageAlt"
                width="262"
                height="185"
                class="w-full h-full object-contain p-2 sm:p-3 select-none transition-transform duration-300 ease-out group-hover:scale-[1.03]"
                loading="lazy"
                decoding="async"
                @error="onImageError"
            />
            <div
                v-else
                class="w-full h-full bg-[#EEF2F6] flex items-center justify-center p-4"
                aria-hidden="true"
            >
                <span class="w-10 h-[3px] rounded-[2px] bg-[#CDD5DF]" />
            </div>
        </div>

        <!-- Product Info & CTA (Figma I979:30462;754:22350) -->
        <div class="flex-1 flex flex-col justify-between bg-white">
            <!-- Title Container (Figma 754:22340: px 24px, pt 10px, min-h 98px desktop) -->
            <div class="px-3 pt-2.5 sm:px-6 sm:pt-[10px] min-h-[70px] sm:min-h-[98px]">
                <h3
                    class="text-[14px] leading-[20px] sm:text-[16px] sm:leading-[26px] font-bold text-[#00284B] font-sans line-clamp-3 break-words group-hover:text-[#005EA4] transition-colors"
                    :title="productTitle"
                >
                    {{ productTitle }}
                </h3>
            </div>

            <!-- Action Button Container (Figma 754:22349: p 24px, button h 38px) -->
            <div class="p-3 sm:p-6 pt-3 sm:pt-6">
                <span
                    class="w-full h-[38px] px-4 rounded-[4px] bg-[#005EA4] group-hover:bg-[#004D86] group-active:bg-[#003967] text-[#FAFAFA] text-[13px] sm:text-[14px] leading-[22px] font-medium font-sans inline-flex items-center justify-center transition-colors"
                >
                    {{ tt('Xem chi tiết') }}
                </span>
            </div>
        </div>
    </Link>
</template>

<script setup>
import { computed, getCurrentInstance, ref, watch } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { hasVietnameseDiacritics, useTranslate } from '@/composables/useTranslate.js'

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
})

const instance = getCurrentInstance()
const { translate, currentLocale, isEn } = useTranslate()

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

const productTitle = computed(() => {
    return String(props.product?.title || props.product?.name || '').trim()
})

const productUrl = computed(() => {
    const p = props.product
    if (!p || typeof p !== 'object') {
        return safeRoute('products.index', undefined, isEn.value ? '/en/products' : '/san-pham')
    }
    if (typeof p.url === 'string' && p.url.trim() !== '') {
        return p.url.trim()
    }
    if (p.url && typeof p.url === 'object') {
        const key = currentLocale.value.toUpperCase()
        if (p.url[key]) return p.url[key]
        const firstVal = Object.values(p.url)[0]
        if (firstVal) return firstVal
    }
    if (p.slug) {
        return safeRoute(
            'products.show',
            { slug: p.slug },
            `${isEn.value ? '/en/products' : '/san-pham'}/${encodeURIComponent(p.slug)}`
        )
    }
    if (p.id) {
        return safeRoute(
            'products.show',
            { slug: p.id },
            `${isEn.value ? '/en/products' : '/san-pham'}/${encodeURIComponent(p.id)}`
        )
    }
    return safeRoute('products.index', undefined, isEn.value ? '/en/products' : '/san-pham')
})

function normalizeImageUrl(url) {
    if (!url || typeof url !== 'string') return ''
    let trimmed = url.trim()
    if (!trimmed) return ''
    trimmed = trimmed.replace(/\/static\/\/?(assets\/|images\/)/g, '/$1').replace(/\/static\/\//g, '/')
    if (/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\/(assets|images)\//i.test(trimmed)) {
        return trimmed.replace(/^https?:\/\/[^/]+/i, '')
    }
    return trimmed
}

const productImageSrc = computed(() => {
    const p = props.product
    if (!p || typeof p !== 'object') return ''
    let raw = ''
    if (typeof p.image === 'string' && p.image) {
        raw = p.image
    } else if (p.image && typeof p.image === 'object') {
        raw = p.image.url || p.image.path || ''
    } else if (typeof p.thumbnail === 'string' && p.thumbnail) {
        raw = p.thumbnail
    }
    return normalizeImageUrl(raw)
})

const productImageAlt = computed(() => {
    const rawAlt =
        props.product?.image && typeof props.product.image === 'object'
            ? String(props.product.image.alt || '').trim()
            : ''
    const title = productTitle.value
    if (isEn.value && title) {
        if (!rawAlt || hasVietnameseDiacritics(rawAlt) || rawAlt !== title) {
            return title
        }
    }
    return rawAlt || title
})

const imageFailed = ref(false)
watch(productImageSrc, () => {
    imageFailed.value = false
})

function onImageError() {
    imageFailed.value = true
}
</script>
