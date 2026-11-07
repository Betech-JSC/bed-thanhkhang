<template>
    <section v-scroll-reveal id="brand-story-section" class="py-12 md:py-20 lg:py-24 overflow-hidden bg-dark-bg">
        <div class="app-container">
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
                <div class="lg:col-span-6 space-y-6 sm:space-y-4">
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
                        <p v-for="(paragraph, pIdx) in formattedParagraphs" :key="pIdx">
                            {{ paragraph }}
                        </p>
                    </div>

                    <!-- 3 Metrics Strip (Figma Node 2701:1398 / 2701:1419: 10+, 500+, 95% with 1px white divider, centered text, 8px gap) -->
                    <div class="grid grid-cols-3 gap-2 sm:gap-3 lg:gap-3 pt-4 max-w-[543px]">
                        <div
                            v-for="(stat, sIdx) in displayStats"
                            :key="sIdx"
                            class="flex flex-col items-center justify-between text-center min-h-[98px] gap-2 px-1 sm:px-3 lg:px-5"
                            :class="sIdx < storyStats.length - 1 ? 'border-r border-white/60 sm:border-r sm:border-white' : ''"
                        >
                            <div class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] tracking-tight font-sans leading-[46px] text-center w-full">
                                {{ stat.number }}
                            </div>
                            <div class="text-xs sm:text-sm lg:text-base text-[#FAFAFA] font-normal leading-[22px] text-center w-full">
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
        formattedParagraphs() {
            const rawContent = this.description || this.story;
            if (!rawContent) return [];

            const splitTextIntoParagraphs = (val) => {
                if (!val) return [];
                if (typeof val === 'string') {
                    return val
                        .split(/(?:\s*\/n\s*|\s*\\n\s*|\r?\n|<br\s*\/?>)/gi)
                        .map((p) => p.trim())
                        .filter(Boolean);
                }
                return [val];
            };

            const translateFn = (txt) => {
                if (typeof this.tt === 'function') {
                    return this.tt(txt);
                }
                return txt;
            };

            let paragraphs = [];
            if (Array.isArray(rawContent)) {
                for (const item of rawContent) {
                    paragraphs.push(...splitTextIntoParagraphs(item));
                }
            } else {
                const translatedWhole = translateFn(rawContent);
                if (translatedWhole && translatedWhole !== rawContent) {
                    paragraphs = splitTextIntoParagraphs(translatedWhole);
                } else {
                    paragraphs = splitTextIntoParagraphs(rawContent);
                }
            }

            return paragraphs.map((p) => translateFn(p));
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
        story: {
            type: [String, Array],
            default: () => [
                'Xuất phát từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại, tôi thành lập HH Maker với sứ mệnh rút ngắn khoảng cách giữa ý tưởng sơ khai và sản phẩm vật lý.',
                'Với mỗi dự án, tôi cùng đội ngũ luôn tỉ mỉ trong từng đường nét thiết kế, lựa chọn vật liệu và kiểm soát quy trình in để mang lại những sản phẩm sắc nét, chuẩn xác và mang đậm dấu ấn cá nhân nhất.',
            ],
        },
        description: {
            type: [String, Array],
            default: 'Xuất phát từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại, tôi thành lập HH Maker với sứ mệnh rút ngắn khoảng cách giữa ý tưởng sơ khai và sản phẩm vật lý. /n Với mỗi dự án, tôi cùng đội ngũ luôn tỉ mỉ trong từng đường nét thiết kế, lựa chọn vật liệu và kiểm soát quy trình in để mang lại những sản phẩm sắc nét, chuẩn xác và mang đậm dấu ấn cá nhân nhất.',
        },
    },
    data() {
        return {
            storyStats: [
                {
                    number: '10+',
                    label: this.tt ? this.tt('Năm hoạt động<br />trong lĩnh vực') : 'Năm hoạt động<br />trong lĩnh vực',
                },
                {
                    number: '500+',
                    label: this.tt ? this.tt('Mẫu mã và sản<br />phẩm hoàn thiện') : 'Mẫu mã và sản<br />phẩm hoàn thiện',
                },
                {
                    number: '95%',
                    label: this.tt ? this.tt('Khách hàng hài<br />lòng với dịch vụ') : 'Khách hàng hài<br />lòng với dịch vụ',
                },
            ],
        };
    },
};
</script>
