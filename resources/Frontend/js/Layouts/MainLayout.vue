<template>
    <div class="min-h-screen flex flex-col bg-dark-bg text-secondary font-sans selection:bg-primary selection:text-white">
        <Header />
        <main class="flex-grow pt-[var(--header-height-md)] lg:pt-[var(--header-height-lg)] xl:pt-[var(--header-height-xl)]">
            <slot />
        </main>
        <Footer />
        <NotificationPopup />
    </div>
</template>

<script>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import NotificationPopup from '@/Components/NotificationPopup.vue'

export default {
    name: 'MainLayout',
    components: {
        Header,
        Footer,
        NotificationPopup,
    },
    watch: {
        '$page.url': function () {
            if (typeof document === 'undefined') return

            const seo = this.$page.props?.seo
            const global = this.$page.props?.global

            const seo_meta_title = seo?.seo_meta_title ?? global?.seo_meta_title ?? this.tt('HH Maker - In 3D & Chế Tác Theo Yêu Cầu')
            const seo_meta_description = seo?.seo_meta_description ?? global?.seo_meta_description ?? this.tt('Hiện thực hóa mọi ý tưởng với công nghệ in 3D hiện đại.')
            const seo_meta_robots = seo?.seo_meta_robots ?? global?.seo_meta_robots ?? 'robots.txt'
            const seo_meta_keywords = seo?.seo_meta_keywords ?? global?.seo_meta_keywords ?? 'HH Maker, in 3D, FDM, SLA'
            const seo_image = seo?.seo_image ?? (global?.seo_image && !global.seo_image.includes('funkystyle') && !global.seo_image.includes('khoqua') && !global.seo_image.includes('copy.jpg') ? global.seo_image : '/hhmaker-og-2026.jpg?v=20261110')

            const titleEl = document.querySelector('title')
            if (titleEl) titleEl.innerHTML = seo_meta_title
            document.querySelector('meta[property="og:title"]')?.setAttribute('content', seo_meta_title)
            document.querySelector('meta[name="twitter:title"]')?.setAttribute('content', seo_meta_title)

            document.querySelector('meta[name="description"]')?.setAttribute('content', seo_meta_description)
            document.querySelector('meta[property="og:description"]')?.setAttribute('content', seo_meta_description)
            document.querySelector('meta[name="twitter:description"]')?.setAttribute('content', seo_meta_description)

            document.querySelector('meta[name="robots"]')?.setAttribute('content', seo_meta_robots)
            document.querySelector('meta[name="keywords"]')?.setAttribute('content', seo_meta_keywords)

            let absoluteSeoImage = seo_image
            if (seo_image && !seo_image.startsWith('http://') && !seo_image.startsWith('https://')) {
                absoluteSeoImage = window.location.origin + (seo_image.startsWith('/') ? '' : '/') + seo_image
            }

            document.querySelector('meta[property="og:image"]')?.setAttribute('content', absoluteSeoImage)
            document.querySelector('meta[property="og:url"]')?.setAttribute('content', window.location.href)
            document.querySelector('link[rel="canonical"]')?.setAttribute('href', window.location.href)
        },
    },
}
</script>

<style>
/* Đảm bảo lớp overflow-hidden ngăn cuộn */
body.overflow-hidden {
    overflow: hidden;
}
</style>
