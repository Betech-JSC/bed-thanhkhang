<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
            <div class="card mb-4 lg:mb-0">
                <div class="card-header flex items-center gap-2">
                    <ph-pencil-line-bold class="text-primary text-lg" />
                    Nội dung mẫu email
                </div>
                <div class="card-body p-5">
                    <Field
                        v-model="form.subject"
                        :field="{
                            type: 'text',
                            name: 'subject',
                            label: 'Tiêu đề Email',
                            placeholder: 'Nhập tiêu đề mail (Hỗ trợ chèn biến...)'
                        }"
                    />
                    <Field
                        v-model="form.content"
                        :field="{
                            type: 'richtext',
                            name: 'content',
                            label: 'Nội dung biên soạn',
                        }"
                    />
                    
                    <div class="flex justify-end mt-4">
                        <Button
                            label="Xem trước (Preview)"
                            icon="pi pi-eye"
                            class="p-button-secondary p-button-sm p-button-outlined"
                            @click="showPreview = true"
                        />
                    </div>
                </div>
            </div>

            <!-- Preview Dialog (Kept outside for modal logic) -->
            <Dialog
                header="Xem trước mẫu Email"
                v-model:visible="showPreview"
                :style="{ width: '850px' }"
                :modal="true"
                :draggable="false"
                class="premium-dialog"
            >
                <div class="preview-container bg-slate-50 p-8 border border-slate-100 rounded-2xl shadow-inner min-h-[500px]">
                    <div class="mb-6 bg-white p-4 rounded-xl border border-slate-200/50 shadow-sm">
                        <div class="grid grid-cols-[100px_1fr] gap-2 mb-2">
                            <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Tiêu đề:</span>
                            <span class="text-slate-800 font-semibold">{{ replacedSubject }}</span>
                        </div>
                        <div class="grid grid-cols-[100px_1fr] gap-2">
                            <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Người nhận:</span>
                            <span class="text-slate-600 italic">customer@example.com</span>
                        </div>
                    </div>
                    <div class="email-body-preview prose prose-slate max-w-none bg-white p-8 rounded-xl border border-slate-200/50 shadow-sm min-h-[300px]" v-html="replacedContent"></div>
                </div>
                <template #footer>
                    <Button label="Đóng cửa sổ" icon="pi pi-times" @click="showPreview = false" class="p-button-text text-slate-500 font-bold"/>
                </template>
            </Dialog>
        </template>
        
        <template #aside="{ form }">
            <div class="sticky top-4 space-y-4">
                <!-- Status & Position Card -->
                <div class="card overflow-hidden">
                    <div class="card-body p-4 bg-slate-50/50">
                        <Field
                            v-model="form.type"
                            :field="{
                                type: 'select_single',
                                name: 'type',
                                label: 'Loại liên hệ (Type)',
                                options: [
                                    { label: 'Liên hệ (CONTACT_FORM)', id: 'CONTACT_FORM' },
                                    { label: 'Đơn hàng tài nguyên (TYPE_CHECKOUT_RESOURCE)', id: 'TYPE_CHECKOUT_RESOURCE' },
                                    { label: 'Đơn hàng khóa học (TYPE_CHECKOUT_COURSE)', id: 'TYPE_CHECKOUT_COURSE' },
                                ],
                            }"
                        />
                        <Field
                            v-model="form.status"
                            :field="{
                                type: 'radio_list',
                                name: 'status',
                                label: 'Trạng thái',
                                options: schema.columns.status.list,
                            }"
                        />
                        <Field
                            v-model="form.position"
                            :field="{
                                type: 'number',
                                name: 'position',
                                label: 'Thứ tự sắp xếp',
                            }"
                        />
                    </div>
                </div>

                <!-- Grouped Placeholders Panel -->
                <div class="card overflow-hidden">
                    <div class="card-header bg-primary/5 py-3 px-4 flex items-center gap-2 border-b border-primary/10">
                        <ph-magic-wand-bold class="text-primary" />
                        <span class="font-black text-xs text-primary uppercase tracking-widest">Biến hỗ trợ</span>
                    </div>
                    <div class="card-body p-0">
                        <Accordion :multiple="true" :activeIndex="[0, 1]">
                            <AccordionTab v-for="(group, gIndex) in groupedPlaceholders" :key="gIndex">
                                <template #header>
                                    <div class="flex items-center gap-2 py-1">
                                        <component :is="group.icon" class="text-slate-400" />
                                        <span class="text-xs font-bold text-slate-700 uppercase tracking-tight">{{ group.title }}</span>
                                    </div>
                                </template>
                                <div class="space-y-2 p-2 mx-1">
                                    <div
                                        v-for="variable in group.items"
                                        :key="variable.code"
                                        draggable="true"
                                        @dragstart="onDragStart($event, variable.code)"
                                        @click="insertVariable(variable.code)"
                                        class="variable-card-sm group transition-all duration-200"
                                    >
                                        <div class="flex items-center justify-between pointer-events-none">
                                            <span class="code-sm">{{ variable.code }}</span>
                                            <ph-copy-bold class="text-[10px] opacity-0 group-hover:opacity-100 text-primary transition-opacity" />
                                        </div>
                                        <div class="label-sm pointer-events-none">{{ variable.label }}</div>
                                    </div>
                                </div>
                            </AccordionTab>
                        </Accordion>
                        <div class="p-4 bg-slate-50 border-t border-slate-100">
                            <p class="text-[10px] leading-relaxed text-slate-400 italic">
                                * Click để chèn tại vị trí con trỏ hoặc kéo thả trực tiếp vào nội dung mail.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Form>
