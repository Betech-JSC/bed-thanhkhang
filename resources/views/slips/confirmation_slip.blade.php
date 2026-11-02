<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Phiếu Xác Nhận Học Phí - {{ $slip->slip_code }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 6mm 10mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }

        body {
            background-color: #ffffff;
            color: #1e293b;
            font-size: 10.5px;
            line-height: 1.35;
        }

        .slip-wrapper {
            width: 100%;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
        }

        /* HEADER */
        .slip-header-table {
            width: 100%;
            border-collapse: collapse;
            background: #0f172a;
            color: #ffffff;
        }

        .slip-header-table td {
            padding: 14px 18px;
            vertical-align: middle;
        }

        .brand-title {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #ffffff;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .brand-subtitle {
            font-size: 9.5px;
            color: #94a3b8;
        }

        .slip-meta-col {
            text-align: right;
            vertical-align: middle;
        }

        .slip-badge {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .slip-meta-text {
            font-size: 9px;
            color: #cbd5e1;
        }

        /* BODY CONTAINER */
        .slip-content {
            padding: 12px 18px;
        }

        /* SECTION HEADERS */
        .section-heading {
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            background: #f1f5f9;
            padding: 4px 8px;
            border-left: 3px solid #2563eb;
            margin-top: 8px;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        /* INFO TABLES (Student & Course) */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .info-table td {
            padding: 3.5px 6px;
            vertical-align: top;
            font-size: 10px;
        }

        .info-label {
            color: #64748b;
            font-weight: normal;
            width: 28%;
        }

        .info-value {
            color: #0f172a;
            font-weight: bold;
            width: 72%;
        }

        /* FEE BREAKDOWN TABLE */
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 8px;
        }

        .fee-table th {
            background: #e2e8f0;
            color: #334155;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
        }

        .fee-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }

        .fee-table .amount {
            text-align: right;
            font-weight: bold;
            white-space: nowrap;
        }

        .row-total td {
            background: #eff6ff;
            color: #1e3a8a;
            font-weight: bold;
            border-top: 1.5px solid #3b82f6;
            border-bottom: 1.5px solid #3b82f6;
        }

        .row-total .amount {
            font-size: 12px;
            color: #1d4ed8;
        }

        .row-paid td {
            background: #f0fdf4;
            color: #166534;
        }

        .row-debt td {
            background: #fffbeb;
            color: #b45309;
            font-weight: bold;
        }

        .row-debt .amount {
            color: #d97706;
            font-size: 11px;
        }

        /* PAYMENT & QR SECTION */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-top: 4px;
            margin-bottom: 8px;
        }

        .payment-table td {
            padding: 8px 10px;
            vertical-align: middle;
        }

        .qr-box {
            width: 105px;
            text-align: center;
            border-right: 1px dashed #cbd5e1;
            padding-right: 10px;
        }

        .qr-box img {
            width: 90px;
            height: 90px;
            display: block;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: #ffffff;
            padding: 3px;
        }

        .qr-caption {
            font-size: 8px;
            color: #64748b;
            margin-top: 3px;
            font-weight: bold;
        }

        .bank-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bank-info-table td {
            padding: 2.5px 4px;
            font-size: 9.5px;
            border: none;
        }

        .bank-label {
            color: #64748b;
            width: 32%;
        }

        .bank-val {
            color: #0f172a;
            font-weight: bold;
            width: 68%;
        }

        .highlight-code {
            color: #dc2626;
            background: #fee2e2;
            padding: 1px 6px;
            border-radius: 3px;
            display: inline-block;
            font-family: monospace;
            font-weight: bold;
            font-size: 10.5px;
        }

        /* NOTICE BOX */
        .notice-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 3px solid #f59e0b;
            padding: 6px 10px;
            border-radius: 4px;
            margin-bottom: 8px;
            font-size: 9px;
            color: #92400e;
            line-height: 1.35;
        }

        /* FOOTER */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            margin-top: 4px;
        }

        .footer-table td {
            padding: 4px 0;
            vertical-align: bottom;
        }

        .footer-contact {
            font-size: 8.5px;
            color: #64748b;
            line-height: 1.4;
        }

        .footer-stamp-col {
            text-align: right;
        }

        .stamp-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 3px;
        }

        .stamp-badge {
            display: inline-block;
            border: 1.5px solid #16a34a;
            color: #16a34a;
            font-weight: bold;
            font-size: 9px;
            padding: 3px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f0fdf4;
        }

        /* WEB PRINT CONTROLS */
        .print-btn-bar {
            max-width: 760px;
            margin: 0 auto 12px auto;
            text-align: right;
        }

        .print-btn {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 11px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 8px;
        }

        @media print {
            .print-btn-bar { display: none !important; }
            body { padding: 0; background: #ffffff; }
            .slip-wrapper { border: none; border-radius: 0; }
        }
    </style>
</head>
<body>

    @if(!isset($isPdf) || !$isPdf)
    <div class="print-btn-bar">
        <button class="print-btn" onclick="window.print()">🖨️ In phiếu / Lưu PDF</button>
        <a href="{{ $slip->download_url }}" class="print-btn" style="background: #10b981;">📥 Tải file PDF</a>
    </div>
    @endif

    <div class="slip-wrapper">
        <!-- HEADER -->
        <table class="slip-header-table">
            <tr>
                <td style="width: 65%;">
                    <div class="brand-title">FUNKY STYLE ACADEMY</div>
                    <div class="brand-subtitle">Học viện Đào tạo Kiến trúc & Thiết kế Đồ họa Chuyên Nghiệp</div>
                </td>
                <td class="slip-meta-col" style="width: 35%;">
                    <div class="slip-badge">{{ $slip->slip_code }}</div>
                    <div class="slip-meta-text">Ngày lập: {{ $slip->formatted_created_at }}</div>
                    <div class="slip-meta-text">Phiên bản: v{{ $slip->version }}.0</div>
                </td>
            </tr>
        </table>

        <div class="slip-content">
            <!-- 1. THÔNG TIN HỌC VIÊN & KHÓA HỌC (Ghép chung 2 cột cân đối) -->
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <!-- Cột trái: Thông tin học viên -->
                    <td style="width: 49%; vertical-align: top; padding-right: 8px;">
                        <div class="section-heading">1. Thông tin học viên</div>
                        <table class="info-table">
                            <tr>
                                <td class="info-label">Họ và tên:</td>
                                <td class="info-value">{{ $slip->student_name }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Điện thoại:</td>
                                <td class="info-value">{{ $slip->student_phone ?: '---' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Email:</td>
                                <td class="info-value" style="font-size: 9.5px; word-break: break-all;">{{ $slip->student_email ?: '---' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Trường học:</td>
                                <td class="info-value">{{ $studentSchool ?: '---' }}</td>
                            </tr>
                        </table>
                    </td>

                    <!-- Cột phải: Thông tin khóa học -->
                    <td style="width: 49%; vertical-align: top; padding-left: 8px;">
                        <div class="section-heading">2. Khóa học đăng ký</div>
                        <table class="info-table">
                            <tr>
                                <td class="info-label">Khóa học:</td>
                                <td class="info-value" style="color: #1e40af;">{{ $slip->course_title }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Lịch học:</td>
                                <td class="info-value">{{ $slip->schedule_info ?: 'Lịch học tiêu chuẩn' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Hình thức:</td>
                                <td class="info-value">Thực chiến đồ án trực tiếp</td>
                            </tr>
                            <tr>
                                <td class="info-label">Trạng thái:</td>
                                <td class="info-value" style="color: #059669;">
                                    @if($slip->debt_amount > 0)
                                        Đã giữ chỗ (Đóng cọc)
                                    @else
                                        Đã hoàn tất học phí (100%)
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- 2. BẢNG KÊ HỌC PHÍ & ƯU ĐÃI -->
            <div class="section-heading">3. Bảng kê học phí & Ưu đãi</div>
            <table class="fee-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Nội dung chi tiết mục học phí</th>
                        <th style="text-align: right; width: 140px;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Học phí niêm yết: {{ $slip->course_title }}</strong>
                        </td>
                        <td class="amount">{{ $slip->formatted_original_price }}</td>
                    </tr>
                    @if($slip->discount_amount > 0)
                    <tr>
                        <td>
                            <span style="color: #16a34a; font-weight: bold;">Tổng ưu đãi giảm trừ:</span>
                            <span style="color: #64748b; font-size: 9px;">({{ $slip->discounts_summary ?: 'Chính sách ưu đãi khóa học' }})</span>
                        </td>
                        <td class="amount" style="color: #16a34a;">-{{ $slip->formatted_discount_amount }}</td>
                    </tr>
                    @endif
                    <tr class="row-total">
                        <td><strong>TỔNG HỌC PHÍ THỰC THU</strong></td>
                        <td class="amount">{{ $slip->formatted_final_price }}</td>
                    </tr>
                    @if($slip->paid_amount > 0)
                    <tr class="row-paid">
                        <td>Đã thanh toán (Tiền cọc / Đợt 1)</td>
                        <td class="amount">{{ $slip->formatted_paid_amount }}</td>
                    </tr>
                    @endif
                    @if($slip->debt_amount > 0)
                    <tr class="row-debt">
                        <td>CÒN THIẾU CẦN NỘP TRƯỚC KHAI GIẢNG</td>
                        <td class="amount">{{ $slip->formatted_debt_amount }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <!-- 3. THÔNG TIN THANH TOÁN SEPAY VIETQR -->
            <div class="section-heading">4. Thanh toán học phí (SePay VietQR)</div>
            <table class="payment-table">
                <tr>
                    <td class="qr-box">
                        <img src="{{ $slip->qr_url }}" alt="VietQR SePay">
                        <div class="qr-caption">Quét mã bằng App Ngân hàng</div>
                    </td>
                    <td style="padding-left: 12px;">
                        <table class="bank-info-table">
                            <tr>
                                <td class="bank-label">Ngân hàng:</td>
                                <td class="bank-val">{{ $bankName }} ({{ $bankCode }})</td>
                            </tr>
                            <tr>
                                <td class="bank-label">Số tài khoản:</td>
                                <td class="bank-val" style="font-size: 11px; font-family: monospace;">{{ $accountNumber }}</td>
                            </tr>
                            <tr>
                                <td class="bank-label">Chủ tài khoản:</td>
                                <td class="bank-val">{{ $accountHolder }}</td>
                            </tr>
                            <tr>
                                <td class="bank-label">Số tiền:</td>
                                <td class="bank-val" style="color: #2563eb; font-size: 11px;">
                                    @if($slip->debt_amount > 0)
                                        {{ $slip->formatted_debt_amount }} <span style="font-size: 8.5px; color: #64748b; font-weight: normal;">(Thanh toán phần còn lại)</span>
                                    @else
                                        {{ $slip->formatted_final_price }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="bank-label">Nội dung CK:</td>
                                <td class="bank-val">
                                    <span class="highlight-code">{{ $slip->reference_code }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- LƯU Ý QUAN TRỌNG -->
            <div class="notice-box">
                <strong>Lưu ý:</strong> Vui lòng nhập chính xác mã nội dung chuyển khoản <strong>{{ $slip->reference_code }}</strong> để cổng SePay tự động đối soát và kích hoạt quyền lợi học viên tức thì.
            </div>

            <!-- FOOTER -->
            <table class="footer-table">
                <tr>
                    <td class="footer-contact" style="width: 70%;">
                        <div><strong>FUNKY STYLE ACADEMY</strong></div>
                        <div>Hotline: {{ $hotline }} | Email: info@funkystyle.vn | Website: funkystyle.vn</div>
                        <div>Địa chỉ: TP. Hồ Chí Minh, Việt Nam</div>
                    </td>
                    <td class="footer-stamp-col" style="width: 30%;">
                        <div class="stamp-title">Ban Quản Trị Đào Tạo</div>
                        <div class="stamp-badge">✓ ĐÃ XÁC THỰC</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
