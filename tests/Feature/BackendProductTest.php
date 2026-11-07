<?php

namespace Tests\Feature;

use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Database\Seeders\FigmaProductsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use JamstackVietnam\Core\Models\Admin;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BackendProductTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'sqlite') {
            $this->artisan('migrate', ['--path' => 'database/migrations/blog']);
        }

        // Cấp quyền đầy đủ cho Admin thông qua role Super Admin và Gate::before
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $this->admin = Admin::firstOrCreate(
            ['email' => 'admin_product_test@jamstackvietnam.com'],
            [
                'name'     => 'Admin Product Tester',
                'password' => bcrypt('secret123'),
                'status'   => Admin::STATUS_ACTIVE,
            ]
        );
        $this->admin->assignRole($role);
    }

    /**
     * Test 1: Khách vãng lai chưa đăng nhập bị từ chối truy cập và chuyển hướng về đăng nhập (HTTP 302).
     */
    public function test_guest_cannot_access_product_form(): void
    {
        $responseVi = $this->get('/admin/products/form');
        $this->assertEquals(302, $responseVi->getStatusCode());

        $responseEn = $this->get('/admin/en/products/form');
        $this->assertEquals(302, $responseEn->getStatusCode());

        $responseStore = $this->post('/admin/products/store', ['title' => 'Unauthorized Product']);
        $this->assertEquals(302, $responseStore->getStatusCode());
    }

    /**
     * Test 2: Quản trị viên truy cập GET /admin/products/form tạo mới sản phẩm thành công (HTTP 200).
     */
    public function test_admin_can_access_product_create_form(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/admin/products/form');
        $response->assertStatus(200);

        // Kiểm tra request dạng JSON nhận về cấu trúc form item hợp lệ
        $jsonResponse = $this->actingAs($this->admin, 'admin')->getJson('/admin/products/form');
        $jsonResponse->assertStatus(200);
    }

    /**
     * Test 3: Quản trị viên truy cập GET /admin/en/products/form ngôn ngữ Tiếng Anh thành công (HTTP 200).
     */
    public function test_admin_can_access_product_create_form_english_locale(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/admin/en/products/form');
        $response->assertStatus(200);
    }

    /**
     * Test 4: Quản trị viên truy cập GET /admin/products/form/{id} chỉnh sửa sản phẩm hiện hữu thành công (HTTP 200).
     */
    public function test_admin_can_access_product_edit_form(): void
    {
        $this->seed(FigmaProductsSeeder::class);

        $product = Product::first();
        $this->assertNotNull($product, 'Cần có ít nhất 1 sản phẩm mẫu từ Seeder');

        $response = $this->actingAs($this->admin, 'admin')->get("/admin/products/form/{$product->id}");
        $response->assertStatus(200);

        $jsonResponse = $this->actingAs($this->admin, 'admin')->getJson("/admin/products/form/{$product->id}");
        $jsonResponse->assertStatus(200);
    }

    /**
     * Test 5: Quản trị viên gửi POST /admin/products/store lưu sản phẩm mới thành công (HTTP 302/200).
     */
    public function test_admin_can_store_new_product(): void
    {
        $payload = [
            'title'            => 'Sản phẩm Test Admin Mới',
            'slug'             => 'san-pham-test-admin-moi',
            'sku'              => 'TEST-ADMIN-001',
            'price'            => 450000,
            'compare_at_price' => 500000,
            'status'           => 'ACTIVE',
        ];

        $response = $this->actingAs($this->admin, 'admin')->post('/admin/products/store', $payload);

        // HasCrudActions chuyển hướng về form sản phẩm sau khi lưu thành công
        $this->assertTrue(
            in_array($response->getStatusCode(), [200, 302]),
            'Mã trạng thái trả về phải là 200 hoặc 302 redirect'
        );

        $this->assertDatabaseHas('products', [
            'sku'    => 'TEST-ADMIN-001',
            'price'  => 450000,
            'status' => 'ACTIVE',
        ]);

        $this->assertDatabaseHas('product_translations', [
            'title' => 'Sản phẩm Test Admin Mới',
        ]);
    }
}
