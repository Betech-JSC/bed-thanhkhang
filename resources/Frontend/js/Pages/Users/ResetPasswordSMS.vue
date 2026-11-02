<template>
    <div class="container">
        <div class="flex items-center justify-center py-[64px]">
            <div
                class="p-[32px] shadow-lg md:min-w-[600px] space-y-[1rem] bg-white border rounded-[12px] max-md:w-fulls"
            >
                <div class="headline-1">Đặt lại mật khẩu SMS OTP</div>
                <FlashMessages />
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block mb-1 font-semibold" for="password">Mật khẩu mới</label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            placeholder="Nhập mật khẩu mới"
                            class="w-full border border-gray-300 rounded px-3 py-2"
                            autocomplete="new-password"
                        />
                    </div>

                    <div>
                        <label class="block mb-1 font-semibold" for="password_confirmation">Xác nhận mật khẩu</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            placeholder="Nhập lại mật khẩu mới"
                            class="w-full border border-gray-300 rounded px-3 py-2"
                            autocomplete="new-password"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700"
                        :disabled="processing"
                    >
                        Đặt lại mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: this.$inertia.form({
                method: 'sms',
                password: '',
                password_confirmation: '',
            }),
            processing: false,
        }
    },
    computed: {
        errors() {
            return this.$page.props.errors || {}
        },
    },
    methods: {
        submit() {
            this.processing = true
            this.form.post('/dat-lai-mat-khau', {
                onFinish: () => {
                    this.processing = false
                },
                onError: () => {
                    this.processing = false
                },
            })
        },
    },
}
</script>
