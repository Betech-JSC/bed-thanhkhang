<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
            <div class="card">
                <div class="card-header font-bold text-slate-800">
                    {{ tt('models.setting.general_information') }}
                </div>
                <div class="card-body">
                    <Field v-model="form.title" :field="{
                        type: 'text',
                        name: 'title',
                        label: 'Tiêu đề chính',
                        size: 'sm',
                        placeholder: 'VD: GIẢI PHÁP THIẾT BỊ CÔNG NGHIỆP & PHỤ TÙNG THANG CUỐN CHUẨN OEM',
                    }" />
                    <Field v-model="form.subtitle" :field="{
                        type: 'text',
                        name: 'subtitle',
                        label: 'Phụ đề (Subtitle / Tagline)',
                        placeholder: 'VD: VỀ CHÚNG TÔI / GIẢI PHÁP TRUYỀN ĐỘNG & TIẾT KIỆM ĐIỆN',
                    }" />
                    <Field v-model="form.description" :field="{
                        type: 'textarea',
                        name: 'description',
                        label: 'Mô tả ngắn',
                        rows: 3,
                        placeholder: 'Cung cấp linh kiện thang cuốn, xích tải công nghiệp và giải pháp tiết kiệm điện hàng đầu...',
                    }" />

                    <!-- Cụm CTA Buttons -->
                    <div class="pt-3 mt-3 border-t border-slate-100">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">
                            Nút kêu gọi hành động (Call To Action - CTA)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <Field v-model="form.primary_cta_text" :field="{
                                type: 'text',
                                name: 'primary_cta_text',
                                label: 'Nút chính - Tiêu đề',
                                placeholder: 'VD: Khám phá ngay',
                            }" />
                            <Field v-model="form.primary_cta_link" :field="{
                                type: 'text',
                                name: 'primary_cta_link',
                                label: 'Nút chính - Đường dẫn',
                                placeholder: 'VD: #featured-carousel hoặc /san-pham',
                            }" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field v-model="form.secondary_cta_text" :field="{
                                type: 'text',
                                name: 'secondary_cta_text',
                                label: 'Nút phụ - Tiêu đề',
                                placeholder: 'VD: Nhận tư vấn thiết kế',
                            }" />
                            <Field v-model="form.secondary_cta_link" :field="{
                                type: 'text',
                                name: 'secondary_cta_link',
                                label: 'Nút phụ - Đường dẫn',
                                placeholder: 'VD: #contact-section hoặc /lien-he',
                            }" />
                        </div>
                    </div>

                    <div class="pt-3 mt-3 border-t border-slate-100">
                        <Field v-model="form.image" :field="{
                            type: 'file_upload',
                            name: 'image',
                            label: 'Hình desktop (Khuyến nghị: 1280x620px)',
                        }" />
                        <Field v-model="form.image_mobile" :field="{
                            type: 'file_upload',
                            name: 'image_mobile',
                            label: 'Hình mobile',
                        }" />
                        <Field v-model="form.is_video" :field="{
                            type: 'checkbox',
                            name: 'is_video',
                            label: 'Sử dụng Video thay cho hình ảnh',
                        }" />
                        <Field v-if="form.is_video" v-model="form.link" :field="{
                            type: 'text',
                            name: 'link',
                            label: 'Đường dẫn Video (YouTube / MP4)',
                        }" />
                    </div>
                </div>
            </div>
        </template>
        <template #aside="{ form }">
            <div class="card">
                <div class="card-body">
                    <Field v-model="form.status" :field="{
                        type: 'radio_list',
                        name: 'status',
                        label: 'Trạng thái',
                        options: schema.columns.status.list,
                    }" />
                    <Field v-model="form.position_display" :field="{
                        type: 'radio_list',
                        name: 'position_display',
                        label: 'Vị trí hiển thị',
                        options: position_display,
                    }" />
                </div>
            </div>
        </template>
    </Form>
</template>
<script>
export default {
    props: ['item', 'schema', 'data'],
    data() {
        return {
            formData: {
                ...this.item,
                target: this.item.target ?? '_self',
                subtitle: this.item.subtitle ?? this.item.custom_fields?.subtitle ?? '',
                primary_cta_text: this.item.primary_cta_text ?? this.item.custom_fields?.primary_cta_text ?? '',
                primary_cta_link: this.item.primary_cta_link ?? this.item.custom_fields?.primary_cta_link ?? '',
                secondary_cta_text: this.item.secondary_cta_text ?? this.item.custom_fields?.secondary_cta_text ?? '',
                secondary_cta_link: this.item.secondary_cta_link ?? this.item.custom_fields?.secondary_cta_link ?? '',
            },
            position_display: this.data.slider.position_display,
        }
    },

    watch: {
        item() {
            this.formData = {
                ...this.item,
                target: this.item.target ?? '_self',
                subtitle: this.item.subtitle ?? this.item.custom_fields?.subtitle ?? '',
                primary_cta_text: this.item.primary_cta_text ?? this.item.custom_fields?.primary_cta_text ?? '',
                primary_cta_link: this.item.primary_cta_link ?? this.item.custom_fields?.primary_cta_link ?? '',
                secondary_cta_text: this.item.secondary_cta_text ?? this.item.custom_fields?.secondary_cta_text ?? '',
                secondary_cta_link: this.item.secondary_cta_link ?? this.item.custom_fields?.secondary_cta_link ?? '',
            }
        },
    },
}
</script>
