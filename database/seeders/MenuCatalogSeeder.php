<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use JsonException;

class MenuCatalogSeeder extends Seeder
{
    /**
     * @throws JsonException
     */
    public function run(): void
    {
        $categoryImages = [
            'stone-oven' => 'category-images/From the oven.webp',
            'cold-appetizers' => 'category-images/cold.webp',
            'hot-appetizers' => 'category-images/Hot aptiezer.webp',
            'signature-sandwiches' => 'category-images/Signatur sandwish.webp',
            'charcoal-grills' => 'category-images/Charcol grill.webp',
            'usta-special-dishes' => 'category-images/Usta special dishes.webp',
            'traditional-oven-dishes' => 'category-images/Traditional oven dishis .webp',
            'side-dishes' => 'category-images/Side dish.webp',
            'turkish-desserts' => 'category-images/Kunafa.webp',
            'beverages' => 'category-images/Bavareg.webp',
        ];

        $payload = json_decode(
            file_get_contents(database_path('seeders/data/menu.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $categoryIds = [];

        foreach ($payload['menuCategories'] as $index => $category) {
            $record = Category::query()->updateOrCreate(
                ['slug' => $category['id']],
                [
                    'name_en' => $category['label']['en'],
                    'name_ar' => $category['label']['ar'],
                    'image_path' => $categoryImages[$category['id']] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );

            $categoryIds[$category['id']] = $record->id;
        }

        foreach ($payload['menuItems'] as $index => $item) {
            MenuItem::query()->updateOrCreate(
                [
                    'category_id' => $categoryIds[$item['category']],
                    'name_en' => $item['name']['en'],
                ],
                [
                    'name_ar' => $item['name']['ar'],
                    'description_en' => $item['description']['en'] ?? null,
                    'description_ar' => $item['description']['ar'] ?? null,
                    'price' => (float) str_replace(' AED', '', $item['price']),
                    'currency' => 'AED',
                    'image_path' => isset($item['image']) ? 'menu-items/' . $item['image'] : null,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
