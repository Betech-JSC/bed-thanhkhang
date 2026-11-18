<template>
    <div class="bg-dark-bg text-white overflow-hidden selection:bg-primary selection:text-white">
        <!-- Head / SEO -->
        <Head>
            <title>{{ pageTitle }}</title>
            <meta name="description" :content="pageDescription" />
            <meta property="og:title" :content="pageTitle" />
            <meta property="og:description" :content="pageDescription" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- 1. Hero Section (Figma Node 2261:5475) -->
        <HeroSection
            :bannerImage="dynamicHeroBanner"
            :title="dynamicHeroTitle"
            :subtitle="dynamicHeroSubtitle"
            :description="dynamicHeroDescription"
            :primaryCtaText="dynamicPrimaryCtaText"
            :primaryCtaLink="dynamicPrimaryCtaLink"
            :secondaryCtaText="dynamicSecondaryCtaText"
            :secondaryCtaLink="dynamicSecondaryCtaLink"
        />

        <!-- Announcement Ticker (Flip 3D Mechanical Split-Flap) -->
        <AnnouncementTicker
            v-if="announcements && announcements.length > 0"
            :announcements="announcements"
        />

        <!-- 2. Featured Products Carousel (Figma Node 2261:5503: Khung tranh chứng chỉ RMIT, nút > điều hướng bên phải) -->
        <FeaturedProductsCarousel
            :items="products"
            :title="tt('SẢN PHẨM NỔI BẬT')"
        />

        <!-- 3. Services Section (Figma Node 2261:5528: 3D glass icons + cobalt gradient) -->
        <ServicesSection
            :heading="tt('TỔNG QUAN VỀ DỊCH VỤ')"
            :services="services"
        />

        <!-- 4. Category Products Section with 3 Carousel Rows (Figma Node 2261:5563: Móc khoá, Đồ dùng/Decor, Gia công) -->
        <ProductsSection
            :title="''"
            :categories="categories"
            :products="products"
        />

        <!-- 5. Brand Story Section (Figma Node 2261:5696) -->
        <BrandStorySection
            :founderImage="founderBanner?.image?.url || '/assets/images/homepage/founder-composite.png'"
            :subtitle="founderBanner?.subtitle ? tt(founderBanner.subtitle) : tt('VỀ THÀNH KHANG TECSAT')"
            :title="founderBanner?.title ? tt(founderBanner.title) : tt('ĐỒNG HÀNH CÙNG VẬN HÀNH CÔNG NGHIỆP BỀN BỈ')"
            :description="founderBanner?.description ? tt(founderBanner.description) : tt('Thành Khang Tecsat là đơn vị chuyên cung cấp giải pháp và thiết bị công nghiệp B2B hàng đầu trong lĩnh vực linh kiện thang cuốn - thang máy, hệ thống băng tải và xích tải công nghiệp Universal Chains. /n Bên cạnh vật tư truyền động chính xác, chúng tôi còn tiên phong triển khai giải pháp tiết kiệm điện với dòng quạt trần công nghiệp BLDC HVLS, cam kết chất lượng chuẩn OEM và dịch vụ kỹ thuật tận tâm.')"
        />

        <!-- 6. 5-Step Process Section (Figma Node 2261:5723) -->
        <ProcessSection
            :processImage="'/assets/images/homepage/process-banner-figma.png'"
            :stepCount="'05'"
            :title="tt('BƯỚC TRONG QUY TRÌNH CUNG ỨNG CHUYÊN NGHIỆP & MINH BẠCH TẠI THÀNH KHANG TECSAT')"
            :description="tt('Đối tác gửi mã linh kiện, thông số bước xích hoặc bản vẽ kỹ thuật, đội ngũ kỹ sư Thành Khang Tecsat sẽ đối chiếu chuẩn OEM, tư vấn vật liệu và giải pháp vận hành tối ưu cùng chứng chỉ CO/CQ đầy đủ.')"
            :steps="processSteps"
            :customSteps="processSteps"
        />

        <!-- 7. Fast Consultation Form Section (Figma Node 2261:5755) -->
        <ContactSection
            :title="tt('KẾT NỐI VỚI THÀNH KHANG TECSAT')"
            :description="tt('Bạn cần tư vấn phụ tùng thang cuốn, xích công nghiệp hay quạt BLDC? Hãy gửi thông tin yêu cầu cho chúng tôi qua các kênh bên dưới')"
        />
    </div>
