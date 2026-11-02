<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key constraints and truncate tables
        \Schema::disableForeignKeyConstraints();
        DB::table('order_lines')->truncate();
        DB::table('orders')->truncate();
        DB::table('contacts')->where('type', 'TYPE_CHECKOUT_COURSE')->delete();
        \Schema::enableForeignKeyConstraints();

        // 1. Ensure products 1-6 have SKUs
        $skus = ['PROD001', 'PROD002', 'PROD003', 'PROD004', 'PROD005', 'PROD006'];
        foreach ($skus as $idx => $sku) {
            $p = Product::find($idx + 1);
            if ($p) {
                $p->update(['sku' => $sku]);
            }
        }

        // 2. Create Order 1 (Products/Equipment Order)
        $order1 = Order::create([
            'order_number' => 'ORD0001',
            'payment_method' => Order::PAYMENT_METHOD_COD,
            'status' => Order::STATUS_NEW,
            'name' => 'Nguyễn Văn A',
            'phone' => '0901234567',
            'email' => 'nguyenvana@gmail.com',
            'total_price' => 5000000,
            'subtotal_price' => 4000000,
            'deposit_fee' => 1000000,
            'note' => 'Đơn hàng mẫu 1 - Đồng bộ Sheets',
            'start_rental_date' => '2026-07-15',
            'end_rental_date' => '2026-07-20',
            'rental_date' => 5,
            'rental' => Order::PERSONAL,
            'financial_status' => Order::FINANCIAL_STATUS_PENDING,
            'shipping_address' => [
                'address' => '123 Nguyễn Trãi, Phường 2, Quận 5',
                'city' => 'Hồ Chí Minh'
            ]
        ]);

        OrderLine::create([
            'order_id' => $order1->id,
            'product_id' => 1,
            'price' => 1500000,
            'sale_price' => 1500000,
            'is_sale' => false,
            'quantity' => 2
        ]);

        // 3. Create Sample Course Orders (TYPE_CHECKOUT_COURSE in contacts table)
        Contact::create([
            'type' => 'TYPE_CHECKOUT_COURSE',
            'status' => Contact::STATUS_NEW,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'request_url' => 'http://localhost:8000/checkout',
            'data' => [
                'name' => 'Nguyễn Văn A',
                'phone' => '0901234567',
                'email' => 'nguyenvana@gmail.com',
                'link_facebook' => 'https://facebook.com/nguyenvana',
                'note' => 'Cần tư vấn thêm về lịch học và tài liệu',
                'course' => [
                    'id' => 1,
                    'title' => 'Khóa học AutoCAD chuyên nghiệp',
                    'price' => 1500000,
                    'schedule' => [
                        'day' => 'Tối thứ 2, 4, 6 (18:30 - 21:00)'
                    ]
                ],
                'Hiện tại bạn đang là' => 'Sinh viên',
                'Trường bạn đã/đang học' => 'Đại học Kiến trúc TP.HCM',
                'Ngành học hiện tại' => 'Thiết kế nội thất',
                'Biết funky từ đâu' => 'Facebook',
                'Bạn có muốn' => 'Có',
                'Bạn đã từng học' => 'Chưa'
            ]
        ]);

        Contact::create([
            'type' => 'TYPE_CHECKOUT_COURSE',
            'status' => Contact::STATUS_PROCESSED,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'request_url' => 'http://localhost:8000/checkout',
            'data' => [
                'name' => 'Trần Thị B',
                'phone' => '0912345678',
                'email' => 'tranthib@gmail.com',
                'link_facebook' => 'https://facebook.com/tranthib',
                'note' => 'Đã đóng đủ học phí và nhận link lớp học',
                'course' => [
                    'id' => 2,
                    'title' => 'Khóa học Revit Architecture dựng hình 3D',
                    'price' => 3500000,
                    'schedule' => [
                        'day' => 'Tối thứ 3, 5, 7 (19:00 - 21:30)'
                    ]
                ],
                'Hiện tại bạn đang là' => 'Người đi làm',
                'Trường bạn đã/đang học' => 'Đại học Bách Khoa TP.HCM',
                'Ngành học hiện tại' => 'Kỹ thuật xây dựng',
                'Biết funky từ đâu' => 'Google Search',
                'Bạn có muốn' => 'Có',
                'Bạn đã từng học' => 'Rồi'
            ]
        ]);

        Contact::create([
            'type' => 'TYPE_CHECKOUT_COURSE',
            'status' => Contact::STATUS_RESPONSE,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'request_url' => 'http://localhost:8000/checkout',
            'data' => [
                'name' => 'Lê Văn C',
                'phone' => '0987654321',
                'email' => 'levanc@gmail.com',
                'link_facebook' => 'https://facebook.com/levanc',
                'note' => 'Hỏi thăm chính sách ưu đãi cho học viên cũ',
                'course' => [
                    'id' => 3,
                    'title' => 'Khóa học 3ds Max & Vray Render chuyên sâu',
                    'price' => 2800000,
                    'schedule' => [
                        'day' => 'Chiều thứ 7, Chủ nhật (14:00 - 17:00)'
                    ]
                ],
                'Hiện tại bạn đang là' => 'Sinh viên',
                'Trường bạn đã/đang học' => 'Đại học Mỹ thuật TP.HCM',
                'Ngành học hiện tại' => 'Thiết kế đồ họa',
                'Biết funky từ đâu' => 'Bạn bè giới thiệu',
                'Bạn có muốn' => 'Không',
                'Bạn đã từng học' => 'Chưa'
            ]
        ]);
    }
}
