<template>
    <section
        v-scroll-reveal
        id="process-section"
        class="relative w-full overflow-hidden text-white bg-dark-bg"
    >
        <!-- Centered Main Canvas matching Figma Node 2261:5723 (1440px x 892px) -->
        <div
            class="relative z-10 w-full max-w-[1440px] mx-auto min-h-[600px] lg:h-[892px] flex flex-col lg:flex-row items-stretch overflow-hidden"
            style="background: linear-gradient(0deg, #111322 20%, #2970FF 94%);"
        >
            <!-- Left Column: 3D Wave Graphic + 05 Hero Headline (Figma Node 2261:5724: width 639px) -->
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

                <!-- Giant 05 & Headline directly overlaid on 3D Graphic (Figma Node 2261:5727) -->
                <div class="relative z-10 max-w-[435px]">
                    <div
                        class="process-giant-number text-[72px] sm:text-[96px] lg:text-[120px] font-bold text-[#FAFAFA] leading-none tracking-tight font-sans select-none drop-shadow-md"
                    >
                        {{ stepCount }}
                    </div>

                    <h2
                        class="text-2xl sm:text-3xl lg:text-[36px] font-bold text-[#FAFAFA] leading-tight lg:leading-[50px] font-sans uppercase mt-4 sm:mt-6 lg:mt-8 tracking-normal drop-shadow-sm"
                    >
                        {{ tt(title) }}
                    </h2>
                </div>
            </div>

            <!-- Right Column: Description & 5-Step Process (Figma Node 2261:5730: width 657px with 1px border-left) -->
            <div
                class="flex-1 lg:h-full lg:border-l lg:border-[#B5B5B5]/30 relative flex flex-col justify-center py-10 lg:py-12 px-6 sm:px-10 lg:pl-[64px] lg:pr-14"
            >
                <p
                    class="text-sm sm:text-base text-[#FAFAFA] leading-relaxed lg:leading-[24px] font-normal max-w-[560px]"
                >
                    {{ tt(description) }}
                </p>

                <!-- 5 Steps Minimalist Rows (Figma Node 2261:5732) -->
                <div v-if="steps.length > 0" class="mt-8 sm:mt-10 lg:mt-12 max-w-[560px] w-full border-b border-[#B5B5B5]/30">
                    <div
                        v-for="(step, idx) in steps"
                        :key="step.number"
                        class="border-t border-[#B5B5B5]/30 transition-colors duration-200 group hover:bg-white/[0.05]"
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
                                class="text-lg sm:text-xl lg:text-[22px] font-bold text-[#FAFAFA] group-hover:text-white transition-colors leading-[32px] flex-1"
                            >
                                {{ tt(step.title) }}
                            </span>
                        </button>
                        <Transition
                            enter-active-class="transition-transform duration-200 ease-out"
                            enter-from-class="translate-y-1"
                            enter-to-class="translate-y-0"
                            leave-active-class="transition-transform duration-150 ease-in"
                            leave-from-class="translate-y-0"
                            leave-to-class="translate-y-1"
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
            default: tt('BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER'),
        },
        description: {
            type: String,
            default: tt('Khách hàng gửi file 3D hoặc chia sẻ ý tưởng, đội ngũ kỹ thuật của HH Maker sẽ tiến hành phân tích độ khả thi, tư vấn công nghệ in tối ưu, cũng như lựa chọn loại vật liệu phù hợp nhất để đảm bảo sản phẩm đạt độ bền, tính thẩm mỹ và công năng sử dụng đúng như kỳ vọng của bạn.'),
        },
        customSteps: {
            type: Array,
            default: null,
        },
    },
    data() {
        return {
            activeStep: null,
            };
    },
    computed: {
        steps() {
            if (this.customSteps && this.customSteps.length > 0) return this.customSteps;
            return [];
        },
    },
    methods: {
        toggleStep(idx) {
            this.activeStep = this.activeStep === idx ? null : idx;
        },
    },
};
</script>
