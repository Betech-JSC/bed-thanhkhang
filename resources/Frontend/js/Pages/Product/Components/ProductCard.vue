<template>
    <Link
        :href="productUrl"
        class="group relative flex flex-col w-full lg:w-[273px] h-[419px] rounded-[12px] overflow-hidden bg-[#30374F] hover:-translate-y-1.5 hover:border-[#2970FF]/50 hover:shadow-2xl hover:shadow-[#2970FF]/20 border border-white/5 shadow-lg select-none transition-all duration-300"
    >
        <!-- Showcase Image Container (Locked 273x335px Frame) -->
        <div class="relative w-full h-[335px] bg-[#D1D2D2] overflow-hidden shrink-0">
            <!-- Best Choice Badge (Figma 2283:6509) -->
            <div
                v-if="product.isBestChoice"
                class="absolute top-[12px] left-[13px] z-20 w-[97px] h-[25px] rounded-[4px] px-2 py-1 bg-[#00359E] text-[#F5F8FF] text-xs font-medium leading-[17px] flex items-center justify-center shadow-sm pointer-events-none"
            >
                {{ tt('Best choice') }}
            </div>

            <div class="w-full h-full relative overflow-hidden">
                <!-- Product Showcase Image -->
                <img
                    :src="productImageSrc"
                    :alt="productImageAlt"
                    width="273"
                    height="335"
                    class="w-full h-full object-cover select-none group-hover:scale-105 transition-transform duration-500 ease-out"
                    loading="lazy"
                    decoding="async"
                    @error="onImageError"
                />
            </div>

            <!-- Subtle hover tint -->
            <div
                class="absolute inset-0 bg-[#00359E]/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-10"
            ></div>
        </div>

        <!-- Bottom Info Bar (Locked Height 84px, Figma 2283:6515) -->
        <div class="relative h-[84px] w-full bg-[#30374F] overflow-hidden shrink-0">
            <div class="w-full h-full px-2 py-3 flex flex-col justify-between gap-1">
                <h3
                    class="text-base font-bold text-[#F5F8FF] line-clamp-1 h-[22px] leading-[22px] font-sans group-hover:text-[#84ADFF] transition-colors"
                    :title="tt(product.name || product.title)"
                >
                    {{ tt(product.name || product.title) }}
                </h3>
                <p class="text-xs text-[#B9C0D4] line-clamp-2 h-[34px] leading-[17px] font-normal">
                    {{ product.description ? tt(product.description) : tt('Mô hình trưng bày · In theo yêu cầu') }}
                </p>
            </div>
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
    mounted() {
        if (typeof window !== 'undefined' && this.productImageSrc) {
            const img = new window.Image();
            img.src = this.productImageSrc;
        }
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
            return this.product.name || this.product.title || this.tt('Sản phẩm Thành Khang Tecsat');
        },
    },
    methods: {
        onImageError(e) {
            e.target.src = '/assets/images/products/figma-product-sample.png';
        },
    },
}
</script>
