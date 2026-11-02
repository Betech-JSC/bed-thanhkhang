<template>
    <div class="relative w-full h-full project-slider-zoom ">
        <div class="relative h-full overflow-hidden group">
            <swiper :modules="modules" :slides-per-view="1" :space-between="20" :loop="true" :navigation="{
                prevEl: '.swiper-button-prev-custom',
                nextEl: '.swiper-button-next-custom',
            }" centered-slides="true" :breakpoints="{
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20
                },
                768: {
                    slidesPerView: 1.2,
                    spaceBetween: 30
                },
                1024: {
                    slidesPerView: 2.2,
                    spaceBetween: 24
                },
                1440: {
                    slidesPerView: 3,
                    spaceBetween: 24
                },
                1920: {
                    slidesPerView: 3,
                    spaceBetween: 32
                }
            }" class="h-full w-full flex items-center justify-center" @swiper="onSwiper" @slideChange="onSlideChange">
                <swiper-slide v-for="(image, index) in images" :key="index"
                    class="h-full w-full relative overflow-hidden">
                    <div class="relative w-full h-full overflow-hidden"
                        :class="isActiveSlide(index) ? 'cursor-none' : 'cursor-default'"
                        @mousemove="isActiveSlide(index) ? handleMouseMove($event) : null"
                        @mouseenter="isActiveSlide(index) ? showMagnifier = true : null"
                        @mouseleave="showMagnifier = false">
                        <img :src="image.url || '/assets/images/cover.jpg'" :alt="image.alt || 'project image'"
                            class="h-full w-full object-contain" ref="imageRefs" />

                        <!-- Magnifier Circle - Only show on active slide -->
                        <div v-show="showMagnifier && isActiveSlide(index)"
                            class="magnifier-circle pointer-events-none absolute rounded-full border-2 border-dark-bg shadow-2xl overflow-hidden"
                            :style="magnifierStyle">
                            <img :src="image.url || '/assets/images/cover.jpg'" :alt="image.alt || 'project image'"
                                class="absolute top-0 left-0 max-w-none max-h-none object-contain"
                                :style="magnifierImageStyle" />
                        </div>
                    </div>
                </swiper-slide>
            </swiper>

            <!-- Custom Navigation Buttons -->
            <button v-if="images.length > 1"
                class="swiper-button-prev-custom absolute left-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button v-if="images.length > 1"
                class="swiper-button-next-custom absolute right-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
    </div>
</template>

<script>
import { ref, computed } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation } from 'swiper/modules';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';

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
        const swiperInstance = ref(null);
        const activeIndex = ref(0);

        const magnifierSize = 200;
        const zoomLevel = 3.8;

        const onSwiper = (swiper) => {
            swiperInstance.value = swiper;
            activeIndex.value = swiper.realIndex;
        };

        const onSlideChange = (swiper) => {
            activeIndex.value = swiper.realIndex;
            showMagnifier.value = false; // Hide magnifier when changing slides
        };

        const isActiveSlide = (index) => {
            if (!swiperInstance.value) return false;
            return activeIndex.value === index;
        };

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
                transform: `translate(${magnifierSize / 2 - mouseX.value * zoomLevel}px, ${magnifierSize / 2 - mouseY.value * zoomLevel}px)`,
            };
        });

        return {
            modules: [Navigation],
            showMagnifier,
            mouseX,
            mouseY,
            imageRefs,
            magnifierStyle,
            magnifierImageStyle,
            handleMouseMove,
            onSwiper,
            onSlideChange,
            isActiveSlide,
        };
    },
};
</script>

<style lang="scss" scoped>
.project-slider-zoom {
    :deep(.swiper) {
        @apply h-[600px];

        .swiper-slide {
            @apply h-[600px];
        }
    }
}

.magnifier-circle {
    z-index: 50;
    pointer-events: none;
    background-color: white;
}
</style>
