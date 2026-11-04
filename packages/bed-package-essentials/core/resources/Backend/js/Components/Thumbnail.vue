<template>
    <div class="flex items-center w-full h-full">
        <!-- Folder Icon -->
        <div v-if="file.type === 'dir'" class="flex items-center justify-center w-full h-full text-blue-500">
            <ph:folder-notch-open class="w-20 h-20" />
        </div>
        <!-- Video -->
        <vue-plyr v-else-if="isVideo(staticUrl(file.path))">
            <div>
                <video muted playsinline onmouseover="this.play()" onmouseout="this.pause()">
                    <source :src="staticUrl(file.path)" type="video/mp4" />
                </video>
            </div>
        </vue-plyr>
        <!-- Image -->
        <v-lazy-image v-else :src="`${staticUrl(file.path)}?w=200`" class="object-contain w-full h-full" />

        <!-- Actions for files only -->
        <template v-if="file.type !== 'dir'">
            <a
                class="absolute top-0 right-0 invisible space-x-1 text-white uppercase bg-black group-hover:visible w-[20px] h-[20px] flex items-center justify-center rounded-sm"
                :href="staticUrl(file.path)"
                target="_blank"
            >
                <ph:arrow-square-up-right />
            </a>
        </template>

        <Button
            class="absolute invisible right-1 bottom-1 group-hover:visible btn-white btn-sm"
            :label="tt('models.files.delete')"
            @click.stop="$emit('remove', file)"
        />
    </div>
</template>

<script>
import VLazyImage from 'v-lazy-image'
import VuePlyr from 'vue-plyr'
import 'vue-plyr/dist/vue-plyr.css'
export default {
    components: { VLazyImage, VuePlyr },
    props: ['file'],

    methods: {
        isVideo(url) {
            return (
                url.endsWith('.mp4') ||
                url.endsWith('.avi') ||
                url.endsWith('.mov') ||
                url.endsWith('.wmv') ||
                url.endsWith('.flv') ||
                url.endsWith('.mkv')
            )
        },
    },
}
</script>

<style scoped>
.v-lazy-image {
    filter: blur(10px);
    transition: filter 0.5s cubic-bezier(0.65, 0, 0.35, 1);
    will-change: filter;
}
.v-lazy-image-loaded {
    filter: blur(0);
}
</style>
