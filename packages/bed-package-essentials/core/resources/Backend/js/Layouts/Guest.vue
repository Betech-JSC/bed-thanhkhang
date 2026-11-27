<template>
    <div class="relative flex flex-col items-center justify-center min-h-screen p-4 sm:p-6 overflow-hidden bg-[#FAFAFA] text-[#202939] selection:bg-[#005EA4] selection:text-white">
        <!-- Top Brand Accent Bar -->
        <div class="fixed inset-x-0 top-0 h-1.5 bg-gradient-to-r from-[#00284B] via-[#005EA4] to-[#0075E7] z-20 pointer-events-none"></div>

        <!-- Subtle Navy Backdrop Wash -->
        <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-[#00284B]/[0.06] via-[#005EA4]/[0.02] to-transparent pointer-events-none"></div>

        <!-- Content Slot -->
        <div class="relative z-10 w-full max-w-md my-auto">
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    name: 'GuestLayout',
}
</script>
