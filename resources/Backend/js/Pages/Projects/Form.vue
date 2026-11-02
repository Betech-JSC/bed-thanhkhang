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
                    <Field v-model="form.contact" :field="{
                        type: 'text',
                        name: 'contact',
                        label: 'Liên hệ',
                    }" />
                    <Field v-model="form.author_name" :field="{
                        type: 'text',
                        name: 'author_name',
                        label: 'Tên tác giả',
                    }" />
                    <Field v-model="form.year" :field="{
                        type: 'number',
                        name: 'year',
                        label: 'Năm',
                    }" />
                    <Field v-model="form.categories" :field="{
                        type: 'select_multiple',
                        name: 'categories',
                        label: 'Ngành',
                        labelBy: 'title',
                        source: {
                            model: 'App\\Models\\Post\\PostCategory',
                            method: 'getProject',
                            only: ['id', 'title'],
                        },
                    }" />
                    <Field v-model="form.link" :field="{
                        type: 'text',
                        name: 'link',
                        label: 'Link',
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
                        multiple: false,
                    }" />

                    <Field v-model="form.images" :field="{
                        type: 'file_upload',
                        name: 'images',
                        multiple: true,
                    }" />

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
                type: 'PROJECT',
                title: this.item.title ?? '',
                contact: this.item.contact ?? '',
                author_name: this.item.author_name ?? '',
                year: this.item.year ?? null,
                description: this.item.description ?? '',
                content: this.item.content ?? '',
                categories: this.item.categories ?? [],
                link: this.item.link ?? '',
                view_count: this.item.view_count ?? 0,
                status: this.item.status ?? 'ACTIVE',
                image: this.item.image ?? null,
                related_posts: this.item.related_posts ?? [],
                author_avatar: this.item.author_avatar ?? null,
                images: this.item.images ?? [],
                price: this.item.price ?? 0,
                is_countdown_active: this.item.is_countdown_active ?? 0,
                ...this.item,
            },
        }
    },
    watch: {
        item() {
            this.formData = {
                type: 'PROJECT',
                title: this.item.title ?? '',
                contact: this.item.contact ?? '',
                author_name: this.item.author_name ?? '',
                year: this.item.year ?? null,
                description: this.item.description ?? '',
                content: this.item.content ?? '',
                categories: this.item.categories ?? [],
                link: this.item.link ?? '',
                view_count: this.item.view_count ?? 0,
                status: this.item.status ?? 'ACTIVE',
                image: this.item.image ?? null,
                related_posts: this.item.related_posts ?? [],
                author_avatar: this.item.author_avatar ?? null,
                images: this.item.images ?? [],
                price: this.item.price ?? 0,
                is_countdown_active: this.item.is_countdown_active ?? 0,
                ...this.item,
            }
        },
    },
}
</script>