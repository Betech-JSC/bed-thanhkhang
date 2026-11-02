<template>
    <Head title="Tổng quan" />

    <div class="max-w-screen-xl mx-auto my-8 w-full px-4">
        <!-- Thống kê đơn hàng khóa học -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div
                class="px-5 py-4 font-bold text-gray-700 bg-gray-50/80 border-b flex flex-wrap items-center justify-between gap-4"
            >
                <span>THỐNG KÊ ĐƠN HÀNG KHÓA HỌC</span>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-sm font-normal text-gray-500">Theo dõi theo:</span>
                    <select
                        v-model="periodType"
                        class="rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                        @change="onPeriodChange"
                    >
                        <option value="month">Theo tháng</option>
                        <option value="quarter">Theo quý</option>
                        <option value="year">Theo năm</option>
                    </select>
                    <select
                        v-model="filterYear"
                        class="rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                        @change="fetchStats"
                    >
                        <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
                    </select>
                    <select
                        v-if="periodType === 'quarter'"
                        v-model="filterQuarter"
                        class="rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                        @change="fetchStats"
                    >
                        <option :value="null">Tất cả quý</option>
                        <option v-for="q in 4" :key="q" :value="q">Quý {{ q }}</option>
                    </select>
                    <select
                        v-if="periodType === 'month'"
                        v-model="filterMonth"
                        class="rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                        @change="fetchStats"
                    >
                        <option :value="null">Cả năm</option>
                        <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
                    </select>
                    <button
                        type="button"
                        class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700"
                        @click="fetchStats"
                    >
                        Xem
                    </button>
                </div>
            </div>

            <div class="p-5 space-y-8">
                <!-- Biểu đồ tổng đơn theo kỳ -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Tổng đơn theo kỳ</h3>
                    <div class="h-80">
                        <Chart
                            v-if="totalChartData.labels && totalChartData.labels.length"
                            type="bar"
                            :data="totalChartData"
                            :options="totalChartOptions"
                            class="h-full w-full"
                        />
                        <div v-else class="h-full flex items-center justify-center text-gray-400 bg-gray-50 rounded-lg">
                            Chưa có dữ liệu trong khoảng thời gian này
                        </div>
                    </div>
                </div>

                <!-- Biểu đồ đơn theo từng khóa -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Đơn theo từng khóa học</h3>
                    <div class="h-80">
                        <Chart
                            v-if="byCourseChartData.labels && byCourseChartData.labels.length"
                            type="bar"
                            :data="byCourseChartData"
                            :options="byCourseChartOptions"
                            class="h-full w-full"
                        />
                        <div v-else class="h-full flex items-center justify-center text-gray-400 bg-gray-50 rounded-lg">
                            Chưa có dữ liệu trong khoảng thời gian này
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import Chart from 'primevue/chart'

export default {
    components: {
        Head,
        Chart,
    },
    props: {
        dashboard: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        const currentYear = new Date().getFullYear()
        return {
            periodType: 'month',
            filterYear: currentYear,
            filterQuarter: null,
            filterMonth: null,
            stats: null,
            loading: false,
            yearOptions: Array.from({ length: 6 }, (_, i) => currentYear - i),
        }
    },
    computed: {
        dashboardData() {
            const d =
                this.dashboard && Object.keys(this.dashboard).length
                    ? this.dashboard
                    : this.$page?.props?.dashboard ?? {}
            return d || {}
        },
        todayContact() {
            return this.dashboardData.today_contact_count ?? 0
        },
        todayApply() {
            return this.dashboardData.today_apply_count ?? 0
        },
        todayCourseOrder() {
            return this.dashboardData.today_course_order_count ?? 0
        },
        totalChartData() {
            if (!this.stats?.totalByPeriod) return { labels: [], datasets: [] }
            const labels = this.stats.labels || Object.keys(this.stats.totalByPeriod)
            const values = Array.isArray(labels) ? labels.map((l) => this.stats.totalByPeriod[l] ?? 0) : []
            return {
                labels,
                datasets: [
                    {
                        label: 'Số đơn',
                        data: values,
                        backgroundColor: 'rgba(16, 185, 129, 0.6)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1,
                    },
                ],
            }
        },
        totalChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                    },
                },
            }
        },
        byCourseChartData() {
            if (!this.stats?.byCourse || typeof this.stats.byCourse !== 'object') return { labels: [], datasets: [] }
            const byCourse = this.stats.byCourse
            const labels = Object.keys(byCourse)
            const values = Object.values(byCourse)
            return {
                labels,
                datasets: [
                    {
                        label: 'Số đơn',
                        data: values,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                    },
                ],
            }
        },
        byCourseChartOptions() {
            return {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                    },
                },
            }
        },
    },
    mounted() {
        this.stats = this.dashboardData.course_order_stats ?? null
        if (!this.stats && typeof this.route === 'function') this.fetchStats()
    },
    methods: {
        onPeriodChange() {
            this.filterQuarter = null
            this.filterMonth = null
            this.fetchStats()
        },
        async fetchStats() {
            if (typeof this.route !== 'function' || !this.$axios) return
            this.loading = true
            const params = new URLSearchParams({
                period: this.periodType,
                year: this.filterYear,
            })
            if (this.periodType === 'quarter' && this.filterQuarter) params.set('quarter', this.filterQuarter)
            if (this.periodType === 'month' && this.filterMonth) params.set('month', this.filterMonth)
            try {
                const url = this.route('admin.dashboard.course-order-stats') + '?' + params.toString()
                const res = await this.$axios.get(url)
                this.stats = res.data ?? res
            } catch (e) {
                this.stats = { totalByPeriod: {}, byCourse: {}, labels: [] }
            } finally {
                this.loading = false
            }
        },
    },
}
</script>

<style scoped>
.p-component-overlay-enter {
    animation: none !important;
}
</style>
