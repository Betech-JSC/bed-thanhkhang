<?php

namespace App\Http\Controllers\Backend;

use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
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

        // Current Month Stats
        $thisMonthContacts = Contact::where('type', 'CONTACT_FORM')
            ->where('created_at', '>=', $thisMonth)
            ->count();
        $thisMonthOrders = Contact::where('type', 'TYPE_CHECKOUT_COURSE')
            ->where('created_at', '>=', $thisMonth)
            ->count();

        // Last Month Stats
        $lastMonthContacts = Contact::where('type', 'CONTACT_FORM')
            ->whereBetween('created_at', [$lastMonth, $thisMonth])
            ->count();
        $lastMonthOrders = Contact::where('type', 'TYPE_CHECKOUT_COURSE')
            ->whereBetween('created_at', [$lastMonth, $thisMonth])
            ->count();

        // Calculations
        $daysInMonth = Carbon::now()->daysInMonth;
        $dayOfMonth = Carbon::now()->day;
        
        // Simple linear forecast
        $forecastOrders = $dayOfMonth > 0 ? (int)round(($thisMonthOrders / $dayOfMonth) * $daysInMonth) : 0;
        
        // Growth (MoM)
        $contactGrowth = $lastMonthContacts > 0 ? (($thisMonthContacts - $lastMonthContacts) / $lastMonthContacts) * 100 : 0;
        $orderGrowth = $lastMonthOrders > 0 ? (($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100 : 0;

        // Conversion Rate
        $conversionRate = $thisMonthContacts > 0 ? ($thisMonthOrders / $thisMonthContacts) * 100 : 0;

        $dashboard = [
            'today_contact_count' => (int) Contact::where('type', 'CONTACT_FORM')
                ->whereDate('created_at', $today)
                ->count(),
            'today_apply_count' => (int) Contact::where('type', 'APPLY_FORM')
                ->whereDate('created_at', $today)
                ->count(),
            'today_course_order_count' => (int) Contact::where('type', 'TYPE_CHECKOUT_COURSE')
                ->whereDate('created_at', $today)
                ->count(),
            'course_order_stats' => $this->getCourseOrderStats('month', $year),
            'metrics' => [
                'this_month_orders' => $thisMonthOrders,
                'forecast_orders' => $forecastOrders,
                'order_growth' => round($orderGrowth, 1),
                'contact_growth' => round($contactGrowth, 1),
                'conversion_rate' => round($conversionRate, 1),
                'last_month_orders' => $lastMonthOrders,
            ]
        ];

        return Inertia::render('Dashboard', [
            'dashboard' => $dashboard,
        ]);
    }

    public function courseOrderStats(Request $request)
    {
        if (!current_admin()->can('admin.dashboard.course-order-stats')) {
            return abort(403);
        }

        $period = $request->get('period', 'month');
        $year = (int) $request->get('year', now()->year);
        $quarter = $request->has('quarter') ? (int) $request->get('quarter') : null;
        $month = $request->has('month') ? (int) $request->get('month') : null;

        return response()->json($this->getCourseOrderStats($period, $year, $quarter, $month));
    }

    /**
     * @param string $period 'month' | 'quarter' | 'year'
     * @param int $year
     * @param int|null $quarter 1-4 khi period=quarter
     * @param int|null $month 1-12 khi period=month
     */
    private function getCourseOrderStats(string $period, int $year, ?int $quarter = null, ?int $month = null): array
    {
        $base = Contact::where('type', 'TYPE_CHECKOUT_COURSE');

        if ($period === 'year') {
            $base->whereYear('created_at', $year);
            $labels = array_map(fn($m) => sprintf('%d-%02d', $year, $m), range(1, 12));
            $groupKey = fn($c) => $c->created_at->format('Y-m');
        } elseif ($period === 'quarter') {
            if ($quarter) {
                $start = Carbon::createFromDate($year, ($quarter - 1) * 3 + 1, 1);
                $end = $start->copy()->endOfQuarter();
                $base->whereBetween('created_at', [$start, $end]);
                $labels = ['Q' . $quarter . '/' . $year];
                $groupKey = fn($c) => 'Q' . $quarter . '/' . $year;
            } else {
                $base->whereYear('created_at', $year);
                $labels = array_map(fn($q) => 'Q' . $q . '/' . $year, range(1, 4));
                $groupKey = fn($c) => 'Q' . (int) ceil($c->created_at->month / 3) . '/' . $c->created_at->year;
            }
        } else {
            if ($month) {
                $base->whereMonth('created_at', $month)->whereYear('created_at', $year);
                $lastDay = Carbon::createFromDate($year, $month, 1)->endOfMonth()->day;
                $labels = array_map(fn($d) => sprintf('%02d/%02d', $d, $month), range(1, $lastDay));
                $groupKey = fn($c) => $c->created_at->format('d/m');
            } else {
                $base->whereYear('created_at', $year);
                $labels = array_map(fn($m) => sprintf('%d-%02d', $year, $m), range(1, 12));
                $groupKey = fn($c) => $c->created_at->format('Y-m');
            }
        }

        $items = $base->orderBy('created_at')->get();
        $groups = $items->groupBy($groupKey);

        $totalByPeriod = [];
        foreach ($labels as $label) {
            $totalByPeriod[$label] = isset($groups[$label]) ? $groups[$label]->count() : 0;
        }

        $byCourse = $this->aggregateByCourse($items);

        return [
            'totalByPeriod' => $totalByPeriod,
            'byCourse' => $byCourse,
            'labels' => array_values($labels),
        ];
    }

    /**
     * @param  Collection<int, Contact>  $items
     */
    private function aggregateByCourse(Collection $items): array
    {
        $byCourse = [];
        foreach ($items as $c) {
            $name = $this->getCourseTitle($c);
            $byCourse[$name] = ($byCourse[$name] ?? 0) + 1;
        }
        arsort($byCourse);
        return $byCourse;
    }

    private function getCourseTitle(Contact $contact): string
    {
        $data = $contact->data;
        if (is_array($data) && isset($data['course']) && is_array($data['course']) && !empty($data['course']['title'])) {
            return $data['course']['title'];
        }
        return 'Không xác định';
    }

    public function majorYearStats(Request $request)
    {
        if (!current_admin()->can('admin.dashboard.major-year-stats')) {
            return abort(403);
        }

        $year = (int) $request->get('year', now()->year);
        $major = $request->get('major');
        $yearLevel = $request->get('year_level');

        $query = Contact::where('type', 'TYPE_CHECKOUT_COURSE')
            ->whereYear('created_at', $year);

        $items = $query->orderBy('created_at')->get();

        $byMajor = [];
        $byYearLevel = [];

        foreach ($items as $contact) {
            $data = $contact->data;
            if (!is_array($data)) continue;

            $contactMajor = $data['Ngành học hiện tại'] ?? 'Không xác định';
            $contactYearLevel = $data['Hiện tại bạn đang là'] ?? 'Không xác định';

            if ($major && $contactMajor !== $major) continue;
            if ($yearLevel && $contactYearLevel !== $yearLevel) continue;

            $byMajor[$contactMajor] = ($byMajor[$contactMajor] ?? 0) + 1;
            $byYearLevel[$contactYearLevel] = ($byYearLevel[$contactYearLevel] ?? 0) + 1;
        }

        arsort($byMajor);
        arsort($byYearLevel);

        return response()->json([
            'byMajor' => $byMajor,
            'byYearLevel' => $byYearLevel,
        ]);
    }
}
