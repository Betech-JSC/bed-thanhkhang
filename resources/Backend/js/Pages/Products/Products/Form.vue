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
                            placeholder: 'VD: Mô hình Iron Man Mark 85 - Resin 8K',
                        }"
                    />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.sku"
                            :field="{
                                type: 'text',
                                name: 'sku',
                                label: 'Mã SKU / Mã sản phẩm',
                                placeholder: 'VD: HH-PRD-001',
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

            <!-- 2. Giá & Thông số kỹ thuật xưởng in 3D -->
            <div class="card">
                <div class="card-header font-bold text-slate-800 flex items-center justify-between">
                    <span>Giá &amp; Thông số kỹ thuật in 3D</span>
                    <span class="text-xs text-blue-600 font-semibold">Chuẩn xưởng chế tác HH Maker</span>
                </div>
                <div class="card-body">
                    <!-- Giá bán & Giá niêm yết -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <Field
                            v-model="form.price"
                            :field="{
                                type: 'number',
                                name: 'price',
                                label: 'Giá bán / Giá tham khảo (VNĐ)',
                                placeholder: 'VD: 250000',
                            }"
                        />
                        <Field
                            v-model="form.compare_at_price"
                            :field="{
                                type: 'number',
                                name: 'compare_at_price',
                                label: 'Giá niêm yết / Giá gốc chưa giảm (VNĐ)',
                                placeholder: 'VD: 320000',
                            }"
                        />
                    </div>

                    <!-- 3 Thông số kỹ thuật cốt lõi in 3D -->
                    <div class="pt-3 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Field
                            v-model="form.print_technology"
                            :field="{
                                type: 'select',
                                name: 'print_technology',
                                label: 'Công nghệ in',
                                options: [
                                    { id: 'FDM', label: 'FDM (Sợi đùn bồi đắp)' },
                                    { id: 'SLA', label: 'SLA / MSLA (Quang hóa Resin 8K)' },
                                    { id: 'SLS', label: 'SLS (Thiêu kết bột laser)' },
                                    { id: 'Custom', label: 'Gia công theo yêu cầu' },
                                ],
                            }"
                        />
                        <Field
                            v-model="form.material"
                            :field="{
                                type: 'select',
                                name: 'material',
                                label: 'Vật liệu in',
                                options: [
                                    { id: 'PLA+', label: 'PLA+ / Tough PLA' },
                                    { id: 'Resin 8K', label: 'Resin 8K Chi Tiết Cao' },
                                    { id: 'PETG', label: 'PETG Kháng Chịu Lực' },
                                    { id: 'Carbon Fiber', label: 'Carbon Fiber Cường Lực' },
                                    { id: 'ABS', label: 'ABS Kỹ Thuật' },
                                    { id: 'TPU', label: 'TPU Nhựa Dẻo Đàn Hồi' },
                                ],
                            }"
                        />
                        <Field
                            v-model="form.lead_time"
                            :field="{
                                type: 'text',
                                name: 'lead_time',
                                label: 'Lead time gia công',
                                placeholder: 'VD: 1-2 ngày, 24h...',
                            }"
                        />
                    </div>
                </div>
            </div>

            <!-- 3. Mô tả & Nội dung -->
            <div class="card">
                <div class="card-header font-bold text-slate-800">Mô tả &amp; Chi tiết sản phẩm</div>
                <div class="card-body">
                    <Field
                        v-model="form.description"
                        :field="{
                            type: 'textarea',
                            name: 'description',
                            label: 'Mô tả ngắn',
                            rows: 3,
                            placeholder: 'Tóm tắt đặc điểm nổi bật, ứng dụng của sản phẩm...',
                        }"
                    />
                    <Field
                        v-model="form.content"
                        :field="{
                            type: 'richtext',
                            name: 'content',
                            label: 'Bài viết chi tiết sản phẩm',
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
export default {
    props: ['item', 'schema'],
    data() {
        return {
            formData: {
                ...this.item,
                categories: this.item.categories ?? [],
                price: this.item.price ?? null,
                compare_at_price: this.item.compare_at_price ?? null,
                print_technology: this.item.print_technology ?? 'FDM',
                material: this.item.material ?? 'PLA+',
                lead_time: this.item.lead_time ?? '1-2 ngày',
                images: this.item.images ?? [],
                status: this.item.status ?? 'ACTIVE',
                view_count: this.item.view_count ?? 0,
            },
        }
    },

    watch: {
        item() {
            this.formData = {
                ...this.item,
                categories: this.item.categories ?? [],
                price: this.item.price ?? null,
                compare_at_price: this.item.compare_at_price ?? null,
                print_technology: this.item.print_technology ?? 'FDM',
                material: this.item.material ?? 'PLA+',
                lead_time: this.item.lead_time ?? '1-2 ngày',
                images: this.item.images ?? [],
                status: this.item.status ?? 'ACTIVE',
                view_count: this.item.view_count ?? 0,
            }
        },
    },
}
</script>
