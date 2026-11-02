<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đăng ký khóa học</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #1e293b; background: #f8fafc; padding: 20px; }
        .box { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; }
        .header { background: #0f172a; color: #fff; padding: 24px; text-align: center; }
        .header h2 { margin: 0 0 6px 0; color: #fff; font-size: 20px; }
        .content { padding: 24px; font-size: 14px; }
        .highlight-card { background: #f1f5f9; padding: 16px; border-radius: 8px; margin: 16px 0; border: 1px solid #cbd5e1; }
        .row { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .btn { display: inline-block; background: #2563eb; color: #fff !important; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; margin-top: 12px; }
        .footer { padding: 16px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="box">
        <div class="header">
            <h2>FUNKY STYLE ACADEMY</h2>
            <p style="margin: 0; color: #94a3b8; font-size: 12px;">Phiếu xác nhận đăng ký & học phí</p>
        </div>
        <div class="content">
            <p>Chào <strong>{{ $slip->student_name }}</strong>,</p>
            <p>Funky Style Academy xin thông báo đơn đăng ký khóa học của bạn đã được xác nhận thành công trên hệ thống. Dưới đây là thông tin chi tiết:</p>

            <div class="highlight-card">
                <p style="margin: 0 0 8px 0;"><strong>Mã phiếu:</strong> {{ $slip->slip_code }}</p>
                <p style="margin: 0 0 8px 0;"><strong>Khóa học:</strong> {{ $slip->course_title }}</p>
                <p style="margin: 0 0 8px 0;"><strong>Lịch học:</strong> {{ $slip->schedule_info ?: 'Tiêu chuẩn' }}</p>
                <p style="margin: 0 0 8px 0; font-size: 16px; color: #2563eb;"><strong>Học phí cần thanh toán:</strong> {{ $slip->formatted_final_price }}</p>
                <p style="margin: 0; font-size: 13px;"><strong>Nội dung chuyển khoản (SePay):</strong> <span style="background: #fee2e2; color: #dc2626; padding: 2px 6px; border-radius: 4px; font-weight: bold;">{{ $slip->reference_code }}</span></p>
            </div>

            <p>Phiếu xác nhận chi tiết có đính kèm mã QR thanh toán động đã được gửi kèm trong email này dưới định dạng PDF.</p>
            <p>Bạn cũng có thể xem và tải trực tiếp phiếu xác nhận bất cứ lúc nào qua liên kết bảo mật dưới đây:</p>

            <div style="text-align: center; margin: 24px 0;">
                <a href="{{ $slip->view_url }}" class="btn">Xem & Tải phiếu xác nhận</a>
            </div>

            <p style="font-size: 12px; color: #64748b;"><em>Lưu ý: Để hệ thống đối soát tự động ghi nhận ngay trong 1-3 phút, vui lòng giữ nguyên nội dung chuyển khoản <strong>{{ $slip->reference_code }}</strong> khi thực hiện thanh toán qua App Ngân hàng.</em></p>
        </div>
        <div class="footer">
            <p>Funky Style Academy — Đào tạo Kiến trúc & Thiết kế nội thất chuyên nghiệp</p>
            <p>Hotline: 0318 148 569 | Website: funkystyle.vn</p>
        </div>
    </div>
</body>
</html>
