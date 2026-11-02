<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\CourseClass;
use App\Models\CourseConfirmationSlip;
use App\Models\Post\Post;
use App\Services\CourseClassOrderingService;
use App\Services\CourseConfirmationSlipService;
use App\Services\SePayService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseClassE2EFlowSeeder extends Seeder
{
    public function run(): void
    {
        $sketchup = Post::where('type', 'COURSE')->where('id', 11)->first();
        $revit = Post::where('type', 'COURSE')->where('id', 10)->first();

        if (!$sketchup || !$revit) {
            $this->command->error('Không tìm thấy khóa học Sketchup (ID 11) hoặc Revit (ID 10)!');
            return;
        }

        // 1. Tạo 5 lớp Sketchup đa dạng sĩ số & ngày khai giảng, STT không theo logic
        $skpClassesData = [
            [
                'class_code'    => 'SKP-K26-T35',
                'name'          => 'Lớp Sketchup K26 - Tối T3,T5 (Phòng máy)',
                'start_date'    => '2026-09-09',
                'schedule_time' => 'T4 + CN (20h30-22h30)',
                'duration'      => '12 buổi',
                'mentor'        => 'KTS. Chế Hà Viên Quân',
                'class_type'    => 'OFFLINE',
                'tuition_fee'   => 2400000,
                'max_students'  => 12,
                'display_order' => 4, // Xáo trộn
            ],
            [
                'class_code'    => 'SKP-K28-ONLINE',
                'name'          => 'Lớp Sketchup K28 - Online Toàn quốc Zoom',
                'start_date'    => '2026-10-05',
                'schedule_time' => 'T2 + T6 (20h30-22h30)',
                'duration'      => '12 buổi',
                'mentor'        => 'KTS. Chế Hà Viên Quân',
                'class_type'    => 'ONLINE',
                'tuition_fee'   => 2400000,
                'max_students'  => 20,
                'display_order' => 1,
            ],
            [
                'class_code'    => 'SKP-K29-CAPTOK',
                'name'          => 'Lớp Sketchup K29 - Cấp tốc chiều T2-T6',
                'start_date'    => '2026-10-18',
                'schedule_time' => 'T2-T6 (14h00 - 17h00)',
                'duration'      => '10 buổi',
                'mentor'        => 'Tutor Tài Nguyễn',
                'class_type'    => 'OFFLINE',
                'tuition_fee'   => 3200000,
                'max_students'  => 10,
                'display_order' => 5, // Xáo trộn
            ],
            [
                'class_code'    => 'SKP-K25-T24',
                'name'          => 'Lớp Sketchup K25 - Tối T2,T4 (Phòng máy)',
                'start_date'    => '2026-11-20',
                'schedule_time' => 'T2 + T4 (19h30 - 21h30)',
                'duration'      => '12 buổi',
                'mentor'        => 'KTS. Lê Hoàng Kỳ',
                'class_type'    => 'OFFLINE',
                'tuition_fee'   => 2600000,
                'max_students'  => 15,
                'display_order' => 2,
            ],
            [
                'class_code'    => 'SKP-K27-T7CN',
                'name'          => 'Lớp Sketchup K27 - Cuối tuần T7,CN',
                'start_date'    => '2026-09-22',
                'schedule_time' => 'T3 + T7 (19h00-21h30)',
                'duration'      => '12 buổi',
                'mentor'        => 'Phạm Gia Bảo - Đinh Gia Bảo',
                'class_type'    => 'OFFLINE',
                'tuition_fee'   => 2600000,
                'max_students'  => 15,
                'display_order' => 3,
            ],
        ];

        // 2. Tạo 5 lớp Revit đa dạng
        $rvtClassesData = [
            [
                'class_code'    => 'RVT-K19-T24',
                'name'          => 'Lớp Revit Kiến trúc K19 - Tối T2,T4',
                'start_date'    => '2026-09-22',
                'schedule_time' => 'T2 + T4 (19h30 - 21h30)',
                'duration'      => '12 buổi',
                'mentor'        => 'KTS. Trần Quốc Tuấn',
                'class_type'    => 'OFFLINE',
                'tuition_fee'   => 2800000,
                'max_students'  => 12,
                'display_order' => 5, // Xáo trộn
            ],
            [
                'class_code'    => 'RVT-K21-T7CN',
                'name'          => 'Lớp Revit Chuyên sâu K21 - Cuối tuần T7,CN',
                'start_date'    => '2026-09-12',
                'schedule_time' => 'T7 + CN (08h30 - 11h30)',
                'duration'      => '10 buổi',
                'mentor'        => 'KTS. Nguyễn Văn Hùng',
                'class_type'    => 'OFFLINE',
                'tuition_fee'   => 2800000,
                'max_students'  => 15,
                'display_order' => 2,
            ],
            [
                'class_code'    => 'RVT-K20-ONLINE',
                'name'          => 'Lớp Revit Master K20 - Online Zoom',
                'start_date'    => '2026-12-05',
                'schedule_time' => 'T3 + T5 + T7 (20h00 - 22h00)',
                'duration'      => '15 buổi',
                'mentor'        => 'ThS. KTS. Đỗ Hoàng Long',
                'class_type'    => 'ONLINE',
                'tuition_fee'   => 2500000,
                'max_students'  => 25,
                'display_order' => 1,
            ],
            [
                'class_code'    => 'RVT-K22-DOAN',
                'name'          => 'Lớp Revit Triển khai Đồ án K22',
                'start_date'    => '2026-11-10',
                'schedule_time' => 'T2 + T6 (18h30 - 21h30)',
                'duration'      => '12 buổi',
                'mentor'        => 'KTS. Lê Quang Huy',
                'class_type'    => 'OFFLINE',
                'tuition_fee'   => 3000000,
                'max_students'  => 10,
                'display_order' => 4,
            ],
            [
                'class_code'    => 'RVT-K18-T35',
                'name'          => 'Lớp Revit Kiến trúc K18 - Tối T3,T5',
                'start_date'    => '2026-10-12',
                'schedule_time' => 'T3 + T5 (19h30 - 21h30)',
                'duration'      => '12 buổi',
                'mentor'        => 'KTS. Trần Quốc Tuấn',
                'class_type'    => 'OFFLINE',
                'tuition_fee'   => 2800000,
                'max_students'  => 20,
                'display_order' => 3,
            ],
        ];

        $allClasses = [];

        foreach ($skpClassesData as $d) {
            $c = CourseClass::updateOrCreate(
                ['course_id' => $sketchup->id, 'class_code' => $d['class_code']],
                array_merge($d, [
                    'enrolled_students' => 0,
                    'status'            => CourseClass::STATUS_OPEN,
                ])
            );
            $allClasses[$d['class_code']] = $c;
        }

        foreach ($rvtClassesData as $d) {
            $c = CourseClass::updateOrCreate(
                ['course_id' => $revit->id, 'class_code' => $d['class_code']],
                array_merge($d, [
                    'enrolled_students' => 0,
                    'status'            => CourseClass::STATUS_OPEN,
                ])
            );
            $allClasses[$d['class_code']] = $c;
        }

        // 3. Danh sách 18 học viên thật với số tiền, phân bổ đa dạng
        $students = [
            // SKP-K26-T35 (Max 12): Sẽ nạp 11 bạn (gần đầy, 91.7%)
            ['name' => 'Nguyễn Hoàng Nam', 'phone' => '0903112233', 'email' => 'nam.nguyen@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],
            ['name' => 'Trần Thị Mai Anh', 'phone' => '0918223344', 'email' => 'mai.anh@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],
            ['name' => 'Lê Quốc Bảo', 'phone' => '0987334455', 'email' => 'bao.le@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 1000000, 'status' => 'DEPOSITED'],
            ['name' => 'Phạm Minh Tuấn', 'phone' => '0933445566', 'email' => 'tuan.pham@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],
            ['name' => 'Đặng Thúy Hằng', 'phone' => '0977556677', 'email' => 'hang.dang@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],
            ['name' => 'Vũ Đức Trí', 'phone' => '0944667788', 'email' => 'tri.vu@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],
            ['name' => 'Hoàng Ngọc Lan', 'phone' => '0966778899', 'email' => 'lan.hoang@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],
            ['name' => 'Bùi Quang Huy', 'phone' => '0912889900', 'email' => 'huy.bui@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],
            ['name' => 'Phan Vĩnh Khang', 'phone' => '0938112244', 'email' => 'khang.phan@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],
            ['name' => 'Trịnh Mỹ Duyên', 'phone' => '0908223355', 'email' => 'duyen.trinh@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 1000000, 'status' => 'DEPOSITED'],
            ['name' => 'Đinh Công Thành', 'phone' => '0978334466', 'email' => 'thanh.dinh@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K26-T35', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],

            // SKP-K28-ONLINE (Max 20): Nạp 3 bạn (còn nhiều chỗ, 15%)
            ['name' => 'Lý Hải Đăng', 'phone' => '0919335577', 'email' => 'dang.ly@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K28-ONLINE', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],
            ['name' => 'Ngô Thị Kim Oanh', 'phone' => '0989446688', 'email' => 'oanh.ngo@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K28-ONLINE', 'fee' => 2400000, 'paid' => 500000, 'status' => 'DEPOSITED'],
            ['name' => 'Võ Trường Giang', 'phone' => '0939557799', 'email' => 'giang.vo@gmail.com', 'course_id' => 11, 'class_code' => 'SKP-K28-ONLINE', 'fee' => 2400000, 'paid' => 2400000, 'status' => 'PAID_FULL'],

            // RVT-K19-T24 (Max 12): Nạp 12 bạn (Lớp Full, 100%)
            ['name' => 'Ngô Thanh Sơn', 'phone' => '0938990011', 'email' => 'son.ngo@gmail.com', 'course_id' => 10, 'class_code' => 'RVT-K19-T24', 'fee' => 2800000, 'paid' => 2800000, 'status' => 'PAID_FULL'],
            ['name' => 'Đỗ Quỳnh Nga', 'phone' => '0909113355', 'email' => 'nga.do@gmail.com', 'course_id' => 10, 'class_code' => 'RVT-K19-T24', 'fee' => 2800000, 'paid' => 2800000, 'status' => 'PAID_FULL'],
            ['name' => 'Phan Anh Đức', 'phone' => '0988224466', 'email' => 'duc.phan@gmail.com', 'course_id' => 10, 'class_code' => 'RVT-K19-T24', 'fee' => 2800000, 'paid' => 1500000, 'status' => 'DEPOSITED'],
            ['name' => 'Cao Trọng Nhân', 'phone' => '0977335577', 'email' => 'nhan.cao@gmail.com', 'course_id' => 10, 'class_code' => 'RVT-K19-T24', 'fee' => 2800000, 'paid' => 2800000, 'status' => 'PAID_FULL'],
        ];

        $sePay = app(SePayService::class);

        foreach ($students as $s) {
            $courseClass = $allClasses[$s['class_code']];
            $course = $s['course_id'] === 11 ? $sketchup : $revit;

            $contact = Contact::where('data->Email', $s['email'])->first();
            if (!$contact) {
                $contact = new Contact();
            }

            $contact->fill([
                'type'                    => Contact::TYPE_CHECKOUT_COURSE,
                'status'                  => Contact::STATUS_PROCESSED, // Đã xếp lớp
                'class_id'                => $courseClass->id,
                'class_allocation_status' => Contact::ALLOCATION_ALLOCATED,
                'data'                    => [
                    'Họ và tên'           => $s['name'],
                    'Số điện thoại'       => $s['phone'],
                    'Email'               => $s['email'],
                    'paid_amount'         => $s['paid'],
                    'debt_amount'         => max(0, $s['fee'] - $s['paid']),
                    'payment_status'      => $s['status'],
                    'course'              => [
                        'id'             => $course->id,
                        'title'          => $course->title,
                        'price'          => $s['fee'],
                        'price_original' => $s['fee'],
                        'schedule_time'  => $courseClass->schedule_time,
                        'day'            => $courseClass->formatted_start_date,
                    ],
                ],
            ]);
            $contact->saveQuietly();

            // Tạo phiếu xác nhận SePay
            $refCode = $sePay->generateReferenceCodeForCourse($contact);
            $qrUrl = $sePay->generateVietQRUrl($s['paid'], $refCode);

            $existingSlip = CourseConfirmationSlip::where('contact_id', $contact->id)->first();
            CourseConfirmationSlip::updateOrCreate(
                ['contact_id' => $contact->id],
                [
                    'slip_code'         => 'SLIP-' . str_pad($contact->id, 5, '0', STR_PAD_LEFT),
                    'reference_code'    => $refCode,
                    'student_name'      => $s['name'],
                    'student_email'     => $s['email'],
                    'student_phone'     => $s['phone'],
                    'course_title'      => $course->title,
                    'schedule_info'     => "{$courseClass->formatted_start_date} | {$courseClass->schedule_time}",
                    'original_price'    => $s['fee'],
                    'discount_amount'   => 0,
                    'final_price'       => $s['fee'],
                    'paid_amount'       => $s['paid'],
                    'debt_amount'       => max(0, $s['fee'] - $s['paid']),
                    'payment_status'    => $s['status'],
                    'qr_url'            => $qrUrl,
                    'discounts_summary' => [],
                    'email_status'      => 'SENT',
                    'download_token'    => $existingSlip?->download_token ?: Str::random(40),
                ]
            );

            $courseClass->increment('enrolled_students');
        }

        // Cập nhật trạng thái FULL nếu lớp đạt hoặc vượt sĩ số
        foreach ($allClasses as $c) {
            $cFresh = $c->fresh();
            if ($cFresh->enrolled_students >= $cFresh->max_students) {
                $cFresh->status = CourseClass::STATUS_FULL;
                $cFresh->save();
            }
        }

        // Đồng bộ schedule ra PostTranslation
        CourseClassOrderingService::syncScheduleOrderToCourse($sketchup->id);
        CourseClassOrderingService::syncScheduleOrderToCourse($revit->id);

        $this->command->info("Đã seed xong 10 lớp học thực tế và 18 học viên thật!");
    }
}
