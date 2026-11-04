<template>
    <Link
        :href="productUrl"
        class="group relative block w-full h-[419px] rounded-[12px] overflow-hidden bg-[#30374F] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl shadow-lg border border-transparent hover:border-[#5D6B98]/40"
    >
        <!-- Showcase Image Container (Height ~335px) -->
        <div class="relative w-full h-[335px] bg-[#D1D2D2] overflow-hidden flex items-center justify-center">
            <!-- Best Choice Badge (Figma 2283:6509) -->
            <div
                v-if="product.isBestChoice"
                class="absolute top-3 left-3 z-10 bg-[#00359E] text-[#F5F8FF] text-xs font-medium px-2.5 py-1 rounded shadow-sm pointer-events-none"
            >
                {{ tt('Best choice') }}
            </div>

            <!-- Product Showcase Image -->
            <img
                :src="productImageSrc"
                :alt="productImageAlt"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                loading="lazy"
                @error="onImageError"
            />

            <!-- Subtle hover tint -->
            <div
                class="absolute inset-0 bg-[#00359E]/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
            ></div>
        </div>

        <!-- Bottom Info Bar (Height 84px, Figma 2283:6515) -->
        <div class="h-[84px] w-full bg-[#30374F] px-6 py-3 flex flex-col justify-center gap-1">
            <h3
                class="text-base font-bold text-[#F5F8FF] line-clamp-1 leading-[22px] font-sans group-hover:text-[#84ADFF] transition-colors"
                :title="product.name || product.title"
            >
                {{ product.name || product.title }}
            </h3>
            <p class="text-xs text-[#B9C0D4] line-clamp-1 leading-[17px]">
                {{ product.description || tt('Mẫu thiết kế in 3D hoàn thiện tinh xảo, độ chính xác cơ khí cao.') }}
            </p>
        </div>
    </Link>
</template>

<script>
export default {
    name: 'ProductCard',
    props: {
        product: {
            type: Object,
            required: true,
        },
    },
    computed: {
        productUrl() {
            if (typeof this.product.url === 'string' && this.product.url) {
                return this.product.url;
            }
            if (this.product.url && typeof this.product.url === 'object') {
                const current = (this.$page?.props?.locale?.current || 'VI').toUpperCase();
                if (this.product.url[current]) return this.product.url[current];
                const firstVal = Object.values(this.product.url)[0];
                if (firstVal) return firstVal;
            }
            if (this.product.slug) {
                return this.route('products.show', { slug: this.product.slug });
            }
            if (this.product.id) {
                return this.route('products.show', { slug: this.product.id });
            }
            return this.route('products.index');
        },
        productImageSrc() {
            let src = '/assets/images/products/figma-product-sample.png';
            if (typeof this.product.image === 'string' && this.product.image) {
                src = this.product.image;
            } else if (this.product.image && typeof this.product.image === 'object') {
                src = this.product.image.url || this.product.image.path || src;
            } else if (typeof this.product.thumbnail === 'string' && this.product.thumbnail) {
                src = this.product.thumbnail;
            }

            // Chuẩn hóa URL nếu có chứa /static// hoặc /static/assets/ hoặc /static/images/
            if (typeof src === 'string') {
                src = src.replace(/\/static\/\/?(assets\/|images\/)/g, '/$1')
                         .replace(/\/static\/\//g, '/');
            }

            return src;
        },
        productImageAlt() {
            if (this.product.image && typeof this.product.image === 'object' && this.product.image.alt) {
                return this.product.image.alt;
            }
            return this.product.name || this.product.title || 'Mẫu in 3D HH Maker';
        },
    },
    methods: {
        onImageError(e) {
            e.target.src = '/assets/images/products/figma-product-sample.png';
        },
    },
}
</script>
