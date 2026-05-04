<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingRequestMail;
use App\Models\Booking;
use App\Models\Product;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function create(string $slug): Response
    {
        $product = Product::with(['images', 'category'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        $settings = SiteSetting::allKeyed();

        $blockedRanges = Booking::query()
            ->where('product_id', $product->id)
            ->where('status', 'confirmed')
            ->whereRaw('COALESCE(end_date, start_date) >= ?', [now()->toDateString()])
            ->orderBy('start_date')
            ->get(['start_date', 'end_date'])
            ->map(fn (Booking $booking) => [
                'start_date' => $booking->start_date?->toDateString(),
                'end_date' => ($booking->end_date ?? $booking->start_date)?->toDateString(),
            ])
            ->values();

        return Inertia::render('Booking/Checkout', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'short_description' => $product->short_description,
                'price_per_day' => $product->price_per_day,
                'category' => $product->category ? ['name' => $product->category->name] : null,
                'images' => $product->images->map(fn ($image) => [
                    'url' => $image->url,
                    'alt_text' => $image->alt_text,
                ])->values(),
            ],
            'settings' => $settings,
            'blockedRanges' => $blockedRanges,
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $startDate = Carbon::parse($validated['start_date'])->toDateString();
        $endDate = Carbon::parse($validated['end_date'])->toDateString();

        $hasConflict = Booking::query()
            ->where('product_id', (int) $validated['product_id'])
            ->where('status', 'confirmed')
            ->whereDate('start_date', '<=', $endDate)
            ->whereRaw('COALESCE(end_date, start_date) >= ?', [$startDate])
            ->exists();

        if ($hasConflict) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'Já existe uma reserva confirmada para este período. Escolha outra data.',
                ]);
        }

        $booking = Booking::create($validated);

        $notificationEmail = SiteSetting::get('booking_notification_email');

        if ($notificationEmail) {
            Mail::to($notificationEmail)->send(new BookingRequestMail($booking));
            $booking->update(['notified_at' => now()]);
        }

        return redirect()->route('booking.success')->with('booking_reference', (string) $booking->id);
    }

    public function success(): Response
    {
        return Inertia::render('Booking/Success', [
            'settings' => SiteSetting::allKeyed(),
            'bookingReference' => session('booking_reference'),
        ]);
    }
}
