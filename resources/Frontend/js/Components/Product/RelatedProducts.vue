<template>
    <section class="w-full py-10 lg:py-14 border-t border-[#404968]">
        <!-- TIÊU ĐỀ BỘ SƯU TẬP (FIGMA 2358:3095: 1152x29 px) -->
        <div class="flex items-center justify-between mb-6 lg:mb-8">
            <div>
                <h2 class="text-2xl lg:text-3xl font-bold text-[#FAFAFA] font-sans tracking-tight">
                    {{ tt('Sản phẩm khác') }}
                </h2>
                <p class="text-xs sm:text-sm text-[#94A3B8] mt-1 font-sans">
                    {{ tt('Khám phá thêm các mô hình thiết kế in 3D độc bản và decor độc đáo') }}
                </p>
            </div>
            <Link
                :href="route('products.index')"
                class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-[#84ADFF] hover:text-white transition-colors"
            >
                {{ tt('Xem tất cả') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </Link>
        </div>

        <!-- DANH SÁCH GỢI Ý (FIGMA 2358:3100: 1152x419 px, 4 cards 273x419 px, gap 20px) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div
                v-for="(item, idx) in productList"
                :key="item.id || idx"
                class="group bg-[#141C2E] border border-[#30374F] hover:border-[#2970FF] rounded-xl overflow-hidden flex flex-col transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1"
            >
                <!-- Hình ảnh sản phẩm (Figma image 15: 273x419) -->
                <Link
                    :href="item.slug ? route('products.show', { slug: item.slug }) : route('products.index')"
                    class="relative block w-full h-[280px] sm:h-[320px] bg-[#1E2538] overflow-hidden"
                >
                    <img
                        :src="item.image"
                        :alt="item.title"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-[#141C2E] via-transparent to-transparent opacity-60"></div>
                    <span
                        v-if="item.category"
                        class="absolute top-3 left-3 px-2.5 py-1 rounded text-[11px] font-semibold bg-[#111322]/80 text-[#84ADFF] border border-[#5D6B98]/40 backdrop-blur-sm"
                    >
                        {{ tt(item.category) }}
                    </span>
                </Link>

                <!-- Frame 263: Thông tin sản phẩm (Figma I2358:3101;2283:6515: 273x67 px) -->
                <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between bg-[#141C2E]">
                    <div>
                        <h3 class="text-base font-semibold text-[#FAFAFA] line-clamp-1 group-hover:text-[#84ADFF] transition-colors">
                            <Link :href="item.slug ? route('products.show', { slug: item.slug }) : route('products.index')">
                                {{ item.title }}
                            </Link>
                        </h3>
                        <p class="text-xs text-[#94A3B8] mt-1 line-clamp-2">
                            {{ item.description || tt('Chất liệu PLA cao cấp, công nghệ in FDM tỉ mỉ từng chi tiết.') }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-4 mt-3 border-t border-[#30374F]/70">
                        <span class="text-sm font-bold text-white">
                            {{ item.priceFormatted || tt('Liên hệ báo giá') }}
                        </span>
                        <Link
                            :href="item.slug ? route('products.show', { slug: item.slug }) : route('products.index')"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-[#84ADFF] group-hover:translate-x-0.5 transition-transform"
                        >
                            {{ tt('Chi tiết') }}
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="sm:hidden text-center mt-6">
            <Link
                :href="route('products.index')"
                class="inline-flex items-center justify-center w-full py-2.5 px-4 rounded-lg bg-[#141C2E] border border-[#404968] text-sm font-medium text-[#84ADFF]"
            >
                {{ tt('Xem tất cả sản phẩm') }}
            </Link>
        </div>
    </section>
</template>

<script>
export default {
    name: 'RelatedProducts',
    props: {
        products: {
            type: Array,
            default: () => [],
        },
    },
    computed: {
        isEn() {
            return (this.$page?.props?.locale?.current || 'vi') === 'en'
        },
        productList() {
            if (this.products && this.products.length > 0) {
                return this.products.slice(0, 4).map((p, idx) => ({
                    id: p.id || idx,
                    title: p.title || p.name || (this.isEn ? '3D Printed Product' : 'Sản phẩm in 3D'),
                    slug: p.slug || '',
                    category: p.category?.title || p.category_name || (this.isEn ? '3D Printed Model' : 'Mô hình in 3D'),
                    image: p.image?.url || p.thumbnail || p.cover || `/images/pdp/related_${(idx % 4) + 1}.png`,
                    description: p.description || p.excerpt || '',
                    priceFormatted: p.price ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.price) : this.tt('Liên hệ báo giá'),
                }))
            }

            if (this.isEn) {
                return [
                    {
                        id: 1,
                        title: 'Space Astronaut Model',
                        slug: 'mo-hinh-phi-hanh-gia',
                        category: 'Space Decor',
                        image: '/images/pdp/related_1.png',
                        description: 'Crisp contours, matte PLA fingerprint-resistant finish, high color retention.',
                        priceFormatted: '$12.00',
                    },
                    {
                        id: 2,
                        title: 'Mini Geometric Plant Pot',
                        slug: 'chau-cay-da-giac-mini',
                        category: 'Space Decor',
                        image: '/images/pdp/related_2.png',
                        description: 'Smart drainage design ideal for desk succulents and workspace greenery.',
                        priceFormatted: '$6.00',
                    },
                    {
                        id: 3,
                        title: 'Mecha Cyberpunk Phone Stand',
                        slug: 'de-do-dien-thoai-mecha',
                        category: 'Daily Essentials',
                        image: '/images/pdp/related_3.png',
                        description: 'Ergonomic 60-degree viewing angle, compatible with all smartphone models.',
                        priceFormatted: '$9.50',
                    },
                    {
                        id: 4,
                        title: 'Articulated Robot Assembly Kit',
                        slug: 'robot-khop-cu-dong',
                        category: 'Custom Manufacturing',
                        image: '/images/pdp/related_4.png',
                        description: '14 flexible articulated joints, seamless print-in-place without metal screws.',
                        priceFormatted: '$15.00',
                    },
                ]
            }

            // Fallback to Figma related cards in Vietnamese
            return [
                {
                    id: 1,
                    title: 'Mô hình Phi hành gia Không gian',
                    slug: 'mo-hinh-phi-hanh-gia',
                    category: 'Trang trí bàn làm việc',
                    image: '/images/pdp/related_1.png',
                    description: 'Tạo hình sắc nét, vật liệu PLA matte chống bám vân tay, độ bền màu cao.',
                    priceFormatted: '250.000 ₫',
                },
                {
                    id: 2,
                    title: 'Chậu cây đa giác Mini Geometric',
                    slug: 'chau-cay-da-giac-mini',
                    category: 'Decor không gian',
                    image: '/images/pdp/related_2.png',
                    description: 'Thiết kế thoát nước thông minh cho sen đá và cây để bàn văn phòng.',
                    priceFormatted: '120.000 ₫',
                },
                {
                    id: 3,
                    title: 'Đế đỡ điện thoại Mecha Cyberpunk',
                    slug: 'de-do-dien-thoai-mecha',
                    category: 'Phụ kiện công nghệ',
                    image: '/images/pdp/related_3.png',
                    description: 'Góc nghiêng công thái học 60 độ, tương thích tất cả các dòng smartphone.',
                    priceFormatted: '190.000 ₫',
                },
                {
                    id: 4,
                    title: 'Bộ lắp ghép Robot Khớp cử động',
                    slug: 'robot-khop-cu-dong',
                    category: 'Mô hình khớp động',
                    image: '/images/pdp/related_4.png',
                    description: '14 khớp linh hoạt, in liền mạch không cần lắp ráp vít kim loại.',
                    priceFormatted: '320.000 ₫',
                },
            ]
        },
    },
}
</script>
