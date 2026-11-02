<template>
    <Banner
        v-if="$page.props.data.banner.banner_feedback"
        :image="$page.props.data.banner.banner_feedback.image?.url"
        :imageMobile="$page.props.data.banner.banner_feedback.image_mobile?.url"
        :alt="$page.props.data.banner.banner_feedback.title"
    >
        <div>
            <div class="space-y-3 max-w-[470px] w-full text-white">
                <h1 class="display-2 uppercase font-display">
                    {{ $page.props.data.banner.banner_feedback.title }}
                </h1>
                <p class="body-1 font-beau">
                    {{ $page.props.data.banner.banner_feedback.description }}
                </p>
            </div>
        </div>
    </Banner>
    <section class="container py-12">
        <div class="grid md:grid-cols-3 grid-cols-2 gap-4">
            <div v-for="(item, index) in feedbacks" :key="index" class="w-full group col-span-1">
                <div class="p-3 rounded-3xl bg-white border border-gray-warm-300 space-y-3 order-1 md:order-2 h-full">
                    <div class="flex items-center gap-x-2">
                        <img :src="item.image.url || ''" :alt="item.image.alt || ''" class="w-10 h-10 rounded-full" />
                        <div>
                            <p class="label-1 text-black-fks font-medium font-beau">
                                {{ item.title }}
                            </p>
                            <p class="body-2 text-black-fks font-medium font-beau">
                                {{ item.description }}
                            </p>
                        </div>
                    </div>
                    <p class="body-2 props-feedback prose" v-html="item.content"></p>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    components: {},
    props: ['feedbacks'],
    data() {
        return {
            galleryCol: [], // Thêm vào data
        }
    },
}
</script>
<style scoped>
.bg-about {
    border-bottom: 1px solid #dde1e6;
    background: linear-gradient(180deg, #fff 0%, rgba(255, 255, 255, 0) 100%),
        url('/public/assets/images/shap-banner.png') repeat;
}
.shape-top-course {
    display: flex;
    padding: 0px 0.047px 0.058px 0px;
    flex-direction: column;
    align-items: center;
    mix-blend-mode: exclusion;
}
</style>
