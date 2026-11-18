<template>
    <div class="max-lg:hidden h-6 bg-[#2B40B6] flex items-center">
        <div class="container flex items-center justify-end">
            <div class="flex items-center xl:space-x-3 lg:space-x-2">
                <div class="flex space-x-1 body-3">
                    <div class="text-white">{{ tt('Hotline') }}</div>
                    <a
                        :href="`tel:${tt('0967732665')}`"
                        class="text-white lg:hover:text-green-default lg:duration-150"
                        >{{ tt('0967 732 665') }}</a
                    >
                </div>
                <div class="body-3">
                    <span class="text-white">{{ tt('Email') }}: </span>
                    <a
                        :href="'mailto:' + ($page.props.global?.general_email || 'info@dvktthanhkhang.vn')"
                        class="text-white lg:hover:text-green-default lg:duration-150"
                        >{{ $page.props.global?.general_email || 'info@dvktthanhkhang.vn' }}</a
                    >
                </div>
            </div>
        </div>
    </div>
</template>
