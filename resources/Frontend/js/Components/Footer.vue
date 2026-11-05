<template>
    <footer id="footer-section" class="bg-[#111322] border-t border-[#F5F8FF]/10 text-secondary pt-14 pb-8">
        <div class="app-container space-y-12">
            <!-- 4 Column Grid (Figma 2261:5820 / 2226:3250) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <!-- Column 1: Brand Info (5 cols / 361px) (Figma 2226:3251) -->
                <div class="lg:col-span-5 space-y-5">
                    <Link :href="route('home')" class="inline-flex items-center">
                        <img
                            src="/assets/images/header/figma-header-logo.svg"
                            alt="HH Maker Logo"
                            class="h-[48px] w-auto object-contain"
                        />
                    </Link>
                    <div class="space-y-2 pt-1 max-w-[361px]">
                        <h3 class="text-xl lg:text-[24px] font-medium text-[#F5F8FF] tracking-normal leading-[34px]">
                            {{ tt('CÔNG TY IN ẤN 3D HH MAKER') }}
                        </h3>
                        <p class="text-sm sm:text-base text-[#D1E0FF] leading-[22px] font-normal">
                            {{ tt(displayAddress) }}
                        </p>
                    </div>
                </div>

                <!-- Column 2: Trang chủ / Điều hướng (2 cols) (Figma 2226:3263) -->
                <div class="lg:col-span-2 space-y-4">
                    <h4 class="text-base font-bold text-[#D1E0FF] tracking-normal font-sans leading-[22px]">
                        {{ tt('Trang chủ') }}
                    </h4>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <Link :href="route('products.index')" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                {{ tt('Sản phẩm') }}
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('histories.index')" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                {{ tt('Giới thiệu') }}
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('process.index')" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                {{ tt('Quy trình') }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Dịch vụ (2 cols) (Figma 2226:3270) -->
                <div class="lg:col-span-2 space-y-4">
                    <h4 class="text-base font-bold text-[#D1E0FF] tracking-normal font-sans leading-[22px]">
                        {{ tt('Dịch vụ') }}
                    </h4>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <Link :href="route('services.index')" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                {{ tt('Thiết kế 3D') }}
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('services.index')" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                {{ tt('Gia công 3D') }}
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('services.index')" class="text-[#F5F8FF] hover:text-[#2970FF] transition-colors leading-[20px] font-medium">
                                {{ tt('Custom 3D') }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Liên hệ tư vấn (3 cols) (Figma 2226:3276) -->
                <div class="lg:col-span-3 space-y-4">
                    <h4 class="text-base font-bold text-[#D1E0FF] tracking-normal font-sans leading-[22px]">
                        {{ tt('Liên hệ tư vấn') }}
                    </h4>
                    <div class="space-y-3 text-sm font-medium">
                        <a
                            :href="'tel:' + displayHotline.replace(/\s+/g, '')"
                            class="group flex items-center gap-2.5 text-[#F5F8FF] hover:text-[#2970FF] transition-colors"
                        >
                            <svg class="w-5 h-5 text-[#F5F8FF] group-hover:text-[#2970FF] transition-colors shrink-0" viewBox="0 0 48 48" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M28.0993 12C30.0528 12.3811 31.8481 13.3365 33.2555 14.7439C34.6628 16.1512 35.6182 17.9465 35.9993 19.9M28.0993 4C32.1579 4.45087 35.9425 6.26835 38.8318 9.15402C41.7211 12.0397 43.5433 15.822 43.9993 19.88M20.4533 27.7261C18.0501 25.323 16.1525 22.6057 14.7606 19.7065C14.6408 19.4571 14.581 19.3324 14.535 19.1746C14.3715 18.6139 14.4889 17.9254 14.8289 17.4505C14.9246 17.3169 15.0389 17.2026 15.2676 16.974C15.9668 16.2747 16.3164 15.9251 16.5449 15.5736C17.4069 14.2478 17.4069 12.5386 16.5449 11.2129C16.3164 10.8613 15.9668 10.5117 15.2676 9.81249L14.8778 9.42275C13.8149 8.35986 13.2835 7.82842 12.7127 7.53973C11.5776 6.96559 10.2371 6.96559 9.10195 7.53973C8.53119 7.82842 7.99975 8.35987 6.93686 9.42275L6.62159 9.73802C5.56235 10.7973 5.03273 11.3269 4.62823 12.047C4.17939 12.846 3.85666 14.0869 3.85939 15.0034C3.86185 15.8293 4.02206 16.3937 4.34247 17.5226C6.06441 23.5894 9.31336 29.3141 14.0893 34.0901C18.8653 38.8661 24.59 42.115 30.6568 43.8369C31.7857 44.1574 32.3501 44.3176 33.176 44.32C34.0925 44.3228 35.3335 44 36.1325 43.5512C36.8525 43.1467 37.3822 42.6171 38.4414 41.5578L38.7567 41.2426C39.8196 40.1797 40.351 39.6482 40.6397 39.0775C41.2138 37.9423 41.2138 36.6018 40.6397 35.4667C40.351 34.8959 39.8196 34.3645 38.7567 33.3016L38.3669 32.9119C37.6677 32.2127 37.3181 31.863 36.9666 31.6345C35.6408 30.7725 33.9316 30.7725 32.6058 31.6345C32.2543 31.863 31.9047 32.2127 31.2055 32.9119C30.9768 33.1405 30.8625 33.2548 30.7289 33.3505C30.254 33.6905 29.5655 33.8079 29.0048 33.6445C28.847 33.5985 28.7223 33.5386 28.473 33.4189C25.5737 32.0269 22.8564 30.1293 20.4533 27.7261Z" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ displayHotline }}</span>
                        </a>
                        <a
                            :href="'mailto:' + displayEmail"
                            class="group flex items-center gap-2.5 text-[#F5F8FF] hover:text-[#2970FF] transition-colors"
                        >
                            <svg class="w-5 h-5 text-[#F5F8FF] group-hover:text-[#2970FF] transition-colors shrink-0" viewBox="0 0 48 48" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M42.9999 36L29.7143 24M18.2857 24L5.00007 36M4 14L20.3298 25.4309C21.6522 26.3565 22.3134 26.8193 23.0325 26.9986C23.6678 27.157 24.3322 27.157 24.9675 26.9986C25.6866 26.8193 26.3478 26.3565 27.6702 25.4309L44 14M13.6 40H34.4C37.7603 40 39.4405 40 40.7239 39.346C41.8529 38.7708 42.7708 37.8529 43.346 36.7239C44 35.4405 44 33.7603 44 30.4V17.6C44 14.2397 44 12.5595 43.346 11.2761C42.7708 10.1471 41.8529 9.2292 40.7239 8.65396C39.4405 8 37.7603 8 34.4 8H13.6C10.2397 8 8.55953 8 7.27606 8.65396C6.14708 9.2292 5.2292 10.1471 4.65396 11.2761C4 12.5595 4 14.2397 4 17.6V30.4C4 33.7603 4 35.4405 4.65396 36.7239C5.2292 37.8529 6.14708 38.7708 7.27606 39.346C8.55953 40 10.2397 40 13.6 40Z" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ displayEmail }}</span>
                        </a>
                        <a
                            :href="'mailto:' + ($page.props.global?.general_email || 'lamhonghai123@gmail.com')"
                            class="group flex items-center gap-2.5 text-[#F5F8FF] hover:text-[#2970FF] transition-colors"
                        >
                            <svg class="w-5 h-5 text-[#F5F8FF] group-hover:text-[#2970FF] transition-colors shrink-0" viewBox="0 0 48 48" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M42.9999 36L29.7143 24M18.2857 24L5.00007 36M4 14L20.3298 25.4309C21.6522 26.3565 22.3134 26.8193 23.0325 26.9986C23.6678 27.157 24.3322 27.157 24.9675 26.9986C25.6866 26.8193 26.3478 26.3565 27.6702 25.4309L44 14M13.6 40H34.4C37.7603 40 39.4405 40 40.7239 39.346C41.8529 38.7708 42.7708 37.8529 43.346 36.7239C44 35.4405 44 33.7603 44 30.4V17.6C44 14.2397 44 12.5595 43.346 11.2761C42.7708 10.1471 41.8529 9.2292 40.7239 8.65396C39.4405 8 37.7603 8 34.4 8H13.6C10.2397 8 8.55953 8 7.27606 8.65396C6.14708 9.2292 5.2292 10.1471 4.65396 11.2761C4 12.5595 4 14.2397 4 17.6V30.4C4 33.7603 4 35.4405 4.65396 36.7239C5.2292 37.8529 6.14708 38.7708 7.27606 39.346C8.55953 40 10.2397 40 13.6 40Z" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
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
                <p>© {{ tt('Bản quyền') }} {{ currentYear }} HH MAKER</p>
                <div class="flex flex-wrap items-center justify-center sm:justify-end gap-6 sm:gap-10">
                    <Link
                        v-for="(item, index) in policyLinks"
                        :key="index"
                        :href="item.url"
                        class="text-[#D1E0FF] hover:text-white transition-colors"
                    >
                        {{ item.title }}
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
        currentLocale() {
            return this.$inertia?.page?.props?.locale?.current || this.$page?.props?.locale?.current || 'vi'
        },
        policyLinks() {
            const isEn = this.currentLocale === 'en'
            const policies = [
                {
                    title: this.tt('Chính sách bảo mật'),
                    slug: isEn ? 'privacy-policy' : 'chinh-sach-bao-mat',
                },
                {
                    title: this.tt('Điều khoản dịch vụ'),
                    slug: isEn ? 'terms-of-service' : 'dieu-khoan-dich-vu',
                },
                {
                    title: this.tt('Chính sách bảo hành & đổi trả'),
                    slug: isEn ? 'warranty-return-policy' : 'chinh-sach-bao-hanh-doi-tra',
                },
            ]
            return policies.map((item) => ({
                ...item,
                url: this.getPolicyUrl(item.slug),
            }))
        },
        displayAddress() {
            const raw = this.address || this.$page.props.global?.general_address || this.tt('Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh')
            return this.tt(raw)
        },
        displayHotline() {
            return this.hotline || this.$page.props.global?.general_phone || '0906636425'
        },
        displayEmail() {
            return this.email || this.$page.props.global?.general_email || 'info@hhmaker.vn'
        },
    },
    methods: {
        getPolicyUrl(slug) {
            if (typeof this.route === 'function') {
                try {
                    return this.route('policies.show', slug)
                } catch (e) {
                    // Fallback to localized URL
                }
            }
            return this.currentLocale === 'en' ? `/en/policies/${slug}` : `/chinh-sach/${slug}`
        },
    },
}
</script>
