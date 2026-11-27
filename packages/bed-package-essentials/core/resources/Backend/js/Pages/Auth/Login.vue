<template>
    <Head title="Đăng nhập Hệ thống - Thành Khang" />

    <div class="w-full">
        <!-- Main Corporate Login Card -->
        <div class="relative bg-white border border-[#E3E8EF] rounded-2xl shadow-xl shadow-[#00284B]/5 p-6 sm:p-8 overflow-hidden">
            <!-- Top Brand Accent Line -->
            <div class="absolute top-0 inset-x-0 h-1 bg-[#005EA4]"></div>

            <!-- Card Header -->
            <div class="text-center mb-6 pt-1">
                <!-- Brand Logo Thành Khang -->
                <div class="flex items-center justify-center mb-5">
                    <img
                        src="/assets/images/logo/logo-thanhkhang.png"
                        alt="Thành Khang"
                        class="h-10 w-auto object-contain"
                    />
                </div>

                <!-- Title & Subtitle -->
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#00284B] uppercase font-sans">
                    ĐĂNG NHẬP HỆ THỐNG
                </h1>
                <p class="text-xs text-[#4B5565] mt-1.5">
                    Hệ thống Quản trị Thiết bị &amp; Phụ tùng Công nghiệp
                </p>
            </div>

            <!-- Status Alert if any -->
            <div v-if="status" class="mb-5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center">
                {{ status }}
            </div>

            <!-- Flash Error / General Error -->
            <div v-if="form.errors.email && !form.errors.password && form.errors.email.includes('credentials')" class="mb-5 text-xs font-medium text-rose-700 bg-rose-50 border border-rose-200 rounded-xl p-3 text-center">
                {{ form.errors.email }}
            </div>

            <!-- Login Form -->
            <form class="space-y-4" @submit.prevent="submit">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#364152] mb-1.5">
                        Tài khoản Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#697586]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autocomplete="username"
                            placeholder="info@dvktthanhkhang.vn"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-[#D6D6D6] rounded-xl text-sm text-[#202939] placeholder-[#9AA4B2] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-all"
                            :class="{ '!border-rose-500 !ring-rose-500/20': form.errors.email }"
                        />
                    </div>
                    <p v-if="form.errors.email" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ form.errors.email }}</span>
                    </p>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-[#364152] mb-1.5">
                        Mật khẩu Quản trị
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#697586]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••••••"
                            class="w-full pl-10 pr-10 py-2.5 bg-white border border-[#D6D6D6] rounded-xl text-sm text-[#202939] placeholder-[#9AA4B2] focus:outline-none focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-all"
                            :class="{ '!border-rose-500 !ring-rose-500/20': form.errors.password }"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            tabindex="-1"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#697586] hover:text-[#00284B] transition-colors"
                        >
                            <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <p v-if="form.errors.password" class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ form.errors.password }}</span>
                    </p>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none group">
                        <input
                            id="remember"
                            type="checkbox"
                            v-model="form.remember"
                            class="w-4 h-4 rounded bg-white border border-[#D6D6D6] text-[#005EA4] focus:ring-[#005EA4]/30 focus:ring-offset-0 cursor-pointer accent-[#005EA4]"
                        />
                        <span class="text-xs text-[#4B5565] group-hover:text-[#00284B] transition-colors">
                            Ghi nhớ đăng nhập
                        </span>
                    </label>
                </div>

                <!-- Submit CTA Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-white font-semibold text-sm tracking-wide shadow-sm hover:shadow transition-all duration-200 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer mt-2"
                >
                    <svg v-if="form.processing" class="w-4 h-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>{{ form.processing ? 'ĐANG XỬ LÝ...' : 'ĐĂNG NHẬP HỆ THỐNG' }}</span>
                </button>
            </form>

            <!-- Card Footer / Security Notice -->
            <div class="mt-6 pt-5 border-t border-[#E3E8EF] text-center">
                <p class="text-[11px] font-medium tracking-tight text-[#697586]">
                    Authorized Personnel Only • Thành Khang Management System
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import Guest from '@Core/Layouts/Guest.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'

export default {
    components: {
        Head,
        Link,
    },
    layout: Guest,

    props: {
        canResetPassword: Boolean,
        status: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                email: null,
                password: null,
                remember: true,
            }),
            showPassword: false,
            isProduction: import.meta.env.PROD,
        }
    },

    methods: {
        submit() {
            this.form
                .transform((data) => ({
                    ...data,
                    remember: this.form.remember ? 'on' : '',
                }))
                .post(this.route('admin.login'), {
                    onFinish: () => this.form.reset('password'),
                })
        },
    },
}
</script>
