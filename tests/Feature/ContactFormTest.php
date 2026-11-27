<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Case 1a: Gửi thành công qua web form (Inertia / Web Redirect).
     * Kiểm tra HTTP status 302 redirect, flash message success, và bản ghi được lưu đúng vào DB.
     */
    public function test_submit_contact_form_successfully_via_web_redirect()
    {
        Notification::fake();

        $payload = [
            'contact' => [
                'type' => 'CONTACT_FORM',
                'data' => [
                    'Họ và tên' => 'Nguyễn Văn A',
                    'Số điện thoại' => '0906636425',
                    'Email' => 'nguyenvana@example.com',
                    'Dịch vụ quan tâm' => 'Gia công 3D',
                    'Service' => [
                        'title' => 'Gia công 3D'
                    ],
                    'Nội dung cần hỗ trợ' => 'Tôi cần báo giá in 3D 10 mô hình SLA chi tiết cao cho dự án thiết bị y tế.',
                ]
            ]
        ];

        $response = $this->from('/lien-he')->post('/contacts', $payload);

        // 1. Kiểm tra HTTP Status (Redirect 302 về trang trước)
        $response->assertStatus(302);
        $response->assertRedirect('/lien-he');

        // 2. Kiểm tra flash message thành công trong session
        $response->assertSessionHas('success', 'success');

        // 3. Kiểm tra bản ghi được lưu đúng trong DB (bảng contacts)
        $this->assertDatabaseCount('contacts', 1);

        $contact = Contact::first();
        $this->assertNotNull($contact);
        $this->assertEquals('CONTACT_FORM', $contact->type);
        $this->assertEquals(Contact::STATUS_NEW, $contact->status);
        $this->assertEquals('Nguyễn Văn A', $contact->data['Họ và tên']);
        $this->assertEquals('0906636425', $contact->data['Số điện thoại']);
        $this->assertEquals('nguyenvana@example.com', $contact->data['Email']);
        $this->assertEquals('Gia công 3D', $contact->data['Dịch vụ quan tâm']);
        $this->assertEquals('Tôi cần báo giá in 3D 10 mô hình SLA chi tiết cao cho dự án thiết bị y tế.', $contact->data['Nội dung cần hỗ trợ']);
    }

    /**
     * Case 1b: Gửi form qua Inertia request (kèm header X-Inertia: true).
     * BẮT BUỘC nhận về Inertia Redirect 302 kèm session flash, TUYỆT ĐỐI không trả plain JSON response.
     */
    public function test_submit_contact_form_successfully_via_inertia_request()
    {
        Notification::fake();

        $payload = [
            'name' => 'Nguyễn Văn Inertia',
            'phone' => '0901234567',
            'email' => 'inertia@example.com',
            'service' => 'Thiết kế 3D',
            'content' => 'Gửi yêu cầu thiết kế qua Inertia form useForm.',
            'contact' => [
                'type' => 'CONTACT_FORM',
                'data' => [
                    'Họ và tên' => 'Nguyễn Văn Inertia',
                    'Số điện thoại' => '0901234567',
                    'Email' => 'inertia@example.com',
                    'Dịch vụ quan tâm' => 'Thiết kế 3D',
                    'Service' => [
                        'title' => 'Thiết kế 3D'
                    ],
                    'Nội dung cần hỗ trợ' => 'Gửi yêu cầu thiết kế qua Inertia form useForm.',
                ]
            ]
        ];

        $response = $this->from('/contact')->post('/contacts', $payload, [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        // Kiểm tra trả về 302 redirect (giao thức Inertia), KHÔNG trả plain JSON
        $response->assertStatus(302);
        $response->assertRedirect('/contact');
        $response->assertSessionHas('success', 'success');

        // Kiểm tra DB đã lưu thành công
        $this->assertDatabaseHas('contacts', [
            'type' => 'CONTACT_FORM',
            'status' => 'NEW',
        ]);

        $contact = Contact::where('data->Email', 'inertia@example.com')->first();
        $this->assertNotNull($contact);
        $this->assertEquals('Nguyễn Văn Inertia', $contact->data['Họ và tên']);
    }

    /**
     * Case 1c: Gửi thành công qua AJAX / JSON API (axios.post).
     * Kiểm tra HTTP status 200, JSON success=true, contact_id và bản ghi trong DB.
     */
    public function test_submit_contact_form_successfully_via_json_api()
    {
        Notification::fake();

        $payload = [
            'contact' => [
                'type' => 'CONTACT_FORM',
                'data' => [
                    'Họ và tên' => 'Trần Thị B',
                    'Số điện thoại' => '0908123890',
                    'Email' => 'tranthib@example.com',
                    'Dịch vụ quan tâm' => 'Thiết kế 3D',
                    'Service' => [
                        'title' => 'Thiết kế 3D'
                    ],
                    'Nội dung cần hỗ trợ' => 'Yêu cầu tư vấn thiết kế 3D CAD từ bản vẽ 2D mẫu đồ gá cơ khí.',
                ]
            ]
        ];

        $response = $this->postJson('/contacts', $payload);

        // 1. Kiểm tra HTTP Status 200 cho JSON API
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'contact_id',
        ]);

        // 2. Kiểm tra bản ghi được lưu đúng trong DB
        $contactId = $response->json('contact_id');
        $contact = Contact::find($contactId);
        $this->assertNotNull($contact);
        $this->assertEquals('CONTACT_FORM', $contact->type);
        $this->assertEquals(Contact::STATUS_NEW, $contact->status);
        $this->assertEquals('Trần Thị B', $contact->data['Họ và tên']);
        $this->assertEquals('0908123890', $contact->data['Số điện thoại']);
        $this->assertEquals('tranthib@example.com', $contact->data['Email']);
        $this->assertEquals('Thiết kế 3D', $contact->data['Dịch vụ quan tâm']);
        $this->assertEquals('Yêu cầu tư vấn thiết kế 3D CAD từ bản vẽ 2D mẫu đồ gá cơ khí.', $contact->data['Nội dung cần hỗ trợ']);
    }

    /**
     * Case 1d: Gửi dạng phẳng (flat payload) tương thích ContactSection ở trang chủ.
     */
    public function test_submit_contact_form_flat_payload_compatibility()
    {
        Notification::fake();

        $payload = [
            'type' => 'CONTACT_FORM',
            'name' => 'Lê Văn C',
            'phone' => '0987654310',
            'email' => 'levanc@example.com',
            'data' => [
                'Họ và tên' => 'Lê Văn C',
                'Số điện thoại' => '0987654310',
                'Email' => 'levanc@example.com',
                'Nội dung cần hỗ trợ' => 'Báo giá in 3D FDM số lượng lớn vật liệu PETG chống UV.',
            ]
        ];

        $response = $this->postJson('/contacts', $payload);
        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', [
            'type' => 'CONTACT_FORM',
            'status' => 'NEW',
        ]);
    }

    /**
     * Case 2a: Nguyên tắc an toàn tuyệt đối khi lỗi SMTP (530 5.7.0 Authentication required).
     * Khi SMTP mailer fail, lead BẮT BUỘC vẫn được lưu vào DB an toàn và khách hàng vẫn nhận thông báo thành công.
     */
    public function test_contact_submission_resilient_when_smtp_fails()
    {
        // Giả lập Notification::route ném ngoại lệ mô phỏng SMTP 530 Authentication required
        Notification::shouldReceive('route')
            ->andReturnUsing(function () {
                throw new \Exception('Expected response code "250" but got code "530", with message "530 5.7.0 Authentication required"');
            });

        $payload = [
            'name' => 'Khách Hàng Thử Thách SMTP',
            'phone' => '0933112233',
            'email' => 'khachhang.smtp@example.com',
            'service' => 'Gia công 3D',
            'content' => 'Kiểm tra hệ thống có bị sập khi SMTP không gửi được mail hay không.',
            'contact' => [
                'type' => 'CONTACT_FORM',
                'data' => [
                    'Họ và tên' => 'Khách Hàng Thử Thách SMTP',
                    'Số điện thoại' => '0933112233',
                    'Email' => 'khachhang.smtp@example.com',
                    'Dịch vụ quan tâm' => 'Gia công 3D',
                    'Nội dung cần hỗ trợ' => 'Kiểm tra hệ thống có bị sập khi SMTP không gửi được mail hay không.',
                ]
            ]
        ];

        // 1. Kiểm tra qua luồng Inertia request
        $response = $this->from('/contact')->post('/contacts', $payload, [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        // Vẫn redirect 302 về trang trước với thông báo thành công, KHÔNG crash modal Inertia
        $response->assertStatus(302);
        $response->assertRedirect('/contact');
        $response->assertSessionHas('success', 'success');

        // BẮT BUỘC bản ghi liên hệ phải được lưu thành công trong database
        $this->assertDatabaseHas('contacts', [
            'type' => 'CONTACT_FORM',
            'status' => 'NEW',
        ]);

        $savedContact = Contact::where('data->Email', 'khachhang.smtp@example.com')->first();
        $this->assertNotNull($savedContact);
        $this->assertEquals('Khách Hàng Thử Thách SMTP', $savedContact->data['Họ và tên']);
        $this->assertEquals('0933112233', $savedContact->data['Số điện thoại']);
    }

    /**
     * Case 2b: Kiểm tra từ chối email sai định dạng theo validation server-side của ContactController.
     */
    public function test_contact_submission_resilient_when_email_address_is_malformed()
    {
        $payload = [
            'contact' => [
                'type' => 'CONTACT_FORM',
                'data' => [
                    'Họ và tên' => 'Khách Hàng Email Lạ',
                    'Số điện thoại' => '0906636425',
                    'Email' => 'email-khong-hop-le@@',
                    'Nội dung cần hỗ trợ' => 'Cần in 3D chi tiết nhựa',
                ]
            ]
        ];

        $response = $this->from('/lien-he')->post('/contacts', $payload);

        $response->assertStatus(302);
        $response->assertRedirect('/lien-he');
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('contacts', 0);
    }

    /**
     * Case 2c: Kiểm tra phòng chống Spam (Blackwords).
     * Khi nội dung chứa từ khóa nằm trong danh sách đen, hệ thống tự động gán status IS_SPAM và soft-delete.
     */
    public function test_contact_with_blackwords_marked_as_spam_and_soft_deleted()
    {
        $payload = [
            'contact' => [
                'type' => 'CONTACT_FORM',
                'data' => [
                    'Họ và tên' => 'Spammer',
                    'Số điện thoại' => '0901234568',
                    'Email' => 'spam@test.com',
                    'Nội dung cần hỗ trợ' => 'Đây là tin nhắn lừa đảo tài chính',
                ]
            ]
        ];

        $response = $this->postJson('/contacts', $payload);
        $response->assertStatus(200);

        $contact = Contact::withTrashed()->latest('id')->first();
        $this->assertNotNull($contact);
        $this->assertEquals(Contact::STATUS_IS_SPAM, $contact->status);
        $this->assertNotNull($contact->deleted_at);
    }

    /**
     * Case 3a: Gửi payload phẳng hoàn toàn (flat payload không có key 'data'),
     * đảm bảo Controller tự động gom các trường và 'data' KHÔNG BAO GIỜ bị null.
     */
    public function test_contact_creation_from_completely_flat_keys_never_has_null_data()
    {
        Notification::fake();

        $payload = [
            'name' => 'Trần Văn Nam',
            'phone' => '0912345689',
            'email' => 'nam.tran@example.com',
            'service' => 'Gia công 3D',
            'content' => 'Tôi cần báo giá gấp 5 mẫu in SLA.',
        ];

        $response = $this->postJson('/contacts', $payload);
        $response->assertStatus(200);

        $contact = Contact::latest('id')->first();
        $this->assertNotNull($contact);
        $this->assertIsArray($contact->data);
        $this->assertEquals('Trần Văn Nam', $contact->data['Họ và tên']);
        $this->assertEquals('0912345689', $contact->data['Số điện thoại']);
        $this->assertEquals('nam.tran@example.com', $contact->data['Email']);
        $this->assertEquals('Gia công 3D', $contact->data['Dịch vụ quan tâm']);
        $this->assertEquals('Tôi cần báo giá gấp 5 mẫu in SLA.', $contact->data['Nội dung cần hỗ trợ']);
    }

    /**
     * Case 3b: Đảm bảo dữ liệu khách hàng (data) KHÔNG BAO GIỜ bị ghi đè thành null
     * khi Admin cập nhật giá tạm tính (estimated_price), ghi chú (internal_notes) hoặc trạng thái (status).
     */
    public function test_contact_data_never_overwritten_when_admin_updates_price_or_notes_or_status()
    {
        $contact = Contact::create([
            'type' => 'CONTACT_FORM',
            'data' => [
                'Họ và tên' => 'Khách Hàng Quan Trọng',
                'Số điện thoại' => '0909999999',
                'Email' => 'khachhang@example.com',
                'Dịch vụ quan tâm' => 'Thiết kế 3D',
                'Nội dung cần hỗ trợ' => 'Yêu cầu vẽ 3D chi tiết vỏ động cơ.',
            ],
            'status' => Contact::STATUS_NEW,
        ]);

        $this->assertNotNull($contact->data);

        // Giả lập admin cập nhật giá và ghi chú với data = null
        $contact->update([
            'estimated_price' => 500000,
            'internal_notes' => 'Kỹ thuật đã kiểm tra file 3D, chuẩn bị in SLA',
            'status' => Contact::STATUS_QUOTED,
            'data' => null,
        ]);

        $contact->refresh();

        // Kiểm tra dữ liệu data không bị mất
        $this->assertNotNull($contact->data);
        $this->assertIsArray($contact->data);
        $this->assertEquals('Khách Hàng Quan Trọng', $contact->data['Họ và tên']);
        $this->assertEquals('0909999999', $contact->data['Số điện thoại']);
        $this->assertEquals('500000.00', $contact->estimated_price);
        $this->assertEquals('Kỹ thuật đã kiểm tra file 3D, chuẩn bị in SLA', $contact->internal_notes);
        $this->assertEquals(Contact::STATUS_QUOTED, $contact->status);
    }

    /**
     * Case 4a: Chấp nhận các định dạng SĐT Việt Nam và quốc tế (+) hợp lệ, bao gồm placeholder 0901234567.
     *
     * @dataProvider validPhoneNumbersProvider
     */
    public function test_accepts_valid_vietnam_and_international_phone_numbers(string $phone)
    {
        Notification::fake();

        $payload = [
            'name' => 'Nguyễn Văn Hợp Lệ',
            'phone' => $phone,
            'email' => 'valid.phone@example.com',
            'content' => 'Kiểm tra nhận số điện thoại hợp lệ.',
        ];

        $response = $this->postJson('/contacts', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseCount('contacts', 1);

        $contact = Contact::find($response->json('contact_id'));
        $this->assertNotNull($contact);
        $this->assertEquals($phone, $contact->data['Số điện thoại']);
    }

    public function validPhoneNumbersProvider(): array
    {
        return [
            'vn mobile standard' => ['0906636425'],
            'vn mobile placeholder' => ['0901234567'],
            'vn mobile spaced placeholder' => ['0901 234 567'],
            'vn mobile plus 84' => ['+84 967 732 665'],
            'vn mobile 84 without plus' => ['84967732665'],
            'vn landline 11 digits' => ['024 3856 1234'],
            'vn landline 10 digits' => ['024 3856 123'],
            'vn landline plus 84' => ['+84 28 3856 1234'],
            'international us' => ['+1 415 555 2671'],
            'international uk' => ['+44 20 7946 0958'],
        ];
    }

    /**
     * Case 4b: Từ chối ngay SĐT chứa chữ cái, ký tự lạ hoặc dấu '+' đặt sai vị trí.
     * Bao gồm kiểm chứng KHÔNG tự ý xóa chữ cái trước khi kiểm tra độ dài (zero-stripping proof).
     *
     * @dataProvider alphabeticAndMalformedPhoneNumbersProvider
     */
    public function test_rejects_alphabetic_and_malformed_phone_numbers_without_stripping_letters(string $phone)
    {
        Notification::fake();

        $payload = [
            'name' => 'Khách Nhập Sai Ký Tự',
            'phone' => $phone,
            'email' => 'invalid.chars@example.com',
            'content' => 'Test từ chối ký tự chữ cái và định dạng sai.',
        ];

        $response = $this->postJson('/contacts', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => ['phone'],
        ]);
        $this->assertDatabaseCount('contacts', 0);
    }

    public function alphabeticAndMalformedPhoneNumbersProvider(): array
    {
        return [
            'letters in middle' => ['090123abc7'],
            'letters prefix' => ['abc0901234567'],
            'zero-stripping proof 10 valid digits plus trailing letter' => ['0901234567a'],
            'zero-stripping proof 11 digits plus letters' => ['09012345abc6789'],
            'unicode letters' => ['090123456đ'],
            'plus not at start' => ['09+01234567'],
            'double plus' => ['++84901234567'],
            'plus inside parentheses' => ['(+84) 901 234 567'],
            'disallowed symbol' => ['0901#234567'],
            'missing prefix' => ['123456789'],
            'international too short' => ['+1234567'],
            'international leading zero' => ['+0123456789'],
            'international too long' => ['+1234567890123456'],
            'vn too short' => ['09012345'],
            'empty spaces' => ['   '],
        ];
    }

    /**
     * Case 4c: Chặn triệt để SĐT giả/spam: toàn bộ giống nhau, lặp phần thuê bao, và dãy số tuần tự.
     *
     * @dataProvider spamAndFakePhoneNumbersProvider
     */
    public function test_rejects_repeated_and_sequential_fake_phone_numbers(string $phone)
    {
        Notification::fake();

        $payload = [
            'name' => 'Khách Nhập Số Giả',
            'phone' => $phone,
            'email' => 'fake.phone@example.com',
            'content' => 'Test chặn số giả hoặc tuần tự spam.',
        ];

        $response = $this->postJson('/contacts', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => ['phone'],
        ]);
        $this->assertDatabaseCount('contacts', 0);
    }

    public function spamAndFakePhoneNumbersProvider(): array
    {
        return [
            'all zeros' => ['0000000000'],
            'all ones' => ['1111111111'],
            'vn repeated tail 0900000000' => ['0900000000'],
            'vn repeated tail 0911111111' => ['0911111111'],
            'vn repeated 7-digit tail 0901111111' => ['0901111111'],
            'vn plus84 repeated tail' => ['+84900000000'],
            'international 6+ repeated consecutive digits' => ['+19999999999'],
            'sequential ascending 0123456789' => ['0123456789'],
            'sequential descending 9876543210' => ['9876543210'],
            'vn sequential 0912345678' => ['0912345678'],
            'vn sequential 0987654321' => ['0987654321'],
            'vn plus84 invalid sequential +8412345678' => ['+8412345678'],
            'vn plus84 sequential +84912345678' => ['+84912345678'],
            'international sequential +1234567890' => ['+1234567890'],
        ];
    }

    /**
     * Case 4d: Phản hồi lỗi Inertia redirect kèm session errors và thông báo song ngữ vi/en.
     */
    public function test_invalid_phone_returns_bilingual_errors_for_inertia_and_english_locale()
    {
        Notification::fake();

        // 1. Inertia request (vi) phải redirect 302 kèm session errors cho field 'phone'
        $inertiaResponse = $this->from('/lien-he')->post('/contacts', [
            'name' => 'Nguyễn Văn Test',
            'phone' => '090123abc7',
            'email' => 'inertia.fail@example.com',
        ], [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $inertiaResponse->assertStatus(302);
        $inertiaResponse->assertRedirect('/lien-he');
        $inertiaResponse->assertSessionHasErrors('phone');

        // 2. English endpoint (/en/contacts) phải trả lỗi tiếng Anh
        $enResponse = $this->postJson('/en/contacts', [
            'name' => 'John Doe',
            'phone' => '0900000000',
            'email' => 'john@example.com',
        ]);

        $enResponse->assertStatus(422);
        $enResponse->assertJson([
            'success' => false,
        ]);
        $enError = (string) $enResponse->json('errors.phone');
        $this->assertNotEmpty($enError);
        $this->assertMatchesRegularExpression('/phone number/i', $enError);

        $this->assertDatabaseCount('contacts', 0);
    }

    /**
     * Case 4e: Kiểm chứng rule ValidPhoneNumber hoạt động đúng chuẩn Laravel Validator InvokableRule.
     */
    public function test_valid_phone_number_rule_works_directly_with_laravel_validator()
    {
        $passing = Validator::make(
            ['phone' => '+84 967 732 665'],
            ['phone' => ['required', new ValidPhoneNumber('en')]]
        );
        $this->assertFalse($passing->fails());

        $failing = Validator::make(
            ['phone' => '0901234567a'],
            ['phone' => ['required', new ValidPhoneNumber('en')]]
        );
        $this->assertTrue($failing->fails());
        $this->assertNotEmpty($failing->errors()->first('phone'));
    }
}
