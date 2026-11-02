<template>
    <div class="grid grid-cols-3 lg:border-gray-200 lg:border py-[68px] md:py-[196px]">
        <div class="col-span-full flex items-center justify-center max-lg:py-[62px]">
            <div class="px-[32px] md:px-[64px] max-w-md w-full space-y-6">
                <div class="headline-1">Đổi mật khẩu</div>
                <FlashMessages />
                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <div>
                        <label class="block mb-1 font-medium text-gray-700">Mật khẩu mới</label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full border p-[10px] rounded-[8px] focus:outline-none focus:ring-2 focus:ring-primary"
                            :class="errors.password ? 'border-red-500' : 'border-gray-300'"
                            placeholder="Nhập mật khẩu mới"
                            autocomplete="new-password"
                        />
                        <p v-if="errors.password" class="text-sm text-red-600 mt-1">{{ errors.password }}</p>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-700">Xác nhận mật khẩu mới</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="w-full border border-gray-300 p-[10px] rounded-[8px] focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="Nhập lại mật khẩu mới"
                            autocomplete="new-password"
                        />
                    </div>
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-red-fks px-5 py-3 text-sm font-medium text-white hover:opacity-90 focus:outline-none focus:ring-4 focus:ring-red-fks/50"
                        :disabled="form.processing"
                    >
                        Đổi mật khẩu
                    </button>
                </form>
                <div class="pt-4">
                    <Link
                        :href="route('users.profile.index')"
                        class="text-primary underline"
                    >
                        Quay lại trang thông tin cá nhân
                    </Link>
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
                password: '',
                password_confirmation: '',
            }),
        }
    },
    computed: {
        errors() {
            return this.$page.props.errors || {}
        },
    },
    methods: {
        submit() {
            this.form.post(route('users.reset.submit'), {
                preserveScroll: true,
            })
        },
    },
}
</script>
