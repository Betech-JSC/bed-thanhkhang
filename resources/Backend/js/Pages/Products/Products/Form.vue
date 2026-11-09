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
                            placeholder: 'VD: Mô hình Cáo Low-Poly Trang Trí',
                        }"
                    />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.sku"
                            :field="{
                                type: 'text',
                                name: 'sku',
                                label: 'Mã SKU / Mã sản phẩm',
                                placeholder: 'VD: HHM-LP-FOX-01',
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

            <!-- 2. Thông số kỹ thuật xưởng in 3D & Bảng thông số in tham khảo (7 dòng) -->
            <div class="card">
                <div class="card-header font-bold text-slate-800 flex items-center justify-between">
                    <span>Thông số kỹ thuật in 3D &amp; Bảng thông số tham khảo</span>
                    <span class="text-xs text-blue-600 font-semibold">Chuẩn xưởng chế tác HH Maker (7 thông số)</span>
                </div>
                <div class="card-body space-y-4">
                    <!-- Dòng 1, 2 & Lead time -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Field
                            v-model="form.print_technology"
                            :field="{
                                type: 'select_single',
                                name: 'print_technology',
                                label: '1. Công nghệ in',
                                options: printTechnologyOptions,
                            }"
                        />
                        <Field
                            v-model="form.material"
                            :field="{
                                type: 'select_single',
                                name: 'material',
                                label: '2. Vật liệu in',
                                options: materialOptions,
                            }"
                        />
                        <Field
                            v-model="form.lead_time"
                            :field="{
                                type: 'text',
                                name: 'lead_time',
                                label: 'Thời gian hoàn thiện (Lead time)',
                                placeholder: 'VD: Hoàn thiện trong 24-48h',
                            }"
                        />
                    </div>

                    <!-- Dòng 3, 4, 5 của bảng Thông số in tham khảo -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Field
                            v-model="form.sample_size"
                            :field="{
                                type: 'text',
                                name: 'sample_size',
                                label: '3. Kích thước mẫu',
                                placeholder: 'VD: 120 x 85 x 140 mm',
                            }"
                        />
                        <Field
                            v-model="form.layer_height"
                            :field="{
                                type: 'text',
                                name: 'layer_height',
                                label: '4. Chiều cao lớp in',
                                placeholder: 'VD: 0.16 mm',
                            }"
                        />
                        <Field
                            v-model="form.nozzle_size"
                            :field="{
                                type: 'text',
                                name: 'nozzle_size',
                                label: '5. Đầu phun',
                                placeholder: 'VD: 0.40 mm Hardened Steel',
                            }"
                        />
                    </div>

                    <!-- Dòng 6, 7 & Màu trong ảnh / Tuỳ chỉnh -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.infill"
                            :field="{
                                type: 'text',
                                name: 'infill',
                                label: '6. Mật độ ruột (Infill)',
                                placeholder: 'VD: 20% Gyroid',
                            }"
                        />
                        <Field
                            v-model="form.support_material"
                            :field="{
                                type: 'text',
                                name: 'support_material',
                                label: '7. Vật liệu hỗ trợ (Support)',
                                placeholder: 'VD: Không cần (Zero-support)',
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
                                placeholder: 'VD: Nhỏ gọn. Nhiều điều để khám phá.',
                            }"
                        />
                        <Field
                            v-model="form.pdp_metadata.story_intro"
                            :field="{
                                type: 'textarea',
                                name: 'story_intro',
                                label: 'Đoạn mở đầu câu chuyện (Tab Tổng quan)',
                                rows: 2,
                                placeholder: 'Mô tả câu chuyện thiết kế và cảm hứng của mẫu in...',
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
                                placeholder: 'VD: Tạo hình Low-Poly sắc nét',
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
    { id: 'FDM High-Precision', label: 'FDM High-Precision (In sợi độ chính xác cao)' },
    { id: 'FDM Print-in-Place', label: 'FDM Print-in-Place (In khớp nối liền khối)' },
    { id: 'FDM Multi-Color (AMS)', label: 'FDM Multi-Color (AMS Đa sắc)' },
    { id: 'SLA Resin 8K', label: 'SLA / MSLA (Quang hóa Resin 8K)' },
    { id: 'SLS Nylon', label: 'SLS Nylon (Thiêu kết bột laser)' },
    { id: 'FDM', label: 'FDM (Sợi đùn bồi đắp)' },
    { id: 'SLA', label: 'SLA (Quang hóa Resin)' },
    { id: 'SLS', label: 'SLS (Thiêu kết bột)' },
    { id: 'Custom', label: 'Gia công theo yêu cầu' },
]

