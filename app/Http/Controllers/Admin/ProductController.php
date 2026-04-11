<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        return Inertia::render("Admin/Products/Index", [
            "products" => Product::with(["category", "images"])
                ->orderBy("order")
                ->get()
                ->map(fn($p) => [
                    "id"           => $p->id,
                    "name"         => $p->name,
                    "slug"         => $p->slug,
                    "availability" => $p->availability,
                    "is_active"    => $p->is_active,
                    "category"     => $p->category?->name,
                    "main_image"   => $p->main_image_url,
                    "order"        => $p->order,
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("Admin/Products/Form", [
            "categories" => Category::where("is_active", true)->orderBy("name")->get(["id", "name"]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            "name"                    => "required|string|max:255",
            "category_id"             => "required|exists:categories,id",
            "short_description"       => "nullable|string|max:500",
            "description"             => "nullable|string",
            "availability"            => "required|in:available,rented,maintenance",
            "price_per_day"           => "nullable|numeric|min:0",
            "is_active"               => "boolean",
            "order"                   => "nullable|integer",
            "meta_title"              => "nullable|string|max:255",
            "meta_description"        => "nullable|string|max:500",
            "meta_keywords"           => "nullable|string|max:500",
            "gm_brand"                => "nullable|string|max:255",
            "gm_gtin"                 => "nullable|string|max:50",
            "gm_mpn"                  => "nullable|string|max:50",
            "gm_condition"            => "nullable|string",
            "gm_google_product_category" => "nullable|string|max:500",
            "gm_price"                => "nullable|numeric|min:0",
            "gm_currency"             => "nullable|string|max:3",
            "images"                  => "nullable|array",
            "images.*"                => "image|max:5120",
        ]);

        $data["slug"] = Str::slug($data["name"]);

        $images = $data["images"] ?? [];
        unset($data["images"]);

        $product = Product::create($data);

        foreach ($images as $index => $imageFile) {
            $path = $imageFile->store("products", "public");
            ProductImage::create([
                "product_id" => $product->id,
                "path"       => $path,
                "order"      => $index,
                "is_primary" => $index === 0,
            ]);
        }

        return redirect()->route("admin.products.index")->with("success", "Produto criado com sucesso!");
    }

    public function edit(Product $product): Response
    {
        return Inertia::render("Admin/Products/Form", [
            "product"    => $product->load("images"),
            "categories" => Category::where("is_active", true)->orderBy("name")->get(["id", "name"]),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            "name"                    => "required|string|max:255",
            "category_id"             => "required|exists:categories,id",
            "short_description"       => "nullable|string|max:500",
            "description"             => "nullable|string",
            "availability"            => "required|in:available,rented,maintenance",
            "price_per_day"           => "nullable|numeric|min:0",
            "is_active"               => "boolean",
            "order"                   => "nullable|integer",
            "meta_title"              => "nullable|string|max:255",
            "meta_description"        => "nullable|string|max:500",
            "meta_keywords"           => "nullable|string|max:500",
            "gm_brand"                => "nullable|string|max:255",
            "gm_gtin"                 => "nullable|string|max:50",
            "gm_mpn"                  => "nullable|string|max:50",
            "gm_condition"            => "nullable|string",
            "gm_google_product_category" => "nullable|string|max:500",
            "gm_price"                => "nullable|numeric|min:0",
            "gm_currency"             => "nullable|string|max:3",
            "images"                  => "nullable|array",
            "images.*"                => "image|max:5120",
            "delete_image_ids"        => "nullable|array",
            "delete_image_ids.*"      => "integer",
        ]);

        $data["slug"] = Str::slug($data["name"]);

        $deleteIds  = $data["delete_image_ids"] ?? [];
        $newImages  = $data["images"] ?? [];
        unset($data["images"], $data["delete_image_ids"]);

        if (!empty($deleteIds)) {
            $toDelete = ProductImage::whereIn("id", $deleteIds)->where("product_id", $product->id)->get();
            foreach ($toDelete as $img) {
                Storage::disk("public")->delete($img->path);
                $img->delete();
            }
        }

        $product->update($data);

        $existingCount = $product->images()->count();
        foreach ($newImages as $index => $imageFile) {
            $path = $imageFile->store("products", "public");
            ProductImage::create([
                "product_id" => $product->id,
                "path"       => $path,
                "order"      => $existingCount + $index,
                "is_primary" => $existingCount === 0 && $index === 0,
            ]);
        }

        return redirect()->route("admin.products.index")->with("success", "Produto atualizado com sucesso!");
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk("public")->delete($image->path);
        }
        $product->delete();
        return redirect()->route("admin.products.index")->with("success", "Produto removido.");
    }

    public function setPrimaryImage(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $imageId = $request->validate(["image_id" => "required|integer"])["image_id"];
        $product->images()->update(["is_primary" => false]);
        $product->images()->where("id", $imageId)->update(["is_primary" => true]);
        return response()->json(["ok" => true]);
    }
}