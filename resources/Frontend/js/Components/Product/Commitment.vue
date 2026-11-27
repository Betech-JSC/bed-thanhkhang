<template>
    <div v-if="Array.isArray(commits) && commits.length > 0" class="border border-gray-300 rounded-lg">
        <div v-if="title" class="px-3 pt-2 pb-1 font-medium text-gray-700 body-custom">
            {{ title }}
        </div>

        <div class="space-y-[16px] py-2 px-3">
            <div
                v-for="(commit, index) in commits"
                :key="index"
                class="flex items-center space-x-3"
            >
                <div v-if="commit?.image_url" class="w-[36px] h-[36px] flex-shrink-0">
                    <JamPicture
                        :alt="commit?.title || ''"
                        :src="commit.image_url"
                        class="picture-cover"
                    />
                </div>
                <div v-if="commit?.title" class="text-gray-700 caption-custom">
                    {{ commit.title }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ProductCommitment',
    props: {
        title: {
            type: String,
            default: '',
        },
        commits: {
            type: Array,
            default: () => [],
        },
    },
}
</script>
