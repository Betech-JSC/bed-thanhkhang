<template>
    <footer id="footer-section" class="bg-[#111322] border-t border-[#F5F8FF]/10 text-secondary pt-14 pb-8">
        <div class="w-full max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-0 space-y-12">
            <!-- 4 Column Grid (Figma 2261:5820 / 2226:3250) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <!-- Column 1: Brand Info (5 cols / 361px) (Figma 2226:3251) -->
                <div class="lg:col-span-5 space-y-5">
                    <Link href="/" class="inline-flex items-center">
                        <img
                            src="/assets/images/header/figma-header-logo.svg"
                            alt="HH Maker Logo"
                            class="h-[48px] w-auto object-contain"
                        />
                    </Link>
                    <div class="space-y-2 pt-1 max-w-[361px]">
                        <h3 class="text-xl lg:text-[24px] font-medium text-[#F5F8FF] tracking-normal leading-[34px]">
                            CÔNG TY IN ẤN 3D HH MAKER
                        </h3>
                        <p class="text-sm sm:text-base text-[#D1E0FF] leading-[22px] font-normal">
                            {{ displayAddress }}
                        </p>
                    </div>
                </div>

                <!-- Column 2: Trang chủ / Điều hướng (2 cols) (Figma 2226:3263) -->
                <div class="lg:col-span-2 space-y-4">
                    <h4 class="text-base font-bold text-[#D1E0FF] tracking-normal font-sans leading-[22px]">
                        Trang chủ
                    </h4>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <Link href="/san-pham" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                Sản phẩm
                            </Link>
                        </li>
                        <li>
                            <Link href="/gioi-thieu" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                Giới thiệu
                            </Link>
                        </li>
                        <li>
                            <Link href="/quy-trinh" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                Quy trình
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Dịch vụ (2 cols) (Figma 2226:3270) -->
                <div class="lg:col-span-2 space-y-4">
                    <h4 class="text-base font-bold text-[#D1E0FF] tracking-normal font-sans leading-[22px]">
                        Dịch vụ
                    </h4>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <Link href="/dich-vu" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                Thiết kế 3D
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                Gia công 3D
                            </Link>
                        </li>
                        <li>
                            <Link href="/dich-vu" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                Custom 3D
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Liên hệ tư vấn (3 cols) (Figma 2226:3276) -->
                <div class="lg:col-span-3 space-y-4">
                    <h4 class="text-base font-bold text-[#D1E0FF] tracking-normal font-sans leading-[22px]">
                        Liên hệ tư vấn
                    </h4>
                    <div class="space-y-3 text-sm font-medium">
                        <a
                            :href="'tel:' + displayHotline.replace(/\s+/g, '')"
                            class="flex items-center gap-2.5 text-[#F5F8FF] hover:text-[#2970FF] transition-colors"
                        >
                            <svg class="w-5 h-5 text-[#2970FF] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>{{ displayHotline }}</span>
                        </a>
                        <a
                            :href="'mailto:' + displayEmail"
                            class="flex items-center gap-2.5 text-[#F5F8FF] hover:text-[#2970FF] transition-colors"
                        >
                            <svg class="w-5 h-5 text-[#2970FF] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ displayEmail }}</span>
                        </a>
                        <a
                            href="mailto:lamhonghai123@gmail.com"
                            class="flex items-center gap-2.5 text-[#F5F8FF] hover:text-[#2970FF] transition-colors"
                        >
                            <svg class="w-5 h-5 text-[#2970FF] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>lamhonghai123@gmail.com</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright Bar (Figma 2261:5820 / 2226:3288) -->
            <div
                class="pt-6 border-t border-[#F5F8FF]/20 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#D1E0FF]"
            >
                <p>© Copyright 2026 HH MAKER</p>
                <div class="flex items-center gap-6 sm:gap-10">
                    <Link href="/chinh-sach" class="text-[#D1E0FF] hover:text-white transition-colors">
                        Chính sách bảo mật
                    </Link>
                    <Link href="/chinh-sach" class="text-[#D1E0FF] hover:text-white transition-colors">
                        Điều khoản sử dụng
                    </Link>
                    <Link href="/chinh-sach" class="text-[#D1E0FF] hover:text-white transition-colors">
                        Chính sách Cookie
                    </Link>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'

export default {
    name: 'Footer',
    components: {
        Link,
    },
    props: {
        address: {
            type: String,
            default: '',
        },
        hotline: {
            type: String,
            default: '',
        },
        email: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            currentYear: new Date().getFullYear(),
        }
    },
    computed: {
        displayAddress() {
            return this.address || this.$page.props.global?.general_address || 'Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh'
        },
        displayHotline() {
            return this.hotline || this.$page.props.global?.general_phone || '0906636425'
        },
        displayEmail() {
            return this.email || this.$page.props.global?.general_email || 'info@hhmaker.vn'
        },
    },
}
</script>
