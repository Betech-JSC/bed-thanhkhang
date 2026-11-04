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
        <div class="max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
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
                <ServiceBlock
                    v-for="(service, index) in serviceList"
                    :key="service.id || index"
                    :service="service"
                    :reverse="service.reverse"
                    :href="route('services.show', { slug: service.slug })"
                />
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

const defaultServicesVi = [
    {
        id: 'service-01',
        serviceKey: 'design',
        slug: 'thiet-ke-3d',
        category: '01 / DỊCH VỤ',
        title: 'Thiết kế 3D',
        subtitle: 'Từ phác thảo đến hình khối rõ ràng.',
        description: 'Bạn có ý tưởng nhưng chưa có mô hình? HH Maker cùng bạn làm rõ tỉ lệ, cấu trúc và chi tiết, chuyển hình ảnh tham khảo hoặc bản phác thảo thành thiết kế 3D hướng đến khả năng chế tác thực tế.',
        image: '/images/services/service-design-3d.png',
        tags: ['Phát triển ý tưởng', 'Dựng hình', 'Tối ưu để in'],
        prepTitle: 'BẠN CHUẨN BỊ',
        preparation: 'Ý tưởng, ảnh tham khảo, kích thước dự kiến và mục đích sử dụng.',
        compTitle: 'CÙNG HOÀN THIỆN',
        completion: 'Phương án mô hình 3D và các chi tiết cần thống nhất trước khi gia công.',
        ctaText: 'Liên hệ tư vấn thiết kế 3D',
        reverse: false,
    },
    {
        id: 'service-02',
        serviceKey: 'print',
        slug: 'gia-cong-3d',
        category: '02 / DỊCH VỤ',
        title: 'Gia công 3D',
        subtitle: 'Đưa bản thiết kế ra ngoài màn hình.',
        description: 'Từ file 3D đã có, chúng tôi xem xét cấu trúc mô hình, tư vấn vật liệu và cách in phù hợp. Quá trình gia công chú trọng từng lớp in, bề mặt và các chi tiết liên quan đến công năng của sản phẩm.',
        image: '/images/services/service-print-3d.png',
        tags: ['Kiểm tra mô hình', 'Tư vấn vật liệu', 'In & hoàn thiện'],
        prepTitle: 'BẠN CHUẨN BỊ',
        preparation: 'File 3D, số lượng, kích thước và yêu cầu về bề mặt hoặc lắp ghép.',
        compTitle: 'CÙNG HOÀN THIỆN',
        completion: 'Sản phẩm vật lý theo phương án và yêu cầu gia công đã thống nhất.',
        ctaText: 'Liên hệ tư vấn gia công 3D',
        reverse: true,
    },
    {
        id: 'service-03',
        serviceKey: 'custom',
        slug: 'lam-do-custom',
        category: '03 / DỊCH VỤ',
        title: 'Làm đồ custom',
        subtitle: 'Không chỉ khác biệt. Là của riêng bạn.',
        description: 'Cá nhân hoá phụ kiện, đồ trang trí hoặc phát triển một món đồ từ ý tưởng riêng. HH Maker kết hợp thiết kế và gia công để cân nhắc hình dáng, màu sắc, chi tiết chữ và cách sử dụng trong cùng một phương án.',
        image: '/images/services/service-custom-3d.png',
        tags: ['Quà tặng cá nhân', 'Phụ kiện', 'Đồ trang trí'],
        prepTitle: 'BẠN CHUẨN BỊ',
        preparation: 'Câu chuyện hoặc nhu cầu, mẫu tham khảo muốn cá nhân hoá.',
        compTitle: 'CÙNG HOÀN THIỆN',
        completion: 'Sản phẩm mang dấu ấn riêng, được phát triển theo nội dung đã trao đổi.',
        ctaText: 'Liên hệ tư vấn làm đồ custom',
        reverse: false,
    },
]

const defaultServicesEn = [
    {
        id: 'service-01',
        serviceKey: 'design',
        slug: 'thiet-ke-3d',
        category: '01 / SERVICE',
        title: '3D Design',
        subtitle: 'From sketch to concrete geometry.',
        description: 'Have an idea but no 3D model yet? HH Maker works with you to define proportions, structure, and details, transforming reference photos or sketches into 3D designs tailored for practical manufacturing.',
        image: '/images/services/service-design-3d.png',
        tags: ['Concept Development', '3D Modeling', 'Print Optimization'],
        prepTitle: 'YOU PREPARE',
        preparation: 'Ideas, reference images, intended dimensions, and functional requirements.',
        compTitle: 'WE CO-CREATE',
        completion: '3D model blueprint and technical details aligned before production.',
        ctaText: 'Consult 3D Design',
        reverse: false,
    },
    {
        id: 'service-02',
        serviceKey: 'print',
        slug: 'gia-cong-3d',
        category: '02 / SERVICE',
        title: '3D Machining & Printing',
        subtitle: 'Bringing digital designs into the physical world.',
        description: 'From existing 3D files, we review geometry, recommend suitable materials, and select the optimal printing method. Manufacturing focuses on layer resolution, surface finish, and functional mechanical aspects.',
        image: '/images/services/service-print-3d.png',
        tags: ['Model Verification', 'Material Consultation', 'Printing & Finishing'],
        prepTitle: 'YOU PREPARE',
        preparation: '3D files, quantities, dimensions, and surface or assembly tolerances.',
        compTitle: 'WE CO-CREATE',
        completion: 'Physical parts produced to agreed specifications and machining standards.',
        ctaText: 'Consult 3D Machining',
        reverse: true,
    },
    {
        id: 'service-03',
        serviceKey: 'custom',
        slug: 'lam-do-custom',
        category: '03 / SERVICE',
        title: 'Custom Bespoke Creations',
        subtitle: 'Not just different. Exclusively yours.',
        description: 'Personalizing accessories, home decor, or crafting custom items from scratch. HH Maker combines design and machining to balance form, color, typography, and ergonomics in a single unified solution.',
        image: '/images/services/service-custom-3d.png',
        tags: ['Personal Gifts', 'Accessories', 'Spatial Decor'],
        prepTitle: 'YOU PREPARE',
        preparation: 'Story or requirements, sample models you wish to personalize.',
        compTitle: 'WE CO-CREATE',
        completion: 'Unique signature product tailored to the discussed concept.',
        ctaText: 'Consult Custom Creations',
        reverse: false,
    },
]

const activeDefaultServices = computed(() =>
    currentLocale.value === 'en' ? defaultServicesEn : defaultServicesVi
)

const serviceList = computed(() => {
    const defaults = activeDefaultServices.value
    if (!props.services || props.services.length === 0) {
        return defaults
    }
    return defaults.map((def, idx) => {
        const backendItem = props.services.find(
            (s) => s.id === (idx + 1) || s.slug === def.slug || s.title?.toLowerCase() === def.title?.toLowerCase()
        )
        if (!backendItem) return def
        return {
            ...def,
            slug: backendItem.slug || def.slug,
            title: backendItem.title || def.title,
            description: backendItem.description || def.description,
            image: backendItem.image?.url || def.image,
        }
    })
})
</script>
