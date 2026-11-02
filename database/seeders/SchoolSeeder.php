<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            // 15 trường ưu tiên đầu tiên (position 1 - 15)
            ['name' => 'Đại học Kiến trúc TP.HCM',            'short_name' => 'UAH',       'type' => 'DH',      'position' => 1],
            ['name' => 'Đại học Văn Lang',                     'short_name' => 'VLU',       'type' => 'DH',      'position' => 2],
            ['name' => 'Đại học Tôn Đức Thắng',                'short_name' => 'TDTU',      'type' => 'DH',      'position' => 3],
            ['name' => 'Đại học Bách Khoa TP.HCM',             'short_name' => 'HCMUT',     'type' => 'DH',      'position' => 4],
            ['name' => 'Đại học Kiến trúc Hà Nội',            'short_name' => 'HAU',       'type' => 'DH',      'position' => 5],
            ['name' => 'Đại học Xây dựng Hà Nội',              'short_name' => 'HUCE',      'type' => 'DH',      'position' => 6],
            ['name' => 'Đại học Sư phạm Kỹ thuật TP.HCM',     'short_name' => 'HCMUTE',    'type' => 'DH',      'position' => 7],
            ['name' => 'Đại học Kinh tế TP.HCM',               'short_name' => 'UEH',       'type' => 'DH',      'position' => 8],
            ['name' => 'Đại học Việt Đức',                     'short_name' => 'VGU',       'type' => 'DH',      'position' => 9],
            ['name' => 'Đại học Nguyễn Tất Thành',             'short_name' => 'NTTU',      'type' => 'DH',      'position' => 10],
            ['name' => 'Đại học Kiến trúc Đà Nẵng',           'short_name' => 'DAU',       'type' => 'DH',      'position' => 11],
            ['name' => 'Đại học Hồng Bàng',                    'short_name' => 'HIU',       'type' => 'DH',      'position' => 12],
            ['name' => 'Đại học Công nghệ Tp.HCM',             'short_name' => 'HUTECH',    'type' => 'DH',      'position' => 13],
            ['name' => 'Đại học Xây dựng Miền Trung',          'short_name' => 'MUCE',      'type' => 'DH',      'position' => 14],
            ['name' => 'Đại học Xây dựng Miền Tây',            'short_name' => 'MTU',       'type' => 'DH',      'position' => 15],

            // 35 trường còn lại (position 16 - 50)
            ['name' => 'Đại học Bách Khoa - Đại học Đà Nẵng',  'short_name' => 'DUT',       'type' => 'DH',      'position' => 16],
            ['name' => 'Đại học Giao thông Vận tải',           'short_name' => 'UTC/UTC2',  'type' => 'DH',      'position' => 17],
            ['name' => 'Đại học Thủ Dầu Một',                  'short_name' => 'TDMU',      'type' => 'DH',      'position' => 18],
            ['name' => 'Deakin University',                    'short_name' => 'MTU',       'type' => 'FOREIGN', 'position' => 19],
            ['name' => 'Đại học Hoa Sen',                      'short_name' => 'HSU',       'type' => 'DH',      'position' => 20],
            ['name' => 'Đại học Khoa học - Đại học Huế',        'short_name' => 'HUSC',      'type' => 'DH',      'position' => 21],
            ['name' => 'Đại học FPT',                          'short_name' => 'FPTU',      'type' => 'DH',      'position' => 22],
            ['name' => 'RMIT University',                      'short_name' => 'RMIT',      'type' => 'FOREIGN', 'position' => 23],
            ['name' => 'Đại học Duy Tân',                      'short_name' => 'DTU',       'type' => 'DH',      'position' => 24],
            ['name' => 'Đại học Swinburne',                    'short_name' => 'SU',        'type' => 'FOREIGN', 'position' => 25],
            ['name' => 'Đại học Cần Thơ / CĐ Cần Thơ',        'short_name' => 'CTU',       'type' => 'DH',      'position' => 26],
            ['name' => 'Curtin University',                    'short_name' => 'CU',        'type' => 'FOREIGN', 'position' => 27],
            ['name' => 'Đại học Sân khấu - Điện ảnh TPHCM',    'short_name' => 'SKDA',      'type' => 'DH',      'position' => 28],
            ['name' => 'UNSW Sydney',                          'short_name' => 'US',        'type' => 'FOREIGN', 'position' => 29],
            ['name' => 'Western Sydney University',            'short_name' => 'WSU',       'type' => 'FOREIGN', 'position' => 30],
            ['name' => 'University of Technology Sydney',      'short_name' => 'UTS',       'type' => 'FOREIGN', 'position' => 31],
            ['name' => 'CĐ Kiến trúc Xây dựng TP.HCM',         'short_name' => 'HCMCAC',    'type' => 'CD',      'position' => 32],
            ['name' => 'Đại học Mỹ thuật TP.HCM',             'short_name' => 'HCMUFA',    'type' => 'DH',      'position' => 33],
            ['name' => 'Đại học Yersin Đà Lạt',                'short_name' => 'YersinUni', 'type' => 'DH',      'position' => 34],
            ['name' => 'Monash University',                    'short_name' => 'MU',        'type' => 'FOREIGN', 'position' => 35],
            ['name' => 'Griffith University',                  'short_name' => 'GU',        'type' => 'FOREIGN', 'position' => 36],
            ['name' => 'Đại học Văn Hiến',                     'short_name' => 'VHU',       'type' => 'DH',      'position' => 37],
            ['name' => 'Đại học Mở TPHCM',                      'short_name' => 'OU',        'type' => 'DH',      'position' => 38],
            ['name' => 'Đại học Tài chính - Marketing',        'short_name' => 'UFM',       'type' => 'DH',      'position' => 39],
            ['name' => 'Đại học Trà Vinh',                     'short_name' => 'TVU',       'type' => 'DH',      'position' => 40],
            ['name' => 'Đại học Công thương TP.HCM',           'short_name' => 'HUIT',      'type' => 'DH',      'position' => 41],
            ['name' => 'Kingston University',                  'short_name' => 'KU',        'type' => 'FOREIGN', 'position' => 42],
            ['name' => 'University of Northampton',            'short_name' => 'MTU',       'type' => 'FOREIGN', 'position' => 43],
            ['name' => 'FPT Arena',                            'short_name' => 'FAN',       'type' => 'OTHER',   'position' => 44],
            ['name' => 'Đại học New South Wales',              'short_name' => 'UNSW',      'type' => 'FOREIGN', 'position' => 45],
            ['name' => 'Cao Đẳng kinh tế kỹ thuật Cần Thơ',    'short_name' => 'CTEC',      'type' => 'CD',      'position' => 46],
            ['name' => 'Đại học Mỹ thuật Công nghiệp',         'short_name' => 'UIFA',      'type' => 'DH',      'position' => 47],
            ['name' => 'Đại học Nam Cần Thơ',                  'short_name' => 'DNC/NCTU',  'type' => 'DH',      'position' => 48],
            ['name' => 'Đại học nông lâm thành phố Hồ Chí Minh','short_name' => 'NLU',      'type' => 'DH',      'position' => 49],
            ['name' => 'University of Washington',             'short_name' => 'UW',        'type' => 'FOREIGN', 'position' => 50],

            // Khác & Chưa cập nhật
            ['name' => 'Chưa cập nhật thông tin',              'short_name' => null,        'type' => 'OTHER',   'position' => 51],
            ['name' => 'Khác',                                 'short_name' => null,        'type' => 'OTHER',   'position' => 52],
        ];

        $existing = School::all();
        $usedIds = [];

        // Ánh xạ tên cũ sang tên mới cho các bản ghi đã có trong database
        $knownMap = [
            'Đại học Giao thông Vận tải'        => 'ĐH Giao thông Vận tải 2',
            'Đại học FPT'                       => 'ĐH FPT / FPT Arena',
            'Đại học Sân khấu - Điện ảnh TPHCM' => 'ĐH Sân khấu - Điện ảnh',
            'Đại học Mở TPHCM'                  => 'ĐH Mở',
        ];

        foreach ($schools as $item) {
            $target = null;

            // 1. Khớp chính xác tên
            $target = $existing->first(fn($e) => !in_array($e->id, $usedIds) && $e->name === $item['name']);

            // 2. Khớp theo ánh xạ tên cũ
            if (!$target && isset($knownMap[$item['name']])) {
                $target = $existing->first(fn($e) => !in_array($e->id, $usedIds) && $e->name === $knownMap[$item['name']]);
            }

            // 3. Khớp chuyển đổi tiền tố (Đại học -> ĐH, CĐ -> CD)
            if (!$target) {
                $alt1 = str_replace('Đại học ', 'ĐH ', $item['name']);
                $alt2 = str_replace('CĐ ', 'CD ', $item['name']);
                $target = $existing->first(fn($e) => !in_array($e->id, $usedIds) && ($e->name === $alt1 || $e->name === $alt2));
            }

            // 4. Khớp theo short_name duy nhất nếu có
            if (!$target && !empty($item['short_name'])) {
                $target = $existing->first(fn($e) => !in_array($e->id, $usedIds) && $e->short_name === $item['short_name']);
            }

            if ($target) {
                $usedIds[] = $target->id;
                $target->update([
                    'name'       => $item['name'],
                    'short_name' => $item['short_name'],
                    'type'       => $item['type'],
                    'status'     => 'ACTIVE',
                    'position'   => $item['position'],
                ]);
            } else {
                $created = School::create([
                    'name'       => $item['name'],
                    'short_name' => $item['short_name'],
                    'type'       => $item['type'],
                    'status'     => 'ACTIVE',
                    'position'   => $item['position'],
                ]);
                $usedIds[] = $created->id;
            }
        }

        $this->command->info('✅ Cập nhật thành công ' . count($schools) . ' trường học vào database!');
    }
}
