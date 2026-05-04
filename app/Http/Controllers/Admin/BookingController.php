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
        $bookings = Booking::with('product')
            ->latest()
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'customer_name' => $b->customer_name,
                'customer_email' => $b->customer_email,
                'customer_phone' => $b->customer_phone,
                'customer_zip_code' => $b->customer_zip_code,
                'customer_city' => $b->customer_city,
                'customer_street' => $b->customer_street,
                'customer_number' => $b->customer_number,
                'customer_reference' => $b->customer_reference,
                'product_name' => $b->product?->name,
                'product_id' => $b->product_id,
                'start_date' => $b->start_date?->format('d/m/Y'),
                'end_date' => $b->end_date?->format('d/m/Y'),
                'message' => $b->message,
                'status' => $b->status,
                'status_label' => $b->status_label,
                'admin_notes' => $b->admin_notes,
                'created_at' => $b->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Admin/Bookings/Index', ['bookings' => $bookings]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,contacted,confirmed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($data['status'] === 'confirmed') {
            $startDate = $booking->start_date?->toDateString();
            $endDate = ($booking->end_date ?? $booking->start_date)?->toDateString();

            $hasConflict = Booking::query()
                ->where('id', '!=', $booking->id)
                ->where('product_id', $booking->product_id)
                ->where('status', 'confirmed')
                ->whereDate('start_date', '<=', $endDate)
                ->whereRaw('COALESCE(end_date, start_date) >= ?', [$startDate])
                ->exists();

            if ($hasConflict) {
                return back()->withErrors([
                    'status' => 'Conflito de agenda: já existe uma reserva confirmada para esse período.',
                ]);
            }
        }

        $booking->update($data);

        return redirect()->route('admin.bookings.index')->with('success', 'Status atualizado!');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Reserva removida.');
    }
}
