<?php

namespace Tests\Feature;

use Tests\TestCase;

class DangNhapRedirectTest extends TestCase
{
    /**
     * Kiểm tra /dang-nhap chuyển hướng 301 về trang chủ.
     *
     * @return void
     */
    public function test_dang_nhap_redirects_permanently_to_home()
    {
        $response = $this->get('/dang-nhap');

        $response->assertStatus(301);
        $response->assertRedirect('/');
    }

    /**
     * Kiểm tra route name user.login vẫn tồn tại và trỏ an toàn về dang-nhap.
     *
     * @return void
     */
    public function test_user_login_route_is_named_and_safe()
    {
        $url = route('user.login');
        $this->assertStringContainsString('/dang-nhap', $url);
    }
}
