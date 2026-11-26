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
            <section v-scroll-reveal class="py-10">
                <div class="max-w-[626px] flex flex-col gap-6">
                    <span class="inline-block text-xs sm:text-[14px] sm:leading-[20px] font-normal text-[#84ADFF] uppercase select-none font-sans">
                        {{ tt('GIẢI PHÁP & DỊCH VỤ TẠI THÀNH KHANG TECSAT') }}
                    </span>

                    <!-- Cụm Tiêu đề + Dẫn nhập -->
                    <div class="flex flex-col gap-2">
                        <!-- Tiêu đề H1 -->
                        <h1 class="text-2xl sm:text-3xl lg:text-display-hero lg:whitespace-nowrap font-bold text-[#FAFAFA] font-sans">
                            {{ tt('CHUẨN KỸ THUẬT - VẬN HÀNH BỀN BỈ') }}
                        </h1>

                        <!-- Lời dẫn -->
                        <p class="text-sm sm:text-[16px] text-[#B9C0D4] leading-relaxed sm:leading-[26px] font-normal font-sans">
                            {{ tt('Bạn có thể bắt đầu từ mã linh kiện OEM, thông số bước xích hoặc bài toán mặt bằng nhà xưởng. Thành Khang Tecsat đồng hành từ tư vấn kỹ thuật đến bàn giao thiết bị hoàn chỉnh.') }}
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
import { computed } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import { useTranslate } from '@/composables/useTranslate.js'
import ServiceBlock from './Components/ServiceBlock.vue'

const props = defineProps({
    services: {
        type: Array,
        default: () => [],
    },
})

const { translate, currentLocale } = useTranslate()

const pageTitle = computed(() =>
    currentLocale.value === 'en'
        ? 'Industrial Equipment & Technical Services - Thành Khang Tecsat'
        : translate('Giải Pháp Thiết Bị Công Nghiệp & Kỹ Thuật - Thành Khang Tecsat')
)

const pageDescription = computed(() =>
    currentLocale.value === 'en'
        ? 'Thành Khang Tecsat supplies OEM escalator spare parts, Universal Chains industrial conveyors, and energy-saving BLDC HVLS ceiling fans.'
        : translate('Thành Khang Tecsat cung cấp phụ tùng thang cuốn - thang máy, hệ thống băng tải & xích công nghiệp Universal Chains, và quạt trần công nghiệp BLDC HVLS chuẩn OEM.')
)

