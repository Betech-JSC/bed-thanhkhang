<template>
    <Head title="Xác thực Email - Thành Khang" />

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
                    Xác thực Email
                </h1>
            </div>

            <div class="mb-4 text-sm text-[#4B5565] leading-relaxed">
                Thanks for signing up! Before getting started, could you verify your
                email address by clicking on the link we just emailed to you? If you
                didn't receive the email, we will gladly send you another.
            </div>

            <div
                class="mb-4 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3"
                v-if="verificationLinkSent"
            >
                A new verification link has been sent to the email address you
                provided during registration.
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <FlashMessages />
                <div class="flex items-center justify-between gap-4 pt-1">
                    <Button
                        type="submit"
                        :loading="form.processing"
                        label="Gửi lại email xác thực"
                        class="btn-primary !rounded-xl !py-2.5"
                    />
                    <Link
                        :href="route('admin.logout')"
                        method="post"
                        as="button"
                        class="text-sm text-[#4B5565] underline hover:text-[#00284B] transition-colors"
                    >
                        Log Out
                    </Link>
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
            form: this.$inertia.form(),
        };
    },

    computed: {
        verificationLinkSent() {
            return this.status === "verification-link-sent";
        },
    },

    methods: {
        submit() {
            this.form.post(this.route("admin.verification.send"));
        },
    },
};
</script>
