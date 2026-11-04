<template>
    <div class="relative flex flex-col items-center justify-center min-h-screen p-4 overflow-hidden bg-[#0B0D19] text-gray-100 selection:bg-[#2970FF] selection:text-white">
        <!-- Heated Bed Mesh Grid Pattern -->
        <div
            class="absolute inset-0 pointer-events-none opacity-40"
            :style="{
                backgroundImage: `
                    linear-gradient(to right, rgba(64, 73, 104, 0.18) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(64, 73, 104, 0.18) 1px, transparent 1px)
                `,
                backgroundSize: '40px 40px',
            }"
        ></div>

        <!-- Ambient Tech Glows -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#2970FF]/15 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#1E56CC]/20 rounded-full blur-[140px] pointer-events-none"></div>

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
