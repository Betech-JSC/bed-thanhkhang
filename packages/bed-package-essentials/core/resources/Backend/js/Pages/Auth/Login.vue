<template>
    <Head title="Đăng nhập Hệ thống - HH Maker" />

    <div class="w-full">
        <!-- Main Dark Tech Login Card -->
        <div class="relative bg-[#111322]/90 backdrop-blur-xl border border-[#404968]/50 rounded-2xl shadow-2xl shadow-black/80 p-6 sm:p-8 overflow-hidden">
            <!-- Subtle Card Accent Glow -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-1 bg-gradient-to-r from-transparent via-[#2970FF] to-transparent"></div>

            <!-- Card Header -->
            <div class="text-center mb-6">
                <!-- Logo HH Maker 3D with Subtle Glow -->
                <div class="flex items-center justify-center mb-4">
                    <img
                        src="/assets/images/header/figma-header-logo.svg"
                        alt="HH Maker"
                        class="h-10 w-auto object-contain filter drop-shadow-[0_0_15px_rgba(41,112,255,0.45)]"
                    />
                </div>

                <!-- Status Badge with Pulsing LED -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#181D33] border border-[#2B334E] text-[11px] font-mono tracking-wider text-[#8B9FC7]">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#2970FF] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#2970FF]"></span>
                    </span>
                    <span>HH MAKER WORKSHOP OS • v2.0</span>
                </div>

                <!-- Title & Subtitle -->
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white uppercase font-sans mt-3">
                    ĐĂNG NHẬP HỆ THỐNG
                </h1>
                <p class="text-xs text-[#8B9FC7] mt-1">
                    Hệ thống Quản trị &amp; Điều hành Xưởng in 3D
                </p>
            </div>

            <!-- Status Alert if any -->
            <div v-if="status" class="mb-5 text-xs font-medium text-emerald-400 bg-emerald-950/40 border border-emerald-500/30 rounded-xl p-3 text-center">
                {{ status }}
            </div>

            <!-- Flash Error / General Error -->
            <div v-if="form.errors.email && !form.errors.password && form.errors.email.includes('credentials')" class="mb-5 text-xs font-medium text-rose-400 bg-rose-950/40 border border-rose-500/30 rounded-xl p-3 text-center">
                {{ form.errors.email }}
            </div>

            <!-- Login Form -->
            <form class="space-y-4" @submit.prevent="submit">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-medium text-[#C5CEE0] mb-1.5">
                        Tài khoản Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#64748B]">
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
                            placeholder="admin@hhmaker.vn"
                            class="w-full pl-10 pr-4 py-2.5 bg-[#0F121F] border border-[#2B334E] rounded-xl text-sm text-gray-100 placeholder-[#64748B] focus:outline-none focus:border-[#2970FF] focus:ring-1 focus:ring-[#2970FF] transition-all"
                            :class="{ '!border-rose-500 !ring-rose-500/30': form.errors.email }"
                        />
                    </div>
                    <p v-if="form.errors.email" class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ form.errors.email }}</span>
                    </p>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-medium text-[#C5CEE0] mb-1.5">
                        Mật khẩu Quản trị
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#64748B]">
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
                            class="w-full pl-10 pr-10 py-2.5 bg-[#0F121F] border border-[#2B334E] rounded-xl text-sm text-gray-100 placeholder-[#64748B] focus:outline-none focus:border-[#2970FF] focus:ring-1 focus:ring-[#2970FF] transition-all"
                            :class="{ '!border-rose-500 !ring-rose-500/30': form.errors.password }"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            tabindex="-1"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#64748B] hover:text-gray-300 transition-colors"
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
                    <p v-if="form.errors.password" class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
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
                            class="w-4 h-4 rounded bg-[#0F121F] border border-[#2B334E] text-[#2970FF] focus:ring-[#2970FF] focus:ring-offset-0 focus:ring-offset-[#111322] cursor-pointer"
                        />
                        <span class="text-xs text-[#8B9FC7] group-hover:text-gray-300 transition-colors">
                            Ghi nhớ đăng nhập
                        </span>
                    </label>
                </div>

                <!-- Submit CTA Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-[#2970FF] to-[#1E56CC] hover:from-[#3B82F6] hover:to-[#2563EB] text-white font-semibold text-sm tracking-wide shadow-[0_4px_16px_rgba(41,112,255,0.35)] hover:shadow-[0_6px_20px_rgba(41,112,255,0.45)] transition-all duration-200 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer mt-2"
                >
                    <svg v-if="form.processing" class="w-4 h-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>{{ form.processing ? 'ĐANG XỬ LÝ...' : 'ĐĂNG NHẬP HỆ THỐNG' }}</span>
                </button>
            </form>

            <!-- Card Footer / Security Notice -->
            <div class="mt-6 pt-5 border-t border-[#2B334E]/60 text-center">
                <p class="text-[11px] font-mono tracking-tight text-[#64748B]">
                    Authorized Personnel Only • Additive Manufacturing &amp; Rapid Prototyping System
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
