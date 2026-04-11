<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\TrackingCode;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $products = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('order')
            ->get();

        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('order')
            ->get();

        $settings = SiteSetting::allKeyed();

        $trackingCodes = TrackingCode::where('is_active', true)->get()->mapToGroups(function ($code) {
            return [$code->position => ['name' => $code->name, 'code' => $code->code, 'pages' => $code->pages]];
        });

        return Inertia::render('Home', [
            'products'      => $products,
            'categories'    => $categories,
            'testimonials'  => $testimonials,
            'settings'      => $settings,
            'trackingCodes' => $trackingCodes,
        ]);
    }

    private function formatProduct(Product $product): array
    {
        return [
            'id'                => $product->id,
            'name'              => $product->name,
            'slug'              => $product->slug,
            'short_description' => $product->short_description,
            'availability'      => $product->availability,
            'price_per_day'     => $product->price_per_day,
            'category'          => $product->category,
            'main_image_url'    => $product->main_image_url,
            'images'            => $product->images->map(fn($i) => [
                'id'         => $i->id,
                'url'        => $i->url,
                'alt_text'   => $i->alt_text,
                'is_primary' => $i->is_primary,
            ]),
        ];
    }
}
