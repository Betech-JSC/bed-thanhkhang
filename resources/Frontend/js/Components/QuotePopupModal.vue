<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-[2px] px-4 md:px-8 py-6 md:py-8 overflow-y-auto overscroll-contain"
                role="dialog"
                aria-modal="true"
                aria-labelledby="quote-popup-title"
                @click.self="handleClose"
            >
                <div
                    class="w-full max-w-full lg:max-w-quote-popup my-auto flex flex-col items-center gap-5"
                    @click.self="handleClose"
                >
                    <div
                        class="max-h-[90dvh] lg:max-h-none flex flex-col overflow-hidden relative w-full bg-dark rounded-cta shadow-2xl"
                    >
                        <!-- Mobile Close Button (Top-Right of Popup Card) -->
                        <button
                            type="button"
                            class="lg:hidden absolute top-3.5 right-3.5 z-30 w-9 h-9 rounded-btn text-white/90 hover:text-white hover:bg-white/10 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-white/80 transition-colors"
                            :aria-label="translate('Đóng')"
                            @click="handleClose"
                        >
                            <svg
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.75"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>

                        <!-- Decorative Chain Mask: Top-Right (Exact Figma 1264:49743) -->
                        <div
                            class="quote-chain-wrap quote-chain-wrap--top-right pointer-events-none select-none"
                            aria-hidden="true"
                        >
                            <div class="quote-chain-rot quote-chain-rot--top-right">
                                <div class="quote-chain-img quote-chain-img--top-right">
                                    <img
                                        src="/assets/figma/quote-popup/chain-texture.png"
                                        alt=""
                                        class="w-full h-full object-cover max-w-none"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Decorative Chain Mask: Bottom-Left (Exact Figma 1264:49746) -->
                        <div
                            class="quote-chain-wrap quote-chain-wrap--bottom-left pointer-events-none select-none"
                            aria-hidden="true"
                        >
                            <div class="quote-chain-rot quote-chain-rot--bottom-left">
                                <div class="quote-chain-img quote-chain-img--bottom-left">
                                    <img
                                        src="/assets/figma/quote-popup/chain-texture.png"
                                        alt=""
                                        class="w-full h-full object-cover max-w-none"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Scrollable Body Container (Mobile) / Flex Row (Desktop) -->
                        <div
                            class="relative z-10 flex-1 min-h-0 overflow-y-auto lg:overflow-visible overscroll-contain pt-12 px-5 pb-5 sm:pt-12 sm:px-6 sm:pb-6 lg:p-10 flex flex-col lg:flex-row gap-4 md:gap-6 items-stretch lg:items-center"
                        >
                            <!-- Left Column: Brand Value Proposition & 4 Assurances -->
                            <div class="relative z-10 min-w-0 flex flex-col lg:justify-start gap-5 md:gap-6 lg:gap-12 lg:w-[372px] lg:h-[508px] shrink-0">
                                <div class="flex flex-col gap-3">
                            <h2
                                id="quote-popup-title"
                                class="text-h2-mobile md:text-pc-h5 font-bold leading-[1.6] text-surface uppercase break-words"
                            >
                                {{ displayTitle }}
                            </h2>
                            <p class="text-body-sm font-medium leading-[1.6] text-ink-muted">
                                {{ displaySubtitle }}
                            </p>
                        </div>

                        <ul class="flex flex-col gap-3 md:gap-5">
                            <li
                                v-for="(item, index) in assuranceItems"
                                :key="index"
                                class="flex items-center gap-3 min-w-0"
                            >
                                <span
                                    class="w-8 h-8 rounded-btn bg-surface-100 flex items-center justify-center shrink-0"
                                >
                                    <img
                                        :src="item.icon"
                                        alt=""
                                        class="w-5 h-5 object-contain"
                                        aria-hidden="true"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                </span>
                                <span class="text-body-sm font-bold leading-[1.6] text-surface-100">
                                    {{ translate(item.label) }}
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- Right Column: Interactive Quote Form -->
                    <div
                        class="relative z-10 min-w-0 max-w-full flex-1 bg-surface-200 backdrop-blur-[3px] rounded-btn p-5 sm:p-6 lg:p-10 flex flex-col justify-between gap-4 md:gap-5"
                    >
                        <!-- Success View -->
                        <div
                            v-if="isSubmitted"
                            class="flex flex-col items-center justify-center text-center py-8 sm:py-12 gap-4"
                        >
                            <div
                                class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center"
                            >
                                <img
                                    src="/assets/figma/quote-popup/icon-check-circle.svg"
                                    alt=""
                                    class="w-7 h-7 object-contain"
                                    aria-hidden="true"
                                />
                            </div>
                            <h3 class="text-h3-mobile md:text-h3 font-bold text-ink-heading">
                                {{ translate('Gửi yêu cầu báo giá thành công') }}
                            </h3>
                            <p class="text-body-sm text-ink-body max-w-md leading-[1.6]">
                                {{ translate('Cảm ơn quý khách đã liên hệ Thành Khang. Đội ngũ kỹ thuật và kinh doanh của chúng tôi sẽ phản hồi báo giá trong thời gian sớm nhất.') }}
                            </p>
                            <button
                                type="button"
                                class="mt-2 bg-primary hover:bg-primary-hover active:bg-primary-active text-surface font-medium text-body-md leading-[1.6] px-6 py-2.5 rounded-cta transition-colors"
                                @click="handleClose"
                            >
                                {{ translate('Đóng cửa sổ') }}
                            </button>
                        </div>

                        <!-- Form View -->
                        <form id="quote-popup-form" v-else class="flex flex-col gap-4 md:gap-5" novalidate @submit.prevent="handleSubmit">
                            <div class="flex flex-col gap-3 md:gap-4">
                                <!-- Row 1: Full Name & Company -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            for="quote-popup-name"
                                            class="text-body-sm font-medium text-field-label"
                                        >
                                            {{ translate('Họ và tên') }}<span class="text-field-required">*</span>
                                        </label>
                                        <input
                                            id="quote-popup-name"
                                            v-model.trim="form.name"
                                            type="text"
                                            autocomplete="name"
                                            required
                                            :placeholder="translate('Nguyễn Văn A')"
                                            :aria-invalid="Boolean(errors.name)"
                                            class="w-full text-base bg-surface-white border border-field-border rounded-btn shadow-xs px-3 py-2 text-body-md text-ink-dark placeholder:text-field-placeholder focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                            :class="{ 'border-error focus:border-error focus:ring-error/20': errors.name }"
                                            @input="clearError('name')"
                                        />
                                        <p v-if="errors.name" class="text-body-xs text-error">
                                            {{ errors.name }}
                                        </p>
                                    </div>

                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            for="quote-popup-company"
                                            class="text-body-sm font-medium text-field-label"
                                        >
                                            {{ translate('Tên công ty/ Đơn vị') }}
                                        </label>
                                        <input
                                            id="quote-popup-company"
                                            v-model.trim="form.company"
                                            type="text"
                                            autocomplete="organization"
                                            :placeholder="translate('Công ty TNHH ABC')"
                                            class="w-full text-base bg-surface-white border border-field-border rounded-btn shadow-xs px-3 py-2 text-body-md text-ink-dark placeholder:text-field-placeholder focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                        />
                                    </div>
                                </div>

                                <!-- Row 2: Email & Phone -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            for="quote-popup-email"
                                            class="text-body-sm font-medium text-field-label"
                                        >
                                            {{ translate('Email') }}<span class="text-field-required">*</span>
                                        </label>
                                        <input
                                            id="quote-popup-email"
                                            v-model.trim="form.email"
                                            type="email"
                                            autocomplete="email"
                                            required
                                            :placeholder="translate('ten@congty.com')"
                                            :aria-invalid="Boolean(errors.email)"
                                            class="w-full text-base bg-surface-white border border-field-border rounded-btn shadow-xs px-3 py-2 text-body-md text-ink-dark placeholder:text-field-placeholder focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                            :class="{ 'border-error focus:border-error focus:ring-error/20': errors.email }"
                                            @input="clearError('email')"
                                        />
                                        <p v-if="errors.email" class="text-body-xs text-error">
                                            {{ errors.email }}
                                        </p>
                                    </div>

                                    <div class="flex flex-col gap-1.5">
                                        <label
                                            for="quote-popup-phone"
                                            class="text-body-sm font-medium text-field-label"
                                        >
                                            {{ translate('Số điện thoại') }}<span class="text-field-required">*</span>
                                        </label>
                                        <input
                                            id="quote-popup-phone"
                                            v-model.trim="form.phone"
                                            type="tel"
                                            autocomplete="tel"
                                            required
                                            :placeholder="translate('0901 234 567')"
                                            :aria-invalid="Boolean(errors.phone)"
                                            class="w-full text-base bg-surface-white border border-field-border rounded-btn shadow-xs px-3 py-2 text-body-md text-ink-dark placeholder:text-field-placeholder focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                            :class="{ 'border-error focus:border-error focus:ring-error/20': errors.phone }"
                                            @input="clearError('phone')"
                                        />
                                        <p v-if="errors.phone" class="text-body-xs text-error">
                                            {{ errors.phone }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Row 3: Note / Message -->
                                <div class="flex flex-col gap-1.5">
                                    <label
                                        for="quote-popup-note"
                                        class="text-body-sm font-medium text-field-label"
                                    >
                                        {{ translate('Lời nhắn') }}
                                    </label>
                                    <textarea
                                        id="quote-popup-note"
                                        v-model="form.note"
                                        :placeholder="translate('Nhập nội dung bạn cần báo giá (sản phẩm, số lượng, thời gian cần hàng...)')"
                                        class="w-full min-h-[140px] lg:min-h-[154px] resize-y text-base bg-surface-white border border-field-border rounded-btn shadow-xs px-3 py-2 text-body-md text-ink-dark placeholder:text-field-placeholder focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Server Error Alert -->
                            <div
                                v-if="serverError"
                                class="rounded-btn bg-error-50 border border-error-200 px-3.5 py-2.5 text-body-sm text-error-700"
                                role="alert"
                            >
                                {{ serverError }}
                            </div>

                            <!-- Submit CTA (Desktop In-Form) -->
                            <button
                                type="submit"
                                :disabled="isLoading"
                                class="hidden lg:flex w-full bg-primary hover:bg-primary-hover active:bg-primary-active disabled:opacity-60 disabled:cursor-not-allowed text-surface font-medium text-body-md leading-[1.6] px-6 py-2.5 rounded-cta transition-colors items-center justify-center gap-2"
                            >
                                <span
                                    v-if="isLoading"
                                    class="inline-block w-4 h-4 border-2 border-surface/30 border-t-surface rounded-full animate-spin"
                                    aria-hidden="true"
                                ></span>
                                <span>{{ isLoading ? translate('Đang gửi yêu cầu...') : translate('Nhận tư vấn ngay') }}</span>
                            </button>
                        </form>
                    </div>
                        </div>

                        <!-- Sticky Mobile Footer CTA Bar -->
                        <div
                            v-if="!isSubmitted"
                            class="lg:hidden relative z-20 shrink-0 bg-surface-200 px-5 sm:px-6 py-4 shadow-[0_-4px_12px_rgba(0,0,0,0.12)]"
                        >
                            <button
                                type="submit"
                                form="quote-popup-form"
                                :disabled="isLoading"
                                class="w-full bg-primary hover:bg-primary-hover active:bg-primary-active disabled:opacity-60 disabled:cursor-not-allowed text-surface font-medium text-body-md leading-[1.6] px-6 py-2.5 rounded-cta transition-colors flex items-center justify-center gap-2"
                            >
                                <span
                                    v-if="isLoading"
                                    class="inline-block w-4 h-4 border-2 border-surface/30 border-t-surface rounded-full animate-spin"
                                    aria-hidden="true"
                                ></span>
                                <span>{{ isLoading ? translate('Đang gửi yêu cầu...') : translate('Nhận tư vấn ngay') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Close Button (Centered Below Modal - Desktop Only) -->
                    <button
                        type="button"
                        class="hidden lg:flex w-11 h-11 rounded-full border border-white/90 bg-dark/90 hover:bg-dark active:bg-primary shadow-lg text-white items-center justify-center focus:outline-none focus:ring-2 focus:ring-white/80 transition-colors shrink-0"
                        :aria-label="translate('Đóng')"
                        @click="handleClose"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { computed, getCurrentInstance, nextTick, onBeforeUnmount, reactive, ref, watch } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { useQuoteModal } from '@/composables/useQuoteModal'

const props = defineProps({
    quoteContent: {
        type: Object,
        default: null,
    },
})

const instance = getCurrentInstance()
const page = usePage()

const translate = (text) => {
    const globalTt = instance?.appContext?.config?.globalProperties?.tt
    if (typeof globalTt === 'function') {
        return globalTt.call(instance.proxy, text)
    }
    const proxyTt = instance?.proxy?.tt
    if (typeof proxyTt === 'function') {
        return proxyTt.call(instance.proxy, text)
    }
    return text
}

const pageProps = computed(() => {
    const raw = page?.props
    if (!raw) return {}
    return raw.value !== undefined ? raw.value || {} : raw
})

const currentLocale = computed(() => {
    const locale = pageProps.value?.locale
    if (typeof locale === 'string') return locale
    return locale?.current || 'vi'
})

const siteConfig = computed(() => ({
    ...(pageProps.value?.global || {}),
    ...(pageProps.value?.configs || {}),
}))

const getLocalizedConfig = (key) => {
    const cfg = siteConfig.value
    if (currentLocale.value === 'en') {
        return cfg[`${key}_en`] || cfg[key] || null
    }
    return cfg[key] || null
}

const DEFAULT_ASSURANCE_ITEMS = [
    {
        icon: '/assets/figma/quote-popup/icon-check-circle.svg',
        label: 'Giá gốc nhà máy ( Không trung gian)',
    },
    {
        icon: '/assets/figma/quote-popup/icon-truck.svg',
        label: 'Giao hàng tận chân công trình',
    },
    {
        icon: '/assets/figma/quote-popup/icon-file-check.svg',
        label: 'Đầy đủ CO/CQ, Hoá đơn VAT',
    },
    {
        icon: '/assets/figma/quote-popup/icon-coins-hand.svg',
        label: 'Chiết khấu cao cho nhà thầu',
    },
]

const displayTitle = computed(() => {
    return (
        props.quoteContent?.title ||
        getLocalizedConfig('quote_popup_title') ||
        translate('NHẬN BÁO GIÁ CÁC SẢN PHẨM TẠI THÀNH KHANG')
    )
})

const displaySubtitle = computed(() => {
    return (
        props.quoteContent?.subtitle ||
        getLocalizedConfig('quote_popup_subtitle') ||
        translate('Để lại thông tin để nhận báo giá xích công nghiệp chính hãng, đủ kích thước, giao hàng nhanh trên toàn quốc')
    )
})

const normalizeAssurances = (rawList) => {
    if (!Array.isArray(rawList) || rawList.length === 0) return null
    const normalized = rawList
        .map((item, idx) => {
            const fallback = DEFAULT_ASSURANCE_ITEMS[idx % DEFAULT_ASSURANCE_ITEMS.length]
            if (typeof item === 'string') {
                const label = item.trim()
                return label ? { icon: fallback.icon, label } : null
            }
            if (item && typeof item === 'object') {
                const label = String(item.label || item.title || item.text || '').trim()
                if (!label) return null
                return {
                    icon: item.icon || fallback.icon,
                    label,
                }
            }
            return null
        })
        .filter(Boolean)
    return normalized.length > 0 ? normalized : null
}

const assuranceItems = computed(() => {
    const fromProp = normalizeAssurances(props.quoteContent?.assurances)
    if (fromProp) return fromProp

    const rawConfigAssurances = getLocalizedConfig('quote_popup_assurances')
    const parsedConfigAssurances =
        typeof rawConfigAssurances === 'string'
            ? (() => {
                  try {
                      return JSON.parse(rawConfigAssurances)
                  } catch (e) {
                      return null
                  }
              })()
            : rawConfigAssurances
    const fromConfig = normalizeAssurances(parsedConfigAssurances)
    if (fromConfig) return fromConfig

    return DEFAULT_ASSURANCE_ITEMS
})

const resolveContactUrl = () => {
    const proxy = instance?.proxy
    if (proxy && typeof proxy.route === 'function') {
        const resolved = proxy.route('contact.store')
        if (resolved && resolved !== '#') return resolved
    }
    if (typeof window !== 'undefined' && typeof window.route === 'function') {
        try {
            const resolved = window.route('contact.store')
            if (resolved && resolved !== '#') return resolved
        } catch (e) {
            // fallback to default endpoint
        }
    }
    return '/contacts'
}

const { isOpen, initialContext, closeQuoteModal } = useQuoteModal()

const form = reactive({
    name: '',
    company: '',
    email: '',
    phone: '',
    note: '',
})

const errors = reactive({
    name: '',
    email: '',
    phone: '',
})

const isLoading = ref(false)
const isSubmitted = ref(false)
const serverError = ref('')

const clearError = (field) => {
    if (errors[field]) {
        errors[field] = ''
    }
    if (serverError.value) {
        serverError.value = ''
    }
}

const resetForm = () => {
    form.name = ''
    form.company = ''
    form.email = ''
    form.phone = ''
    form.note = ''
    errors.name = ''
    errors.email = ''
    errors.phone = ''
    serverError.value = ''
    isSubmitted.value = false
    isLoading.value = false
}

const validateForm = () => {
    errors.name = ''
    errors.email = ''
    errors.phone = ''
    serverError.value = ''

    let isValid = true

    if (!form.name || form.name.trim().length < 2) {
        errors.name = translate('Vui lòng nhập họ và tên.')
        isValid = false
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!form.email || !emailRegex.test(form.email.trim())) {
        errors.email = translate('Vui lòng nhập địa chỉ email hợp lệ.')
        isValid = false
    }

    const digitsOnly = (form.phone || '').replace(/[\s\-().+]/g, '')
    if (!digitsOnly || !/^\d{8,15}$/.test(digitsOnly)) {
        errors.phone = translate('Vui lòng nhập số điện thoại hợp lệ (8–15 chữ số).')
        isValid = false
    }

    return isValid
}

const handleSubmit = async () => {
    if (isLoading.value) return
    if (!validateForm()) {
        await nextTick()
        if (typeof document !== 'undefined') {
            document
                .querySelector('[aria-invalid="true"]')
                ?.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }
        return
    }

    isLoading.value = true
    serverError.value = ''

    const payload = {
        type: 'CONTACT_FORM',
        name: form.name.trim(),
        email: form.email.trim(),
        phone: form.phone.trim(),
        note: (form.note || '').trim(),
        data: {
            'Họ và tên': form.name.trim(),
            'Tên công ty/ Đơn vị': form.company.trim(),
            Email: form.email.trim(),
            'Số điện thoại': form.phone.trim(),
            'Lời nhắn': (form.note || '').trim(),
            form_source: initialContext.value?.source || 'quote_popup_modal',
            lead_type: 'quote',
            product_context: initialContext.value,
        },
    }

    try {
        const endpoint = resolveContactUrl()
        if (typeof window !== 'undefined' && window.axios) {
            await window.axios.post(endpoint, payload, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
        } else {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            })
            if (!response.ok) {
                throw new Error('Request failed')
            }
        }
        isSubmitted.value = true
    } catch (error) {
        const responseData = error?.response?.data
        if (responseData?.errors && typeof responseData.errors === 'object') {
            if (responseData.errors.name?.[0]) errors.name = responseData.errors.name[0]
            if (responseData.errors.email?.[0]) errors.email = responseData.errors.email[0]
            if (responseData.errors.phone?.[0]) errors.phone = responseData.errors.phone[0]
            serverError.value =
                responseData.message || translate('Vui lòng kiểm tra lại thông tin đã nhập.')
        } else {
            serverError.value =
                responseData?.message ||
                translate('Không thể gửi yêu cầu lúc này. Vui lòng thử lại sau hoặc gọi hotline để được hỗ trợ ngay.')
        }
    } finally {
        isLoading.value = false
    }
}

const handleClose = () => {
    closeQuoteModal()
}

const handleKeydown = (event) => {
    if (event.key === 'Escape' && isOpen.value) {
        handleClose()
    }
}

const syncBodyScrollLock = (locked) => {
    if (typeof document === 'undefined') return
    const shouldLock = Boolean(locked)
    document.documentElement.classList.toggle('overflow-hidden', shouldLock)
    document.body.classList.toggle('overflow-hidden', shouldLock)
}

watch(isOpen, (open) => {
    syncBodyScrollLock(open)
    if (typeof window !== 'undefined') {
        if (open) {
            window.addEventListener('keydown', handleKeydown)
            if (isSubmitted.value) {
                resetForm()
            }
        } else {
            window.removeEventListener('keydown', handleKeydown)
        }
    }
})

onBeforeUnmount(() => {
    syncBodyScrollLock(false)
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', handleKeydown)
    }
})
</script>

<style scoped>
.quote-chain-wrap {
    position: absolute;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 0;
    opacity: 1;
}

.quote-chain-rot {
    flex: none;
}

.quote-chain-img {
    position: relative;
    mask-image: url('/assets/figma/quote-popup/chain-mask.svg');
    -webkit-mask-image: url('/assets/figma/quote-popup/chain-mask.svg');
    mask-repeat: no-repeat;
    -webkit-mask-repeat: no-repeat;
}

/* Mobile: smaller & tucked to edges so they don't cross heading/text */
.quote-chain-wrap--top-right {
    width: 340px;
    height: 202px;
    right: -135px;
    top: -102px;
    opacity: 0.45;
}
.quote-chain-rot--top-right {
    transform: rotate(161.81deg);
}
.quote-chain-img--top-right {
    width: 321px;
    height: 107px;
    mask-position: -142px 60px;
    -webkit-mask-position: -142px 60px;
    mask-size: 531px 126px;
    -webkit-mask-size: 531px 126px;
}

.quote-chain-wrap--bottom-left {
    width: 420px;
    height: 252px;
    left: -90px;
    bottom: -120px;
    opacity: 0.65;
}
.quote-chain-rot--bottom-left {
    transform: rotate(161.64deg);
}
.quote-chain-img--bottom-left {
    width: 399px;
    height: 133px;
    mask-position: -130px 41px;
    -webkit-mask-position: -130px 41px;
    mask-size: 542px 185px;
    -webkit-mask-size: 542px 185px;
}

/* Desktop (>=1024px): Exact Figma 1264:49742 geometry & full opacity */
@media (min-width: 1024px) {
    .quote-chain-wrap {
        display: flex;
        opacity: 1;
    }

    .quote-chain-wrap--top-right {
        width: 676.876px;
        height: 403.822px;
        left: 697px;
        right: auto;
        top: -168px;
    }
    .quote-chain-img--top-right {
        width: 642.146px;
        height: 214.049px;
        mask-position: -284.999px 121px;
        -webkit-mask-position: -284.999px 121px;
        mask-size: 1062px 253px;
        -webkit-mask-size: 1062px 253px;
    }

    .quote-chain-wrap--bottom-left {
        width: 841.165px;
        height: 503.795px;
        left: 104.64px;
        top: 240.78px;
        bottom: auto;
    }
    .quote-chain-img--bottom-left {
        width: 798px;
        height: 266px;
        mask-position: -259.644px 81.216px;
        -webkit-mask-position: -259.644px 81.216px;
        mask-size: 1083.714px 370.818px;
        -webkit-mask-size: 1083.714px 370.818px;
    }
}
</style>
