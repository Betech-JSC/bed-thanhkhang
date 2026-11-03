<template>
    <section id="services-section" class="py-12 md:py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-8 lg:gap-12">
                <!-- Left Title (Figma Node 2261:5529: 38px, font-bold 700, line-height 46px) -->
                <div class="lg:w-[320px] flex-shrink-0 pt-2">
                    <h2
                        class="text-2xl sm:text-3xl lg:text-display-hero font-bold text-[#FAFAFA] uppercase tracking-tight leading-[1.2] font-sans"
                    >
                        TỔNG QUAN VỀ<br class="hidden lg:inline" /> DỊCH VỤ
                    </h2>
                </div>

                <!-- Right 3 Service Cards (Figma Node 2261:5541: 3 cards, 241x316, rounded 12px, gradient 180deg #111322 to #2970FF) -->
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link
                        v-for="(service, index) in displayServices"
                        :key="index"
                        :href="getServiceUrl(service)"
                        class="relative rounded-[12px] p-6 sm:p-7 flex flex-col items-center justify-between text-center min-h-[316px] transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-[#2970FF]/30 border border-white/10 group cursor-pointer block no-underline focus:outline-none focus:ring-2 focus:ring-[#2970FF]/50"
                        :style="{
                            background: 'linear-gradient(180deg, #111322 20%, #2970FF 94%)',
                        }"
                    >
                        <!-- 3D Glass Icon at Top (Figma: 120x120px) -->
                        <div class="w-[120px] h-[120px] flex items-center justify-center flex-shrink-0">
                            <img
                                :src="service.image"
                                :alt="service.title"
                                class="w-full h-full object-contain drop-shadow-2xl group-hover:scale-110 transition-transform duration-500 ease-out select-none"
                                loading="lazy"
                            />
                        </div>

                        <!-- Card Content: Title & Short Description -->
                        <div class="space-y-3 pt-4 flex-1 flex flex-col justify-center">
                            <h3 class="text-display-card font-extrabold text-[#FAFAFA] group-hover:text-[#84ADFF] transition-colors tracking-tight font-sans">
                                {{ service.title }}
                            </h3>
                            <p class="text-sm sm:text-base text-[#D1E0FF] leading-[24px] font-normal">
                                {{ service.description }}
                            </p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
    name: 'ServicesSection',
    components: {
        Link,
    },
    props: {
        heading: {
            type: String,
            default: 'TỔNG QUAN VỀ DỊCH VỤ',
        },
        services: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            defaultServices: [
                {
                    title: 'Gia công 3D',
                    slug: 'gia-cong-3d',
                    image: '/assets/images/homepage/service-giacong-3d.png',
                    description:
                        'Hiện thực hóa mô hình bằng công nghệ in 3D, chú trọng độ chính xác và chất lượng sản phẩm',
                    link: '/dich-vu/gia-cong-3d',
                },
                {
                    title: 'Thiết kế 3D',
                    slug: 'thiet-ke-3d',
                    image: '/assets/images/homepage/service-thietke-3d.png',
                    description:
                        'Biến ý tưởng thành mô hình 3D chi tiết, kết hợp tính thẩm mỹ và khả năng chế tác thực tế.',
                    link: '/dich-vu/thiet-ke-3d',
                },
                {
                    title: 'Custom',
                    slug: 'lam-do-custom',
                    image: '/assets/images/homepage/service-custom-3d.png',
                    description:
                        'Thiết kế và chế tác theo yêu cầu, tạo nên những món đồ độc đáo mang dấu ấn riêng của bạn.',
                    link: '/dich-vu/lam-do-custom',
                },
            ],
        };
    },
    computed: {
        displayServices() {
            if (this.services && this.services.length > 0) {
                return this.services;
            }
            return this.defaultServices;
        },
    },
    methods: {
        getServiceUrl(service) {
            const slugMap = {
                'Gia công 3D': 'gia-cong-3d',
                'Thiết kế 3D': 'thiet-ke-3d',
                'Custom': 'lam-do-custom',
                'Làm đồ custom': 'lam-do-custom',
                'Làm đồ Custom': 'lam-do-custom',
            };
            const slug = service.slug || slugMap[service.title] || 'thiet-ke-3d';
            try {
                if (typeof route === 'function') {
                    return route('services.show', { slug });
                }
            } catch (e) {
                // Fallback if Ziggy helper is unavailable
            }
            return `/dich-vu/${slug}`;
        },
    },
}
</script>
