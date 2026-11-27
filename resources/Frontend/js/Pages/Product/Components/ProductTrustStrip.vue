<template>
    <section class="bg-[#FAFAFA] py-6 md:py-8">
        <div class="app-container-narrow">
            <div
                class="bg-[#EEF2F6] rounded-[4px] p-4 lg:px-5 lg:py-0 grid grid-cols-1 sm:grid-cols-2 lg:flex lg:items-center lg:justify-between gap-3 md:gap-4"
            >
                <div
                    v-for="item in trustItems"
                    :key="item.id"
                    class="flex items-center gap-2 lg:p-[20px] p-1"
                >
                    <img
                        :src="item.icon"
                        alt=""
                        aria-hidden="true"
                        width="24"
                        height="24"
                        class="w-6 h-6 shrink-0"
                    />
                    <span class="text-[14px] font-bold leading-[1.6] text-[#00284B]">
                        {{ tt(item.label) }}
                    </span>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
const trustItems = [
    {
        id: 'return-policy',
        icon: '/assets/svg/icons/pdp-trust-return.svg',
        label: 'Đổi trả trong vòng 7 ngày',
    },
    {
        id: 'warranty',
        icon: '/assets/svg/icons/pdp-trust-warranty.svg',
        label: 'Bảo hành chính hãng',
    },
    {
        id: 'authentic',
        icon: '/assets/svg/icons/pdp-trust-authentic.svg',
        label: 'Sản phẩm mới 100%, nguyên seal',
    },
    {
        id: 'shipping',
        icon: '/assets/svg/icons/pdp-trust-shipping.svg',
        label: 'Giao toàn quốc 7 ngày/tuần',
    },
]
</script>
