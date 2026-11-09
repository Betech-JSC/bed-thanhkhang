<template>
    <Head>
        <title>{{ (status || 404) + ' - ' + tt('Không tìm thấy trang') + ' | HH Maker' }}</title>
        <meta name="robots" content="noindex, nofollow" head-key="robots" />
        <meta name="googlebot" content="noindex, nofollow" />
    </Head>
    <div class="flex flex-col min-h-screen pt-16 pb-12 bg-white">
        <main class="flex flex-col justify-center flex-grow w-full px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <JPageError
                :title="tt('KHÔNG TÌM THẤY TRANG (404)')"
                :description="tt('Địa chỉ truy cập không tồn tại hoặc đã bị thay đổi. Quý khách vui lòng liên hệ hotline HH Maker: ')"
                phone="0906 636 425"
                :link="route('home')"
            >
            </JPageError>
        </main>
    </div>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'

export default {
    components: {
        Head,
    },
    props: {
        status: {
            type: Number,
            default: 404,
        },
    },
}
</script>
