<template>
    <div
        v-if="zaloHref || phoneHref || showScrollTop"
        data-testid="floating-cta-widget"
        class="fixed right-3 lg:right-6 bottom-[calc(16px+env(safe-area-inset-bottom))] lg:bottom-[72px] z-[950] flex flex-col items-center gap-[16px] print:hidden"
    >
        <!-- Scroll-to-Top Floating Button -->
        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-2 scale-95"
        >
            <button
                v-if="showScrollTop"
                type="button"
                data-testid="scroll-to-top-button"
                :aria-label="scrollTopLabel"
                :title="scrollTopLabel"
                class="w-11 h-11 lg:w-[60px] lg:h-[60px] rounded-full bg-[#00284B] hover:bg-[#005EA4] active:bg-[#003967] text-white shadow-lg inline-flex items-center justify-center transition-all duration-200 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                @click="scrollToTop"
            >
                <svg
                    class="w-5 h-5 lg:w-6 lg:h-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M12 19V5M12 5L5 12M12 5L19 12"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </button>
        </Transition>

        <!-- Zalo Floating Button (Figma 1264:48278 top circle) -->
        <a
            v-if="zaloHref"
            :href="zaloHref"
            target="_blank"
            rel="noopener noreferrer"
            :aria-label="tt('Liên hệ Zalo')"
            :title="tt('Chat Zalo')"
            class="w-11 h-11 lg:w-[60px] lg:h-[60px] rounded-full bg-[#0068FF] hover:bg-[#0055D4] active:bg-[#0045B0] text-white shadow-lg inline-flex items-center justify-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0068FF] focus-visible:ring-offset-2"
        >
            <svg
                class="w-[28px] h-[14px] lg:w-[40px] lg:h-[18px]"
                viewBox="0 0 60 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <!-- Crisp white Zalo wordmark vector -->
                <path
                    d="M3.2 4.5H16.2C17.2 4.5 17.8 5.4 17.2 6.3L8.4 17.5H16.6C17.4 17.5 18 18.1 18 18.9C18 19.7 17.4 20.3 16.6 20.3H3.4C2.4 20.3 1.8 19.4 2.4 18.5L11.2 7.3H3.2C2.4 7.3 1.8 6.7 1.8 5.9C1.8 5.1 2.4 4.5 3.2 4.5Z"
                    fill="#FFFFFF"
                />
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M30.4 9.5C31.2 9.5 31.8 10.1 31.8 10.9V18.9C31.8 19.7 31.2 20.3 30.4 20.3C29.6 20.3 29 19.8 29 19.1V18.6C28.1 19.8 26.6 20.5 24.9 20.5C21.7 20.5 19.3 18.1 19.3 14.9C19.3 11.7 21.7 9.3 24.9 9.3C26.6 9.3 28.1 10 29 11.2V10.9C29 10.1 29.6 9.5 30.4 9.5ZM25.5 12C23.6 12 22.2 13.2 22.2 14.9C22.2 16.6 23.6 17.8 25.5 17.8C27.4 17.8 28.8 16.6 28.8 14.9C28.8 13.2 27.4 12 25.5 12Z"
                    fill="#FFFFFF"
                />
                <path
                    d="M36 3.8C36.8 3.8 37.5 4.4 37.5 5.2V18.9C37.5 19.7 36.8 20.3 36 20.3C35.2 20.3 34.5 19.7 34.5 18.9V5.2C34.5 4.4 35.2 3.8 36 3.8Z"
                    fill="#FFFFFF"
                />
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M46.6 9.3C50.1 9.3 52.8 11.7 52.8 14.9C52.8 18.1 50.1 20.5 46.6 20.5C43.1 20.5 40.4 18.1 40.4 14.9C40.4 11.7 43.1 9.3 46.6 9.3ZM46.6 12C44.7 12 43.3 13.2 43.3 14.9C43.3 16.6 44.7 17.8 46.6 17.8C48.5 17.8 49.9 16.6 49.9 14.9C49.9 13.2 48.5 12 46.6 12Z"
                    fill="#FFFFFF"
                />
            </svg>
        </a>

        <!-- Hotline Floating Button (Figma 1264:48287 bottom circle) -->
        <a
            v-if="phoneHref"
            :href="phoneHref"
            :aria-label="tt('Gọi hotline') + (displayPhone ? ': ' + displayPhone : '')"
            :title="displayPhone ? tt('Gọi hotline') + ': ' + displayPhone : tt('Gọi hotline')"
            class="w-11 h-11 lg:w-[60px] lg:h-[60px] rounded-full bg-[#008910] hover:bg-[#00700D] active:bg-[#005B0A] text-white shadow-lg inline-flex items-center justify-center transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#008910] focus-visible:ring-offset-2"
        >
            <svg
                class="w-5 h-5 lg:w-[28px] lg:h-[28px]"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path
                    d="M21.97 18.33C21.97 18.69 21.89 19.06 21.72 19.42C21.55 19.78 21.33 20.12 21.04 20.44C20.55 20.98 20.01 21.37 19.4 21.62C18.8 21.87 18.15 22 17.45 22C16.43 22 15.34 21.76 14.19 21.27C13.04 20.78 11.89 20.12 10.75 19.29C9.6 18.45 8.51 17.52 7.47 16.49C6.44 15.45 5.51 14.36 4.68 13.22C3.86 12.08 3.2 10.94 2.72 9.81C2.24 8.67 2 7.58 2 6.54C2 5.86 2.12 5.21 2.36 4.61C2.6 4 2.98 3.44 3.51 2.94C4.15 2.31 4.85 2 5.59 2C5.87 2 6.15 2.06 6.4 2.18C6.66 2.3 6.89 2.48 7.07 2.74L9.39 6.01C9.57 6.26 9.7 6.49 9.79 6.71C9.88 6.92 9.93 7.13 9.93 7.32C9.93 7.56 9.86 7.8 9.72 8.03C9.59 8.26 9.4 8.5 9.16 8.74L8.4 9.53C8.29 9.64 8.24 9.77 8.24 9.93C8.24 10.01 8.25 10.08 8.27 10.16C8.3 10.24 8.33 10.3 8.35 10.36C8.53 10.69 8.84 11.12 9.28 11.64C9.73 12.16 10.21 12.69 10.73 13.22C11.27 13.75 11.79 14.24 12.32 14.69C12.84 15.13 13.27 15.43 13.61 15.61C13.66 15.63 13.72 15.66 13.79 15.69C13.87 15.72 13.95 15.73 14.04 15.73C14.21 15.73 14.34 15.67 14.45 15.56L15.21 14.81C15.46 14.56 15.7 14.37 15.93 14.25C16.16 14.11 16.39 14.04 16.64 14.04C16.83 14.04 17.03 14.08 17.25 14.17C17.47 14.26 17.7 14.39 17.95 14.56L21.26 16.91C21.52 17.09 21.7 17.3 21.81 17.55C21.91 17.8 21.97 18.05 21.97 18.33Z"
                    fill="#FFFFFF"
                />
            </svg>
        </a>
    </div>
