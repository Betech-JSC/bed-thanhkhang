<template>
    <ProductShow v-bind="$props" />
</template>

<script>
import ProductShow from '../Product/Show.vue'

export default {
    name: 'ProductsShow',
    components: {
        ProductShow,
    },
    props: {
        product: {
            type: Object,
            default: () => ({}),
        },
        related_products: {
            type: Array,
            default: () => [],
        },
        seo: {
            type: Object,
            default: () => ({}),
        },
    },
}
</script>
