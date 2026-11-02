<template>
    <div class="container">
        <!-- MODAL -->
        <transition name="fade">
            <div v-if="showOtpInput" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
                    <button @click="closeOtpModal" class="absolute top-2 right-2 text-gray-500 hover:text-black">
                        &times;
                    </button>

                    <div class="space-y-4">
                        <label for="otp" class="block text-sm font-medium">Nhập mã OTP vừa gửi</label>
                        <input
                            type="text"
                            id="otp"
                            maxlength="6"
                            v-model="form.otp"
                            class="w-full p-2 border border-gray-300 rounded-md"
                            placeholder="Nhập 6 số"
                        />
                        <button
                            @click="verifyOtp"
                            class="w-full bg-green-600 text-white py-2 rounded-md mt-2"
                            :disabled="!form.otp"
                        >
                            Xác minh OTP
                        </button>
                        <p class="text-sm text-gray-500 mt-2">
                            Bạn chưa nhận được mã?
                            <button @click="resendOtp" class="underline text-primary" :disabled="countdown > 0">
                                Gửi lại mã <span v-if="countdown > 0">({{ countdown }}s)</span>
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </transition>

        <div class="flex items-center justify-center max-md:py-[62px] py-[120px]">
            <div
                class="p-[32px] shadow-lg md:min-w-[600px] space-y-[1rem] bg-white border rounded-[12px] max-md:w-full"
            >
                <div class="space-y-[1rem]">
                    <div class="headline-1">Đặt lại mật khẩu</div>
                    <FlashMessages />

                    <!-- Form -->
                    <form class="mt-8 space-y-6" @submit.prevent="submit">
                        <!-- Phương thức khôi phục -->
                        <div class="space-y-[6px]">
                            <label>Xác thực qua</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center space-x-2">
                                    <input type="radio" value="email" v-model="form.method" />
                                    <span>Email</span>
                                </label>
                            </div>
                        </div>

                        <!-- Email input -->
                        <div v-if="form.method === 'email'" class="space-y-[6px]">
                            <label for="email">Nhập email <span class="text-red-500">*</span></label>
                            <div class="flex items-center space-x-[8px] p-[10px] border border-gray-300 rounded-[8px]">
                                <input
                                    type="email"
                                    id="email"
                                    placeholder="Nhập email"
                                    v-model="form.email"
                                    class="w-full ring-0 border-none focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- Phone input -->
                        <div v-if="form.method === 'sms' || form.method === 'zalo'" class="space-y-[6px]">
                            <label for="phone">Nhập số điện thoại <span class="text-red-500">*</span></label>
                            <div class="flex items-center space-x-[8px] p-[10px] border border-gray-300 rounded-[8px]">
                                <input
                                    type="tel"
                                    id="phone"
                                    placeholder="Nhập số điện thoại (VD: 0987xxxxxx)"
                                    v-model="form.phone"
                                    class="w-full ring-0 border-none focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            class="w-full rounded-lg bg-primary px-5 py-3 text-sm font-medium text-white transition duration-150 hover:bg-primary-accent-300 focus:outline-none focus:ring-4 focus:ring-primary focus:ring-opacity-50"
                            :disabled="form.processing || invalid"
                            :class="{
                                'opacity-50 cursor-not-allowed': form.processing || invalid,
                            }"
                        >
                            Gửi liên kết / mã OTP
                        </button>
                    </form>
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
                method: 'email',
                phone: '',
                email: '',
                otp: '',
            }),
            showOtpInput: false,
            countdown: 60,
        }
    },
    methods: {
        submit() {
            this.form.post('/quen-mat-khau', {
                onSuccess: () => {
                    if (this.form.method === 'sms') {
                        this.showOtpInput = true
                        this.startCountdown()
                    }
                    if (this.form.method === 'zalo') {
                        this.showOtpInput = true
                        this.startCountdown()
                    }
                },
            })
        },
        verifyOtp() {
            this.form.post('/verify-otp', {
                onSuccess: () => {},
            })
        },
        resendOtp() {
            this.countdown = 60
            this.startCountdown()

            let route = '/send-otp' // mặc định là SMS

            if (this.form.method === 'zalo') {
                route = '/send-zns-otp'
            }

            this.form.post(route)
        },
        startCountdown() {
            const interval = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--
                } else {
                    clearInterval(interval)
                }
            }, 1000)
        },
        closeOtpModal() {
            this.showOtpInput = false
        },
    },
}
</script>
<style lang="scss" scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
