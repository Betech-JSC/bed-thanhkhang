<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class ServiceContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Safe idempotent seeder using updateOrInsert / updateOrCreate without dropping or truncating tables.
     *
     * @return void
     */
    public function run()
    {
        // Standard services order according to Figma Node 2261:5549 (Figure 2):
        // 1: Gia công 3D (ID 2, Position 1)
        // 2: Thiết kế 3D (ID 1, Position 2 - Center Dark Slate #30374F)
        // 3: Custom (ID 3, Position 3)
        $services = [
            [
                'id' => 2,
                'key' => 'gia-cong-3d',
                'position' => 1,
            ],
            [
                'id' => 1,
                'key' => 'thiet-ke-3d',
                'position' => 2,
            ],
            [
                'id' => 3,
                'key' => 'lam-do-custom',
                'position' => 3,
            ],
        ];

        $locales = ['vi', 'en'];

        foreach ($services as $item) {
            $serviceId = $item['id'];
            $serviceKey = $item['key'];
            $position = $item['position'];
            $kbVi = Service::getServiceKnowledgeBase($serviceKey, 'vi');
            $serviceImage = [
                'path' => $kbVi['listing_image'] ?? '/images/services/service-design-3d.png',
                'alt' => $kbVi['listing_title'] ?? $kbVi['title'] ?? '',
            ];

            DB::table('services')->updateOrInsert(
                ['id' => $serviceId],
                [
                    'status' => Service::STATUS_ACTIVE,
                    'position' => $position,
                    'image' => json_encode($serviceImage, JSON_UNESCAPED_UNICODE),
                    'is_content_by_tab' => 1,
                    'updated_at' => now(),
                ]
            );
            $service = Service::find($serviceId);

            foreach ($locales as $locale) {
                $kb = Service::getServiceKnowledgeBase($serviceKey, $locale);

                $contentByTab = [
                    'hero' => $kb['hero'],
                    'toc_items' => $kb['toc_items'],
                    'sections' => $kb['sections'],
                    'steps' => $kb['steps'],
                    'listing' => [
                        'subtitle' => $kb['subtitle'] ?? '',
                        'page_description' => $kb['page_description'] ?? '',
                        'tags' => $kb['tags'] ?? [],
                        'prep_title' => $kb['prep_title'] ?? '',
                        'preparation' => $kb['preparation'] ?? '',
                        'comp_title' => $kb['comp_title'] ?? '',
                        'completion' => $kb['completion'] ?? '',
                        'cta_text' => $kb['cta_text'] ?? '',
                    ],
                ];

                $benefits = [
                    'checklist_items' => $kb['checklist_items'],
                ];

                // Check existing translation record
                $existing = DB::table('service_translations')
                    ->where('service_id', $serviceId)
                    ->where('locale', $locale)
                    ->first();

                $overviewDesc = $kb['overview_description'] ?? $kb['description'] ?? $kb['hero']['description'];
                $title = ($serviceId === 3 || $serviceKey === 'lam-do-custom') ? 'Custom' : $kb['title'];

                $data = [
                    'title' => $title,
                    'slug' => $serviceKey,
                    'description' => $overviewDesc,
                    'title_content' => $kb['hero']['headline'],
                    'content_by_tab' => json_encode($contentByTab, JSON_UNESCAPED_UNICODE),
                    'benefits' => json_encode($benefits, JSON_UNESCAPED_UNICODE),
                    'benefit_title' => $kb['sections']['section_03']['title'],
                ];

                if ($existing) {
                    DB::table('service_translations')
                        ->where('id', $existing->id)
                        ->update($data);
                } else {
                    $data['service_id'] = $serviceId;
                    $data['locale'] = $locale;
                    DB::table('service_translations')->insert($data);
                }
            }

            Service::clearServiceCaches($service);
        }

        Service::clearServiceCaches();
        if (function_exists('cache')) {
            cache()->flush();
        }

        $this->command?->info('ServiceContentSeeder executed safely and successfully!');
    }
}
