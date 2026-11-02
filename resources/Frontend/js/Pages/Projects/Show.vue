<template>
    <main class="min-h-screen bg-[#F9F9F9] relative md:pt-28 pt-24 xl:pt-40 pb-20">
        <!-- Grid Background -->
        <div class="absolute inset-0">
            <img src="/assets/images/posts/bannerTop.png" loading="eager" class="object-fit h-auto w-full opacity-40"
                alt="background mask" />
        </div>

        <section class="py-12 relative z-10">
            <div class="container">
                <div class="space-y-6 text-center max-w-[780px] w-full mx-auto">
                    <div class="display-2 font-display font-bold uppercase leading-none mb-6">
                        Đồ án từ học viên
                    </div>
                    <div class="body-1">Hệ thống Cơ sở đồ án được thực hiện và chia sẻ bởi các học viên FunkyStyle nhằm
                        lan
                        tỏa lưu trữ kiến thức và kinh nghiệm đồ án cho cộng đồng sinh viên học thuật kiến trúc tại Việt
                        Nam
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-gray-200 py-10 relative z-10">
            <ProjectSliderZoomDetail :images="project.images" class="mb-8" />

            <div class="container space-y-8">
                <h1 class="display-2 font-display font-semibold text-gray-900 uppercase">
                    {{ project.title }}
                </h1>
                <div class="flex md:flex-row flex-col max-md:space-y-2 items-start justify-between">
                    <div v-if="(project.categories && project.categories.length > 0) || project.industry"
                        class="headline-3 uppercase">
                        Ngành {{(project.categories && project.categories.length > 0) ? project.categories.map(c =>
                            c.title).join(' - ') : project.industry}}
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="title-1 text-gray-900 font-bold uppercase">Người thực hiện:</span>
                            <span class="body-0 !font-medium">{{ project.author_name }}</span>
                        </div>
                        <div class="body-0 md:text-right">{{ project.contact }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related Projects -->
        <section class="py-16 md:py-24 bg-[#F5F5F5] relative z-10">
            <div class="container overflow-visible">
                <!-- Filters Section -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-12">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="relative group min-w-[200px]">
                            <select v-model="filters.industry"
                                class="w-full appearance-none bg-white border-2 border-black rounded-full px-6 py-3 font-display font-bold uppercase tracking-wider text-black focus:outline-none cursor-pointer">
                                <option value="">{{ tt('Ngành') }}</option>
                                <option v-for="opt in industryOptions" :key="opt.id" :value="opt.id">{{ opt.title }}
                                </option>
                            </select>
                            <div
                                class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none transition-transform group-hover:translate-y-[-2px]">
                                <svg width="14" height="8" viewBox="0 0 14 8" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L13 1" stroke="black" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>

                        <div class="relative group min-w-[150px]">
                            <select v-model="filters.year"
                                class="w-full appearance-none bg-white border-2 border-black rounded-full px-6 py-3 font-display font-bold uppercase tracking-wider text-black focus:outline-none cursor-pointer">
                                <option value="">{{ tt('Năm') }}</option>
                                <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
                            </select>
                            <div
                                class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none transition-transform group-hover:translate-y-[-2px]">
                                <svg width="14" height="8" viewBox="0 0 14 8" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L13 1" stroke="black" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="relative w-full md:w-[350px]">
                        <input type="text" v-model="filters.search" :placeholder="tt('Tìm kiếm')"
                            class="w-full bg-white border border-gray-200 rounded-lg px-6 py-3 pr-12 text-body-1 focus:outline-none focus:border-black transition-colors" />
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Projects Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <RelatedProjectCard v-for="(item, index) in filteredProjects" :key="index" :project="item" />
                </div>

                <!-- Empty State -->
                <div v-if="filteredProjects.length === 0" class="py-20 text-center">
                    <p class="title-2 text-gray-400">{{ tt('Không tìm thấy đồ án phù hợp') }}</p>
                </div>
            </div>
        </section>
    </main>
</template>

<script>
import JamBreadcrumb from '@/Components/Jam/Breadcrumb.vue'
import ProjectCard from './Components/ProjectCard.vue'
import RelatedProjectCard from './Components/RelatedProjectCard.vue'
import ProjectShareSocial from './Components/ProjectShareSocial.vue'
import dayjs from 'dayjs'
import ProjectSliderZoomDetail from './Components/ProjectSliderZoomDetail.vue'

export default {
    props: {
        project: Object,
        categories: Array,
        related_projects: {
            type: Array,
            default: () => []
        }
    },
    components: {
        JamBreadcrumb,
        ProjectCard,
        RelatedProjectCard,
        ProjectSliderZoomDetail,
        ProjectShareSocial
    },
    data() {
        return {
            filters: {
                industry: '',
                year: '',
                search: ''
            },
        }
    },
    computed: {
        displayImages() {
            if (this.project?.images && this.project.images.length > 0) {
                return this.project.images;
            }
            return [
                {
                    url: 'https://images.pexels.com/photos/20063051/pexels-photo-20063051.jpeg',
                    alt: 'project image'
                },
                {
                    url: 'https://images.pexels.com/photos/35416522/pexels-photo-35416522.jpeg',
                    alt: 'project image'
                },
                {
                    url: 'https://images.pexels.com/photos/35696067/pexels-photo-35696067.jpeg',
                    alt: 'project image'
                }
            ]
        },
        projectUrl() {
            return window.location.href
        },
        industryOptions() {
            return this.categories || [];
        },
        yearOptions() {
            const years = this.related_projects?.map(p => p.year).filter(Boolean) || [];
            return [...new Set(years)].sort((a, b) => b - a);
        },
        filteredProjects() {
            return (this.related_projects || []).filter(item => {
                const matchIndustry = !this.filters.industry ||
                    item.categories?.some(c => c.id === this.filters.industry);
                const matchYear = !this.filters.year || item.year === this.filters.year;
                const matchSearch = !this.filters.search ||
                    (item.title && item.title.toLowerCase().includes(this.filters.search.toLowerCase())) ||
                    (item.author_name && item.author_name.toLowerCase().includes(this.filters.search.toLowerCase()));

                return matchIndustry && matchYear && matchSearch;
            });
        }
    },
    methods: {
        formatDate(date) {
            if (!date) return ''
            return dayjs(date).format('DD/MM/YYYY')
        },
        openContact() {
            document.getElementById('footer')?.scrollIntoView({ behavior: 'smooth' })
        }
    }
}
</script>

<style scoped>
.prose :deep(img) {
    @apply rounded-3xl shadow-lg my-8;
}

.prose :deep(h2),
.prose :deep(h3) {
    @apply font-display font-bold uppercase text-gray-900 mt-8 mb-4;
}
</style>
