<template>
    <section class="pt-8 pb-12 lg:pt-12 lg:pb-16">
        <div
            class="relative w-full max-w-[1152px] mx-auto min-h-[183px] rounded-[16px] overflow-hidden p-8 sm:p-10 lg:px-12 lg:py-8 shadow-2xl flex flex-col lg:flex-row lg:items-center justify-between gap-6"
            style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
        >
            <!-- Decorative soft light glow -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>

            <!-- Content Area (Figma 2346:742) -->
            <div class="relative z-10 max-w-2xl space-y-2">
                <h3 class="text-2xl sm:text-3xl lg:text-[32px] font-bold text-[#FAFAFA] tracking-tight leading-tight lg:leading-[40px] font-sans">
                    Tạo nên phiên bản mang dấu ấn riêng
                </h3>
                <p class="text-sm sm:text-base lg:text-[16px] text-[#F5F8FF] leading-relaxed lg:leading-[24px] font-normal">
                    Gửi hình ảnh tham khảo hoặc chia sẻ ý tưởng để HH Maker cùng bạn định hình sản phẩm mong muốn. Chúng tôi sẽ tư vấn giải pháp phù hợp với phong cách, nhu cầu và ngân sách của bạn.
                </p>
            </div>

            <!-- CTA Single Action Button (Figma 2346:745 - 2346:749) -->
            <div class="relative z-10 shrink-0">
                <Link
                    href="/lien-he"
                    class="h-10 w-full sm:w-[175px] rounded-[8px] bg-[#F5F8FF] hover:bg-white text-[#111322] font-medium text-[16px] inline-flex items-center justify-center transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 select-none"
                >
                    Nhận tư vấn ngay
                </Link>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'ProductCtaBanner',
}
</script>
