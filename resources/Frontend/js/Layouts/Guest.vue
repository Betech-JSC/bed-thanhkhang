<template>
    <div>
        <Header />
        <slot />
        <Footer v-if="!isMigrated" />
        <SocialFixed />
        <NotificationPopup />
        <!-- <CursorIcon /> -->
    </div>
</template>

<script>
export default {
    components: {
    },
    computed: {
        isMigrated() {
            const comp = this.$page?.component || ''
            const url = this.$page?.url || ''
            return (
                comp === 'Home' ||
                comp === 'Home/Index' ||
                comp === 'About' ||
                comp === 'About/Index' ||
                comp === 'Product' ||
                comp === 'Product/Index' ||
                comp === 'Products' ||
                comp === 'Products/Index' ||
                url === '/' ||
                url === '' ||
                url.includes('gioi-thieu') ||
                url.includes('about') ||
                url.includes('san-pham') ||
                url.includes('products')
            )
        },
        isHome() {
            return this.isMigrated
        },
    },
    watch: {
        '$page.url': function (newUrl, oldUrl) {
            const seo = this.$page.props?.seo
            const global = this.$page.props?.global

            let seo_meta_title = seo?.seo_meta_title ?? global?.seo_meta_title ?? this.tt('HH Maker - In 3D & Chế Tác Theo Yêu Cầu')
            let seo_meta_description = seo?.seo_meta_description ?? global?.seo_meta_description ?? this.tt('Hiện thực hóa mọi ý tưởng với công nghệ in 3D hiện đại.')
            let seo_meta_robots = seo?.seo_meta_robots ?? global?.seo_meta_robots ?? 'robots.txt'
            let seo_meta_keywords = seo?.seo_meta_keywords ?? global?.seo_meta_keywords ?? 'HH Maker, in 3D, FDM, SLA'
            let seo_image = seo?.seo_image ?? (global?.seo_image && !global.seo_image.includes('funkystyle') && !global.seo_image.includes('khoqua') && !global.seo_image.includes('copy.jpg') ? global.seo_image : '/hhmaker-og-2026.jpg?v=20261110')

            document.querySelector('title').innerHTML = seo_meta_title
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

            document
                .querySelector('meta[property="og:image"]')
                ?.setAttribute('content', absoluteSeoImage)

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
