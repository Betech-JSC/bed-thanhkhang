<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use Illuminate\Support\Str;
use App\Services\SynologyService;

class SynologyResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonFile = base_path('backupvalue.json');
        if (!file_exists($jsonFile)) {
            if (isset($this->command)) {
                $this->command->error('File backupvalue.json không tồn tại!');
            }
            return;
        }

        $links = json_decode(file_get_contents($jsonFile), true);
        if (empty($links)) {
            if (isset($this->command)) {
                $this->command->error('Không tìm thấy link trong backupvalue.json!');
            }
            return;
        }

        if (isset($this->command)) {
            $this->command->info("Bắt đầu nạp " . count($links) . " tài nguyên từ backupvalue.json vào Database...");
        }

        // Tạo Danh mục tài nguyên nếu chưa có
        $category = PostCategory::firstOrCreate(
            ['type' => PostCategory::TYPE_RESOURCE],
            [
                'status' => PostCategory::STATUS_ACTIVE,
                'title' => 'Tài nguyên Synology',
                'slug' => 'tai-nguyen-synology',
                'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Category Image']
            ]
        );

        $synologyService = new SynologyService();
        $count = 0;

        foreach ($links as $id => $link) {
            $id = (int)$id;
            
            // Tự động resolve NAS path nếu link là Synology sharing URL
            $finalPath = $link;
            if (str_starts_with($link, 'http')) {
                $resolved = $synologyService->resolveNasPath($link);
                if ($resolved) {
                    $finalPath = $resolved;
                }
            }

            $title = "Tài nguyên #" . $id;
            if (str_starts_with($finalPath, '/')) {
                $title = basename($finalPath);
            }

            $post = Post::updateOrCreate(
                ['id' => $id],
                [
                    'type' => Post::TYPE_RESOURCE,
                    'status' => Post::STATUS_ACTIVE,
                    'resource_type' => ($id % 2 === 0) ? Post::TYPE_RESOURCE_FREE : Post::TYPE_RESOURCE_PAY,
                    'points_cost' => ($id % 2 === 0) ? 0 : rand(5, 20),
                    'link' => $finalPath,
                    'image' => ['path' => 'assets/images/funky.png', 'alt' => $title],
                    'icon' => ['path' => 'assets/images/funky.png', 'alt' => $title],
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . $id,
                    'description' => 'Tài nguyên mẫu nạp tự động từ backupvalue.json (ID #' . $id . ')',
                    'published_at' => now(),
                    'view_count' => rand(10, 500),
                ]
            );

            if ($category) {
                $post->categories()->syncWithoutDetaching([$category->id]);
            }

            $count++;
        }

        if (isset($this->command)) {
            $this->command->info("✅ ĐÃ NẠP THÀNH CÔNG {$count} TÀI NGUYÊN VÀO DATABASE LOCAL!");
        }
    }
}
