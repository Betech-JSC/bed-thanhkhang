<template>
    <Head title="Đặt lại mật khẩu - Thành Khang" />

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
                    Đặt lại mật khẩu
                </h1>
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <FlashMessages />
                <Field
                    v-model="form.email"
                    :field="{
                        label: 'Email',
                    }"
                />
                <Field
                    v-model="form.password"
                    :field="{
                        label: 'Mật khẩu',
                        type: 'password',
                    }"
                />
                <Field
                    v-model="form.password_confirmation"
                    :field="{
                        label: 'Nhập lại mật khẩu',
                        type: 'password',
                    }"
                />

                <div class="pt-1">
                    <Button
                        type="submit"
                        :loading="form.processing"
                        label="Đặt lại mật khẩu"
                        class="w-full justify-center btn-primary !rounded-xl !py-2.5"
                    />
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
        email: String,
        token: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                token: this.token,
                email: this.email,
                password: "",
                password_confirmation: "",
            }),
        };
    },

    methods: {
        submit() {
            this.form.post(this.route("admin.password.update"), {
                onFinish: () => this.form.reset("password", "password_confirmation"),
            });
        },
    },
};
</script>
