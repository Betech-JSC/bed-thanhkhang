<?php

namespace Tests\Feature;

use App\Jobs\SyncCourseOrderToGoogleSheetJob;
use App\Models\Contact;
use App\Models\ContactStatusLog;
use App\Models\User;
use App\Services\ContactWorkflowService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Tests\TestCase;

class ContactWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['sepay.reference_prefix' => 'FK']);
    }

    public function test_valid_transition_records_status_log_and_updates_status()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'   => 'Nguyễn Văn Học',
                'phone'  => '0912345678',
                'course' => ['title' => 'Sketchup Pro', 'price' => 2000000],
            ]
        ]);

        $service = app(ContactWorkflowService::class);

        // 1. Chuyển từ NEW sang RESPONSE (Đã liên hệ)
        $service->transition($contact, Contact::STATUS_RESPONSE, [
            'note'       => 'Đã gọi tư vấn ca học buổi tối',
            'admin_name' => 'Tư vấn viên A',
        ]);

        $contact->refresh();
        $this->assertEquals(Contact::STATUS_RESPONSE, $contact->status);

        $log = ContactStatusLog::where('contact_id', $contact->id)->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals(Contact::STATUS_NEW, $log->old_status);
        $this->assertEquals(Contact::STATUS_RESPONSE, $log->new_status);
        $this->assertEquals('Tư vấn viên A', $log->admin_name);
        $this->assertEquals('Đã gọi tư vấn ca học buổi tối', $log->note);
    }

    public function test_transition_to_confirmed_triggers_sheet_and_slip()
    {
        Queue::fake();

        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_RESPONSE,
            'data'   => [
                'name'   => 'Trần Thanh Toàn',
                'phone'  => '0987654321',
                'email'  => 'toan@example.com',
                'course' => ['title' => 'Revit Architecture', 'price' => 3000000],
            ]
        ]);

        $service = app(ContactWorkflowService::class);
        $service->transition($contact, Contact::STATUS_CONFIRMED, [
            'note' => 'Học viên đã nộp học phí',
        ]);

        $contact->refresh();
        $this->assertEquals(Contact::STATUS_CONFIRMED, $contact->status);

        // Kiểm tra trigger Google Sheet (GĐ1)
        Queue::assertPushed(SyncCourseOrderToGoogleSheetJob::class, function ($job) use ($contact) {
            return $job->contactId === $contact->id;
        });

        // Kiểm tra trigger Phiếu xác nhận (GĐ2)
        $this->assertCount(1, $contact->confirmationSlips);
        $slip = $contact->confirmationSlips->first();
        $this->assertEquals('Revit Architecture', $slip->course_title);
        $this->assertEquals(3000000, $slip->final_price);
    }

    public function test_free_transition_between_all_statuses_is_allowed()
    {
        // 1. Chuyển từ COMPLETED (Đã tốt nghiệp) sang CONFIRMED (Đã xác nhận)
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_COMPLETED,
            'data'   => [
                'name'   => 'Lê Đã Tốt Nghiệp',
                'phone'  => '0933333333',
                'course' => ['title' => '3DsMax', 'price' => 2000000],
            ]
        ]);

        $service = app(ContactWorkflowService::class);
        $service->transition($contact, Contact::STATUS_CONFIRMED);

        $contact->refresh();
        $this->assertEquals(Contact::STATUS_CONFIRMED, $contact->status);

        // 2. Chuyển tự do từ NEW sang COMPLETED
        $contact2 = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'   => 'Nguyễn Nhảy Cóc',
                'phone'  => '0933333334',
                'course' => ['title' => 'Sketchup', 'price' => 2000000],
            ]
        ]);
        $service->transition($contact2, Contact::STATUS_COMPLETED);
        $contact2->refresh();
        $this->assertEquals(Contact::STATUS_COMPLETED, $contact2->status);
    }

    public function test_cancel_transition_without_reason_assigns_default_reason()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'   => 'Phạm Hủy Đơn',
                'phone'  => '0944444444',
                'course' => ['title' => 'Rhino', 'price' => 2000000],
            ]
        ]);

        $service = app(ContactWorkflowService::class);

        // Chuyển sang CLOSE không truyền reason -> Tự gán lý do mặc định 'Quản trị viên cập nhật trạng thái'
        $service->transition($contact, Contact::STATUS_CLOSE, [
            'reason' => '',
        ]);

        $contact->refresh();
        $this->assertEquals(Contact::STATUS_CLOSE, $contact->status);

        $log = ContactStatusLog::where('contact_id', $contact->id)->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals('Quản trị viên cập nhật trạng thái', $log->reason);
    }

    public function test_quick_update_note_preserves_customer_note_and_updates_admin_note()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'   => 'Trần Học Viên',
                'phone'  => '0988776655',
                'note'   => 'Khách muốn học ca tối sau 19h',
                'course' => ['title' => 'Lumion Pro', 'price' => 2500000],
            ]
        ]);

        $controller = app(\App\Http\Controllers\Backend\ContactCheckoutCourseController::class);

        // Giáo vụ cập nhật ghi chú
        $request = new \Illuminate\Http\Request([
            'admin_note' => 'Đã gọi tư vấn và xếp vào lớp tối thứ 4',
        ]);
        $response = $controller->quickUpdateNote($request, $contact->id);

        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Đã gọi tư vấn và xếp vào lớp tối thứ 4', $responseData['admin_note']);
        $this->assertEquals('Khách muốn học ca tối sau 19h', $responseData['customer_note']);

        $contact->refresh();
        // Kiểm tra dữ liệu trong DB
        $this->assertEquals('Khách muốn học ca tối sau 19h', $contact->data['customer_note']);
        $this->assertEquals('Đã gọi tư vấn và xếp vào lớp tối thứ 4', $contact->data['admin_note']);
        $this->assertEquals('Đã gọi tư vấn và xếp vào lớp tối thứ 4', $contact->data['note_1']);

        // Kiểm tra transform()
        $transformed = $controller->transform($contact);
        $this->assertEquals('Khách muốn học ca tối sau 19h', $transformed->customer_note);
        $this->assertEquals('Đã gọi tư vấn và xếp vào lớp tối thứ 4', $transformed->admin_note);
        $this->assertEquals('Đã gọi tư vấn và xếp vào lớp tối thứ 4', $transformed->note);
    }

    public function test_optimistic_locking_prevents_race_condition()
    {
        $contact = Contact::create([
            'type'       => Contact::TYPE_CHECKOUT_COURSE,
            'status'     => Contact::STATUS_NEW,
            'created_at' => Carbon::now()->subMinutes(10),
            'updated_at' => Carbon::now()->subMinutes(5),
            'data'       => [
                'name'   => 'Đỗ Đồng Thời',
                'phone'  => '0955555555',
                'course' => ['title' => 'Vray', 'price' => 1500000],
            ]
        ]);

        $service = app(ContactWorkflowService::class);

        // Giả sử client gửi mốc updated_at cũ hơn DB (do đã có người khác cập nhật trước đó)
        $outdatedTime = Carbon::now()->subMinutes(10)->toISOString();

        $this->expectException(ConflictHttpException::class);
        $service->transition($contact, Contact::STATUS_RESPONSE, [
            'expected_updated_at' => $outdatedTime,
        ]);
    }

    public function test_update_payment_and_deposit_tracking()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'Hoàng Đặt Cọc',
                'phone'  => '0966666666',
                'course' => ['title' => 'Photoshop Kiến Trúc', 'price' => 2500000],
            ]
        ]);

        $controller = app(\App\Http\Controllers\Backend\ContactCheckoutCourseController::class);

        // Học viên cọc 1.000.000₫ trên tổng 2.500.000₫ và yêu cầu xuất phiếu mới
        $request = new \Illuminate\Http\Request([
            'paid_amount'     => 1000000,
            'payment_status'  => 'PARTIAL',
            'regenerate_slip' => true,
        ]);

        $response = $controller->updatePayment($request, $contact->id);

        $contact->refresh();
        $this->assertEquals(1000000, $contact->data['paid_amount']);
        $this->assertEquals('PARTIAL', $contact->data['payment_status']);

        // Kiểm tra phiếu xác nhận mới đã lưu đúng tiền cọc và công nợ
        $slip = $contact->confirmationSlips()->first();
        $this->assertNotNull($slip);
        $this->assertEquals(1000000, $slip->paid_amount);
        $this->assertEquals(1500000, $slip->debt_amount);
        $this->assertEquals('PARTIAL', $slip->payment_status);
        // Mã VietQR được tạo với số tiền công nợ còn lại (1.500.000₫)
        $this->assertStringContainsString('1500000', $slip->qr_url);
    }

    public function test_student_profile_aggregates_multiple_orders()
    {
        $phone = '0977889900';

        // Đơn 1: Khóa 1 đã hoàn thành học phí 2.000.000₫
        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'           => 'Vũ Học Nhiều Khóa',
                'phone'          => $phone,
                'email'          => 'vu@example.com',
                'paid_amount'    => 2000000,
                'payment_status' => 'PAID',
                'course'         => ['title' => 'Khóa Cơ Bản', 'price' => 2000000],
            ]
        ]);

        // Đơn 2: Khóa 2 đặt cọc 1.000.000₫ trên 3.000.000₫ (nợ 2.000.000₫)
        Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_PROCESSED,
            'data'   => [
                'name'           => 'Vũ Học Nhiều Khóa',
                'phone'          => $phone,
                'email'          => 'vu@example.com',
                'paid_amount'    => 1000000,
                'payment_status' => 'PARTIAL',
                'course'         => ['title' => 'Khóa Nâng Cao', 'price' => 3000000],
            ]
        ]);

        $controller = app(\App\Http\Controllers\Backend\StudentProfileController::class);
        $request = new \Illuminate\Http\Request();
        $response = $controller->show($request, $phone);

        $props = $response->toResponse($request)->getOriginalContent()->getData()['page']['props'];

        $this->assertEquals('Vũ Học Nhiều Khóa', $props['student']['name']);
        $this->assertEquals($phone, $props['student']['phone']);
        $this->assertEquals(2, $props['stats']['total_courses']);
        // Tổng học phí = 2.000.000 + 3.000.000 = 5.000.000₫
        $this->assertStringContainsString('5.000.000', $props['stats']['total_tuition_fmt']);
        // Tổng đã nộp = 2.000.000 + 1.000.000 = 3.000.000₫
        $this->assertStringContainsString('3.000.000', $props['stats']['total_paid_fmt']);
        // Công nợ = 2.000.000₫
        $this->assertStringContainsString('2.000.000', $props['stats']['total_debt_fmt']);
        $this->assertTrue($props['stats']['has_debt']);
    }
}
