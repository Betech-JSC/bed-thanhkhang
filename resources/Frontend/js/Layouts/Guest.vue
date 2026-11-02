<template>
    <div>
        <LoadingPage v-if="isProduction && isLoading" />
        <Header />
        <slot />
        <Footer />
        <SocialFixed />
        <NotificationPopup />
        <DailyRewardPopup />
        <!-- <CursorIcon /> -->
    </div>
</template>

<script>
import LoadingPage from '@/Components/LoadingPage.vue'
import DailyRewardPopup from '@/Components/DailyRewardPopup.vue'

export default {
    components: {
        LoadingPage,
        DailyRewardPopup,
    },
    data() {
        return {
            isLoading: false,
            isProduction: import.meta.env.PROD,
        }
    },
    created() {
        if (this.isProduction && this.$attrs.route.path === '/') {
            this.isLoading = true
            document.body.classList.add('overflow-hidden')
            setTimeout(() => {
                this.isLoading = false
                document.body.classList.remove('overflow-hidden')
            }, 3000)
        }
    },
    watch: {
        isLoading(newValue) {
            if (newValue) {
                document.body.classList.add('overflow-hidden')
            } else {
                document.body.classList.remove('overflow-hidden')
            }
        },

        '$page.url': function (newUrl, oldUrl) {
            const seo = this.$page.props?.seo
            const global = this.$page.props?.global

            let seo_meta_title = seo?.seo_meta_title ?? global?.seo_meta_title ?? 'FunkyStyle'
            let seo_meta_description = seo?.seo_meta_description ?? global?.seo_meta_description ?? 'FunkyStyle'
            let seo_meta_robots = seo?.seo_meta_robots ?? global?.seo_meta_robots ?? 'robots.txt'
            let seo_meta_keywords = seo?.seo_meta_keywords ?? global?.seo_meta_keywords ?? 'FunkyStyle'
            let seo_image = seo?.seo_image ?? global?.seo_image ?? '/cover.jpg'

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
