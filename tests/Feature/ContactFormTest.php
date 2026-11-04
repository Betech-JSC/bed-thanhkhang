<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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
     * Case 1b: Gửi thành công qua AJAX / JSON API.
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
                    'Số điện thoại' => '0912345678',
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
        $this->assertEquals('0912345678', $contact->data['Số điện thoại']);
        $this->assertEquals('tranthib@example.com', $contact->data['Email']);
        $this->assertEquals('Thiết kế 3D', $contact->data['Dịch vụ quan tâm']);
        $this->assertEquals('Yêu cầu tư vấn thiết kế 3D CAD từ bản vẽ 2D mẫu đồ gá cơ khí.', $contact->data['Nội dung cần hỗ trợ']);
    }

    /**
     * Case 1c: Gửi dạng phẳng (flat payload) tương thích ContactSection ở trang chủ.
     */
    public function test_submit_contact_form_flat_payload_compatibility()
    {
        Notification::fake();

        $payload = [
            'type' => 'CONTACT_FORM',
            'name' => 'Lê Văn C',
            'phone' => '0987654321',
            'email' => 'levanc@example.com',
            'data' => [
                'Họ và tên' => 'Lê Văn C',
                'Số điện thoại' => '0987654321',
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
     * Case 2a: Kiểm tra khi email sai định dạng qua Web Form Submit.
     * Khi hệ thống gửi thông báo tới email không hợp lệ, Controller bắt lỗi và chuyển hướng kèm flash error.
     */
    public function test_validation_error_when_email_is_malformed_via_web()
    {
        $payload = [
            'contact' => [
                'type' => 'CONTACT_FORM',
                'data' => [
                    'Họ và tên' => 'Khách Hàng Lỗi Email',
                    'Số điện thoại' => '0906636425',
                    'Email' => 'email-khong-hop-le@@',
                    'Nội dung cần hỗ trợ' => 'Cần in 3D chi tiết nhựa',
                ]
            ]
        ];

        $response = $this->from('/lien-he')->post('/contacts', $payload);

        // Chuyển hướng lại trang form kèm error session
        $response->assertStatus(302);
        $response->assertRedirect('/lien-he');
        $response->assertSessionHasErrors(['error']);
    }

    /**
     * Case 2b: Kiểm tra khi email sai định dạng qua API JSON.
     * API trả về HTTP 500 kèm success=false và thông điệp lỗi định dạng RFC 2822.
     */
    public function test_validation_error_when_email_is_malformed_via_json()
    {
        $payload = [
            'contact' => [
                'type' => 'CONTACT_FORM',
                'data' => [
                    'Họ và tên' => 'Khách Hàng Lỗi Email',
                    'Số điện thoại' => '0906636425',
                    'Email' => 'invalid-email-format',
                    'Nội dung cần hỗ trợ' => 'Cần in 3D',
                ]
            ]
        ];

        $response = $this->postJson('/contacts', $payload);

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('RFC 2822', $response->json('message'));
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
                    'Số điện thoại' => '0900000000',
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
            'phone' => '0912345678',
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
        $this->assertEquals('0912345678', $contact->data['Số điện thoại']);
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
}
