<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
            <!-- 1. Thông tin chung -->
            <div class="card">
                <div class="card-header font-bold text-slate-800">
                    {{ tt('models.setting.general_information') }}
                </div>
                <div class="card-body">
                    <Field
                        v-model="form.title"
                        :field="{
                            type: 'text',
                            name: 'title',
                            label: 'Tên sản phẩm',
                            placeholder: 'VD: Xích thang cuốn bước lớn TL133',
                        }"
                    />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.sku"
                            :field="{
                                type: 'text',
                                name: 'sku',
                                label: 'Mã SKU / Mã sản phẩm',
                                placeholder: 'VD: TK-TC-TL133',
                            }"
                        />
                        <Field
                            v-model="form.categories"
                            :field="{
                                type: 'select_multiple',
                                name: 'categories',
                                label: 'Danh mục sản phẩm',
                                labelBy: 'title',
                                source: {
                                    model: 'App\\Models\\Product\\ProductCategory',
                                    method: 'getCategories',
                                    only: ['id', 'title'],
                                },
                            }"
                        />
                    </div>
                </div>
            </div>

            <!-- 2. Thông số kỹ thuật & Đặc tính cơ khí (B2B Specifications) -->
            <div class="card">
                <div class="card-header font-bold text-slate-800 flex items-center justify-between">
                    <span>Thông số kỹ thuật &amp; Đặc tính cơ khí</span>
                    <span class="text-xs text-blue-600 font-semibold">Tiêu chuẩn kỹ thuật Thành Khang Tecsat</span>
                </div>
                <div class="card-body space-y-4">
                    <!-- Dòng 1, 2 & Lead time -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Field
                            v-model="form.print_technology"
                            :field="{
                                type: 'select_single',
                                name: 'print_technology',
                                label: '1. Tiêu chuẩn / Công nghệ chế tạo',
                                options: printTechnologyOptions,
                            }"
                        />
                        <Field
                            v-model="form.material"
                            :field="{
                                type: 'select_single',
                                name: 'material',
                                label: '2. Vật liệu / Cấu tạo cơ khí',
                                options: materialOptions,
                            }"
                        />
                        <Field
                            v-model="form.lead_time"
                            :field="{
                                type: 'text',
                                name: 'lead_time',
                                label: 'Thời gian cấp hàng (Lead time)',
                                placeholder: 'VD: Sẵn kho / Đặt hàng 3-7 ngày làm việc',
                            }"
                        />
                    </div>

                    <!-- Dòng 3, 4, 5 của bảng Thông số kỹ thuật -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Field
                            v-model="form.sample_size"
                            :field="{
                                type: 'text',
                                name: 'sample_size',
                                label: '3. Kích thước / Bước xích / Đường kính',
                                placeholder: 'VD: Bước xích p=100mm, Bản rộng B=400mm',
                            }"
                        />
                        <Field
                            v-model="form.layer_height"
                            :field="{
                                type: 'text',
                                name: 'layer_height',
                                label: '4. Tải trọng làm việc / Giới hạn đứt',
                                placeholder: 'VD: Tải trọng phá hủy tối thiểu 120 kN',
                            }"
                        />
                        <Field
                            v-model="form.nozzle_size"
                            :field="{
                                type: 'text',
                                name: 'nozzle_size',
                                label: '5. Xử lý bề mặt / Bôi trơn',
                                placeholder: 'VD: Mạ kẽm / Thấm Nitơ / Tra mỡ chịu nhiệt',
                            }"
                        />
                    </div>

                    <!-- Dòng 6, 7 & Tiêu chuẩn -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.infill"
                            :field="{
                                type: 'text',
                                name: 'infill',
                                label: '6. Tiêu chuẩn áp dụng / Độ cứng',
                                placeholder: 'VD: ISO 606 / DIN 8187 / JIS B1801',
                            }"
                        />
                        <Field
                            v-model="form.support_material"
                            :field="{
                                type: 'text',
                                name: 'support_material',
                                label: '7. Chứng chỉ / Kiểm định',
                                placeholder: 'VD: CO/CQ, Test report nhà máy',
                            }"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <Field
                            v-model="form.image_color_label"
                            :field="{
                                type: 'text',
                                name: 'image_color_label',
                                label: 'Màu trong ảnh (Hiển thị trên PDP)',
                                placeholder: 'VD: Cam',
                            }"
                        />
                        <Field
                            v-model="form.customization_label"
                            :field="{
                                type: 'text',
                                name: 'customization_label',
                                label: 'Có thể tùy chỉnh (Hiển thị trên PDP)',
                                placeholder: 'VD: Màu sắc · Kích thước · Số lượng',
                            }"
                        />
                    </div>
                </div>
            </div>

            <!-- 3. Mô tả & Nội dung PDP -->
            <div class="card">
                <div class="card-header font-bold text-slate-800">Mô tả &amp; Nội dung chi tiết sản phẩm (PDP)</div>
                <div class="card-body space-y-4">
                    <Field
                        v-model="form.description"
                        :field="{
                            type: 'textarea',
                            name: 'description',
                            label: 'Mô tả ngắn (Hiển thị đầu trang chi tiết)',
                            rows: 3,
                            placeholder: 'Tóm tắt đặc điểm nổi bật, ứng dụng của sản phẩm...',
                        }"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <Field
                            v-model="form.pdp_metadata.story_title"
                            :field="{
                                type: 'text',
                                name: 'story_title',
                                label: 'Tiêu đề câu chuyện sản phẩm (Tab Tổng quan)',
                                placeholder: 'VD: Độ bền chuẩn công nghiệp cho hệ thống vận hành liên tục.',
                            }"
                        />
                        <Field
                            v-model="form.pdp_metadata.story_intro"
                            :field="{
                                type: 'textarea',
                                name: 'story_intro',
                                label: 'Đoạn mở đầu câu chuyện (Tab Tổng quan)',
                                rows: 2,
                                placeholder: 'Mô tả đặc tính cơ khí, quy trình nhiệt luyện và ứng dụng công nghiệp của sản phẩm...',
                            }"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.pdp_metadata.feature1_title"
                            :field="{
                                type: 'text',
                                name: 'feature1_title',
                                label: 'Tiêu đề điểm nổi bật 1',
                                placeholder: 'VD: Chịu tải cao & chống mài mòn vượt trội',
                            }"
                        />
                        <Field
                            v-model="form.pdp_metadata.feature1_desc"
                            :field="{
                                type: 'textarea',
                                name: 'feature1_desc',
                                label: 'Nội dung điểm nổi bật 1',
                                rows: 2,
                            }"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.pdp_metadata.feature2_title"
                            :field="{
                                type: 'text',
                                name: 'feature2_title',
                                label: 'Tiêu đề điểm nổi bật 2',
                                placeholder: 'VD: Tùy chỉnh theo yêu cầu',
                            }"
                        />
                        <Field
                            v-model="form.pdp_metadata.feature2_desc"
                            :field="{
                                type: 'textarea',
                                name: 'feature2_desc',
                                label: 'Nội dung điểm nổi bật 2',
                                rows: 2,
                            }"
                        />
                    </div>

                    <Field
                        v-model="form.content"
                        :field="{
                            type: 'richtext',
                            name: 'content',
                            label: 'Bài viết chi tiết sản phẩm (Bổ sung)',
                        }"
                    />
                </div>
            </div>
        </template>
        <template #aside="{ form }">
            <div class="card">
                <div class="card-body">
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
                        v-model="form.image"
                        :field="{
                            type: 'file_upload',
                            name: 'image',
                            label: 'Ảnh đại diện chính',
                            multiple: false,
                        }"
                    />
                    <Field
                        v-model="form.images"
                        :field="{
                            type: 'file_upload',
                            name: 'images',
                            label: 'Bộ sưu tập hình ảnh',
                            multiple: true,
                        }"
                    />
                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <Field
                            v-model="form.is_featured"
                            :field="{
                                type: 'checkbox',
                                name: 'is_featured',
                                label: 'Sản phẩm nổi bật',
                            }"
                        />
                        <Field
                            v-model="form.is_new"
                            :field="{
                                type: 'checkbox',
                                name: 'is_new',
                                label: 'Sản phẩm mới',
                            }"
                        />
                    </div>
                    <Field
                        v-model="form.view_count"
                        :disabled="true"
                        :field="{
                            type: 'number',
                            name: 'view_count',
                            label: 'Lượt xem',
                        }"
                    />
                </div>
            </div>
        </template>
    </Form>
