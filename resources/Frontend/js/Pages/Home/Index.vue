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

        <!-- 2. Featured Products Carousel (Figma Node 2261:5503: Khung tranh chứng chỉ RMIT, nút > điều hướng bên phải) -->
        <FeaturedProductsCarousel
            :title="'SẢN PHẨM NỔI BẬT'"
        />

        <!-- 3. Services Section (Figma Node 2261:5528: 3D glass icons + cobalt gradient) -->
        <ServicesSection
            :heading="'TỔNG QUAN VỀ DỊCH VỤ'"
            :services="services"
        />

        <!-- 4. Category Products Section with 3 Carousel Rows (Figma Node 2261:5563: Móc khoá, Đồ dùng/Decor, Gia công) -->
        <ProductsSection
            :title="''"
            :products="products"
        />

        <!-- 5. Brand Story Section (Figma Node 2261:5696) -->
        <BrandStorySection
            :founderImage="'/assets/images/homepage/founder-composite.png'"
            :subtitle="'CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER'"
            :title="'HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH'"
        />

        <!-- 6. 5-Step Process Section (Figma Node 2261:5723) -->
        <ProcessSection
            :processImage="'/assets/images/homepage/process-banner.png'"
            :stepCount="'05'"
            :title="'BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER'"
            :description="'Khách hàng gửi file 3D hoặc chia sẻ ý tưởng, đội ngũ kỹ thuật của HH Maker sẽ tiến hành phân tích độ khả thi, tư vấn công nghệ in tối ưu, cũng như lựa chọn loại vật liệu phù hợp nhất để đảm bảo sản phẩm đạt độ bền, tính thẩm mỹ và công năng sử dụng đúng như kỳ vọng của bạn.'"
        />

        <!-- 7. Fast Consultation Form Section (Figma Node 2261:5755) -->
        <ContactSection
            :title="'KẾT NỐI VỚI HH MAKER'"
            :description="'Bạn cần tư vấn về in 3D? Hãy chia sẻ nhu cầu & ý tưởng của bạn với HH MAKER qua các kênh bên dưới'"
        />
    </div>
</template>

<script>
import HeroSection from './Components/HeroSection.vue';
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
        FeaturedProductsCarousel,
        ServicesSection,
        ProductsSection,
        BrandStorySection,
        ProcessSection,
        ContactSection,
    },
    props: {
        sliders: {
            type: [Array, Object],
            default: () => [],
        },
        services: {
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
    },
    data() {
        return {
            pageTitle: 'HH Maker - Hiện Thực Hóa Mọi Ý Tưởng Với Công Nghệ In 3D',
            pageDescription:
                'Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại.',
            heroBanner: '/assets/images/homepage/hero-banner.png',
            heroSubtitle: 'VỀ CHÚNG TÔI',
            heroTitle: 'HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D',
            heroDescription:
                'Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại',
        };
    },
    computed: {
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
            return this.activeSlider?.title || this.heroTitle;
        },
        dynamicHeroSubtitle() {
            return this.activeSlider?.subtitle || this.heroSubtitle;
        },
        dynamicHeroDescription() {
            return this.activeSlider?.description || this.heroDescription;
        },
        dynamicPrimaryCtaText() {
            return this.activeSlider?.primary_cta_text || 'Khám phá ngay';
        },
        dynamicPrimaryCtaLink() {
            return this.activeSlider?.primary_cta_link || '#featured-carousel';
        },
        dynamicSecondaryCtaText() {
            return this.activeSlider?.secondary_cta_text || 'Nhận tư vấn thiết kế';
        },
        dynamicSecondaryCtaLink() {
            return this.activeSlider?.secondary_cta_link || '#contact-section';
        },
    },
};
</script>
