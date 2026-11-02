<template>
    <component :is="isLink ? 'Link' : 'div'" :href="isLink ? route('projects.show', project.slug || '') : undefined"
        class="group block h-full cursor-pointer lg:hover:-translate-y-5 duration-300 ease-in-out"
        @click="$emit('select', project)">
        <div
            class="h-full bg-white border-2 border-black rounded-[24px] overflow-hidden transition-all duration-300 lg:group-hover:bg-red-fks lg:group-hover:border-red-fks flex flex-col">
            <!-- Image Container -->
            <div class="relative aspect-[1/1] overflow-hidden">
                <img :src="project.image?.url || '/assets/images/cover.jpg'" :alt="project.title"
                    class="w-full h-full object-cover" />

                <!-- Badges -->
                <div class="absolute top-3 right-3 flex flex-col items-end gap-1.5">
                    <span v-if="(project.categories && project.categories.length > 0) || project.industry"
                        class="bg-[#CCFF00] text-black text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-sm">
                        {{(project.categories && project.categories.length > 0) ? project.categories.map(c =>
                            c.title).join(' - ') : project.industry }}
                    </span>
                    <span v-if="project.year"
                        class="bg-[#CCFF00] text-black text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-sm">
                        {{ project.year }}
                    </span>
                </div>
            </div>

            <!-- Content Container -->
            <div class="p-5 flex-1 flex flex-col justify-between transition-colors">
                <h3
                    class="title-3 font-display font-bold uppercase text-black mb-4 line-clamp-3 group-hover:text-white transition-colors">
                    {{ project.title }}
                </h3>

                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#CCFF00] border border-black/10"></div>
                    <span class="body-2 !font-medium text-black group-hover:text-white/90 transition-colors">
                        {{ project.author_name || 'Nguyễn Văn A' }}
                    </span>
                </div>
            </div>
        </div>
    </component>
</template>

<script>
export default {
    props: {
        project: {
            type: Object,
            required: true
        },
        isLink: {
            type: Boolean,
            default: true
        }
    }
}
</script>
