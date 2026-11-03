<template>
    <!-- SECTION 4: FAQ Section (Figma Node 2334:8455, 1440x621px, padding: 80px 144px 120px) -->
    <section class="relative w-full pt-12 pb-20 lg:pt-20 lg:pb-[120px] overflow-hidden">
        <!-- Container max-width 1152px -->
        <div class="max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0 space-y-8">
            <!-- Section Header (Figma Node 2334:8456, gap: 16px) -->
            <div class="space-y-4">
                <span class="text-sm font-semibold tracking-wider text-[#84ADFF] uppercase select-none leading-[20px] block">
                    CÂU HỎI THƯỜNG GẶP
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-[32px] font-bold text-[#FAFAFA] leading-[37px]">
                    Giải đáp trước khi liên hệ
                </h2>
            </div>

            <!-- FAQ Accordion List (Figma Node 2390:5367, gap: 16px) -->
            <div class="space-y-4">
                <div
                    v-for="(faq, index) in faqs"
                    :key="index"
                    class="rounded-xl border border-[#5D6B98] bg-white/20 backdrop-blur-[10px] transition-all duration-200 overflow-hidden"
                >
                    <!-- Question Button (Figma padding: 24px) -->
                    <button
                        type="button"
                        @click="toggleFaq(index)"
                        class="w-full text-left p-5 sm:p-6 flex items-center justify-between gap-4 cursor-pointer select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-[#84ADFF]"
                        :aria-expanded="activeFaq === index"
                    >
                        <span class="text-base sm:text-lg font-medium text-[#FAFAFA] leading-[22px]">
                            {{ faq.question }}
                        </span>

                        <!-- Indicator Plus / Minus (Figma Node 2399:1430 / 2399:1436: 24x24px) -->
                        <div class="w-6 h-6 shrink-0 flex items-center justify-center text-[#FCFCFC] transition-transform duration-200">
                            <!-- Minus indicator if open (Figma collapse_indicator) -->
                            <svg
                                v-if="activeFaq === index"
                                class="w-6 h-6"
                                viewBox="0 0 48 48"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <rect x="8" y="22" width="32" height="4" rx="2" fill="#FCFCFC" />
                            </svg>

                            <!-- Plus indicator if closed (Figma expand_indicator) -->
                            <svg
                                v-else
                                class="w-6 h-6"
                                viewBox="0 0 48 48"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M24 10V24M24 24V38M24 24H10M24 24H38"
                                    stroke="#FCFCFC"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </div>
                    </button>

                    <!-- Expandable Answer (Figma Node 2399:1432, 16px / 26px / #FCFCFC) -->
                    <div
                        v-show="activeFaq === index"
                        class="px-5 sm:px-6 pb-6 -mt-1 text-sm sm:text-base text-[#FCFCFC] leading-relaxed lg:leading-[26px]"
                    >
                        <p>{{ faq.answer }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from 'vue'

const activeFaq = ref(0) // Câu 1 mặc định mở theo Figma

const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index
}

const faqs = [
    {
        question: 'Tôi chưa có sẵn file thiết kế 3D, bên mình có hỗ trợ dựng file không?',
        answer: 'Có. HH MAKER hỗ trợ dựng file 3D từ ý tưởng, bản phác thảo hoặc hình ảnh tham khảo của bạn. Bạn chỉ cần cung cấp kích thước, công năng và yêu cầu mong muốn; đội ngũ sẽ tư vấn phương án thiết kế, báo chi phí dựng file và gửi bản 3D để bạn duyệt trước khi sản xuất.',
    },
    {
        question: 'Để nhận được bảng báo giá chính xác và nhanh chóng nhất cho dự án của mình, tôi cần cung cấp những gì?',
        answer: 'Bạn chỉ cần gửi file thiết kế 3D (nếu có định dạng .STL, .OBJ, .STEP...), kích thước mong muốn, yêu cầu về độ mịn, vật liệu hoặc ảnh mô tả ý tưởng. Đội ngũ kỹ thuật HH Maker sẽ phản hồi và gửi báo giá chi tiết trong vòng 2-4 giờ làm việc.',
    },
    {
        question: 'Tôi muốn đặt sản phẩm theo ý tưởng riêng hoặc điều chỉnh khác đi so với các mẫu có sẵn thì có được không?',
        answer: 'Hoàn toàn được! Dịch vụ làm đồ Custom và chế tác theo yêu cầu là thế mạnh hàng đầu tại HH Maker. Chúng tôi sẵn sàng tinh chỉnh kích thước, tỷ lệ, phối màu hoặc thiết kế riêng biệt hoàn toàn theo concept của bạn.',
    },
]
</script>
