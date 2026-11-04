<template>
    <section v-scroll-reveal id="services-section" class="py-12 md:py-20 bg-dark-bg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1152px]">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 lg:gap-12">
                <!-- Left Title (Figma Node 2261:5529: 320px width, 38px font-bold, line-height 46px) -->
                <div class="lg:w-[320px] flex-shrink-0">
                    <h2
                        class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] uppercase tracking-tight leading-[1.2] lg:leading-[46px] font-sans"
                    >
                        {{ tt('TỔNG QUAN VỀ DỊCH VỤ') }}
                    </h2>
                </div>

                <!-- Right 3 Service Cards (Figma Node 2261:5541: 3 cards, 241x316px, rounded 12px, gap 24px) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full lg:w-[772px] justify-items-center">
                    <Link
                        v-for="(service, index) in displayServices"
                        :key="index"
                        :href="getServiceUrl(service)"
                        class="relative rounded-[12px] w-full max-w-[280px] md:max-w-[241px] h-[316px] p-[20px_24px] flex flex-col items-center justify-between text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group cursor-pointer block no-underline focus:outline-none focus:ring-2 focus:ring-[#2970FF]/50 border border-white/5"
                        :class="getHoverShadow(service, index)"
                        :style="{
                            background: getServiceGradient(service, index),
                        }"
                    >
                        <!-- 3D Glass Icon at Top (Figma: 120x120px) -->
                        <div class="w-[120px] h-[120px] flex items-center justify-center flex-shrink-0">
                            <img
                                :src="getServiceImage(service)"
                                :alt="service.title"
                                class="w-full h-full object-contain drop-shadow-2xl group-hover:scale-110 transition-transform duration-500 ease-out select-none"
                                loading="lazy"
                                @error="handleImageError($event, service)"
                            />
                        </div>

                        <!-- Card Content: Title & Short Description (Figma Node 2261:5544: 193x138px) -->
                        <div class="w-full max-w-[193px] flex flex-col justify-between items-center text-center gap-2.5">
                            <h3 class="text-[24px] font-extrabold text-[#FAFAFA] group-hover:text-white transition-colors tracking-tight font-sans leading-[32px] text-center">
                                {{ tt(service.title) }}
                            </h3>
                            <p class="text-[16px] text-[#D1E0FF] leading-[24px] font-normal text-center">
                                {{ tt(service.description) }}
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

const tt = (s) => s

export default {
    name: 'ServicesSection',
    components: {
        Link,
    },
    props: {
        heading: {
            type: String,
            default: tt('TỔNG QUAN VỀ DỊCH VỤ'),
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
                    title: this.tt('Thiết kế 3D'),
                    slug: 'thiet-ke-3d',
                    image: '/assets/images/homepage/service-thietke-3d.png',
                    description: this.tt('Một ý tưởng chưa cần hoàn chỉnh để bắt đầu.'),
                    gradient: 'linear-gradient(180deg, #111322 20%, #2970FF 94%)',
                    hoverShadow: 'hover:shadow-[#2970FF]/30',
                },
                {
                    title: this.tt('Gia công 3D'),
                    slug: 'gia-cong-3d',
                    image: '/assets/images/homepage/service-giacong-3d.png',
                    description: this.tt('Đưa bản thiết kế ra ngoài màn hình.'),
                    gradient: 'linear-gradient(180deg, #111322 20%, #30374F 100%)',
                    hoverShadow: 'hover:shadow-[#30374F]/50',
                },
                {
                    title: this.tt('Làm đồ custom'),
                    slug: 'lam-do-custom',
                    image: '/assets/images/homepage/service-custom-3d.png',
                    description: this.tt('Không chỉ khác biệt. Là của riêng bạn.'),
                    gradient: 'linear-gradient(180deg, #111322 20%, #2970FF 94%)',
                    hoverShadow: 'hover:shadow-[#2970FF]/30',
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
        getServiceImage(service) {
            // 1. If service.image is a valid string
            if (typeof service?.image === 'string' && service.image.trim()) {
                return service.image;
            }
            // 2. If service.image is an object with valid url
            if (service?.image?.url && typeof service.image.url === 'string' && service.image.url.trim()) {
                return service.image.url;
            }
            // 3. Robust fallback mapping based on slug and title
            const slug = (service?.slug || '').toLowerCase();
            const title = (service?.title || '').toLowerCase();

            if (slug.includes('thiet-ke') || title.includes(this.tt('thiết kế')) || slug.includes('design')) {
                return '/assets/images/homepage/service-thietke-3d.png';
            }
            if (slug.includes('gia-cong') || title.includes(this.tt('gia công')) || slug.includes('print')) {
                return '/assets/images/homepage/service-giacong-3d.png';
            }
            if (slug.includes('custom') || title.includes('custom')) {
                return '/assets/images/homepage/service-custom-3d.png';
            }
            return '/assets/images/homepage/service-thietke-3d.png';
        },
        handleImageError(event, service) {
            const slug = (service?.slug || '').toLowerCase();
            const title = (service?.title || '').toLowerCase();
            if (slug.includes('gia-cong') || title.includes(this.tt('gia công')) || slug.includes('print')) {
                event.target.src = '/assets/images/homepage/service-giacong-3d.png';
            } else if (slug.includes('custom') || title.includes('custom')) {
                event.target.src = '/assets/images/homepage/service-custom-3d.png';
            } else {
                event.target.src = '/assets/images/homepage/service-thietke-3d.png';
            }
        },
        getServiceGradient(service, index) {
            if (service && service.gradient) {
                return service.gradient;
            }
            // Thẻ giữa (index 1: Thiết kế 3D) dùng nền #30374F theo chuẩn Figma Node 2261:5549
            if (index === 1) {
                return 'linear-gradient(180deg, #111322 20%, #30374F 100%)';
            }
            return 'linear-gradient(180deg, #111322 20%, #2970FF 94%)';
        },
        getHoverShadow(service, index) {
            if (service && service.hoverShadow) {
                return service.hoverShadow;
            }
            if (index === 1) {
                return 'hover:shadow-[#30374F]/50';
            }
            return 'hover:shadow-[#2970FF]/30';
        },
        getServiceUrl(service) {
            const slugMap = {
                [this.tt('Gia công 3D')]: 'gia-cong-3d',
                [this.tt('Thiết kế 3D')]: 'thiet-ke-3d',
                'Custom': 'lam-do-custom',
                [this.tt('Làm đồ custom')]: 'lam-do-custom',
                [this.tt('Làm đồ Custom')]: 'lam-do-custom',
            };
            const slug = service.slug || slugMap[service.title] || 'thiet-ke-3d';
            try {
                if (typeof route === 'function') {
                    return this.route ? this.route('services.show', { slug }) : route('services.show', { slug });
                }
            } catch (e) {
                // Fallback if Ziggy helper is unavailable
            }
            const locale = this.$page?.props?.locale?.current || 'vi';
            return locale === 'en' ? `/en/services/${slug}` : `/dich-vu/${slug}`;
        },
    },
}
</script>
