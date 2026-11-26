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
                            label: 'Tiêu đề tài liệu',
                        }"
                    />
                    <Field
                        v-model="form.description"
                        :field="{
                            type: 'textarea',
                            name: 'description',
                            label: 'Mô tả ngắn',
                        }"
                    />
                    <div class="space-y-2">
                        <Field
                            v-model="form.download"
                            :field="{
                                type: 'text',
                                name: 'download',
                                label: 'Tệp PDF / Link tải',
                                placeholder: '/static/files/catalogue.pdf hoặc https://...',
                            }"
                        />
                        <div class="flex flex-wrap items-center gap-2">
                            <Button
                                type="button"
                                class="btn-outline-primary"
                                @click="showPdfManager = true"
                                label="Chọn / Tải tệp từ FileManager"
                            />
                            <a
                                v-if="form.download"
                                :href="form.download"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm text-blue-600 underline"
                            >
                                Xem tệp hiện tại
                            </a>
                            <Button
                                v-if="form.download"
                                type="button"
                                variant="white"
                                @click="form.download = ''"
                                label="Xóa liên kết"
                            />
                        </div>
                    </div>
                    <Field
                        v-model="form.content"
                        :field="{
                            type: 'richtext',
                            name: 'content',
                            label: 'Nội dung chi tiết',
                        }"
                    />
                </div>
            </div>
            <SeoFields :modelValue="form" @update:modelValue="form = $event" />
            <FileManager
                v-if="showPdfManager"
                v-model:show="showPdfManager"
                :multiple="false"
                @on-select="handleSelectPdf($event, form)"
            />
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
                        v-model="form.categories"
                        :field="{
                            type: 'select_multiple',
                            name: 'categories',
                            label: 'Danh mục tài liệu',
                            labelBy: 'title',
                            source: {
                                model: 'App\\Models\\Post\\PostCategory',
                                method: 'getDocuments',
                                only: ['id', 'title'],
                            },
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
import FileManager from '@Core/Components/FileManager.vue'

export default {
    components: {
        FileManager,
    },
    props: ['item', 'schema'],
    data() {
        return {
            showPdfManager: false,
            formData: {
                ...this.item,
                type: 'DOCUMENT',
                status: this.item.status ?? 'ACTIVE',
                position: this.item.position ?? 0,
                download: this.item.download ?? '',
                categories: this.item.categories ?? [],
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
                download: this.item.download ?? '',
                categories: this.item.categories ?? [],
            }
        },
    },
    methods: {
        handleSelectPdf(files, form) {
            if (!Array.isArray(files) || files.length === 0) return
            const file = files[0]
            const rawUrl = file?.static_url || file?.path || ''
            if (!rawUrl) return
            try {
                if (rawUrl.startsWith('http://') || rawUrl.startsWith('https://')) {
                    const parsed = new URL(rawUrl)
                    if (parsed.pathname.includes('/static/')) {
                        form.download = '/' + parsed.pathname.replace(/^\/+/, '')
                        return
                    }
                }
            } catch (e) {}
            form.download = rawUrl
        },
    },
}
</script>
