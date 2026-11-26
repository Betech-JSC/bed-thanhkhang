<template>
    <div>
        <div class="p-1.5 md:p-2 bg-green-600 rounded-lg relative">
            <div class="flex items-center h-9 md:h-10">
                <div class="flex-1 h-full">
                    <input
                        type="number"
                        :value="form.contact.data.Phone"
                        @input="onChange($event.target.value)"
                        class="input-contact h-full py-[9.5px] pl-[12px] pr-1 rounded-l-lg caption rounded-r-none"
                        placeholder="Để lại số điện thoại đặt hàng, tư vấn"
                        autocomplete="off"
                        inputmode="numeric"
                        onkeypress="return event.charCode >= 48 && event.charCode =< 57"
                        onkeydown="return event.keyCode !== 69 && event.keyCode !== 190"
                    />
                </div>
                <button
                    type="button"
                    @click="onSubmitContact()"
                    :disabled="isLoading"
                    class="flex items-center justify-center w-[100px] h-full bg-yellow-500 hover:bg-yellow-700 duration-200 rounded-r-lg disabled:bg-gray-200 disabled:text-gray-400 disabled:pointer-events-none"
                >
                    <span class="text-white">{{ tt("GỬI") }}</span>

                    <IconLoading v-if="isLoading" />
                </button>
            </div>
            <div
                v-if="!validate"
                class="absolute bottom-0 text-red-500 translate-y-full description"
            >
                {{ tt("Số điện thoại không hợp lệ") }}
            </div>
        </div>
        <div
            v-if="showDesc && hotlineDisplay"
            class="text-center text-gray-700 caption-custom mt-3.5"
        >
            {{ tt("Hoặc đặt hàng trực tiếp qua hotline:") }}
            <a
                :href="'tel:' + hotlineClean"
                class="font-bold text-orange-500 caption-custom"
                >{{ hotlineDisplay }}</a
            >
            <br />
            {{ tt("(Miễn phí cước gọi)") }}
        </div>
    </div>

    <PopupSuccess :open="showPopupSuccess" @close="showPopupSuccess = false">
        <template v-slot:title>
            {{ tt("Gửi thông tin thành công") }}
        </template>
        <template v-slot:desc>
            {{
                tt(
                    "Đội ngũ kỹ thuật sẽ liên hệ quý khách trong thời gian sớm nhất"
                )
            }}
        </template>
    </PopupSuccess>
</template>
<script>
import { validateField } from "@/lib/validator";
import axios from "axios";
export default {
    props: {
        showDesc: {
            type: Boolean,
            default: true,
        },
        product: {
            type: Object,
            default: () => ({}),
        },
    },

    data() {
        const product =
            this.product && typeof this.product === "object" ? this.product : {};
        const { id, slug, title } = product;

        return {
            FORM_DATA_PRODUCT: { id, slug, title },
            rules: {
                phone: "required|phone|min:10|max:10",
            },
            errors: {},
            validate: true,
            showPopupSuccess: false,
            form: this.$inertia.form({
                contact: {
                    data: {
                        Phone: "",
                        Product: { id, slug, title },
                    },
                    type: "ADVISE_FORM",
                },
            }),
            isLoading: false,
        };
    },

    computed: {
        hotlineDisplay() {
            const g = this.$page?.props?.global || {};
            return g.hotline || g.general_phone || g.phone || "";
        },
        hotlineClean() {
            return String(this.hotlineDisplay).replace(/[^\d+]/g, "");
        },
    },

    methods: {
        safeRoute(name, params, fallback = "/") {
            if (typeof this.route === "function" && typeof this.routes === "function") {
                const allRoutes = this.routes() || {};
                const localizedName =
                    typeof this.routeLocaleName === "function" ? this.routeLocaleName(name) : name;
                if (allRoutes[localizedName] || allRoutes[name]) {
                    try {
                        const url = this.route(name, params);
                        if (url && url !== "#") return url;
                    } catch (e) {
                        // Fall through to fallback
                    }
                }
            }
            return fallback;
        },
        onChange(value) {
            this.form.contact.data.Phone = value;
            this.validate = validateField(value, this.rules.phone);
        },

        async onSubmitContact() {
            this.validate = validateField(
                this.form.contact.data.Phone,
                this.rules.phone
            );
            if (!this.validate || this.isLoading) return;

            this.isLoading = true;
            const BASE_URL = this.safeRoute("contact.store", undefined, "/contacts");
            const { status } = await axios.post(BASE_URL, this.form);

            if (status === 200) {
                this.form.contact.data.Phone = "";
                this.showPopupSuccess = true;
            }
            this.isLoading = false;
        },
    },
};
</script>
<style lang="scss" scoped>
.input-contact {
    @apply block w-full bg-white focus:ring-0 outline-none focus:outline-none;

    &::placeholder {
        @apply text-gray-400  leading-[130%];
    }
}
</style>
