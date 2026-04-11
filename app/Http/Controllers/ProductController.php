<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\TrackingCode;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function show(string $slug): Response
    {
        $product = Product::with(["category", "images"])
            ->where("slug", $slug)
            ->where("is_active", true)
            ->firstOrFail();

        $relatedProducts = Product::with(["images"])
            ->where("category_id", $product->category_id)
            ->where("id", "!=", $product->id)
            ->where("is_active", true)
            ->limit(4)
            ->get()
            ->map(fn($p) => [
                "id"             => $p->id,
                "name"           => $p->name,
                "slug"           => $p->slug,
                "short_description" => $p->short_description,
                "availability"   => $p->availability,
                "price_per_day"  => $p->price_per_day,
                "main_image_url" => $p->main_image_url,
            ]);

        $settings = SiteSetting::allKeyed();

        $trackingCodes = TrackingCode::where("is_active", true)->get()->mapToGroups(function ($code) {
            return [$code->position => ["name" => $code->name, "code" => $code->code, "pages" => $code->pages]];
        });

        return Inertia::render("Products/Show", [
            "product" => [
                "id"                => $product->id,
                "name"              => $product->name,
                "slug"              => $product->slug,
                "description"       => $product->description,
                "short_description" => $product->short_description,
                "availability"      => $product->availability,
                "price_per_day"     => $product->price_per_day,
                "meta_title"        => $product->meta_title,
                "meta_description"  => $product->meta_description,
                "meta_keywords"     => $product->meta_keywords,
                "category"          => $product->category,
                "images"            => $product->images->map(fn($i) => [
                    "id"         => $i->id,
                    "url"        => $i->url,
                    "alt_text"   => $i->alt_text,
                    "is_primary" => $i->is_primary,
                ]),
            ],
            "relatedProducts" => $relatedProducts,
            "settings"        => $settings,
            "trackingCodes"   => $trackingCodes,
        ]);
    }
}