</template>
<script>
export default {
    props: ['item', 'schema'],
    data() {
        return {
            formData: {
                ...this.item,
                status: this.item.status ?? 'ACTIVE',
            },
            showPreview: false,
            placeholders: [
                { code: '{{Name}}', label: 'Họ và tên', desc: 'Tên đầy đủ của khách hàng' },
                { code: '{{Email}}', label: 'Email', desc: 'Địa chỉ email người gửi' },
                { code: '{{Phone}}', label: 'Số điện thoại', desc: 'SĐT liên hệ' },
                { code: '{{Note}}', label: 'Lời nhắn', desc: 'Nội dung ghi chú khách để lại' },
                { code: '{{OrderID}}', label: 'Mã đơn hàng', desc: 'Mã số định danh (VD: #123)' },
                { code: '{{CreatedAt}}', label: 'Ngày đăng ký', desc: 'Thời gian gửi form (H:i d/m/Y)' },
                { code: '{{Facebook}}', label: 'Link Facebook', desc: 'Đường dẫn trang cá nhân FB' },
                { code: '{{Status}}', label: 'Đối tượng', desc: 'SV hoặc Người đi làm' },
                { code: '{{School}}', label: 'Cơ sở đào tạo', desc: 'Trường đã/đang học' },
                { code: '{{CourseTitle}}', label: 'Tên khóa học', desc: 'Tên khóa học đã chọn' },
                { code: '{{CourseLink}}', label: 'Link khóa học', desc: 'Đường dẫn xem khóa học trên web' },
                { code: '{{CourseOriginalPrice}}', label: 'Giá gốc', desc: 'Học phí chưa giảm' },
                { code: '{{TotalSavings}}', label: 'Tổng tiền giảm', desc: 'Tổng tất cả số tiền đã được trừ' },
                { code: '{{ScheduleDiscountPercent}}', label: '% Giảm lịch', desc: 'Tỷ lệ % giảm theo lịch học' },
                { code: '{{ScheduleDiscountAmount}}', label: 'Tiền giảm lịch', desc: 'Số tiền trừ được từ lịch học' },
                { code: '{{GroupDiscountPercent}}', label: '% Giảm nhóm', desc: 'Tỷ lệ % giảm khi đk theo nhóm' },
                { code: '{{GroupDiscountAmount}}', label: 'Tiền giảm nhóm', desc: 'Số tiền trừ được từ ưu đãi nhóm' },
                { code: '{{GroupName}}', label: 'Tên nhóm', desc: 'Tên gói ưu đãi nhóm (VD: Nhóm 2-3 người)' },
                { code: '{{QuantityDiscountPercent}}', label: '% Giảm S.Lượng', desc: 'Tỷ lệ % giảm khi đk nhiều khóa' },
                { code: '{{QuantityDiscountAmount}}', label: 'Tiền giảm S.Lượng', desc: 'Số tiền trừ được từ ưu đãi số lượng' },
                { code: '{{QuantityName}}', label: 'Mức S.Lượng', desc: 'Tên mức số lượng (VD: Đăng ký 2 khóa)' },
                { code: '{{OldStudentAmount}}', label: 'Ưu đãi H.Viên Cũ', desc: 'Số tiền giảm cố định (150k)' },
                { code: '{{FinalPrice}}', label: 'Tổng thanh toán', desc: 'Số tiền cuối cùng cần đóng' },
                { code: '{{VoucherCode}}', label: 'Mã Voucher', desc: 'Mã khuyến mãi đã áp dụng' },
                { code: '{{CourseInfo}}', label: 'Bảng Đơn Hàng', desc: 'Bảng chi tiết học phí (dạng HTML)' },
            ],
            mockData: {
                Name: 'Nguyễn Văn A',
                Email: 'customer@example.com',
                Phone: '0987654321',
                Note: 'Tôi cần tư vấn thêm về dịch vụ này.',
                OrderID: '#1234',
                CreatedAt: '15:30 28/02/2026',
                Facebook: 'https://facebook.com/nguyenvana',
                Status: 'Sinh viên năm 3',
                School: 'Đại học Kiến trúc Hà Nội',
                CourseTitle: 'Thiết kế đồ họa chuyên sâu',
                CourseLink: 'https://example.com/courses/graphic-design',
                CourseOriginalPrice: '2.500.000 ₫',
                TotalSavings: '525.000 ₫',
                ScheduleDiscountPercent: '10%',
                ScheduleDiscountAmount: '250.000 ₫',
                GroupDiscountPercent: '5%',
                GroupDiscountAmount: '125.000 ₫',
                GroupName: 'Nhóm 2-3 người',
                QuantityDiscountPercent: '0%',
                QuantityDiscountAmount: '0 ₫',
                QuantityName: 'Học 1 khóa',
                OldStudentAmount: '150.000 ₫',
                FinalPrice: '1.975.000 ₫',
                VoucherCode: 'FUNKY2024',
                CourseInfo: `<div style="padding:10px;border:1px solid #ddd;border-radius:4px"><strong>Khóa học:</strong> Thiết kế đồ họa chuyên sâu<br><strong>Tổng:</strong> 1.975.000 ₫</div>`,
                Service: 'Dịch vụ Tư vấn Thiết kế',
                Job: 'Lập trình viên Senior',
            },
        }
    },
    computed: {
        replacedSubject() {
            return this.replacePlaceholders(this.formData.subject || '')
        },
        replacedContent() {
            return this.replacePlaceholders(this.formData.content || '')
        },
        groupedPlaceholders() {
            const common = this.placeholders.filter(p => ['{{Name}}', '{{Email}}', '{{Phone}}', '{{Note}}'].includes(p.code))
            const course = this.placeholders.filter(p => ['{{CourseTitle}}', '{{Facebook}}', '{{Status}}', '{{School}}'].includes(p.code))
            const pricing = this.placeholders.filter(p => ['{{CourseOriginalPrice}}', '{{FinalPrice}}', '{{VoucherCode}}', '{{CourseInfo}}'].includes(p.code))
            const discounts = this.placeholders.filter(p => p.code.includes('Discount') || p.code.includes('OldStudent') || p.code.includes('Name'))

            return [
                { title: 'Thông tin cơ bản', icon: 'ph-user-bold', items: common },
                { title: 'Thông tin khóa học', icon: 'ph-book-open-bold', items: course },
                { title: 'Thanh toán & Voucher', icon: 'ph-credit-card-bold', items: pricing },
                { title: 'Chi tiết ưu đãi', icon: 'ph-tag-bold', items: discounts },
                { title: 'Khác', icon: 'ph-dots-three-outline-bold', items: this.placeholders.filter(p => !common.includes(p) && !course.includes(p) && !pricing.includes(p) && !discounts.includes(p)) }
            ].filter(g => g.items.length > 0)
        }
    },
    watch: {
        item() {
            this.formData = { ...this.item }
        },
    },
    methods: {
        replacePlaceholders(text) {
            let result = text
            Object.keys(this.mockData).forEach((key) => {
                const regex = new RegExp(`{{${key}}}`, 'g')
                result = result.replace(regex, `<span class="bg-yellow-100 px-1 rounded text-primary font-semibold">${this.mockData[key]}</span>`)
            })
            return result
        },
        onDragStart(event, code) {
            event.dataTransfer.setData('text/plain', code)
        },
        insertVariable(code) {
            if (window.tinymce && window.tinymce.activeEditor) {
                window.tinymce.activeEditor.insertContent(code)
                this.formData.content = window.tinymce.activeEditor.getContent()
            } else {
                this.formData.content += code
            }
        },
    },
}
</script>
<style lang="scss" scoped>
.email-body-preview :deep(img) {
    max-width: 100%;
}

.variable-card-sm {
    @apply p-2 bg-white border border-slate-200 rounded-lg cursor-move transition-all duration-200;
    border-style: dashed;

    &:hover {
        @apply bg-blue-50 border-blue-400 border-solid shadow-sm -translate-y-0.5;
    }

    .code-sm {
        @apply text-[10px] font-black text-slate-700 font-mono tracking-tight;
    }

    .label-sm {
        @apply text-[9px] font-bold text-slate-500 mt-0.5 uppercase tracking-wide;
    }
}

.premium-dialog {
    :deep(.p-dialog-header) {
        @apply border-b border-slate-100 bg-white;
    }
    :deep(.p-dialog-footer) {
        @apply border-t border-slate-100 bg-slate-50/50 p-3;
    }
}
</style>
