<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
            <div class="card">
                <div class="card-header font-bold text-slate-800">
                    {{ tt('models.setting.general_information') }}
                </div>
                <div class="card-body space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.title"
                            :field="{
                                type: 'text',
                                name: 'title',
                                label: 'Tên dịch vụ',
                                placeholder: 'VD: Phụ tùng Thang cuốn / Xích tải Công nghiệp / Quạt BLDC',
                            }"
                        />
                        <Field
                            v-model="form.slug"
                            :field="{
                                type: 'text',
                                name: 'slug',
                                label: 'Slug đường dẫn',
                                placeholder: 'VD: phu-tung-thang-cuon',
                            }"
                        />
                    </div>
                    <Field
                        v-model="form.title_content"
                        :field="{
                            type: 'text',
                            name: 'title_content',
                            label: 'Tiêu đề phụ / Headline dịch vụ',
                            placeholder: 'VD: Vận hành bền bỉ chuẩn kỹ thuật công nghiệp.',
                        }"
                    />
                    <Field
                        v-model="form.description"
                        :field="{
                            type: 'textarea',
                            name: 'description',
                            label: 'Mô tả ngắn dịch vụ',
                            rows: 3,
                            placeholder: 'Tóm tắt quy mô, năng lực gia công và ứng dụng thực tế...',
                        }"
                    />
                    <Field
                        v-model="form.benefit_title"
                        :field="{
                            type: 'text',
                            name: 'benefit_title',
                            label: 'Tiêu đề phần chuẩn bị thông tin (Section 03)',
                            placeholder: 'VD: Bạn cần chuẩn bị gì?',
                        }"
                    />
                    <Field
                        v-model="form.content"
                        :field="{
                            type: 'richtext',
                            name: 'content',
                            label: 'Nội dung chi tiết dịch vụ',
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
                        v-model="form.position"
                        :field="{
                            type: 'number',
                            name: 'position',
                            label: 'Thứ tự sắp xếp',
                        }"
                    />
                    <Field
                        v-model="form.image"
                        :field="{
                            type: 'file_upload',
                            name: 'image',
                            label: 'Icon / Hình đại diện dịch vụ',
                            multiple: false,
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
                title_content: this.item.title_content ?? '',
                benefit_title: this.item.benefit_title ?? '',
                status: this.item.status ?? 'ACTIVE',
                position: this.item.position ?? 0,
            },
        }
    },
    watch: {
        item() {
            this.formData = {
                ...this.item,
                title_content: this.item.title_content ?? '',
                benefit_title: this.item.benefit_title ?? '',
                status: this.item.status ?? 'ACTIVE',
                position: this.item.position ?? 0,
            }
        },
    },
}
</script>
