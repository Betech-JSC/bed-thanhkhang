<template>
    <section class="relative pt-0 pb-0 overflow-hidden">
        <div class="w-full max-w-[1279px] mx-auto px-4 sm:px-6 lg:px-0">
            <!-- Hero Card Wrapper (Figma Node 2701:1177: 1279px x 616px, no border) -->
            <div
                class="relative rounded-[16px] overflow-hidden min-h-[520px] lg:h-[616px] flex items-center bg-[#111322] px-6 py-12 sm:px-10 lg:px-[80px] lg:py-0"
            >
                <!-- Background Image (Figma Node 2701:1180) -->
                <div class="absolute inset-0 z-0 overflow-hidden rounded-[16px]">
                    <picture>
                        <source :srcset="currentBannerWebp" type="image/webp" />
                        <img
                            :src="currentBannerImage"
                            alt="Thành Khang Tecsat - Thiết bị công nghiệp & Phụ tùng thang cuốn"
                            class="w-full h-full object-cover object-right md:object-center select-none"
                            width="1279"
                            height="616"
                            loading="eager"
                            decoding="async"
                            fetchpriority="high"
                            @error="handleImageError"
                        />
                    </picture>
                    <!-- Subtle mobile gradient only on small screens; on desktop the 3D artwork is clear -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-[#080C14]/90 via-[#080C14]/60 to-transparent md:hidden"
                    ></div>
                </div>

                <!-- Hero Content (Figma Node 2701:1181: width 565px, left 80px, top 165px, gap 48px) -->
                <div class="relative z-10 w-full max-w-[580px] flex flex-col gap-8 lg:gap-[48px] items-start">
                    <!-- Text header (Figma Node 2701:1183: width 565px, gap 16px) -->
                    <div class="w-full max-w-[580px] flex flex-col gap-4 items-start">
                        <!-- Subtitle (Figma Node 2701:1184: width 481px, 16px, leading 22px, #D1E0FF) -->
                        <div class="w-full max-w-[481px] text-sm sm:text-[16px] font-normal text-[#D1E0FF] leading-[22px] uppercase font-sans">
                            {{ tt(subtitle) }}
                        </div>

                        <!-- Main Title (Figma Node 2701:1185: width 565px, 38px, font-bold 700, leading 46px, #FAFAFA) -->
                        <h1
                            class="w-full max-w-[580px] text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] uppercase tracking-normal lg:tracking-[-0.015em] leading-[1.2] lg:leading-[46px] font-sans"
                        >
                            {{ tt(title) }}
                        </h1>

                        <!-- Description (Figma Node 2701:1186: width 481px, 16px, leading 26px, #D1E0FF) -->
                        <p class="w-full max-w-[481px] text-sm sm:text-[16px] text-[#D1E0FF] leading-[26px] font-normal font-sans">
                            {{ tt(description) }}
                        </p>
                    </div>

                    <!-- Dual CTA Buttons (Figma Node 2701:1188: gap 10px, height 40px) -->
                    <div class="flex flex-wrap items-center gap-[10px]">
                        <a
                            :href="primaryCtaLink || '#featured-carousel'"
                            class="inline-flex items-center justify-center h-[40px] px-[20px] py-[8px] text-sm sm:text-[16px] leading-[24px] font-medium rounded-[8px] bg-[#F5F8FF] text-[#111322] hover:bg-white transition-colors duration-200 active:scale-[0.98]"
                        >
                            {{ tt(primaryCtaText) }}
                        </a>

                        <a
                            :href="secondaryCtaLink || '#contact-section'"
                            class="inline-flex items-center justify-center h-[40px] px-[20px] py-[8px] rounded-[8px] border border-[#F5F8FF] text-[#F5F8FF] hover:bg-white/10 hover:border-white text-sm sm:text-[16px] leading-[24px] font-medium transition-colors duration-200 active:scale-[0.98]"
                        >
                            {{ tt(secondaryCtaText) }}
                        </a>
                    </div>
                </div>

                <!-- Rotating Circular Badge (Figma Node 2701:1201: 114px x 114px, right 33px, bottom 35px) -->
                <div
                    class="absolute bottom-6 right-6 lg:bottom-[35px] lg:right-[33px] hidden sm:flex items-center justify-center z-10"
                >
                    <CircularBadge
                        tag="a"
                        :href="primaryCtaLink || '#featured-carousel'"
                        :aria-label="tt('Khám phá ngay')"
                    />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import CircularBadge from '@/Components/CircularBadge.vue';

const tt = (s) => s;

export default {
    name: 'HeroSection',
    components: {
        CircularBadge,
    },
    props: {
        bannerImage: {
            type: String,
            default: '/assets/images/homepage/hero-banner.png',
        },
        subtitle: {
            type: String,
            default: tt('Về chúng tôi'),
        },
        title: {
            type: String,
            default: tt('GIẢI PHÁP THIẾT BỊ CÔNG NGHIỆP & PHỤ TÙNG THANG CUỐN CHUẨN OEM'),
        },
        description: {
            type: String,
            default: tt('Thành Khang Tecsat cung cấp linh kiện thang cuốn, xích công nghiệp Universal Chains, hệ thống băng tải và quạt trần công nghiệp BLDC HVLS với đầy đủ chứng từ CO/CQ'),
        },
        primaryCtaText: {
            type: String,
            default: tt('Khám phá ngay'),
        },
        primaryCtaLink: {
            type: String,
            default: '#featured-carousel',
        },
        secondaryCtaText: {
            type: String,
            default: tt('Nhận tư vấn thiết kế'),
        },
        secondaryCtaLink: {
            type: String,
            default: '#contact-section',
        },
    },
    data() {
        return {
            fallbackBanner: '/assets/images/homepage/hero-banner.png',
            hasError: false,
        };
    },
    computed: {
        currentBannerImage() {
            if (this.hasError || !this.bannerImage) {
                return this.fallbackBanner;
            }
            if (typeof this.bannerImage === 'string' && this.bannerImage.includes('/static/assets/')) {
                return this.bannerImage.replace(/^https?:\/\/[^\/]+/, '').replace('/static/', '/');
            }
            return this.bannerImage;
        },
        currentBannerWebp() {
            const img = this.currentBannerImage;
            if (typeof img === 'string' && img.endsWith('.png')) {
                return img.replace(/\.png$/, '.webp');
            }
            return '/assets/images/homepage/hero-banner.webp';
        },
    },
    watch: {
        bannerImage() {
            this.hasError = false;
        },
    },
    methods: {
        handleImageError() {
            this.hasError = true;
        },
    },
}
</script>