</template>
<script>
const DEFAULT_TECH_OPTIONS = [
    { id: 'Gia công CNC chính xác', label: 'Gia công CNC chính xác & Tiện phay' },
    { id: 'Đúc & Nhiệt luyện cao tần', label: 'Đúc & Nhiệt luyện cao tần chịu mỏi' },
    { id: 'Lắp ráp tiêu chuẩn OEM/ODM', label: 'Lắp ráp tiêu chuẩn OEM/ODM công nghiệp' },
    { id: 'Dập nguội & Chốt rỗng chuẩn ISO', label: 'Dập nguội & Chốt rỗng chuẩn ISO/DIN' },
    { id: 'Động cơ BLDC không chổi than', label: 'Công nghệ động cơ BLDC tiết kiệm điện' },
    { id: 'Custom', label: 'Gia công & Chế tạo theo yêu cầu bản vẽ' },
]

const DEFAULT_MATERIAL_OPTIONS = [
    { id: 'Thép hợp kim 40Cr/SCM435', label: 'Thép hợp kim 40Cr / SCM435 (Tôi cao tần)' },
    { id: 'Inox 304 chống rỉ', label: 'Inox 304 / SUS304 (Chống ăn mòn hóa chất)' },
    { id: 'Inox 316', label: 'Inox 316 / SUS316 (Môi trường thực phẩm & biển)' },
    { id: 'Polyurethane (PU) chịu tải', label: 'Polyurethane (PU) chịu tải cao & giảm chấn' },
    { id: 'Nhựa POM kỹ thuật', label: 'Nhựa kỹ thuật POM / Polyacetal (Hệ số ma sát thấp)' },
    { id: 'Nhựa PA66 / Nylon', label: 'Nhựa PA66 / Nylon chịu mài mòn' },
    { id: 'Cao su EPDM / NBR', label: 'Cao su kỹ thuật EPDM / NBR (Chịu nhiệt & dầu)' },
    { id: 'Hợp kim nhôm hàng không', label: 'Hợp kim nhôm hàng không nhẹ & bền' },
    { id: 'Thép Carbon C45', label: 'Thép Carbon C45 cường độ cao' },
]

