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
            return `${'https://funkystyle.vn'}/tai-nguyen/${this.slug}`
        },
        facebookShareUrl() {
            return `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(this.pageUrl)}`
        },
    },
}
</script>


