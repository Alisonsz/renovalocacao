<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Product;
use App\Models\Testimonial;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render("Admin/Dashboard", [
            "stats" => [
                "products"    => Product::count(),
                "categories"  => Category::count(),
                "bookings"    => Booking::count(),
                "pending"     => Booking::where("status", "pending")->count(),
                "testimonials"=> Testimonial::count(),
            ],
            "recentBookings" => Booking::with("product")->latest()->limit(10)->get()
                ->map(fn($b) => [
                    "id"            => $b->id,
                    "customer_name" => $b->customer_name,
                    "product_name"  => $b->product?->name,
                    "start_date"    => $b->start_date?->format("d/m/Y"),
                    "end_date"      => $b->end_date?->format("d/m/Y"),
                    "status"        => $b->status,
                    "status_label"  => $b->status_label,
                    "created_at"    => $b->created_at->format("d/m/Y H:i"),
                ]),
        ]);
    }
}
