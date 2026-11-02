<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SePay Bank Account Information
    |--------------------------------------------------------------------------
    */
    'account_number' => env('SEPAY_ACCOUNT_NUMBER', ''),
    'bank_code'      => env('SEPAY_BANK_CODE', 'MB'),       // VD: MB, VCB, TCB, ACB
    'account_holder' => env('SEPAY_ACCOUNT_HOLDER', ''),
    'webhook_secret' => env('SEPAY_WEBHOOK_SECRET', ''),
    'reference_prefix' => env('SEPAY_REFERENCE_PREFIX', 'FK'),

    /*
    |--------------------------------------------------------------------------
    | Bank Name Mapping
    |--------------------------------------------------------------------------
    | Mapping mã ngân hàng → tên đầy đủ để hiển thị trên giao diện.
    */
    'bank_names' => [
        'MB'   => 'Ngân hàng TMCP Quân đội (MB Bank)',
        'VCB'  => 'Ngân hàng TMCP Ngoại thương (Vietcombank)',
        'TCB'  => 'Ngân hàng TMCP Kỹ thương (Techcombank)',
        'ACB'  => 'Ngân hàng TMCP Á Châu (ACB)',
        'BIDV' => 'Ngân hàng TMCP Đầu tư và Phát triển (BIDV)',
        'VPB'  => 'Ngân hàng TMCP Việt Nam Thịnh Vượng (VPBank)',
        'TPB'  => 'Ngân hàng TMCP Tiên Phong (TPBank)',
        'STB'  => 'Ngân hàng TMCP Sài Gòn Thương Tín (Sacombank)',
        'HDB'  => 'Ngân hàng TMCP Phát triển TP.HCM (HDBank)',
        'MSB'  => 'Ngân hàng TMCP Hàng Hải (MSB)',
        'VIB'  => 'Ngân hàng TMCP Quốc tế (VIB)',
        'SHB'  => 'Ngân hàng TMCP Sài Gòn – Hà Nội (SHB)',
        'EIB'  => 'Ngân hàng TMCP Xuất Nhập khẩu (Eximbank)',
        'OCB'  => 'Ngân hàng TMCP Phương Đông (OCB)',
        'LPB'  => 'Ngân hàng TMCP Bưu Điện Liên Việt (LPBank)',
        'ABB'  => 'Ngân hàng TMCP An Bình (ABBank)',
        'NAB'  => 'Ngân hàng TMCP Nam Á (Nam A Bank)',
        'CAKE' => 'Ngân hàng số CAKE by VPBank',
    ],

    /*
    |--------------------------------------------------------------------------
    | QR Code Settings
    |--------------------------------------------------------------------------
    */
    'qr_expires_minutes' => env('SEPAY_QR_EXPIRES_MINUTES', 15),

    /*
    |--------------------------------------------------------------------------
    | VietQR API
    | Docs: https://www.vietqr.io/danh-sach-api/tao-ma-qr/
    |--------------------------------------------------------------------------
    | Các template hỗ trợ: qr_only, compact, compact, print, v.v.
    */
    'vietqr_base_url' => 'https://img.vietqr.io/image',
    'qr_template'     => env('SEPAY_QR_TEMPLATE', 'compact'),
];
