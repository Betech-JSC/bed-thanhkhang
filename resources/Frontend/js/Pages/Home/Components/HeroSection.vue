<template>
    <section class="relative pt-4 lg:pt-6 pb-3 md:pb-4 overflow-hidden">
        <div class="w-full max-w-[1279px] mx-auto px-4 sm:px-6 lg:px-0">
            <!-- Hero Card Wrapper (Figma Node 2261:5475: 1279px x 616px) -->
            <div
                class="relative rounded-[16px] overflow-hidden min-h-[520px] lg:h-[616px] flex items-center bg-[#0B0D19] border border-white/10 shadow-2xl"
            >
                <!-- Background Image (Figma Node 2261:5478) -->
                <div class="absolute inset-0 z-0 overflow-hidden">
                    <img
                        :src="currentBannerImage"
                        alt="HH Maker 3D Printing Technology"
                        class="w-full h-full object-cover object-right md:object-center select-none"
                        loading="eager"
                        @error="handleImageError"
                    />
                    <!-- Subtle mobile gradient only on small screens; on desktop the 3D artwork is clear -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-[#080C14]/90 via-[#080C14]/60 to-transparent md:hidden"
                    ></div>
                </div>

                <!-- Hero Content (Figma Node 2261:5479) -->
                <div class="relative z-10 max-w-xl lg:max-w-2xl px-6 py-12 sm:px-10 lg:px-20 space-y-6">
                    <!-- Subtitle (Figma Node 2261:5482) -->
                    <div class="text-sm sm:text-base font-normal text-[#D1E0FF] tracking-wider uppercase">
                        {{ tt(subtitle) }}
                    </div>

                    <!-- Main Title (Figma Node 2261:5483: 38px, font-bold 700, leading-[46px]) -->
                    <h1
                        class="text-3xl sm:text-4xl lg:text-[38px] font-bold text-[#FAFAFA] uppercase tracking-tight leading-[1.2] lg:leading-[46px] font-sans"
                    >
                        {{ tt(title) }}
                    </h1>

                    <!-- Description (Figma Node 2261:5484: 16px, leading-[26px]) -->
                    <p class="text-sm sm:text-base text-[#D1E0FF] leading-[26px] max-w-lg font-normal">
                        {{ tt(description) }}
                    </p>

                    <!-- Dual CTA Buttons (Figma Nodes 2261:5488 & 2261:5493) -->
                    <div class="pt-2 flex flex-wrap items-center gap-3 sm:gap-4">
                        <a
                            :href="primaryCtaLink || '#featured-carousel'"
                            class="inline-flex items-center justify-center h-10 px-5 text-sm sm:text-base font-medium rounded-[8px] bg-[#F5F8FF] text-[#111322] hover:bg-white hover:shadow-lg transition-colors duration-200 active:scale-[0.98]"
                        >
                            {{ tt(primaryCtaText) }}
                        </a>

                        <a
                            :href="secondaryCtaLink || '#contact-section'"
                            class="inline-flex items-center justify-center h-10 px-5 rounded-[8px] border border-[#F5F8FF] text-[#F5F8FF] hover:bg-white/10 hover:border-white text-sm sm:text-base font-medium transition-colors duration-200 active:scale-[0.98]"
                        >
                            {{ tt(secondaryCtaText) }}
                        </a>
                    </div>
                </div>

                <!-- Rotating Circular Badge (Figma Node 2261:5499: 114px x 114px, transparent center with 45° arrow) -->
                <div
                    class="absolute bottom-6 right-6 lg:bottom-10 lg:right-10 hidden sm:flex items-center justify-center z-10"
                >
                    <a
                        :href="primaryCtaLink || '#featured-carousel'"
                        class="relative w-28 h-28 lg:w-[114px] lg:h-[114px] flex items-center justify-center group"
                        :aria-label="tt('Khám phá ngay')"
                    >
                        <!-- Rotating Text SVG -->
                        <svg
                            viewBox="0 0 100 100"
                            class="w-full h-full animate-[spin_12s_linear_infinite] group-hover:animate-[spin_4s_linear_infinite] select-none"
                        >
                            <defs>
                                <path
                                    id="circlePath"
                                    d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0"
                                />
                            </defs>
                            <text class="text-[9.2px] uppercase font-medium tracking-[2.6px] fill-[#FAFAFA]">
                                <textPath href="#circlePath">
                                    {{ tt('FROM IDEAS TO REALITY - FROM IDEAS TO REALITY - ') }}
                                </textPath>
                            </text>
                        </svg>

                        <!-- Centered Transparent Icon (Figma 2261:5500: clean white 45° arrow, no solid blue disc) -->
                        <div
                            class="absolute inset-0 m-auto w-10 h-10 flex items-center justify-center text-[#FAFAFA] group-hover:scale-125 transition-transform duration-300"
                        >
                            <svg
                                class="w-4 h-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
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
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
const tt = (s) => s;

export default {
    name: 'HeroSection',
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
            default: tt('HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D'),
        },
        description: {
            type: String,
            default: tt('Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại'),
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
