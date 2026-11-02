<?php

namespace Tests\Feature;

use App\Jobs\SyncCourseOrderToGoogleSheetJob;
use App\Models\Contact;
use App\Models\GoogleSheetSyncLog;
use App\Services\GoogleSheetSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GoogleSheetSyncTest extends TestCase
{
    use RefreshDatabase;

    protected GoogleSheetSyncService $syncService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->syncService = app(GoogleSheetSyncService::class);
    }

    public function test_build_row_data_correctly_formats_contact_information()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'  => 'Nguyễn Văn Test',
                'phone' => '0987654321',
                'email' => 'test@funky.vn',
                'course' => [
                    'title'          => 'Khóa học SketchUp Chuyên Nghiệp',
                    'price_original' => 3000000,
                    'price'          => 2250000,
                    'schedule'       => [
                        'day'  => 'Tối 2-4-6',
                        'time' => '19:00 - 21:30',
                        'schedule_discount' => [
                            ['price' => 10, 'title' => 'Ưu đãi đăng ký sớm']
                        ]
                    ],
                    'schedule_discount_index' => 0,
                    'groups' => [
                        'discount' => 15,
                        'title'    => 'Nhóm 2 người'
                    ],
                    'already_learned' => true,
                ],
                'voucher_code' => 'FUNKY2026',
                'Bạn có muốn'  => 'pay',
                'note'         => 'Muốn học thêm rendering',
            ]
        ]);

        $row = $this->syncService->buildRowData($contact);

        $this->assertCount(13, $row);
        $this->assertEquals('#' . $contact->id, $row[0]);
        $this->assertEquals('Nguyễn Văn Test', $row[2]);
        $this->assertEquals('0987654321', $row[3]);
        $this->assertEquals('test@funky.vn', $row[4]);
        $this->assertEquals('Khóa học SketchUp Chuyên Nghiệp', $row[5]);
        $this->assertStringContainsString('Tối 2-4-6', $row[6]);
        $this->assertEquals('3.000.000₫', $row[7]);
        $this->assertStringContainsString('Lịch học: -10%', $row[8]);
        $this->assertStringContainsString('Nhóm: -15%', $row[8]);
        $this->assertStringContainsString('HV cũ: -150.000₫', $row[8]);
        $this->assertStringContainsString('FUNKY2026', $row[8]);
        $this->assertEquals('2.250.000₫', $row[9]);
        $this->assertStringContainsString('Giữ slot', $row[10]);
        $this->assertEquals('Muốn học thêm rendering', $row[11]);
    }

    public function test_resolve_target_tab_matches_correct_course_sheet()
    {
        $existingTabs = ['Trang chủ', 'SKETCHUP', 'REVIT ARCHITECTURE', 'AUTOCAD', 'Chưa phân loại'];

        // Khớp chính xác hoặc theo từ khóa cốt lõi
        $tab1 = $this->syncService->resolveTargetTab('Khóa học SketchUp Pro K24', $existingTabs);
        $this->assertEquals('SKETCHUP', $tab1);

        $tab2 = $this->syncService->resolveTargetTab('Lớp Revit cơ bản đến nâng cao', $existingTabs);
        $this->assertEquals('REVIT ARCHITECTURE', $tab2);

        $tab3 = $this->syncService->resolveTargetTab('AutoCAD Triển khai bản vẽ', $existingTabs);
        $this->assertEquals('AUTOCAD', $tab3);

        // Khóa học mới chưa từng có tab
        $tab4 = $this->syncService->resolveTargetTab('Khóa học AI Midjourney Thiết kế mới', $existingTabs);
        $this->assertEquals(GoogleSheetSyncService::UNCLASSIFIED_TAB, $tab4);
    }

    public function test_find_existing_row_index_detects_order_code()
    {
        Http::fake([
            'https://sheets.googleapis.com/v4/spreadsheets/mock_sheet_id/values/*' => Http::response([
                'values' => [
                    ['Mã đơn', 'Ngày đăng ký', 'Họ tên'],
                    ['#101', '01/09/2026', 'Học viên A'],
                    ['#102', '02/09/2026', 'Học viên B'],
                    ['#105', '03/09/2026', 'Học viên C'],
                ]
            ], 200)
        ]);

        $rowIndex = $this->syncService->findExistingRowIndex('mock_sheet_id', 'SKETCHUP', '#102', 'mock_token');
        $this->assertEquals(3, $rowIndex); // Dòng 3 trong bảng tính (1-indexed)

        $notFoundIndex = $this->syncService->findExistingRowIndex('mock_sheet_id', 'SKETCHUP', '#999', 'mock_token');
        $this->assertNull($notFoundIndex);
    }

    public function test_status_change_to_confirmed_dispatches_sync_job()
    {
        Queue::fake();

        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => ['name' => 'Nguyễn Test Queue']
        ]);

        // Đổi trạng thái sang CONFIRMED
        $contact->update(['status' => Contact::STATUS_CONFIRMED]);

        Queue::assertPushed(SyncCourseOrderToGoogleSheetJob::class, function ($job) use ($contact) {
            return $job->contactId === $contact->id;
        });
    }

    public function test_status_change_to_processed_dispatches_sync_job()
    {
        Queue::fake();

        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => ['name' => 'Nguyễn Test Processed']
        ]);

        // Đổi trạng thái sang PROCESSED ("Đã xử lý")
        $contact->update(['status' => Contact::STATUS_PROCESSED]);

        Queue::assertPushed(SyncCourseOrderToGoogleSheetJob::class, function ($job) use ($contact) {
            return $job->contactId === $contact->id;
        });
    }

    public function test_sync_job_skips_non_course_contacts()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CONTACT,
            'status' => Contact::STATUS_NEW,
            'data'   => ['name' => 'Khách hàng liên hệ thường']
        ]);

        $job = new SyncCourseOrderToGoogleSheetJob($contact->id);
        $job->handle($this->syncService);

        // Không tạo log đồng bộ vì không phải TYPE_CHECKOUT_COURSE
        $this->assertEquals(0, GoogleSheetSyncLog::count());
    }

    public function test_sync_contact_records_failure_log_when_no_credentials()
    {
        config(['services.google.service_account_json_path' => '/non/existent/path.json']);

        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'Lê Lỗi Credentials',
                'course' => ['title' => 'Sketchup']
            ]
        ]);

        try {
            $this->syncService->syncContact($contact, 'mock_spreadsheet_id');
            $this->fail('Should throw exception when credentials missing');
        } catch (\Throwable $e) {
            $this->assertDatabaseHas('google_sheet_sync_logs', [
                'contact_id' => $contact->id,
                'status'     => GoogleSheetSyncLog::STATUS_FAILED,
            ]);
        }
    }

    public function test_sync_contact_end_to_end_successful_insert()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_CONFIRMED,
            'data'   => [
                'name'   => 'Trần Thành Công',
                'phone'  => '0912345678',
                'course' => ['title' => 'Sketchup Pro']
            ]
        ]);

        Http::fake([
            'https://sheets.googleapis.com/v4/spreadsheets/mock_sheet_id?fields=sheets.properties' => Http::response([
                'sheets' => [
                    ['properties' => ['sheetId' => 0, 'title' => 'SKETCHUP']],
                    ['properties' => ['sheetId' => 1, 'title' => 'Chưa phân loại']],
                ]
            ], 200),
            'https://sheets.googleapis.com/v4/spreadsheets/mock_sheet_id/values/SKETCHUP!A:A' => Http::response([
                'values' => [
                    ['Mã đơn'],
                    ['#9999'],
                ]
            ], 200),
            'https://sheets.googleapis.com/v4/spreadsheets/mock_sheet_id/values/SKETCHUP!A:A:append*' => Http::response([
                'updates' => ['updatedRows' => 1]
            ], 200),
        ]);

        // Mock getAccessToken và getCredentials
        $service = new class extends GoogleSheetSyncService {
            public function getCredentials(): ?array {
                return ['client_email' => 'test@iam.gserviceaccount.com', 'private_key' => 'mock_key'];
            }
            public function getAccessToken(array $credentials): string {
                return 'mock_access_token';
            }
        };

        $result = $service->syncContact($contact, 'mock_sheet_id');

        $this->assertTrue($result['success']);
        $this->assertEquals('SKETCHUP', $result['tab']);
        $this->assertEquals(GoogleSheetSyncLog::ACTION_INSERT, $result['action']);

        $this->assertDatabaseHas('google_sheet_sync_logs', [
            'contact_id' => $contact->id,
            'sheet_tab'  => 'SKETCHUP',
            'action'     => 'INSERT',
            'status'     => 'SUCCESS',
        ]);

        $contact->refresh();
        $this->assertEquals('SUCCESS', $contact->data['google_sheet_sync']['status']);
        $this->assertEquals('SKETCHUP', $contact->data['google_sheet_sync']['tab']);
    }
}
