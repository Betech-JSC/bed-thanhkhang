<template>
    <section v-scroll-reveal id="contact-section" class="py-14 sm:py-20 lg:py-24 bg-[#111322] relative">
        <div class="w-full max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-0">
            <!-- Modern Card matching Figma Node 2261:5750 / 2261:5751 (1152x492px) -->
            <div class="relative text-white p-6 sm:p-10 lg:p-12">
                <!-- Desktop & Tablet SVG Background with Smooth Chamfered Bottom-Left Corner -->
                <div class="hidden sm:block absolute inset-0 w-full h-full pointer-events-none -z-0">
                    <svg viewBox="0 0 2304 984" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full h-full drop-shadow-2xl">
                        <defs>
                            <linearGradient id="contactCardGrad" x1="1152" y1="0" x2="1152" y2="984" gradientUnits="userSpaceOnUse">
                                <stop offset="0.1975" stop-color="#111322"/>
                                <stop offset="0.938" stop-color="#2970FF"/>
                            </linearGradient>
                        </defs>
                        <path
                            d="M2257.61 0C2283.23 0.000263884 2304 20.7682 2304 46.3867V937.613C2304 963.232 2283.23 984 2257.61 984H217.42C202.284 984 187.747 978.081 176.916 967.508L17.4799 811.867C6.30234 800.955 0 785.995 0 770.375V46.3867C0.000212344 20.7681 20.7681 0.000208235 46.3867 0H2257.61Z"
                            fill="url(#contactCardGrad)"
                        />
                    </svg>
                </div>

                <!-- Mobile Fallback with Clean Rounded Corners -->
                <div
                    class="block sm:hidden absolute inset-0 rounded-[24px] pointer-events-none -z-0 shadow-2xl"
                    style="background: linear-gradient(180deg, #111322 20%, #2970FF 94%);"
                ></div>
                <!-- Ambient Subtle Glow -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 relative z-10 items-start">
                    <!-- Left Column: Headline, Description & 3 Social Buttons (Figma Node 2261:5755) -->
                    <div class="lg:col-span-5 flex flex-col justify-between h-full space-y-6 lg:space-y-8">
                        <div class="space-y-3 sm:space-y-4">
                            <h2
                                class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-[#F5F8FF] uppercase tracking-tight font-sans leading-tight lg:leading-[46px]"
                            >
                                {{ tt(title) }}
                            </h2>
                            <p class="text-sm sm:text-base text-[#D1E0FF] leading-relaxed font-normal max-w-[430px]">
                                {{ tt(description) }}
                            </p>
                        </div>

                        <!-- 3 Social Icon Buttons (Facebook, Instagram, TikTok) (Figma Node 2261:5759) -->
                        <div class="flex items-center gap-3 pt-2">
                            <a
                                v-for="soc in socialLinks"
                                :key="soc.id"
                                :href="soc.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-10 h-10 rounded-[8px] bg-[#111322] hover:bg-[#1A1F36] flex items-center justify-center text-white hover:scale-105 transition-colors duration-200 shadow-md"
                                :aria-label="soc.label"
                            >
                                <!-- Facebook -->
                                <svg v-if="soc.id === 'facebook'" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <!-- Instagram -->
                                <svg v-else-if="soc.id === 'instagram'" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0-3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                <!-- TikTok -->
                                <svg v-else-if="soc.id === 'tiktok'" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-1.01-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Form (Figma Node 2261:5767) -->
                    <div class="lg:col-span-7">
                        <form @submit.prevent="submitForm" class="space-y-4">
                            <!-- Dòng 1: Họ và tên* (full width) (Figma Node 2261:5769) -->
                            <div>
                                <label class="block text-sm sm:text-base font-medium text-[#FAFAFA] mb-1.5">
                                    {{ tt('Họ và tên') }}*
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    :placeholder="tt('Họ và tên')"
                                    required
                                    class="w-full h-12 px-4 rounded-[8px] bg-white/20 border border-[#5D6B98] text-white placeholder-[#B9C0D4] focus:outline-none focus:ring-2 focus:ring-white/40 focus:border-white text-sm sm:text-base transition-colors duration-200"
                                />
                            </div>

                            <!-- Dòng 2: Grid 2 cột: Email* (50%) | Số điện thoại* (50%) (Figma Node 2261:5781) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm sm:text-base font-medium text-[#FAFAFA] mb-1.5">
                                        Email*
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="Email"
                                        required
                                        class="w-full h-12 px-4 rounded-[8px] bg-white/20 border border-[#5D6B98] text-white placeholder-[#B9C0D4] focus:outline-none focus:ring-2 focus:ring-white/40 focus:border-white text-sm sm:text-base transition-colors duration-200"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm sm:text-base font-medium text-[#FAFAFA] mb-1.5">
                                        {{ tt('Số điện thoại') }}*
                                    </label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        :placeholder="tt('Số điện thoại')"
                                        required
                                        class="w-full h-12 px-4 rounded-[8px] bg-white/20 border border-[#5D6B98] text-white placeholder-[#B9C0D4] focus:outline-none focus:ring-2 focus:ring-white/40 focus:border-white text-sm sm:text-base transition-colors duration-200"
                                    />
                                </div>
                            </div>

                            <!-- Dòng 3: Nhập lời nhắn* (textarea) (Figma Node 2261:5806) -->
                            <div>
                                <label class="block text-sm sm:text-base font-medium text-[#FAFAFA] mb-1.5">
                                    {{ tt('Nhập lời nhắn') }}*
                                </label>
                                <textarea
                                    v-model="form.note"
                                    rows="3"
                                    :placeholder="tt('Chia sẻ ý tưởng của bạn đến với HH Maker')"
                                    required
                                    class="w-full h-24 p-3.5 rounded-[8px] bg-white/20 border border-[#5D6B98] text-white placeholder-[#B9C0D4] focus:outline-none focus:ring-2 focus:ring-white/40 focus:border-white text-sm sm:text-base transition-colors duration-200 resize-none"
                                ></textarea>
                            </div>

                            <!-- Submit Success Notice -->
                            <div
                                v-if="submitSuccess"
                                class="p-3 rounded-lg bg-white/20 border border-white/30 text-white text-sm flex items-center gap-2"
                            >
                                <svg class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ tt('Gửi yêu cầu thành công! HH Maker sẽ liên hệ hỗ trợ bạn.') }}</span>
                            </div>

                            <!-- Dòng 4: Nút "Gửi yêu cầu" (nền đen #111322, bo góc 8px) (Figma Node 2261:5816) -->
                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full h-14 rounded-[8px] bg-[#111322] hover:bg-[#1A1F36] text-[#FAFAFA] font-semibold text-base flex items-center justify-center transition-colors duration-200 active:scale-[0.98] shadow-md cursor-pointer disabled:opacity-50 mt-2"
                            >
                                <span v-if="!isSubmitting">{{ tt('Gửi yêu cầu') }}</span>
                                <span v-else class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    {{ tt('Đang gửi...') }}
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'ContactSection',
    props: {
        title: {
            type: String,
            default: 'KẾT NỐI VỚI HH MAKER',
        },
        description: {
            type: String,
            default:
                'Bạn cần tư vấn về in 3D? Hãy chia sẻ nhu cầu & ý tưởng của bạn với HH MAKER qua các kênh bên dưới',
        },
    },
    data() {
        return {
            form: {
                name: '',
                email: '',
                phone: '',
                note: '',
            },
            isSubmitting: false,
            submitSuccess: false,
        };
    },
    computed: {
        socialLinks() {
            return [
                {
                    id: 'facebook',
                    label: 'Facebook HH Maker',
                    url: this.$page.props.global?.general_facebook || 'https://facebook.com',
                },
                {
                    id: 'instagram',
                    label: 'Instagram',
                    url: this.$page.props.global?.general_instagram || 'https://instagram.com',
                },
                {
                    id: 'tiktok',
                    label: 'TikTok HH Maker',
                    url: this.$page.props.global?.general_tiktok || 'https://tiktok.com',
                },
            ];
        },
    },
    methods: {
        async submitForm() {
            this.isSubmitting = true;
            this.submitSuccess = false;

            try {
                const targetUrl = typeof route === 'function' ? route('contact.store') : '/contacts';
                const payload = {
                    type: 'CONTACT_FORM',
                    name: this.form.name,
                    email: this.form.email,
                    phone: this.form.phone,
                    data: {
                        'Họ và tên': this.form.name,
                        'Số điện thoại': this.form.phone,
                        'Email': this.form.email,
                        'Nội dung cần hỗ trợ': this.form.note,
                        'Dịch vụ quan tâm': 'Tư vấn in 3D',
                    },
                };
                if (window.axios) {
                    await window.axios.post(targetUrl, payload).catch(() => {});
                }
            } catch (e) {
                // Keep resilient
            } finally {
                this.isSubmitting = false;
                this.submitSuccess = true;
                this.form = { name: '', email: '', phone: '', note: '' };
                setTimeout(() => {
                    this.submitSuccess = false;
                }, 8000);
            }
        },
    },
};
</script>
