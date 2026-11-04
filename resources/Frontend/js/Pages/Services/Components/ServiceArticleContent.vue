<template>
    <article class="w-full max-w-[840px] space-y-10 font-sans text-left">
        <!-- Mục 01: Khi nào bạn cần thiết kế 3D? (Figma Node 2367:2197) -->
        <section id="section-01" class="scroll-mt-32 space-y-4">
            <h2 class="text-2xl sm:text-[28px] font-bold text-[#FAFAFA] leading-tight sm:leading-[36px]">
                {{ tt('Khi nào bạn cần thiết kế 3D?') }}
            </h2>
            <div class="space-y-4 text-base text-[#B9C0D4] leading-[26px]">
                <p>
                    {{ tt('Bạn đã hình dung một món đồ nhưng chưa có mô hình để triển khai? Một bức ảnh thường chỉ cho thấy một góc nhìn; bản phác thảo có thể chưa thể hiện rõ chiều sâu, tỉ lệ hay cách các phần kết nối với nhau.') }}
                </p>
                <p>
                    {{ tt('Thiết kế 3D giúp làm rõ những điều đó trước khi chuyển sang sản phẩm vật lý. Đây là bước phù hợp khi bạn muốn phát triển một hình dáng mới, điều chỉnh mẫu tham khảo hoặc kiểm tra các chi tiết cần lắp ghép. HH Maker cùng bạn chuyển thông tin ban đầu thành phương án có thể xem xét và trao đổi cụ thể hơn.') }}
                </p>
            </div>
        </section>

        <!-- Mục 02: Từ ý tưởng tới mô hình 3D (Figma Node 2367:2201) -->
        <section id="section-02" class="scroll-mt-32 space-y-5">
            <div class="space-y-3">
                <h2 class="text-2xl sm:text-[28px] font-bold text-[#FAFAFA] leading-tight sm:leading-[36px]">
                    {{ tt('Từ ý tưởng tới mô hình 3D') }}
                </h2>
                <p class="text-base text-[#B9C0D4] leading-[28px]">
                    {{ tt('Không nhất thiết phải chốt mọi chi tiết từ đầu. Điều quan trọng là làm rõ từng phần theo thứ tự, để những điều chỉnh sau đó có cơ sở.') }}
                </p>
            </div>

            <!-- 4 Bước thiết kế (Figma Node 2367:2204) -->
            <div class="space-y-0">
                <div
                    v-for="step in steps"
                    :key="step.number"
                    class="border-t border-[#404968] py-5 sm:py-[20px] flex items-start gap-4 sm:gap-5"
                >
                    <!-- Số bước badge (40x40px, bg #30374F, text #84ADFF, bo góc 8px) -->
                    <div class="w-10 h-10 rounded-[8px] bg-[#30374F] flex items-center justify-center shrink-0 mt-0.5 select-none">
                        <span class="text-[15px] font-semibold text-[#84ADFF] leading-[18px]">
                            {{ step.number }}
                        </span>
                    </div>

                    <!-- Nội dung bước -->
                    <div class="space-y-2 flex-grow">
                        <h3 class="text-lg sm:text-[19px] font-semibold text-[#FAFAFA] leading-[27px]">
                            {{ tt(step.title) }}
                        </h3>
                        <p class="text-base text-[#B9C0D4] leading-[28px]">
                            {{ tt(step.description) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mục 03: Bạn cần chuẩn bị gì? (Figma Node 2367:2229) -->
        <section id="section-03" class="scroll-mt-32 space-y-5">
            <div class="space-y-3">
                <h2 class="text-2xl sm:text-[28px] font-bold text-[#FAFAFA] leading-tight sm:leading-[36px]">
                    {{ tt('Bạn cần chuẩn bị gì?') }}
                </h2>
                <p class="text-base text-[#B9C0D4] leading-[28px]">
                    {{ tt('Thông tin càng rõ, việc trao đổi càng sát với mong muốn của bạn. Trước khi gửi yêu cầu, hãy tập hợp những nội dung dưới đây; phần nào chưa chắc có thể ghi chú để cùng thảo luận.') }}
                </p>
            </div>

            <!-- Card Checklist Thông Số Kỹ Thuật (Figma Node 2367:2232 - bg #171B30, bo góc 12px, padding 28px) -->
            <div class="w-full rounded-[12px] bg-[#171B30] p-6 sm:p-7 space-y-5">
                <div
                    v-for="(item, index) in checklistItems"
                    :key="index"
                    class="flex items-start gap-3 sm:gap-3.5"
                >
                    <!-- Checkmark "✓" (16px text, color #84ADFF) -->
                    <span class="text-[16px] font-bold text-[#84ADFF] leading-[24px] shrink-0 select-none">
                        ✓
                    </span>

                    <!-- Detail -->
                    <div class="space-y-1.5 flex-grow">
                        <h4 class="text-[16px] font-semibold text-[#FAFAFA] leading-[24px]">
                            {{ tt(item.title) }}
                        </h4>
                        <p class="text-[15px] text-[#B9C0D4] leading-[26px]">
                            {{ tt(item.description) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mục 04: Cùng hoàn thiện trước khi gia công (Figma Node 2367:2253) -->
        <section id="section-04" class="scroll-mt-32 space-y-4">
            <h2 class="text-2xl sm:text-[28px] font-bold text-[#FAFAFA] leading-tight sm:leading-[36px]">
                {{ tt('Cùng hoàn thiện trước khi gia công') }}
            </h2>
            <p class="text-base text-[#B9C0D4] leading-[28px]">
                {{ tt('Kết quả của giai đoạn thiết kế là phương án mô hình 3D cùng các chi tiết đã thống nhất: hình dáng, tỉ lệ, kích thước và những yêu cầu liên quan đến cách sử dụng. Đây là cơ sở để trao đổi bước gia công tiếp theo, không phải sản phẩm vật lý hoàn thiện.') }}
            </p>

            <!-- Callout Lưu Ý Về Mô Hình (Figma Node 2367:2256 - viền trái #2970FF 2px, padding-left 20px) -->
            <div class="border-l-2 border-[#2970FF] pl-5 py-2">
                <p class="text-base text-[#B2CCFF] leading-[28px]">
                    {{ tt('Một mô hình đẹp trên màn hình vẫn cần được xem xét với vật liệu và cách chế tác thực tế. Hãy thống nhất các yêu cầu quan trọng trước khi chuyển sang gia công.') }}
                </p>
            </div>
        </section>

        <!-- Dynamic CMS Content (nếu backend có nội dung nhập thêm từ admin) -->
        <section v-if="hasDynamicContent" class="pt-6 border-t border-[#404968]">
            <h3 class="text-xl font-bold text-[#FAFAFA] mb-4">
                {{ tt('Thông tin chi tiết bổ sung') }}
            </h3>
            <div
                class="prose prose-invert max-w-none text-[#B9C0D4] leading-relaxed"
                v-html="dynamicHtml"
            ></div>
        </section>
    </article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    service: {
        type: Object,
        default: () => ({}),
    },
    steps: {
        type: Array,
        default: () => [],
    },
    checklistItems: {
        type: Array,
        default: () => [],
    },
    dynamicHtml: {
        type: String,
        default: '',
    },
})

const hasDynamicContent = computed(() => {
    return !!props.dynamicHtml && props.dynamicHtml.trim().length > 0
})

const fallbackSteps = [
    {
        number: '01',
        title: 'Trao đổi ý tưởng và mục đích sử dụng',
        description:
            'Bạn chia sẻ sản phẩm muốn làm, hình ảnh tham khảo và điều quan trọng nhất với mình. HH Maker cùng bạn xác định công năng, kích thước dự kiến và những chi tiết cần ưu tiên.',
    },
    {
        number: '02',
        title: 'Dựng hình khối và xác định tỉ lệ',
        description:
            'Từ thông tin đã trao đổi, ý tưởng được chuyển thành hình khối 3D. Ở bước này, hãy tập trung vào dáng tổng thể, tương quan kích thước và vị trí các bộ phận trước khi đi sâu vào chi tiết.',
    },
    {
        number: '03',
        title: 'Hoàn thiện cấu trúc và chi tiết',
        description:
            'Cùng xem lại độ dày, bề mặt, vị trí lắp ghép hoặc phần chữ cần thể hiện. Những chi tiết nhỏ cần được cân nhắc với kích thước thực tế và hướng gia công, thay vì chỉ đẹp trên màn hình.',
    },
    {
        number: '04',
        title: 'Thống nhất phương án trước khi gia công',
        description:
            'Hai bên rà soát mô hình, kích thước và các chi tiết đã điều chỉnh. Nếu chuyển sang gia công, vật liệu, bề mặt và yêu cầu lắp ghép cần được trao đổi thêm để chọn cách thực hiện phù hợp.',
    },
]

const fallbackChecklistItems = [
    {
        title: 'Ý tưởng & mục đích sử dụng',
        description:
            'Món đồ dùng để làm gì, dành cho ai? Có yêu cầu về cầm nắm, trưng bày hay lắp vào một bộ phận khác không?',
    },
    {
        title: 'Ảnh tham khảo hoặc phác thảo',
        description:
            'Gửi các góc nhìn bạn có và đánh dấu chi tiết muốn giữ hoặc thay đổi. Một bản vẽ tay có ghi chú cũng là điểm bắt đầu hữu ích.',
    },
    {
        title: 'Kích thước dự kiến',
        description:
            'Chiều dài, rộng, cao và đơn vị đo. Nếu cần lắp ghép, bổ sung kích thước hoặc ảnh của phần tiếp xúc liên quan.',
    },
    {
        title: 'Chi tiết cần ưu tiên',
        description:
            'Ghi rõ phần chữ, hình dáng, bề mặt hoặc giới hạn không thể thay đổi để cùng cân nhắc ngay từ đầu.',
    },
]

const steps = computed(() => {
    if (Array.isArray(props.steps) && props.steps.length) return props.steps
    if (Array.isArray(props.service?.steps) && props.service.steps.length) return props.service.steps
    return fallbackSteps
})

const checklistItems = computed(() => {
    if (Array.isArray(props.checklistItems) && props.checklistItems.length) return props.checklistItems
    if (Array.isArray(props.service?.checklist_items) && props.service.checklist_items.length) return props.service.checklist_items
    if (Array.isArray(props.service?.checklistItems) && props.service.checklistItems.length) return props.service.checklistItems
    return fallbackChecklistItems
})
</script>

<style scoped>
:deep(.prose) {
    color: #b9c0d4;
}
:deep(.prose h2),
:deep(.prose h3),
:deep(.prose h4) {
    color: #fafafa;
    font-weight: 600;
}
:deep(.prose a) {
    color: #84adff;
    text-decoration: underline;
}
:deep(.prose strong) {
    color: #ffffff;
}
:deep(.prose ul) {
    list-style-type: disc;
    padding-left: 1.25rem;
}
:deep(.prose ol) {
    list-style-type: decimal;
    padding-left: 1.25rem;
}
</style>
