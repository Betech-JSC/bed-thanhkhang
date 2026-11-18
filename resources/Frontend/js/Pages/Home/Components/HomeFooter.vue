<!--
  @deprecated
  COMPONENT NÀY HIỆN KHÔNG ĐƯỢC SỬ DỤNG TRONG TRANG CHỦ.
  Toàn bộ dự án và trang chủ sử dụng Footer toàn cục tại: resources/Frontend/js/Components/Footer.vue
  (được nạp tự động thông qua resources/Frontend/js/Layouts/MainLayout.vue).
  File này được lưu giữ tham chiếu lịch sử theo quy chuẩn Strict Safe Guard.
-->
<template>
    <footer class="bg-dark-bg border-t border-dark-border text-secondary pt-14 pb-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <!-- 4 Column Grid (Figma 2261:5820) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12">
                <!-- Column 1: Brand Info (5 cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <Link href="/" class="inline-flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#2970FF] to-[#00359E] text-white font-black text-sm tracking-wider">TK</span>
                        <span class="font-extrabold text-base tracking-wider uppercase text-white">THÀNH KHANG TECSAT</span>
                    </Link>
                    <p class="text-sm text-secondary/90 leading-relaxed max-w-sm">
                        {{ tt('Giải pháp thiết bị công nghiệp B2B: linh kiện thang cuốn, thang nâng ô tô, xích tải Universal Chains, hệ thống băng tải và quạt BLDC HVLS.') }}
                    </p>
                    <div class="space-y-1.5 text-xs text-secondary/80 pt-2">
                        <p><strong class="text-white">{{ tt('Trụ sở chính:') }}</strong> {{ tt('Tầng 25, Tòa nhà TNR, 54A Nguyễn Chí Thanh, Đống Đa, Hà Nội') }}</p>
                        <p><strong class="text-white">Hotline:</strong> 0967 732 665</p>
                        <p><strong class="text-white">Email:</strong> info@dvktthanhkhang.vn</p>
                    </div>
                </div>

                <!-- Column 2: Dịch vụ (2 cols) -->
                <div class="lg:col-span-2 space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider font-sans">
                        {{ tt('Giải pháp') }}
                    </h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                {{ tt('Phụ tùng Thang cuốn') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                {{ tt('Xích tải Universal Chains') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                {{ tt('Hệ thống Băng tải') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                {{ tt('Quạt trần BLDC HVLS') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                {{ tt('Tư vấn Kỹ thuật OEM') }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Sản phẩm (2 cols) -->
                <div class="lg:col-span-2 space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider font-sans">
                        {{ tt('Sản phẩm') }}
                    </h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <Link href="/san-pham" class="hover:text-primary transition-colors">
                                {{ tt('Xích bậc thang cuốn') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/san-pham" class="hover:text-primary transition-colors">
                                {{ tt('Xích thang nâng ô tô') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/san-pham" class="hover:text-primary transition-colors">
                                {{ tt('Xích băng tải hành lý') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/san-pham" class="hover:text-primary transition-colors">
                                {{ tt('Xích chốt rỗng') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/san-pham" class="hover:text-primary transition-colors">
                                {{ tt('Linh kiện dây chuyền') }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Hỗ trợ & Chính sách (3 cols) -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider font-sans">
                        {{ tt('Hỗ trợ khách hàng') }}
                    </h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="#process-section" class="hover:text-primary transition-colors">
                                {{ tt('Quy trình cung ứng B2B') }}
                            </a>
                        </li>
                        <li>
                            <a href="#contact-section" class="hover:text-primary transition-colors">
                                {{ tt('Gửi bản vẽ & yêu cầu báo giá') }}
                            </a>
                        </li>
                        <li>
                            <Link href="/chinh-sach" class="hover:text-primary transition-colors">
                                {{ tt('Chính sách bảo hành') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/chinh-sach" class="hover:text-primary transition-colors">
                                {{ tt('Chính sách bảo mật hồ sơ') }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div
                class="pt-8 border-t border-dark-border/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted"
            >
                <p>© {{ currentYear }} Thành Khang Tecsat. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <Link href="/chinh-sach" class="hover:text-white transition-colors">
                        {{ tt('Điều khoản dịch vụ') }}
                    </Link>
                    <Link href="/chinh-sach" class="hover:text-white transition-colors">
                        {{ tt('Chính sách bảo mật') }}
                    </Link>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    name: 'HomeFooter',
    data() {
        return {
            currentYear: new Date().getFullYear(),
        };
    },
};
</script>
