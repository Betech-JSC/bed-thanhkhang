<template>
    <div class="relative w-full h-full project-slider-zoom ">
        <div class="relative h-full max-h-[525px] rounded-[32px] overflow-hidden bg-gray-200">
            <swiper :modules="modules" :slides-per-view="1" :space-between="0" :loop="true" :pagination="{
                clickable: true,
                el: '.swiper-pagination-custom'
            }" class="h-full w-full">
                <swiper-slide v-for="(image, index) in images" :key="index" class="h-full w-full">
                    <div class="relative w-full h-full overflow-hidden cursor-none" @mousemove="handleMouseMove"
                        @mouseenter="showMagnifier = true" @mouseleave="showMagnifier = false">
                        <img :src="image.url || '/assets/images/cover.jpg'" :alt="image.alt || 'project image'"
                            class="h-full w-full object-contain" ref="imageRefs" />

                        <!-- Magnifier Circle -->
                        <div v-show="showMagnifier"
                            class="magnifier-circle pointer-events-none absolute rounded-full border-2 border-dark-bg shadow-2xl overflow-hidden"
                            :style="magnifierStyle">
                            <img :src="image.url || '/assets/images/cover.jpg'" :alt="image.alt || 'project image'"
                                class="absolute top-0 left-0 max-w-none max-h-none object-contain"
                                :style="magnifierImageStyle" />
                        </div>
                    </div>
                </swiper-slide>
            </swiper>
        </div>
        <div v-if="images.length > 1" class="swiper-pagination-custom relative !inset-0 z-[15] flex gap-2"></div>

    </div>
</template>

<script>
import { ref, computed } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Pagination } from 'swiper/modules';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';

export default {
    components: {
        Swiper,
        SwiperSlide,
    },
    props: {
        images: {
            type: Array,
            default: () => []
        }
    },
    setup() {
        const showMagnifier = ref(false);
        const mouseX = ref(0);
        const mouseY = ref(0);
        const containerWidth = ref(0);
        const containerHeight = ref(0);
        const imageRefs = ref([]);

        const magnifierSize = 160; // Increased for better visibility
        const zoomLevel = 3.5;

        const handleMouseMove = (event) => {
            const rect = event.currentTarget.getBoundingClientRect();
            mouseX.value = event.clientX - rect.left;
            mouseY.value = event.clientY - rect.top;
            containerWidth.value = rect.width;
            containerHeight.value = rect.height;
        };

        const magnifierStyle = computed(() => ({
            width: `${magnifierSize}px`,
            height: `${magnifierSize}px`,
            left: `${mouseX.value - magnifierSize / 2}px`,
            top: `${mouseY.value - magnifierSize / 2}px`,
        }));

        const magnifierImageStyle = computed(() => {
            const zoomedWidth = containerWidth.value * zoomLevel;
            const zoomedHeight = containerHeight.value * zoomLevel;

            return {
                width: `${zoomedWidth}px`,
                height: `${zoomedHeight}px`,
                // We use transform to shift the zoomed image so the mouse point is centered in the loupe
                transform: `translate(${magnifierSize / 2 - mouseX.value * zoomLevel}px, ${magnifierSize / 2 - mouseY.value * zoomLevel}px)`,
            };
        });

        return {
            modules: [Pagination],
            showMagnifier,
            mouseX,
            mouseY,
            imageRefs,
            magnifierStyle,
            magnifierImageStyle,
            handleMouseMove,
        };
    },
};
</script>

<style lang="scss" scoped>
.project-slider-zoom {
    :deep(.swiper-pagination-custom) {
        @apply mx-auto flex items-center justify-center gap-2 translate-y-5;

        .swiper-pagination-bullet {
            @apply w-2.5 h-2.5 bg-white opacity-100 rounded-full transition-all ease-in-out cursor-pointer;

            &-active {
                @apply bg-gray-500 opacity-100;
            }
        }
    }
}

.magnifier-circle {
    z-index: 50;
    pointer-events: none;
    background-color: white;
}
</style>
