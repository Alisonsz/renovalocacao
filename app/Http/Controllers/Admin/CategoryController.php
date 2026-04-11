<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render("Admin/Categories/Index", [
            "categories" => Category::withCount("products")->orderBy("order")->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("Admin/Categories/Form");
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            "name"        => "required|string|max:255",
            "description" => "nullable|string",
            "order"       => "nullable|integer",
            "is_active"   => "boolean",
        ]);

        $data["slug"] = Str::slug($data["name"]);

        if ($request->hasFile("image")) {
            $data["image"] = $request->file("image")->store("categories", "public");
        }

        Category::create($data);

        return redirect()->route("admin.categories.index")->with("success", "Categoria criada com sucesso!");
    }

    public function edit(Category $category): Response
    {
        return Inertia::render("Admin/Categories/Form", ["category" => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            "name"        => "required|string|max:255",
            "description" => "nullable|string",
            "order"       => "nullable|integer",
            "is_active"   => "boolean",
        ]);

        $data["slug"] = Str::slug($data["name"]);

        if ($request->hasFile("image")) {
            $data["image"] = $request->file("image")->store("categories", "public");
        }

        $category->update($data);

        return redirect()->route("admin.categories.index")->with("success", "Categoria atualizada com sucesso!");
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route("admin.categories.index")->with("success", "Categoria removida.");
    }
}