const DEFAULT_MATERIAL_OPTIONS = [
    { id: 'Nhựa PLA Matte Cao Cấp', label: 'Nhựa PLA Matte Cao Cấp' },
    { id: 'PLA+', label: 'PLA+ / Tough PLA' },
    { id: 'PLA+ Tough', label: 'PLA+ Tough Chịu Lực' },
    { id: 'PLA+ / PETG', label: 'PLA+ / PETG' },
    { id: 'PLA+ Silk / Matte', label: 'PLA+ Silk / Matte' },
    { id: 'PLA Silk Dual-Color', label: 'PLA Silk Dual-Color (Đổi màu 2 ánh)' },
    { id: 'PLA Matte', label: 'PLA Matte Nhám Mịn' },
    { id: 'PLA Matte / Silk', label: 'PLA Matte / Silk' },
    { id: 'PLA Silk / Matte', label: 'PLA Silk / Matte' },
    { id: 'PLA Matte / TPU', label: 'PLA Matte / TPU' },
    { id: 'PLA+ Tough / Wood PLA', label: 'PLA+ Tough / Wood PLA' },
    { id: 'PLA+ Multi-Color / Resin', label: 'PLA+ Multi-Color / Resin' },
    { id: 'PETG', label: 'PETG Kháng Chịu Lực' },
    { id: 'PETG Tough', label: 'PETG Tough Kháng Gãy' },
    { id: 'PETG Waterproof', label: 'PETG Waterproof Chống Thấm' },
    { id: 'PETG / Carbon Fiber', label: 'PETG / Carbon Fiber' },
    { id: 'Resin 8K', label: 'Resin 8K Chi Tiết Cao' },
    { id: 'Resin 8K Matte Grey', label: 'Resin 8K Matte Grey' },
    { id: 'Carbon Fiber', label: 'Carbon Fiber Cường Lực' },
    { id: 'ABS', label: 'ABS Kỹ Thuật' },
    { id: 'TPU', label: 'TPU Nhựa Dẻo Đàn Hồi' },
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
                print_technology: item.print_technology ?? specs[0]?.value ?? 'FDM High-Precision',
                material: item.material ?? specs[1]?.value ?? 'Nhựa PLA Matte Cao Cấp',
                lead_time: item.lead_time ?? 'Hoàn thiện trong 24-48h',
                sample_size: item.sample_size ?? specs[2]?.value ?? '120 x 85 x 140 mm',
                layer_height: item.layer_height ?? specs[3]?.value ?? '0.16 mm',
                nozzle_size: item.nozzle_size ?? specs[4]?.value ?? '0.40 mm Hardened Steel',
                infill: item.infill ?? specs[5]?.value ?? '20% Gyroid',
                support_material: item.support_material ?? specs[6]?.value ?? 'Không cần (Zero-support)',
                image_color_label: item.image_color_label ?? meta.image_color_label ?? 'Cam',
                customization_label: item.customization_label ?? meta.customization_label ?? 'Màu sắc · Kích thước · Số lượng',
                pdp_metadata: {
                    story_title: meta.story_title ?? 'Nhỏ gọn. Nhiều điều để khám phá.',
                    story_intro: meta.story_intro ?? item.description ?? '',
                    feature1_title: meta.feature1_title ?? 'Tạo hình Low-Poly sắc nét',
                    feature1_desc:
                        meta.feature1_desc ??
                        'Các góc cạnh đa giác được hoàn thiện mượt mà, phản chiếu ánh sáng độc đáo ở từng góc nhìn, thể hiện rõ thế mạnh độ chính xác của công nghệ in 3D FDM.',
                    feature2_title: meta.feature2_title ?? 'Tùy chỉnh theo yêu cầu',
                    feature2_desc:
                        meta.feature2_desc ??
                        'Bạn có thể trao đổi với HH Maker để thay đổi màu sắc, điều chỉnh kích thước (tỉ lệ scale) hoặc đặt số lượng lớn làm quà tặng, decor sự kiện.',
                },
                images: item.images ?? [],
                status: item.status ?? 'ACTIVE',
                view_count: item.view_count ?? 0,
            }
        },
    },
}
</script>
