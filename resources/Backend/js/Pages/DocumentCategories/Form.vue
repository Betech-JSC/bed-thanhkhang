<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
            <div class="card">
                <div class="card-header">{{ tt('models.setting.general_information') }}</div>
                <div class="card-body space-y-4">
                    <Field
                        v-model="form.title"
                        :field="{
                            rules: 'required',
                            type: 'text',
                            name: 'title',
                            label: 'Tên danh mục tài liệu',
                        }"
                    />
                </div>
            </div>
            <SeoFields :modelValue="form" @update:modelValue="form = $event" />
        </template>
        <template #aside="{ form }">
            <div class="card">
                <div class="card-body space-y-4">
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
                            label: 'Thứ tự hiển thị',
                        }"
                    />
                    <Field
                        v-model="form.image"
                        :field="{
                            type: 'file_upload',
                            name: 'image',
                            label: 'Ảnh đại diện',
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
                type: 'DOCUMENT',
                status: this.item.status ?? 'ACTIVE',
                position: this.item.position ?? 0,
            },
        }
    },
    watch: {
        item() {
            this.formData = {
                ...this.item,
                type: 'DOCUMENT',
                status: this.item.status ?? 'ACTIVE',
                position: this.item.position ?? 0,
            }
        },
    },
}
</script>
