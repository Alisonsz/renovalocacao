<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(): Response
    {
        $bookings = Booking::with("product")
            ->latest()
            ->get()
            ->map(fn($b) => [
                "id"             => $b->id,
                "customer_name"  => $b->customer_name,
                "customer_email" => $b->customer_email,
                "customer_phone" => $b->customer_phone,
                "customer_city"  => $b->customer_city,
                "product_name"   => $b->product?->name,
                "product_id"     => $b->product_id,
                "start_date"     => $b->start_date?->format("d/m/Y"),
                "end_date"       => $b->end_date?->format("d/m/Y"),
                "message"        => $b->message,
                "status"         => $b->status,
                "status_label"   => $b->status_label,
                "admin_notes"    => $b->admin_notes,
                "created_at"     => $b->created_at->format("d/m/Y H:i"),
            ]);

        return Inertia::render("Admin/Bookings/Index", ["bookings" => $bookings]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            "status"      => "required|in:pending,contacted,confirmed,cancelled",
            "admin_notes" => "nullable|string|max:1000",
        ]);

        $booking->update($data);

        return redirect()->route("admin.bookings.index")->with("success", "Status atualizado!");
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();
        return redirect()->route("admin.bookings.index")->with("success", "Reserva removida.");
    }
}