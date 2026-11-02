<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
            <div class="card">
                <div class="card-header">{{ tt('models.setting.general_information') }}</div>
                <div class="card-body">
                    <Field v-model="form.title" :field="{
                        type: 'text',
                        name: 'title',
                        label: 'Tiêu đề',
                    }" />
                    <Field v-model="form.description" :field="{
                        type: 'textarea',
                        name: 'description',
                        label: 'Mô tả',
                    }" />
                    <Field v-model="form.link" :field="{
                        type: 'text',
                        name: 'link',
                        label: 'Link',
                    }" />
                    <Field v-model="form.author_name" :field="{
                        type: 'text',
                        name: 'author_name',
                        label: 'Tên tác giả',
                    }" />
                    <Field v-model="form.email" :field="{
                        type: 'text',
                        name: 'email',
                        label: 'Email',
                    }" />
                    <Field v-model="form.content" :field="{
                        type: 'richtext',
                        name: 'content',
                        label: 'Nội dung giải thưởng',
                    }" />

                </div>
            </div>
            <SeoFields :modelValue="form" @update:modelValue="form = $event" />
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
                    <Field v-model="form.image" :field="{
                        type: 'file_upload',
                        name: 'image',
                        label: 'Ảnh đại diện',
                        multiple: false,
                    }" />
                    <Field v-model="form.banner" :field="{
                        type: 'file_upload',
                        name: 'banner',
                        label: 'Banner Desktop',
                        multiple: false,
                    }" />
                    <Field v-model="form.banner_mobile" :field="{
                        type: 'file_upload',
                        name: 'banner_mobile',
                        label: 'Banner Mobile',
                        multiple: false,
                    }" />
                    <Field v-model="form.images" :field="{
                        type: 'file_upload',
                        name: 'images',
                        label: 'Bộ sưu tập ảnh',
                        multiple: true,
                    }" />
                    <Field v-model="form.author_avatar" :field="{
                        type: 'file_upload',
                        name: 'author_avatar',
                        label: 'Ảnh tác giả',
                        multiple: false,
                    }" />
                    <!-- <Field v-model="form.related_awards" :field="{
                        type: 'select_multiple',
                        name: 'related_awards',
                        label: 'Giải thưởng liên quan',
                        labelBy: 'title',
                        source: {
                            model: 'App\\Models\\Post\\Post',
                            method: 'getPrizes',
                            only: ['id', 'title'],
                        },
                    }" /> -->
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
                type: 'PRIZE',
                status: this.item.status ?? 'ACTIVE',
                view_count: this.item.view_count ?? 0,
                images: this.item.images ?? [],
                price: this.item.price ?? 0,
                // related_awards: this.item.related_awards ?? [],
            },
        }
    },
    watch: {
        item() {
            this.formData = {
                ...this.item,
                type: 'PRIZE',
                status: this.item.status ?? 'ACTIVE',
                price: this.item.price ?? 0,
                images: this.item.images ?? [],
                // related_awards: this.item.related_awards ?? [],

            }
        },
    },
}
</script>
