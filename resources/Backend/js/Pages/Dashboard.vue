<template>
    <Head title="Tổng quan" />
    <div class="px-6 py-8 bg-[#F8FAFC] min-h-screen">
        <!-- Dashboard Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Bảng điều khiển</h2>
                <p class="text-slate-500 text-sm">Chào mừng bạn trở lại! Đây là tóm tắt những gì đang diễn ra hôm nay.</p>
            </div>
            <div class="text-right">
                <div class="text-sm font-medium text-slate-400 uppercase tracking-wider">Hôm nay</div>
                <div class="text-slate-700 font-bold">{{ toDate(new Date(), 'dddd, DD/MM/YYYY') }}</div>
            </div>
        </div>

        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[2px] mb-4 flex items-center gap-2">
            <span class="w-8 h-[2px] bg-slate-200"></span>
            Dự báo & Hiệu suất tháng {{ new Date().getMonth() + 1 }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-title">Dự báo đơn mới</span>
                    <span class="pi pi-bolt text-amber-500"></span>
                </div>
                <div class="metric-value">
                    {{ metrics.forecast_orders }}
                    <span class="text-xs font-normal text-slate-400">/ đơn</span>
                </div>
                <div class="metric-footer">
                    <span :class="metrics.this_month_orders >= metrics.last_month_orders ? 'text-green-500' : 'text-red-500'">
                        {{ metrics.this_month_orders }} đơn hiện tại
                    </span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-amber-400 h-full transition-all duration-1000" 
                         :style="{ width: Math.min((metrics.this_month_orders / Math.max(metrics.forecast_orders, 1)) * 100, 100) + '%' }"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-title">Tỉ lệ chuyển đổi</span>
                    <span class="pi pi-sync text-blue-500"></span>
                </div>
                <div class="metric-value">
                    {{ metrics.conversion_rate }}%
                </div>
                <div class="metric-footer">
                    <span class="text-slate-500">Từ liên hệ sang đơn hàng</span>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-title">Tăng trưởng đơn</span>
                    <span class="pi pi-chart-line text-emerald-500"></span>
                </div>
                <div class="metric-value" :class="metrics.order_growth >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                    {{ metrics.order_growth > 0 ? '+' : '' }}{{ metrics.order_growth }}%
                </div>
                <div class="metric-footer">
                    <span class="text-slate-400 text-[10px] uppercase">So với tháng trước</span>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-title">Tăng trưởng liên hệ</span>
                    <span class="pi pi-users text-indigo-500"></span>
                </div>
                <div class="metric-value" :class="metrics.contact_growth >= 0 ? 'text-indigo-600' : 'text-rose-600'">
                    {{ metrics.contact_growth > 0 ? '+' : '' }}{{ metrics.contact_growth }}%
                </div>
                <div class="metric-footer">
                    <span class="text-slate-400 text-[10px] uppercase">So với tháng trước</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <!-- Main Chart: Course Orders -->
            <div class="col-span-12 lg:col-span-8">
                <div class="dashboard-card h-full">
                    <div class="card-header">
                        <div class="flex items-center gap-2">
                            <span class="header-icon bg-emerald-100 text-emerald-700"><i class="pi pi-chart-bar"></i></span>
                            <span class="font-bold">THỐNG KÊ ĐƠN HÀNG KHÓA HỌC</span>
                        </div>
                        <div class="filter-group">
                            <select v-model="periodType" @change="onPeriodChange" class="premium-select">
                                <option value="month">Từng tháng</option>
                                <option value="quarter">Từng quý</option>
                                <option value="year">Cả năm</option>
                            </select>
                            <select v-model="filterYear" @change="fetchStats" class="premium-select">
                                <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
                            </select>
                            <!-- Secondary filters -->
                            <select v-if="periodType === 'quarter'" v-model="filterQuarter" @change="fetchStats" class="premium-select-alt">
                                <option :value="null">Tất cả quý</option>
                                <option v-for="q in 4" :key="q" :value="q">Quý {{ q }}</option>
                            </select>
                            <select v-if="periodType === 'month'" v-model="filterMonth" @change="fetchStats" class="premium-select-alt">
                                <option :value="null">Chọn tháng</option>
                                <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="h-[400px]">
                            <Chart v-if="totalChartData.labels?.length" type="bar"
                                :data="totalChartData" :options="totalChartOptions" class="h-full w-full" />
                            <div v-else class="empty-chart">
                                <i class="pi pi-database mb-2 text-2xl text-slate-300"></i>
                                <p>Chưa có dữ liệu cho khoảng thời gian này</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Chart: Popular Courses -->
            <div class="col-span-12 lg:col-span-4">
                <div class="dashboard-card h-full">
                    <div class="card-header">
                        <div class="flex items-center gap-2">
                            <span class="header-icon bg-blue-100 text-blue-700"><i class="pi pi-bookmark-fill"></i></span>
                            <span class="font-bold">KHÓA HỌC NỔI BẬT</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="h-[400px]">
                            <Chart v-if="byCourseChartData.labels?.length" type="bar"
                                :data="byCourseChartData" :options="byCourseChartOptions" class="h-full w-full" />
                            <div v-else class="empty-chart">
                                <i class="pi pi-filter-slash mb-2 text-2xl text-slate-300"></i>
                                <p>Không có dữ liệu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demographics Section -->
            <div class="col-span-12">
                <div class="dashboard-card">
                    <div class="card-header">
                        <div class="flex items-center gap-2">
                            <span class="header-icon bg-slate-100 text-slate-700"><i class="pi pi-users"></i></span>
                            <span class="font-bold">PHÂN TÍCH ĐỐI TƯỢNG HỌC VIÊN</span>
                        </div>
                        <div class="filter-group">
                            <select v-model="majorYearFilterYear" @change="fetchMajorYearStats" class="premium-select">
                                <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <div class="w-1 h-3 bg-orange-400 rounded-full"></div>
                                Theo ngành học
                            </h4>
                            <div class="h-[300px]">
                                <Chart v-if="byMajorChartData.labels?.length" type="bar"
                                    :data="byMajorChartData" :options="horizontalBarOptions" class="h-full w-full" />
                                <div v-else class="empty-chart-sm">Chưa có dữ liệu</div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <div class="w-1 h-3 bg-purple-400 rounded-full"></div>
                                Theo năm học
                            </h4>
                            <div class="h-[300px]">
                                <Chart v-if="byYearLevelChartData.labels?.length" type="bar"
                                    :data="byYearLevelChartData" :options="horizontalBarOptions" class="h-full w-full" />
                                <div v-else class="empty-chart-sm">Chưa có dữ liệu</div>
                            </div>
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
            majorFilter: null,
            yearLevelFilter: null,
            majorYearFilterYear: currentYear,
            majorYearStats: null,
            majorYearLoading: false,
            majorOptions: ['Kiến trúc', 'Nội thất', 'Cảnh quan', 'Quy hoạch', 'Đô thị', 'Đồ họa', 'Khác'],
            yearLevelOptions: ['năm 1', 'năm 2', 'năm 3', 'năm 4', 'năm 5', 'Đã ra trường'],
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
        metrics() {
            return this.dashboardData.metrics ?? {
                this_month_orders: 0,
                forecast_orders: 0,
                order_growth: 0,
                contact_growth: 0,
                conversion_rate: 0,
                last_month_orders: 0,
            }
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
        byMajorChartData() {
            if (!this.majorYearStats?.byMajor || typeof this.majorYearStats.byMajor !== 'object')
                return { labels: [], datasets: [] }
            const labels = Object.keys(this.majorYearStats.byMajor)
            const values = Object.values(this.majorYearStats.byMajor)
            return {
                labels,
                datasets: [
                    {
                        label: 'Số đơn',
                        data: values,
                        backgroundColor: 'rgba(245, 158, 11, 0.6)',
                        borderColor: 'rgb(245, 158, 11)',
                        borderWidth: 1,
                    },
                ],
            }
        },
        byYearLevelChartData() {
            if (!this.majorYearStats?.byYearLevel || typeof this.majorYearStats.byYearLevel !== 'object')
                return { labels: [], datasets: [] }
            const labels = Object.keys(this.majorYearStats.byYearLevel)
            const values = Object.values(this.majorYearStats.byYearLevel)
            return {
                labels,
                datasets: [
                    {
                        label: 'Số đơn',
                        data: values,
                        backgroundColor: 'rgba(139, 92, 246, 0.6)',
                        borderColor: 'rgb(139, 92, 246)',
                        borderWidth: 1,
                    },
                ],
            }
        },
        horizontalBarOptions() {
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
        if (!this.stats) this.fetchStats()
        this.fetchMajorYearStats()
    },
    methods: {
        onPeriodChange() {
            this.filterQuarter = null
            this.filterMonth = null
            this.fetchStats()
        },
        async fetchStats() {
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
        async fetchMajorYearStats() {
            this.majorYearLoading = true
            const params = new URLSearchParams({ year: this.majorYearFilterYear })
            if (this.majorFilter) params.set('major', this.majorFilter)
            if (this.yearLevelFilter) params.set('year_level', this.yearLevelFilter)
            try {
                const url = this.route('admin.dashboard.major-year-stats') + '?' + params.toString()
                const res = await this.$axios.get(url)
                this.majorYearStats = res.data ?? res
            } catch (e) {
                this.majorYearStats = { byMajor: {}, byYearLevel: {} }
            } finally {
                this.majorYearLoading = false
            }
        },
    },
}
</script>

<style lang="scss" scoped>
.stat-card {
    @apply bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5 transition-all duration-300;
    
    &:hover {
        @apply shadow-md -translate-y-1 border-blue-100;
    }

    .stat-icon {
        @apply w-14 h-14 rounded-xl flex items-center justify-center text-xl transition-all duration-300;
    }

    .stat-content {
        .stat-label {
            @apply text-slate-400 text-xs font-bold uppercase tracking-wider mb-1;
        }
        .stat-value {
            @apply text-2xl font-black text-slate-800 leading-none mb-1;
        }
        .stat-trend {
            @apply text-[10px] font-bold flex items-center;
        }
    }
}

.dashboard-card {
    @apply bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden;

    .card-header {
        @apply px-6 py-4 border-b border-slate-50 flex flex-wrap items-center justify-between gap-4;
        
        .header-icon {
            @apply w-8 h-8 rounded-lg flex items-center justify-center text-sm;
        }

        span.font-bold {
            @apply text-slate-700 text-sm tracking-tight;
        }
    }

    .card-body {
        @apply p-6;
    }
}

.filter-group {
    @apply flex items-center gap-2;
}

.premium-select {
    @apply bg-slate-50 border-none rounded-lg text-xs font-bold text-slate-600 px-3 py-2 cursor-pointer transition-all focus:ring-2 focus:ring-blue-100 focus:bg-white;
}

.premium-select-alt {
    @apply bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-500 px-3 py-2 cursor-pointer transition-all hover:border-blue-300;
}

.metric-card {
    @apply bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col transition-all duration-300 relative overflow-hidden;
    
    &:hover {
        @apply shadow-md border-slate-200;
    }

    .metric-header {
        @apply flex items-center justify-between mb-3;
        
        .metric-title {
            @apply text-slate-400 text-[10px] font-black uppercase tracking-wider;
        }
    }

    .metric-value {
        @apply text-2xl font-black text-slate-800 mb-1 flex items-baseline gap-1;
    }

    .metric-footer {
        @apply text-[10px] font-bold mt-auto;
    }
}

.empty-chart {
    @apply h-full flex flex-col items-center justify-center text-slate-400 text-sm italic bg-slate-50/50 rounded-xl border border-dashed border-slate-200;
}

.empty-chart-sm {
    @apply h-full flex items-center justify-center text-slate-400 text-xs italic bg-slate-50/30 rounded-lg;
}
</style>
