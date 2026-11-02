<?php

namespace App\Http\Controllers\Backend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SupportInfoController extends Controller
{
    const SETTINGS_GROUP = 'support_info';

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $routeName = str_replace(current_locale() . '.', '', $request->route()->getName());
            if (!current_admin() || !current_admin()->can($routeName)) {
                abort(403);
            }
            return $next($request);
        })->except(['api', 'getSupportData']);
    }

    /**
     * Lấy dữ liệu mặc định khi admin chưa cấu hình.
     */
    public static function getDefaultSupportInfo(): array
    {
        return [
            'hotline'        => '0318148569',
            'hotline_label'  => 'Hotline hỗ trợ',
            'group_link'     => '',
            'group_label'    => 'Group hỗ trợ Zalo/Facebook',
            'policy_title'   => 'Chính sách hỗ trợ khách hàng',
            'policy_content' => 'Liên hệ hotline hoặc fanpage để được tư vấn sản phẩm và giải đáp thắc mắc.',
        ];
    }

    /**
     * Hiển thị trang cài đặt thông tin hỗ trợ.
     */
    public function index()
    {
        $data = settings()->group(self::SETTINGS_GROUP)->all();
        $defaults = self::getDefaultSupportInfo();

        $item = [
            'hotline'        => $data['hotline'] ?? $defaults['hotline'],
            'hotline_label'  => !empty($data['hotline_label']) ? $data['hotline_label'] : $defaults['hotline_label'],
            'group_link'     => $data['group_link'] ?? $defaults['group_link'],
            'group_label'    => !empty($data['group_label']) ? $data['group_label'] : $defaults['group_label'],
            'policy_title'   => !empty($data['policy_title']) ? $data['policy_title'] : $defaults['policy_title'],
            'policy_content' => !empty($data['policy_content']) ? $data['policy_content'] : $defaults['policy_content'],
            'is_visible'     => filter_var($data['is_visible'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ];

        return Inertia::render('SupportInfo/Index', [
            'item' => $item,
        ]);
    }

    /**
     * Lưu cài đặt thông tin hỗ trợ.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotline'        => 'nullable|string|max:50',
            'hotline_label'  => 'nullable|string|max:100',
            'group_link'     => 'nullable|url|max:500',
            'group_label'    => 'nullable|string|max:100',
            'policy_title'   => 'nullable|string|max:200',
            'policy_content' => 'nullable|string|max:5000',
            'is_visible'     => 'nullable|boolean',
        ], [
            'group_link.url' => 'Link nhóm hỗ trợ phải là URL hợp lệ (bắt đầu bằng https://).',
        ]);

        settings()->group(self::SETTINGS_GROUP)->set($validated);

        return back()->with('success', 'Đã lưu thông tin hỗ trợ thành công.');
    }

    /**
     * Lấy array data hỗ trợ công khai.
     */
    public function getSupportData(): array
    {
        $data = settings()->group(self::SETTINGS_GROUP)->all();
        $defaults = self::getDefaultSupportInfo();

        $isVisible = filter_var($data['is_visible'] ?? true, FILTER_VALIDATE_BOOLEAN);

        if (!$isVisible) {
            return ['visible' => false];
        }

        return [
            'visible'        => true,
            'hotline'        => !empty($data['hotline']) ? $data['hotline'] : $defaults['hotline'],
            'hotline_label'  => !empty($data['hotline_label']) ? $data['hotline_label'] : $defaults['hotline_label'],
            'group_link'     => !empty($data['group_link']) ? $data['group_link'] : $defaults['group_link'],
            'group_label'    => !empty($data['group_label']) ? $data['group_label'] : $defaults['group_label'],
            'policy_title'   => !empty($data['policy_title']) ? $data['policy_title'] : $defaults['policy_title'],
            'policy_content' => !empty($data['policy_content']) ? $data['policy_content'] : $defaults['policy_content'],
        ];
    }

    /**
     * API công khai: trả về thông tin hỗ trợ cho Frontend.
     */
    public function api()
    {
        return response()->json($this->getSupportData());
    }
}
