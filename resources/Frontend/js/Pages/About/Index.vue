<template>
    <Head>
        <title>{{ pageTitle }}</title>
        <meta name="description" :content="pageDescription" />
        <meta property="og:title" :content="pageTitle" />
        <meta property="og:description" :content="pageDescription" />
    </Head>

    <div class="bg-dark-bg text-white">
        <!-- 1. HERO BANNER (FIGMA 2283:7100) -->
        <section class="relative pt-4 lg:pt-0 pb-12 lg:pb-[64px]">
            <div class="w-full max-w-[1279px] mx-auto px-4 sm:px-6 lg:px-0">
                <!-- Hero Outer Container (1279px x 616px Mask Group - Figma 2283:7573) -->
                <div class="relative rounded-[16px] overflow-hidden min-h-[500px] lg:h-[616px] flex items-center p-6 sm:p-10 lg:px-[80px] lg:py-0">
                    <!-- Background Image from Figma (Figma 2283:7576) -->
                    <div class="absolute inset-0 z-0">
                        <img
                            :src="bannerImage"
                            :alt="bannerImageAlt"
                            class="w-full h-full object-cover object-center"
                        />
                        <!-- Dark gradient overlay only on mobile for text readability; desktop keeps original sharp image -->
                        <div class="absolute inset-0 bg-gradient-to-t from-dark-bg via-dark-bg/80 to-dark-bg/40 lg:hidden"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-dark-bg/90 via-dark-bg/60 to-transparent lg:hidden"></div>
                    </div>

                    <!-- Hero Content -->
                    <div class="z-10 w-full flex flex-col gap-8">
                        <!-- Left: Main Headline & Intro (Figma 2283:7579) -->
                        <div class="w-full max-w-[565px] flex flex-col gap-4">
                            <p class="text-[#D1E0FF] font-normal text-[16px] leading-[22px] font-sans">
                                {{ tt(bannerSubtitle) }}
                            </p>

                            <h1 class="text-2xl sm:text-3xl lg:text-[38px] lg:leading-[46px] font-bold text-[#FAFAFA] uppercase font-sans tracking-normal">
                                {{ tt(bannerTitle) }}
                            </h1>

                            <p class="text-[#D1E0FF] font-normal text-[16px] leading-[26px] font-sans max-w-[565px]">
                                {{ tt(bannerDescription) }}
                            </p>
                        </div>

                        <!-- Right: Interactive Circular Rotating Stamp Badge (Figma Frame 237 - 2283:7597) -->
                        <div class="relative lg:absolute lg:right-[33px] lg:bottom-[35px] shrink-0 flex items-center justify-center select-none z-10">
                            <CircularBadge
                                tag="a"
                                href="#about-story"
                                :title="tt('Khám phá câu chuyện thương hiệu')"
                                :aria-label="tt('Khám phá câu chuyện thương hiệu')"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. LÝ DO KHỞI ĐẦU (FIGMA 2283:7109) -->
        <section id="about-story" v-scroll-reveal class="py-10 lg:py-[40px] bg-dark-bg">
            <div class="app-container">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8 lg:gap-[120px]">
                    <!-- Left Column: Chuyên mục & Tiêu đề (Figma 2283:7110) -->
                    <div class="lg:w-[360px] shrink-0 flex flex-col gap-4">
                        <p class="text-[#84ADFF] font-normal text-[16px] leading-[19px] font-sans">
                            {{ tt('Về Thành Khang Tecsat') }}
                        </p>
                        <h2 class="text-2xl sm:text-3xl lg:text-[38px] lg:leading-[46px] font-bold text-[#FAFAFA] uppercase font-sans tracking-normal">
                            {{ tt('CHUYÊN MÔN KỸ THUẬT - UY TÍN ĐỒNG HÀNH') }}
                        </h2>
                    </div>

                    <!-- Right Column: Câu chuyện sứ mệnh -->
                    <div class="lg:w-[672px] flex flex-col gap-6 text-[#B9C0D4] font-normal text-[16px] leading-[26px] font-sans">
                        <p>
                            {{ tt('Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat hoạt động chuyên sâu trong lĩnh vực cung ứng thiết bị công nghiệp B2B, phụ tùng thang cuốn - thang máy và hệ thống xích tải truyền động chuẩn OEM.') }}
                        </p>
                        <p>
                            {{ tt('Với nền tảng kỹ thuật vững chắc và mạng lưới nhà sản xuất uy tín như Universal Chains, chúng tôi luôn kiểm soát chặt chẽ từng thông số tải trọng, vật liệu và chứng từ CO/CQ để đảm bảo vận hành ổn định cho mọi công trình.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. TẦM NHÌN & SỨ MỆNH -->
        <section v-scroll-reveal class="py-10 lg:py-[40px] bg-dark-bg">
            <div class="app-container">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-10 lg:gap-[72px]">
                    <div class="w-full max-w-[561px] lg:w-[561px] lg:h-[666px] mx-auto lg:mx-0 shrink-0 select-none">
                        <img
                            src="/assets/images/about/founder-composite.png"
                            :alt="tt('Thành Khang Tecsat - Giải pháp Thiết bị Công nghiệp')"
                            class="w-full h-full object-contain"
                            loading="lazy"
                        />
                    </div>

                    <div class="w-full lg:w-[519px] flex flex-col gap-8 lg:gap-[40px]">
                        <div class="flex flex-col gap-4">
                            <p class="text-[#84ADFF] font-normal text-[16px] leading-[26px] font-sans">
                                {{ tt('Cam kết từ Thành Khang Tecsat') }}
                            </p>
                            <h2 class="text-2xl sm:text-3xl lg:text-[32px] lg:leading-[38px] font-bold text-[#FAFAFA] font-sans tracking-normal">
                                {{ tt('Chuẩn xác kỹ thuật trong từng linh kiện vận hành') }}
                            </h2>
                        </div>

                        <div class="flex flex-col gap-6 text-[#B9C0D4] font-normal text-[16px] leading-[26px] font-sans">
                            <p>
                                {{ tt('Thành Khang Tecsat tập trung vào ba mảng giải pháp cốt lõi: Linh kiện & Phụ tùng Thang cuốn - Thang máy, Hệ thống Băng tải & Xích tải Công nghiệp, cùng Giải pháp Tiết kiệm Điện với Quạt trần Công nghiệp BLDC HVLS.') }}
                            </p>
                            <p>
                                {{ tt('Mỗi đơn hàng đều được đội ngũ kỹ sư đối chiếu kỹ thuật chuẩn xác theo mã phụ tùng OEM, tiêu chuẩn EN 115, ISO và DIN trước khi xuất kho.') }}
                            </p>
                            <p>
                                {{ tt('Chúng tôi mong muốn trở thành đối tác tin cậy lâu dài của các chủ đầu tư tòa nhà, nhà máy sản xuất và đơn vị bảo trì thang máy trên toàn quốc.') }}
                            </p>
                        </div>

                        <p class="text-[#FAFAFA] font-semibold text-[16px] leading-[26px] font-sans">
                            {{ tt('Chất lượng chuẩn OEM - Tiến độ đúng cam kết.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. TRIẾT LÝ CUNG ỨNG -->
        <section v-scroll-reveal class="py-14 lg:py-[80px] bg-dark-bg">
            <div class="app-container flex flex-col gap-8 lg:gap-[40px]">
                <div class="flex flex-col gap-3">
                    <p class="text-[#84ADFF] font-normal text-[16px] leading-[19px] font-sans">
                        {{ tt('Cách Thành Khang Tecsat thực hiện trong từng dự án') }}
                    </p>
                    <h2 class="text-2xl sm:text-3xl lg:text-[38px] lg:leading-[53px] font-bold text-[#FAFAFA] uppercase font-sans tracking-normal">
                        {{ tt('CHUẨN KỸ THUẬT - ĐỒNG HÀNH BỀN VỮNG') }}
                    </h2>
                </div>

                <!-- 3 Value Cards Grid (Figma 2283:7132) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card 1: Thấu hiểu từng ý tưởng (Figma 2283:7133) -->
                    <div class="w-full lg:w-[368px] lg:h-[310px] border border-[#5D6B98] rounded-[12px] p-[28px] flex flex-col justify-start gap-[32px] transition-colors duration-300 hover:border-[#84ADFF]">
                        <div class="w-12 h-12 shrink-0">
                            <img
                                src="/assets/images/about/lightbulb.svg"
                                :alt="tt('Thấu hiểu từng ý tưởng')"
                                class="w-12 h-12 object-contain"
                            />
                        </div>
                        <div class="flex flex-col gap-4">
                            <h3 class="text-[24px] leading-[28px] font-bold text-[#FAFAFA] font-sans">
                                {{ tt('Thấu hiểu từng ý tưởng') }}
                            </h3>
                            <p class="text-[#B9C0D4] font-normal text-[16px] leading-[26px] font-sans">
                                {{ tt('Chúng tôi lắng nghe câu chuyện, nhu cầu sử dụng và mong muốn của bạn để tìm ra hướng thiết kế phù hợp, trước khi bắt đầu những nét phác thảo đầu tiên.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Card 2: Chăm chút từng chi tiết (Figma 2283:7138) -->
                    <div class="w-full lg:w-[368px] lg:h-[310px] border border-[#5D6B98] rounded-[12px] p-[28px] flex flex-col justify-start gap-[32px] transition-colors duration-300 hover:border-[#84ADFF]">
                        <div class="w-12 h-12 shrink-0">
                            <img
                                src="/assets/images/about/scan-line.svg"
                                :alt="tt('Chăm chút từng chi tiết')"
                                class="w-12 h-12 object-contain"
                            />
                        </div>
                        <div class="flex flex-col gap-4">
                            <h3 class="text-[24px] leading-[28px] font-bold text-[#FAFAFA] font-sans">
                                {{ tt('Chăm chút từng chi tiết') }}
                            </h3>
                            <p class="text-[#B9C0D4] font-normal text-[16px] leading-[26px] font-sans">
                                {{ tt('Từ hình khối, tỉ lệ, vật liệu đến quá trình in và hoàn thiện, sản phẩm đều được chăm chút để hài hòa về hình thức và phù hợp với cách sử dụng thực tế.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Card 3: Giữ trọn dấu ấn riêng (Figma 2283:7143) -->
                    <div class="w-full lg:w-[368px] lg:h-[310px] border border-[#5D6B98] rounded-[12px] p-[28px] flex flex-col justify-start gap-[32px] transition-colors duration-300 hover:border-[#84ADFF]">
                        <div class="w-12 h-12 shrink-0">
                            <img
                                src="/assets/images/about/fingerprint.svg"
                                :alt="tt('Giữ trọn dấu ấn riêng')"
                                class="w-12 h-12 object-contain"
                            />
                        </div>
                        <div class="flex flex-col gap-4">
                            <h3 class="text-[24px] leading-[28px] font-bold text-[#FAFAFA] font-sans">
                                {{ tt('Chuẩn xác và bền bỉ') }}
                            </h3>
                            <p class="text-[#B9C0D4] font-normal text-[16px] leading-[26px] font-sans">
                                {{ tt('Mỗi hệ thống vận hành đòi hỏi thông số riêng về tải trọng, bước xích và vật liệu. Thành Khang Tecsat tư vấn đúng cấu hình để đảm bảo hiệu suất và an toàn lâu dài.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. KẾT NỐI DỰ ÁN CTA (FIGMA 2283:7633) -->
        <section v-scroll-reveal class="pt-10 pb-14 lg:pt-[40px] lg:pb-[80px] bg-dark-bg">
            <div class="app-container">
                <!-- Banner gradient #2970FF -> #00359E (Figma 2283:7634) -->
                <div
                    class="relative rounded-[16px] overflow-hidden p-6 sm:p-8 lg:p-[40px] lg:h-[157px] flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6"
                    style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%)"
                >
                    <div class="lg:w-[817px] flex flex-col gap-3">
                        <h2 class="text-2xl sm:text-3xl lg:text-[32px] lg:leading-[37px] font-bold text-[#FAFAFA] font-sans">
                            {{ tt(ctaTitle) }}
                        </h2>
                        <p class="text-[#FAFAFA] font-normal text-[16px] leading-[26px] font-sans">
                            {{ tt(ctaDescription) }}
                        </p>
                    </div>

                    <div class="shrink-0">
                        <Link
                            :href="ctaButtonLink"
                            class="inline-flex items-center justify-center min-w-[175px] h-[40px] px-[20px] py-[8px] bg-[#F5F8FF] hover:bg-white text-[#111322] font-medium text-[16px] leading-[24px] rounded-[8px] transition-colors duration-200 active:scale-[0.98] whitespace-nowrap font-sans"
                        >
                            {{ tt(ctaButtonText) }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import CircularBadge from '@/Components/CircularBadge.vue'

export default {
    name: 'AboutIndex',
    components: {
        Head,
        Link,
        CircularBadge,
    },
    props: {
        heroBanner: {
            type: Object,
            default: () => null,
        },
        posts: {
            type: Array,
            default: () => [],
        },
        seo: {
            type: Object,
            default: () => null,
        },
    },
    computed: {
        pageTitle() {
            return (
                this.seo?.title ||
                this.tt('Giới thiệu - Thành Khang Tecsat | Thiết bị Công nghiệp & Phụ tùng Thang cuốn')
            )
        },
        pageDescription() {
            return (
                this.seo?.description ||
                this.tt('Thành Khang Tecsat cung cấp linh kiện thang cuốn - thang máy, hệ thống băng tải, xích tải Universal Chains và quạt trần công nghiệp BLDC HVLS chuẩn OEM.')
            )
        },
        bannerSubtitle() {
            return this.heroBanner?.subtitle || this.tt('Giới thiệu về Thành Khang Tecsat')
        },
        bannerTitle() {
            return this.heroBanner?.title || this.tt('GIẢI PHÁP THIẾT BỊ CÔNG NGHIỆP BỀN BỈ & CHUẨN OEM')
        },
        bannerDescription() {
            return (
                this.heroBanner?.description ||
                this.tt('Chúng tôi kết nối kỹ thuật truyền động chính xác và nguồn linh kiện chính hãng, đảm bảo an toàn và hiệu suất tối ưu cho công trình của bạn.')
            )
        },
        bannerImage() {
            return this.heroBanner?.image?.url || '/assets/images/about/hero-about.png'
        },
        bannerImageAlt() {
            return this.heroBanner?.image?.alt || this.tt('Thành Khang Tecsat - Thiết bị & Giải pháp Kỹ thuật Công nghiệp')
        },
        globalCta() {
            return (
                this.$page?.props?.globalCtaBanner ||
                this.$page?.props?.data?.banner?.BANNER_GLOBAL_CTA ||
                null
            )
        },
        ctaTitle() {
            return this.globalCta?.title || this.tt('Yêu cầu kỹ thuật của bạn, giải pháp của chúng tôi.')
        },
        ctaDescription() {
            return (
                this.globalCta?.description ||
                this.tt('Chia sẻ thông số linh kiện hoặc nhu cầu công trình. Thành Khang Tecsat sẽ tư vấn phương án kỹ thuật và báo giá phù hợp.')
            )
        },
        ctaButtonText() {
            return this.globalCta?.primary_cta_text || this.tt('Nhận tư vấn ngay')
        },
        ctaButtonLink() {
            return (
                this.globalCta?.primary_cta_link ||
                this.globalCta?.link ||
                (typeof route === 'function' ? route('contact') : '/lien-he')
            )
        },
    },
}
</script>
