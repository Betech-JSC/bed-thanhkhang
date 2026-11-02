<template>
    <div @click="$emit('open-modal', project)"
        class="group relative rounded-[32px] overflow-hidden cursor-pointer border border-gray-200 lg:hover:-translate-y-5 duration-300 ease-in-out">
        <div class="bg-white p-5 space-y-3">
            <div class="block overflow-hidden rounded-[24px]">
                <div class="aspect-w-5 aspect-h-6 bg-gray-100 overflow-hidden relative">
                    <JPicture :src="project.image?.url || '/assets/images/placeholder.jpg'"
                        :alt="project.image?.alt || project.title" class="w-full h-full object-cover" />
                </div>
            </div>
            <div class="flex items-center gap-4 mt-auto pt-2">
                <div class="relative w-24 h-24 rounded-full overflow-hidden shrink-0">
                    <JPicture :src="project.author_avatar?.url || '/assets/images/avatar.png'"
                        :alt="project.author_avatar?.alt || project.title" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 overflow-hidden space-y-1">
                    <h4
                        class="title-1 font-display font-bold text-gray-900 uppercase lg:group-hover:text-red-fks duration-300 ease-in-out">
                        {{ project.author_name || 'Học viên Funky' }}
                    </h4>
                    <p class="body-1 !leading-[130%] text-gray-900 line-clamp-3 font-medium">
                        {{ project.title }}
                    </p>
                </div>
            </div>
        </div>
        <div
            class="opacity-0 invisible lg:group-hover:opacity-100 lg:group-hover:visible duration-300 ease-in-out absolute inset-0 bg-red-fks h-full w-full flex flex-col justify-between p-8">
            <div class="space-y-3 relative z-10 text-white">
                <h3 class="display-3 font-display font-bold uppercase line-clamp-5">
                    {{ project.title }}
                </h3>
                <div class="w-24 h-1.5 w-full bg-white rounded-full"></div>
                <p class="title-1 tracking-normal font-semibold uppercase">
                    {{ project.author_name || 'Học viên Funky' }}
                </p>
            </div>

            <div class="space-y-3 relative z-10">
                <p class="body-3 !leading-[120%] text-white/90 leading-relaxed line-clamp-6 md:line-clamp-[8]">
                    {{ project.description }}
                </p>
                <div class="flex justify-end">
                    <button @click.stop="$emit('open-modal', project)" class="btn btn-green">
                        {{ tt('Tìm hiểu thêm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        project: {
            type: Object,
            required: true
        }
    },
    emits: ['open-modal']
}
</script>