</template>

<script>
import HeroSection from './Components/HeroSection.vue';
import AnnouncementTicker from './Components/AnnouncementTicker.vue';
import FeaturedProductsCarousel from './Components/FeaturedProductsCarousel.vue';
import ServicesSection from './Components/ServicesSection.vue';
import ProductsSection from './Components/ProductsSection.vue';
import BrandStorySection from './Components/BrandStorySection.vue';
import ProcessSection from './Components/ProcessSection.vue';
import ContactSection from './Components/ContactSection.vue';

export default {
    name: 'HomePage',
    components: {
        HeroSection,
        AnnouncementTicker,
        FeaturedProductsCarousel,
        ServicesSection,
        ProductsSection,
        BrandStorySection,
        ProcessSection,
        ContactSection,
    },
    props: {
        announcements: {
            type: Array,
            default: () => [],
        },
        sliders: {
            type: [Array, Object],
            default: () => [],
        },
        founderBanner: {
            type: Object,
            default: () => null,
        },
        services: {
            type: Array,
            default: () => [],
        },
        categories: {
            type: Array,
            default: () => [],
        },
        products: {
            type: Array,
            default: () => [],
        },
        featured_posts: {
            type: Array,
            default: () => [],
        },
        feedback: {
            type: Array,
            default: () => [],
        },
        processSteps: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            heroBanner: '/assets/images/homepage/hero-banner.png',
            heroSubtitle: this.tt('VỀ CHÚNG TÔI'),
            heroTitle: this.tt('GIẢI PHÁP THIẾT BỊ CÔNG NGHIỆP & PHỤ TÙNG THANG CUỐN CHUẨN OEM'),
            heroDescription: this.tt('Thành Khang Tecsat cung cấp linh kiện thang cuốn, xích công nghiệp Universal Chains, hệ thống băng tải và quạt trần công nghiệp BLDC HVLS với đầy đủ chứng từ CO/CQ'),
        };
    },
    computed: {
        pageTitle() {
            return this.tt('Thành Khang Tecsat - Thiết bị công nghiệp & Phụ tùng thang cuốn');
        },
        pageDescription() {
            return this.tt('Cung cấp linh kiện thang cuốn, thang nâng ô tô, xích công nghiệp Universal Chains, hệ thống băng tải và quạt trần công nghiệp BLDC HVLS.');
        },
        activeSlider() {
            if (Array.isArray(this.sliders) && this.sliders.length > 0) {
                return this.sliders[0];
            }
            if (this.sliders && typeof this.sliders === 'object' && this.sliders.title) {
                return this.sliders;
            }
            return null;
        },
        dynamicHeroBanner() {
            const incomingUrl = this.activeSlider?.image?.url;
            if (incomingUrl) {
                if (incomingUrl.includes('/static/assets/')) {
                    return incomingUrl.replace(/^https?:\/\/[^\/]+/, '').replace('/static/', '/');
                } else if (incomingUrl.startsWith('http://localhost') && !incomingUrl.includes('/public/')) {
                    return this.heroBanner;
                }
                return incomingUrl;
            }
            return this.heroBanner;
        },
        dynamicHeroTitle() {
            if (this.activeSlider?.title && !this.activeSlider.title.includes('HH Maker')) {
                return this.tt(this.activeSlider.title);
            }
            return this.tt(this.heroTitle);
        },
        dynamicHeroSubtitle() {
            return this.tt(this.activeSlider?.subtitle || this.heroSubtitle);
        },
        dynamicHeroDescription() {
            if (this.activeSlider?.description && !this.activeSlider.description.includes('Giải pháp gia công')) {
                return this.tt(this.activeSlider.description);
            }
            return this.tt(this.heroDescription);
        },
        dynamicPrimaryCtaText() {
            return this.activeSlider?.primary_cta_text ? this.tt(this.activeSlider.primary_cta_text) : this.tt('Khám phá ngay');
        },
        dynamicPrimaryCtaLink() {
            return this.activeSlider?.primary_cta_link || '#featured-carousel';
        },
        dynamicSecondaryCtaText() {
            return this.activeSlider?.secondary_cta_text ? this.tt(this.activeSlider.secondary_cta_text) : this.tt('Nhận tư vấn thiết kế');
        },
        dynamicSecondaryCtaLink() {
            return this.activeSlider?.secondary_cta_link || '#contact-section';
        },
    },
};
</script>
