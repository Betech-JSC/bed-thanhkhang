<template>
    <ProductShow v-bind="$props" />
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
    name: 'ProductsShow',
    layout: MainLayout,
}
</script>

<script setup>
import ProductShow from '../Product/Show.vue'

defineProps({
    product: {
        type: Object,
        default: () => ({}),
    },
    related_products: {
        type: Array,
        default: () => [],
    },
    faqs: {
        type: Array,
        default: () => [],
    },
    ctaBanner: {
        type: [Object, Array],
        default: null,
    },
    pdpDefaults: {
        type: Object,
        default: () => ({}),
    },
    seo: {
        type: Object,
        default: null,
    },
})
</script>
