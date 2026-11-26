<template>
    <button
        type="button"
        @click="$emit('click', $event)"
        class="flex items-center justify-center w-full h-12 duration-200 bg-orange-600 rounded-lg hover:bg-orange-500 md:h-14"
    >
        <div class="px-2 py-1 text-center text-white">
            <div class="font-bold">MUA NGAY</div>
            <div class="caption">
                Nhận hàng trả tiền, 30 ngày đổi trả giữ nguyên giá
            </div>
        </div>
    </button>
</template>
<script>
export default {
    name: 'ProductActionButton',
    emits: ['click'],
}
</script>
