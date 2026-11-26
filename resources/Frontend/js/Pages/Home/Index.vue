<template>
    <div class="bg-surface text-ink-body overflow-x-clip selection:bg-primary selection:text-white">
        <!-- Head / SEO -->
        <Head>
            <title v-if="pageTitle">{{ pageTitle }}</title>
            <meta v-if="pageDescription" name="description" :content="pageDescription" />
            <meta v-if="pageTitle" property="og:title" :content="pageTitle" />
            <meta v-if="pageDescription" property="og:description" :content="pageDescription" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- 1. Hero Banner Section (Figma Node 1171:41716 / 1190:6357 — CMS HOME_SLIDER) -->
        <HeroSection :sliders="normalizedSliders" />

        <!-- Body Sections Wrapper (Figma Node 1171:41527 — 80px desktop vertical rhythm & bottom gap before Footer, 0px mobile outer gap) -->
        <div class="flex flex-col gap-0 lg:gap-[80px] lg:pb-[80px] bg-[#FAFAFA]">
            <!-- 2. Product Categories Section (Figma Node 1171:41528 / 1190:6392 — CMS categories & categoriesCtaBanner) -->
            <ProductsSection
                :categories="categories"
                :cta-banner="categoriesCtaBanner"
            />

            <!-- 3. About Intro Section (Figma Node 1171:41542 / 1190:6454 — CMS aboutSection) -->
            <BrandStorySection :about="aboutSection" />

            <!-- 4. Why Choose Us Section (Figma Node 1171:41560 / 1191:6399 — CMS trustSection) -->
            <ServicesSection :trust="trustSection" />

            <!-- 5. Process Section (Figma Node 1171:41607 / 1191:6449 — CMS processSection) -->
            <ProcessSection :process="processSection" />

            <!-- 6. Blog / News Section (Figma Node 1171:41649 / 1191:6474 — CMS featured_posts) -->
            <BlogSection :posts="featured_posts" />

            <!-- 7. Contact & Quote Form Section (Figma Node 1171:41662 / 1193:6488 — CMS contactSection) -->
            <ContactSection :contact-section="contactSection" />
        </div>
    </div>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'
import HeroSection from './Components/HeroSection.vue'
import BrandStorySection from './Components/BrandStorySection.vue'
import ProductsSection from './Components/ProductsSection.vue'
import ServicesSection from './Components/ServicesSection.vue'
import ProcessSection from './Components/ProcessSection.vue'
import BlogSection from './Components/BlogSection.vue'
import ContactSection from './Components/ContactSection.vue'

export default {
    name: 'HomePage',
    layout: MainLayout,
    components: {
        HeroSection,
        BrandStorySection,
        ProductsSection,
        ServicesSection,
        ProcessSection,
        BlogSection,
        ContactSection,
    },
    props: {
        sliders: {
            type: [Array, Object],
            default: () => [],
        },
        aboutSection: {
            type: Object,
            default: () => null,
        },
        categories: {
            type: Array,
            default: () => [],
        },
        categoriesCtaBanner: {
            type: Object,
            default: () => null,
        },
        trustSection: {
            type: Object,
            default: () => null,
        },
        processSection: {
            type: Object,
            default: () => null,
        },
        featured_posts: {
            type: Array,
            default: () => [],
        },
        contactSection: {
            type: Object,
            default: () => null,
        },
        seo: {
            type: Object,
            default: () => null,
        },
    },
    computed: {
        resolvedSeo() {
            return this.seo || this.$page?.props?.seo || {}
        },
        pageTitle() {
            return (
                this.resolvedSeo.seo_meta_title ||
                this.resolvedSeo.title ||
                this.$page?.props?.global?.seo_meta_title ||
                this.$page?.props?.global?.general_site_name ||
                this.$page?.props?.global?.general_business_name ||
                ''
            )
        },
        pageDescription() {
            return (
                this.resolvedSeo.seo_meta_description ||
                this.resolvedSeo.description ||
                this.$page?.props?.global?.seo_meta_description ||
                this.$page?.props?.global?.general_description ||
                ''
            )
        },
        normalizedSliders() {
            if (Array.isArray(this.sliders)) {
                return this.sliders.filter((item) => item && typeof item === 'object')
            }
            if (this.sliders && typeof this.sliders === 'object' && Object.keys(this.sliders).length > 0) {
                return [this.sliders]
            }
            return []
        },
    },
}
</script>
