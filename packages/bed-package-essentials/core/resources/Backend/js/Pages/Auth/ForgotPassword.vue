<template>
    <Head title="Quên mật khẩu - Thành Khang" />

    <div class="w-full">
        <div class="relative bg-white border border-[#E3E8EF] rounded-2xl shadow-xl shadow-[#00284B]/5 p-6 sm:p-8 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-[#005EA4]"></div>

            <div class="text-center mb-6 pt-1">
                <div class="flex items-center justify-center mb-5">
                    <img
                        src="/assets/images/logo/logo-thanhkhang.png"
                        alt="Thành Khang"
                        class="h-10 w-auto object-contain"
                    />
                </div>
                <h1 class="text-xl font-bold tracking-tight text-[#00284B] uppercase font-sans">
                    Quên mật khẩu
                </h1>
            </div>

            <div class="mb-4 text-sm text-[#4B5565] leading-relaxed">
                Forgot your password? No problem. Just let us know your email address
                and we will email you a password reset link that will allow you to
                choose a new one.
            </div>

            <div v-if="status" class="mb-4 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">
                {{ status }}
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <FlashMessages />
                <Field
                    v-model="form.email"
                    :field="{
                        label: 'Email',
                    }"
                />
                <div class="pt-1">
                    <Button
                        type="submit"
                        :loading="form.processing"
                        class="w-full justify-center btn-primary !rounded-xl !py-2.5"
                    >
                        Email Password Reset Link
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import Guest from "@Core/Layouts/Guest.vue";
import FlashMessages from "@Core/Components/FlashMessages.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";

export default {
    components: {
        Head,
        Link,
        FlashMessages,
    },
    layout: Guest,

    props: {
        status: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                email: null,
            }),
        };
    },

    methods: {
        submit() {
            this.form.post(this.route("admin.password.email"));
        },
    },
};
</script>
