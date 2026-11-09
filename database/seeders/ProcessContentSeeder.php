<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Config;
use App\Models\Post\Post;

class ProcessContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Safe, idempotent seeder using updateOrCreate / updateOrInsert without dropping or truncating tables.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed 5 Process Steps into settings table (group 'general', key 'process_steps' and 'process_steps_en')
        $stepsVi = [
            [
                'number' => '01',
                'title' => 'Liên hệ',
                'title_en' => 'Contact',
                'subtitle' => 'Tiếp nhận & làm rõ nhu cầu',
                'subtitle_en' => 'Initial Consultation & Requirement Clarification',
                'description' => 'Mọi sản phẩm bắt đầu bằng một cuộc trao đổi. HH Maker tiếp nhận ý tưởng và làm rõ nhu cầu trước khi đề xuất hướng thực hiện.',
                'description_en' => 'Every product begins with a conversation. HH Maker receives your idea and clarifies requirements before proposing an execution direction.',
                'client_prep' => 'Gửi ý tưởng, ảnh tham khảo hoặc file 3D; nêu mục đích sử dụng, kích thước và số lượng dự kiến.',
                'client_prep_en' => 'Send your idea, reference images or 3D files; state intended use, dimensions, and estimated quantity.',
                'clientPrep' => 'Gửi ý tưởng, ảnh tham khảo hoặc file 3D; nêu mục đích sử dụng, kích thước và số lượng dự kiến.',
                'maker_action' => 'Xem xét tính khả thi, đặt câu hỏi để hiểu yêu cầu và tư vấn hướng thiết kế, vật liệu, cách gia công.',
                'maker_action_en' => 'Evaluate feasibility, ask clarifying questions, and advise on design direction, materials, and fabrication methods.',
                'makerAction' => 'Xem xét tính khả thi, đặt câu hỏi để hiểu yêu cầu và tư vấn hướng thiết kế, vật liệu, cách gia công.',
                'deliverables' => 'Tư vấn giải pháp kỹ thuật & định hướng vật liệu',
                'deliverables_en' => 'Technical consultation & material direction',
                'quality_standard' => 'Phản hồi nhanh chóng, nắm bắt đúng trọng tâm ý tưởng',
                'quality_standard_en' => 'Prompt response & accurate understanding of your concept',
                'isLast' => false,
            ],
            [
                'number' => '02',
                'title' => 'Báo giá',
                'title_en' => 'Quotation',
                'subtitle' => 'Phương án & chi phí minh bạch',
                'subtitle_en' => 'Transparent Plan & Pricing',
                'description' => 'Thống nhất những điều quan trọng trước khi bắt tay vào chế tác: phạm vi công việc, yêu cầu sản phẩm và phương án thực hiện.',
                'description_en' => 'Align on key details before crafting begins: scope of work, product specifications, and execution plan.',
                'client_prep' => 'Xác nhận thiết kế, kích thước, số lượng và các yêu cầu hoàn thiện; phản hồi những điểm cần điều chỉnh.',
                'client_prep_en' => 'Confirm design, dimensions, quantity, and finishing requirements; provide feedback on any needed adjustments.',
                'clientPrep' => 'Xác nhận thiết kế, kích thước, số lượng và các yêu cầu hoàn thiện; phản hồi những điểm cần điều chỉnh.',
                'maker_action' => 'Tổng hợp phương án và báo giá theo yêu cầu thực tế. Trao đổi các hạng mục trước khi khách hàng xác nhận triển khai.',
                'maker_action_en' => 'Compile the execution plan and quotation based on actual requirements. Discuss all items before confirming production.',
                'makerAction' => 'Tổng hợp phương án và báo giá theo yêu cầu thực tế. Trao đổi các hạng mục trước khi khách hàng xác nhận triển khai.',
                'deliverables' => 'Bảng báo giá chi tiết & tiến độ dự kiến',
                'deliverables_en' => 'Detailed quotation & estimated timeline',
                'quality_standard' => 'Minh bạch hạng mục, tối ưu chi phí theo nhu cầu thực tế',
                'quality_standard_en' => 'Transparent breakdown, cost-optimized for actual needs',
                'isLast' => false,
            ],
            [
                'number' => '03',
                'title' => 'Gia công 3D',
                'title_en' => '3D Fabrication',
                'subtitle' => 'Chế tác & hoàn thiện chi tiết',
                'subtitle_en' => 'Precision Printing & Finishing',
                'description' => 'Từ mô hình số đến sản phẩm vật lý. Thiết kế, vật liệu và quy trình in được kết nối trong từng chi tiết.',
                'description_en' => 'From digital model to physical product. Design, materials, and printing processes come together in every detail.',
                'client_prep' => 'Xác nhận các điều chỉnh kỹ thuật nếu có để phương án gia công phù hợp với nhu cầu đã trao đổi.',
                'client_prep_en' => 'Confirm any technical adjustments so the fabrication plan matches the agreed requirements.',
                'clientPrep' => 'Xác nhận các điều chỉnh kỹ thuật nếu có để phương án gia công phù hợp với nhu cầu đã trao đổi.',
                'maker_action' => 'Chuẩn bị mô hình, thực hiện in 3D và hoàn thiện theo phương án đã chốt; kiểm tra hình dáng, bề mặt và chi tiết sản phẩm.',
                'maker_action_en' => 'Prepare the model, execute 3D printing and post-processing per the approved plan; inspect shape, surface, and fine details.',
                'makerAction' => 'Chuẩn bị mô hình, thực hiện in 3D và hoàn thiện theo phương án đã chốt; kiểm tra hình dáng, bề mặt và chi tiết sản phẩm.',
                'deliverables' => 'Sản phẩm hoàn thiện theo đúng thông số đã duyệt',
                'deliverables_en' => 'Finished product matching approved specifications',
                'quality_standard' => 'Chuẩn xác kích thước, bề mặt mịn và kết cấu vững chắc',
                'quality_standard_en' => 'Dimensional accuracy, smooth surface, and structural integrity',
                'isLast' => false,
            ],
            [
                'number' => '04',
                'title' => 'Đóng gói',
                'title_en' => 'Packaging',
                'subtitle' => 'Kiểm định & bảo vệ an toàn',
                'subtitle_en' => 'Inspection & Protective Packaging',
                'description' => 'Sản phẩm sau gia công được kiểm tra và chuẩn bị cho hành trình vận chuyển.',
                'description_en' => 'Fabricated products are carefully inspected and prepared for safe transit.',
                'client_prep' => 'Cung cấp thông tin người nhận và lưu ý về việc giao nhận hoặc cách đóng gói nếu có yêu cầu riêng.',
                'client_prep_en' => 'Provide recipient details and any special delivery or packaging notes if needed.',
                'clientPrep' => 'Cung cấp thông tin người nhận và lưu ý về việc giao nhận hoặc cách đóng gói nếu có yêu cầu riêng.',
                'maker_action' => 'Kiểm tra sản phẩm, sắp xếp các chi tiết và lựa chọn cách bảo vệ, đóng gói phù hợp với hình dáng của từng món đồ.',
                'maker_action_en' => 'Inspect the product, arrange components, and select protective packaging tailored to each item\'s geometry.',
                'makerAction' => 'Kiểm tra sản phẩm, sắp xếp các chi tiết và lựa chọn cách bảo vệ, đóng gói phù hợp với hình dáng của từng món đồ.',
                'deliverables' => 'Kiện hàng đóng gói chống sốc tiêu chuẩn',
                'deliverables_en' => 'Shock-resistant packaged parcel',
                'quality_standard' => 'Bảo vệ tối đa các chi tiết mảnh và bề mặt hoàn thiện',
                'quality_standard_en' => 'Maximum protection for delicate parts and finished surfaces',
                'isLast' => false,
            ],
            [
                'number' => '05',
                'title' => 'Gửi hàng',
                'title_en' => 'Delivery',
                'subtitle' => 'Bàn giao & đồng hành sau nhận hàng',
                'subtitle_en' => 'Handover & Post-Delivery Support',
                'description' => 'Khép lại quá trình chế tác, mở đầu trải nghiệm sử dụng sản phẩm của bạn.',
                'description_en' => 'Completing the crafting journey and beginning your experience with the product.',
                'client_prep' => 'Xác nhận địa chỉ, số điện thoại nhận hàng; kiểm tra sản phẩm khi nhận và trao đổi nếu cần hỗ trợ.',
                'client_prep_en' => 'Confirm shipping address and phone number; inspect the product upon arrival and reach out if support is needed.',
                'clientPrep' => 'Xác nhận địa chỉ, số điện thoại nhận hàng; kiểm tra sản phẩm khi nhận và trao đổi nếu cần hỗ trợ.',
                'maker_action' => 'Bàn giao kiện hàng để vận chuyển, cung cấp thông tin gửi hàng và tiếp nhận phản hồi sau khi khách hàng nhận sản phẩm.',
                'maker_action_en' => 'Hand over the parcel for shipping, provide tracking details, and follow up after you receive the product.',
                'makerAction' => 'Bàn giao kiện hàng để vận chuyển, cung cấp thông tin gửi hàng và tiếp nhận phản hồi sau khi khách hàng nhận sản phẩm.',
                'deliverables' => 'Bàn giao sản phẩm & mã vận đơn theo dõi',
                'deliverables_en' => 'Product handover & tracking information',
                'quality_standard' => 'Giao hàng đúng hẹn, hỗ trợ tận tâm sau bàn giao',
                'quality_standard_en' => 'On-time delivery & dedicated post-handover support',
                'isLast' => true,
            ],
        ];

        $stepsEn = array_map(function ($step) {
            return [
                'number' => $step['number'],
                'title' => $step['title_en'],
                'subtitle' => $step['subtitle_en'],
                'description' => $step['description_en'],
                'client_prep' => $step['client_prep_en'],
                'clientPrep' => $step['client_prep_en'],
                'maker_action' => $step['maker_action_en'],
                'makerAction' => $step['maker_action_en'],
                'deliverables' => $step['deliverables_en'],
                'quality_standard' => $step['quality_standard_en'],
                'isLast' => $step['isLast'],
            ];
        }, $stepsVi);

        DB::table('settings')->updateOrInsert(
            ['group' => 'general', 'name' => 'process_steps'],
            [
                'val' => json_encode($stepsVi, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['group' => 'general', 'name' => 'process_steps_en'],
            [
                'val' => json_encode($stepsEn, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        try {
            if (function_exists('settings')) {
                settings()->group('general')->set('process_steps', json_encode($stepsVi, JSON_UNESCAPED_UNICODE));
                settings()->group('general')->set('process_steps_en', json_encode($stepsEn, JSON_UNESCAPED_UNICODE));
                settings()->group('general')->flushCache();
            }
        } catch (\Throwable $e) {
            // Ignore settings helper exceptions
        }

        // 1b. Also seed 5 Process Steps into posts (type = 'PROCESS_STEPS') for CMS & HomeController sync
        foreach ($stepsVi as $idx => $stepItem) {
            $position = $idx + 1;
            $stepPost = Post::updateOrCreate(
                [
                    'type' => 'PROCESS_STEPS',
                    'position' => $position,
                ],
                [
                    'status' => Post::STATUS_ACTIVE,
                    'published_at' => now(),
                    'is_home' => true,
                    'is_featured' => false,
                ]
            );

            DB::table('post_translations')->updateOrInsert(
                [
                    'post_id' => $stepPost->id,
                    'locale' => 'vi',
                ],
                [
                    'title' => $stepItem['title'],
                    'slug' => Str::slug('quy-trinh-' . $stepItem['number'] . '-' . $stepItem['title']),
                    'description' => $stepItem['description'],
                    'content' => $stepItem['maker_action'] . "\n" . $stepItem['client_prep'],
                ]
            );

            DB::table('post_translations')->updateOrInsert(
                [
                    'post_id' => $stepPost->id,
                    'locale' => 'en',
                ],
                [
                    'title' => $stepItem['title_en'],
                    'slug' => Str::slug('process-' . $stepItem['number'] . '-' . $stepItem['title_en']),
                    'description' => $stepItem['description_en'],
                    'content' => $stepItem['maker_action_en'] . "\n" . $stepItem['client_prep_en'],
                ]
            );
        }

        // 2. Seed 6 FAQ items into posts & post_translations
        $faqs = [
            [
                'position' => 1,
                'vi' => [
                    'question' => 'Tôi chưa có sẵn file thiết kế 3D, bên mình có hỗ trợ dựng file không?',
                    'answer' => 'Có. HH MAKER hỗ trợ dựng file 3D từ ý tưởng, bản phác thảo hoặc hình ảnh tham khảo của bạn. Bạn chỉ cần cung cấp kích thước, công năng và yêu cầu mong muốn; đội ngũ sẽ tư vấn phương án thiết kế, báo chi phí dựng file và gửi bản 3D để bạn duyệt trước khi sản xuất.',
                ],
                'en' => [
                    'question' => 'I do not have a 3D CAD file yet, does HH Maker support 3D modeling from scratch?',
                    'answer' => 'Yes. HH Maker provides full 3D modeling support from your concepts, sketches, or reference photos. You simply share dimensions, functions, and aesthetic preferences; our team will advise on designs, provide quote estimates, and send 3D renders for your approval before production.',
                ],
            ],
            [
                'position' => 2,
                'vi' => [
                    'question' => 'Để nhận được bảng báo giá chính xác và nhanh chóng nhất cho dự án của mình, tôi cần cung cấp những gì?',
                    'answer' => 'Bạn chỉ cần gửi file thiết kế 3D (nếu có định dạng .STL, .OBJ, .STEP...), kích thước mong muốn, yêu cầu về độ mịn, vật liệu hoặc ảnh mô tả ý tưởng. Đội ngũ kỹ thuật HH Maker sẽ phản hồi và gửi báo giá chi tiết trong vòng 2-4 giờ làm việc.',
                ],
                'en' => [
                    'question' => 'What information should I provide to get the fastest and most accurate quotation?',
                    'answer' => 'Simply share your 3D design files (if available in .STL, .OBJ, .STEP, .3MF format), desired dimensions, surface finish expectations, material preferences, or reference imagery. Our engineering team will respond with a comprehensive quote within 2-4 business hours.',
                ],
            ],
            [
                'position' => 3,
                'vi' => [
                    'question' => 'Tôi muốn đặt sản phẩm theo ý tưởng riêng hoặc điều chỉnh khác đi so với các mẫu có sẵn thì có được không?',
                    'answer' => 'Hoàn toàn được! Dịch vụ làm đồ Custom và chế tác theo yêu cầu là thế mạnh hàng đầu tại HH Maker. Chúng tôi sẵn sàng tinh chỉnh kích thước, tỷ lệ, phối màu hoặc thiết kế riêng biệt hoàn toàn theo concept của bạn.',
                ],
                'en' => [
                    'question' => 'Can I request a custom product based on my own concept or modify an existing model?',
                    'answer' => 'Absolutely! Bespoke custom crafting and on-demand fabrication are HH Maker\'s core strengths. We are ready to adjust dimensions, proportions, color schemes, or develop entirely new designs matching your vision.',
                ],
            ],
            [
                'position' => 4,
                'vi' => [
                    'question' => 'Thời gian in và giao hàng thường mất bao lâu?',
                    'answer' => 'Thời gian hoàn thiện phụ thuộc vào kích thước, công nghệ và số lượng sản phẩm. Với các mẫu in thử đơn chiếc, thời gian gia công thường từ 24 - 48 giờ. Đơn hàng số lượng lớn sẽ được lên lịch sản xuất chi tiết.',
                ],
                'en' => [
                    'question' => 'How long does 3D printing and delivery typically take?',
                    'answer' => 'Turnaround time depends on dimensions, printing technology, and batch volume. Single prototypes are typically completed within 24-48 hours. Volume orders are scheduled with transparent delivery timelines.',
                ],
            ],
            [
                'position' => 5,
                'vi' => [
                    'question' => 'HH Maker hỗ trợ những định dạng file 3D nào?',
                    'answer' => 'Chúng tôi tiếp nhận hầu hết các định dạng 3D tiêu chuẩn bao gồm STL, OBJ, STEP, 3MF, IGES, SLDPRT. Nếu bạn chỉ có bản vẽ 2D hoặc hình phác thảo, HH Maker sẽ hỗ trợ dựng lại thành file 3D.',
                ],
                'en' => [
                    'question' => 'Which 3D file formats does HH Maker support?',
                    'answer' => 'We accept standard 3D formats including STL, OBJ, STEP, 3MF, IGES, and SLDPRT. If you only have 2D drawings or reference images, our engineers will help convert them into 3D printable files.',
                ],
            ],
            [
                'position' => 6,
                'vi' => [
                    'question' => 'Sản phẩm in 3D có độ bền và khả năng chịu lực như thế nào?',
                    'answer' => 'Độ bền phụ thuộc vào loại vật liệu được lựa chọn: từ PLA+ thân thiện, PETG chịu va đập, ABS/Nylon chịu nhiệt và cơ tính cao, đến Carbon Fiber siêu cứng hoặc Resin 8K độ mịn tuyệt đối. Kỹ sư sẽ tư vấn loại nhựa tối ưu theo mục đích sử dụng của bạn.',
                ],
                'en' => [
                    'question' => 'What is the durability and mechanical strength of 3D printed products?',
                    'answer' => 'Durability depends on the chosen material: eco-friendly PLA+, impact-resistant PETG, heat-resistant ABS/Nylon, high-stiffness Carbon Fiber, or ultra-smooth 8K Resin. Our engineers advise on the optimal material suited to your application.',
                ],
            ],
        ];

        foreach ($faqs as $faqItem) {
            $position = $faqItem['position'];
            $post = Post::updateOrCreate(
                [
                    'type' => Post::TYPE_FAQ,
                    'position' => $position,
                ],
                [
                    'status' => Post::STATUS_ACTIVE,
                    'published_at' => now(),
                    'is_home' => false,
                    'is_featured' => false,
                ]
            );

            // VI Translation
            DB::table('post_translations')->updateOrInsert(
                [
                    'post_id' => $post->id,
                    'locale' => 'vi',
                ],
                [
                    'title' => $faqItem['vi']['question'],
                    'slug' => Str::slug($faqItem['vi']['question']),
                    'description' => $faqItem['vi']['answer'],
                    'content' => $faqItem['vi']['answer'],
                ]
            );

            // EN Translation
            DB::table('post_translations')->updateOrInsert(
                [
                    'post_id' => $post->id,
                    'locale' => 'en',
                ],
                [
                    'title' => $faqItem['en']['question'],
                    'slug' => Str::slug($faqItem['en']['question']),
                    'description' => $faqItem['en']['answer'],
                    'content' => $faqItem['en']['answer'],
                ]
            );

            Post::clearPostCaches($post);
        }

        Config::clearBrandCaches();
        if (function_exists('cache')) {
            cache()->flush();
        }

        $this->command?->info('ProcessContentSeeder executed safely and successfully!');
    }
}
