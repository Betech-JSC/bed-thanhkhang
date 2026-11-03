<template>
    <section class="py-4 md:py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 bg-dark-surface/80 border border-dark-border rounded-2xl p-6 md:p-8 backdrop-blur-sm shadow-xl"
            >
                <div
                    v-for="(metric, idx) in displayMetrics"
                    :key="idx"
                    class="flex flex-col space-y-2 p-3 sm:p-4 rounded-xl transition-all duration-300 hover:bg-dark-card/60 group"
                    :class="idx < displayMetrics.length - 1 ? 'lg:border-r lg:border-dark-border/60' : ''"
                >
                    <div class="flex items-baseline gap-1">
                        <span
                            class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight font-sans group-hover:text-primary transition-colors"
                        >
                            {{ metric.value }}
                        </span>
                        <span class="text-xl sm:text-2xl font-bold text-primary">
                            {{ metric.suffix }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-secondary font-medium leading-snug">
                        {{ metric.label }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'HighlightMetrics',
    props: {
        metrics: {
            type: Array,
            default: () => [],
        },
    },
    computed: {
        displayMetrics() {
            if (this.metrics && this.metrics.length > 0) {
                return this.metrics
            }
            return [
                {
                    value: '10',
                    suffix: '+',
                    label: 'Năm hoạt động trong lĩnh vực in & chế tác 3D',
                },
                {
                    value: '500',
                    suffix: '+',
                    label: 'Mẫu mã và sản phẩm hoàn thiện thực tế',
                },
                {
                    value: '95',
                    suffix: '%',
                    label: 'Khách hàng và đối tác hài lòng với dịch vụ',
                },
                {
                    value: '0.05',
                    suffix: 'mm',
                    label: 'Độ chính xác gia công đạt chuẩn công nghiệp',
                },
            ]
        },
    },
}
</script>
