<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
            <div class="card">
                <div class="card-header">{{ tt('models.setting.general_information') }}</div>
                <div class="card-body space-y-4">
                    <Field
                        v-model="form.name"
                        :field="{
                            type: 'text',
                            name: 'name',
                            label: 'Họ và tên',
                        }"
                    />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.email"
                            :field="{
                                type: 'text',
                                name: 'email',
                                label: 'Email',
                            }"
                        />
                        <Field
                            v-model="form.phone"
                            :field="{
                                type: 'text',
                                name: 'phone',
                                label: 'Số điện thoại',
                            }"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.password"
                            :field="{
                                type: 'password',
                                name: 'password',
                                label: form.id ? 'Mật khẩu mới (Để trống nếu không đổi)' : 'Mật khẩu',
                            }"
                        />
                    </div>

                    <div class="border-t border-slate-100 my-4 pt-4 space-y-4">
                        <Field
                            v-model="form.address"
                            :field="{
                                type: 'textarea',
                                name: 'address',
                                label: 'Địa chỉ liên hệ',
                            }"
                        />
                        <Field
                            v-model="form.note"
                            :field="{
                                type: 'textarea',
                                name: 'note',
                                label: 'Ghi chú quản trị',
                                placeholder: 'Ghi chú nội bộ dành cho quản trị viên...',
                            }"
                        />
                    </div>
                </div>
            </div>
        </template>

        <template #aside="{ form }">
            <div class="card h-fit">
                <div class="card-header">
                    <span>Trạng thái tài khoản</span>
                </div>
                <div class="card-body space-y-4">
                    <Field
                        v-model="form.role"
                        :field="{
                            type: 'select_single',
                            name: 'role',
                            label: 'Vai trò người dùng',
                            options: rolesOptions,
                        }"
                    />
                    <Field
                        v-model="form.status"
                        :field="{
                            type: 'select_single',
                            name: 'status',
                            label: 'Trạng thái hoạt động',
                            options: statusOptions,
                        }"
                    />
                </div>
            </div>
        </template>
    </Form>
</template>

<script>
export default {
    props: ['item', 'schema', 'roles'],
    data() {
        return {
            formData: {
                ...this.item,
            },
        }
    },
    computed: {
        rolesOptions() {
            const list = (this.roles || []).map(r => ({ label: r.label, id: r.value }))
            return [{ label: '-- Chọn vai trò --', id: '' }, ...list]
        },
        statusOptions() {
            const list = this.schema?.columns?.status?.list || []
            return list.filter(opt => ['ACTIVE', 'INACTIVE'].includes(opt.id))
        }
    },
    watch: {
        item() {
            this.formData = {
                ...this.item,
            }
        },
    },
}
</script>
