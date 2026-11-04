<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
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
                            label: 'Tên dịch vụ',
                            placeholder: 'VD: Gia công in 3D FDM & SLA chính xác cao',
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
                            label: 'Icon 3D / Hình đại diện dịch vụ',
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
                status: this.item.status ?? 'ACTIVE',
                position: this.item.position ?? 0,
            },
        }
    },
    watch: {
        item() {
            this.formData = {
                ...this.item,
                status: this.item.status ?? 'ACTIVE',
                position: this.item.position ?? 0,
            }
        },
    },
}
</script>
