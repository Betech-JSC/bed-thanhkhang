<!--    
        Name: Contact.vue
        Author: Alex Vu
        Date: 25/11/26 
-->

<template>
    <Head>
        <title>{{ pageTitle }}</title>
        <meta name="description" :content="pageDescription" />
        <meta property="og:title" :content="pageTitle" />
        <meta property="og:description" :content="pageDescription" />
        <meta property="og:type" content="website" />
    </Head>

    <div class="bg-surface text-ink-body">
        <!-- ======================================================== -->
        <!-- HERO BANNER — Liên hệ (Figma 939:2604, 1440×252)          -->
        <!-- ======================================================== -->
        <section class="relative w-full overflow-hidden bg-dark min-h-[220px] lg:min-h-[252px] flex items-center">
            <img
                src="/assets/images/contact/lien-he-hero.webp"
                alt=""
                aria-hidden="true"
                width="1920"
                height="336"
                class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none select-none"
                loading="eager"
            />
            <div class="hero-scrim absolute inset-0 pointer-events-none" aria-hidden="true" />
            <div class="app-container-narrow relative z-10 py-10 lg:py-0">
                <div class="flex flex-col gap-5 max-w-[580px]">
                    <nav :aria-label="tt('Breadcrumb')" class="flex items-center gap-2 text-[14px]">
                        <Link :href="homeHref" class="text-gray-modern-300 hover:text-white transition-colors">
                            {{ tt('Trang chủ') }}
                        </Link>
                        <span class="text-gray-modern-300" aria-hidden="true">/</span>
                        <span class="font-medium text-surface" aria-current="page">{{ tt('Liên hệ') }}</span>
                    </nav>
                    <div class="flex flex-col gap-2">
                        <h1 class="text-h1-page-mobile lg:text-h1-page font-bold text-surface uppercase">
                            {{ tt('LIÊN HỆ VỚI CHÚNG TÔI') }}
                        </h1>
                        <p class="text-[14px] leading-[1.6] text-white max-w-[400px] drop-shadow-[0_1px_2px_rgba(0,0,0,0.4)]">
                            {{ tt('Đội ngũ tư vấn viên của Thành Khang Tecsat luôn sẵn sàng hỗ trợ báo giá, tư vấn kỹ thuật và cung ứng vật tư chính hãng trên toàn quốc') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- QUICK CONTACT CHANNELS (Figma 939:2615)                   -->
        <!-- ======================================================== -->
        <section class="pt-10 pb-6 lg:pt-16 lg:pb-10">
            <div class="app-container-narrow flex flex-col gap-6">
                <div class="flex items-center gap-[10px]">
                    <span class="block w-3 h-3 rounded-[2px] bg-primary shrink-0" aria-hidden="true" />
                    <h2 class="!text-body-lg !leading-[1.6] font-bold text-primary">{{ tt('Thông tin liên hệ') }}</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                    <component
                        :is="card.href ? 'a' : 'div'"
                        v-for="card in contactCards"
                        :key="card.key"
                        :href="card.href || undefined"
                        class="flex flex-row items-center sm:flex-col sm:items-start gap-4 bg-white border border-gray-modern-200 rounded-[12px] px-4 py-4 sm:px-5 sm:py-6 shadow-xs transition-colors"
                        :class="card.href ? 'hover:border-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary' : ''"
                    >
                        <span class="w-12 h-12 rounded-[10px] bg-gray-modern-100 inline-flex items-center justify-center shrink-0">
                            <img :src="card.icon" alt="" aria-hidden="true" width="24" height="24" class="w-6 h-6" />
                        </span>
                        <span class="flex flex-col gap-1 min-w-0">
                            <span class="text-body-sm font-medium text-ink-subtle">{{ card.label }}</span>
                            <span class="text-body-md font-bold text-dark break-words">{{ card.value }}</span>
                        </span>
                    </component>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- CONTACT FORM SECTION (Figma 939:2657)                     -->
        <!-- ======================================================== -->
        <section class="pt-6 pb-10 lg:pt-10 lg:pb-20">
            <div class="app-container-narrow grid grid-cols-1 lg:grid-cols-[448px_minmax(0,627px)] lg:justify-between gap-8 lg:gap-10">
                <!-- Left: Support & Commitments (939:2658) -->
                <div class="flex flex-col gap-7">
                    <div class="flex flex-col gap-3">
                        <h2 class="!text-h2-mobile lg:!text-pc-h5 font-bold text-dark">
                            {{ tt('Nhận tư vấn kỹ thuật & báo giá nhanh trong 2 giờ') }}
                        </h2>
                        <p class="text-body-md text-ink-body">
                            {{ tt('Cung cấp thông số kỹ thuật, mã linh kiện hoặc nhu cầu dự án của bạn. Đội ngũ kỹ sư Thành Khang sẽ kiểm tra tồn kho và đề xuất giải pháp tối ưu nhất') }}
                        </p>
                    </div>
                    <ul class="flex flex-col gap-5">
                        <li v-for="item in assurances" :key="item.key" class="flex items-start gap-4">
                            <span class="w-10 h-10 rounded-[8px] bg-gray-modern-100 inline-flex items-center justify-center shrink-0">
                                <svg
                                    v-if="item.kind === 'tool'"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    width="20"
                                    height="20"
                                    class="w-5 h-5 text-[#697586]"
                                    aria-hidden="true"
                                >
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                                </svg>
                                <img v-else :src="item.icon" alt="" aria-hidden="true" width="20" height="20" class="w-5 h-5" />
                            </span>
                            <span class="flex flex-col gap-1 min-w-0">
                                <span class="text-body-md font-bold text-dark">{{ item.title }}</span>
                                <span class="text-body-sm text-ink-body">{{ item.description }}</span>
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- Right: Form (944:28704) -->
                <form
                    novalidate
                    class="flex flex-col gap-6 lg:justify-between bg-gray-modern-200 rounded-[8px] p-5 sm:p-8 lg:p-10 lg:min-h-[508px]"
                    @submit.prevent="submitContact"
                >
                    <div class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 sm:gap-x-3">
                            <div v-for="field in formFields" :key="field.key" class="flex flex-col gap-1">
                                <label :for="field.id" class="text-body-sm font-medium text-field-label">
                                    {{ field.label }}<span v-if="field.required" class="text-field-required">*</span>
                                </label>
                                <input
                                    :id="field.id"
                                    v-model="form[field.key]"
                                    :type="field.type"
                                    :autocomplete="field.autocomplete"
                                    :placeholder="field.placeholder"
                                    :class="fieldClass(field.key)"
                                    :aria-invalid="field.required ? !!fieldError(field.key) : undefined"
                                    @input="clearFieldError(field.key)"
                                />
                                <p v-if="field.required && fieldError(field.key)" class="text-body-xs text-field-required">{{ fieldError(field.key) }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-[6px]">
                            <label for="contact-content" class="text-body-sm font-medium text-field-label">
                                {{ tt('Lời nhắn') }}
                            </label>
                            <textarea
                                id="contact-content"
                                v-model="form.content"
                                rows="5"
                                :placeholder="tt('Nhập nội dung bạn cần báo giá (sản phẩm, số lượng, thời gian cần hàng...)')"
                                :class="[fieldClass('content'), 'h-[150px] resize-none px-[14px] py-[10px]']"
                            ></textarea>
                        </div>
                        <p v-if="form.errors.error" class="text-body-sm text-field-required" role="alert">{{ form.errors.error }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-cta bg-primary hover:bg-primary-hover active:bg-primary-active text-surface text-body-md font-medium px-6 py-[10px] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span>{{ form.processing ? tt('Đang gửi yêu cầu...') : tt('Nhận tư vấn ngay') }}</span>
                    </button>
                </form>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- OFFICES & MAP (Figma 940:2702)                            -->
        <!-- ======================================================== -->
        <section v-if="offices.length" class="bg-white py-10 lg:py-20">
            <div class="app-container-narrow flex flex-col gap-6 lg:gap-8">
                <div class="flex items-center gap-2">
                    <span class="block w-2 h-2 rounded-[1px] bg-primary shrink-0" aria-hidden="true" />
                    <h2 class="!text-body-sm !leading-[1.6] font-bold text-primary">{{ tt('Địa chỉ trụ sở & văn phòng giao dịch') }}</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_440px] gap-5 lg:gap-8 items-start">
                    <!-- Illustrated map (940:2761) -->
                    <div class="flex flex-col gap-3">
                        <!-- Mobile: box cropped to 680×516 (blocks bottom 499 + top inset 17) so bottom gap = top gap; md+: Figma 680×607 -->
                        <div class="relative w-full aspect-[680/516] md:aspect-[680/607] rounded-[16px] border border-gray-modern-200 bg-[#EEF3F8] overflow-hidden">
                            <div
                                class="absolute inset-x-0 top-0 aspect-[680/607] md:inset-0 md:aspect-auto origin-center transition-transform duration-300"
                                :style="{ transform: `scale(${mapZoom})` }"
                            >
                                <span
                                    v-for="(block, i) in mapBlocks"
                                    :key="'b' + i"
                                    class="absolute rounded-[8px]"
                                    :class="block[4] ? 'bg-[#D5EDDF]' : 'bg-[#E4EBF3]'"
                                    :style="pos(block[0], block[1], block[2], block[3])"
                                />
                                <img src="/assets/figma/lien-he/map-lake-1.svg" alt="" aria-hidden="true" width="62" height="82" class="absolute" :style="pos(591, 191, 62, 82)" />
                                <img src="/assets/figma/lien-he/map-lake-2.svg" alt="" aria-hidden="true" width="74" height="52" class="absolute" :style="pos(433, 41, 74, 52)" />
                                <img src="/assets/figma/lien-he/map-roads.svg" alt="" aria-hidden="true" width="680" height="520" class="absolute" :style="pos(-1, -1, 680, 520)" />
                                <span
                                    v-for="label in mapStreetLabels"
                                    :key="label.text"
                                    class="hidden sm:block absolute bg-white/90 rounded-[4px] px-2 py-[2px] text-body-xs font-medium text-ink-body whitespace-nowrap"
                                    :style="pos(label.left, label.top)"
                                >
                                    {{ label.text }}
                                </span>
                                <button
                                    v-for="(pin, i) in mapPins"
                                    :key="'p' + i"
                                    type="button"
                                    class="absolute -translate-x-1/2 -translate-y-1/2 block focus:outline-none group"
                                    :style="pos(pin.left, pin.top)"
                                    :aria-label="pin.office.title"
                                    :aria-pressed="activeOfficeIndex === i"
                                    @click="activeOfficeIndex = i"
                                >
                                    <!-- Button is the dot itself (anchored by its centre); label floats above without shifting the dot -->
                                    <span
                                        class="hidden sm:block absolute bottom-full left-1/2 -translate-x-1/2 mb-[6px] rounded-[8px] px-3 py-[6px] text-body-xs font-bold whitespace-nowrap shadow-xs border group-focus-visible:ring-2 group-focus-visible:ring-primary"
                                        :class="activeOfficeIndex === i ? 'bg-dark border-primary text-surface' : 'bg-white border-gray-modern-300 text-dark'"
                                    >
                                        {{ pin.office.title }}
                                    </span>
                                    <img
                                        :src="activeOfficeIndex === i ? '/assets/figma/lien-he/pin-primary.svg' : '/assets/figma/lien-he/pin-secondary.svg'"
                                        alt=""
                                        aria-hidden="true"
                                        width="40"
                                        height="40"
                                        class="block w-7 h-7 sm:w-10 sm:h-10"
                                    />
                                </button>
                            </div>

                            <!-- Map view toggle (940:2810) -->
                            <div class="absolute left-[15px] top-[15px] flex gap-[2px] p-1 bg-white border border-gray-modern-200 rounded-[8px] shadow-xs">
                                <span class="px-3 py-[5px] rounded-[6px] bg-primary text-body-xs font-bold text-surface">{{ tt('Bản đồ') }}</span>
                                <a
                                    :href="satelliteHref"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="px-3 py-[5px] rounded-[6px] text-body-xs font-medium text-ink-body hover:bg-gray-modern-100 transition-colors"
                                >
                                    {{ tt('Vệ tinh') }}
                                </a>
                            </div>

                            <!-- Zoom controls (940:2815) -->
                            <div class="flex absolute right-[15px] bottom-[15px] flex-col bg-white border border-gray-modern-200 rounded-[8px] shadow-xs overflow-hidden">
                                <button type="button" class="w-11 h-11 sm:w-9 sm:h-9 inline-flex items-center justify-center text-body-md font-bold text-gray-modern-700 hover:bg-gray-modern-100 disabled:opacity-40" :disabled="mapZoom >= 1.6" :aria-label="tt('Phóng to')" @click="mapZoom = Math.min(1.6, +(mapZoom + 0.2).toFixed(1))">+</button>
                                <span class="block h-px w-11 sm:w-9 bg-gray-modern-200" aria-hidden="true" />
                                <button type="button" class="w-11 h-11 sm:w-9 sm:h-9 inline-flex items-center justify-center text-body-md font-bold text-gray-modern-700 hover:bg-gray-modern-100 disabled:opacity-40" :disabled="mapZoom <= 1" :aria-label="tt('Thu nhỏ')" @click="mapZoom = Math.max(1, +(mapZoom - 0.2).toFixed(1))">−</button>
                            </div>

                            <!-- Active location overlay (940:2821) — desktop -->
                            <div v-if="activeOffice" class="hidden md:flex absolute left-[15px] bottom-[15px] max-w-[calc(100%-90px)] items-center gap-4 bg-white border border-gray-modern-200 rounded-[12px] shadow-xs px-4 py-[14px]">
                                <div class="flex flex-col gap-[2px] min-w-0">
                                    <p class="text-body-sm font-bold text-dark truncate">{{ activeOffice.title }}</p>
                                    <p class="text-body-xs text-ink-subtle truncate">{{ activeOffice.location }}</p>
                                </div>
                                <a :href="directionsHref" target="_blank" rel="noopener noreferrer" class="shrink-0 inline-flex items-center justify-center rounded-cta bg-primary hover:bg-primary-hover text-surface text-body-sm font-medium px-5 py-2 transition-colors">
                                    {{ tt('Chỉ đường') }}
                                </a>
                            </div>
                        </div>

                        <!-- Active location overlay — mobile (stacked below the map) -->
                        <div v-if="activeOffice" class="md:hidden flex items-center justify-between gap-3 bg-white border border-gray-modern-200 rounded-[12px] shadow-xs px-4 py-3">
                            <div class="flex flex-col gap-[2px] min-w-0">
                                <p class="text-body-sm font-bold text-dark">{{ activeOffice.title }}</p>
                                <p class="text-body-xs text-ink-subtle">{{ activeOffice.location }}</p>
                            </div>
                            <a :href="directionsHref" target="_blank" rel="noopener noreferrer" class="shrink-0 inline-flex items-center justify-center min-h-[44px] rounded-cta bg-primary hover:bg-primary-hover text-surface text-body-sm font-medium px-4 transition-colors">
                                {{ tt('Chỉ đường') }}
                            </a>
                        </div>
                    </div>

                    <!-- Office cards (940:2710) -->
                    <ul class="flex flex-col gap-4 lg:gap-5">
                        <li v-for="(office, index) in offices" :key="index">
                            <button
                                type="button"
                                class="w-full text-left flex flex-col gap-[14px] p-5 lg:p-6 rounded-[12px] shadow-xs transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                :class="activeOfficeIndex === index ? 'bg-[#F9FCFF] border-[1.5px] border-primary' : 'bg-white border border-gray-modern-200 hover:border-gray-modern-300'"
                                :aria-pressed="activeOfficeIndex === index"
                                @click="activeOfficeIndex = index"
                            >
                                <span class="text-body-lg font-bold text-dark">{{ office.title }}</span>
                                <span class="flex flex-col gap-[10px] text-body-sm text-ink-body">
                                    <span v-if="office.location" class="flex items-start gap-2">
                                        <img src="/assets/svg/icons/footer-pin.svg" alt="" aria-hidden="true" width="18" height="18" class="w-[18px] h-[18px] mt-[2px] shrink-0" />
                                        <span>{{ office.location }}</span>
                                    </span>
                                    <span v-if="office.phone" class="flex items-start gap-2">
                                        <img src="/assets/svg/icons/footer-phone.svg" alt="" aria-hidden="true" width="18" height="18" class="w-[18px] h-[18px] mt-[2px] shrink-0" />
                                        <span>{{ tt('Hotline') }}: {{ office.phone }}</span>
                                    </span>
                                    <span v-if="office.hours" class="flex items-start gap-2">
                                        <img src="/assets/svg/clock.svg" alt="" aria-hidden="true" width="18" height="18" class="w-[18px] h-[18px] mt-[2px] shrink-0" />
                                        <span>{{ office.hours }}</span>
                                    </span>
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- FAQ (Figma 940:2829) — CMS faqs                          -->
        <!-- ======================================================== -->
        <section v-if="faqItems.length" class="py-10 lg:pt-[72px] lg:pb-20">
            <div class="app-container-narrow flex flex-col gap-6 lg:gap-8">
                <div class="flex flex-col gap-[10px]">
                    <div class="flex items-center gap-2">
                        <span class="block w-2 h-2 rounded-[1px] bg-primary shrink-0" aria-hidden="true" />
                        <p class="text-body-sm font-bold text-primary">{{ tt('Câu hỏi thường gặp') }}</p>
                    </div>
                    <h2 class="!text-h2-mobile lg:!text-pc-h5 font-bold text-dark">{{ tt('Giải đáp nhanh trước khi liên hệ') }}</h2>
                </div>

                <div class="flex flex-col gap-[14px]">
                    <div
                        v-for="(faq, index) in faqItems"
                        :key="index"
                        class="bg-white rounded-[10px] shadow-xs transition-colors"
                        :class="activeFaq === index ? 'border-[1.5px] border-primary' : 'border border-gray-modern-200 hover:border-gray-modern-300'"
                    >
                        <button
                            :id="'faq-q-' + index"
                            type="button"
                            class="w-full flex items-center justify-between gap-4 px-5 lg:px-6 py-5 text-left cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-[10px]"
                            :aria-expanded="activeFaq === index"
                            :aria-controls="'faq-a-' + index"
                            @click="toggleFaq(index)"
                        >
                            <span class="text-body-md font-bold" :class="activeFaq === index ? 'text-primary' : 'text-dark'">
                                {{ faq.question }}
                            </span>
                            <span class="w-8 h-8 rounded-[6px] inline-flex items-center justify-center shrink-0">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    width="18"
                                    height="18"
                                    class="w-[18px] h-[18px]"
                                    :class="activeFaq === index ? 'text-primary' : 'text-ink-body'"
                                    aria-hidden="true"
                                >
                                    <path d="M5 12h14" />
                                    <path v-if="activeFaq !== index" d="M12 5v14" />
                                </svg>
                            </span>
                        </button>
                        <div
                            :id="'faq-a-' + index"
                            role="region"
                            :aria-labelledby="'faq-q-' + index"
                            class="faq-accordion-grid"
                            :class="{ 'is-expanded': activeFaq === index }"
                        >
                            <div class="overflow-hidden min-h-0">
                                <p class="px-5 lg:px-6 pb-5 text-body-sm text-ink-body max-w-[800px] whitespace-pre-line">
                                    {{ faq.answer }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- DIRECT SUPPORT CTA (Figma 940:2862)                       -->
        <!-- ======================================================== -->
        <section class="bg-surface pb-10 lg:pb-20">
            <div
                class="relative w-full overflow-hidden bg-dark py-12 md:py-16 lg:min-h-[362px] flex items-center justify-center"
            >
                <div class="cta-texture cta-texture--left pointer-events-none select-none" aria-hidden="true">
                    <div class="cta-texture__inner cta-texture__inner--left">
                        <img src="/assets/images/posts/cta-chain.webp" alt="" width="1584" height="672" loading="lazy" />
                    </div>
                </div>
                <div class="cta-texture cta-texture--right pointer-events-none select-none" aria-hidden="true">
                    <div class="cta-texture__inner cta-texture__inner--right">
                        <img src="/assets/images/posts/cta-chain.webp" alt="" width="1584" height="672" loading="lazy" />
                    </div>
                </div>

                <div
                    class="relative z-10 max-w-[600px] w-full mx-auto px-4 text-center flex flex-col items-center gap-6 lg:gap-8"
                >
                    <div class="flex flex-col items-center">
                        <h2
                            class="text-[22px] lg:text-[28px] font-bold leading-[1.6] text-surface uppercase lg:whitespace-nowrap"
                        >
                            {{ tt('CẦN HỖ TRỢ KỸ THUẬT KHẨN CẤP?') }}
                        </h2>
                        <p class="text-[14px] lg:text-[16px] font-medium leading-[1.6] text-gray-modern-400">
                            {{ tt('Hỗ trợ kỹ thuật 24/7, vật tư công nghiệp chính hãng tại Thành Khang') }}
                        </p>
                    </div>
                    <a
                        :href="'tel:' + cleanPhone"
                        class="bg-surface hover:bg-white text-primary text-[16px] lg:text-[18px] font-medium h-[49px] w-full max-w-[531px] px-6 py-2.5 rounded-[4px] inline-flex items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                    >
                        {{ tt('Gọi Hotline') }}: {{ displayPhone }}
                    </a>
                </div>
            </div>
        </section>

        <!-- SUCCESS MODAL -->
        <BaseModal v-model="showSuccessModal" max-width="md" variant="light">
            <div class="flex flex-col items-center text-center p-4 sm:p-6 space-y-4">
                <h3 class="text-xl sm:text-2xl font-bold text-[#00284B]">{{ tt('Gửi yêu cầu thành công!') }}</h3>
                <p class="text-[#4B5565] text-sm sm:text-base leading-relaxed">
                    {{ tt('Hệ thống đã nhận được thông tin yêu cầu của bạn. Đội ngũ kỹ sư Thành Khang sẽ liên hệ và tư vấn phương án phù hợp trong thời gian sớm nhất.') }}
                </p>
                <button
                    type="button"
                    class="w-full mt-4 h-[44px] rounded-cta bg-primary text-white font-medium text-sm hover:bg-primary-hover transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                    @click="showSuccessModal = false"
                >
                    {{ tt('Đóng thông báo') }}
                </button>
            </div>
        </BaseModal>
    </div>
</template>

<script setup>
import { ref, reactive, computed, getCurrentInstance } from 'vue'
import { Head, Link, useForm } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'
import { validatePhoneNumber } from '@/lib/validator'
import BaseModal from '@/Components/Base/BaseModal.vue'

const props = defineProps({
    agencies: { type: Array, default: () => [] },
    faqs: { type: Array, default: () => [] },
    seo: { type: Object, default: () => null },
})

const instance = getCurrentInstance()
const { translate: trans, isEn, page } = useTranslate()

// Shared site settings (HandleInertiaFrontendRequests shares `global`)
const site = computed(() => page.props.value?.global || {})

const safeRoute = (name, params, fallback) => {
    const proxy = instance?.proxy
    try {
        if (proxy && typeof proxy.route === 'function' && typeof proxy.routes === 'function') {
            const all = proxy.routes() || {}
            const localized = typeof proxy.routeLocaleName === 'function' ? proxy.routeLocaleName(name) : name
            if (all[localized] || all[name]) {
                const url = proxy.route(name, params)
                if (url && url !== '#') return url
            }
        }
    } catch (e) {
        // fall through
    }
    return fallback
}

const homeHref = computed(() => safeRoute('home', undefined, isEn.value ? '/en' : '/'))
const productsHref = computed(() => safeRoute('products.index', undefined, isEn.value ? '/en/products' : '/san-pham'))

const pageTitle = computed(
    () =>
        props.seo?.title ||
        (isEn.value
            ? 'Contact & Technical Consultation - Thành Khang'
            : trans('Liên hệ tư vấn kỹ thuật - Thành Khang | Thiết bị công nghiệp & Phụ tùng thang cuốn'))
)
const pageDescription = computed(
    () =>
        props.seo?.description ||
        (isEn.value
            ? 'Get in touch with Thành Khang for escalator spare parts, industrial conveyors and technical consultation.'
            : trans('Liên hệ ngay với Thành Khang để tư vấn linh kiện thang cuốn, thang nâng ô tô, xích công nghiệp và nhận báo giá nhanh.'))
)

// Safe fallbacks (Figma values) — used only when the CMS key is empty. Reported to Hub as missing keys.
const displayPhone = computed(() => site.value.hotline || '0967 732 665')
const cleanPhone = computed(() => String(displayPhone.value).replace(/[^\d+]/g, ''))
const displayEmail = computed(() => site.value.general_email || 'info@dvktthanhkhang.vn')
const displayHours = computed(() => site.value.general_working_hours || '08:00 – 17:30 (T2 – T6)')
const displayTaxCode = computed(() => site.value.general_tax_code || site.value.tax_code || '107621881')

const contactCards = computed(() => [
    {
        key: 'phone',
        icon: '/assets/svg/icons/footer-phone.svg',
        label: trans('Hotline tư vấn & kỹ thuật'),
        value: displayPhone.value,
        href: 'tel:' + cleanPhone.value,
    },
    {
        key: 'email',
        icon: '/assets/svg/icons/footer-mail.svg',
        label: trans('Email tiếp nhận báo giá'),
        value: displayEmail.value,
        href: 'mailto:' + displayEmail.value,
    },
    {
        key: 'hours',
        icon: '/assets/svg/clock.svg',
        label: trans('Thời gian làm việc'),
        value: displayHours.value,
        href: '',
    },
    {
        key: 'tax',
        icon: '/assets/svg/icons/footer-building.svg',
        label: trans('Thông tin pháp nhân'),
        value: 'MST: ' + displayTaxCode.value,
        href: '',
    },
])

const assurances = computed(() => [
    {
        key: 'check',
        icon: '/assets/figma/quote-popup/icon-check-circle.svg',
        title: trans('Sản phẩm chính hãng – Đầy đủ CO/CQ'),
        description: trans('Cam kết đúng nguồn gốc, đúng thông số kỹ thuật và chính sách bảo hành minh bạch từ nhà sản xuất.'),
    },
    {
        key: 'tool',
        kind: 'tool',
        title: trans('Kỹ sư tư vấn trực tiếp theo ứng dụng'),
        description: trans('Hỗ trợ đọc bản vẽ, đo thông số xích con lăn, linh kiện thang cuốn, thang nâng ô tô & dây chuyền công nghiệp.'),
    },
    {
        key: 'truck',
        icon: '/assets/figma/quote-popup/icon-truck.svg',
        title: trans('Kho hàng sẵn – Giao nhanh toàn quốc 24/7'),
        description: trans('Đáp ứng kịp thời tiến độ công trình và nhu cầu thay thế khẩn cấp, hạn chế tối đa thời gian dừng máy.'),
    },
])

// ---------- Offices (AgencyController `agencies` grouped by region) ----------
const offices = computed(() => {
    const groups = Array.isArray(props.agencies) ? props.agencies : []
    const list = groups.flatMap((group) => (Array.isArray(group?.agencies) ? group.agencies : []))
    const mapped = list
        .filter((a) => a && a.title)
        .map((a) => ({
            title: a.title,
            location: a.location || '',
            phone: a.phone ? String(a.phone).replace(/^\D*(0\d{3})\D*(\d{3})\D*(\d{3})\D*$/, '$1 $2 $3') : displayPhone.value,
            hours: displayHours.value,
            map: a.link_google_map || '',
        }))
    if (mapped.length) return mapped

    // Fallback when the Agency table is empty: CMS general settings (general_address + showroom_address)
    const fallback = []
    const address = site.value.general_address || ''
    const showroom = site.value.showroom_address || ''
    if (address) {
        fallback.push({ title: trans('Trụ sở chính – TNR Nguyễn Chí Thanh'), location: address, phone: displayPhone.value, hours: displayHours.value, map: '' })
    }
    if (showroom && showroom !== address) {
        fallback.push({ title: trans('VPGD & Showroom – Thống Nhất Complex'), location: showroom, phone: displayPhone.value, hours: displayHours.value, map: '' })
    }
    return fallback
})

const activeOfficeIndex = ref(0)
const activeOffice = computed(() => offices.value[activeOfficeIndex.value] || offices.value[0] || null)
const mapsUrl = (office, extra = '') =>
    office?.map || `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(office?.location || office?.title || '')}${extra}`
const directionsHref = computed(() => mapsUrl(activeOffice.value))
const satelliteHref = computed(() => mapsUrl(activeOffice.value, '&basemap=satellite'))

// Illustrated map geometry from Figma 940:2761 (canvas 680×607)
const MAP_W = 680
const MAP_H = 607
const pos = (left, top, width, height) => {
    const style = { left: `${(left / MAP_W) * 100}%`, top: `${(top / MAP_H) * 100}%` }
    if (width !== undefined) style.width = `${(width / MAP_W) * 100}%`
    if (height !== undefined) style.height = `${(height / MAP_H) * 100}%`
    return style
}
const mapZoom = ref(1)
const mapBlocks = [
    [23, 19, 155, 105], [204, 17, 170, 112], [404, 19, 130, 98, true], [557, 21, 110, 118],
    [23, 154, 148, 130], [204, 161, 175, 122], [409, 154, 145, 125], [574, 167, 98, 130, true],
    [29, 314, 145, 160, true], [204, 314, 180, 175], [411, 311, 165, 180], [594, 324, 80, 175],
]
const mapStreetLabels = [
    { text: 'Đ. Nguyễn Chí Thanh', left: 494, top: 184 },
    { text: 'Đường Láng', left: 355, top: 244 },
    { text: 'Đ. Trần Duy Hưng', left: 43, top: 277 },
    { text: 'Đ. Nguyễn Tuân', left: 47, top: 371 },
    { text: 'Đ. Lê Văn Lương', left: 464, top: 381 },
]
// Pin anchors = DOT CENTRE in 680×607 map space, placed on the dashed-route endpoints of
// map-roads.svg (svg (460,158) & (226,385), drawn at pos(-1, -1) → map (459,157) & (225,384)).
// Width-independent: the dot is centred on the anchor and the label is absolutely positioned above it.
const PIN_ANCHORS = [
    { left: 459, top: 157 },
    { left: 225, top: 384 },
]
const mapPins = computed(() =>
    offices.value.slice(0, PIN_ANCHORS.length).map((office, index) => ({ ...PIN_ANCHORS[index], office }))
)

// ---------- FAQ (CMS) ----------
const activeFaq = ref(0)
const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index
}
const faqItems = computed(() => (Array.isArray(props.faqs) ? props.faqs : []).filter((item) => item?.question))

// ---------- Form ----------
function buildInitialInquiryContent() {
    let params = null
    if (typeof window !== 'undefined' && window.location?.search) {
        params = new URLSearchParams(window.location.search)
    } else {
        const pageQuery = page.props.value?.route?.query
        if (pageQuery && typeof pageQuery === 'object') {
            params = new URLSearchParams(pageQuery)
        }
    }
    if (!params) return ''

    const product = (params.get('product') || '').trim()
    const category = (params.get('category') || '').trim()
    const material = (params.get('material') || '').trim()
    const color = (params.get('color') || '').trim()

    if (!product && !category) return ''

    const parts = []
    if (product) {
        parts.push(
            isEn.value
                ? `Product inquiry: ${product}`
                : `${trans('Sản phẩm quan tâm')}: ${product}`
        )
    }
    if (category) {
        parts.push(
            isEn.value
                ? `Category: ${category}`
                : `${trans('Danh mục')}: ${category}`
        )
    }
    if (material) {
        parts.push(
            isEn.value
                ? `Material: ${material}`
                : `${trans('Vật liệu')}: ${material}`
        )
    }
    if (color) {
        parts.push(
            isEn.value
                ? `Color: ${color}`
                : `${trans('Màu sắc')}: ${color}`
        )
    }
    return parts.join('\n')
}

const form = useForm({
    name: '',
    company: '',
    email: '',
    phone: '',
    content: buildInitialInquiryContent(),
})
const clientErrors = reactive({ name: '', email: '', phone: '' })
const showSuccessModal = ref(false)

const formFields = computed(() => [
    {
        key: 'name',
        id: 'contact-name',
        label: trans('Họ và tên'),
        type: 'text',
        autocomplete: 'name',
        placeholder: trans('Nguyễn Văn A'),
        required: true,
    },
    {
        key: 'company',
        id: 'contact-company',
        label: trans('Tên công ty/ Đơn vị'),
        type: 'text',
        autocomplete: 'organization',
        placeholder: trans('Công ty TNHH ABC'),
        required: false,
    },
    {
        key: 'email',
        id: 'contact-email',
        label: trans('Email'),
        type: 'email',
        autocomplete: 'email',
        placeholder: 'example@congty.com',
        required: true,
    },
    {
        key: 'phone',
        id: 'contact-phone',
        label: trans('Số điện thoại'),
        type: 'tel',
        autocomplete: 'tel',
        placeholder: '0901 234 567',
        required: true,
    },
])

const fieldError = (field) => clientErrors[field] || form.errors[field] || ''
const clearFieldError = (field) => {
    if (clientErrors[field]) {
        clientErrors[field] = ''
    }
    if (typeof form.clearErrors === 'function') {
        form.clearErrors(field)
    }
}
const fieldClass = (field) => [
    'w-full bg-white border rounded-[8px] px-3 py-2 text-body-md text-dark placeholder-field-placeholder shadow-xs transition-colors focus:outline-none focus:ring-2 focus:ring-secondary',
    fieldError(field) ? 'border-field-required focus:border-field-required' : 'border-field-border hover:border-gray-modern-400 focus:border-primary',
]

const validate = () => {
    clientErrors.name = ''
    clientErrors.email = ''
    clientErrors.phone = ''
    let ok = true

    if (!form.name || form.name.trim().length < 2) {
        clientErrors.name = isEn.value ? 'Please enter your full name.' : trans('Vui lòng nhập họ và tên (tối thiểu 2 ký tự).')
        ok = false
    }
    const email = (form.email || '').trim()
    if (!email) {
        clientErrors.email = isEn.value ? 'Please enter your email.' : trans('Vui lòng nhập email.')
        ok = false
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        clientErrors.email = isEn.value ? 'Invalid email format.' : trans('Địa chỉ email không đúng định dạng.')
        ok = false
    }
    const phoneCheck = validatePhoneNumber(form.phone, {
        locale: isEn.value ? 'en' : 'vi',
    })
    if (!phoneCheck.valid) {
        clientErrors.phone = isEn.value ? phoneCheck.message : trans(phoneCheck.message)
        ok = false
    }
    return ok
}

const submitContact = () => {
    if (!validate()) return

    form.transform((data) => ({
        ...data,
        contact: {
            type: 'CONTACT_FORM',
            data: {
                'Họ và tên': data.name,
                'Tên công ty/ Đơn vị': data.company,
                'Số điện thoại': data.phone,
                Email: data.email,
                'Lời nhắn': data.content,
            },
        },
    }))

    const submitUrl = safeRoute('contact.store', undefined, '/contacts')

    form.post(submitUrl, {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessModal.value = true
            form.reset()
        },
    })
}
</script>

<style scoped>

.hero-scrim {
    background: linear-gradient(
        180deg,
        rgba(1, 16, 30, 0.78) 0%,
        rgba(1, 16, 30, 0.72) 60%,
        rgba(1, 16, 30, 0.84) 100%
    );
}
@media (min-width: 768px) {
    .hero-scrim {
        background: linear-gradient(
            90deg,
            rgba(1, 16, 30, 0.94) 0%,
            rgba(1, 16, 30, 0.88) 22%,
            rgba(1, 16, 30, 0.74) 40%,
            rgba(1, 16, 30, 0.36) 54%,
            rgba(1, 16, 30, 0) 64%
        );
    }
}
.faq-accordion-grid {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 300ms cubic-bezier(0.22, 1, 0.36, 1);
}
.faq-accordion-grid.is-expanded {
    grid-template-rows: 1fr;
}

/* CTA chain texture (Figma 940:2866 / 940:2869) — ported 1:1 from the 1440 frame. Figma's rectangular
   "Rectangle 18" mask is intentionally NOT applied: clipping comes only from the banner's overflow-hidden. */
.cta-texture {
    display: none;
    position: absolute;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}
.cta-texture--left {
    left: -268.4px;
    top: 85.11px;
    width: 816.817px;
    height: 415.251px;
}
.cta-texture--right {
    right: -290.7px;
    top: -129px;
    width: 835.703px;
    height: 424.852px;
}
.cta-texture__inner {
    position: relative;
    flex: none;
}
.cta-texture__inner img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    max-width: none;
    object-fit: cover;
}
.cta-texture__inner--left {
    width: 779.966px;
    height: 259.989px;
    transform: rotate(-168.1deg) scaleY(-1);
}
.cta-texture__inner--right {
    width: 798px;
    height: 266px;
    transform: rotate(11.9deg) scaleY(-1);
}
/* Show each chain only from the width where its visible pixels no longer touch the CTA title, subtitle or
   buttons (Playwright pixel-diff sweep 768–1920, text rects padded 8px): left ≥ 1088px, right ≥ 1056px. */
@media (min-width: 1056px) {
    .cta-texture--right {
        display: flex;
    }
}
@media (min-width: 1088px) {
    .cta-texture--left {
        display: flex;
    }
}
</style>
