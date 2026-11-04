<template>
    <Head>
        <title>Liên hệ tư vấn - HH Maker | Đưa sáng tạo vượt ra khỏi màn hình</title>
        <meta
            name="description"
            content="Liên hệ ngay với HH Maker để trao đổi ý tưởng thiết kế 3D, gia công in 3D công nghiệp hoặc chế tác sản phẩm custom theo yêu cầu."
        />
    </Head>

    <div class="bg-[#111322] text-white min-h-screen overflow-x-hidden selection:bg-primary selection:text-white">
        <!-- ======================================================== -->
        <!-- SECTION 1: HERO BANNER & ARTWORK (Figma Node 2308:8195)  -->
        <!-- ======================================================== -->
        <section
            class="relative w-full overflow-hidden pt-12 md:pt-16 pb-20 md:pb-28"
            style="background: linear-gradient(0deg, #111322 20%, #2970FF 94%);"
        >
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1152px] relative">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                    <!-- Left Hero Content (Figma Node 2283:6728) -->
                    <div class="lg:col-span-7 space-y-4 md:space-y-6 pt-4 sm:pt-6">
                        <div class="inline-block text-[#D1E0FF] text-sm md:text-base font-semibold tracking-wider uppercase">
                            KẾT NỐI VỚI HH MAKER
                        </div>

                        <h1 class="text-white text-3xl sm:text-4xl lg:text-[38px] lg:leading-[46px] font-bold uppercase tracking-tight max-w-[740px]">
                            KỂ CHÚNG TÔI NGHE Ý TƯỞNG CỦA BẠN
                        </h1>

                        <p class="text-[#D1E0FF] text-base md:text-lg leading-[26px] max-w-[519px] font-normal">
                            Một bản phác thảo, một file 3D hay một ý tưởng chưa thành hình. Hãy bắt đầu cuộc trao đổi qua kênh thuận tiện nhất với bạn.
                        </p>
                    </div>

                    <!-- Right Hero Logo 3D HH Maker (Figma Node 2308:8196) -->
                    <div class="hidden lg:flex lg:col-span-5 items-center justify-end relative">
                        <img
                            src="/images/contact/hh_maker-01_2.png"
                            alt="HH Maker Logo 3D - From Ideas To Reality"
                            class="w-[260px] xl:w-[310px] max-w-full h-auto object-contain pointer-events-none select-none drop-shadow-[0_12px_28px_rgba(0,0,0,0.35)] transition-transform duration-500 hover:scale-[1.02]"
                            loading="eager"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SECTION 2: 3 THẺ KÍNH KÊNH LIÊN HỆ (Figma Node 2308:8241)-->
        <!-- ======================================================== -->
        <section class="relative z-20 -mt-10 sm:-mt-14 mb-12 sm:mb-16">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1152px]">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- 1. Card Zalo (Node 2308:8242) -->
                    <a
                        :href="displayZaloUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bg-white/20 border border-[#5D6B98] backdrop-blur-[10px] rounded-[12px] p-7 transition-all duration-300 hover:border-primary hover:bg-white/[0.28] hover:shadow-glow-primary-subtle group flex flex-col justify-between min-h-[193px]"
                    >
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white group-hover:text-primary transition-colors">
                            <!-- Lucide MessageCircle (28x28) -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="28"
                                height="28"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
                            </svg>
                        </div>
                        <div class="space-y-2 mt-4">
                            <h3 class="text-2xl font-semibold text-[#FAFAFA] group-hover:text-primary transition-colors leading-[29px]">
                                Tư vấn qua Zalo
                            </h3>
                            <p class="text-base text-[#B9C0D4] leading-[26px]">
                                Trao đổi ý tưởng, hình ảnh và yêu cầu sản phẩm
                            </p>
                        </div>
                    </a>

                    <!-- 2. Card Hotline (Node 2308:8252) -->
                    <a
                        :href="'tel:' + displayPhone.replace(/\s+/g, '')"
                        class="bg-white/20 border border-[#5D6B98] backdrop-blur-[10px] rounded-[12px] p-7 transition-all duration-300 hover:border-primary hover:bg-white/[0.28] hover:shadow-glow-primary-subtle group flex flex-col justify-between min-h-[193px]"
                    >
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white group-hover:text-primary transition-colors">
                            <!-- Lucide Phone (28x28) -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="28"
                                height="28"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                                />
                            </svg>
                        </div>
                        <div class="space-y-2 mt-4">
                            <h3 class="text-2xl font-semibold text-[#FAFAFA] group-hover:text-primary transition-colors leading-[29px]">
                                {{ displayPhone }}
                            </h3>
                            <p class="text-base text-[#B9C0D4] leading-[26px]">
                                Gọi HH Maker để trao đổi trực tiếp về dự án của bạn
                            </p>
                        </div>
                    </a>

                    <!-- 3. Card Email (Node 2308:8262) -->
                    <a
                        :href="'mailto:' + displayEmail"
                        class="bg-white/20 border border-[#5D6B98] backdrop-blur-[10px] rounded-[12px] p-7 transition-all duration-300 hover:border-primary hover:bg-white/[0.28] hover:shadow-glow-primary-subtle group flex flex-col justify-between min-h-[193px]"
                    >
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white group-hover:text-primary transition-colors">
                            <!-- Lucide Mail (28x28) -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="28"
                                height="28"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </div>
                        <div class="space-y-2 mt-4">
                            <h3 class="text-2xl font-semibold text-[#FAFAFA] group-hover:text-primary transition-colors leading-[29px]">
                                {{ displayEmail }}
                            </h3>
                            <p class="text-base text-[#B9C0D4] leading-[26px]">
                                Gửi mô tả chi tiết và tài liệu tham khảo của bạn
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SECTION 3: GỬI YÊU CẦU TƯ VẤN (Figma Node 2283:6767)     -->
        <!-- ======================================================== -->
        <section class="py-12 sm:py-16 md:py-20 bg-[#111322]">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1152px]">
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-16 items-start">
                    <!-- Cột Trái: Lời Mời & Artwork Điêu khắc xanh (Node 2283:6768) - Width 380px -->
                    <div class="w-full lg:w-[380px] shrink-0 flex flex-col justify-between space-y-6">
                        <div class="space-y-3">
                            <div class="text-[#84ADFF] text-sm md:text-base font-semibold tracking-wider uppercase">
                                TỪ Ý TƯỞNG ĐẾN HIỆN THỰC
                            </div>
                            <h2 class="text-white text-2xl sm:text-3xl lg:text-[36px] font-bold leading-tight lg:leading-[42px]">
                                CÙNG BẮT ĐẦU MỘT SẢN PHẨM MỚI.
                            </h2>
                            <p class="text-[#B9C0D4] text-sm sm:text-base leading-[26px]">
                                Điền thông tin để HH Maker hiểu thêm về nhu cầu của bạn và có cơ sở trao đổi phương án phù hợp.
                            </p>
                        </div>

                        <!-- Khung Artwork Hình ảnh điêu khắc xanh (Node 2283:6772 & 2283:6773: 380px x 384px) -->
                        <div class="w-full max-w-[380px] mx-auto lg:mx-0 rounded-[12px] overflow-hidden border border-[#5D6B98]/40 shadow-2xl bg-dark-surface/40 group">
                            <img
                                src="/images/contact/dieu_khac_xanh.png"
                                alt="HH Maker tác phẩm điêu khắc 3D xanh"
                                class="w-full h-auto aspect-[380/384] object-cover transition-transform duration-500 group-hover:scale-105 select-none"
                                loading="lazy"
                            />
                        </div>
                    </div>

                    <!-- Cột Phải: Biểu mẫu Form (Node 2283:6775) - Width 708px -->
                    <div class="w-full lg:w-[708px] shrink-0">
                        <h3 class="text-white text-2xl sm:text-[28px] font-bold leading-[33px] mb-6">
                            Thông tin yêu cầu
                        </h3>

                        <form @submit.prevent="submitContact" novalidate class="space-y-5">
                            <!-- Hàng 1: Họ tên & Số điện thoại (Node 2283:6777) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                <!-- Họ và tên (Node 2283:6778) -->
                                <div>
                                    <label class="block text-sm font-medium text-[#FAFAFA] mb-2">
                                        Họ và tên <span class="text-red-400">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.name"
                                        placeholder="Nhập họ và tên"
                                        :class="[
                                            'w-full h-[52px] bg-white/20 border rounded-[8px] px-4 text-[#FAFAFA] placeholder-[#B9C0D4] text-sm transition-all focus:outline-none',
                                            clientErrors.name || form.errors.name
                                                ? 'border-red-500 focus:border-red-500'
                                                : 'border-[#5D6B98] focus:border-primary focus:ring-1 focus:ring-primary focus:bg-white/[0.25]'
                                        ]"
                                    />
                                    <p v-if="clientErrors.name || form.errors.name" class="text-xs text-red-400 mt-1.5">
                                        {{ clientErrors.name || form.errors.name }}
                                    </p>
                                </div>

                                <!-- Số điện thoại (Node 2283:6782) -->
                                <div>
                                    <label class="block text-sm font-medium text-[#FAFAFA] mb-2">
                                        Số điện thoại <span class="text-red-400">*</span>
                                    </label>
                                    <input
                                        type="tel"
                                        v-model="form.phone"
                                        placeholder="Nhập số điện thoại"
                                        :class="[
                                            'w-full h-[52px] bg-white/20 border rounded-[8px] px-4 text-[#FAFAFA] placeholder-[#B9C0D4] text-sm transition-all focus:outline-none',
                                            clientErrors.phone || form.errors.phone
                                                ? 'border-red-500 focus:border-red-500'
                                                : 'border-[#5D6B98] focus:border-primary focus:ring-1 focus:ring-primary focus:bg-white/[0.25]'
                                        ]"
                                    />
                                    <p v-if="clientErrors.phone || form.errors.phone" class="text-xs text-red-400 mt-1.5">
                                        {{ clientErrors.phone || form.errors.phone }}
                                    </p>
                                </div>
                            </div>

                            <!-- Hàng 2: Email (Node 2283:6786) -->
                            <div>
                                <label class="block text-sm font-medium text-[#FAFAFA] mb-2">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    v-model="form.email"
                                    placeholder="Nhập địa chỉ email"
                                    :class="[
                                        'w-full h-[52px] bg-white/20 border rounded-[8px] px-4 text-[#FAFAFA] placeholder-[#B9C0D4] text-sm transition-all focus:outline-none',
                                        clientErrors.email || form.errors.email
                                            ? 'border-red-500 focus:border-red-500'
                                            : 'border-[#5D6B98] focus:border-primary focus:ring-1 focus:ring-primary focus:bg-white/[0.25]'
                                    ]"
                                />
                                <p v-if="clientErrors.email || form.errors.email" class="text-xs text-red-400 mt-1.5">
                                    {{ clientErrors.email || form.errors.email }}
                                </p>
                            </div>

                            <!-- Hàng 3: Dịch vụ quan tâm (Node 2283:6790 - Pill buttons) -->
                            <div>
                                <label class="block text-sm font-medium text-[#FAFAFA] mb-2.5">
                                    Bạn cần hỗ trợ điều gì?
                                </label>
                                <div class="flex flex-wrap gap-2.5 sm:gap-3">
                                    <button
                                        type="button"
                                        v-for="service in availableServices"
                                        :key="service"
                                        @click="form.service = service"
                                        :class="[
                                            'h-[39px] px-4 rounded-[8px] text-xs sm:text-xs font-medium transition-all duration-200 cursor-pointer flex items-center justify-center',
                                            form.service === service
                                                ? 'bg-[#243971] border border-[#528BFF] text-[#FAFAFA] shadow-[0_0_12px_rgba(82,139,255,0.35)]'
                                                : 'bg-[#1A1C32] border border-[#2C3049] text-[#FAFAFA] hover:border-[#5D6B98] hover:text-white'
                                        ]"
                                    >
                                        {{ service }}
                                    </button>
                                </div>
                            </div>

                            <!-- Hàng 4: Mô tả ý tưởng (Node 2283:6802) -->
                            <div>
                                <label class="block text-sm font-medium text-[#FAFAFA] mb-2">
                                    Mô tả ý tưởng <span class="text-red-400">*</span>
                                </label>
                                <textarea
                                    v-model="form.content"
                                    rows="4"
                                    placeholder="Chia sẻ ý tưởng, hình ảnh hoặc bản phác thảo của bạn..."
                                    :class="[
                                        'w-full h-[136px] bg-white/20 border rounded-[8px] p-4 text-[#FAFAFA] placeholder-[#B9C0D4] text-sm transition-all focus:outline-none resize-none',
                                        clientErrors.content || form.errors.content
                                            ? 'border-red-500 focus:border-red-500'
                                            : 'border-[#5D6B98] focus:border-primary focus:ring-1 focus:ring-primary focus:bg-white/[0.25]'
                                    ]"
                                ></textarea>
                                <p v-if="clientErrors.content || form.errors.content" class="text-xs text-red-400 mt-1.5">
                                    {{ clientErrors.content || form.errors.content }}
                                </p>
                            </div>

                            <!-- Hàng 5: Nút Submit (Node 2308:8396) -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full h-[56px] rounded-[8px] text-[#FAFAFA] font-semibold text-base transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer shadow-lg hover:brightness-110 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed mt-2"
                                style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
                            >
                                <svg
                                    v-if="form.processing"
                                    class="animate-spin h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Đang gửi yêu cầu...' : 'Gửi yêu cầu tư vấn' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SECTION 4: FAQ ACCORDION (Figma Node 2330:8413)          -->
        <!-- ======================================================== -->
        <section class="py-16 sm:py-20 lg:py-24 bg-[#111322] border-t border-dark-border/40">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1152px]">
                <div class="space-y-3 mb-8 sm:mb-10">
                    <div class="text-[#84ADFF] text-sm font-semibold tracking-wider uppercase">
                        CÂU HỎI THƯỜNG GẶP
                    </div>
                    <h2 class="text-white text-2xl sm:text-3xl lg:text-[32px] lg:leading-[37px] font-bold">
                        Giải đáp trước khi liên hệ
                    </h2>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(faq, index) in faqList"
                        :key="index"
                        class="bg-white/20 border border-[#5D6B98] rounded-[12px] overflow-hidden transition-all duration-200 backdrop-blur-[10px]"
                    >
                        <button
                            type="button"
                            @click="toggleFaq(index)"
                            class="w-full min-h-[72px] px-6 py-4 flex items-center justify-between text-left cursor-pointer transition-colors hover:bg-white/[0.04]"
                            :aria-expanded="activeFaq === index"
                        >
                            <span class="text-base sm:text-[18px] sm:leading-[22px] font-medium text-[#FAFAFA] pr-4">
                                {{ faq.question }}
                            </span>
                            <span
                                class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-[#FAFAFA] bg-white/10 transition-transform duration-300"
                                :class="{ 'rotate-45': activeFaq === index }"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </span>
                        </button>
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-1"
                        >
                            <div
                                v-show="activeFaq === index"
                                class="px-6 pb-6 pt-2 text-[#B9C0D4] text-base leading-[26px] border-t border-white/10"
                            >
                                {{ faq.answer }}
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SUCCESS MODAL (Dark Tech Design)                         -->
        <!-- ======================================================== -->
        <BaseModal v-model="showSuccessModal" max-width="md">
            <div class="flex flex-col items-center text-center p-4 sm:p-6 space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shadow-[0_0_20px_rgba(16,185,129,0.25)]">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="32"
                        height="32"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M20 6 9 17l-5-5" />
                    </svg>
                </div>

                <div class="space-y-2">
                    <h3 class="text-xl sm:text-2xl font-bold text-white">
                        Gửi yêu cầu thành công!
                    </h3>
                    <p class="text-[#B9C0D4] text-sm sm:text-base leading-relaxed">
                        Hệ thống đã nhận được thông tin yêu cầu của bạn. Đội ngũ kỹ thuật HH Maker sẽ liên hệ và tư vấn phương án phù hợp trong thời gian sớm nhất.
                    </p>
                </div>

                <button
                    type="button"
                    @click="showSuccessModal = false"
                    class="w-full mt-4 h-[44px] rounded-[8px] bg-primary text-white font-medium text-sm hover:bg-primary-600 transition-colors cursor-pointer"
                >
                    Đóng thông báo
                </button>
            </div>
        </BaseModal>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { Head, useForm } from '@inertiajs/inertia-vue3'
import BaseModal from '@/Components/Base/BaseModal.vue'

defineProps({
    agencies: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()

const displayZaloUrl = computed(() => {
    const raw = page.props.value?.global?.general_zalo
    if (!raw) return 'https://zalo.me/0906636425'
    if (raw.startsWith('http://') || raw.startsWith('https://')) return raw
    const digits = raw.replace(/\D/g, '')
    return digits ? `https://zalo.me/${digits}` : 'https://zalo.me/0906636425'
})

const displayPhone = computed(() => {
    return page.props.value?.global?.general_phone || '0906636425'
})

const displayEmail = computed(() => {
    return page.props.value?.global?.general_email || 'info@hhmaker.vn'
})

// Dịch vụ lựa chọn
const availableServices = ['Thiết kế 3D', 'Gia công 3D', 'Làm đồ custom']

// FAQ Accordion State & Data
const activeFaq = ref(null)

const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index
}

const faqList = [
    {
        question: 'Tôi chưa có sẵn file thiết kế 3D, bên mình có hỗ trợ dựng file không?',
        answer: 'Có, HH Maker hỗ trợ trọn gói từ khâu phác thảo ý tưởng, lên bản vẽ 2D/3D kỹ thuật số (CAD/CAM hoặc Digital Sculpting) đến khi hoàn thiện file in sẵn sàng sản xuất.',
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

// Form state using Inertia useForm
const form = useForm({
    name: '',
    phone: '',
    email: '',
    service: 'Thiết kế 3D',
    content: '',
})

const clientErrors = reactive({
    name: '',
    phone: '',
    email: '',
    content: '',
})

const showSuccessModal = ref(false)

const validate = () => {
    let isValid = true
    clientErrors.name = ''
    clientErrors.phone = ''
    clientErrors.email = ''
    clientErrors.content = ''

    if (!form.name || form.name.trim().length < 2) {
        clientErrors.name = 'Vui lòng nhập họ và tên (tối thiểu 2 ký tự).'
        isValid = false
    }

    const phoneTrimmed = (form.phone || '').trim()
    const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$/
    if (!phoneTrimmed) {
        clientErrors.phone = 'Vui lòng nhập số điện thoại.'
        isValid = false
    } else if (!phoneRegex.test(phoneTrimmed) && phoneTrimmed.length < 10) {
        clientErrors.phone = 'Số điện thoại không hợp lệ (ví dụ: 0906636425).'
        isValid = false
    }

    if (form.email && form.email.trim().length > 0) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        if (!emailRegex.test(form.email.trim())) {
            clientErrors.email = 'Địa chỉ email không đúng định dạng.'
            isValid = false
        }
    }

    if (!form.content || form.content.trim().length < 5) {
        clientErrors.content = 'Vui lòng mô tả ý tưởng hoặc yêu cầu của bạn (tối thiểu 5 ký tự).'
        isValid = false
    }

    return isValid
}

const submitContact = () => {
    if (!validate()) {
        return
    }

    // Biến đổi payload vừa tương thích useForm vừa đáp ứng cấu trúc ContactController backend
    form.transform((data) => ({
        name: data.name,
        phone: data.phone,
        email: data.email,
        service: data.service,
        content: data.content,
        contact: {
            type: 'CONTACT_FORM',
            data: {
                'Họ và tên': data.name,
                'Số điện thoại': data.phone,
                'Email': data.email,
                'Dịch vụ quan tâm': data.service,
                'Service': {
                    title: data.service,
                },
                'Nội dung cần hỗ trợ': data.content,
            },
        },
    }))

    // Sử dụng route Ziggy nếu có hoặc fallback về '/contacts'
    const submitUrl = typeof window !== 'undefined' && typeof window.route === 'function'
        ? window.route('contact.store')
        : '/contacts'

    form.post(submitUrl, {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessModal.value = true
            form.reset()
            form.service = 'Thiết kế 3D'
        },
        onError: (errs) => {
            console.error('Lỗi khi gửi yêu cầu liên hệ:', errs)
        },
    })
}
</script>
