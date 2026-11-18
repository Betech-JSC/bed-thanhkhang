<template>
    <section v-scroll-reveal class="py-10 lg:pt-10 lg:pb-20">
        <div class="max-w-[1152px]">
            <!-- Banner Container (Figma Node 2352:11795 - 1152x183px) -->
            <div
                class="relative w-full rounded-2xl p-6 sm:p-8 lg:px-10 lg:py-10 lg:min-h-[183px] flex items-center overflow-hidden shadow-xl"
                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%)"
            >
                <div class="relative z-10 w-full flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 lg:gap-10">
                    <!-- Message Content (Figma Node 2352:11796 - 817px) -->
                    <div class="space-y-3 max-w-[817px]">
                        <h2 class="text-2xl sm:text-3xl lg:text-[32px] font-bold text-[#FAFAFA] leading-tight lg:leading-[37px] font-sans">
                            {{ tt(title) }}
                        </h2>
                        <p class="text-base text-[#FAFAFA] leading-[26px] font-sans">
                            {{ tt(description) }}
                        </p>
                    </div>

                    <!-- Action Button (Figma Node 2352:11801 - 175x40px, bo góc 8px) -->
                    <div class="shrink-0 flex items-center">
                        <Link
                            :href="contactHref"
                            class="inline-flex items-center justify-center w-[175px] h-10 rounded-[8px] bg-[#F5F8FF] hover:bg-white text-[#111322] font-medium text-[16px] leading-6 transition-transform duration-200 shadow-md hover:shadow-[0_0_16px_rgba(255,255,255,0.4)] active:scale-[0.98] select-none focus:outline-none focus:ring-2 focus:ring-white font-sans"
                        >
                            {{ tt(ctaText) }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
    serviceSlug: {
        type: String,
        default: '',
    },
    banner: {
        type: Object,
        default: () => null,
    },
})

const instance = getCurrentInstance()
const i18n = {
    tt: (text) => (typeof instance?.proxy?.tt === 'function' ? instance.proxy.tt(text) : text),
}
const page = usePage()
const isEn = computed(() => page.props.value?.locale?.current === 'en')

const ctaData = computed(() => {
    return (
        props.banner ||
        page.props.value?.globalCtaBanner ||
        page.props.value?.data?.banner?.BANNER_GLOBAL_CTA ||
        null
    )
})

const title = computed(() => ctaData.value?.title || i18n.tt('Tạo nên phiên bản mang dấu ấn riêng'))
const description = computed(() => {
    return (
        ctaData.value?.description ||
        i18n.tt('Gửi thông số kỹ thuật, mã linh kiện hoặc bản vẽ công trình để đội ngũ kỹ sư Thành Khang Tecsat tư vấn phương án phụ tùng OEM, xích tải công nghiệp hoặc giải pháp tiết kiệm điện tối ưu.')
    )
})
const ctaText = computed(() => ctaData.value?.primary_cta_text || i18n.tt('Nhận tư vấn ngay'))

const contactHref = computed(() => {
    if (ctaData.value?.primary_cta_link) return ctaData.value.primary_cta_link
    if (ctaData.value?.link) return ctaData.value.link
    try {
        if (typeof route === 'function') {
            const routeName = isEn.value ? 'en.contact' : 'vi.contact'
            return route(routeName)
        }
    } catch {
        // Fallback
    }
    return isEn.value ? '/en/contact' : '/lien-he'
})
</script>
