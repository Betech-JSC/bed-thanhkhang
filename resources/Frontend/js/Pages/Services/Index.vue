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
            <section class="py-10 lg:py-12">
                <div class="max-w-[626px] space-y-4">
                    <!-- Badge chuyên mục -->
                    <span class="inline-block text-xs sm:text-sm font-semibold tracking-wider text-[#84ADFF] uppercase select-none font-sans">
                        CÁC DỊCH VỤ IN ẤN TẠI HH MAKER
                    </span>

                    <!-- Tiêu đề H1 -->
                    <h1 class="text-2xl sm:text-3xl lg:text-display-hero font-bold text-[#FAFAFA] font-sans">
                        MỘT Ý TƯỞNG - NHIỀU KHẢ NĂNG
                    </h1>

                    <!-- Lời dẫn -->
                    <p class="text-sm sm:text-base text-[#B9C0D4] leading-relaxed lg:leading-[26px] font-sans">
                        Bạn có thể bắt đầu bằng một file 3D, một bản vẽ hay chỉ một hình dung. HH Maker đồng hành từ việc làm rõ ý tưởng đến lựa chọn cách tạo ra sản phẩm.
                    </p>
                </div>
            </section>

            <!-- Section 2: Danh sách 3 Khối Dịch Vụ Xen Kẽ (Z-Pattern - Figma Node 2283:7013) -->
            <section class="pb-16 lg:pb-24">
                <ServiceBlock
                    v-for="(service, index) in serviceList"
                    :key="service.id || index"
                    :service="service"
                    :reverse="service.reverse"
                />
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import ServiceBlock from './Components/ServiceBlock.vue'

const props = defineProps({
    services: {
        type: Array,
        default: () => [],
    },
})

const pageTitle = 'Dịch Vụ In Ấn & Thiết Kế 3D Theo Yêu Cầu - HH Maker'
const pageDescription = 'HH Maker đồng hành từ làm rõ ý tưởng đến lựa chọn cách tạo ra sản phẩm: Thiết kế 3D, Gia công 3D, và Làm đồ custom với tiêu chuẩn hoàn thiện cao nhất.'

const defaultServices = [
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

const serviceList = computed(() => {
    if (!props.services || props.services.length === 0) {
        return defaultServices
    }
    return defaultServices.map((def) => {
        const backendItem = props.services.find((s) => s.slug === def.slug || s.title?.toLowerCase() === def.title?.toLowerCase())
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
