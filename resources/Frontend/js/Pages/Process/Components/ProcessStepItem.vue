<template>
    <!-- Figma Node 2283:6883 / 2283:6899 / 2283:6915 / 2283:6931 / 2283:6947: padding: 40px 0px, border-t: 1px solid #404968 -->
    <div
        :id="'step-' + step.number"
        v-scroll-reveal
        class="border-t border-[#404968] py-8 sm:py-10 lg:pt-[39px] lg:pb-10 first:border-t-0 first:lg:pt-10 scroll-mt-24"
    >
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-10 justify-between items-start">
            <!-- Left Column: Định danh bước (Figma 240px wide, gap: 20px) -->
            <div class="w-full lg:w-[240px] shrink-0 flex flex-row lg:flex-col items-baseline lg:items-start gap-4 lg:gap-5">
                <!-- Chỉ dấu: Số bước (Figma Node 2283:6886: Inter 42px / 51px / Bold / #84ADFF) -->
                <span class="font-sans font-bold text-[32px] sm:text-[42px] leading-[40px] sm:leading-[51px] text-[#84ADFF] select-none">
                    {{ step.number }}
                </span>
                <!-- Tiêu đề (Figma Node 2283:6889: 28px / 33px / Bold / #FAFAFA) -->
                <h2 class="text-xl sm:text-2xl lg:text-[28px] font-bold text-[#FAFAFA] leading-[33px]">
                    {{ tt(step.title) }}
                </h2>
            </div>

            <!-- Right Column: Nội dung bước (Figma 872px wide, gap: 24px) -->
            <div class="w-full lg:w-[872px] space-y-6">
                <!-- Mô tả bước (Figma Node 2283:6891: 18px / 27px / #FAFAFA) -->
                <p class="text-base sm:text-lg text-[#FAFAFA] leading-relaxed lg:leading-[27px] font-normal">
                    {{ tt(step.description) }}
                </p>

                <!-- Phối hợp: 2-Column Coordination Table (Figma Node 2283:6892, gap: 32px) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-8">
                    <!-- Column 1: BẠN CHUẨN BỊ (Figma 420px wide, gap: 8px) -->
                    <div class="flex flex-col gap-2">
                        <h3 class="font-semibold text-[13px] leading-[16px] text-[#84ADFF] uppercase">
                            {{ tt('BẠN CHUẨN BỊ') }}
                        </h3>
                        <p class="text-sm sm:text-base text-[#B9C0D4] leading-relaxed lg:leading-[26px]">
                            {{ tt(step.clientPrep) }}
                        </p>
                    </div>

                    <!-- Column 2: THÀNH KHANG TECSAT THỰC HIỆN -->
                    <div class="flex flex-col gap-2">
                        <h3 class="font-semibold text-[13px] leading-[16px] text-[#84ADFF] uppercase">
                            {{ tt('THÀNH KHANG TECSAT THỰC HIỆN') }}
                        </h3>
                        <p class="text-sm sm:text-base text-[#B9C0D4] leading-relaxed lg:leading-[26px]">
                            {{ tt(step.makerAction) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    step: {
        type: Object,
        required: true,
    },
})
</script>
