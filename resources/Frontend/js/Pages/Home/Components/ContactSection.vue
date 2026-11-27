<template>
    <section
        id="contact-section"
        class="relative w-full bg-[#00284B] overflow-hidden py-10 md:py-16 lg:pt-[125px] lg:pb-[124px]"
    >
        <!-- Mobile Top-Right Decorative Chain Accent (Figma 1193:6560) -->
        <div
            class="md:hidden pointer-events-none select-none absolute -right-[20px] top-0 w-[260px] z-0 opacity-85"
            aria-hidden="true"
        >
            <img
                src="/assets/images/homepage/contact-chain-deco.png"
                alt=""
                class="w-full h-auto block"
                loading="lazy"
                decoding="async"
            />
        </div>

        <!-- Decorative Chain Accent 1: Bottom-Left (Figma 1171:41664) -->
        <div
            class="hidden md:block pointer-events-none select-none absolute -left-[120px] -bottom-[155px] w-[560px] -rotate-[6deg] z-0 opacity-95"
            aria-hidden="true"
        >
            <img
                src="/assets/images/homepage/contact-chain-deco.png"
                alt=""
                class="w-full h-auto block"
                loading="lazy"
                decoding="async"
            />
        </div>

        <!-- Decorative Chain Accent 2: Top-Right (sweeping diagonally behind form card) -->
        <div
            class="hidden lg:block pointer-events-none select-none absolute -right-[120px] -top-[55px] w-[760px] rotate-[24deg] z-0 opacity-95"
            aria-hidden="true"
        >
            <img
                src="/assets/images/homepage/contact-chain-deco.png"
                alt=""
                class="w-full h-auto block"
                loading="lazy"
                decoding="async"
            />
        </div>

        <!-- Decorative Chain Accent 3: Bottom-Right -->
        <div
            class="hidden lg:block pointer-events-none select-none absolute -right-[130px] -bottom-[85px] w-[620px] -rotate-[18deg] z-0 opacity-95"
            aria-hidden="true"
        >
            <img
                src="/assets/images/homepage/contact-chain-deco.png"
                alt=""
                class="w-full h-auto block"
                loading="lazy"
                decoding="async"
            />
        </div>

        <!-- Main Content Grid (Figma 1171:41662 — 1152px narrow container: Left 462px + 63px gap + Form 627px = 1152px) -->
        <div class="app-container-narrow relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-stretch justify-between gap-7 lg:gap-[63px]">
                <!-- Left Column: Title, Subtitle & 4 B2B Assurance Badges (Figma 1171:41666 & 1193:6489) -->
                <div
                    class="w-full lg:w-[462px] lg:min-h-[508px] shrink-0 flex flex-col justify-between gap-5 lg:gap-8"
                >
                    <div v-if="heading || description">
                        <h2
                            v-if="heading"
                            class="font-bold text-[20px] sm:text-[24px] lg:text-[28px] leading-[28px] lg:leading-[1.6] lg:min-h-[100px] text-[#FAFAFA] uppercase break-words"
                        >
                            {{ heading }}
                        </h2>
                        <p
                            v-if="description"
                            class="font-normal lg:font-medium text-[14px] sm:text-[15px] lg:text-[16px] leading-[1.6] text-[#9AA4B2] mt-2.5 lg:mt-0 break-words"
                        >
                            {{ description }}
                        </p>
                    </div>

                    <!-- 4 B2B Assurance Badges (Figma 1171:41676 & 1193:6493 — CMS dynamic override or i18n keys, zero static script arrays) -->
                    <div
                        v-if="dynamicBadges.length > 0"
                        class="flex flex-col gap-3 lg:gap-5"
                    >
                        <div
                            v-for="(badge, idx) in dynamicBadges"
                            :key="badge.id || idx"
                            class="flex items-center gap-3 lg:gap-4"
                        >
                            <span
                                class="w-9 h-9 lg:w-10 lg:h-10 rounded-[6px] lg:rounded-[8px] bg-[#FAFAFA] lg:bg-[#EEF2F6] flex items-center justify-center shrink-0"
                                aria-hidden="true"
                            >
                                <img
                                    v-if="badge.icon"
                                    :src="badge.icon"
                                    alt=""
                                    width="20"
                                    height="20"
                                    class="w-5 h-5 object-contain"
                                />
                            </span>
                            <span class="font-bold text-[14px] lg:text-[16px] leading-[1.6] text-[#FAFAFA] lg:text-[#EEF2F6] break-words">
                                {{ badge.title || badge.text }}
                            </span>
                        </div>
                    </div>

                    <div v-else class="flex flex-col gap-3 lg:gap-5">
                        <div class="flex items-center gap-3 lg:gap-4">
                            <span
                                class="w-9 h-9 lg:w-10 lg:h-10 rounded-[6px] lg:rounded-[8px] bg-[#FAFAFA] lg:bg-[#EEF2F6] flex items-center justify-center shrink-0"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/homepage/contact-assurance-factory.svg"
                                    alt=""
                                    width="20"
                                    height="20"
                                    class="w-5 h-5 object-contain"
                                />
                            </span>
                            <span class="font-bold text-[14px] lg:text-[16px] leading-[1.6] text-[#FAFAFA] lg:text-[#EEF2F6] break-words">
                                {{ t('Giá gốc nhà máy (Không trung gian)') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 lg:gap-4">
                            <span
                                class="w-9 h-9 lg:w-10 lg:h-10 rounded-[6px] lg:rounded-[8px] bg-[#FAFAFA] lg:bg-[#EEF2F6] flex items-center justify-center shrink-0"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/homepage/contact-assurance-delivery.svg"
                                    alt=""
                                    width="20"
                                    height="20"
                                    class="w-5 h-5 object-contain"
                                />
                            </span>
                            <span class="font-bold text-[14px] lg:text-[16px] leading-[1.6] text-[#FAFAFA] lg:text-[#EEF2F6] break-words">
                                {{ t('Giao hàng tận chân công trình') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 lg:gap-4">
                            <span
                                class="w-9 h-9 lg:w-10 lg:h-10 rounded-[6px] lg:rounded-[8px] bg-[#FAFAFA] lg:bg-[#EEF2F6] flex items-center justify-center shrink-0"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/homepage/contact-assurance-cocq.svg"
                                    alt=""
                                    width="20"
                                    height="20"
                                    class="w-5 h-5 object-contain"
                                />
                            </span>
                            <span class="font-bold text-[14px] lg:text-[16px] leading-[1.6] text-[#FAFAFA] lg:text-[#EEF2F6] break-words">
                                {{ t('Đầy đủ CO/CQ, Hoá đơn VAT') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 lg:gap-4">
                            <span
                                class="w-9 h-9 lg:w-10 lg:h-10 rounded-[6px] lg:rounded-[8px] bg-[#FAFAFA] lg:bg-[#EEF2F6] flex items-center justify-center shrink-0"
                                aria-hidden="true"
                            >
                                <img
                                    src="/assets/svg/homepage/contact-assurance-discount.svg"
                                    alt=""
                                    width="20"
                                    height="20"
                                    class="w-5 h-5 object-contain"
                                />
                            </span>
                            <span class="font-bold text-[14px] lg:text-[16px] leading-[1.6] text-[#FAFAFA] lg:text-[#EEF2F6] break-words">
                                {{ t('Chiết khấu cao cho nhà thầu') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Quote Form Card (Figma 1264:48218 627x508 bg-[#E3E8EF] backdrop-blur-[3px]) -->
                <div
                    class="w-full lg:w-[627px] lg:min-h-[508px] min-w-0 backdrop-blur-[3px] bg-[#E3E8EF] rounded-[8px] px-4 py-5 sm:p-6 lg:p-[40px] flex flex-col justify-between"
                >
                    <form
                        @submit.prevent="submitForm"
                        novalidate
                        class="flex flex-col justify-between h-full gap-5 lg:gap-[31px]"
                    >
                        <div class="flex flex-col gap-[14px] lg:gap-[16px]">
                            <!-- Row 1: Full Name* + Company / Organization (Figma 1264:48220) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-[14px] sm:gap-[12px]">
                                <div>
                                    <label
                                        for="home-contact-name"
                                        class="block text-[14px] font-medium leading-[1.6] text-[#4B5565] mb-[4px]"
                                    >
                                        {{ isEn ? 'Full Name' : 'Họ và tên' }}<span class="text-[#FF4749]">*</span>
                                    </label>
                                    <input
                                        id="home-contact-name"
                                        v-model="form.name"
                                        type="text"
                                        name="name"
                                        autocomplete="name"
                                        required
                                        :placeholder="isEn ? 'John Doe' : 'Nguyễn Văn A'"
                                        :aria-invalid="Boolean(fieldErrors.name)"
                                        :aria-describedby="fieldErrors.name ? 'home-contact-name-error' : undefined"
                                        class="w-full h-[42px] bg-white border rounded-[8px] px-[12px] py-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] text-[16px] font-normal leading-[1.6] text-[#1A1A1A] placeholder:text-[#808080] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-colors"
                                        :class="fieldErrors.name ? 'border-[#D92D20]' : 'border-[#D6D6D6]'"
                                        @input="clearFieldError('name')"
                                    />
                                    <p
                                        v-if="fieldErrors.name"
                                        id="home-contact-name-error"
                                        role="alert"
                                        class="text-[13px] font-medium text-[#B42318] mt-1"
                                    >
                                        {{ fieldErrors.name }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="home-contact-company"
                                        class="block text-[14px] font-medium leading-[1.6] text-[#4B5565] mb-[4px]"
                                    >
                                        {{ t('Tên công ty/ Đơn vị') }}
                                    </label>
                                    <input
                                        id="home-contact-company"
                                        v-model="form.company"
                                        type="text"
                                        name="company"
                                        autocomplete="organization"
                                        :placeholder="t('Công ty TNHH ABC')"
                                        class="w-full h-[42px] bg-white border border-[#D6D6D6] rounded-[8px] px-[12px] py-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] text-[16px] font-normal leading-[1.6] text-[#1A1A1A] placeholder:text-[#808080] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-colors"
                                    />
                                </div>
                            </div>

                            <!-- Row 2: Email* + Phone* (Figma 1264:48223) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-[14px] sm:gap-[12px]">
                                <div>
                                    <label
                                        for="home-contact-email"
                                        class="block text-[14px] font-medium leading-[1.6] text-[#4B5565] mb-[4px]"
                                    >
                                        Email<span class="text-[#FF4749]">*</span>
                                    </label>
                                    <input
                                        id="home-contact-email"
                                        v-model="form.email"
                                        type="email"
                                        name="email"
                                        autocomplete="email"
                                        required
                                        :placeholder="isEn ? 'name@company.com' : 'ten@congty.com'"
                                        :aria-invalid="Boolean(fieldErrors.email)"
                                        :aria-describedby="fieldErrors.email ? 'home-contact-email-error' : undefined"
                                        class="w-full h-[42px] bg-white border rounded-[8px] px-[12px] py-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] text-[16px] font-normal leading-[1.6] text-[#1A1A1A] placeholder:text-[#808080] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-colors"
                                        :class="fieldErrors.email ? 'border-[#D92D20]' : 'border-[#D6D6D6]'"
                                        @input="clearFieldError('email')"
                                    />
                                    <p
                                        v-if="fieldErrors.email"
                                        id="home-contact-email-error"
                                        role="alert"
                                        class="text-[13px] font-medium text-[#B42318] mt-1"
                                    >
                                        {{ fieldErrors.email }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="home-contact-phone"
                                        class="block text-[14px] font-medium leading-[1.6] text-[#4B5565] mb-[4px]"
                                    >
                                        {{ isEn ? 'Phone Number' : 'Số điện thoại' }}<span class="text-[#FF4749]">*</span>
                                    </label>
                                    <input
                                        id="home-contact-phone"
                                        v-model="form.phone"
                                        type="tel"
                                        name="phone"
                                        autocomplete="tel"
                                        required
                                        :placeholder="isEn ? '(+84) 901 234 567' : '0901 234 567'"
                                        :aria-invalid="Boolean(fieldErrors.phone)"
                                        :aria-describedby="fieldErrors.phone ? 'home-contact-phone-error' : undefined"
                                        class="w-full h-[42px] bg-white border rounded-[8px] px-[12px] py-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] text-[16px] font-normal leading-[1.6] text-[#1A1A1A] placeholder:text-[#808080] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-colors"
                                        :class="fieldErrors.phone ? 'border-[#D92D20]' : 'border-[#D6D6D6]'"
                                        @input="clearFieldError('phone')"
                                    />
                                    <p
                                        v-if="fieldErrors.phone"
                                        id="home-contact-phone-error"
                                        role="alert"
                                        class="text-[13px] font-medium text-[#B42318] mt-1"
                                    >
                                        {{ fieldErrors.phone }}
                                    </p>
                                </div>
                            </div>

                            <!-- Row 3: Message / Note (Figma 1264:48226 h-[152px] px-[14px] py-[10px]) -->
                            <div>
                                <label
                                    for="home-contact-note"
                                    class="block text-[14px] font-medium leading-[1.6] text-[#424242] mb-[4px] lg:mb-[6px]"
                                >
                                    {{ isEn ? 'Message' : 'Lời nhắn' }}
                                </label>
                                <textarea
                                    id="home-contact-note"
                                    v-model="form.note"
                                    name="note"
                                    rows="4"
                                    :placeholder="
                                        isEn
                                            ? 'Enter your quotation details (product, quantity, required delivery time...)'
                                            : 'Nhập nội dung bạn cần báo giá (sản phẩm, số lượng, thời gian cần hàng...)'
                                    "
                                    :aria-invalid="Boolean(fieldErrors.note)"
                                    :aria-describedby="fieldErrors.note ? 'home-contact-note-error' : undefined"
                                    class="block w-full h-[120px] lg:h-[152px] resize-y bg-white border rounded-[8px] px-[14px] py-[10px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] text-[16px] font-normal leading-[1.6] text-[#1A1A1A] placeholder:text-[#808080] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-colors"
                                    :class="fieldErrors.note ? 'border-[#D92D20]' : 'border-[#D6D6D6]'"
                                    @input="clearFieldError('note')"
                                />
                                <p
                                    v-if="fieldErrors.note"
                                    id="home-contact-note-error"
                                    role="alert"
                                    class="text-[13px] font-medium text-[#B42318] mt-1"
                                >
                                    {{ fieldErrors.note }}
                                </p>
                            </div>
                        </div>

                        <!-- Feedback Banners -->
                        <div
                            v-if="submitSuccess"
                            role="status"
                            aria-live="polite"
                            class="rounded-[6px] bg-[#ECFDF3] border border-[#ABEFC6] px-4 py-3 text-[14px] font-medium text-[#067647]"
                        >
                            {{
                                isEn
                                    ? 'Thank you! Your consultation request has been received. Our engineering team will contact you shortly.'
                                    : 'Cảm ơn bạn! Yêu cầu tư vấn và báo giá đã được ghi nhận. Đội ngũ kỹ sư Thành Khang sẽ liên hệ với bạn sớm nhất.'
                            }}
                        </div>

                        <div
                            v-if="submitError"
                            role="alert"
                            aria-live="assertive"
                            class="rounded-[6px] bg-[#FEF3F2] border border-[#FECDCA] px-4 py-3 text-[14px] font-medium text-[#B42318]"
                        >
                            {{ submitError }}
                        </div>

                        <!-- Full-Width Primary Submit Button (Figma 1264:48227 547x49, 18px/1.6 "Liên hệ ngay") -->
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full h-[49px] bg-[#005EA4] hover:bg-[#004c86] active:bg-[#003d6d] text-[#FAFAFA] font-medium text-[14px] lg:text-[18px] leading-[1.6] px-[24px] py-[10px] rounded-[4px] inline-flex items-center justify-center gap-2 transition-colors disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                        >
                            <span>
                                {{
                                    isSubmitting
                                        ? t('Đang gửi...')
                                        : t('Liên hệ ngay')
                                }}
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance, reactive, ref } from 'vue'
import { useTranslate } from '@/composables/useTranslate.js'

const props = defineProps({
    contactSection: {
        type: Object,
        default: () => null,
    },
})

const instance = getCurrentInstance()
const { translate: t, isEn } = useTranslate()

const heading = computed(() => (props.contactSection?.title || '').trim())

const description = computed(() => (props.contactSection?.description || '').trim())

const dynamicBadges = computed(() => {
    const list = props.contactSection?.badges
    if (!Array.isArray(list)) return []
    return list.filter((item) => item && typeof item === 'object' && (item.title || item.text))
})

const form = reactive({
    name: '',
    company: '',
    email: '',
    phone: '',
    note: '',
})

const fieldErrors = reactive({
    name: '',
    email: '',
    phone: '',
    note: '',
})

const isSubmitting = ref(false)
const submitSuccess = ref(false)
const submitError = ref('')

function clearFieldError(field) {
    if (fieldErrors[field]) {
        fieldErrors[field] = ''
    }
    if (submitError.value) {
        submitError.value = ''
    }
}

function resetErrors() {
    fieldErrors.name = ''
    fieldErrors.email = ''
    fieldErrors.phone = ''
    fieldErrors.note = ''
    submitError.value = ''
}

function validateClient() {
    resetErrors()
    let valid = true

    const trimmedName = form.name.trim()
    const trimmedEmail = form.email.trim()
    const trimmedPhone = form.phone.trim()

    if (!trimmedName) {
        fieldErrors.name = isEn.value ? 'Please enter your full name.' : 'Vui lòng nhập họ và tên.'
        valid = false
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!trimmedEmail) {
        fieldErrors.email = isEn.value ? 'Please enter your email address.' : 'Vui lòng nhập địa chỉ email.'
        valid = false
    } else if (!emailRegex.test(trimmedEmail)) {
        fieldErrors.email = isEn.value ? 'Please enter a valid email address.' : 'Địa chỉ email không hợp lệ.'
        valid = false
    }

    const phoneDigits = trimmedPhone.replace(/[\s\-()+.]/g, '')
    if (!trimmedPhone) {
        fieldErrors.phone = isEn.value ? 'Please enter your phone number.' : 'Vui lòng nhập số điện thoại.'
        valid = false
    } else if (!/^\d{8,15}$/.test(phoneDigits)) {
        fieldErrors.phone = isEn.value
            ? 'Phone number must contain 8 to 15 digits.'
            : 'Số điện thoại phải gồm từ 8 đến 15 chữ số.'
        valid = false
    }

    return valid
}

function applyServerErrors(errors) {
    if (!errors || typeof errors !== 'object') return
    const firstMsg = (val) => (Array.isArray(val) ? val[0] : typeof val === 'string' ? val : '')
    const nameErr = firstMsg(errors.name || errors['data.name'])
    const emailErr = firstMsg(errors.email || errors['data.email'])
    const phoneErr = firstMsg(errors.phone || errors['data.phone'])
    const noteErr = firstMsg(errors.note || errors.content || errors['data.note'])

    if (nameErr) fieldErrors.name = nameErr
    if (emailErr) fieldErrors.email = emailErr
    if (phoneErr) fieldErrors.phone = phoneErr
    if (noteErr) fieldErrors.note = noteErr
}

function getEndpoint() {
    const proxy = instance?.proxy
    if (proxy && typeof proxy.route === 'function') {
        try {
            const url = proxy.route('contact.store')
            if (url && url !== '#') return url
        } catch (e) {
            // Fall through to localized fallback
        }
    }
    return isEn.value ? '/en/contacts' : '/contacts'
}

async function submitForm() {
    if (isSubmitting.value) return
    submitSuccess.value = false

    if (!validateClient()) return

    isSubmitting.value = true
    submitError.value = ''

    const trimmedCompany = form.company.trim()
    const payload = {
        type: 'CONTACT_FORM',
        name: form.name.trim(),
        company: trimmedCompany,
        email: form.email.trim(),
        phone: form.phone.trim(),
        note: form.note.trim(),
        data: {
            name: form.name.trim(),
            company: trimmedCompany,
            email: form.email.trim(),
            phone: form.phone.trim(),
            note: form.note.trim(),
            form_source: 'home_contact_section',
            lead_type: 'quote',
        },
    }

    const endpoint = getEndpoint()
    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }

    try {
        let responseData = null

        if (typeof window !== 'undefined' && window.axios) {
            const response = await window.axios.post(endpoint, payload, { headers })
            responseData = response?.data
        } else {
            const csrfToken =
                typeof document !== 'undefined'
                    ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    : ''
            const res = await fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    ...headers,
                    'Content-Type': 'application/json',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
                body: JSON.stringify(payload),
            })
            const data = await res.json().catch(() => null)
            if (!res.ok) {
                const err = new Error(data?.message || 'Request failed')
                err.response = { status: res.status, data }
                throw err
            }
            responseData = data
        }

        if (responseData && responseData.success === true) {
            submitSuccess.value = true
            form.name = ''
            form.company = ''
            form.email = ''
            form.phone = ''
            form.note = ''
            resetErrors()
        } else {
            if (responseData?.errors) {
                applyServerErrors(responseData.errors)
            }
            submitError.value =
                responseData?.message ||
                (isEn.value
                    ? 'Unable to submit your request right now. Please try again.'
                    : 'Không thể gửi yêu cầu lúc này. Vui lòng thử lại.')
        }
    } catch (error) {
        const status = error?.response?.status
        const errData = error?.response?.data
        if (status === 422 && errData?.errors) {
            applyServerErrors(errData.errors)
            submitError.value =
                errData.message ||
                (isEn.value
                    ? 'Please check the highlighted fields and try again.'
                    : 'Vui lòng kiểm tra lại các thông tin bên trên.')
        } else {
            submitError.value =
                errData?.message ||
                (isEn.value
                    ? 'A network or server error occurred. Please try again or call our hotline directly.'
                    : 'Có lỗi kết nối hoặc máy chủ. Vui lòng thử lại hoặc gọi trực tiếp hotline.')
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>
