<template>
    <div class="bg-[#111322] min-h-screen text-white selection:bg-primary selection:text-white">
        <!-- SEO Head Metadata -->
        <Head>
            <title>{{ pageTitle }}</title>
            <meta name="description" :content="pageDescription" />
            <meta property="og:title" :content="pageTitle" />
            <meta property="og:description" :content="pageDescription" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- SECTION 1: Hero Banner (Figma Node 2283:6841, 1440x680px) -->
        <ProcessHero :banner="heroBanner" />

        <!-- SECTION 2: Stepper Bar 5 Điểm Quy Trình (Figma Node 2283:6851, 1440x176px) -->
        <ProcessStepper />

        <!-- SECTION 3: Chi Tiết 5 Bước Gia Công (Figma Node 2283:6882, 1440x1273px) -->
        <section class="relative w-full py-6 lg:py-10">
            <div class="max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
                <ProcessStepItem
                    v-for="step in processSteps"
                    :key="step.number"
                    :step="step"
                />
            </div>
        </section>

        <!-- SECTION 4: FAQ Accordion (Figma Node 2334:8455, 1440x621px) -->
        <ProcessFaq />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/inertia-vue3'
import ProcessHero from './Components/ProcessHero.vue'
import ProcessStepper from './Components/ProcessStepper.vue'
import ProcessStepItem from './Components/ProcessStepItem.vue'
import ProcessFaq from './Components/ProcessFaq.vue'

const props = defineProps({
    heroBanner: {
        type: Object,
        default: () => null,
    },
    seo: {
        type: Object,
        default: () => null,
    },
})

const page = usePage()
const currentLocale = computed(() => page.props.value?.locale?.current || 'vi')

const pageTitle = computed(() => {
    return (
        props.seo?.title ||
        (currentLocale.value === 'en'
            ? 'Professional 3D Printing & Manufacturing Process - HH Maker'
            : 'Quy Trình Gia Công & Chế Tác 3D Chuyên Nghiệp - HH Maker')
    )
})

const pageDescription = computed(() => {
    return (
        props.seo?.description ||
        (currentLocale.value === 'en'
            ? 'Explore HH Maker\'s 5-step professional 3D printing and design process: From contact, detailed quotation, precise fabrication, careful packaging to safe delivery.'
            : 'Khám phá quy trình 5 bước in 3D và thiết kế chuyên nghiệp tại HH Maker: Từ liên hệ, báo giá chi tiết, gia công chuẩn xác, đóng gói cẩn thận đến giao hàng an toàn.')
    )
})

const processSteps = [
    {
        number: '01',
        title: 'Liên hệ',
        description: 'Mọi sản phẩm bắt đầu bằng một cuộc trao đổi. HH Maker tiếp nhận ý tưởng và làm rõ nhu cầu trước khi đề xuất hướng thực hiện.',
        clientPrep: 'Gửi ý tưởng, ảnh tham khảo hoặc file 3D; nêu mục đích sử dụng, kích thước và số lượng dự kiến.',
        makerAction: 'Xem xét tính khả thi, đặt câu hỏi để hiểu yêu cầu và tư vấn hướng thiết kế, vật liệu, cách gia công.',
    },
    {
        number: '02',
        title: 'Báo giá',
        description: 'Thống nhất những điều quan trọng trước khi bắt tay vào chế tác: phạm vi công việc, yêu cầu sản phẩm và phương án thực hiện.',
        clientPrep: 'Xác nhận thiết kế, kích thước, số lượng và các yêu cầu hoàn thiện; phản hồi những điểm cần điều chỉnh.',
        makerAction: 'Tổng hợp phương án và báo giá theo yêu cầu thực tế. Trao đổi các hạng mục trước khi khách hàng xác nhận triển khai.',
    },
    {
        number: '03',
        title: 'Gia công 3D',
        description: 'Từ mô hình số đến sản phẩm vật lý. Thiết kế, vật liệu và quy trình in được kết nối trong từng chi tiết.',
        clientPrep: 'Xác nhận các điều chỉnh kỹ thuật nếu có để phương án gia công phù hợp với nhu cầu đã trao đổi.',
        makerAction: 'Chuẩn bị mô hình, thực hiện in 3D và hoàn thiện theo phương án đã chốt; kiểm tra hình dáng, bề mặt và chi tiết sản phẩm.',
    },
    {
        number: '04',
        title: 'Đóng gói',
        description: 'Sản phẩm sau gia công được kiểm tra và chuẩn bị cho hành trình vận chuyển.',
        clientPrep: 'Cung cấp thông tin người nhận và lưu ý về việc giao nhận hoặc cách đóng gói nếu có yêu cầu riêng.',
        makerAction: 'Kiểm tra sản phẩm, sắp xếp các chi tiết và lựa chọn cách bảo vệ, đóng gói phù hợp với hình dáng của từng món đồ.',
    },
    {
        number: '05',
        title: 'Gửi hàng',
        description: 'Khép lại quá trình chế tác, mở đầu trải nghiệm sử dụng sản phẩm của bạn.',
        clientPrep: 'Xác nhận địa chỉ, số điện thoại nhận hàng; kiểm tra sản phẩm khi nhận và trao đổi nếu cần hỗ trợ.',
        makerAction: 'Bàn giao kiện hàng để vận chuyển, cung cấp thông tin gửi hàng và tiếp nhận phản hồi sau khi khách hàng nhận sản phẩm.',
    },
]
</script>
