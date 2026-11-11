<?php

namespace App\Http\Controllers\Backend;

use App\Models\Contact;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!current_admin()->can('admin.dashboard.index')) {
            $permissions = current_admin()->getAllPermissions()->pluck('name')->toArray();
            foreach ($permissions as $perm) {
                if (\Illuminate\Support\Str::endsWith($perm, '.index')) {
                    $localizedRoute = current_locale() . '.' . $perm;
                    if (\Illuminate\Support\Facades\Route::has($localizedRoute)) {
                        return redirect()->route($localizedRoute);
                    } elseif (\Illuminate\Support\Facades\Route::has($perm)) {
                        return redirect()->route($perm);
                    }
                }
            }
            return abort(403);
        }

        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $year = (int) ($request->get('year') ?? now()->year);

        // 1. Thống kê Báo giá In 3D (CONTACT_FORM)
        $todayQuotes = 0;
        $thisMonthQuotes = 0;
        $lastMonthQuotes = 0;
        try {
            if (Schema::hasTable('contacts')) {
                $todayQuotes = Contact::where('type', 'CONTACT_FORM')
                    ->whereDate('created_at', $today)
                    ->count();

                $thisMonthQuotes = Contact::where('type', 'CONTACT_FORM')
                    ->where('created_at', '>=', $thisMonth)
                    ->count();

                $lastMonthQuotes = Contact::where('type', 'CONTACT_FORM')
                    ->whereBetween('created_at', [$lastMonth, $thisMonth])
                    ->count();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // 2. Thống kê Đơn hàng (Order nếu có)
        $todayOrders = 0;
        $thisMonthOrders = 0;
        $lastMonthOrders = 0;
        try {
            if (class_exists(Order::class) && Schema::hasTable('orders')) {
                $todayOrders = Order::whereDate('created_at', $today)->count();
                $thisMonthOrders = Order::where('created_at', '>=', $thisMonth)->count();
                $lastMonthOrders = Order::whereBetween('created_at', [$lastMonth, $thisMonth])->count();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // 3. Tính toán Hiệu suất & Dự báo
        $daysInMonth = Carbon::now()->daysInMonth;
        $dayOfMonth = Carbon::now()->day;

        $forecastQuotes = $dayOfMonth > 0 ? (int) round(($thisMonthQuotes / $dayOfMonth) * $daysInMonth) : 0;
        $quoteGrowth = $lastMonthQuotes > 0 ? (($thisMonthQuotes - $lastMonthQuotes) / $lastMonthQuotes) * 100 : 0;
        $orderGrowth = $lastMonthOrders > 0 ? (($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100 : 0;
        $conversionRate = $thisMonthQuotes > 0 ? ($thisMonthOrders / $thisMonthQuotes) * 100 : 0;

        // 4. Cơ cấu Dịch vụ In 3D quan tâm
        $serviceStats = $this->getServiceDistribution($year);

        // 5. Thống kê Báo giá theo tháng trong năm
        $quoteStats = $this->getQuoteStats($year);

        // 6. Danh sách Báo giá in 3D gần đây
        $recentQuotes = collect();
        try {
            if (Schema::hasTable('contacts')) {
                $recentQuotes = Contact::where('type', 'CONTACT_FORM')
                    ->latest('id')
                    ->take(8)
                    ->get()
                    ->map(function ($c) {
                        $d = $c->data ?? [];
                        if (!is_array($d)) {
                            $d = [];
                        }
                        $name = $d['Họ và tên'] ?? $d['Họ tên'] ?? $d['name'] ?? $d['full_name'] ?? 'Khách hàng';
                        $phone = $d['Số điện thoại'] ?? $d['Số ĐT'] ?? $d['phone'] ?? $d['telephone'] ?? '-';
                        $email = $d['Email'] ?? $d['email'] ?? ($c->data_contact->email ?? '-');
                        $service = $d['Dịch vụ quan tâm'] ?? $d['Dịch vụ'] ?? ($d['Service']['title'] ?? null) ?? $d['service'] ?? 'Tư vấn in 3D';
                        if (is_array($service)) {
                            $service = $service['title'] ?? 'Tư vấn in 3D';
                        }

                        $hasCad = !empty($c->images) || !empty($d['File đính kèm']) || !empty($d['files']);

                        return [
                            'id' => $c->id,
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $email,
                            'service' => $service,
                            'status' => $c->status,
                            'formatted_status' => $c->formatted_status ?: 'Mới',
                            'has_cad' => $hasCad,
                            'created_at' => $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-',
                        ];
                    });
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $dashboard = [
            'today_quote_count' => $todayQuotes,
            'today_order_count' => $todayOrders,
            'quote_stats' => $quoteStats,
            'service_distribution' => $serviceStats,
            'recent_quotes' => $recentQuotes,
            'metrics' => [
                'this_month_quotes' => $thisMonthQuotes,
                'forecast_quotes' => $forecastQuotes,
                'quote_growth' => round($quoteGrowth, 1),
                'this_month_orders' => $thisMonthOrders,
                'order_growth' => round($orderGrowth, 1),
                'conversion_rate' => round($conversionRate, 1),
                'last_month_quotes' => $lastMonthQuotes,
            ],
        ];

        return Inertia::render('Dashboard', [
            'dashboard' => $dashboard,
        ]);
    }

    /**
     * Thống kê số lượng báo giá theo từng tháng trong năm
     */
    private function getQuoteStats(int $year): array
    {
        $base = collect();
        try {
            if (Schema::hasTable('contacts')) {
                $base = Contact::where('type', 'CONTACT_FORM')
                    ->whereYear('created_at', $year)
                    ->orderBy('created_at')
                    ->get();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $labels = array_map(fn($m) => sprintf('T%02d', $m), range(1, 12));
        $fullLabels = array_map(fn($m) => sprintf('Tháng %d', $m), range(1, 12));

        $groups = $base->groupBy(fn($c) => sprintf('T%02d', $c->created_at->month));

        $totalByPeriod = [];
        foreach ($labels as $label) {
            $totalByPeriod[$label] = isset($groups[$label]) ? $groups[$label]->count() : 0;
        }

        return [
            'labels' => $labels,
            'fullLabels' => $fullLabels,
            'totalByPeriod' => $totalByPeriod,
            'total' => $base->count(),
        ];
    }

    /**
     * Phân loại cơ cấu dịch vụ in 3D theo từ khóa
     */
    private function getServiceDistribution(int $year): array
    {
        $contacts = collect();
        try {
            if (Schema::hasTable('contacts')) {
                $contacts = Contact::where('type', 'CONTACT_FORM')
                    ->whereYear('created_at', $year)
                    ->get();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $distribution = [
            'Gia công in 3D' => 0,
            'Thiết kế 3D & CAD' => 0,
            'Làm đồ custom & Quà tặng' => 0,
            'Tư vấn kỹ thuật khác' => 0,
        ];

        foreach ($contacts as $contact) {
            $category = $this->classifyService($contact);
            $distribution[$category] = ($distribution[$category] ?? 0) + 1;
        }

        $total = array_sum($distribution);
        $percentages = [];
        foreach ($distribution as $key => $count) {
            $percentages[$key] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
        }

        return [
            'counts' => $distribution,
            'percentages' => $percentages,
            'total' => $total,
        ];
    }

    private function classifyService(Contact $contact): string
    {
        $data = $contact->data ?? [];
        if (!is_array($data)) {
            return 'Tư vấn kỹ thuật khác';
        }

        $text = ($data['Dịch vụ quan tâm'] ?? '') . ' ' .
                ($data['Dịch vụ'] ?? '') . ' ' .
                ($data['Service']['title'] ?? '') . ' ' .
                ($data['service'] ?? '') . ' ' .
                ($data['Nội dung cần hỗ trợ'] ?? '') . ' ' .
                ($data['note'] ?? '') . ' ' .
                ($data['customer_note'] ?? '');
        $text = mb_strtolower($text);

        if (preg_match('/thiết kế|cad|dựng hình|scan 3d|reverse|tạo mẫu 3d/u', $text)) {
            return 'Thiết kế 3D & CAD';
        }
        if (preg_match('/custom|quà tặng|decor|cá nhân|móc khóa|đồ chơi|mô hình/u', $text)) {
            return 'Làm đồ custom & Quà tặng';
        }
        if (preg_match('/gia công|in 3d|fdm|sla|nhựa|resin|in sợi|sls|in mẫu/u', $text)) {
            return 'Gia công in 3D';
        }

        return 'Tư vấn kỹ thuật khác';
    }

    public function userGuide()
    {
        return Inertia::render('UserGuide');
    }
}
