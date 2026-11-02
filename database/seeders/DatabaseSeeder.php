<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use JamstackVietnam\Tag\Models\Tag;
use JamstackVietnam\Job\Models\Job;
use App\Models\History;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Truncate tables before seeding to avoid duplicates and ensure clean seeding
        \Schema::disableForeignKeyConstraints();
        PostCategory::truncate();
        Post::truncate();
        Tag::truncate();
        Job::truncate();
        History::truncate();
        \App\Models\User::truncate();
        \DB::table('post_translations')->truncate();
        \DB::table('post_category_translations')->truncate();
        \DB::table('tag_translations')->truncate();
        \DB::table('job_translations')->truncate();
        \DB::table('history_translations')->truncate();
        \DB::table('post_ref_categories')->truncate();
        \DB::table('post_ref_tags')->truncate();
        \DB::table('related_posts')->truncate();
        \Schema::enableForeignKeyConstraints();

        // Call standard seeders
        $this->call([
            RoleSeeder::class,
            AgencySeeder::class,
            SchoolSeeder::class,
            ProductSeeder::class,
            PointPackageSeeder::class,
            SynologyResourceSeeder::class,
            UserRankSeeder::class,
        ]);

        // Seed dynamic data using factories
        $categories = PostCategory::factory()->count(5)->create();
        $tags = Tag::factory()->count(10)->create();

        // Seed normal posts
        Post::factory()->count(20)->create([
            'type' => Post::TYPE_POST
        ])->each(function ($post) use ($categories, $tags) {
            $post->categories()->attach($categories->random(rand(1, 2))->pluck('id')->toArray());
            $post->tags()->attach($tags->random(rand(1, 3))->pluck('id')->toArray());
        });

        // Seed course categories
        $courseCategories = PostCategory::factory()->count(3)->create([
            'type' => PostCategory::TYPE_COURSE,
            'status' => PostCategory::STATUS_ACTIVE,
            'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Course Category Image']
        ]);

        // Seed courses (type = COURSE)
        $courses = Post::factory()->count(5)->create([
            'type' => Post::TYPE_COURSE,
            'status' => Post::STATUS_ACTIVE,
            'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Course Image'],
            'icon' => ['path' => 'assets/images/funky.png', 'alt' => 'Course Icon'],
        ]);

        $courses->each(function ($course) use ($courseCategories, $tags) {
            $course->categories()->attach($courseCategories->random(rand(1, 2))->pluck('id')->toArray());
            $course->tags()->attach($tags->random(rand(1, 3))->pluck('id')->toArray());
        });

        // Link seeded courses to products in product_ref_posts
        $productIds = \App\Models\Product\Product::pluck('id')->toArray();
        if (!empty($productIds)) {
            \DB::table('product_ref_posts')->truncate();
            $productRefPosts = [];
            foreach ($productIds as $productId) {
                // Link each product to 1-2 random courses
                $randomCourses = $courses->random(min($courses->count(), rand(1, 2)));
                foreach ($randomCourses as $course) {
                    $productRefPosts[] = [
                        'product_id' => $productId,
                        'post_id' => $course->id,
                    ];
                }
            }
            \DB::table('product_ref_posts')->insert($productRefPosts);
        }

        // Seed resource categories and resource posts (Tài nguyên)
        $resourceCategories = PostCategory::factory()->count(3)->create([
            'type' => PostCategory::TYPE_RESOURCE,
            'status' => PostCategory::STATUS_ACTIVE,
            'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Resource Category Image']
        ]);

        // Seed 4 Free Resources
        Post::factory()->count(4)->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
            'resource_type' => Post::TYPE_RESOURCE_FREE,
            'points_cost' => 0,
            'link' => 'https://funkystyle.vn/assets/images/funky.png',
            'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Resource Image'],
            'icon' => ['path' => 'assets/images/funky.png', 'alt' => 'Resource Icon'],
        ])->each(function ($resource) use ($resourceCategories, $tags) {
            $resource->categories()->attach($resourceCategories->random(rand(1, 2))->pluck('id')->toArray());
            $resource->tags()->attach($tags->random(rand(1, 3))->pluck('id')->toArray());
        });

        // Seed 4 Paid Resources
        Post::factory()->count(4)->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
            'resource_type' => Post::TYPE_RESOURCE_PAY,
            'points_cost' => 10,
            'link' => 'https://funkystyle.vn/assets/images/funky.png',
            'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Resource Image'],
            'icon' => ['path' => 'assets/images/funky.png', 'alt' => 'Resource Icon'],
        ])->each(function ($resource) use ($resourceCategories, $tags) {
            $resource->categories()->attach($resourceCategories->random(rand(1, 2))->pluck('id')->toArray());
            $resource->tags()->attach($tags->random(rand(1, 3))->pluck('id')->toArray());
        });

        // Seed prizes (Giải thưởng)
        Post::factory()->count(6)->create([
            'type' => Post::TYPE_PRIZE,
            'status' => Post::STATUS_ACTIVE,
            'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Prize Image'],
            'icon' => ['path' => 'assets/images/funky.png', 'alt' => 'Prize Icon'],
        ]);

        // Seed project categories and project posts (Đồ án)
        $projectCategories = PostCategory::factory()->count(3)->create([
            'type' => PostCategory::TYPE_PROJECT,
            'status' => PostCategory::STATUS_ACTIVE,
            'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Project Category Image']
        ]);

        Post::factory()->count(8)->create([
            'type' => Post::TYPE_PROJECT,
            'status' => Post::STATUS_ACTIVE,
            'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Project Image'],
            'icon' => ['path' => 'assets/images/funky.png', 'alt' => 'Project Icon'],
        ])->each(function ($project) use ($projectCategories, $tags) {
            $project->categories()->attach($projectCategories->random(rand(1, 2))->pluck('id')->toArray());
            $project->tags()->attach($tags->random(rand(1, 3))->pluck('id')->toArray());
        });

        // Seed feedbacks (Feedback)
        Post::factory()->count(8)->create([
            'type' => Post::TYPE_FEEDBACK,
            'status' => Post::STATUS_ACTIVE,
            'image' => ['path' => 'assets/images/funky.png', 'alt' => 'Feedback Image'],
            'icon' => ['path' => 'assets/images/funky.png', 'alt' => 'Feedback Icon'],
        ])->each(function ($feedback) use ($categories, $tags) {
            $feedback->categories()->attach($categories->random(rand(1, 2))->pluck('id')->toArray());
            $feedback->tags()->attach($tags->random(rand(1, 3))->pluck('id')->toArray());
        });

        Job::factory()->count(10)->create();
        History::factory()->count(10)->create();
        \App\Models\User::factory()->count(10)->create();

        \App\Models\User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => \Hash::make('admin@gmail.com'),
                'points' => 100,
                'status' => \App\Models\User::STATUS_ACTIVE,
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'testuser@gmail.com'],
            [
                'name' => 'Test User',
                'password' => \Hash::make('12345678'),
                'points' => 20,
                'status' => \App\Models\User::STATUS_ACTIVE,
            ]
        );
    }
}
