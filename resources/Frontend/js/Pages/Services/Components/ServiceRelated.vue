<template>
    <section v-if="displayCards.length > 0" v-scroll-reveal class="pb-12 lg:pb-16">
        <div class="max-w-[1152px] space-y-6">
            <!-- Section Title (Figma Node 2367:2271) -->
            <div class="flex items-center justify-between">
                <h2 class="text-2xl sm:text-[28px] font-bold text-[#FAFAFA] font-sans leading-[36px]">
                    {{ tt('Dịch vụ liên quan') }}
                </h2>
            </div>

            <!-- 2 Related Service Cards Grid (Figma Node 2367:2276) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-6">
                <Link
                    v-for="(card, index) in displayCards"
                    :key="card.slug || index"
                    :href="card.href"
                    class="group bg-[#111322] border border-[#404968] hover:border-[#2970FF] rounded-[12px] overflow-hidden flex flex-col sm:flex-row sm:h-[220px] transition-colors duration-300 hover:shadow-[0_8px_24px_rgba(41,112,255,0.15)] focus:outline-none focus:ring-1 focus:ring-[#2970FF]"
                >
                    <!-- Left Thumbnail (Figma Node 2367:2278 - 180x220px) -->
                    <div
                        class="w-full sm:w-[180px] h-[180px] sm:h-full shrink-0 overflow-hidden relative flex items-center justify-center p-3"
                        style="background: linear-gradient(180deg, #101121 0%, #2D4BE2 100%)"
                    >
                        <img
                            :src="card.image"
                            :alt="card.title"
                            class="w-full h-full object-contain drop-shadow-[0_8px_16px_rgba(0,0,0,0.5)] transition-transform duration-500 group-hover:scale-105"
                            loading="lazy"
                        />
                    </div>

                    <!-- Right Info (Figma Node 2367:2280 - 384x220px, padding 24px) -->
                    <div class="p-6 flex flex-col justify-between flex-grow min-h-[180px] sm:min-h-0 sm:h-full">
                        <div class="space-y-3">
                            <h3 class="text-xl sm:text-[24px] font-bold text-[#FAFAFA] group-hover:text-[#84ADFF] transition-colors font-sans leading-[29px]">
                                {{ tt(card.title) }}
                            </h3>
                            <p class="text-sm sm:text-[14px] text-[#B9C0D4] leading-[22px] font-sans line-clamp-3">
                                {{ tt(card.description) }}
                            </p>
                        </div>

                        <!-- CTA Link (Node 2367:2283) -->
                        <div class="pt-4 flex items-center text-[14px] leading-[17px] font-medium text-[#84ADFF] group-hover:text-white transition-colors">
                            <span>{{ tt('Tìm hiểu dịch vụ') }}</span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
    currentSlug: {
        type: String,
        default: '',
    },
    services: {
        type: Array,
        default: () => [],
    },
})

const instance = getCurrentInstance()
const i18n = {
    tt: (text) => (typeof instance?.proxy?.tt === 'function' ? instance.proxy.tt(text) : text),
}
const page = usePage()
const isEn = computed(() => page.props.value?.locale?.current === 'en')

const getServiceHref = (slug) => {
    try {
        if (typeof route === 'function') {
            const routeName = isEn.value ? 'en.services.show' : 'vi.services.show'
            return route(routeName, { slug })
        }
    } catch {}
    return isEn.value ? `/en/services/${slug}` : `/dich-vu/${slug}`
}

const displayCards = computed(() => {
    if (props.services && props.services.length > 0) {
        return props.services
            .filter((item) => item.slug !== props.currentSlug)
            .map((item) => ({
                slug: item.slug,
                title: item.title,
                description: item.description,
                image:
                    item.image?.url ||
                    (typeof item.image === 'string' ? item.image : '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp'),
                href: item.slug ? getServiceHref(item.slug) : (isEn.value ? '/en/services' : '/dich-vu'),
            }))
            .slice(0, 2)
    }
    return []
})
</script>
