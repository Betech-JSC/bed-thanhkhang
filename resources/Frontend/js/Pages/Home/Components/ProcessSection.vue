<template>
    <section
        v-scroll-reveal
        id="process-section"
        class="relative w-full overflow-hidden text-white bg-dark-bg"
        style="background: linear-gradient(0deg, #111322 20%, #2970FF 94%);"
    >
        <!-- Centered Main Canvas matching Figma Node 2701:1425 (1440px x 892px) -->
        <div
            class="relative z-10 w-full max-w-[1440px] mx-auto min-h-[600px] lg:h-[892px] flex flex-col lg:flex-row items-stretch overflow-hidden"
        >
            <!-- Left Column: 3D Wave Graphic + 05 Hero Headline (Figma Node 2701:1426 & 2701:1429: width 639px) -->
            <div
                class="w-full lg:w-[639px] shrink-0 relative min-h-[460px] sm:min-h-[520px] lg:h-full flex flex-col justify-center px-6 sm:px-12 lg:pl-[144px] lg:pr-10 py-12 overflow-hidden"
            >
                <!-- Background 3D Wave Graphic flush with left edge -->
                <img
                    :src="processImage"
                    alt="HH Maker 3D Layer Graphic"
                    class="absolute inset-0 w-full h-full object-cover object-left-center select-none pointer-events-none"
                    loading="lazy"
                />

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
                    <div v-if="steps.length > 0" class="mt-8 sm:mt-10 lg:mt-0 max-w-[616px] w-full">
                        <div
                            v-for="(step, idx) in steps"
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
            default: 'BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER',
        },
        description: {
            type: String,
            default:
                'Khách hàng gửi file 3D hoặc chia sẻ ý tưởng, đội ngũ kỹ thuật của HH Maker sẽ tiến hành phân tích độ khả thi, tư vấn công nghệ in tối ưu, cũng như lựa chọn loại vật liệu phù hợp nhất để đảm bảo sản phẩm đạt độ bền, tính thẩm mỹ và công năng sử dụng đúng như kỳ vọng của bạn.',
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
                    title: 'Tiếp nhận & Tư vấn',
                    detail: 'Tiếp nhận file mô hình 3D (STL, OBJ, STEP, 3MF) hoặc bản vẽ 2D phác thảo từ khách hàng; tư vấn công nghệ in và vật liệu phù hợp nhất.',
                },
                {
                    number: '02',
                    title: 'Phân tích & Báo giá',
                    detail: 'Kỹ sư kiểm tra tính toàn vẹn cấu trúc lưới mesh, slice mô hình, tính toán thời gian in, chi phí tối ưu và gửi bảng báo giá chi tiết.',
                },
                {
                    number: '03',
                    title: 'Gia công In 3D',
                    detail: 'Vận hành dây chuyền in 3D công nghiệp với thông số cài đặt chuẩn xác, kiểm soát nhiệt độ buồng và độ phân giải từng lớp in 0.05mm.',
                },
                {
                    number: '04',
                    title: 'Đóng gói chuẩn quy cách',
                    detail: 'Gọt bỏ support, làm sạch siêu âm, xử lý bề mặt hoàn thiện mịn đẹp, kiểm tra kích thước dung sai và đóng gói chống sốc an toàn.',
                },
                {
                    number: '05',
                    title: 'Giao hàng tận nơi',
                    detail: 'Bàn giao tận tay khách hàng qua mạng lưới vận chuyển hỏa tốc toàn quốc, kèm chính sách bảo hành và hướng dẫn sử dụng chu đáo.',
                },
            ],
        };
    },
    computed: {
        steps() {
            if (this.customSteps && this.customSteps.length > 0) {
                return this.customSteps;
            }
            return this.defaultSteps;
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
