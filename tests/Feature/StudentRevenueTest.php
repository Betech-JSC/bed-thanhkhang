<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentRevenueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_dashboard_displays_correct_kpis()
    {
        // 1. Học viên đã thanh toán đủ 3.000.000₫
        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'           => 'Học viên Đã Đóng Đủ',
                'phone'          => '0901111111',
                'payment_status' => 'PAID',
                'paid_amount'    => 3000000,
                'course'         => [
                    'title' => 'Khóa Học Sketchup',
                    'price' => 3000000,
                ]
            ]
        ]);

        // 2. Học viên đóng cọc 1.000.000₫ trên tổng 2.500.000₫ (công nợ 1.500.000₫)
        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_PROCESSED,
            'data'   => [
                'name'           => 'Học viên Đóng Cọc',
                'phone'          => '0902222222',
                'payment_status' => 'PARTIAL',
                'paid_amount'    => 1000000,
                'course'         => [
                    'title' => 'Khóa Học Revit',
                    'price' => 2500000,
                ]
            ]
        ]);

        // 3. Học viên mới chưa thanh toán 2.000.000₫ (công nợ 2.000.000₫)
        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'           => 'Học viên Chưa Đóng',
                'phone'          => '0903333333',
                'payment_status' => 'UNPAID',
                'paid_amount'    => 0,
                'course'         => [
                    'title' => 'Khóa Học 3DsMax',
                    'price' => 2000000,
                ]
            ]
        ]);

        $controller = app(\App\Http\Controllers\Backend\StudentRevenueController::class);
        $request = new \Illuminate\Http\Request();
        $response = $controller->index($request);

        $props = $response->toResponse($request)->getOriginalContent()->getData()['page']['props'];

        $this->assertEquals(3, $props['kpis']['total_students']);
        // Doanh thu thực thu = 3.000.000 + 1.000.000 = 4.000.000
        $this->assertEquals(4000000, $props['kpis']['total_revenue']);
        // Công nợ = 1.500.000 + 2.000.000 = 3.500.000
        $this->assertEquals(3500000, $props['kpis']['total_debt']);
        // Tỷ lệ hoàn tất = 1 / 3 = 33.3%
        $this->assertEquals(33.3, $props['kpis']['conversion_rate']);
        $this->assertEquals(1, $props['kpis']['paid_students_count']);
    }

    public function test_filter_by_course()
    {
        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'HV Sketchup',
                'course' => ['title' => 'Sketchup Pro', 'price' => 2000000]
            ]
        ]);

        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'HV Revit',
                'course' => ['title' => 'Revit Architecture', 'price' => 3000000]
            ]
        ]);

        $controller = app(\App\Http\Controllers\Backend\StudentRevenueController::class);

        $request = new \Illuminate\Http\Request(['course' => 'Sketchup Pro']);
        $response = $controller->index($request);
        $props = $response->toResponse($request)->getOriginalContent()->getData()['page']['props'];

        $this->assertEquals(1, $props['students']['total']);
        $this->assertEquals('HV Sketchup', $props['students']['data'][0]['name']);
    }

    public function test_filter_by_payment_status()
    {
        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'           => 'HV Đã Đóng',
                'payment_status' => 'PAID',
                'course'         => ['title' => 'Sketchup', 'price' => 2000000]
            ]
        ]);

        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'           => 'HV Chưa Đóng',
                'payment_status' => 'UNPAID',
                'course'         => ['title' => 'Sketchup', 'price' => 2000000]
            ]
        ]);

        $controller = app(\App\Http\Controllers\Backend\StudentRevenueController::class);

        $request = new \Illuminate\Http\Request(['payment_status' => 'PAID']);
        $response = $controller->index($request);
        $props = $response->toResponse($request)->getOriginalContent()->getData()['page']['props'];

        $this->assertEquals(1, $props['students']['total']);
        $this->assertEquals('HV Đã Đóng', $props['students']['data'][0]['name']);
    }

    public function test_filter_by_date_range_and_auto_swap()
    {
        $oldStudent = Contact::create([
            'type'       => Contact::TYPE_CHECKOUT_COURSE,
            'status'     => Contact::STATUS_CONFIRMED,
            'data'       => [
                'name'   => 'HV Cũ',
                'course' => ['title' => 'Sketchup', 'price' => 2000000]
            ]
        ]);
        Contact::where('id', $oldStudent->id)->update(['created_at' => Carbon::now()->subDays(20)]);

        $newStudent = Contact::create([
            'type'       => Contact::TYPE_CHECKOUT_COURSE,
            'status'     => Contact::STATUS_CONFIRMED,
            'created_at' => Carbon::now(),
            'data'       => [
                'name'   => 'HV Mới',
                'course' => ['title' => 'Sketchup', 'price' => 2000000]
            ]
        ]);

        $controller = app(\App\Http\Controllers\Backend\StudentRevenueController::class);

        // Trường hợp nhập ngược: start_date (ngày mai) > end_date (2 ngày trước) -> Hệ thống tự động hoán đổi an toàn
        $request = new \Illuminate\Http\Request([
            'start_date' => Carbon::now()->addDay()->format('Y-m-d'),
            'end_date'   => Carbon::now()->subDays(2)->format('Y-m-d'),
        ]);

        $response = $controller->index($request);
        $props = $response->toResponse($request)->getOriginalContent()->getData()['page']['props'];

        // Chỉ chứa HV mới trong 2 ngày gần đây, không chứa HV cũ 20 ngày trước
        $this->assertEquals(1, $props['students']['total']);
        $this->assertEquals('HV Mới', $props['students']['data'][0]['name']);
    }

    public function test_excel_export_returns_streamed_file()
    {
        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'           => 'Trần Xuất Excel',
                'phone'          => '0988776655',
                'email'          => 'excel@example.com',
                'payment_status' => 'PAID',
                'course'         => [
                    'title'          => 'Khóa Học Kiến Trúc Nội Thất',
                    'price'          => 3500000,
                    'price_original' => 4000000,
                ]
            ]
        ]);

        $controller = app(\App\Http\Controllers\Backend\StudentRevenueController::class);
        $request = new \Illuminate\Http\Request();
        $response = $controller->export($request);

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment;', $contentDisposition);
        $this->assertStringContainsString('.xlsx', $contentDisposition);
    }
}
