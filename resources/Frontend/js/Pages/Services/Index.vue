<template>
    <div class="bg-[#111322] min-h-screen text-white selection:bg-primary selection:text-white">
        <!-- Head / SEO -->
        <Head>
            <title>{{ pageTitle }}</title>
            <meta name="description" :content="pageDescription" />
            <meta property="og:title" :content="pageTitle" />
            <meta property="og:description" :content="pageDescription" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- Main Services Content Container (1152px on Desktop) -->
        <div class="app-container">
            <!-- Section 1: Hero / Mở đầu (Figma Node 2352:10097) -->
            <section class="py-10">
                <div class="max-w-[626px] flex flex-col gap-6">
                    <!-- Badge chuyên mục (Node 2352:10098) -->
                    <span class="inline-block text-xs sm:text-[14px] sm:leading-[20px] font-normal text-[#84ADFF] uppercase select-none font-sans">
                        {{ tt('CÁC DỊCH VỤ IN ẤN TẠI HH MAKER') }}
                    </span>

                    <!-- Cụm Tiêu đề + Dẫn nhập (Node 2352:10099) -->
                    <div class="flex flex-col gap-2">
                        <!-- Tiêu đề H1 (Node 2352:10100) -->
                        <h1 class="text-2xl sm:text-3xl lg:text-display-hero lg:whitespace-nowrap font-bold text-[#FAFAFA] font-sans">
                            {{ tt('MỘT Ý TƯỞNG - NHIỀU KHẢ NĂNG') }}
                        </h1>

                        <!-- Lời dẫn (Node 2352:10102) -->
                        <p class="text-sm sm:text-[16px] text-[#B9C0D4] leading-relaxed sm:leading-[26px] font-normal font-sans">
                            {{ tt('Bạn có thể bắt đầu bằng một file 3D, một bản vẽ hay chỉ một hình dung. HH Maker đồng hành từ việc làm rõ ý tưởng đến lựa chọn cách tạo ra sản phẩm.') }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section 2: Danh sách 3 Khối Dịch Vụ Xen Kẽ (Z-Pattern - Figma Node 2283:7013) -->
            <section class="pb-12 lg:pb-[56px]">
                <template v-if="serviceList.length > 0">
                    <ServiceBlock
                        v-for="(service, index) in serviceList"
                        :key="service.id || index"
                        :service="service"
                        :reverse="service.reverse"
                        :href="route('services.show', { slug: service.slug })"
                    />
                </template>
                <div v-else class="py-16 text-center text-[#B9C0D4]">
                    {{ tt('Danh sách dịch vụ đang được cập nhật.') }}
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue'
import { Head, usePage } from '@inertiajs/inertia-vue3'
import ServiceBlock from './Components/ServiceBlock.vue'

const props = defineProps({
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
const currentLocale = computed(() => page.props.value?.locale?.current || 'vi')

const pageTitle = computed(() =>
    currentLocale.value === 'en'
        ? 'On-Demand 3D Printing & Design Services - HH Maker'
        : i18n.tt('Dịch Vụ In Ấn & Thiết Kế 3D Theo Yêu Cầu - HH Maker')
)

const pageDescription = computed(() =>
    currentLocale.value === 'en'
        ? 'HH Maker accompanies you from clarifying concepts to choosing fabrication methods: 3D Design, 3D Machining, and Custom Creations with the highest finishing standards.'
        : i18n.tt('HH Maker đồng hành từ làm rõ ý tưởng đến lựa chọn cách tạo ra sản phẩm: Thiết kế 3D, Gia công 3D, và Làm đồ custom với tiêu chuẩn hoàn thiện cao nhất.')
)

const serviceList = computed(() => {
    if (!props.services || !Array.isArray(props.services) || props.services.length === 0) {
        return []
    }
    return props.services.map((item, idx) => ({
        id: item.id || `service-${idx + 1}`,
        slug: item.slug || '',
        category: item.category || `${String(idx + 1).padStart(2, '0')} / ${currentLocale.value === 'en' ? 'SERVICE' : 'DỊCH VỤ'}`,
        title: item.title || '',
        subtitle: item.subtitle || '',
        description: item.description || '',
        image: item.image?.url || (typeof item.image === 'string' ? item.image : '/images/services/service-design-3d.png'),
        tags: Array.isArray(item.tags) ? item.tags : (typeof item.tags === 'string' ? item.tags.split(',').map(t => t.trim()) : []),
        prepTitle: item.prep_title || (currentLocale.value === 'en' ? 'YOU PREPARE' : 'BẠN CHUẨN BỊ'),
        preparation: item.preparation || item.prep_desc || '',
        compTitle: item.comp_title || (currentLocale.value === 'en' ? 'WE CO-CREATE' : 'CÙNG HOÀN THIỆN'),
        completion: item.completion || item.comp_desc || '',
        ctaText: item.cta_text || (currentLocale.value === 'en' ? 'Contact for consultation' : 'Liên hệ tư vấn dịch vụ'),
        reverse: item.reverse !== undefined ? item.reverse : idx % 2 === 1,
    }))
})
</script>
