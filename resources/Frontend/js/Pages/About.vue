<template>
    <AboutIndex v-bind="$attrs" />
</template>

<script>
import AboutIndex from './About/Index.vue'

export default {
    name: 'About',
    components: {
        AboutIndex,
    },
}
</script>
