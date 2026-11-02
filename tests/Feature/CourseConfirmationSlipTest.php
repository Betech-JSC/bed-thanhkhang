<?php

namespace Tests\Feature;

use App\Jobs\SendCourseConfirmationSlipEmailJob;
use App\Mail\CourseConfirmationSlipMail;
use App\Models\Contact;
use App\Models\CourseConfirmationSlip;
use App\Services\CourseConfirmationSlipService;
use App\Services\SePayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseConfirmationSlipTest extends TestCase
{
    use RefreshDatabase;

    protected CourseConfirmationSlipService $slipService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->slipService = app(CourseConfirmationSlipService::class);
    }

    public function test_validation_fails_when_tuition_is_zero_or_negative()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'   => 'Nguyễn Văn Test',
                'phone'  => '0901234567',
                'course' => [
                    'title' => 'Khóa Học Revit',
                    'price' => 0, // Học phí = 0
                ]
            ]
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Học phí thực thu phải lớn hơn 0');

        $this->slipService->generateSlip($contact);
    }

    public function test_validation_fails_when_missing_student_name_or_phone()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'   => '', // Thiếu tên
                'phone'  => '0901234567',
                'course' => [
                    'title' => 'Khóa Học 3DsMax',
                    'price' => 3500000,
                ]
            ]
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Họ tên học viên không được để trống');

        $this->slipService->generateSlip($contact);
    }

    public function test_generate_slip_creates_correct_sepay_reference_and_vietqr()
    {
        Mail::fake();

        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'Trần Hoàng An',
                'phone'  => '0987654321',
                'email'  => 'hoangan@example.com',
                'course' => [
                    'title'          => 'Khóa Học SketchUp Nâng Cao',
                    'price'          => 2800000,
                    'price_original' => 3500000,
                    'schedule'       => [
                        'day'  => 'T3-T5',
                        'time' => '18:30 - 21:00',
                    ]
                ]
            ]
        ]);

        $slip = $this->slipService->generateSlip($contact, null, false);

        $this->assertNotNull($slip);
        $this->assertEquals(1, $slip->version);
        $this->assertEquals("XN-{$contact->id}-V1", $slip->slip_code);

        // Kiểm tra mã chuyển khoản SePay (Prefix + C + contact_id có đệm)
        $prefix = config('sepay.course_reference_prefix', 'FK');
        $expectedRef = $prefix . 'C' . str_pad($contact->id, 5, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedRef, $slip->reference_code);

        // Kiểm tra QR URL chứa số tiền và mã chuyển khoản
        $this->assertStringContainsString('amount=2800000', $slip->qr_url);
        $this->assertStringContainsString("addInfo={$expectedRef}", $slip->qr_url);

        // Kiểm tra số tiền
        $this->assertEquals(3500000, $slip->original_price);
        $this->assertEquals(700000, $slip->discount_amount);
        $this->assertEquals(2800000, $slip->final_price);

        // Kiểm tra token bảo mật 64 ký tự
        $this->assertEquals(64, strlen($slip->download_token));
    }

    public function test_versioning_increments_when_re_generating_slip()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'Lê Thị Version',
                'phone'  => '0912345678',
                'email'  => 'leversion@example.com',
                'course' => [
                    'title' => 'Khóa Học Photoshop',
                    'price' => 1500000,
                ]
            ]
        ]);

        // Tạo phiếu lần 1 (v1)
        $slipV1 = $this->slipService->generateSlip($contact, null, false);
        $this->assertEquals(1, $slipV1->version);
        $this->assertEquals("XN-{$contact->id}-V1", $slipV1->slip_code);

        // Cập nhật thông tin đơn (ví dụ thêm voucher giảm giá còn 1.200.000₫)
        $data = $contact->data;
        $data['course']['price'] = 1200000;
        $contact->update(['data' => $data]);

        // Tạo phiếu lần 2 (v2)
        $slipV2 = $this->slipService->generateSlip($contact, null, false);
        $this->assertEquals(2, $slipV2->version);
        $this->assertEquals("XN-{$contact->id}-V2", $slipV2->slip_code);
        $this->assertEquals(1200000, $slipV2->final_price);

        // Cả 2 bản ghi đều tồn tại trong DB (lưu toàn bộ lịch sử)
        $this->assertEquals(2, $contact->confirmationSlips()->count());
        $this->assertEquals($slipV2->id, $contact->latest_confirmation_slip->id);
    }

    public function test_secure_token_access_control()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'Phạm Bảo Mật',
                'phone'  => '0933445566',
                'email'  => 'baomat@example.com',
                'course' => [
                    'title' => 'Khóa Học AutoCAD',
                    'price' => 2000000,
                ]
            ]
        ]);

        $slip = $this->slipService->generateSlip($contact, null, false);

        // 1. Truy cập bằng đúng token bảo mật -> thành công 200
        $response = $this->get(route('slips.view', ['token' => $slip->download_token]));
        $response->assertStatus(200);
        $response->assertSee($slip->slip_code);
        $response->assertSee('2.000.000');

        // 2. Truy cập bằng token giả mạo -> trả về 404 (chống đoán ID/IDOR)
        $fakeResponse = $this->get(route('slips.view', ['token' => 'invalid-fake-token-attempt']));
        $fakeResponse->assertStatus(404);

        $fakeDownload = $this->get(route('slips.download', ['token' => 'invalid-fake-token-attempt']));
        $fakeDownload->assertStatus(404);
    }

    public function test_send_slip_email_job_handles_invalid_email_gracefully()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'Hoàng Lỗi Email',
                'phone'  => '0944556677',
                'email'  => 'invalid-email-address', // Sai định dạng email
                'course' => [
                    'title' => 'Khóa Học Nội Thất',
                    'price' => 5000000,
                ]
            ]
        ]);

        $slip = $this->slipService->generateSlip($contact, null, false);

        // Chạy job gửi email
        $job = new SendCourseConfirmationSlipEmailJob($slip->id);
        $job->handle();

        $slip->refresh();

        // Không crash mà đánh dấu FAILED kèm lý do cụ thể
        $this->assertEquals(CourseConfirmationSlip::EMAIL_STATUS_FAILED, $slip->email_status);
        $this->assertStringContainsString('không hợp lệ', $slip->email_error);
    }

    public function test_send_slip_email_job_successfully_sends_mail()
    {
        Mail::fake();

        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'Đỗ Thành Đạt',
                'phone'  => '0977889900',
                'email'  => 'thanhdat@example.com',
                'course' => [
                    'title' => 'Khóa Học Kiến Trúc',
                    'price' => 4500000,
                ]
            ]
        ]);

        $slip = $this->slipService->generateSlip($contact, null, false);

        $job = new SendCourseConfirmationSlipEmailJob($slip->id);
        $job->handle();

        Mail::assertSent(CourseConfirmationSlipMail::class, function ($mail) use ($slip) {
            return $mail->hasTo('thanhdat@example.com') && $mail->slip->id === $slip->id;
        });

        $slip->refresh();
        $this->assertEquals(CourseConfirmationSlip::EMAIL_STATUS_SENT, $slip->email_status);
        $this->assertNotNull($slip->email_sent_at);
        $this->assertNull($slip->email_error);
    }
}