</template>

<script>
export default {
    name: 'FloatingCtaWidget',
    data() {
        return {
            showScrollTop: false,
        }
    },
    computed: {
        currentLocale() {
            const pageUrl = String(this.$page?.url || '').split('?')[0].split('#')[0]
            const urlLocale = pageUrl === '/en' || pageUrl.startsWith('/en/') ? 'en' : ''
            const raw =
                this.$page?.props?.locale?.current ||
                urlLocale ||
                this.$inertia?.page?.props?.locale?.current ||
                'vi'
            return String(raw).toLowerCase()
        },
        scrollTopLabel() {
            const translated = typeof this.tt === 'function' ? this.tt('Lên đầu trang') : 'Lên đầu trang'
            if (translated && translated !== 'Lên đầu trang') return translated
            return this.currentLocale === 'en' ? 'Back to top' : 'Lên đầu trang'
        },
        siteConfig() {
            return {
                ...(this.$page?.props?.global || {}),
                ...(this.$page?.props?.configs || {}),
            }
        },
        displayPhone() {
            const raw =
                this.siteConfig.general_phone ||
                this.siteConfig.hotline ||
                this.siteConfig.phone ||
                ''
            return typeof raw === 'string' ? raw.trim() : ''
        },
        cleanPhone() {
            return String(this.displayPhone || '').replace(/[^\d+]/g, '')
        },
        phoneHref() {
            return this.cleanPhone ? `tel:${this.cleanPhone}` : ''
        },
        zaloHref() {
            const raw =
                this.siteConfig.general_zalo ||
                this.siteConfig.zalo ||
                this.siteConfig.social_zalo ||
                ''
            const trimmed = typeof raw === 'string' ? raw.trim() : ''
            if (trimmed) {
                if (/^https?:\/\//i.test(trimmed)) return trimmed
                if (/^zalo\.me\//i.test(trimmed)) return `https://${trimmed}`
                const digits = trimmed.replace(/[^\d]/g, '')
                if (digits) return `https://zalo.me/${digits}`
            }
            const fallbackDigits = this.cleanPhone.replace(/^\+/, '')
            return fallbackDigits ? `https://zalo.me/${fallbackDigits}` : ''
        },
    },
    watch: {
        '$page.url'() {
            this.handleScroll()
        },
    },
    methods: {
        handleScroll() {
            if (typeof window === 'undefined') return
            const scrollY = window.scrollY ?? window.pageYOffset ?? 0
            this.showScrollTop = scrollY > 300
        },
        scrollToTop() {
            if (typeof window === 'undefined') return
            const prefersReducedMotion =
                typeof window.matchMedia === 'function' &&
                window.matchMedia('(prefers-reduced-motion: reduce)').matches
            window.scrollTo({
                top: 0,
                behavior: prefersReducedMotion ? 'auto' : 'smooth',
            })
        },
    },
    mounted() {
        if (typeof window !== 'undefined') {
            this.handleScroll()
            window.addEventListener('scroll', this.handleScroll, { passive: true })
        }
    },
    beforeUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('scroll', this.handleScroll)
        }
    },
}
</script>
