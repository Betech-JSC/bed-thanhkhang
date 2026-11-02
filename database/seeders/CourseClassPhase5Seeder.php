<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\CourseClass;
use App\Models\Post\Post;
use App\Models\Post\PostTranslation;
use App\Services\CourseClassOrderingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CourseClassPhase5Seeder extends Seeder
{
    public function run()
    {
        $sketchup = Post::where('type', 'COURSE')->where('id', 11)->first()
            ?? Post::where('type', 'COURSE')->whereHas('translations', fn($q) => $q->where('title', 'like', '%SKETCHUP%'))->first();

        if (!$sketchup) {
            $sketchup = Post::create([
                'type'         => 'COURSE',
                'status'       => 'ACTIVE',
                'published_at' => now(),
                'price'        => 2600000,
            ]);
            $tr = new PostTranslation();
            $tr->post_id = $sketchup->id;
            $tr->locale = 'vi';
            $tr->status_locale = 'ACTIVE';
            $tr->title = 'SKETCHUP - THINKING BY DOING';
            $tr->slug = 'sketchup-thinking-by-doing';
            $tr->schedule = [];
            $tr->save();
        }

        $revit = Post::where('type', 'COURSE')->where('id', 10)->first()
            ?? Post::where('type', 'COURSE')->whereHas('translations', fn($q) => $q->where('title', 'like', '%REVIT%'))->first();

        if (!$revit) {
            $revit = Post::create([
                'type'         => 'COURSE',
                'status'       => 'ACTIVE',
                'published_at' => now(),
                'price'        => 2800000,
            ]);
            $tr = new PostTranslation();
            $tr->post_id = $revit->id;
            $tr->locale = 'vi';
            $tr->status_locale = 'ACTIVE';
            $tr->title = 'ARCHITECTURAL REVIT';
            $tr->slug = 'architectural-revit';
            $tr->schedule = [];
            $tr->save();
        }

        $sketchup = $sketchup->fresh(['translations']);
        $revit = $revit->fresh(['translations']);

        CourseClass::whereIn('course_id', [$sketchup->id, $revit->id])->delete();

        $sketchupClasses = [
            [
                'course_id'         => $sketchup->id,
                'class_code'        => 'SKP-K25-T24',
                'name'              => 'Lớp Sketchup K25 - Tối T2,T4 (Phòng máy)',
                'start_date'        => '2026-11-20',
                'schedule_time'     => 'T2 + T4 (19h30 - 21h30)',
                'duration'          => '12 buổi',
                'mentor'            => 'KTS. Lê Hoàng Kỳ',
                'class_type'        => 'OFFLINE',
                'tuition_fee'       => 2600000,
                'max_students'      => 15,
                'enrolled_students' => 3,
                'status'            => CourseClass::STATUS_OPEN,
                'display_order'     => 4,
                'version'           => 1,
            ],
            [
                'course_id'         => $sketchup->id,
                'class_code'        => 'SKP-K26-T35',
                'name'              => 'Lớp Sketchup K26 - Tối T3,T5 (Phòng máy)',
                'start_date'        => '2026-09-15',
                'schedule_time'     => 'T3 + T5 (19h30 - 21h30)',
                'duration'          => '12 buổi',
                'mentor'            => 'KTS. Chế Hà Viên Quân',
                'class_type'        => 'OFFLINE',
                'tuition_fee'       => 2600000,
                'max_students'      => 12,
                'enrolled_students' => 11,
                'status'            => CourseClass::STATUS_OPEN,
                'display_order'     => 1,
                'version'           => 1,
            ],
            [
                'course_id'         => $sketchup->id,
                'class_code'        => 'SKP-K27-T7CN',
                'name'              => 'Lớp Sketchup K27 - Cuối tuần T7,CN',
                'start_date'        => '2026-10-05',
                'schedule_time'     => 'T7 + CN (09h00 - 11h30)',
                'duration'          => '12 buổi',
                'mentor'            => 'Phạm Gia Bảo - Đinh Gia Bảo',
                'class_type'        => 'OFFLINE',
                'tuition_fee'       => 2600000,
                'max_students'      => 15,
                'enrolled_students' => 15,
                'status'            => CourseClass::STATUS_FULL,
                'display_order'     => 5,
                'version'           => 1,
            ],
            [
                'course_id'         => $sketchup->id,
                'class_code'        => 'SKP-K28-ONLINE',
                'name'              => 'Lớp Sketchup K28 - Online Toàn quốc Zoom',
                'start_date'        => '2026-09-25',
                'schedule_time'     => 'T4 + CN (20h30 - 22h30)',
                'duration'          => '12 buổi',
                'mentor'            => 'KTS. Chế Hà Viên Quân',
                'class_type'        => 'ONLINE',
                'tuition_fee'       => 2400000,
                'max_students'      => 20,
                'enrolled_students' => 18,
                'status'            => CourseClass::STATUS_OPEN,
                'display_order'     => 2,
                'version'           => 1,
            ],
            [
                'course_id'         => $sketchup->id,
                'class_code'        => 'SKP-K29-CAPTOK',
                'name'              => 'Lớp Sketchup K29 - Cấp tốc chiều T2-T6',
                'start_date'        => '2026-10-18',
                'schedule_time'     => 'T2-T6 (14h00 - 17h00)',
                'duration'          => '10 buổi',
                'mentor'            => 'Tutor Tài Nguyễn',
                'class_type'        => 'OFFLINE',
                'tuition_fee'       => 3200000,
                'max_students'      => 10,
                'enrolled_students' => 2,
                'status'            => CourseClass::STATUS_OPEN,
                'display_order'     => 3,
                'version'           => 1,
            ],
        ];

        $studentNames = [
            'Nguyễn Văn An', 'Trần Thị Mai', 'Lê Hoàng Long', 'Phạm Minh Đức',
            'Đỗ Thị Ngọc', 'Vũ Quốc Huy', 'Bùi Thu Trang', 'Hoàng Gia Bảo',
            'Đặng Thùy Dung', 'Ngô Quang Khải', 'Dương Mỹ Linh', 'Hồ Tấn Tài',
            'Trịnh Thảo Nhi', 'Lý Quốc Cường', 'Võ Hoàng Yến', 'Phan Thanh Tùng',
            'Mai Anh Tuấn', 'Đoàn Thúy Vy', 'Lâm Khánh Chi', 'Tạ Đình Phong',
            'Lương Bích Hữu', 'Cao Thái Sơn', 'Hà Anh Tuấn', 'Đinh Tiến Đạt',
            'Trương Quỳnh Anh'
        ];

        $seedStudentsForClass = function(CourseClass $class, $course) use ($studentNames) {
            $count = $class->enrolled_students;
            for ($i = 0; $i < $count; $i++) {
                $name = $studentNames[$i % count($studentNames)] . ($i >= count($studentNames) ? ' (' . ($i + 1) . ')' : '');
                $phone = '09' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                $cleanName = \Illuminate\Support\Str::slug($name, '');
                $email = $cleanName . mt_rand(10, 99) . '@gmail.com';
                $price = (float)($class->tuition_fee > 0 ? $class->tuition_fee : 2600000);
                
                // 80% học viên đóng full, 20% học viên cọc
                $isFullPaid = ($i % 5 !== 4);
                $paidAmount = $isFullPaid ? $price : 1000000;

                Contact::create([
                    'type'                    => Contact::TYPE_CHECKOUT_COURSE,
                    'status'                  => Contact::STATUS_CONFIRMED,
                    'class_id'                => $class->id,
                    'class_allocation_status' => Contact::ALLOCATION_ALLOCATED,
                    'ip_address'              => '127.0.0.1',
                    'data'                    => [
                        'name'        => $name,
                        'phone'       => $phone,
                        'email'       => $email,
                        'paid_amount' => $paidAmount,
                        'course'      => [
                            'id'    => $course->id,
                            'title' => $course->translations->first()->title ?? $course->title,
                            'price' => $price,
                        ],
                        'class_code'  => $class->class_code,
                        'schedule'    => [
                            'time' => $class->schedule_time,
                            'day'  => $class->formatted_start_date,
                        ],
                    ],
                ]);
            }
        };

        foreach ($sketchupClasses as $c) {
            $createdClass = CourseClass::create($c);
            $seedStudentsForClass($createdClass, $sketchup);
        }

        $revitClasses = [
            [
                'course_id'         => $revit->id,
                'class_code'        => 'RVT-K18-T35',
                'name'              => 'Lớp Revit Kiến trúc K18 - Tối T3,T5',
                'start_date'        => '2026-12-05',
                'schedule_time'     => 'T3 + T5 (19h30 - 21h30)',
                'duration'          => '16 buổi',
                'mentor'            => 'KTS. Nguyễn Văn Hùng',
                'class_type'        => 'OFFLINE',
                'tuition_fee'       => 2800000,
                'max_students'      => 15,
                'enrolled_students' => 1,
                'status'            => CourseClass::STATUS_OPEN,
                'display_order'     => 1,
                'version'           => 1,
            ],
            [
                'course_id'         => $revit->id,
                'class_code'        => 'RVT-K19-T24',
                'name'              => 'Lớp Revit Kiến trúc K19 - Tối T2,T4',
                'start_date'        => '2026-09-12',
                'schedule_time'     => 'T2 + T4 (19h30 - 21h30)',
                'duration'          => '16 buổi',
                'mentor'            => 'KTS. Trần Quốc Tuấn',
                'class_type'        => 'OFFLINE',
                'tuition_fee'       => 2800000,
                'max_students'      => 12,
                'enrolled_students' => 12,
                'status'            => CourseClass::STATUS_FULL,
                'display_order'     => 5,
                'version'           => 1,
            ],
            [
                'course_id'         => $revit->id,
                'class_code'        => 'RVT-K20-ONLINE',
                'name'              => 'Lớp Revit Master K20 - Online Zoom',
                'start_date'        => '2026-10-12',
                'schedule_time'     => 'T3 + T6 (20h00 - 22h00)',
                'duration'          => '14 buổi',
                'mentor'            => 'KTS. Nguyễn Văn Hùng',
                'class_type'        => 'ONLINE',
                'tuition_fee'       => 2500000,
                'max_students'      => 25,
                'enrolled_students' => 23,
                'status'            => CourseClass::STATUS_OPEN,
                'display_order'     => 2,
                'version'           => 1,
            ],
            [
                'course_id'         => $revit->id,
                'class_code'        => 'RVT-K21-T7CN',
                'name'              => 'Lớp Revit Chuyên sâu K21 - Cuối tuần T7,CN',
                'start_date'        => '2026-09-22',
                'schedule_time'     => 'T7 + CN (14h00 - 17h00)',
                'duration'          => '14 buổi',
                'mentor'            => 'KTS. Trần Quốc Tuấn',
                'class_type'        => 'OFFLINE',
                'tuition_fee'       => 2800000,
                'max_students'      => 15,
                'enrolled_students' => 4,
                'status'            => CourseClass::STATUS_OPEN,
                'display_order'     => 3,
                'version'           => 1,
            ],
            [
                'course_id'         => $revit->id,
                'class_code'        => 'RVT-K22-DOAN',
                'name'              => 'Lớp Revit Triển khai Đồ án K22',
                'start_date'        => '2026-11-10',
                'schedule_time'     => 'T4 + T7 (19h30 - 21h30)',
                'duration'          => '12 buổi',
                'mentor'            => 'KTS. Lê Hoàng Kỳ',
                'class_type'        => 'OFFLINE',
                'tuition_fee'       => 3000000,
                'max_students'      => 10,
                'enrolled_students' => 9,
                'status'            => CourseClass::STATUS_OPEN,
                'display_order'     => 4,
                'version'           => 1,
            ],
        ];

        foreach ($revitClasses as $c) {
            $createdClass = CourseClass::create($c);
            $seedStudentsForClass($createdClass, $revit);
        }

        CourseClassOrderingService::syncScheduleOrderToCourse($sketchup->id);
        CourseClassOrderingService::syncScheduleOrderToCourse($revit->id);
        CourseClassOrderingService::clearCourseCache($sketchup->id);
        CourseClassOrderingService::clearCourseCache($revit->id);
    }
}
