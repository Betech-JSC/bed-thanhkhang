<template>
    <Head title="Bảng điều khiển - HH Maker" />
    <div class="px-6 py-8 bg-[#F8FAFC] min-h-screen">
        <!-- Dashboard Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-blue-600 rounded-full inline-block"></span>
                    Bảng điều khiển HH Maker 3D
                </h2>
                <p class="text-slate-500 text-sm mt-1">
                    Tổng quan hiệu suất yêu cầu báo giá in 3D, cơ cấu dịch vụ chế tác và đơn hàng.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('admin.user-guide.index')"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 hover:text-blue-600 font-semibold text-sm rounded-xl border border-slate-200 shadow-sm transition-all"
                >
                    <i class="pi pi-book text-blue-600"></i>
                    <span>Hướng dẫn sử dụng CMS</span>
                </Link>
                <div class="text-right bg-white px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hôm nay</div>
                    <div class="text-slate-700 font-bold text-sm">
                        {{ toDate(new Date(), 'dddd, DD/MM/YYYY') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Metric Cards -->
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[2px] mb-4 flex items-center gap-2">
            <span class="w-8 h-[2px] bg-slate-200"></span>
            Hiệu suất &amp; Chỉ số Báo giá Tháng {{ new Date().getMonth() + 1 }}
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Metric 1: Báo giá hôm nay -->
            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-title">Báo giá hôm nay</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="pi pi-send text-sm"></i>
                    </div>
                </div>
                <div class="metric-value text-blue-600">
                    {{ todayQuote }}
                    <span class="text-xs font-normal text-slate-400">/ yêu cầu</span>
                </div>
                <div class="metric-footer">
                    <span class="text-slate-500">
                        {{ metrics.this_month_quotes }} yêu cầu trong tháng
                    </span>
                </div>
            </div>

            <!-- Metric 2: Dự báo RFQ tháng -->
            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-title">Dự báo báo giá tháng</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center">
                        <i class="pi pi-bolt text-sm"></i>
                    </div>
                </div>
                <div class="metric-value">
                    {{ metrics.forecast_quotes }}
                    <span class="text-xs font-normal text-slate-400">/ dự kiến</span>
                </div>
                <div class="metric-footer">
                    <span class="text-slate-500">
                        Hiện có: <strong class="text-slate-700">{{ metrics.this_month_quotes }}</strong>
                    </span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div
                        class="bg-amber-400 h-full transition-all duration-1000"
                        :style="{
                            width:
                                Math.min(
                                    (metrics.this_month_quotes / Math.max(metrics.forecast_quotes, 1)) * 100,
                                    100
                                ) + '%',
                        }"
                    ></div>
                </div>
            </div>

            <!-- Metric 3: Tăng trưởng Báo giá MoM -->
            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-title">Tăng trưởng Báo giá</span>
                    <div
                        class="w-8 h-8 rounded-lg flex items-center justify-center"
                        :class="metrics.quote_growth >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'"
                    >
                        <i class="pi pi-chart-line text-sm"></i>
                    </div>
                </div>
                <div
                    class="metric-value"
                    :class="metrics.quote_growth >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                >
                    {{ metrics.quote_growth > 0 ? '+' : '' }}{{ metrics.quote_growth }}%
                </div>
                <div class="metric-footer">
                    <span class="text-slate-400 text-[10px] uppercase">
                        So với tháng trước ({{ metrics.last_month_quotes }} yêu cầu)
                    </span>
                </div>
            </div>

            <!-- Metric 4: Tỉ lệ chuyển đổi & Đơn hàng -->
            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-title">Đơn hàng Cửa hàng</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i class="pi pi-shopping-bag text-sm"></i>
                    </div>
                </div>
                <div class="metric-value text-indigo-600">
                    {{ metrics.this_month_orders }}
                    <span class="text-xs font-normal text-slate-400">/ đơn tháng này</span>
                </div>
                <div class="metric-footer">
                    <span class="text-slate-500">
                        Tỉ lệ chuyển đổi: <strong class="text-slate-700">{{ metrics.conversion_rate }}%</strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts Grid (12 cols) -->
        <div class="grid grid-cols-12 gap-6 mb-8">
            <!-- Main Chart: Quote RFQ Trend -->
            <div class="col-span-12 lg:col-span-8">
                <div class="dashboard-card h-full">
                    <div class="card-header">
                        <div class="flex items-center gap-2">
                            <span class="header-icon bg-blue-100 text-blue-700">
                                <i class="pi pi-chart-bar"></i>
                            </span>
                            <span class="font-bold uppercase tracking-tight">
                                YÊU CẦU BÁO GIÁ IN 3D NĂM {{ filterYear }}
                            </span>
                        </div>
                        <div class="filter-group">
                            <span class="text-xs text-slate-400 font-semibold">Năm:</span>
                            <select v-model="filterYear" @change="onYearChange" class="premium-select">
                                <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="h-[360px]">
                            <Chart
                                v-if="quoteChartData.labels?.length"
                                type="bar"
                                :data="quoteChartData"
                                :options="quoteChartOptions"
                                class="h-full w-full"
                            />
                            <div v-else class="empty-chart">
                                <i class="pi pi-database mb-2 text-2xl text-slate-300"></i>
                                <p>Chưa có dữ liệu báo giá cho năm {{ filterYear }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Chart: Service Distribution -->
            <div class="col-span-12 lg:col-span-4">
                <div class="dashboard-card h-full flex flex-col justify-between">
                    <div>
                        <div class="card-header">
                            <div class="flex items-center gap-2">
                                <span class="header-icon bg-cyan-100 text-cyan-700">
                                    <i class="pi pi-chart-pie"></i>
                                </span>
                                <span class="font-bold uppercase tracking-tight">CƠ CẤU DỊCH VỤ IN 3D</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="h-[230px] flex items-center justify-center">
                                <Chart
                                    v-if="servicePieData.labels?.length"
                                    type="doughnut"
                                    :data="servicePieData"
                                    :options="servicePieOptions"
                                    class="h-full w-full"
                                />
                                <div v-else class="empty-chart">
                                    <p>Chưa có dữ liệu dịch vụ</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown Table -->
                    <div class="px-6 pb-6 pt-2 border-t border-slate-100 space-y-2.5">
                        <div
                            v-for="(color, name) in serviceColors"
                            :key="name"
                            class="flex items-center justify-between text-xs"
                        >
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: color }"></span>
                                <span class="font-medium text-slate-700">{{ name }}</span>
                            </div>
                            <div class="font-bold text-slate-800">
                                {{ servicePercentages[name] || 0 }}%
                                <span class="text-slate-400 font-normal">({{ serviceCounts[name] || 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Recent Quotes (Báo giá in 3D gần đây) -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="flex items-center gap-2">
                    <span class="header-icon bg-indigo-100 text-indigo-700">
                        <i class="pi pi-list"></i>
                    </span>
                    <span class="font-bold uppercase tracking-tight">YÊU CẦU BÁO GIÁ IN 3D GẦN ĐÂY</span>
                </div>
                <Link
                    v-if="can('admin.contacts.index')"
                    :href="route('admin.contacts.index')"
                    class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors"
                >
                    <span>Xem tất cả</span>
                    <i class="pi pi-arrow-right text-[10px]"></i>
                </Link>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase text-slate-400 tracking-wider">
                            <th class="py-3 px-6">ID</th>
                            <th class="py-3 px-6">Khách hàng</th>
                            <th class="py-3 px-6">Số điện thoại</th>
                            <th class="py-3 px-6">Dịch vụ yêu cầu</th>
                            <th class="py-3 px-6 text-center">Tệp CAD</th>
                            <th class="py-3 px-6">Trạng thái</th>
                            <th class="py-3 px-6">Thời gian</th>
                            <th class="py-3 px-6 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <tr
                            v-for="quote in recentQuotes"
                            :key="quote.id"
                            class="hover:bg-slate-50/80 transition-colors"
                        >
                            <td class="py-3.5 px-6 font-mono text-xs text-slate-500">
                                #{{ quote.id }}
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-800">
                                {{ quote.name }}
                                <div class="text-xs text-slate-400 font-normal">{{ quote.email }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-slate-600">
                                <a
                                    v-if="quote.phone && quote.phone !== '-'"
                                    :href="`tel:${quote.phone}`"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{ quote.phone }}
                                </a>
                                <span v-else>-</span>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ quote.service }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span
                                    v-if="quote.has_cad"
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800"
                                >
                                    <i class="pi pi-box text-[10px]"></i> CAD
                                </span>
                                <span v-else class="text-xs text-slate-300">-</span>
                            </td>
                            <td class="py-3.5 px-6">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="getStatusBadgeClass(quote.status)"
                                >
                                    {{ quote.formatted_status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-400 whitespace-nowrap">
                                {{ quote.created_at }}
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <Link
                                    v-if="can('admin.contacts.form')"
                                    :href="route('admin.contacts.form', { id: quote.id })"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 text-xs font-semibold transition-colors"
                                >
                                    <span>Xem</span>
                                    <i class="pi pi-external-link text-[10px]"></i>
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!recentQuotes.length">
                            <td colspan="8" class="py-8 text-center text-slate-400 text-sm italic">
                                Chưa có yêu cầu báo giá in 3D nào được ghi nhận.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Chart from 'primevue/chart'

export default {
    components: {
        Head,
        Link,
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
            filterYear: currentYear,
            yearOptions: Array.from({ length: 5 }, (_, i) => currentYear - i),
            serviceColors: {
                'Gia công in 3D': '#2970FF',
                'Thiết kế 3D & CAD': '#06B6D4',
                'Làm đồ custom & Quà tặng': '#F59E0B',
                'Tư vấn kỹ thuật khác': '#64748B',
            },
        }
    },
    computed: {
        dashboardData() {
            return (
                this.dashboard && Object.keys(this.dashboard).length
                    ? this.dashboard
                    : this.$page?.props?.dashboard ?? {}
            ) || {}
        },
        todayQuote() {
            return this.dashboardData.today_quote_count ?? 0
        },
        metrics() {
            return (
                this.dashboardData.metrics ?? {
                    this_month_quotes: 0,
                    forecast_quotes: 0,
                    quote_growth: 0,
                    this_month_orders: 0,
                    order_growth: 0,
                    conversion_rate: 0,
                    last_month_quotes: 0,
                }
            )
        },
        quoteStats() {
            return this.dashboardData.quote_stats ?? { labels: [], totalByPeriod: {} }
        },
        serviceDistribution() {
            return (
                this.dashboardData.service_distribution ?? {
                    counts: {},
                    percentages: {},
                    total: 0,
                }
            )
        },
        serviceCounts() {
            return this.serviceDistribution.counts ?? {}
        },
        servicePercentages() {
            return this.serviceDistribution.percentages ?? {}
        },
        recentQuotes() {
            return this.dashboardData.recent_quotes ?? []
        },
        quoteChartData() {
            const labels = this.quoteStats.labels ?? []
            const dataMap = this.quoteStats.totalByPeriod ?? {}
            const values = labels.map((l) => dataMap[l] ?? 0)

            return {
                labels,
                datasets: [
                    {
                        label: 'Số yêu cầu báo giá',
                        data: values,
                        backgroundColor: 'rgba(41, 112, 255, 0.75)',
                        borderColor: '#2970FF',
                        borderRadius: 6,
                        borderWidth: 1,
                    },
                ],
            }
        },
        quoteChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        cornerRadius: 8,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: {
                            color: 'rgba(226, 232, 240, 0.6)',
                        },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            }
        },
        servicePieData() {
            const labels = Object.keys(this.serviceColors)
            const data = labels.map((name) => this.serviceCounts[name] || 0)
            const backgroundColor = labels.map((name) => this.serviceColors[name])

            return {
                labels,
                datasets: [
                    {
                        data,
                        backgroundColor,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    },
                ],
            }
        },
        servicePieOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
            }
        },
    },
    methods: {
        onYearChange() {
            this.$inertia.get(
                this.route('admin.dashboard.index'),
                { year: this.filterYear },
                { preserveState: true, replace: true }
            )
        },
        getStatusBadgeClass(status) {
            switch (status) {
                case 'NEW':
                    return 'bg-blue-100 text-blue-800'
                case 'RESPONSE':
                    return 'bg-amber-100 text-amber-800'
                case 'CONFIRMED':
                    return 'bg-purple-100 text-purple-800'
                case 'PROCESSED':
                    return 'bg-emerald-100 text-emerald-800'
                case 'CLOSE':
                    return 'bg-slate-100 text-slate-600'
                case 'IS_SPAM':
                    return 'bg-rose-100 text-rose-700'
                default:
                    return 'bg-slate-100 text-slate-700'
            }
        },
    },
}
</script>

<style lang="scss" scoped>
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
    @apply bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 px-3 py-1.5 cursor-pointer transition-all focus:ring-2 focus:ring-blue-100 focus:bg-white;
}

.metric-card {
    @apply bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col transition-all duration-300 relative overflow-hidden;

    &:hover {
        @apply shadow-md border-slate-200 -translate-y-0.5;
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
        @apply text-[11px] font-semibold mt-auto;
    }
}

.empty-chart {
    @apply h-full flex flex-col items-center justify-center text-slate-400 text-sm italic bg-slate-50/50 rounded-xl border border-dashed border-slate-200;
}
</style>
