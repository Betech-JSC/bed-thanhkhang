<template>
    <Head>
        <title>{{ pageTitle }}</title>
        <meta name="description" :content="pageDescription" />
        <meta property="og:title" :content="pageTitle" />
        <meta property="og:description" :content="pageDescription" />
        <meta property="og:type" content="website" />
    </Head>

    <div class="bg-[#111322] text-white min-h-screen selection:bg-primary selection:text-white">
        <!-- ======================================================== -->
        <!-- SECTION 1: HERO BANNER & ARTWORK (Figma Node 2308:8195)  -->
        <!-- ======================================================== -->
        <section
            class="relative w-full overflow-hidden lg:overflow-visible pt-12 md:pt-16 pb-20 md:pb-28 lg:py-0 lg:h-[545px]"
            style="background: linear-gradient(0deg, #111322 20%, #2970FF 94%);"
        >
            <div class="app-container relative">
                <!-- Left Hero Content (Figma Node 2283:6728) -->
                <div class="relative z-10 flex flex-col gap-4 max-w-[740px] pt-4 sm:pt-6 lg:pt-[197px]">
                    <div class="text-[16px] leading-[22px] font-normal text-[#D1E0FF] uppercase">
                        {{ tt('KẾT NỐI VỚI HH MAKER') }}
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-[38px] lg:leading-[46px] lg:whitespace-nowrap font-bold text-[#FAFAFA] uppercase max-w-[740px]">
                        {{ tt('KỂ CHÚNG TÔI NGHE Ý TƯỞNG CỦA BẠN') }}
                    </h1>

                    <p class="text-[16px] leading-[26px] font-normal text-[#D1E0FF] max-w-[519px]">
                        {{ tt('Một bản phác thảo, một file 3D hay một ý tưởng chưa thành hình. Hãy bắt đầu cuộc trao đổi qua kênh thuận tiện nhất với bạn.') }}
                    </p>
                </div>

                <!-- Right Hero Logo 3D HH Maker (Figma Node 2308:8196: 469x889 at left 1021px, top -172px) -->
                <div
                    class="w-[200px] sm:w-[240px] max-h-[220px] mx-auto mt-6 lg:mt-0 lg:mx-0 lg:max-h-none lg:absolute lg:w-[469px] lg:h-[889px] lg:-top-[172px] lg:left-[909px] pointer-events-none select-none opacity-20"
                >
                    <img
                        src="/images/contact/hh_maker_monogram.png?v=2"
                        :alt="tt('HH Maker Logo 3D - From Ideas To Reality')"
                        class="w-full h-full object-contain pointer-events-none select-none"
                        loading="eager"
                    />
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SECTION 2: 3 THẺ KÍNH KÊNH LIÊN HỆ (Figma Node 2308:8241)-->
        <!-- ======================================================== -->
        <section v-scroll-reveal class="relative z-20 -mt-10 sm:-mt-14 lg:-mt-[165px] py-6 sm:py-8 lg:py-[40px]">
            <div class="app-container">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- 1. Card Zalo (Node 2308:8242) -->
                    <a
                        :href="displayZaloUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="lg:w-[368px] lg:h-[193px] p-7 rounded-[12px] bg-white/20 border border-[#5D6B98] backdrop-blur-[10px] flex flex-col justify-between gap-5 transition duration-200 hover:border-white/50 hover:bg-white/[0.28] hover:shadow-[0_0_20px_rgba(255,255,255,0.06)]"
                    >
                        <div class="w-7 h-7 flex items-center text-white">
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
                        <div class="flex flex-col gap-2">
                            <h3 class="text-[24px] leading-[29px] font-semibold text-[#FAFAFA]">
                                {{ tt('Tư vấn qua Zalo') }}
                            </h3>
                            <p class="text-[16px] leading-[26px] font-normal text-[#B9C0D4]">
                                {{ tt('Trao đổi ý tưởng, hình ảnh và yêu cầu sản phẩm') }}
                            </p>
                        </div>
                    </a>

                    <!-- 2. Card Hotline (Node 2308:8252) -->
                    <a
                        :href="'tel:' + displayPhone.replace(/\s+/g, '')"
                        class="lg:w-[368px] lg:h-[193px] p-7 rounded-[12px] bg-white/20 border border-[#5D6B98] backdrop-blur-[10px] flex flex-col justify-between gap-5 transition duration-200 hover:border-white/50 hover:bg-white/[0.28] hover:shadow-[0_0_20px_rgba(255,255,255,0.06)]"
                    >
                        <div class="w-7 h-7 flex items-center text-white">
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
                        <div class="flex flex-col gap-2">
                            <h3 class="text-[24px] leading-[29px] font-semibold text-[#FAFAFA]">
                                {{ displayPhone }}
                            </h3>
                            <p class="text-[16px] leading-[26px] font-normal text-[#B9C0D4]">
                                {{ tt('Gọi HH Maker để trao đổi trực tiếp về dự án của bạn') }}
                            </p>
                        </div>
                    </a>

                    <!-- 3. Card Email (Node 2308:8262) -->
                    <a
                        :href="'mailto:' + displayEmail"
                        class="lg:w-[368px] lg:h-[193px] p-7 rounded-[12px] bg-white/20 border border-[#5D6B98] backdrop-blur-[10px] flex flex-col justify-between gap-5 transition duration-200 hover:border-white/50 hover:bg-white/[0.28] hover:shadow-[0_0_20px_rgba(255,255,255,0.06)]"
                    >
                        <div class="w-7 h-7 flex items-center text-white">
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
                        <div class="flex flex-col gap-2">
                            <h3 class="text-[24px] leading-[29px] font-semibold text-[#FAFAFA]">
                                {{ displayEmail }}
                            </h3>
                            <p class="text-[16px] leading-[26px] font-normal text-[#B9C0D4]">
                                {{ tt('Gửi mô tả chi tiết và tài liệu tham khảo của bạn') }}
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SECTION 3: GỬI YÊU CẦU TƯ VẤN (Figma Node 2283:6767)     -->
        <!-- ======================================================== -->
        <section v-scroll-reveal class="py-12 sm:py-16 lg:pt-[112px] lg:pb-[64px] bg-[#111322]">
            <div class="app-container">
                <div class="flex flex-col lg:flex-row items-start gap-10 lg:gap-16">
                    <!-- Cột Trái: Lời Mời & Artwork Điêu khắc xanh (Node 2283:6768) - Width 380px -->
                    <div class="w-full lg:w-[380px] lg:h-[598px] shrink-0 flex flex-col gap-6">
                        <div class="text-[16px] leading-[22px] font-normal text-[#84ADFF] uppercase">
                            {{ tt('TỪ Ý TƯỞNG ĐẾN HIỆN THỰC') }}
                        </div>

                        <div class="flex flex-col gap-2">
                            <h2 class="text-3xl sm:text-[36px] sm:leading-[42px] font-bold text-[#FAFAFA]">
                                {{ tt('CÙNG BẮT ĐẦU MỘT SẢN PHẨM MỚI.') }}
                            </h2>
                            <p class="text-[16px] leading-[26px] font-normal text-[#B9C0D4]">
                                {{ tt('Điền thông tin để HH Maker hiểu thêm về nhu cầu của bạn và có cơ sở trao đổi phương án phù hợp.') }}
                            </p>
                        </div>

                        <!-- Khung Artwork Hình ảnh điêu khắc xanh (Node 2283:6772 & 2283:6773: 380px x 384px) -->
                        <div class="w-full lg:w-[380px] h-[300px] sm:h-[360px] lg:h-[384px] rounded-[12px] overflow-hidden">
                            <img
                                src="/images/contact/dieu_khac_xanh.png"
                                :alt="tt('HH Maker tác phẩm điêu khắc 3D xanh')"
                                class="w-full h-full object-cover select-none"
                                loading="lazy"
                            />
                        </div>
                    </div>

                    <!-- Cột Phải: Biểu mẫu Form (Node 2283:6775) - Width 708px -->
                    <div class="w-full lg:w-[708px] lg:min-h-[598px] shrink-0 flex flex-col gap-6">
                        <h3 class="text-2xl sm:text-[28px] sm:leading-[33px] font-bold text-[#FAFAFA]">
                            {{ tt('Thông tin yêu cầu') }}
                        </h3>

                        <form @submit.prevent="submitContact" novalidate class="flex flex-col gap-6">
                            <!-- Cụm 1 (Node 2308:8386): Họ tên, Số điện thoại & Email -->
                            <div class="flex flex-col gap-3">
                                <!-- Hàng 1: Họ tên & Số điện thoại (Node 2283:6777) -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <!-- Họ và tên (Node 2283:6778) -->
                                    <div>
                                        <label class="block text-[14px] leading-[20px] font-normal text-[#FAFAFA] mb-[10px]">
                                            {{ tt('Họ và tên *') }}
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.name"
                                            :placeholder="tt('Nhập họ và tên')"
                                            :class="[
                                                'w-full h-[52px] px-4 rounded-[8px] bg-white/20 border text-[14px] leading-[20px] text-white placeholder-[#B9C0D4] transition-colors duration-200 focus:outline-none',
                                                clientErrors.name || form.errors.name
                                                    ? 'border-red-500 focus:border-red-500'
                                                    : 'border-[#5D6B98] hover:border-white/40 focus:ring-2 focus:ring-white/40 focus:border-white'
                                            ]"
                                        />
                                        <p v-if="clientErrors.name || form.errors.name" class="text-xs text-red-400 mt-1.5">
                                            {{ clientErrors.name || form.errors.name }}
                                        </p>
                                    </div>

                                    <!-- Số điện thoại (Node 2283:6782) -->
                                    <div>
                                        <label class="block text-[14px] leading-[20px] font-normal text-[#FAFAFA] mb-[10px]">
                                            {{ tt('Số điện thoại *') }}
                                        </label>
                                        <input
                                            type="tel"
                                            v-model="form.phone"
                                            :placeholder="tt('Nhập số điện thoại')"
                                            :class="[
                                                'w-full h-[52px] px-4 rounded-[8px] bg-white/20 border text-[14px] leading-[20px] text-white placeholder-[#B9C0D4] transition-colors duration-200 focus:outline-none',
                                                clientErrors.phone || form.errors.phone
                                                    ? 'border-red-500 focus:border-red-500'
                                                    : 'border-[#5D6B98] hover:border-white/40 focus:ring-2 focus:ring-white/40 focus:border-white'
                                            ]"
                                        />
                                        <p v-if="clientErrors.phone || form.errors.phone" class="text-xs text-red-400 mt-1.5">
                                            {{ clientErrors.phone || form.errors.phone }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Hàng 2: Email (Node 2283:6786) -->
                                <div>
                                    <label class="block text-[14px] leading-[20px] font-normal text-[#FAFAFA] mb-[10px]">
                                        {{ tt('Email') }}
                                    </label>
                                    <input
                                        type="email"
                                        v-model="form.email"
                                        :placeholder="tt('Nhập địa chỉ email')"
                                        :class="[
                                            'w-full h-[52px] px-4 rounded-[8px] bg-white/20 border text-[14px] leading-[20px] text-white placeholder-[#B9C0D4] transition-colors duration-200 focus:outline-none',
                                            clientErrors.email || form.errors.email
                                                ? 'border-red-500 focus:border-red-500'
                                                : 'border-[#5D6B98] hover:border-white/40 focus:ring-2 focus:ring-white/40 focus:border-white'
                                        ]"
                                    />
                                    <p v-if="clientErrors.email || form.errors.email" class="text-xs text-red-400 mt-1.5">
                                        {{ clientErrors.email || form.errors.email }}
                                    </p>
                                </div>
                            </div>

                            <!-- Cụm 2: Dịch vụ quan tâm (Node 2283:6790 - Pill buttons) -->
                            <div>
                                <label class="block text-[14px] leading-[20px] font-normal text-[#FAFAFA] mb-3">
                                    {{ tt('Bạn cần hỗ trợ điều gì?') }}
                                </label>
                                <div class="flex flex-wrap gap-[10px]">
                                    <button
                                        type="button"
                                        v-for="service in availableServices"
                                        :key="service"
                                        @click="form.service = service"
                                        :class="[
                                            'h-[39px] px-[14px] py-[12px] rounded-[8px] text-[12px] leading-[15px] font-normal inline-flex items-center justify-center border transition-colors duration-200 cursor-pointer',
                                            form.service === service
                                                ? 'bg-[#243971] border-[#528BFF] text-[#FAFAFA]'
                                                : 'bg-[#1A1C32] border-[#2C3049] text-[#FAFAFA] hover:border-[#5D6B98]'
                                        ]"
                                    >
                                        {{ tt(service) }}
                                    </button>
                                </div>
                            </div>

                            <!-- Cụm 3: Mô tả ý tưởng (Node 2308:8387) -->
                            <div>
                                <label class="block text-[14px] leading-[20px] font-normal text-[#FAFAFA] mb-[10px]">
                                    {{ tt('Mô tả ý tưởng *') }}
                                </label>
                                <textarea
                                    v-model="form.content"
                                    rows="4"
                                    :placeholder="tt('Chia sẻ ý tưởng, hình ảnh hoặc bản phác thảo của bạn...')"
                                    :class="[
                                        'block w-full h-[136px] p-4 rounded-[8px] bg-white/20 border text-[14px] leading-[20px] text-white placeholder-[#B9C0D4] resize-none transition-colors duration-200 focus:outline-none',
                                        clientErrors.content || form.errors.content
                                            ? 'border-red-500 focus:border-red-500'
                                            : 'border-[#5D6B98] hover:border-white/40 focus:ring-2 focus:ring-white/40 focus:border-white'
                                    ]"
                                ></textarea>
                                <p v-if="clientErrors.content || form.errors.content" class="text-xs text-red-400 mt-1.5">
                                    {{ clientErrors.content || form.errors.content }}
                                </p>
                            </div>

                            <!-- Thông báo lỗi từ Server (nếu có) -->
                            <div
                                v-if="form.errors.error"
                                class="p-3.5 rounded-[8px] bg-red-500/10 border border-red-500/30 text-red-400 text-sm flex items-center gap-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ form.errors.error }}</span>
                            </div>

                            <!-- Cụm 4: Nút Submit (Node 2308:8396) -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full h-[56px] rounded-[8px] text-[16px] leading-[24px] font-semibold text-[#FAFAFA] transition-transform duration-200 flex items-center justify-center gap-2 cursor-pointer shadow-lg hover:brightness-110 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed"
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
                                <span>{{ form.processing ? tt('Đang gửi yêu cầu...') : tt('Gửi yêu cầu tư vấn') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- SECTION 4: FAQ ACCORDION (Figma Node 2330:8413)          -->
        <!-- ======================================================== -->
        <section v-if="faqList.length > 0" v-scroll-reveal class="py-14 sm:py-20 lg:pt-[80px] lg:pb-[120px] bg-[#111322]">
            <div class="app-container">
                <div class="flex flex-col gap-4 mb-8">
                    <div class="text-[14px] leading-[20px] font-normal text-[#84ADFF] uppercase">
                        {{ tt('CÂU HỎI THƯỜNG GẶP') }}
                    </div>
                    <h2 class="text-2xl sm:text-[32px] sm:leading-[37px] font-bold text-[#FAFAFA]">
                        {{ tt('Giải đáp trước khi liên hệ') }}
                    </h2>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(faq, index) in faqList"
                        :key="index"
                        :class="[
                            'rounded-[12px] bg-white/20 border border-[#5D6B98] backdrop-blur-[10px] hover:border-white/50 hover:bg-white/[0.24] transition-colors duration-200 overflow-hidden',
                            activeFaq === index ? '!border-white/60 !bg-white/[0.25]' : ''
                        ]"
                    >
                        <button
                            type="button"
                            @click="toggleFaq(index)"
                            class="w-full px-5 py-5 sm:px-6 sm:py-[23px] flex items-center justify-between gap-4 text-left cursor-pointer select-none focus:outline-none"
                            :aria-expanded="activeFaq === index"
                        >
                            <span class="text-base sm:text-[18px] leading-[22px] font-medium text-[#FAFAFA]">
                                {{ tt(faq.question) }}
                            </span>
                            <span
                                class="w-6 h-6 shrink-0 flex items-center justify-center text-[#FCFCFC] transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                                :class="activeFaq === index ? 'rotate-180' : 'rotate-0'"
                            >
                                <svg
                                    class="w-6 h-6"
                                    viewBox="0 0 48 48"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M24 10V38"
                                        stroke="#FCFCFC"
                                        stroke-width="4"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="origin-center transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                                        :class="activeFaq === index ? 'rotate-90 scale-y-0' : 'rotate-0 scale-y-100'"
                                    />
                                    <path
                                        d="M10 24H38"
                                        stroke="#FCFCFC"
                                        stroke-width="4"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </span>
                        </button>
                        <div
                            class="faq-accordion-grid"
                            :class="{ 'is-expanded': activeFaq === index }"
                            :aria-hidden="activeFaq !== index"
                        >
                            <div class="overflow-hidden min-h-0">
                                <div
                                    class="px-5 pb-5 sm:px-6 sm:pb-[23px] -mt-1 sm:-mt-[7px] text-sm sm:text-[16px] leading-[26px] font-normal text-[#FCFCFC]"
                                >
                                    {{ tt(faq.answer) }}
                                </div>
                            </div>
                        </div>
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
                        {{ tt('Gửi yêu cầu thành công!') }}
                    </h3>
                    <p class="text-[#B9C0D4] text-sm sm:text-base leading-relaxed">
                        {{ tt('Hệ thống đã nhận được thông tin yêu cầu của bạn. Đội ngũ kỹ thuật HH Maker sẽ liên hệ và tư vấn phương án phù hợp trong thời gian sớm nhất.') }}
                    </p>
                </div>

                <button
                    type="button"
                    @click="showSuccessModal = false"
                    class="w-full mt-4 h-[44px] rounded-[8px] bg-primary text-white font-medium text-sm hover:bg-primary-600 transition-colors cursor-pointer"
                >
                    {{ tt('Đóng thông báo') }}
                </button>
            </div>
        </BaseModal>
    </div>
</template>

<script setup>
import { ref, reactive, computed, getCurrentInstance } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { Head, useForm } from '@inertiajs/inertia-vue3'
import BaseModal from '@/Components/Base/BaseModal.vue'

const props = defineProps({
    agencies: {
        type: Array,
        default: () => [],
    },
    faqs: {
        type: Array,
        default: () => [],
    },
    seo: {
        type: Object,
        default: () => null,
    },
})

const instance = getCurrentInstance()
const i18n = {
    tt: (text) => (typeof instance?.proxy?.tt === 'function' ? instance.proxy.tt(text) : text),
}
const key = { tt: (s) => s }
const page = usePage()
const isEn = computed(() => (page.props.value?.locale?.current || 'vi') === 'en')

const pageTitle = computed(() => {
    return (
        props.seo?.title ||
        (isEn.value
            ? 'Contact & Consultation - HH Maker | Turning Creativity Beyond the Screen'
            : i18n.tt('Liên hệ tư vấn - HH Maker | Đưa sáng tạo vượt ra khỏi màn hình'))
    )
})

const pageDescription = computed(() => {
    return (
        props.seo?.description ||
        (isEn.value
            ? 'Get in touch with HH Maker to discuss 3D design concepts, industrial 3D printing services, or bespoke custom fabrication.'
            : i18n.tt('Liên hệ ngay với HH Maker để trao đổi ý tưởng thiết kế 3D, gia công in 3D công nghiệp hoặc chế tác sản phẩm custom theo yêu cầu.'))
    )
})

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
const availableServices = [key.tt('Thiết kế 3D'), key.tt('Gia công 3D'), key.tt('Làm đồ custom')]

// FAQ Accordion State & Data
const activeFaq = ref(null)

const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index
}

const faqList = computed(() =>
    Array.isArray(props.faqs) && props.faqs.length
        ? props.faqs.map((item) => ({
              question: item.question || item.title || '',
              answer: item.answer || item.content || item.description || '',
          }))
        : []
)

// Form state using Inertia useForm
const form = useForm({
    name: '',
    phone: '',
    email: '',
    service: key.tt('Thiết kế 3D'),
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
        clientErrors.name = isEn.value
            ? 'Please enter your full name (minimum 2 characters).'
            : i18n.tt('Vui lòng nhập họ và tên (tối thiểu 2 ký tự).')
        isValid = false
    }

    const phoneTrimmed = (form.phone || '').trim()
    const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$/
    if (!phoneTrimmed) {
        clientErrors.phone = isEn.value
            ? 'Please enter your phone number.'
            : i18n.tt('Vui lòng nhập số điện thoại.')
        isValid = false
    } else if (!phoneRegex.test(phoneTrimmed) && phoneTrimmed.length < 10) {
        clientErrors.phone = isEn.value
            ? 'Invalid phone number (e.g. 0906636425).'
            : i18n.tt('Số điện thoại không hợp lệ (ví dụ: 0906636425).')
        isValid = false
    }

    if (form.email && form.email.trim().length > 0) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        if (!emailRegex.test(form.email.trim())) {
            clientErrors.email = isEn.value
                ? 'Invalid email format.'
                : i18n.tt('Địa chỉ email không đúng định dạng.')
            isValid = false
        }
    }

    if (!form.content || form.content.trim().length < 5) {
        clientErrors.content = isEn.value
            ? 'Please describe your idea or requirements (minimum 5 characters).'
            : i18n.tt('Vui lòng mô tả ý tưởng hoặc yêu cầu của bạn (tối thiểu 5 ký tự).')
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
                [key.tt('Họ và tên')]: data.name,
                [key.tt('Số điện thoại')]: data.phone,
                Email: data.email,
                [key.tt('Dịch vụ quan tâm')]: data.service,
                Service: {
                    title: data.service,
                },
                [key.tt('Nội dung cần hỗ trợ')]: data.content,
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
            form.service = key.tt('Thiết kế 3D')
        },
        onError: (errs) => {
            console.error('Lỗi khi gửi yêu cầu liên hệ:', errs)
        },
    })
}
</script>

<style scoped>
.faq-accordion-grid {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 300ms cubic-bezier(0.22, 1, 0.36, 1);
}

.faq-accordion-grid.is-expanded {
    grid-template-rows: 1fr;
}
</style>