const fallbackServicesVi = [
    {
        id: 'service-01',
        serviceKey: 'escalator',
        slug: 'phu-tung-thang-cuon',
        category: '01 / GIẢI PHÁP',
        title: 'Linh kiện Thang cuốn - Thang máy',
        subtitle: 'Vận hành an toàn chuẩn EN 115.',
        description:
            'Cung cấp xích bậc thang cuốn, xích dẫn động chính, tay vịn cao su/kháng khuẩn, bánh xe và con lăn chịu tải cao tương thích đa dạng các hãng thang cuốn thương mại & công cộng.',
        image: '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp',
        tags: ['Xích bậc thang cuốn', 'Chứng chỉ CO/CQ', 'Chuẩn EN 115'],
        prepTitle: 'BẠN CHUẨN BỊ',
        preparation: 'Model thang cuốn, mã linh kiện OEM, bước xích hoặc hình ảnh thực tế tại công trình.',
        compTitle: 'CÙNG HOÀN THIỆN',
        completion: 'Phương án thay thế đồng bộ, kiểm định kỹ thuật và lịch giao hàng tận công trình.',
        ctaText: 'Tư vấn phụ tùng thang cuốn',
        reverse: false,
    },
    {
        id: 'service-02',
        serviceKey: 'chains',
        slug: 'xich-tai-cong-nghiep',
        category: '02 / GIẢI PHÁP',
        title: 'Hệ thống Băng tải & Xích tải Công nghiệp',
        subtitle: 'Độ bền truyền động chuẩn Universal Chains.',
        description:
            'Giải pháp xích tải hành lý sân bay, xích thang nâng ô tô bãi đỗ xe thông minh, xích chốt rỗng và dây chuyền đóng gói chịu tải nặng với thép hợp kim tôi cao tần & Inox 304/316.',
        image: '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
        tags: ['Xích tải nặng', 'Bãi đỗ xe thông minh', 'Băng tải hành lý'],
        prepTitle: 'BẠN CHUẨN BỊ',
        preparation: 'Thông số bước xích, tải trọng kéo đứt, môi trường làm việc và bản vẽ tuyến băng tải.',
        compTitle: 'CÙNG HOÀN THIỆN',
        completion: 'Bộ xích tải và phụ kiện khớp nối đúng dung sai kỹ thuật, sẵn sàng lắp đặt.',
        ctaText: 'Tư vấn xích & băng tải',
        reverse: true,
    },
    {
        id: 'service-03',
        serviceKey: 'fans',
        slug: 'quat-cong-nghiep-bldc',
        category: '03 / GIẢI PHÁP',
        title: 'Giải pháp Tiết kiệm Điện & Quạt BLDC HVLS',
        subtitle: 'Lưu lượng gió lớn, tiết kiệm điện vượt trội.',
        description:
            'Triển khai hệ thống quạt trần công nghiệp sải cánh lớn động cơ nam châm vĩnh cửu không chổi than (BLDC) cho nhà xưởng, kho vận logistics và trung tâm thương mại.',
        image: '/images/products/thanhkhang/xich-bai-do-xe/vertical-circulation-photo.webp',
        tags: ['Động cơ BLDC', 'Tiết kiệm điện', 'Làm mát nhà xưởng'],
        prepTitle: 'BẠN CHUẨN BỊ',
        preparation: 'Diện tích mặt bằng, chiều cao trần nhà xưởng và bản vẽ bố trí kết cấu mái.',
        compTitle: 'CÙNG HOÀN THIỆN',
        completion: 'Phương án bố trí số lượng quạt tối ưu luồng khí và bài toán hoàn vốn điện năng.',
        ctaText: 'Tư vấn giải pháp BLDC',
        reverse: false,
    },
]

const fallbackServicesEn = [
    {
        id: 'service-01',
        serviceKey: 'escalator',
        slug: 'phu-tung-thang-cuon',
        category: '01 / SOLUTION',
        title: 'Escalator & Elevator Spare Parts',
        subtitle: 'Safe operation compliant with EN 115.',
        description:
            'Supplying escalator step chains, main drive chains, antibacterial handrails, and heavy-duty rollers compatible with major commercial and public-transit escalator brands.',
        image: '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp',
        tags: ['Step Chains', 'CO/CQ Certified', 'EN 115 Standard'],
        prepTitle: 'YOU PREPARE',
        preparation: 'Escalator model, OEM part number, chain pitch, or on-site inspection photos.',
        compTitle: 'WE DELIVER',
        completion: 'Matched OEM replacement plan, technical inspection, and scheduled site delivery.',
        ctaText: 'Consult Escalator Parts',
        reverse: false,
    },
    {
        id: 'service-02',
        serviceKey: 'chains',
        slug: 'xich-tai-cong-nghiep',
        category: '02 / SOLUTION',
        title: 'Conveyor Systems & Industrial Chains',
        subtitle: 'Universal Chains transmission durability.',
        description:
            'Airport baggage handling chains, automated car-lift parking chains, hollow-pin chains, and packaging conveyor lines crafted from heat-treated alloy steel and SUS304/316.',
        image: '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
        tags: ['Heavy-Duty Chains', 'Car Parking Lift', 'Baggage Conveyor'],
        prepTitle: 'YOU PREPARE',
        preparation: 'Chain pitch specs, breaking load, operating environment, and conveyor layout drawings.',
        compTitle: 'WE DELIVER',
        completion: 'Precision-matched industrial chain assembly ready for installation.',
        ctaText: 'Consult Chains & Conveyors',
        reverse: true,
    },
    {
        id: 'service-03',
        serviceKey: 'fans',
        slug: 'quat-cong-nghiep-bldc',
        category: '03 / SOLUTION',
        title: 'Energy Saving & BLDC HVLS Fans',
        subtitle: 'High airflow volume with superior energy savings.',
        description:
            'Deploying large-diameter High-Volume Low-Speed industrial ceiling fans powered by brushless DC (BLDC) motors for factories, logistics hubs, and commercial halls.',
        image: '/images/products/thanhkhang/xich-bai-do-xe/vertical-circulation-photo.webp',
        tags: ['BLDC Motor', 'Energy Saving', 'Factory Ventilation'],
        prepTitle: 'YOU PREPARE',
        preparation: 'Floor area, ceiling clearance height, and roof truss layout drawings.',
        compTitle: 'WE DELIVER',
        completion: 'Optimized airflow layout plan and power-saving ROI calculation.',
        ctaText: 'Consult BLDC Solutions',
        reverse: false,
    },
]

