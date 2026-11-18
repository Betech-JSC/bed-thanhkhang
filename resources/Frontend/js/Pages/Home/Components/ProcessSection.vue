<template>
    <section
        v-scroll-reveal
        id="process-section"
        class="relative w-full overflow-hidden text-white bg-dark-bg"
        style="background: linear-gradient(0deg, #111322 20%, #2970FF 94%);"
    >
        <!-- Centered Main Canvas matching Figma Node 2701:1425 (1440px x 892px) -->
        <div
            class="relative z-10 w-full max-w-[1440px] mx-auto min-h-[600px] lg:h-[892px] flex flex-col lg:flex-row items-stretch"
        >
            <!-- Left Column: 3D Wave Graphic + 05 Hero Headline (Figma Node 2701:1426 & 2701:1429: width 639px) -->
            <div
                class="w-full lg:w-[639px] shrink-0 relative min-h-[460px] sm:min-h-[520px] lg:h-full flex flex-col justify-center px-6 sm:px-12 lg:pl-[144px] lg:pr-10 py-12"
            >
                <!-- Background 3D Wave Graphic flush with left viewport edge up to divider -->
                <div
                    class="absolute inset-y-0 left-0 right-0 overflow-hidden pointer-events-none select-none"
                    style="left: min(0px, calc((1440px - 100vw) / 2));"
                >
                    <img
                        :src="processImage"
                        alt="Thành Khang Tecsat Industrial Process"
                        class="w-full h-full object-cover object-left-center select-none pointer-events-none"
                        loading="lazy"
                    />
                </div>

                <!-- Giant 05 & Headline directly overlaid on 3D Graphic (Figma Node 2701:1429) -->
                <div class="relative z-10 max-w-[435px]">
                    <div
                        class="process-giant-number text-[72px] sm:text-[96px] lg:text-[120px] font-bold text-[#FAFAFA] leading-none tracking-tight font-sans select-none drop-shadow-md"
                    >
                        {{ stepCount }}
                    </div>

                    <h2
                        class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#FAFAFA] leading-tight lg:leading-[53px] font-sans uppercase mt-4 sm:mt-6 lg:mt-8 tracking-normal drop-shadow-sm"
                    >
                        <template v-if="titleParts">
                            <span>{{ titleParts.prefix }}</span> <span class="inline-block whitespace-nowrap">{{ titleParts.brand }}</span>
                        </template>
                        <template v-else>
                            {{ tt(title) }}
                        </template>
                    </h2>
                </div>
            </div>

            <!-- Right Column: Description & 5-Step Process (Figma Node 2701:1432 Frame 243: width 657px, height 652px with solid left stroke #B5B5B5) -->
            <div
                class="w-full lg:w-[801px] shrink-0 lg:h-full relative flex flex-col justify-center px-6 sm:px-10 lg:px-0 py-10 lg:py-0"
            >
                <div
                    class="relative w-full max-w-[616px] lg:max-w-none lg:w-[657px] lg:h-[652px] flex flex-col justify-between lg:border-l lg:border-solid lg:border-[#B5B5B5] lg:pl-[41px]"
                >
                    <p
                        class="text-sm sm:text-base text-[#FAFAFA] leading-relaxed lg:leading-[24px] font-normal max-w-[616px]"
                    >
                        {{ tt(description) }}
                    </p>

                    <!-- 5 Steps Minimalist Rows (Figma Node 2701:1434 Frame 245) -->
                    <div v-if="displaySteps.length > 0" class="mt-8 sm:mt-10 lg:mt-0 max-w-[616px] w-full">
                        <div
                            v-for="(step, idx) in displaySteps"
                            :key="step.number || idx"
                            class="border-t border-[#B5B5B5] transition-colors duration-200 group hover:bg-white/[0.05]"
                        >
                            <button
                                type="button"
                                @click="toggleStep(idx)"
                                class="w-full min-h-[64px] py-3.5 flex items-center gap-5 sm:gap-6 text-left cursor-pointer select-none focus:outline-none"
                            >
                                <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 flex items-center justify-center">
                                    <svg
                                        class="w-7 h-7 sm:w-8 sm:h-8 text-[#FCFCFC] transition-transform duration-200 group-hover:translate-x-1 group-hover:-translate-y-1"
                                        :class="{ 'rotate-90': activeStep === idx }"
                                        viewBox="0 0 80 80"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M23.3334 56.6663L56.6667 23.333M56.6667 56.6663V23.333H23.3334"
                                            stroke="currentColor"
                                            stroke-width="4"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                                <span
                                    class="text-lg sm:text-xl lg:text-[24px] font-medium text-[#FAFAFA] group-hover:text-white transition-colors leading-[34px] flex-1"
                                >
                                    {{ tt(step.title) }}
                                </span>
                            </button>
                            <Transition
                                enter-active-class="transition-all duration-200 ease-out"
                                enter-from-class="opacity-0 -translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition-all duration-150 ease-in"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-1"
                            >
                                <div
                                    v-if="step.detail && activeStep === idx"
                                    class="pb-4 pl-14 sm:pl-16 pr-4 text-xs sm:text-sm text-[#D1E0FF] leading-relaxed"
                                >
                                    {{ tt(step.detail) }}
                                </div>
                            </Transition>
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
    name: 'ProcessSection',
    props: {
        processImage: {
            type: String,
            default: '/assets/images/homepage/process-banner-figma.png',
        },
        stepCount: {
            type: String,
            default: '05',
        },
        title: {
            type: String,
            default: 'BƯỚC TRONG QUY TRÌNH CUNG ỨNG CHUYÊN NGHIỆP & MINH BẠCH TẠI THÀNH KHANG TECSAT',
        },
        description: {
            type: String,
            default:
                'Đối tác gửi mã linh kiện, thông số bước xích hoặc bản vẽ kỹ thuật, đội ngũ kỹ sư Thành Khang Tecsat sẽ đối chiếu chuẩn OEM, tư vấn vật liệu và giải pháp vận hành tối ưu cùng chứng chỉ CO/CQ đầy đủ.',
        },
        steps: {
            type: Array,
            default: () => [],
        },
        customSteps: {
            type: Array,
            default: null,
        },
    },
    data() {
        return {
            activeStep: null,
            defaultSteps: [
                {
                    number: '01',
                    title: 'Tiếp nhận & Khảo sát',
                    detail: 'Tiếp nhận thông số kỹ thuật, mã linh kiện OEM, bản vẽ CAD (.STEP, .DWG) hoặc khảo sát thực tế hệ thống thang cuốn, băng tải, nhà xưởng.',
                },
                {
                    number: '02',
                    title: 'Đối chiếu & Báo giá',
                    detail: 'Kỹ sư đối chiếu tiêu chuẩn Universal Chains / EN 115 / DIN, đề xuất vật liệu (40Cr, Inox 304/316, POM, EPDM) và gửi báo giá chi tiết.',
                },
                {
                    number: '03',
                    title: 'Chuẩn bị & Kiểm định',
                    detail: 'Xuất kho linh kiện chính hãng hoặc gia công OEM chuẩn dung sai, kiểm định cơ tính và hoàn thiện hồ sơ chứng từ CO/CQ.',
                },
                {
                    number: '04',
                    title: 'Đóng gói & Bàn giao',
                    detail: 'Đóng gói kiện gỗ/pallet chuẩn công nghiệp chống ẩm va đập và vận chuyển an toàn đến tận công trình, nhà máy trên toàn quốc.',
                },
                {
                    number: '05',
                    title: 'Lắp đặt & Bảo hành',
                    detail: 'Hỗ trợ kỹ thuật nghiệm thu lắp đặt, chạy thử ổn định và áp dụng chính sách bảo hành chính hãng dài hạn.',
                },
            ],
        };
    },
    computed: {
        displaySteps() {
            const rawList =
                Array.isArray(this.steps) && this.steps.length > 0
                    ? this.steps
                    : Array.isArray(this.customSteps) && this.customSteps.length > 0
                    ? this.customSteps
                    : this.defaultSteps;

            const homeTitleMap = {
                'liên hệ': 'Tiếp nhận & Khảo sát',
                'báo giá': 'Đối chiếu & Báo giá',
                'chuẩn bị': 'Chuẩn bị & Kiểm định',
                'đóng gói': 'Đóng gói & Bàn giao',
                'gửi hàng': 'Lắp đặt & Bảo hành',
            };

            return rawList.map((item, idx) => {
                const fallback = this.defaultSteps[idx] || {};
                const rawTitle = String(item.title || item.name || fallback.title || '').trim();
                const cleanTitle = rawTitle.replace(/^(?:(?:Bước|Step)\s*\d+\s*[:.\-–]?\s*|\d+\s*[.:\-–]\s*)+/i, '').trim();
                const mappedTitle = homeTitleMap[cleanTitle.toLowerCase()] || cleanTitle;
                return {
                    number: item.number || String(idx + 1).padStart(2, '0'),
                    title: mappedTitle || rawTitle || fallback.title || '',
                    detail: item.detail || item.description || item.content || fallback.detail || '',
                };
            });
        },
        titleParts() {
            const rawTitle = typeof this.tt === 'function' ? this.tt(this.title) : this.title;
            if (!rawTitle) return null;

            const match = rawTitle.match(/^(.*?)[\s\u00A0]+(HH[\s\u00A0]+MAKER)$/i);
            if (match) {
                return {
                    prefix: match[1].trim(),
                    brand: match[2].trim(),
                };
            }
            return null;
        },
    },
    methods: {
        toggleStep(idx) {
            this.activeStep = this.activeStep === idx ? null : idx;
        },
    },
};
</script>
