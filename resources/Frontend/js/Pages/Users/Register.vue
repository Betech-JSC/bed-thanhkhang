<template>
    <div class="grid grid-cols-3 lg:border-gray-200 lg:border md:gap-[32px] py-[68px] md:py-[196px]">
        <div class="col-span-full flex items-center justify-center bg-white max-lg:pt-[12px] max-lg:pb-[120px]">
            <div class="px-[32px] md:px-[64px] max-[600px] lg:space-y-[2rem] space-y-[12px]">
                <div class="headline-1">Tạo tài khoản</div>
                <FlashMessages />
                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <div>
                        <input
                            placeholder="Nhập Họ tên"
                            v-model="form.name"
                            class="w-full ring-0 focus:outline-none border p-[10px] rounded-[8px]"
                            :class="errorName ? 'border-red-500' : 'border-gray-300'"
                        />
                        <p v-if="errorName" class="text-sm text-red-600 mt-1">{{ errorName }}</p>
                    </div>
                    <div>
                        <input
                            placeholder="Nhập Email"
                            v-model="form.email"
                            class="w-full ring-0 focus:outline-none border p-[10px] rounded-[8px]"
                            :class="errorEmail ? 'border-red-500' : 'border-gray-300'"
                        />
                        <p v-if="errorEmail" class="text-sm text-red-600 mt-1">{{ errorEmail }}</p>
                    </div>
                    <div>
                        <input
                            placeholder="Nhập Số điện thoại (10–11 chữ số)"
                            v-model="form.phone"
                            class="w-full ring-0 focus:outline-none border p-[10px] rounded-[8px]"
                            :class="errorPhone ? 'border-red-500' : 'border-gray-300'"
                        />
                        <p v-if="errorPhone" class="text-sm text-red-600 mt-1">{{ errorPhone }}</p>
                    </div>

                    <div>
                        <input
                            placeholder="Nhập Mật khẩu"
                            v-model="form.password"
                            type="password"
                            class="w-full ring-0 focus:outline-none border p-[10px] rounded-[8px]"
                            :class="errorPassword ? 'border-red-500' : 'border-gray-300'"
                        />
                        <p v-if="errorPassword" class="text-sm text-red-600 mt-1">{{ errorPassword }}</p>
                    </div>
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-red-fks px-5 py-3 text-sm font-medium text-white transition duration-150 hover:bg-primary-accent-300 focus:outline-none focus:ring-4 focus:ring-bg-red-fks focus:ring-opacity-50"
                        :class="{
                            'opacity-50 cursor-not-allowed': !form.name || !form.email || !form.phone || !form.password,
                        }"
                        :disabled="!form.name || !form.email || !form.phone || !form.password"
                    >
                        Tạo tài khoản
                    </button>
                    <div class="relative my-4">
                        <span class="block text-center text-sm text-gray-500">Hoặc</span>
                    </div>
                    <a
                        :href="route('auth.google')"
                        class="w-full flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Đăng ký với Google
                    </a>
                    <div class="pt-2">
                        <Link :href="route('user.login')" class="text-primary underline"
                            >Đã có tài khoản? Đăng nhập</Link
                        >
                    </div>
                </form>
                <div class="body-3 text-gray-700">
                    Qua việc đăng nhập hoặc tạo tài khoản, bạn đồng ý
                    <Link href="/chinh-sach/gioi-thieu" class="text-primary border-b border-primary"
                        >với Điều khoản sử dụng</Link
                    >
                    và
                    <Link href="/chinh-sach/chinh-sach-bao-mat" class="text-primary border-b border-primary"
                        >Chính sách bảo mật</Link
                    >
                    của chúng tôi.
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: this.$inertia.form({
                name: '',
                email: '',
                phone: '',
                password: '',
            }),
            clientErrors: {
                name: '',
                email: '',
                phone: '',
                password: '',
            },
        }
    },
    computed: {
        errors() {
            return this.$page.props.errors || {}
        },
        errorName() {
            return this.clientErrors.name || this.errors.name || null
        },
        errorEmail() {
            return this.clientErrors.email || this.errors.email || null
        },
        errorPhone() {
            return this.clientErrors.phone || this.errors.phone || null
        },
        errorPassword() {
            return this.clientErrors.password || this.errors.password || null
        },
    },
    methods: {
        validateName() {
            this.clientErrors.name = ''
            if (!this.form.name) {
                this.clientErrors.name = 'Vui lòng nhập họ và tên.'
            } else if (this.form.name.length > 255) {
                this.clientErrors.name = 'Họ và tên không được vượt quá 255 ký tự.'
            }
        },
        validateEmail() {
            this.clientErrors.email = ''
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
            if (!this.form.email) {
                this.clientErrors.email = 'Vui lòng nhập email.'
            } else if (!emailRegex.test(this.form.email)) {
                this.clientErrors.email = 'Email không đúng định dạng.'
            } else if (this.form.email.length > 255) {
                this.clientErrors.email = 'Email không được vượt quá 255 ký tự.'
            }
        },
        validatePhone() {
            this.clientErrors.phone = ''
            const phoneRegex = /^[0-9]{10,11}$/
            if (!this.form.phone) {
                this.clientErrors.phone = 'Vui lòng nhập số điện thoại.'
            } else if (!phoneRegex.test(this.form.phone)) {
                this.clientErrors.phone = 'Số điện thoại phải có 10 hoặc 11 chữ số.'
            }
        },
        validatePassword() {
            this.clientErrors.password = ''
            if (!this.form.password) {
                this.clientErrors.password = 'Vui lòng nhập mật khẩu.'
            } else if (this.form.password.length < 8) {
                this.clientErrors.password = 'Mật khẩu phải có ít nhất 8 ký tự.'
            }
        },
        submit() {
            this.validateName()
            this.validateEmail()
            this.validatePhone()
            this.validatePassword()
            if (
                this.clientErrors.name ||
                this.clientErrors.email ||
                this.clientErrors.phone ||
                this.clientErrors.password
            ) {
                return
            }
            this.form.post('/dang-ky', {
                onError: (errors) => {
                    console.error('Lỗi đăng ký:', errors)
                },
            })
        },
    },
}
</script>