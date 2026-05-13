<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $items = MenuItem::query()
            ->with('category:id,slug')
            ->where('is_active', true)
            ->whereHas('category', fn($query) => $query->where('is_active', true))
            ->orderBy('sort_order')
            ->get();

        return new JsonResponse([
            'categories' => $categories->map(fn(Category $category) => [
                'id' => $category->slug,
                'label' => [
                    'en' => $category->name_en,
                    'ar' => $category->name_ar,
                ],
                'image' => $category->image_path
                    ? $request->getSchemeAndHttpHost() . '/storage/' . ltrim($category->image_path, '/')
                    : null,
            ])->values(),
            'items' => $items->map(fn(MenuItem $item) => [
                'id' => $item->id,
                'name' => [
                    'en' => $item->name_en,
                    'ar' => $item->name_ar,
                ],
                'category' => $item->category->slug,
                'price' => $item->formatted_price,
                'description' => $item->description_en || $item->description_ar
                    ? [
                        'en' => $item->description_en,
                        'ar' => $item->description_ar,
                    ]
                    : null,
                'image' => $item->image_path
                    ? $request->getSchemeAndHttpHost() . '/storage/' . ltrim($item->image_path, '/')
                    : null,
            ])->values(),
        ]);
    }
}
