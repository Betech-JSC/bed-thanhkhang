<?php

namespace Tests\Feature;

use App\Jobs\SendCourseConfirmationSlipEmailJob;
use App\Jobs\SyncCourseOrderToGoogleSheetJob;
use App\Models\Contact;
use App\Models\CourseClass;
use App\Models\CourseConfirmationSlip;
use App\Models\Post\Post;
use App\Models\Post\PostTranslation;
use App\Services\ClassAllocationService;
use App\Services\CourseConfirmationSlipService;
use App\Services\SePayService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoursePaymentAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected SePayService $sePayService;
    protected ClassAllocationService $allocationService;
    protected CourseConfirmationSlipService $slipService;
    protected string $webhookSecret = 'test_sepay_webhook_secret_key_123';

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--path' => 'database/migrations/blog']);

        Storage::fake('public');
        Mail::fake();
        Notification::fake();
        Queue::fake([
            SyncCourseOrderToGoogleSheetJob::class,
            SendCourseConfirmationSlipEmailJob::class,
        ]);

        config([
            'sepay.webhook_secret'   => $this->webhookSecret,
            'sepay.reference_prefix' => 'FK',
            'sepay.account_number'   => '1234567890',
            'sepay.bank_code'        => 'ACB',
        ]);

        $this->sePayService = app(SePayService::class);
        $this->allocationService = app(ClassAllocationService::class);
        $this->slipService = app(CourseConfirmationSlipService::class);
    }

    protected function createCourse(string $title = 'Khóa Học Kiến Trúc Revit', float $price = 2500000): Post
    {
        $course = Post::create([
            'type'   => 'COURSE',
            'status' => 'ACTIVE',
            'price'  => $price,
        ]);

        $tr = new PostTranslation();
        $tr->post_id = $course->id;
        $tr->locale = 'vi';
        $tr->status_locale = 'ACTIVE';
        $tr->title = $title;
        $tr->slug = 'khoa-hoc-revit-' . uniqid();
        $tr->schedule = [
            [
                'day'          => '15/09/2026',
                'time'         => 'T3 + T5 (19h30-21h30)',
                'mentor'       => 'KTS. Nguyễn Văn Thầy',
                'price'        => $price,
                'duration'     => '12 buổi',
                'max_students' => 15,
                'types'        => [['id' => 'Offline', 'title' => 'Offline']],
            ]
        ];
        $tr->save();

        return $course->fresh(['translations']);
    }

    protected function createCourseClass(Post $course, int $maxStudents = 15, int $enrolled = 0, string $status = CourseClass::STATUS_OPEN): CourseClass
    {
        return CourseClass::create([
            'course_id'         => $course->id,
            'class_code'        => 'RVT-TEST-' . uniqid(),
            'name'              => $course->title . ' (Lớp Test)',
            'class_type'        => 'OFFLINE',
            'schedule_time'     => 'T3 + T5 (19h30-21h30)',
            'start_date'        => '2026-09-15',
            'duration'          => '12 buổi',
            'mentor'            => 'KTS. Nguyễn Văn Thầy',
            'tuition_fee'       => $course->price,
            'max_students'      => $maxStudents,
            'enrolled_students' => $enrolled,
            'status'            => $status,
            'display_order'     => 1,
        ]);
    }

    protected function createContactOrder(Post $course, CourseClass $class, array $dataOverrides = []): Contact
    {
        $defaultData = [
            'name'           => 'Trần Văn Học Viên',
            'phone'          => '0987654321',
            'email'          => 'hocvien@example.com',
            'payment_status' => 'PENDING_PAYMENT',
            'course'         => [
                'id'             => $course->id,
                'class_id'       => $class->id,
                'title'          => $course->translations->first()->title ?? 'Khóa Học Kiến Trúc Revit',
                'slug'           => $course->translations->first()->slug ?? 'khoa-hoc-revit',
                'price'          => $course->price,
                'price_original' => $course->price,
                'schedule'       => [
                    'day'  => '15/09/2026',
                    'time' => 'T3 + T5 (19h30-21h30)',
                ],
            ],
        ];

        return Contact::create([
            'type'                    => Contact::TYPE_CHECKOUT_COURSE,
            'status'                  => Contact::STATUS_NEW,
            'class_id'                => null,
            'class_allocation_status' => Contact::ALLOCATION_NOT_ALLOCATED,
            'data'                    => array_merge($defaultData, $dataOverrides),
        ]);
    }

    /**
     * Case 1 (Happy Path): Webhook SePay hợp lệ, chuyển đủ tiền cho đơn khóa học (mã FKC{contact_id}).
     * - Contact chuyển sang trạng thái CONFIRMED và thanh toán PAID_FULL.
     * - Học viên được xếp đúng lớp học (CourseClass).
     * - Sĩ số lớp (enrolled_students) tăng chính xác +1.
     * - Phiếu xác nhận học viên (CourseConfirmationSlip) được sinh tự động.
     */
    public function test_case_1_happy_path_sepay_webhook_full_payment_and_auto_allocation()
    {
        $course = $this->createCourse('Khóa Học Revit Chuyên Nghiệp', 2500000);
        $class = $this->createCourseClass($course, 15, 0);
        $contact = $this->createContactOrder($course, $class);

        $refCode = $this->sePayService->generateReferenceCodeForCourse($contact);
        $this->assertEquals('FKC' . str_pad($contact->id, 5, '0', STR_PAD_LEFT), $refCode);

        $payload = [
            'id'              => 100001,
            'gateway'         => 'ACB',
            'transactionDate' => now()->format('Y-m-d H:i:s'),
            'accountNumber'   => '1234567890',
            'subAccount'      => null,
            'transferType'    => 'in',
            'transferAmount'  => 2500000,
            'accumulated'     => 2500000,
            'code'            => null,
            'content'         => "Thanh toan hoc phi {$refCode} CK SePay",
            'referenceCode'   => $refCode,
            'description'     => 'SePay Webhook Test Case 1',
        ];

        $response = $this->postJson(route('api.sepay.webhook'), $payload, [
            'Authorization' => "Apikey {$this->webhookSecret}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'type'    => 'COURSE',
            'status'  => 'PAID_FULL',
        ]);

        // 1. Kiểm tra Contact chuyển sang CONFIRMED và payment_status là PAID_FULL
        $contact->refresh();
        $this->assertEquals(Contact::STATUS_CONFIRMED, $contact->status, 'Đơn đăng ký phải chuyển sang CONFIRMED');
        $this->assertEquals('PAID_FULL', $contact->data['payment_status']);
        $this->assertEquals(2500000, $contact->data['paid_amount']);
        $this->assertEquals(0, $contact->data['debt_amount']);

        // 2. Kiểm tra học viên được xếp đúng lớp học
        $this->assertEquals($class->id, $contact->class_id, 'Học viên phải được gán đúng ID lớp học');
        $this->assertEquals(Contact::ALLOCATION_ALLOCATED, $contact->class_allocation_status, 'Trạng thái xếp lớp phải là ALLOCATED');

        // 3. Sĩ số lớp (enrolled_students) tăng chính xác +1
        $class->refresh();
        $this->assertEquals(1, $class->enrolled_students, 'Sĩ số lớp phải tăng từ 0 lên 1');

        // 4. Phiếu xác nhận học viên (CourseConfirmationSlip) được sinh tự động
        $slips = $contact->confirmationSlips()->get();
        $this->assertCount(1, $slips, 'Phải tự động sinh đúng 1 phiếu xác nhận học viên');

        $slip = $slips->first();
        $this->assertEquals($contact->id, $slip->contact_id);
        $this->assertEquals(2500000, $slip->final_price);
        $this->assertEquals(2500000, $slip->paid_amount);
        $this->assertEquals(0, $slip->debt_amount);
        $this->assertEquals('PAID_FULL', $slip->payment_status);
        $this->assertEquals($refCode, $slip->reference_code);
    }

    /**
     * Case 2 (Idempotency): Webhook SePay gửi lặp lại 2-3 lần liên tiếp với cùng transaction id hoặc nội dung.
     * - Hệ thống xử lý an toàn, chỉ tính 1 lần.
     * - Sĩ số lớp KHÔNG bị cộng dồn thành +2, +3.
     * - Không bị tạo trùng lặp phiếu xác nhận.
     */
    public function test_case_2_idempotency_prevents_duplicate_processing_and_over_enrollment()
    {
        $course = $this->createCourse('Khóa Học 3DsMax Idempotency', 3000000);
        $class = $this->createCourseClass($course, 15, 0);
        $contact = $this->createContactOrder($course, $class);

        $refCode = $this->sePayService->generateReferenceCodeForCourse($contact);
        $transactionId = 200002;

        $payload = [
            'id'              => $transactionId,
            'gateway'         => 'ACB',
            'transactionDate' => now()->format('Y-m-d H:i:s'),
            'accountNumber'   => '1234567890',
            'transferType'    => 'in',
            'transferAmount'  => 3000000,
            'content'         => "Thanh toan hoc phi {$refCode} idempotency test",
            'referenceCode'   => $refCode,
        ];

        // Lần 1: Xử lý thành công lần đầu
        $response1 = $this->postJson(route('api.sepay.webhook'), $payload, [
            'Authorization' => "Apikey {$this->webhookSecret}",
        ]);
        $response1->assertStatus(200);
        $response1->assertJson(['success' => true, 'status' => 'PAID_FULL']);

        $contact->refresh();
        $class->refresh();
        $this->assertEquals(1, $class->enrolled_students);
        $this->assertEquals(1, $contact->confirmationSlips()->count());

        // Lần 2: Webhook gửi lại cùng transaction ID (Retry từ SePay)
        $response2 = $this->postJson(route('api.sepay.webhook'), $payload, [
            'Authorization' => "Apikey {$this->webhookSecret}",
        ]);
        $response2->assertStatus(200);
        $response2->assertJson(['success' => true]);
        $this->assertStringContainsString('đối soát trước đó', $response2->json('message'));

        // Lần 3: Webhook gửi lại tiếp lần thứ 3
        $response3 = $this->postJson(route('api.sepay.webhook'), $payload, [
            'Authorization' => "Apikey {$this->webhookSecret}",
        ]);
        $response3->assertStatus(200);
        $response3->assertJson(['success' => true]);
        $this->assertStringContainsString('đối soát trước đó', $response3->json('message'));

        // Kiểm tra an toàn Idempotency:
        // Sĩ số lớp KHÔNG bị cộng dồn thành +2 hay +3, vẫn giữ đúng 1
        $class->refresh();
        $this->assertEquals(1, $class->enrolled_students, 'Sĩ số lớp tuyệt đối không bị cộng dồn sau khi retry webhook');

        // Số lượng phiếu xác nhận không bị tạo trùng lặp
        $contact->refresh();
        $this->assertEquals(1, $contact->confirmationSlips()->count(), 'Không được sinh trùng phiếu xác nhận');
    }

    /**
     * Case 3 (Số tiền sai / thiếu): Webhook gửi số tiền nhỏ hơn giá khóa học (ví dụ: chuyển 50,000đ thay vì giá khóa học).
     * - Đơn không được xác nhận hoàn tất (không full confirm).
     * - Ghi nhận số tiền đã nộp nhưng cảnh báo/thiếu học phí (UNDERPAID).
     * - Không tự ý xếp lớp và không làm tăng sĩ số lớp.
     */
    public function test_case_3_underpaid_webhook_does_not_confirm_and_records_debt()
    {
        $course = $this->createCourse('Khóa Học AutoCAD Thiếu Tiền', 2400000);
        $class = $this->createCourseClass($course, 15, 0);
        $contact = $this->createContactOrder($course, $class);

        $refCode = $this->sePayService->generateReferenceCodeForCourse($contact);

        // Khách chuyển 50,000đ (nhỏ hơn mức cọc tối thiểu 500,000đ và nhỏ hơn 2,400,000đ)
        $payload = [
            'id'              => 300003,
            'gateway'         => 'ACB',
            'transactionDate' => now()->format('Y-m-d H:i:s'),
            'accountNumber'   => '1234567890',
            'transferType'    => 'in',
            'transferAmount'  => 50000,
            'content'         => "Thanh toan hoc phi {$refCode} chuyen thieu tien",
            'referenceCode'   => $refCode,
        ];

        $response = $this->postJson(route('api.sepay.webhook'), $payload, [
            'Authorization' => "Apikey {$this->webhookSecret}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'status'  => 'UNDERPAID',
        ]);
        $this->assertStringContainsString('nhỏ hơn mức cọc tối thiểu', $response->json('message'));

        $contact->refresh();
        $class->refresh();

        // 1. Đơn không được xác nhận hoàn tất (vẫn giữ STATUS_NEW, không sang CONFIRMED)
        $this->assertNotEquals(Contact::STATUS_CONFIRMED, $contact->status, 'Đơn thiếu tiền không được tự động chuyển sang CONFIRMED');
        $this->assertEquals(Contact::STATUS_NEW, $contact->status);

        // 2. Ghi nhận số tiền đã nộp nhưng cảnh báo/thiếu học phí
        $this->assertEquals('UNDERPAID', $contact->data['payment_status']);
        $this->assertEquals(50000, $contact->data['paid_amount']);
        $this->assertEquals(2350000, $contact->data['debt_amount']);

        // 3. Học viên không được xếp lớp chính thức
        $this->assertNotEquals(Contact::ALLOCATION_ALLOCATED, $contact->class_allocation_status);

        // 4. Sĩ số lớp không tăng
        $this->assertEquals(0, $class->enrolled_students, 'Sĩ số lớp không được tăng khi học viên thanh toán thiếu');
    }

    /**
     * Case 4 (Race Condition & Class Overflow): Lớp học đã đầy (sĩ số = max_students = 15).
     * - Khi có người thanh toán hoặc xếp lớp vào lúc lớp đã chạm trần:
     *   + Hệ thống kiểm tra lockForUpdate() và xử lý trạng thái ngoại lệ an toàn ALLOCATION_OVERFLOW_PENDING.
     *   + Sĩ số lớp tuyệt đối không bị vượt ngưỡng (không bị 16/15).
     */
    public function test_case_4_race_condition_and_class_overflow_safeguard()
    {
        $course = $this->createCourse('Khóa Học Revit Đầy Lớp', 2500000);
        // Lớp đã chạm trần: sĩ số 15/15, status FULL
        $class = $this->createCourseClass($course, 15, 15, CourseClass::STATUS_FULL);
        $contact = $this->createContactOrder($course, $class);

        $refCode = $this->sePayService->generateReferenceCodeForCourse($contact);

        $payload = [
            'id'              => 400004,
            'gateway'         => 'ACB',
            'transactionDate' => now()->format('Y-m-d H:i:s'),
            'accountNumber'   => '1234567890',
            'transferType'    => 'in',
            'transferAmount'  => 2500000,
            'content'         => "Thanh toan hoc phi {$refCode} overflow race condition test",
            'referenceCode'   => $refCode,
        ];

        $response = $this->postJson(route('api.sepay.webhook'), $payload, [
            'Authorization' => "Apikey {$this->webhookSecret}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'type'    => 'COURSE',
            'status'  => 'PAID_FULL',
        ]);

        $contact->refresh();
        $class->refresh();

        // 1. Tiền đã nhận đủ, đơn chuyển sang CONFIRMED
        $this->assertEquals(Contact::STATUS_CONFIRMED, $contact->status);
        $this->assertEquals('PAID_FULL', $contact->data['payment_status']);

        // 2. Trạng thái phân bổ an toàn: ALLOCATION_OVERFLOW_PENDING
        $this->assertEquals(Contact::ALLOCATION_OVERFLOW_PENDING, $contact->class_allocation_status, 'Học viên phải nhận trạng thái OVERFLOW_PENDING');
        $this->assertEquals($class->id, $contact->class_id);

        // 3. Sĩ số lớp tuyệt đối không vượt trần (vẫn là 15, không bị 16/15)
        $this->assertEquals(15, $class->enrolled_students, 'Sĩ số lớp tuyệt đối không được vượt quá max_students (15/15)');
        $this->assertEquals(CourseClass::STATUS_FULL, $class->status);

        // 4. Kiểm tra thêm kịch bản tranh chấp slot cuối cùng (14/15) giữa 2 học viên
        $classContest = $this->createCourseClass($course, 15, 14, CourseClass::STATUS_OPEN);
        $studentA = $this->createContactOrder($course, $classContest, ['name' => 'Học Viên A']);
        $studentB = $this->createContactOrder($course, $classContest, ['name' => 'Học Viên B']);

        // Học viên A vào trước -> nhận slot 15/15
        $this->allocationService->allocateStudentToClass($studentA, $classContest->id);
        $studentA->refresh();
        $classContest->refresh();
        $this->assertEquals(Contact::ALLOCATION_ALLOCATED, $studentA->class_allocation_status);
        $this->assertEquals(15, $classContest->enrolled_students);
        $this->assertEquals(CourseClass::STATUS_FULL, $classContest->status);

        // Học viên B vào sau (tranh chấp slot khi đã đầy) -> rơi vào OVERFLOW_PENDING an toàn
        $this->allocationService->allocateStudentToClass($studentB, $classContest->id);
        $studentB->refresh();
        $classContest->refresh();
        $this->assertEquals(Contact::ALLOCATION_OVERFLOW_PENDING, $studentB->class_allocation_status);
        $this->assertEquals(15, $classContest->enrolled_students, 'Sĩ số vẫn là 15, không được thành 16');
    }

    /**
     * Case 5 (Chờ xếp lớp - Giữ chỗ không thanh toán): Đơn đăng ký với option waiting = 'wait'.
     * - Học viên nhận trạng thái tạm giữ chỗ ALLOCATION_HELD_PENDING.
     * - Có hold_expires_at (hạn 24h).
     * - Sĩ số lớp không bị tăng lên.
     */
    public function test_case_5_waiting_option_creates_held_pending_slot_with_24h_expiry()
    {
        $course = $this->createCourse('Khóa Học Photoshop Giữ Chỗ', 1800000);
        $class = $this->createCourseClass($course, 15, 0);

        // 1. Kiểm thử phân bổ giữ chỗ qua autoAllocateContact với isHolding = true
        $contactWaiting = $this->createContactOrder($course, $class, [
            'name'    => 'Lê Thị Chờ Lớp',
            'waiting' => 'wait',
        ]);

        $allocated = $this->allocationService->autoAllocateContact($contactWaiting, true);

        $this->assertNotNull($allocated);
        $this->assertEquals(Contact::ALLOCATION_HELD_PENDING, $allocated->class_allocation_status, 'Trạng thái phải là HELD_PENDING');
        $this->assertEquals($class->id, $allocated->class_id);

        // Kiểm tra thời hạn giữ chỗ 24h
        $this->assertNotEmpty($allocated->data['hold_expires_at']);
        $holdExpiresAt = Carbon::parse($allocated->data['hold_expires_at']);
        $this->assertTrue($holdExpiresAt->isFuture());
        $this->assertEqualsWithDelta(24, now()->diffInHours($holdExpiresAt), 1, 'Hạn giữ chỗ phải xấp xỉ 24 giờ');

        // Sĩ số lớp không tăng vì chỉ là tạm giữ chỗ chưa đóng tiền
        $class->refresh();
        $this->assertEquals(0, $class->enrolled_students, 'Sĩ số lớp không tăng khi chỉ tạm giữ chỗ');

        // 2. Kiểm thử luồng gửi yêu cầu đăng ký frontend với 'waiting' => 'wait'
        $postData = [
            'contact' => [
                'type' => Contact::TYPE_CHECKOUT_COURSE,
                'data' => [
                    'name'        => 'Hoàng Văn Chờ Lớp FE',
                    'phone'       => '0933221100',
                    'email'       => 'cholopfe@example.com',
                    'waiting'     => 'wait',
                    'Bạn có muốn' => 'Chờ xếp lớp',
                    'course'      => [
                        'id'       => $course->id,
                        'class_id' => $class->id,
                        'title'    => $course->translations->first()->title,
                        'slug'     => $course->translations->first()->slug,
                        'price'    => 1800000,
                    ],
                ],
            ],
        ];

        $feResponse = $this->post(route('vi.contact.store'), $postData);
        $this->assertTrue(in_array($feResponse->status(), [200, 302]), 'Đăng ký frontend thành công');

        $createdContact = Contact::where('type', Contact::TYPE_CHECKOUT_COURSE)
            ->where('data->email', 'cholopfe@example.com')
            ->first();

        $this->assertNotNull($createdContact);
        $this->assertEquals(Contact::ALLOCATION_HELD_PENDING, $createdContact->class_allocation_status);
        $this->assertEquals($class->id, $createdContact->class_id);
        $this->assertNotEmpty($createdContact->data['hold_expires_at']);

        // Sĩ số lớp vẫn giữ nguyên là 0
        $class->refresh();
        $this->assertEquals(0, $class->enrolled_students);
    }
}
