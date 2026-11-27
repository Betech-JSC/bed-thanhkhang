<template>
    <div
        class="min-h-screen flex flex-col bg-surface text-ink-body font-sans antialiased selection:bg-primary selection:text-white"
        style="--header-height-desktop: 118px; --header-height-mobile: 92px;"
    >
        <Header />
        <main id="main-content" class="flex-grow">
            <slot />
        </main>
        <Footer />
        <FloatingCtaWidget />
        <QuotePopupModal />
    </div>
</template>

<script>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import FloatingCtaWidget from '@/Components/FloatingCtaWidget.vue'
import QuotePopupModal from '@/Components/QuotePopupModal.vue'

export default {
    name: 'MainLayout',
    components: {
        Header,
        Footer,
        FloatingCtaWidget,
        QuotePopupModal,
    },
    watch: {
        '$page.url': function () {
            if (typeof document === 'undefined') return

            const seo = this.$page.props?.seo || {}
            const global = {
                ...(this.$page.props?.global || {}),
                ...(this.$page.props?.configs || {}),
            }

            const seo_meta_title =
                seo.seo_meta_title ||
                seo.title ||
                global.seo_meta_title ||
                global.general_site_name ||
                global.general_business_name ||
                ''
            const seo_meta_description =
                seo.seo_meta_description ||
                seo.description ||
                global.seo_meta_description ||
                global.general_description ||
                ''
            const seo_meta_robots =
                seo.seo_meta_robots || seo.robots || global.seo_meta_robots || 'index, follow'
            const seo_meta_keywords =
                seo.seo_meta_keywords || seo.keywords || global.seo_meta_keywords || ''
            const seo_image = seo.seo_image || seo.image || global.seo_image || ''
            const seo_canonical = seo.seo_canonical || seo.canonical || window.location.href

            if (seo_meta_title) {
                const titleEl = document.querySelector('title')
                if (titleEl) titleEl.textContent = seo_meta_title
                document.querySelector('meta[property="og:title"]')?.setAttribute('content', seo_meta_title)
                document.querySelector('meta[name="twitter:title"]')?.setAttribute('content', seo_meta_title)
            }

            if (seo_meta_description) {
                document.querySelector('meta[name="description"]')?.setAttribute('content', seo_meta_description)
                document
                    .querySelector('meta[property="og:description"]')
                    ?.setAttribute('content', seo_meta_description)
                document
                    .querySelector('meta[name="twitter:description"]')
                    ?.setAttribute('content', seo_meta_description)
            }

            document.querySelector('meta[name="robots"]')?.setAttribute('content', seo_meta_robots)

            if (seo_meta_keywords) {
                document.querySelector('meta[name="keywords"]')?.setAttribute('content', seo_meta_keywords)
            }

            if (seo_image) {
                let absoluteSeoImage = seo_image
                if (!seo_image.startsWith('http://') && !seo_image.startsWith('https://')) {
                    absoluteSeoImage = window.location.origin + (seo_image.startsWith('/') ? '' : '/') + seo_image
                }
                document.querySelector('meta[property="og:image"]')?.setAttribute('content', absoluteSeoImage)
                document.querySelector('meta[name="twitter:image"]')?.setAttribute('content', absoluteSeoImage)
            }

            document.querySelector('meta[property="og:url"]')?.setAttribute('content', window.location.href)
            document.querySelector('link[rel="canonical"]')?.setAttribute('href', seo_canonical)
        },
    },
}
</script>

<style>
body.overflow-hidden {
    overflow: hidden;
}
</style>
