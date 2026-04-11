<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller
{
    public function index(): Response
    {
        return Inertia::render("Admin/Testimonials/Index", [
            "testimonials" => Testimonial::orderBy("order")->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("Admin/Testimonials/Form");
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            "customer_name"    => "required|string|max:255",
            "customer_company" => "nullable|string|max:255",
            "customer_city"    => "nullable|string|max:255",
            "testimonial"      => "required|string",
            "rating"           => "required|integer|min:1|max:5",
            "is_active"        => "boolean",
            "order"            => "nullable|integer",
        ]);

        if ($request->hasFile("customer_photo")) {
            $data["customer_photo"] = $request->file("customer_photo")->store("testimonials", "public");
        }

        Testimonial::create($data);

        return redirect()->route("admin.testimonials.index")->with("success", "Depoimento criado com sucesso!");
    }

    public function edit(Testimonial $testimonial): Response
    {
        return Inertia::render("Admin/Testimonials/Form", ["testimonial" => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $data = $request->validate([
            "customer_name"    => "required|string|max:255",
            "customer_company" => "nullable|string|max:255",
            "customer_city"    => "nullable|string|max:255",
            "testimonial"      => "required|string",
            "rating"           => "required|integer|min:1|max:5",
            "is_active"        => "boolean",
            "order"            => "nullable|integer",
        ]);

        if ($request->hasFile("customer_photo")) {
            $data["customer_photo"] = $request->file("customer_photo")->store("testimonials", "public");
        }

        $testimonial->update($data);

        return redirect()->route("admin.testimonials.index")->with("success", "Depoimento atualizado!");
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();
        return redirect()->route("admin.testimonials.index")->with("success", "Depoimento removido.");
    }
}