export default {
    props: ['item', 'schema'],
    data() {
        return {
            formData: this.buildFormData(this.item),
        }
    },

    computed: {
        printTechnologyOptions() {
            const list = [...DEFAULT_TECH_OPTIONS]
            const current = this.formData?.print_technology
            if (current && !list.some((opt) => opt.id === current)) {
                list.unshift({ id: current, label: current })
            }
            return list
        },
        materialOptions() {
            const list = [...DEFAULT_MATERIAL_OPTIONS]
            const current = this.formData?.material
            if (current && !list.some((opt) => opt.id === current)) {
                list.unshift({ id: current, label: current })
            }
            return list
        },
    },

    watch: {
        item(newVal) {
            this.formData = this.buildFormData(newVal)
        },
    },

    methods: {
        buildFormData(item = {}) {
            const specs = Array.isArray(item?.specifications) ? item.specifications : []
            const meta = item?.pdp_metadata && typeof item.pdp_metadata === 'object' ? item.pdp_metadata : {}

            return {
                ...item,
                categories: item.categories ?? [],
                price: item.price ?? null,
                compare_at_price: item.compare_at_price ?? null,
                print_technology: item.print_technology ?? specs[0]?.value ?? 'Gia công CNC chính xác',
                material: item.material ?? specs[1]?.value ?? 'Thép hợp kim 40Cr/SCM435',
                lead_time: item.lead_time ?? 'Sẵn kho / Giao hàng trong 3-7 ngày',
                sample_size: item.sample_size ?? specs[2]?.value ?? 'Tiêu chuẩn catalog hoặc bản vẽ',
                layer_height: item.layer_height ?? specs[3]?.value ?? 'Theo tiêu chuẩn tải trọng công nghiệp',
                nozzle_size: item.nozzle_size ?? specs[4]?.value ?? 'Mạ kẽm / Thấm Nitơ / Tra mỡ chịu nhiệt',
                infill: item.infill ?? specs[5]?.value ?? 'ISO 606 / DIN 8187 / JIS B1801',
                support_material: item.support_material ?? specs[6]?.value ?? 'Đầy đủ CO/CQ, Test report nhà máy',
                image_color_label: item.image_color_label ?? meta.image_color_label ?? 'Tiêu chuẩn nhà máy',
                customization_label: item.customization_label ?? meta.customization_label ?? 'Kích thước · Tải trọng · Bản vẽ OEM',
                pdp_metadata: {
                    story_title: meta.story_title ?? 'Giải pháp cơ khí & phụ tùng công nghiệp bền bỉ.',
                    story_intro: meta.story_intro ?? item.description ?? '',
                    feature1_title: meta.feature1_title ?? 'Độ bền cơ tính vượt trội',
                    feature1_desc:
                        meta.feature1_desc ??
                        'Sản phẩm được gia công cơ khí chính xác, nhiệt luyện cao tần đạt độ cứng tiêu chuẩn, đảm bảo chống mài mòn và chịu tải trọng làm việc khắc nghiệt.',
                    feature2_title: meta.feature2_title ?? 'Tư vấn kỹ thuật & Gia công theo bản vẽ',
                    feature2_desc:
                        meta.feature2_desc ??
                        'Đội ngũ kỹ sư Thành Khang Tecsat sẵn sàng khảo sát thực địa tại nhà máy, tư vấn giải pháp tương thích 100% với dây chuyền hiện hữu.',
                },
                images: item.images ?? [],
                status: item.status ?? 'ACTIVE',
                view_count: item.view_count ?? 0,
            }
        },
    },
}
</script>
