<template layout>
    <Head :title="tt('models.titles.setting')" />
    <WrapSetting>
        <Form v-model="formData" :config="{ canDestroy: false, reverse: true, addGrid: false, resource: 'settings' }">
            <template #default="{ form }">
                <div class="card">
                    <div class="card-body">
                        <Field
                            v-model="form.general_business_name"
                            :field="{
                                name: 'general_business_name',
                            }"
                        />
                        <Field
                            v-model="form.general_site_name"
                            :field="{
                                name: 'general_site_name',
                            }"
                        />
                        <Field
                            v-model="form.general_email"
                            :field="{
                                label: 'Email',
                                name: 'general_email',
                            }"
                        />
                        <Field
                            v-model="form.general_address"
                            :field="{
                                label: 'Địa chỉ',
                                name: 'general_address',
                            }"
                        />
                        <Field
                            v-model="form.general_phone"
                            :field="{
                                label: 'Phone',
                                name: 'general_phone',
                            }"
                        />
                        <Field
                            v-model="form.notification_popup_mode"
                            :field="{
                                type: 'dropdown',
                                label: 'Chế độ Popup thông báo góc màn hình',
                                name: 'notification_popup_mode',
                                options: [
                                    { id: 'fake', label: 'Chỉ thông báo ảo (Dùng Message Pool)' },
                                    { id: 'real', label: 'Chỉ thông báo thật (Lượt tải tài nguyên từ hệ thống)' },
                                    { id: 'hybrid', label: 'Kết hợp cả thật và ảo (Ưu tiên dữ liệu thật)' },
                                ],
                                placeholder: 'Chọn chế độ hiển thị',
                            }"
                        />
                        <Field
                            v-show="form.notification_popup_mode !== 'fake'"
                            v-model="form.notification_popup_name_style"
                            :field="{
                                type: 'dropdown',
                                label: 'Định dạng tên người dùng trên Popup thật',
                                name: 'notification_popup_name_style',
                                options: [
                                    { id: 'masked', label: 'Viết tắt bảo mật (VD: Bảo N*** vừa tải về...)' },
                                    { id: 'anonymous', label: 'Ẩn danh chung (VD: Ai đó vừa tải về...)' },
                                    { id: 'full', label: 'Tên đầy đủ (VD: Nguyễn Văn Bảo vừa tải về...)' },
                                ],
                                placeholder: 'Chọn định dạng tên',
                            }"
                        />
                        <Field
                            v-model="form.messagePool"
                            :field="{
                                type: 'textarea',
                                label: 'Message Pool (Dữ liệu thông báo ảo)',
                                name: 'messagePool',
                                rows: 8,
                                help: 'Các câu thông báo ngăn cách bởi dấu phẩy, dùng khi ở chế độ Ảo hoặc Kết hợp.',
                            }"
                        />
                    </div>
                </div>
                <SeoFields
                    :modelValue="form"
                    @update:modelValue="form = $event"
                    :config="{
                        hiddenFields: ['seo_slug'],
                    }"
                />
                <Field class="hidden" disabled v-model="form.id" :field="{ default: 'general' }" />
            </template>
        </Form>
    </WrapSetting>
</template>
<script>
import WrapSetting from '@Core/Components/WrapSetting.vue'
export default {
    components: { WrapSetting },
    props: ['item', 'schema'],
    data() {
        return {
            formData: this.item,
        }
    },
}
</script>
