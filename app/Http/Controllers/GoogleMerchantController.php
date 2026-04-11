<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Response;

class GoogleMerchantController extends Controller
{
    public function feed(): Response
    {
        $products = Product::with(["category", "images"])
            ->where("is_active", true)
            ->get();

        $settings = SiteSetting::allKeyed();
        $storeUrl = $settings["gm_store_url"] ?? config("app.url");
        $storeName = $settings["gm_store_name"] ?? config("app.name");
        $updatedAt = now()->format("Y-m-d\TH:i:s\Z");

        $xml = new \SimpleXMLElement(
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
            "<rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\"></rss>"
        );

        $channel = $xml->addChild("channel");
        $channel->addChild("title", htmlspecialchars($storeName));
        $channel->addChild("link", $storeUrl);
        $channel->addChild("description", "Catálogo de produtos - " . htmlspecialchars($storeName));

        foreach ($products as $product) {
            $item = $channel->addChild("item");
            $item->addChild("g:id", (string) $product->id, "http://base.google.com/ns/1.0");
            $item->addChild("title", htmlspecialchars($product->name));
            $item->addChild("g:description", htmlspecialchars(strip_tags($product->short_description ?? $product->name)), "http://base.google.com/ns/1.0");
            $item->addChild("link", $storeUrl . "/produtos/" . $product->slug);

            if ($product->main_image_url) {
                $item->addChild("g:image_link", $product->main_image_url, "http://base.google.com/ns/1.0");
            }

            $condition = $product->gm_condition ?? "new";
            $item->addChild("g:condition", $condition, "http://base.google.com/ns/1.0");

            $availability = $product->availability === "available" ? "in stock" : "out of stock";
            $item->addChild("g:availability", $availability, "http://base.google.com/ns/1.0");

            $price = $product->gm_price ?? $product->price_per_day ?? "0";
            $currency = $product->gm_currency ?? "BRL";
            $item->addChild("g:price", number_format((float) $price, 2, ".", "") . " " . $currency, "http://base.google.com/ns/1.0");

            if ($product->gm_brand) {
                $item->addChild("g:brand", htmlspecialchars($product->gm_brand), "http://base.google.com/ns/1.0");
            }
            if ($product->gm_gtin) {
                $item->addChild("g:gtin", $product->gm_gtin, "http://base.google.com/ns/1.0");
            }
            if ($product->gm_mpn) {
                $item->addChild("g:mpn", $product->gm_mpn, "http://base.google.com/ns/1.0");
            }
            if ($product->gm_google_product_category) {
                $item->addChild("g:google_product_category", htmlspecialchars($product->gm_google_product_category), "http://base.google.com/ns/1.0");
            }
            if ($product->category) {
                $item->addChild("g:product_type", htmlspecialchars($product->category->name), "http://base.google.com/ns/1.0");
            }
        }

        return response($xml->asXML(), 200, [
            "Content-Type"  => "application/xml; charset=utf-8",
            "Cache-Control" => "no-cache, must-revalidate",
        ]);
    }
}
