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
            :founderImage="'/assets/images/homepage/founder-composite.png'"
            :subtitle="tt('CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER')"
            :title="tt('HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH')"
            :description="tt('Xuất phát từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại, tôi thành lập HH Maker với sứ mệnh rút ngắn khoảng cách giữa ý tưởng sơ khai và sản phẩm vật lý. /n Với mỗi dự án, tôi cùng đội ngũ luôn tỉ mỉ trong từng đường nét thiết kế, lựa chọn vật liệu và kiểm soát quy trình in để mang lại những sản phẩm sắc nét, chuẩn xác và mang đậm dấu ấn cá nhân nhất.')"
        />

        <!-- 6. 5-Step Process Section (Figma Node 2261:5723) -->
        <ProcessSection
            :processImage="'/assets/images/homepage/process-banner-figma.png'"
            :stepCount="'05'"
            :title="tt('BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER')"
            :description="tt('Khách hàng gửi file 3D hoặc chia sẻ ý tưởng, đội ngũ kỹ thuật của HH Maker sẽ tiến hành phân tích độ khả thi, tư vấn công nghệ in tối ưu, cũng như lựa chọn loại vật liệu phù hợp nhất để đảm bảo sản phẩm đạt độ bền, tính thẩm mỹ và công năng sử dụng đúng như kỳ vọng của bạn.')"
            :steps="processSteps"
            :customSteps="processSteps"
        />

        <!-- 7. Fast Consultation Form Section (Figma Node 2261:5755) -->
        <ContactSection
            :title="tt('KẾT NỐI VỚI HH MAKER')"
            :description="tt('Bạn cần tư vấn về in 3D? Hãy chia sẻ nhu cầu & ý tưởng của bạn với HH MAKER qua các kênh bên dưới')"
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
            heroTitle: this.tt('HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D'),
            heroDescription: this.tt('Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại'),
        };
    },
    computed: {
        pageTitle() {
            return this.tt('HH Maker - Hiện Thực Hóa Mọi Ý Tưởng Với Công Nghệ In 3D');
        },
        pageDescription() {
            return this.tt('Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại.');
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
            if (this.activeSlider?.title && !this.activeSlider.title.includes('HH Maker - Dịch vụ')) {
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