const fallbackServices = computed(() =>
    currentLocale.value === 'en' ? fallbackServicesEn : fallbackServicesVi
)

const resolveFallbackForItem = (item, idx, defaults) => {
    const slug = String(item?.slug || '').toLowerCase()
    const title = String(item?.title || '').toLowerCase()
    if (slug.includes('thang-cuon') || slug.includes('escalator') || title.includes('thang cuốn')) {
        return defaults[0]
    }
    if (slug.includes('xich') || slug.includes('bang-tai') || slug.includes('chain') || title.includes('xích') || title.includes('băng tải')) {
        return defaults[1]
    }
    if (slug.includes('quat') || slug.includes('bldc') || title.includes('quạt') || title.includes('tiết kiệm')) {
        return defaults[2]
    }
    return defaults[idx] || defaults[0]
}

const serviceList = computed(() => {
    const defaults = fallbackServices.value
    if (!props.services || !Array.isArray(props.services) || props.services.length === 0) {
        return defaults
    }

    const mapped = props.services.map((item, idx) => {
        const fb = resolveFallbackForItem(item, idx, defaults)
        const rawImage = item.image?.url || (typeof item.image === 'string' ? item.image : '')
        const rawTags = Array.isArray(item.tags)
            ? item.tags
            : typeof item.tags === 'string' && item.tags.trim()
            ? item.tags.split(',').map((t) => t.trim()).filter(Boolean)
            : []
        const rawTitle = (item.title || '').trim()
        const normalizedTitle =
            rawTitle.toLowerCase() === 'custom' && currentLocale.value !== 'en'
                ? fb.title
                : rawTitle || fb.title

        return {
            id: item.id || fb.id || `service-${idx + 1}`,
            serviceKey: item.serviceKey || fb.serviceKey || 'design',
            slug: item.slug || fb.slug || '',
            category: item.category || fb.category,
            title: normalizedTitle,
            subtitle: item.subtitle || fb.subtitle || '',
            description: item.description || fb.description || '',
            image: rawImage || fb.image,
            tags: rawTags.length > 0 ? rawTags : fb.tags,
            prepTitle: item.prep_title || item.prepTitle || fb.prepTitle,
            preparation: item.preparation || item.prep_desc || fb.preparation || '',
            compTitle: item.comp_title || item.compTitle || fb.compTitle,
            completion: item.completion || item.comp_desc || fb.completion || '',
            ctaText: item.cta_text || item.ctaText || fb.ctaText,
            reverse: item.reverse,
        }
    })

    // Chuẩn hóa thứ tự hiển thị 3 giải pháp cốt lõi Thành Khang Tecsat
    const orderMap = { 'phu-tung-thang-cuon': 1, 'xich-tai-cong-nghiep': 2, 'quat-cong-nghiep-bldc': 3, 'industrial-engineering': 4 }
    mapped.sort((a, b) => (orderMap[a.slug] || 99) - (orderMap[b.slug] || 99))

    return mapped.map((item, idx) => ({
        ...item,
        category: `${String(idx + 1).padStart(2, '0')} / ${currentLocale.value === 'en' ? 'SERVICE' : 'DỊCH VỤ'}`,
        reverse: idx % 2 === 1,
    }))
})
</script>
