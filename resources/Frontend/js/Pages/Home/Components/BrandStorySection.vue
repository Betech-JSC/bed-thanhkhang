<template>
    <section v-scroll-reveal id="brand-story-section" class="py-12 md:py-20 lg:py-24 overflow-hidden bg-dark-bg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1240px]">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                <!-- Left: Founder Geometric Graphic (Figma Node 2261:5697) -->
                <div class="lg:col-span-6 flex justify-center">
                    <div class="relative w-full max-w-[520px]">
                        <img
                            :src="founderImage"
                            :alt="tt('Lâm Hồng Hải - Founder HH Maker')"
                            class="w-full h-auto object-contain select-none drop-shadow-2xl"
                            loading="lazy"
                        />
                    </div>
                </div>

                <!-- Right: Story Content & Metrics (Figma Node 2261:5710) -->
                <div class="lg:col-span-6 space-y-6 sm:space-y-8">
                    <!-- Text Header -->
                    <div class="space-y-3">
                        <span class="text-sm sm:text-base font-normal text-[#D1E0FF] tracking-wider uppercase">
                            {{ tt(subtitle) }}
                        </span>
                        <h2
                            class="text-2xl sm:text-3xl lg:text-[38px] lg:leading-[46px] font-bold text-[#FAFAFA] uppercase tracking-tight font-sans"
                        >
                            {{ tt(title) }}
                        </h2>
                    </div>

                    <!-- Story Paragraphs -->
                    <div class="space-y-4 text-sm sm:text-base text-[#D1E0FF] leading-[24px] font-normal">
                        <p>
                            {{ tt('Xuất phát từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại, tôi thành lập HH Maker với sứ mệnh rút ngắn khoảng cách giữa ý tưởng sơ khai và sản phẩm vật lý.') }}
                        </p>
                        <p>
                            {{ tt('Với mỗi dự án, tôi cùng đội ngũ luôn tỉ mỉ trong từng đường nét thiết kế, lựa chọn vật liệu và kiểm soát quy trình in để mang lại những sản phẩm sắc nét, chuẩn xác và mang đậm dấu ấn cá nhân nhất.') }}
                        </p>
                    </div>

                    <!-- 3 Metrics Strip (Figma Node 2261:5717: 10+, 500+, 95% with 1px white divider) -->
                    <div class="grid grid-cols-3 gap-3 sm:gap-6 pt-4">
                        <div
                            v-for="(stat, sIdx) in displayStats"
                            :key="sIdx"
                            class="space-y-1.5"
                            :class="sIdx < storyStats.length - 1 ? 'border-r border-white/60 pr-3 sm:pr-6' : ''"
                        >
                            <div class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] tracking-tight font-sans leading-[46px]">
                                {{ stat.number }}
                            </div>
                            <div class="text-xs sm:text-sm lg:text-base text-[#FAFAFA] font-normal leading-[22px]">
                                <span v-html="stat.label"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
const tt = (s) => s;

export default {
    name: 'BrandStorySection',
    computed: {
        displayStats() {
            const isEn = (this.$page?.props?.locale?.current === 'en') || (this.$i18n?.locale === 'en');
            if (isEn) {
                return [
                    {
                        number: '10+',
                        label: 'Years of Experience<br />in the Industry',
                    },
                    {
                        number: '500+',
                        label: 'Models & Finished<br />Products',
                    },
                    {
                        number: '95%',
                        label: 'Satisfied Clients<br />& Partners',
                    },
                ];
            }
            return this.storyStats;
        },
    },
    props: {
        founderImage: {
            type: String,
            default: '/assets/images/homepage/founder-composite.png',
        },
        subtitle: {
            type: String,
            default: tt('CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER'),
        },
        title: {
            type: String,
            default: tt('HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH'),
        },
    },
    data() {
        return {
            storyStats: [
                {
                    number: '10+',
                    label: this.tt('Năm hoạt động<br />trong lĩnh vực'),
                },
                {
                    number: '500+',
                    label: this.tt('Mẫu mã và sản<br />phẩm hoàn thiện'),
                },
                {
                    number: '95%',
                    label: this.tt('Khách hàng hài<br />lòng với dịch vụ'),
                },
            ],
        };
    },
};
</script>
