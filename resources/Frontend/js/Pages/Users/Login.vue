<template>
    <div class="grid grid-cols-3 lg:border-gray-200 lg:border py-[68px] md:py-[196px]">
        <div class="col-span-full flex items-center justify-center max-lg:py-[62px]">
            <div class="px-[32px] md:px-[64px] max-[500px] space-y-[1rem]">
                <div class="space-y-[1rem]">
                    <div class="headline-1">Đăng nhập</div>
                    <FlashMessages />
                    <form class="mt-8 space-y-6" @submit.prevent="submit">
                        <div class="space-y-[6px]">
                            <div
                                class="flex items-center space-x-[8px] p-[10px] border rounded-[8px]"
                                :class="errorLogin ? 'border-red-500' : 'border-gray-300'"
                            >
                                <div>
                                    <svg
                                        width="20"
                                        height="21"
                                        viewBox="0 0 20 21"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M9.99998 12.8751C7.35825 12.8751 5.00898 14.1507 3.51331 16.1301C3.1914 16.5561 3.03044 16.7691 3.03571 17.057C3.03978 17.2794 3.17944 17.56 3.35445 17.6974C3.58097 17.8751 3.89487 17.8751 4.52267 17.8751H15.4773C16.1051 17.8751 16.419 17.8751 16.6455 17.6974C16.8205 17.56 16.9602 17.2794 16.9642 17.057C16.9695 16.7691 16.8086 16.5561 16.4866 16.1301C14.991 14.1506 12.6417 12.8751 9.99998 12.8751Z"
                                            stroke="#667085"
                                            stroke-width="1.66667"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M9.99998 10.3751C12.071 10.3751 13.75 8.69619 13.75 6.62512C13.75 4.55405 12.071 2.87512 9.99998 2.87512C7.92891 2.87512 6.24998 4.55405 6.24998 6.62512C6.24998 8.69619 7.92891 10.3751 9.99998 10.3751Z"
                                            stroke="#667085"
                                            stroke-width="1.66667"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    placeholder="Email hoặc số điện thoại"
                                    v-model="form.login"
                                    class="w-full ring-0 border-none focus:outline-none"
                                    autocomplete="username"
                                />
                            </div>
                            <p v-if="errorLogin" class="text-sm text-red-600 mt-1">{{ errorLogin }}</p>
                        </div>
                        <div class="space-y-[6px]">
                            <div
                                class="flex items-center justify-between p-[10px] border rounded-[8px]"
                                :class="errorPassword ? 'border-red-500' : 'border-gray-300'"
                            >
                                <div class="flex items-center space-x-[8px] w-full">
                                    <div>
                                        <svg
                                            width="20"
                                            height="21"
                                            viewBox="0 0 20 21"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M14.1666 8.70846V7.04179C14.1666 4.7406 12.3012 2.87512 9.99998 2.87512C7.69879 2.87512 5.83331 4.7406 5.83331 7.04179V8.70846M9.99998 12.4585V14.1251M7.33331 17.8751H12.6666C14.0668 17.8751 14.7668 17.8751 15.3016 17.6026C15.772 17.363 16.1545 16.9805 16.3942 16.5101C16.6666 15.9753 16.6666 15.2753 16.6666 13.8751V12.7085C16.6666 11.3083 16.6666 10.6083 16.3942 10.0735C16.1545 9.60307 15.772 9.22062 15.3016 8.98094C14.7668 8.70846 14.0668 8.70846 12.6666 8.70846H7.33331C5.93318 8.70846 5.23312 8.70846 4.69834 8.98094C4.22793 9.22062 3.84548 9.60307 3.6058 10.0735C3.33331 10.6083 3.33331 11.3083 3.33331 12.7085V13.8751C3.33331 15.2753 3.33331 15.9753 3.6058 16.5101C3.84548 16.9805 4.22793 17.363 4.69834 17.6026C5.23312 17.8751 5.93318 17.8751 7.33331 17.8751Z"
                                                stroke="#667085"
                                                stroke-width="1.66667"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </div>
                                    <input
                                        :type="isPasswordVisible ? 'text' : 'password'"
                                        placeholder="Nhập mật khẩu"
                                        v-model="form.password"
                                        class="w-full ring-0 border-none focus:outline-none"
                                        :class="{ 'border-red-500': errorPassword }"
                                    />
                                </div>
                                <div @click="togglePasswordVisibility" class="cursor-pointer ml-2">
                                    <svg
                                        v-if="isPasswordVisible"
                                        width="20"
                                        height="21"
                                        viewBox="0 0 20 21"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <!-- Mắt mở -->
                                        <path
                                            d="M1.81445 10.1889C1.79059 10.293 1.7906 10.4572 1.81447 10.5613C1.84625 10.6999 1.90326 10.7897 2.01676 10.9694C2.9546 12.4544 5.74617 16.2085 10.0003 16.2085C14.2545 16.2085 17.0461 12.4545 17.9839 10.9695C18.0974 10.7897 18.1541 10.6999 18.1859 10.5613C18.2098 10.4572 18.2098 10.293 18.1859 10.1889C18.1542 10.0503 18.0974 9.96001 17.9839 9.77952C17.0461 8.29453 14.2545 4.54051 10.0003 4.54051C5.74617 4.54051 2.9546 8.29454 2.01676 9.77953C1.90326 9.96001 1.84625 10.0503 1.81447 10.1889Z"
                                            stroke="#667085"
                                            stroke-width="1.25"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <circle cx="10" cy="10.3751" r="2.5" stroke="#667085" stroke-width="1.25" />
                                    </svg>

                                    <svg
                                        v-else
                                        width="20"
                                        height="21"
                                        viewBox="0 0 20 21"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <!-- Mắt bị gạch -->
                                        <path
                                            d="M8.95245 4.61872C9.29113 4.56865 9.64051 4.54179 10.0003 4.54179C14.2545 4.54179 17.0461 8.29582 17.9839 9.78081C18.0974 9.96054 18.1542 10.0504 18.1859 10.189C18.2098 10.2931 18.2098 10.4573 18.1859 10.5614C18.1541 10.7 18.097 10.7905 17.9827 10.9714C17.7328 11.3669 17.3518 11.9227 16.8471 12.5255M5.6036 5.97099C3.80187 7.19321 2.57871 8.89127 2.01759 9.77953C1.90357 9.96001 1.84656 10.0503 1.81478 10.1889C1.79091 10.293 1.7909 10.4572 1.81476 10.5613C1.84652 10.6999 1.90328 10.7897 2.01678 10.9694C2.95462 12.4544 5.74618 16.2085 10.0003 16.2085C11.7157 16.2085 13.1932 15.5981 14.4073 14.7723M2.50035 2.87512L17.5003 17.8751M8.23258 8.60735C7.78017 9.05977 7.50035 9.68477 7.50035 10.3751C7.50035 11.7558 8.61963 12.8751 10.0003 12.8751C10.6907 12.8751 11.3157 12.5953 11.7681 12.1429"
                                            stroke="#D0D5DD"
                                            stroke-width="1.25"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <p v-if="errorPassword" class="text-sm text-red-600 mt-1">{{ errorPassword }}</p>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-lg bg-red-fks px-5 py-3 text-sm font-medium text-white transition duration-150 hover:bg-primary-accent-300 focus:outline-none focus:ring-4 focus:ring-bg-red-fks focus:ring-opacity-50"
                            :class="{
                                'opacity-50 cursor-not-allowed': !form.login || !form.password,
                            }"
                            :disabled="!form.login || !form.password"
                        >
                            Đăng nhập
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
                            Đăng nhập với Google
                        </a>
                        <div class="space-y-[1rem]">
                            <Link :href="route('forgot.password.index')" class="text-primary underline block"
                                >Bạn quên mật khẩu
                            </Link>
                            <Link :href="route('register')" class="text-primary underline block"
                                >Bạn chưa có tài khoản
                            </Link>
                        </div>
                    </form>
                </div>
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
            isPasswordVisible: false,
            form: this.$inertia.form({
                login: '',
                password: '',
            }),
            clientErrors: {
                login: '',
                password: '',
            },
        }
    },

    computed: {
        errors() {
            return this.$page.props.errors || {}
        },
        errorLogin() {
            return this.clientErrors.login || this.errors.login || null
        },
        errorPassword() {
            return this.clientErrors.password || this.errors.password || null
        },
    },
    methods: {
        togglePasswordVisibility() {
            this.isPasswordVisible = !this.isPasswordVisible
        },
        validateLogin() {
            this.clientErrors.login = ''
            const v = String(this.form.login || '').trim()
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
            const phoneRegex = /^[0-9]{10,11}$/
            if (!v) {
                this.clientErrors.login = 'Vui lòng nhập email hoặc số điện thoại.'
            } else if (v.includes('@')) {
                if (!emailRegex.test(v)) {
                    this.clientErrors.login = 'Email không đúng định dạng.'
                }
            } else if (!phoneRegex.test(v)) {
                this.clientErrors.login = 'Số điện thoại phải có 10 hoặc 11 chữ số.'
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
            this.validateLogin()
            this.validatePassword()
            if (this.clientErrors.login || this.clientErrors.password) {
                return
            }
            this.form.post('/dang-nhap', {
                onError: (errors) => {
                    console.error('Lỗi đăng nhập:', errors)
                },
            })
        },
    },
}
</script>
