<template>
    <section
        id="contact-section"
        class="relative w-full bg-[#00284B] overflow-hidden py-10 md:py-16 lg:pt-[125px] lg:pb-[124px]"
    >
        <!-- Decorative Chain Accent 1: Bottom-Left (native RGBA transparency, behind content, Figma 595:15499) -->
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

        <!-- Decorative Chain Accent 2: Top-Right (sweeping diagonally behind frosted card, Figma 595:15502) -->
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

        <!-- Decorative Chain Accent 3: Bottom-Right (sweeping diagonally behind bottom-right of frosted card, Figma 595:15532) -->
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

        <!-- Main Content Grid -->
        <div class="app-container relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-stretch justify-between gap-8 lg:gap-[64px]">
                <!-- Left Column: Title, Subtitle & Live CMS Contact Details -->
                <div class="w-full lg:w-[462px] xl:w-[512px] lg:min-h-[508px] shrink-0 flex flex-col justify-between gap-8">
                    <div v-if="heading || description">
                        <h2
                            v-if="heading"
                            class="font-bold text-[24px] sm:text-[26px] lg:text-[28px] leading-[1.6] lg:min-h-[100px] text-[#FAFAFA] uppercase break-words"
                        >
                            {{ heading }}
                        </h2>
                        <p
                            v-if="description"
                            class="font-medium text-[15px] sm:text-[16px] leading-[1.6] text-[#9AA4B2] mt-3 lg:mt-0 break-words"
                        >
                            {{ description }}
                        </p>
                    </div>

                    <!-- Live CMS Contact Info (Bound to $page.props.global / configs) -->
                    <div class="flex flex-col gap-[12px]">
                        <a
                            v-if="displayHotline"
                            :href="'tel:' + cleanHotline"
                            class="flex items-center gap-2 text-[14px] font-medium leading-[1.6] text-[#EEF2F6] hover:text-[#C9E0FF] transition-colors focus:outline-none focus-visible:underline"
                        >
                            <img
                                src="/assets/svg/icons/contact-phone.svg"
                                alt=""
                                aria-hidden="true"
                                width="24"
                                height="24"
                                class="w-6 h-6 shrink-0"
                            />
                            <span class="break-words">Hotline: {{ displayHotline }}</span>
                        </a>

                        <a
                            v-if="displayEmail"
                            :href="'mailto:' + displayEmail"
                            class="flex items-center gap-2 text-[14px] font-medium leading-[1.6] text-[#EEF2F6] hover:text-[#C9E0FF] transition-colors focus:outline-none focus-visible:underline"
                        >
                            <img
                                src="/assets/svg/icons/contact-mail.svg"
                                alt=""
                                aria-hidden="true"
                                width="24"
                                height="24"
                                class="w-6 h-6 shrink-0"
                            />
                            <span class="break-all sm:break-words">Email: {{ displayEmail }}</span>
                        </a>

                        <div
                            v-if="displayAddress"
                            class="flex items-start gap-2 text-[14px] font-medium leading-[1.6] text-[#EEF2F6]"
                        >
                            <img
                                src="/assets/svg/icons/contact-pin.svg"
                                alt=""
                                aria-hidden="true"
                                width="24"
                                height="24"
                                class="w-6 h-6 shrink-0"
                            />
                            <span class="break-words">
                                {{ isEn ? 'Headquarters' : 'Trụ sở' }}: {{ displayAddress }}
                            </span>
                        </div>

                        <div
                            v-if="displayShowroomAddress"
                            class="flex items-start gap-2 text-[14px] font-medium leading-[1.6] text-[#EEF2F6]"
                        >
                            <img
                                src="/assets/svg/icons/contact-pin.svg"
                                alt=""
                                aria-hidden="true"
                                width="24"
                                height="24"
                                class="w-6 h-6 shrink-0"
                            />
                            <span class="break-words">
                                {{ isEn ? 'Office & Showroom' : 'VPGD & Showroom' }}: {{ displayShowroomAddress }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Frosted Quote Form Card -->
                <div
                    class="w-full flex-1 min-w-0 backdrop-blur-[3px] bg-[rgba(201,224,255,0.8)] rounded-[8px] p-6 sm:p-8 lg:p-[40px] flex flex-col justify-between"
                >
                    <form
                        @submit.prevent="submitForm"
                        novalidate
                        class="flex flex-col justify-between h-full gap-6"
                    >
                        <div class="flex flex-col gap-[16px]">
                            <!-- Field 1: Full Name (required) -->
                            <div>
                                <label
                                    for="home-contact-name"
                                    class="block text-[14px] font-medium leading-[1.6] text-[#424242] mb-[4px]"
                                >
                                    {{ isEn ? 'Full Name*' : 'Họ và tên*' }}
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
                                    class="w-full bg-white border rounded-[8px] px-[12px] py-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] text-[16px] font-normal leading-[1.6] text-[#1A1A1A] placeholder:text-[#808080] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-colors"
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

                            <!-- Field 2 & 3: Email + Phone (2-column row) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-[12px]">
                                <div>
                                    <label
                                        for="home-contact-email"
                                        class="block text-[14px] font-medium leading-[1.6] text-[#424242] mb-[4px]"
                                    >
                                        Email*
                                    </label>
                                    <input
                                        id="home-contact-email"
                                        v-model="form.email"
                                        type="email"
                                        name="email"
                                        autocomplete="email"
                                        required
                                        :placeholder="isEn ? 'example@company.com' : 'example@congty.com'"
                                        :aria-invalid="Boolean(fieldErrors.email)"
                                        :aria-describedby="fieldErrors.email ? 'home-contact-email-error' : undefined"
                                        class="w-full bg-white border rounded-[8px] px-[12px] py-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] text-[16px] font-normal leading-[1.6] text-[#1A1A1A] placeholder:text-[#808080] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-colors"
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
                                        class="block text-[14px] font-medium leading-[1.6] text-[#424242] mb-[4px]"
                                    >
                                        {{ isEn ? 'Phone Number*' : 'Số điện thoại*' }}
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
                                        class="w-full bg-white border rounded-[8px] px-[12px] py-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] text-[16px] font-normal leading-[1.6] text-[#1A1A1A] placeholder:text-[#808080] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-colors"
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

                            <!-- Field 4: Message / Note (optional) -->
                            <div>
                                <label
                                    for="home-contact-note"
                                    class="block text-[14px] font-medium leading-[1.6] text-[#424242] mb-[4px]"
                                >
                                    {{ isEn ? 'Message' : 'Lời nhắn' }}
                                </label>
                                <textarea
                                    id="home-contact-note"
                                    v-model="form.note"
                                    name="note"
                                    rows="5"
                                    :placeholder="
                                        isEn
                                            ? 'Enter your quotation details (product, quantity, required delivery time...)'
                                            : 'Nhập nội dung bạn cần báo giá (sản phẩm, số lượng, thời gian cần hàng...)'
                                    "
                                    :aria-invalid="Boolean(fieldErrors.note)"
                                    :aria-describedby="fieldErrors.note ? 'home-contact-note-error' : undefined"
                                    class="w-full min-h-[146px] resize-y bg-white border rounded-[8px] px-[12px] py-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] text-[16px] font-normal leading-[1.6] text-[#1A1A1A] placeholder:text-[#808080] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-colors"
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

                        <!-- Left-Aligned Primary Submit Button (Figma 595:15531) -->
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="self-start bg-[#005EA4] hover:bg-[#004c86] active:bg-[#003d6d] text-[#FAFAFA] font-medium text-[16px] leading-[1.6] px-[24px] py-[10px] rounded-[4px] inline-flex items-center justify-center gap-2 transition-colors disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer min-h-[46px] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                        >
                            <span>
                                {{
                                    isSubmitting
                                        ? isEn
                                            ? 'Sending...'
                                            : 'Đang gửi...'
                                        : isEn
                                            ? 'Get Consultation Now'
                                            : 'Nhận tư vấn ngay'
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
import { usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
    contactSection: {
        type: Object,
        default: () => null,
    },
})

const page = usePage()
const instance = getCurrentInstance()

const currentLocale = computed(() => {
    const loc = page.props.value?.locale
    if (typeof loc === 'string') return loc
    return loc?.current || 'vi'
})

const isEn = computed(() => currentLocale.value === 'en')

const siteConfig = computed(() => ({
    ...(page.props.value?.global || {}),
    ...(page.props.value?.configs || {}),
}))

const heading = computed(() => (props.contactSection?.title || '').trim())

const description = computed(() => (props.contactSection?.description || '').trim())

const displayHotline = computed(
    () => siteConfig.value.hotline || siteConfig.value.general_phone || siteConfig.value.phone || ''
)

const cleanHotline = computed(() => String(displayHotline.value || '').replace(/[^\d+]/g, ''))

const displayEmail = computed(() => siteConfig.value.general_email || siteConfig.value.email || '')

const displayAddress = computed(() => siteConfig.value.general_address || siteConfig.value.address || '')

const displayShowroomAddress = computed(() => {
    const showroom = siteConfig.value.showroom_address || ''
    if (!showroom || showroom === displayAddress.value) return ''
    return showroom
})

const form = reactive({
    name: '',
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

    const payload = {
        type: 'CONTACT_FORM',
        name: form.name.trim(),
        email: form.email.trim(),
        phone: form.phone.trim(),
        note: form.note.trim(),
        data: {
            name: form.name.trim(),
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
