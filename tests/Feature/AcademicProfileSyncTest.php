<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AcademicProfileSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--path' => 'database/migrations/blog']);
    }

    public function test_academic_options_api_returns_backend_data()
    {
        School::create([
            'name'       => 'Trường Đại học Kiến Trúc TP.HCM',
            'short_name' => 'UAH',
            'type'       => 'DH',
            'status'     => School::STATUS_ACTIVE,
            'position'   => 1,
        ]);

        $response = $this->getJson(route('api.academic-options'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'schools',
                'majors',
                'currentStatusOptions',
                'schoolYears',
            ]);

        $data = $response->json();
        $this->assertNotEmpty($data['schools']);
        $this->assertNotEmpty($data['majors']);
        $this->assertNotEmpty($data['currentStatusOptions']);
        $this->assertNotEmpty($data['schoolYears']);

        $this->assertEquals('Trường Đại học Kiến Trúc TP.HCM', $data['schools'][0]['name']);
    }

    public function test_checkout_course_page_receives_academic_props()
    {
        School::create([
            'name'       => 'Đại học Bách Khoa TP.HCM',
            'short_name' => 'HCMUT',
            'type'       => 'DH',
            'status'     => School::STATUS_ACTIVE,
            'position'   => 1,
        ]);

        $response = $this->get(route('checkout.course.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Checkout/Checkout')
            ->has('schools')
            ->has('majors')
            ->has('currentStatusOptions')
            ->has('schoolYears')
        );
    }

    public function test_checkout_submission_stores_education_fields_in_contact()
    {
        $payload = [
            'contact' => [
                'type' => 'TYPE_CHECKOUT_COURSE',
                'data' => [
                    'name'                       => 'Học Viên Kiến Trúc',
                    'phone'                      => '0938111222',
                    'email'                      => 'kientruc@test.com',
                    'Bạn đang, đã học trường nào'=> 'Đại học Kiến Trúc TP.HCM',
                    'Hiện tại bạn đang là'       => 'Sinh viên năm 3',
                    'Ngành học hiện tại'         => 'Kiến Trúc Công Trình',
                    'Năm học / Khóa học'         => '2023 - 2027 (Khóa 2023)',
                ],
            ],
        ];

        $response = $this->post(route('contact.store'), $payload);
        $response->assertSessionHas('success');

        $contact = Contact::where('type', 'TYPE_CHECKOUT_COURSE')->latest()->first();
        $this->assertNotNull($contact);
        $this->assertEquals('Đại học Kiến Trúc TP.HCM', $contact->data['Bạn đang, đã học trường nào']);
        $this->assertEquals('Sinh viên năm 3', $contact->data['Hiện tại bạn đang là']);
        $this->assertEquals('Kiến Trúc Công Trình', $contact->data['Ngành học hiện tại']);
        $this->assertEquals('2023 - 2027 (Khóa 2023)', $contact->data['Năm học / Khóa học']);
    }

    public function test_checkout_submission_syncs_education_to_logged_in_user_profile()
    {
        $school = School::create([
            'name'       => 'Đại học Văn Lang',
            'short_name' => 'VLU',
            'type'       => 'DH',
            'status'     => School::STATUS_ACTIVE,
            'position'   => 1,
        ]);

        $user = User::create([
            'name'     => 'Nguyễn Văn A',
            'email'    => 'user_sync@test.com',
            'phone'    => '0912345678',
            'password' => bcrypt('password'),
            'status'   => 'ACTIVE',
        ]);

        $this->actingAs($user, 'user');

        $payload = [
            'contact' => [
                'type' => 'TYPE_CHECKOUT_COURSE',
                'data' => [
                    'name'                       => 'Nguyễn Văn A',
                    'phone'                      => '0912345678',
                    'email'                      => 'user_sync@test.com',
                    'Bạn đang, đã học trường nào'=> 'Đại học Văn Lang',
                    'Hiện tại bạn đang là'       => 'Đã ra trường',
                    'Ngành học hiện tại'         => 'Thiết Kế Nội Thất',
                    'Năm học / Khóa học'         => '2019 - 2023 (Khóa 2019)',
                ],
            ],
        ];

        $response = $this->post(route('contact.store'), $payload);
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals($school->id, $user->school_id);
        $this->assertEquals('Đã ra trường', $user->current_status);
        $this->assertEquals('Thiết Kế Nội Thất', $user->major);
        $this->assertEquals('2019 - 2023 (Khóa 2019)', $user->school_year);
    }
}
