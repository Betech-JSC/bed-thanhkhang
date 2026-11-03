<template>
    <Head>
        <title>Giới thiệu - HH Maker | Đưa sáng tạo vượt ra khỏi màn hình</title>
        <meta
            name="description"
            content="HH Maker kết nối nghệ thuật 3D với công nghệ chế tác hiện đại, để một ý tưởng sơ khai có thể trở thành sản phẩm vật lý bạn cầm trên tay."
        />
    </Head>

    <div class="bg-dark-bg text-white overflow-x-hidden">
        <!-- 1. HERO BANNER (FIGMA 2283:7100) -->
        <section class="relative pt-6 md:pt-10 pb-12 md:pb-16">
            <div class="container mx-auto px-4 md:px-8 max-w-7xl">
                <!-- Hero Outer Container (1279px x 616px Mask Group) -->
                <div class="relative w-full rounded-2xl md:rounded-3xl overflow-hidden border border-dark-border min-h-[500px] md:min-h-[616px] flex flex-col justify-end p-6 sm:p-10 md:p-16 shadow-2xl">
                    <!-- Background Image from Figma with Dark Tech Overlays -->
                    <div class="absolute inset-0 z-0">
                        <img
                            src="/assets/images/about/hero-about.png"
                            alt="Xưởng chế tác 3D HH Maker"
                            class="w-full h-full object-cover object-center"
                        />
                        <!-- Dark gradient overlay for text readability -->
                        <div class="absolute inset-0 bg-gradient-to-t from-dark-bg via-dark-bg/80 to-dark-bg/40"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-dark-bg/90 via-dark-bg/60 to-transparent"></div>
                    </div>

                    <!-- Hero Content Grid -->
                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
                        <!-- Left: Main Headline & Intro (Figma 2283:7579) -->
                        <div class="lg:col-span-8 space-y-4 md:space-y-6 max-w-2xl">
                            <p class="text-[#D1E0FF] text-base font-medium leading-[22px]">
                                Giới thiệu về HH Maker
                            </p>

                            <h1 class="text-3xl sm:text-4xl lg:text-display-hero font-bold font-display uppercase tracking-tight text-[#FAFAFA] leading-[1.18]">
                                ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH
                            </h1>

                            <p class="text-[#D1E0FF] text-base md:text-lg leading-relaxed font-normal max-w-xl">
                                Chúng tôi kết nối nghệ thuật 3D với công nghệ chế tác hiện đại, để một ý tưởng sơ khai có thể trở thành sản phẩm bạn cầm trên tay.
                            </p>
                        </div>

                        <!-- Right: Interactive Circular Rotating Stamp Badge (Figma Frame 237 - 2283:7597) -->
                        <div class="lg:col-span-4 flex lg:justify-end">
                            <div class="relative w-28 h-28 md:w-32 md:h-32 flex items-center justify-center shrink-0">
                                <svg class="w-full h-full animate-[spin_15s_linear_infinite]" viewBox="0 0 120 120">
                                    <path
                                        id="circlePath"
                                        d="M 60, 60 m -45, 0 a 45,45 0 1,1 90,0 a 45,45 0 1,1 -90,0"
                                        fill="transparent"
                                    />
                                    <text class="text-[9.5px] font-mono tracking-[2px] uppercase fill-white/90 font-medium">
                                        <textPath href="#circlePath" startOffset="0%">
                                            FROM IDEAS TO REALITY - FROM IDEAS TO REALITY -
                                        </textPath>
                                    </text>
                                </svg>
                                <div class="absolute w-12 h-12 rounded-full bg-dark-bg/85 border border-primary/40 flex items-center justify-center text-primary backdrop-blur-md shadow-glow-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5 text-white"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <line x1="7" y1="17" x2="17" y2="7"></line>
                                        <polyline points="7 7 17 7 17 17"></polyline>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. LÝ DO KHỞI ĐẦU (FIGMA 2283:7109) -->
        <section class="py-12 md:py-16 bg-dark-bg border-t border-dark-border/40">
            <div class="container mx-auto px-4 md:px-8 max-w-7xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-start">
                    <!-- Left Column: Chuyên mục & Tiêu đề (Figma 2283:7110) -->
                    <div class="lg:col-span-5 space-y-3">
                        <p class="text-[#84ADFF] font-medium text-base leading-[19px]">
                            Vì sao HH Maker ra đời?
                        </p>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold font-display uppercase text-[#FAFAFA] leading-tight tracking-tight">
                            TỪ ĐAM MÊ ĐẾN MỘT SỨ MỆNH
                        </h2>
                    </div>

                    <!-- Right Column: Câu chuyện sứ mệnh (Figma 2283:7113) -->
                    <div class="lg:col-span-7 space-y-6 text-[#B9C0D4] text-base md:text-lg leading-relaxed">
                        <p>
                            HH Maker được thành lập từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại của Lâm Hồng Hải. Điểm khởi đầu là một mong muốn giản dị: rút ngắn khoảng cách giữa ý tưởng và sản phẩm vật lý.
                        </p>
                        <p>
                            Một bản phác thảo, một hình ảnh hay một nhu cầu rất riêng đều có thể mở ra một cuộc trao đổi. Tại HH Maker, thiết kế và chế tác không tách rời nhau: hình dáng cần đẹp, đồng thời phải phù hợp để tạo thành sản phẩm thực tế.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. NHÀ SÁNG LẬP (FIGMA 2283:7116) -->
        <section class="py-14 md:py-20 bg-dark-bg-alt/50 border-t border-dark-border/40">
            <div class="container mx-auto px-4 md:px-8 max-w-7xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                    <!-- Left: Khung chân dung Founder Lâm Hồng Hải vát góc công nghệ (Figma 2283:7602) -->
                    <div class="lg:col-span-6 flex justify-center">
                        <div class="relative w-full max-w-[500px] lg:max-w-[560px] select-none group">
                            <!-- Khung hình công nghệ chuẩn Figma 2283:7602 với góc vát (chamfered) trên phải & dưới trái, đầu nhô vượt khung nền -->
                            <img
                                src="/assets/images/about/founder-composite.png"
                                alt="Lâm Hồng Hải - Founder HH Maker"
                                class="w-full h-auto object-contain drop-shadow-2xl transition-transform duration-500 group-hover:scale-[1.02]"
                                loading="lazy"
                            />
                        </div>
                    </div>

                    <!-- Right: Chia sẻ từ Nhà sáng lập (Figma 2283:7122) -->
                    <div class="lg:col-span-6 space-y-6">
                        <div class="space-y-2">
                            <p class="text-[#84ADFF] font-medium text-base leading-[26px]">
                                Chia sẻ từ Nhà sáng lập HH Maker
                            </p>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold font-display text-[#FAFAFA] leading-tight tracking-tight">
                                Mỗi ý tưởng đều xứng đáng có một hình hài
                            </h2>
                        </div>

                        <div class="space-y-4 text-[#B9C0D4] text-base leading-relaxed">
                            <p>
                                Xuất phát từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại, tôi thành lập HH Maker với sứ mệnh rút ngắn khoảng cách giữa ý tưởng sơ khai và sản phẩm vật lý.
                            </p>
                            <p>
                                Với mỗi dự án, tôi cùng đội ngũ luôn tỉ mỉ trong từng đường nét thiết kế, lựa chọn vật liệu và kiểm soát quy trình in để mang lại những sản phẩm sắc nét, chuẩn xác và mang đậm dấu ấn cá nhân nhất.
                            </p>
                            <p>
                                Tôi mong HH Maker sẽ là nơi bạn có thể thoải mái chia sẻ cả những ý tưởng chưa trọn vẹn. Qua từng cuộc trao đổi và mỗi lần thử nghiệm, chúng tôi cùng bạn khám phá những cách thể hiện phù hợp, để mỗi sản phẩm khi hoàn thành đều có ý nghĩa riêng. Với tôi, niềm vui lớn nhất là nhìn thấy sự hào hứng của bạn khi lần đầu cầm trên tay điều mình từng hình dung.
                            </p>
                        </div>

                        <!-- Chữ ký triết lý (Figma 2283:7128) -->
                        <div class="pt-4 border-t border-dark-border/60">
                            <p class="text-[#FAFAFA] font-semibold text-base md:text-lg italic font-display flex items-center gap-2">
                                <span class="text-primary text-xl">“</span>
                                Trân trọng từng ý tưởng - Tận tâm với từng thành phẩm.
                                <span class="text-primary text-xl">”</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. TRIẾT LÝ CHẾ TÁC (FIGMA 2283:7129) -->
        <section class="py-14 md:py-20 bg-dark-bg border-t border-dark-border/40">
            <div class="container mx-auto px-4 md:px-8 max-w-7xl">
                <!-- Section Header (Figma 2283:7647) -->
                <div class="space-y-3 mb-10 md:mb-12">
                    <p class="text-[#84ADFF] font-medium text-base leading-[19px]">
                        Cách HH Maker thực hiện trong từng dự án
                    </p>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold font-display uppercase text-[#FAFAFA] tracking-tight">
                        CÔNG NGHỆ TẠO HÌNH - SỰ TỈ MỈ TẠO DẤU ẤN
                    </h2>
                </div>

                <!-- 3 Value Cards Grid (Figma 2283:7132) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                    <!-- Card 1: Thấu hiểu từng ý tưởng -->
                    <div class="border border-[#5D6B98] rounded-xl p-7 flex flex-col justify-between min-h-[290px] bg-dark-card/40 hover:border-primary/80 transition-colors duration-300">
                        <div class="w-12 h-12 flex items-center justify-center shrink-0 mb-6">
                            <img
                                src="/assets/images/about/lightbulb.svg"
                                alt="Thấu hiểu từng ý tưởng"
                                class="w-12 h-12 object-contain"
                            />
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-xl md:text-2xl font-bold font-display text-[#FAFAFA]">
                                Thấu hiểu từng ý tưởng
                            </h3>
                            <p class="text-[#B9C0D4] text-base leading-relaxed">
                                Chúng tôi lắng nghe câu chuyện, nhu cầu sử dụng và mong muốn của bạn để tìm ra hướng thiết kế phù hợp, trước khi bắt đầu những nét phác thảo đầu tiên.
                            </p>
                        </div>
                    </div>

                    <!-- Card 2: Chăm chút từng chi tiết -->
                    <div class="border border-[#5D6B98] rounded-xl p-7 flex flex-col justify-between min-h-[290px] bg-dark-card/40 hover:border-primary/80 transition-colors duration-300">
                        <div class="w-12 h-12 flex items-center justify-center shrink-0 mb-6">
                            <img
                                src="/assets/images/about/scan-line.svg"
                                alt="Chăm chút từng chi tiết"
                                class="w-12 h-12 object-contain"
                            />
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-xl md:text-2xl font-bold font-display text-[#FAFAFA]">
                                Chăm chút từng chi tiết
                            </h3>
                            <p class="text-[#B9C0D4] text-base leading-relaxed">
                                Từ hình khối, tỉ lệ, vật liệu đến quá trình in và hoàn thiện, sản phẩm đều được chăm chút để hài hòa về hình thức và phù hợp với cách sử dụng thực tế.
                            </p>
                        </div>
                    </div>

                    <!-- Card 3: Giữ trọn dấu ấn riêng -->
                    <div class="border border-[#5D6B98] rounded-xl p-7 flex flex-col justify-between min-h-[290px] bg-dark-card/40 hover:border-primary/80 transition-colors duration-300">
                        <div class="w-12 h-12 flex items-center justify-center shrink-0 mb-6">
                            <img
                                src="/assets/images/about/fingerprint.svg"
                                alt="Giữ trọn dấu ấn riêng"
                                class="w-12 h-12 object-contain"
                            />
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-xl md:text-2xl font-bold font-display text-[#FAFAFA]">
                                Giữ trọn dấu ấn riêng
                            </h3>
                            <p class="text-[#B9C0D4] text-base leading-relaxed">
                                Phong cách của bạn được thể hiện qua màu sắc, đường nét và những chi tiết cá nhân hóa. HH Maker kết nối những yếu tố ấy để truyền tải đúng tinh thần bạn muốn gửi gắm.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. KẾT NỐI DỰ ÁN CTA (FIGMA 2283:7633) -->
        <section class="py-12 md:py-16 bg-dark-bg border-t border-dark-border/40">
            <div class="container mx-auto px-4 md:px-8 max-w-7xl">
                <!-- Banner gradient #2970FF -> #00359E (Figma 2283:7634) -->
                <div
                    class="rounded-2xl p-8 sm:p-10 md:p-12 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 shadow-2xl relative overflow-hidden"
                    style="background: linear-gradient(90deg, #2970FF 0%, #00359E 89%);"
                >
                    <div class="space-y-3 max-w-3xl">
                        <h2 class="text-2xl sm:text-3xl lg:text-[32px] font-bold font-display text-[#FAFAFA] leading-tight lg:leading-[37px]">
                            Ý tưởng của bạn, bước tiếp theo của chúng ta.
                        </h2>
                        <p class="text-[#FAFAFA] text-base leading-relaxed lg:leading-[26px]">
                            Chia sẻ mong muốn của bạn. HH Maker sẽ cùng bạn tìm hướng thiết kế và gia công phù hợp.
                        </p>
                    </div>

                    <div class="shrink-0">
                        <Link
                            :href="route('contact')"
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-[#F5F8FF] hover:bg-white text-[#111322] font-medium text-base rounded-lg transition-all duration-200 shadow-sm hover:shadow-md hover:scale-[1.02]"
                        >
                            Nhận tư vấn ngay
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'

export default {
    name: 'AboutIndex',
    components: {
        Head,
        Link,
    },
}
</script>
