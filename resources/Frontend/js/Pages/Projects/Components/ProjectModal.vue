<template>
    <transition name="fade-enter-from">
        <div v-if="show" class="fixed inset-0 z-[1100] flex items-center justify-center p-4 md:p-8">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-dark-bg/60 duration-300 ease-in-out" @click="close"></div>

            <!-- Modal Content -->
            <transition name="zoom">
                <div v-if="show"
                    class="relative bg-black-fks/90 w-full max-w-[1050px] max-h-[90vh] rounded-[64px] overflow-hidden shadow-2xl grid grid-cols-12 gap-16 py-8 pl-12 pr-8">
                    <div class="col-span-7 flex flex-col text-white">
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <Link :href="route('projects.show', project.slug)" class="block">
                                <h3 class="headline-1 font-display font-bold uppercase">
                                    {{ project.title }}
                                </h3>
                                </Link>
                                <div class="flex justify-end items-center">
                                    <ProjectShareSocial :url="projectUrl" />
                                </div>
                                <div class="w-full h-1.5 bg-white"></div>
                            </div>

                            <div class="overflow-y-auto h-[320px]" v-html="project.description">
                            </div>

                            <div class="flex items-center gap-4">
                                <div
                                    class="bg-green-fks w-max rounded-full py-2 px-4 text-black-fks flex items-center gap-1">
                                    <span class="body-1 uppercase !font-medium">Student:</span>
                                    <span>{{ project.author_name }}</span>
                                </div>
                                <div
                                    class="bg-green-fks w-max rounded-full py-2 px-4 text-black-fks flex items-center gap-1">
                                    <span class="body-1 uppercase !font-medium">Contact:</span>
                                    <span>{{ project.contact }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-5">
                        <ProjectSliderZoom :images="project.images" />
                    </div>
                </div>
            </transition>
        </div>
    </transition>

</template>

<script>
import ProjectShareSocial from './ProjectShareSocial.vue'
import ProjectSliderZoom from './ProjectSliderZoom.vue'

export default {
    components: {
        ProjectShareSocial,
        ProjectSliderZoom,
    },
    props: {
        show: Boolean,
        project: {
            type: Object,
            default: () => ({})
        }
    },
    emits: ['close'],
    data() {
        return {
            copied: false,
        }
    },
    computed: {
        projectUrl() {
            if (!this.project.slug) return window.location.origin
            return `${window.location.origin}${this.route('projects.show', this.project.slug)}`
        },
    },
    methods: {
        close() {
            this.$emit('close');
        },
        handleEsc(e) {
            if (e.key === 'Escape') {
                this.close();
            }
        },
    },
    watch: {
        show(val) {
            if (val) {
                document.body.style.overflow = 'hidden';
                window.addEventListener('keydown', this.handleEsc);
            } else {
                document.body.style.overflow = '';
                window.removeEventListener('keydown', this.handleEsc);
            }
        }
    },
    unmounted() {
        window.removeEventListener('keydown', this.handleEsc);
        document.body.style.overflow = '';
    }
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

.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>


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
