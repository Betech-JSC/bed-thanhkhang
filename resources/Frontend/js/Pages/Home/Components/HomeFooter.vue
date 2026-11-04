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
                        <img
                            src="/assets/images/header/logo.png"
                            alt="HH Maker Logo"
                            class="h-10 w-auto object-contain"
                        />
                    </Link>
                    <p class="text-sm text-secondary/90 leading-relaxed max-w-sm">
                        {{ tt('Hiện thực hóa mọi ý tưởng với công nghệ in 3D hiện đại. Rút ngắn khoảng cách giữa thiết kế số và sản phẩm vật lý chuẩn xác.') }}
                    </p>
                    <div class="space-y-1.5 text-xs text-secondary/80 pt-2">
                        <p><strong class="text-white">{{ tt('Xưởng sản xuất:') }}</strong> {{ tt('Hà Nội & TP. Hồ Chí Minh') }}</p>
                        <p><strong class="text-white">Hotline:</strong> 0968 000 000</p>
                        <p><strong class="text-white">Email:</strong> contact@hhmaker.vn</p>
                    </div>
                </div>

                <!-- Column 2: Dịch vụ (2 cols) -->
                <div class="lg:col-span-2 space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider font-sans">
                        {{ tt('Dịch vụ') }}
                    </h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                {{ tt('Gia công in 3D') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                {{ tt('Thiết kế mô hình CAD') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                {{ tt('Quét 3D Scanning') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                Reverse Engineering
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="hover:text-primary transition-colors">
                                {{ tt('Sơn & Xử lý bề mặt') }}
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
                                {{ tt('Móc khoá custom') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/san-pham" class="hover:text-primary transition-colors">
                                {{ tt('Decor văn phòng') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/san-pham" class="hover:text-primary transition-colors">
                                {{ tt('Nguyên mẫu cơ khí') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/san-pham" class="hover:text-primary transition-colors">
                                {{ tt('Quà tặng doanh nghiệp') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/san-pham" class="hover:text-primary transition-colors">
                                {{ tt('Đồ chơi chuyển động') }}
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
                                {{ tt('Quy trình đặt hàng & in 3D') }}
                            </a>
                        </li>
                        <li>
                            <a href="#contact-section" class="hover:text-primary transition-colors">
                                {{ tt('Hướng dẫn gửi file 3D (STL/STEP)') }}
                            </a>
                        </li>
                        <li>
                            <Link href="/chinh-sach" class="hover:text-primary transition-colors">
                                {{ tt('Chính sách bảo hành & Đổi trả') }}
                            </Link>
                        </li>
                        <li>
                            <Link href="/chinh-sach" class="hover:text-primary transition-colors">
                                {{ tt('Chính sách bảo mật dữ liệu file') }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div
                class="pt-8 border-t border-dark-border/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted"
            >
                <p>© {{ currentYear }} HH Maker. All rights reserved.</p>
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
