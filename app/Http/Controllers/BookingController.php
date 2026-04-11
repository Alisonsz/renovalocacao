<?php

namespace App\Http\Controllers;

use App\Mail\BookingRequestMail;
use App\Models\Booking;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function create(string $slug): Response
    {
        $product = Product::with(["images"])->where("slug", $slug)->where("is_active", true)->firstOrFail();
        $settings = SiteSetting::allKeyed();

        return Inertia::render("Booking/Checkout", [
            "product" => [
                "id"             => $product->id,
                "name"           => $product->name,
                "slug"           => $product->slug,
                "main_image_url" => $product->main_image_url,
                "availability"   => $product->availability,
                "price_per_day"  => $product->price_per_day,
            ],
            "settings" => $settings,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "product_id"       => "required|exists:products,id",
            "customer_name"    => "required|string|max:255",
            "customer_email"   => "required|email|max:255",
            "customer_phone"   => "required|string|max:50",
            "customer_company" => "nullable|string|max:255",
            "customer_city"    => "nullable|string|max:255",
            "start_date"       => "required|date|after_or_equal:today",
            "end_date"         => "nullable|date|after_or_equal:start_date",
            "message"          => "nullable|string|max:1000",
        ]);

        $booking = Booking::create($validated);

        $notificationEmail = SiteSetting::get("booking_notification_email");

        if ($notificationEmail) {
            Mail::to($notificationEmail)->send(new BookingRequestMail($booking));
            $booking->update(["notified_at" => now()]);
        }

        return redirect()->route("booking.success")->with("booking_id", $booking->id);
    }

    public function success(): Response
    {
        return Inertia::render("Booking/Success");
    }
}
