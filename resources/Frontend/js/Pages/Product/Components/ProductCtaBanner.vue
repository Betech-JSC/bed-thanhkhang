<template>
    <section v-scroll-reveal class="pt-8 pb-12 lg:pt-[40px] lg:pb-[80px] bg-[#111322]">
        <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-[144px]">
            <div
                class="relative w-full max-w-[1152px] mx-auto min-h-[183px] lg:h-[183px] rounded-[16px] overflow-hidden p-8 lg:p-[40px] shadow-2xl flex flex-col lg:flex-row lg:items-center justify-between gap-6"
                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
            >
                <!-- Content Area (Figma 2346:742) -->
                <div class="relative z-10 w-full lg:w-[817px] lg:max-w-[817px] flex flex-col gap-3">
                    <h3 class="text-2xl sm:text-3xl lg:text-[32px] font-bold text-[#FAFAFA] tracking-normal leading-tight lg:leading-[37px] font-sans">
                        {{ title }}
                    </h3>
                    <p class="text-sm sm:text-base lg:text-[16px] text-[#FAFAFA] leading-relaxed lg:leading-[26px] font-normal">
                        {{ description }}
                    </p>
                </div>

                <!-- CTA Single Action Button (Figma 2346:745 - 2346:749) -->
                <div class="relative z-10 shrink-0">
                    <Link
                        :href="ctaLink"
                        class="w-full sm:w-[175px] h-[40px] rounded-[8px] bg-[#F5F8FF] hover:bg-white text-[#111322] text-base font-medium leading-6 inline-flex items-center justify-center transition-colors duration-200 active:scale-[0.98] shadow-md hover:shadow-lg select-none"
                    >
                        {{ ctaText }}
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
    name: 'ProductCtaBanner',
    components: {
        Link,
    },
    props: {
        banner: {
            type: Object,
            default: () => null,
        },
    },
    computed: {
        ctaData() {
            return (
                this.banner ||
                this.$page?.props?.globalCtaBanner ||
                this.$page?.props?.data?.banner?.BANNER_GLOBAL_CTA ||
                null
            )
        },
        title() {
            return this.tt(this.ctaData?.title || 'Tạo nên phiên bản mang dấu ấn riêng')
        },
        description() {
            return this.tt(
                this.ctaData?.description ||
                'Gửi hình ảnh tham khảo hoặc chia sẻ ý tưởng để HH Maker cùng bạn định hình sản phẩm mong muốn. Chúng tôi sẽ tư vấn giải pháp phù hợp với phong cách, nhu cầu và ngân sách của bạn.'
            )
        },
        ctaText() {
            return this.tt(this.ctaData?.primary_cta_text || 'Nhận tư vấn ngay')
        },
        ctaLink() {
            return (
                this.ctaData?.primary_cta_link ||
                this.ctaData?.link ||
                (typeof this.route === 'function' ? this.route('contact') : '/lien-he')
            )
        },
    },
}
</script>
