<template>
    <div class="relative w-full h-full prize-slider-zoom">
        <div class="relative h-full max-h-[320px] md:max-h-[525px] lg:max-h-[500px] xl:max-h-[620px] overflow-hidden">
            <swiper :modules="modules" :slides-per-view="1" :space-between="20" :loop="true" :navigation="{
                prevEl: '.swiper-button-prev-custom',
                nextEl: '.swiper-button-next-custom',
            }" :breakpoints="{
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 1.2,
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 1.3,
                    spaceBetween: 24,
                },
            }" :pagination="{
                clickable: true,
                el: '.swiper-pagination-custom',
            }" class="h-full w-full">
                <swiper-slide v-for="(image, index) in images" :key="index"
                    class="h-full w-full relative overflow-hidden">
                    <div class="relative w-full h-full overflow-hidden"
                        :class="hoveredIndex === index ? 'cursor-none' : ''" @mousemove="handleMouseMove"
                        @mouseenter="hoveredIndex = index" @mouseleave="hoveredIndex = null">
                        <img :src="image.url || '/assets/images/cover.jpg'" :alt="image.alt || 'prize image'"
                            class="h-full w-full object-contain" />
                        <!-- Magnifier Circle -->
                        <div v-show="hoveredIndex === index"
                            class="magnifier-circle pointer-events-none absolute rounded-full border-2 border-dark-bg shadow-2xl overflow-hidden"
                            :style="magnifierStyle">
                            <img :src="image.url || '/assets/images/cover.jpg'" :alt="image.alt || 'prize image'"
                                class="absolute top-0 left-0 max-w-none max-h-none object-contain"
                                :style="magnifierImageStyle" />
                        </div>
                    </div>
                </swiper-slide>
            </swiper>

            <!-- Custom Navigation Buttons -->
            <button v-if="images.length > 1"
                class="swiper-button-prev-custom absolute -left-8 top-1/2 -translate-y-1/2 z-10 w-16 h-16 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white duration-300 ease-in-out lg:hover:bg-white lg:hover:text-black-fks">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                    class="ml-6">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button v-if="images.length > 1"
                class="swiper-button-next-custom absolute -right-8 top-1/2 -translate-y-1/2 z-10 w-16 h-16 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white duration-300 ease-in-out lg:hover:bg-white lg:hover:text-black-fks">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                    class="mr-6">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
        <div v-if="images.length > 1" class="swiper-pagination-custom relative !inset-0 z-[15] flex gap-2"></div>
    </div>
</template>

<script>
import { ref, computed } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Pagination } from 'swiper/modules'

// Import Swiper styles
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'

export default {
    components: {
        Swiper,
        SwiperSlide,
    },
    props: {
        images: {
            type: Array,
            default: () => [],
        },
    },
    setup() {
        const hoveredIndex = ref(null);
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
            modules: [Pagination, Navigation],
            hoveredIndex,
            mouseX,
            mouseY,
            imageRefs,
            magnifierStyle,
            magnifierImageStyle,
            handleMouseMove,
        };
    },
}
</script>

<style lang="scss" scoped>
.prize-slider-zoom {
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
