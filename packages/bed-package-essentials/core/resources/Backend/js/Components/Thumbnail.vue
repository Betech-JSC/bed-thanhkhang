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
                class="absolute top-1.5 right-1.5 z-10 w-9 h-9 min-w-[36px] min-h-[36px] rounded-md bg-black/75 hover:bg-black text-white flex items-center justify-center opacity-100 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity focus:outline-none focus:ring-2 focus:ring-primary/40"
                :href="staticUrl(file.path)"
                target="_blank"
                rel="noopener noreferrer"
                :aria-label="tt('models.files.open')"
                @click.stop
            >
                <ph:arrow-square-up-right class="w-4 h-4" />
            </a>
        </template>

        <Button
            class="absolute right-1.5 bottom-1.5 z-10 btn-white btn-sm !min-w-0 !min-h-[36px] !px-2.5 text-gray-700 shadow-sm opacity-100 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity"
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
