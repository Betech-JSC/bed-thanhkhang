<template layout>
    <Head :title="tt('models.titles.setting')" />
    <WrapSetting>
        <Form v-model="formData" :config="{ canDestroy: false, reverse: true, addGrid: false, resource: 'settings' }">
            <template #default="{ form }">
                <div class="card">
                    <div class="card-header font-bold text-lg p-4 border-b">Cấu hình Tỷ giá quy đổi mặc định</div>
                    <div class="card-body p-4">
                        <Field
                            v-model="form.points_exchange_rate"
                            :field="{
                                type: 'number',
                                label: 'Tỷ giá quy đổi mặc định (VNĐ cho 1 Quả)',
                                name: 'points_exchange_rate',
                                placeholder: 'Ví dụ: 1000'
                            }"
                        />
                    </div>
                </div>
                <Field class="hidden" disabled v-model="form.id" :field="{ default: 'points' }" />
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
            formData: {
                points_exchange_rate: 1000,
                ...this.item,
            },
        }
    },
}
</script>
