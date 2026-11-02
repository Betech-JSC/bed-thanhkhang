<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use JamstackVietnam\Core\Models\Admin;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContactCheckoutNoteTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $this->admin = Admin::firstOrCreate(
            ['email' => 'admin_test_note@jamstackvietnam.com'],
            [
                'name'     => 'Admin Tester',
                'password' => bcrypt('secret123'),
                'status'   => Admin::STATUS_ACTIVE,
            ]
        );
        $this->admin->assignRole($role);
    }

    /**
     * Test quick update note succeeds and updates data.
     */
    public function test_quick_update_note_success()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'  => 'Nguyễn Văn Test',
                'phone' => '0901234567',
                'email' => 'test@example.com',
                'note'  => 'Ghi chú ban đầu',
            ],
        ]);

        $response = $this->actingAs($this->admin, 'admin')->postJson(
            route('admin.contact-checkout-courses.quick-update-note', ['id' => $contact->id]),
            ['note' => 'Ghi chú mới được cập nhật nhanh']
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đã cập nhật ghi chú thành công',
                'note'    => 'Ghi chú mới được cập nhật nhanh',
            ]);

        $contact->refresh();
        $this->assertEquals('Ghi chú mới được cập nhật nhanh', $contact->data['note']);
    }

    /**
     * Test quick update note validation with too long note.
     */
    public function test_quick_update_note_validation()
    {
        $contact = Contact::create([
            'type'   => Contact::TYPE_CHECKOUT_COURSE,
            'status' => Contact::STATUS_NEW,
            'data'   => ['name' => 'Nguyễn Văn Test'],
        ]);

        $response = $this->actingAs($this->admin, 'admin')->postJson(
            route('admin.contact-checkout-courses.quick-update-note', ['id' => $contact->id]),
            ['note' => str_repeat('a', 2001)]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['note']);
    }

    /**
     * Test removed course class routes return 404.
     */
    public function test_removed_course_class_routes_return_404()
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/admin/course-classes');
        $this->assertTrue(in_array($response->status(), [404, 302]));

        $responseRoster = $this->actingAs($this->admin, 'admin')->get('/admin/contact-checkout-courses/class-roster');
        $this->assertTrue(in_array($responseRoster->status(), [404, 302]));
    }
}
