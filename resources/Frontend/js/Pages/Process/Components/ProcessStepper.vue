<template>
    <!-- SECTION 2: Lộ trình năm bước (Figma Node 2283:6851, 1440x176px, padding: 16px 144px 64px) -->
    <section id="process-steps" class="relative w-full py-4 sm:py-6 lg:pt-4 lg:pb-16 scroll-mt-24">
        <!-- Container max-width 1152px (1440px - 288px padding) -->
        <div class="max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
            <!-- Stepper Grid (5 steps, 230px each in Figma) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-[10.5px]">
                <div
                    v-for="step in steps"
                    :key="step.number"
                    @click="scrollToStep(step.number)"
                    role="button"
                    tabindex="0"
                    @keydown.enter="scrollToStep(step.number)"
                    @keydown.space.prevent="scrollToStep(step.number)"
                    class="group relative border-t-2 border-[#84ADFF] pt-5 pb-5 pl-4 pr-2 transition-colors duration-200 cursor-pointer flex flex-col justify-between min-h-[96px] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#84ADFF]/50"
                    :title="tt('Xem chi tiết bước') + ' ' + step.number + ': ' + tt(step.title)"
                >
                    <!-- Top Row: Step Number & Trailing Icon (Figma 206x18px) -->
                    <div class="flex items-center justify-between w-full">
                        <span class="text-sm font-semibold tracking-wider text-[#84ADFF] leading-[17px] select-none font-mono">
                            {{ step.number }}
                        </span>

                        <!-- Arrow Right for steps 01-04, Check for step 05 (Figma 18x18px) -->
                        <div class="text-[#84ADFF] transition-transform duration-200 group-hover:translate-x-0.5">
                            <!-- Checkmark for step 05 (Figma Node 2283:6879) -->
                            <svg
                                v-if="step.isLast"
                                class="w-[18px] h-[18px]"
                                viewBox="0 0 36 36"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M29.9996 9L13.5012 25.4988L6.00195 17.9993"
                                    stroke="currentColor"
                                    stroke-width="3.2"
                                    stroke-linecap="round"
                                />
                            </svg>

                            <!-- Arrow Right for steps 01-04 (Figma Node 2283:6855) -->
                            <svg
                                v-else
                                class="w-[18px] h-[18px]"
                                viewBox="0 0 36 36"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M7.49902 18H28.5014M18.0002 28.5012L28.5014 18L18.0002 7.49878"
                                    stroke="currentColor"
                                    stroke-width="3.2"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </div>
                    </div>

                    <!-- Bottom Row: Step Title (Figma 16px / 26px / Semibold / #FAFAFA) -->
                    <div class="mt-3">
                        <span class="text-base font-semibold text-[#FAFAFA] group-hover:text-white transition-colors block leading-[26px]">
                            {{ tt(step.title) }}
                        </span>
                    </div>

                    <!-- Subtle active hover glow line -->
                    <div
                        class="absolute -top-[2px] left-0 right-0 h-[2px] bg-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                    ></div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
const steps = [
    { number: '01', title: 'Liên hệ', isLast: false },
    { number: '02', title: 'Báo giá', isLast: false },
    { number: '03', title: 'Gia công 3D', isLast: false },
    { number: '04', title: 'Đóng gói', isLast: false },
    { number: '05', title: 'Gửi hàng', isLast: true },
]

const scrollToStep = (stepNumber) => {
    if (typeof document === 'undefined') return
    const target = document.getElementById('step-' + stepNumber)
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
}
</script>
