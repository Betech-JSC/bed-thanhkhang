<template>
    <div class="btn btn-primary social-share">
        <a :href="facebookShareUrl" target="_blank" rel="noopener noreferrer nofollow"> Chia sẻ </a>
    </div>
</template>

<script>
export default {
    name: 'SocialShare',
    props: {
        slug: {
            type: String,
            required: true,
        },
    },
    computed: {
        pageUrl() {
            if (typeof window !== 'undefined') {
                return window.location.href
            }
            return `https://dvktthanhkhang.vn/tin-tuc/${this.slug}`
        },
        facebookShareUrl() {
            return `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(this.pageUrl)}`
        },
    },
}
</script>


