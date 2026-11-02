<template>
    <transition name="fade-enter-from">
        <div v-if="show" class="fixed inset-0 z-[1100] flex items-center justify-center p-4 md:p-8">

            <div class="absolute top-0 md:top-3 right-0 md:right-8 z-10">
                <button class="text-green-fks w-10 h-10 bg-red-fks rounded-md flex items-center justify-center"
                    @click="close">
                    <Close />
                </button>
            </div>
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-dark-bg/90 duration-300 ease-in-out" @click="close"></div>

            <!-- Modal Content -->
            <transition name="zoom">
                <div v-if="show"
                    class="relative bg-transparent w-full max-w-[1400px] max-h-[90vh] overflow-hidden shadow-2xl grid grid-cols-12 md:gap-8 gap-6 xl:gap-10 pb-8">
                    <div class="col-span-full lg:col-span-6 flex items-center">
                        <PrizeSliderZoom :images="prize?.images" />
                    </div>
                    <div class="col-span-full lg:col-span-6 flex flex-col text-white">
                        <div class="space-y-3 md:space-y-6">
                            <div class="space-y-6 md:space-y-12">
                                <!-- <Link :href="route('prizes.show', prize.slug)" class="block"> -->
                                <h3 class="headline-1 font-display font-bold uppercase">
                                    {{ prize.title }}
                                </h3>
                                <!-- </Link> -->

                                <div class="space-y-2 md:space-y-6">
                                    <div class="flex flex-col items-end justify-end space-y-3">
                                        <ProjectShareSocial :url="prize.slug" />
                                        <div class="w-full h-1.5 bg-white"></div>
                                    </div>
                                    <div class="overflow-y-auto h-[190px] md:h-[300px] xl:h-[400px] text-white/90 body-1"
                                        v-html="prize.content"></div>
                                </div>
                            </div>

                            <div class="flex md:flex-row flex-col md:items-center gap-2 md:gap-4">
                                <div
                                    class="bg-green-fks w-max rounded-full py-2 px-4 text-black-fks flex items-center gap-1 uppercase body-3 font-bold">
                                    <span>{{ tt('Student') }}:</span>
                                    <span>{{ prize.author_name || 'Đang cập nhật' }}</span>
                                </div>
                                <div
                                    class="bg-green-fks w-max rounded-full py-2 px-4 text-black-fks flex items-center gap-1 uppercase body-3 font-bold">
                                    <span>{{ tt('Contact') }}:</span>
                                    <span>{{ prize.email || 'Đang cập nhật' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<script>
import ProjectShareSocial from '@/Pages/Projects/Components/ProjectShareSocial.vue';
import PrizeSliderZoom from './PrizeSliderZoom.vue';
import Close from '@/Components/Icons/Close.vue';

export default {
    components: {
        PrizeSliderZoom,
        ProjectShareSocial,
        Close
    },
    props: {
        show: Boolean,
        prize: {
            type: Object,
            default: () => ({}),
        },
    },
    emits: ['close'],
    methods: {
        close() {
            this.$emit('close')
        },
        handleEsc(e) {
            if (e.key === 'Escape') {
                this.close()
            }
        },
    },
    watch: {
        show(val) {
            if (val) {
                document.body.style.overflow = 'hidden'
                window.addEventListener('keydown', this.handleEsc)
            } else {
                document.body.style.overflow = ''
                window.removeEventListener('keydown', this.handleEsc)
            }
        },
    },
    unmounted() {
        window.removeEventListener('keydown', this.handleEsc)
        document.body.style.overflow = ''
    },
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.zoom-enter-active,
.zoom-leave-active {
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.zoom-enter-from,
.zoom-leave-to {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
}
</style>
