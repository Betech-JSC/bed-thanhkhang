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
                        <div class="relative lg:absolute lg:right-[33px] lg:bottom-[35px] w-[114px] h-[114px] shrink-0 flex items-center justify-center select-none">
                            <svg class="w-[114px] h-[114px] animate-[spin_15s_linear_infinite]" viewBox="0 0 114 114">
                                <path
                                    id="circlePath"
                                    d="M 57, 57 m -43, 0 a 43,43 0 1,1 86,0 a 43,43 0 1,1 -86,0"
                                    fill="transparent"
                                />
                                <text class="text-[9.5px] font-sans tracking-[1.8px] uppercase fill-[#FAFAFA] font-medium">
                                    <textPath href="#circlePath" startOffset="0%">
                                        FROM IDEAS TO REALITY - FROM IDEAS TO REALITY -
                                    </textPath>
                                </text>
                            </svg>
                            <div class="absolute w-[40px] h-[40px] flex items-center justify-center">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-[17px] h-[17px] text-[#FAFAFA]"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <line x1="7" y1="17" x2="17" y2="7"></line>
                                    <polyline points="7 7 17 7 17 17"></polyline>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. LÝ DO KHỞI ĐẦU (FIGMA 2283:7109) -->
        <section v-scroll-reveal class="py-10 lg:py-[40px] bg-dark-bg">
            <div class="app-container">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8 lg:gap-[120px]">
                    <!-- Left Column: Chuyên mục & Tiêu đề (Figma 2283:7110) -->
                    <div class="lg:w-[360px] shrink-0 flex flex-col gap-4">
                        <p class="text-[#84ADFF] font-normal text-[16px] leading-[19px] font-sans">
                            {{ tt('Vì sao HH Maker ra đời?') }}
                        </p>
                        <h2 class="text-2xl sm:text-3xl lg:text-[38px] lg:leading-[46px] font-bold text-[#FAFAFA] uppercase font-sans tracking-normal">
                            {{ tt('TỪ ĐAM MÊ ĐẾN MỘT SỨ MỆNH') }}
                        </h2>
                    </div>

                    <!-- Right Column: Câu chuyện sứ mệnh (Figma 2283:7113) -->
                    <div class="lg:w-[672px] flex flex-col gap-6 text-[#B9C0D4] font-normal text-[16px] leading-[26px] font-sans">
                        <p>
                            {{ tt('HH Maker được thành lập từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại của Lâm Hồng Hải. Điểm khởi đầu là một mong muốn giản dị: rút ngắn khoảng cách giữa ý tưởng và sản phẩm vật lý.') }}
                        </p>
                        <p>
                            {{ tt('Một bản phác thảo, một hình ảnh hay một nhu cầu rất riêng đều có thể mở ra một cuộc trao đổi. Tại HH Maker, thiết kế và chế tác không tách rời nhau: hình dáng cần đẹp, đồng thời phải phù hợp để tạo thành sản phẩm thực tế.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. NHÀ SÁNG LẬP (FIGMA 2283:7116) -->
        <section v-scroll-reveal class="py-10 lg:py-[40px] bg-dark-bg">
            <div class="app-container">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-10 lg:gap-[72px]">
                    <!-- Left: Khung chân dung Founder Lâm Hồng Hải vát góc công nghệ (Figma 2283:7602) -->
                    <div class="w-full max-w-[561px] lg:w-[561px] lg:h-[666px] mx-auto lg:mx-0 shrink-0 select-none">
                        <img
                            src="/assets/images/about/founder-composite.png"
                            :alt="tt('Lâm Hồng Hải - Founder HH Maker')"
                            class="w-full h-full object-contain"
                            loading="lazy"
                        />
                    </div>

                    <!-- Right: Chia sẻ từ Nhà sáng lập (Figma 2283:7122) -->
                    <div class="w-full lg:w-[519px] flex flex-col gap-8 lg:gap-[40px]">
                        <div class="flex flex-col gap-4">
                            <p class="text-[#84ADFF] font-normal text-[16px] leading-[26px] font-sans">
                                {{ tt('Chia sẻ từ Nhà sáng lập HH Maker') }}
                            </p>
                            <h2 class="text-2xl sm:text-3xl lg:text-[32px] lg:leading-[38px] font-bold text-[#FAFAFA] font-sans tracking-normal">
                                {{ tt('Mỗi ý tưởng đều xứng đáng có một hình hài') }}
                            </h2>
                        </div>

                        <div class="flex flex-col gap-6 text-[#B9C0D4] font-normal text-[16px] leading-[26px] font-sans">
                            <p>
                                {{ tt('Xuất phát từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại, tôi thành lập HH Maker với sứ mệnh rút ngắn khoảng cách giữa ý tưởng sơ khai và sản phẩm vật lý.') }}
                            </p>
                            <p>
                                {{ tt('Với mỗi dự án, tôi cùng đội ngũ luôn tỉ mỉ trong từng đường nét thiết kế, lựa chọn vật liệu và kiểm soát quy trình in để mang lại những sản phẩm sắc nét, chuẩn xác và mang đậm dấu ấn cá nhân nhất.') }}
                            </p>
                            <p>
                                {{ tt('Tôi mong HH Maker sẽ là nơi bạn có thể thoải mái chia sẻ cả những ý tưởng chưa trọn vẹn. Qua từng cuộc trao đổi và mỗi lần thử nghiệm, chúng tôi cùng bạn khám phá những cách thể hiện phù hợp, để mỗi sản phẩm khi hoàn thành đều có ý nghĩa riêng. Với tôi, niềm vui lớn nhất là nhìn thấy sự hào hứng của bạn khi lần đầu cầm trên tay điều mình từng hình dung.') }}
                            </p>
                        </div>

                        <!-- Chữ ký triết lý (Figma 2283:7128) -->
                        <p class="text-[#FAFAFA] font-semibold text-[16px] leading-[26px] font-sans">
                            {{ tt('Trân trọng từng ý tưởng - Tận tâm với từng thành phẩm.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. TRIẾT LÝ CHẾ TÁC (FIGMA 2283:7129) -->
        <section v-scroll-reveal class="py-14 lg:py-[80px] bg-dark-bg">
            <div class="app-container flex flex-col gap-8 lg:gap-[40px]">
                <!-- Section Header (Figma 2283:7647) -->
                <div class="flex flex-col gap-3">
                    <p class="text-[#84ADFF] font-normal text-[16px] leading-[19px] font-sans">
                        {{ tt('Cách HH Maker thực hiện trong từng dự án') }}
                    </p>
                    <h2 class="text-2xl sm:text-3xl lg:text-[38px] lg:leading-[53px] font-bold text-[#FAFAFA] uppercase font-sans tracking-normal">
                        {{ tt('CÔNG NGHỆ TẠO HÌNH - SỰ TỈ MỈ TẠO DẤU ẤN') }}
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
                                {{ tt('Giữ trọn dấu ấn riêng') }}
                            </h3>
                            <p class="text-[#B9C0D4] font-normal text-[16px] leading-[26px] font-sans">
                                {{ tt('Phong cách của bạn được thể hiện qua màu sắc, đường nét và những chi tiết cá nhân hóa. HH Maker kết nối những yếu tố ấy để truyền tải đúng tinh thần bạn muốn gửi gắm.') }}
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
                            class="inline-flex items-center justify-center min-w-[175px] h-[40px] px-[20px] py-[8px] bg-[#F5F8FF] hover:bg-white text-[#111322] font-medium text-[16px] leading-[24px] rounded-[8px] transition-all duration-200 whitespace-nowrap font-sans"
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

export default {
    name: 'AboutIndex',
    components: {
        Head,
        Link,
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
                this.tt('Giới thiệu - HH Maker | Đưa sáng tạo vượt ra khỏi màn hình')
            )
        },
        pageDescription() {
            return (
                this.seo?.description ||
                this.tt('HH Maker kết nối nghệ thuật 3D với công nghệ chế tác hiện đại, để một ý tưởng sơ khai có thể trở thành sản phẩm vật lý bạn cầm trên tay.')
            )
        },
        bannerSubtitle() {
            return this.heroBanner?.subtitle || this.tt('Giới thiệu về HH Maker')
        },
        bannerTitle() {
            return this.heroBanner?.title || this.tt('ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH')
        },
        bannerDescription() {
            return (
                this.heroBanner?.description ||
                this.tt('Chúng tôi kết nối nghệ thuật 3D với công nghệ chế tác hiện đại, để một ý tưởng sơ khai có thể trở thành sản phẩm bạn cầm trên tay.')
            )
        },
        bannerImage() {
            return this.heroBanner?.image?.url || '/assets/images/about/hero-about.png'
        },
        bannerImageAlt() {
            return this.heroBanner?.image?.alt || this.tt('Xưởng chế tác 3D HH Maker')
        },
        globalCta() {
            return (
                this.$page?.props?.globalCtaBanner ||
                this.$page?.props?.data?.banner?.BANNER_GLOBAL_CTA ||
                null
            )
        },
        ctaTitle() {
            return this.globalCta?.title || this.tt('Ý tưởng của bạn, bước tiếp theo của chúng ta.')
        },
        ctaDescription() {
            return (
                this.globalCta?.description ||
                this.tt('Chia sẻ mong muốn của bạn. HH Maker sẽ cùng bạn tìm hướng thiết kế và gia công phù hợp.')
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
