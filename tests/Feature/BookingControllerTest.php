<?php

namespace Tests\Feature;

use App\Mail\BookingRequestMail;
use App\Models\Booking;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_includes_confirmed_blocked_ranges(): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
            'slug' => 'maquina-ultra-hifu',
        ]);

        Booking::factory()->confirmed()->create([
            'product_id' => $product->id,
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-12',
        ]);

        Booking::factory()->pending()->create([
            'product_id' => $product->id,
            'start_date' => '2026-06-20',
            'end_date' => '2026-06-21',
        ]);

        $response = $this->get(route('booking.create', $product->slug));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Booking/Checkout')
            ->has('blockedRanges', 1)
            ->where('blockedRanges.0.start_date', '2026-06-10')
            ->where('blockedRanges.0.end_date', '2026-06-12')
        );
    }

    public function test_store_creates_pending_booking_sends_notification_and_redirects_with_reference(): void
    {
        Mail::fake();

        SiteSetting::query()->create([
            'key' => 'booking_notification_email',
            'value' => 'reservas@example.com',
            'type' => 'text',
            'group' => 'email',
            'label' => 'Booking notification email',
        ]);

        $product = Product::factory()->create(['is_active' => true]);

        $response = $this->post(route('booking.store'), [
            'product_id' => $product->id,
            'customer_name' => 'Maria Souza',
            'customer_email' => 'maria@example.com',
            'customer_phone' => '(11) 99999-0000',
            'customer_company' => 'Clinica Maria',
            'customer_zip_code' => '01310-100',
            'customer_city' => 'Sao Paulo',
            'customer_street' => 'Avenida Paulista',
            'customer_number' => '1578',
            'customer_reference' => 'Conjunto 401',
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(12)->toDateString(),
            'message' => 'Preciso para procedimento de facial.',
        ]);

        $response->assertRedirectToRoute('booking.success');
        $response->assertSessionHas('booking_reference');
        $this->assertDatabaseHas('bookings', [
            'product_id' => $product->id,
            'customer_email' => 'maria@example.com',
            'customer_zip_code' => '01310-100',
            'customer_street' => 'Avenida Paulista',
            'customer_number' => '1578',
            'customer_reference' => 'Conjunto 401',
            'status' => 'pending',
        ]);

        $booking = Booking::query()->where('customer_email', 'maria@example.com')->firstOrFail();

        Mail::assertSent(BookingRequestMail::class, function (BookingRequestMail $mail) use ($booking): bool {
            return $mail->booking->is($booking);
        });

        $this->assertNotNull($booking->fresh()->notified_at);
    }

    public function test_store_rejects_period_when_there_is_confirmed_conflict(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        Booking::factory()->confirmed()->create([
            'product_id' => $product->id,
            'start_date' => now()->addDays(15)->toDateString(),
            'end_date' => now()->addDays(18)->toDateString(),
        ]);

        $response = $this->from(route('booking.create', $product->slug))->post(route('booking.store'), [
            'product_id' => $product->id,
            'customer_name' => 'Joao Silva',
            'customer_email' => 'joao@example.com',
            'customer_phone' => '(11) 98888-0000',
            'customer_company' => null,
            'customer_zip_code' => '13010-100',
            'customer_city' => 'Campinas',
            'customer_street' => 'Rua das Flores',
            'customer_number' => '250',
            'customer_reference' => 'Ao lado da farmacia',
            'start_date' => now()->addDays(16)->toDateString(),
            'end_date' => now()->addDays(19)->toDateString(),
            'message' => null,
        ]);

        $response->assertSessionHasErrors('start_date');
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_store_requires_complete_address_and_limits_number_length(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $response = $this->from(route('booking.create', $product->slug))->post(route('booking.store'), [
            'product_id' => $product->id,
            'customer_name' => 'Maria Souza',
            'customer_email' => 'maria@example.com',
            'customer_phone' => '(11) 99999-0000',
            'customer_company' => 'Clinica Maria',
            'customer_zip_code' => '1234',
            'customer_city' => '',
            'customer_street' => '',
            'customer_number' => '12345678901',
            'customer_reference' => '',
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(12)->toDateString(),
            'message' => 'Preciso para procedimento de facial.',
        ]);

        $response->assertSessionHasErrors([
            'customer_zip_code',
            'customer_city',
            'customer_street',
            'customer_number',
            'customer_reference',
        ]);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_booking_request_mail_renders_full_address_without_markdown_components(): void
    {
        $booking = Booking::factory()->create([
            'customer_zip_code' => '01310-100',
            'customer_city' => 'Sao Paulo',
            'customer_street' => 'Avenida Paulista',
            'customer_number' => '1578',
            'customer_reference' => 'Conjunto 401',
        ]);

        $html = (new BookingRequestMail($booking->load('product')))->render();

        $this->assertStringContainsString('Nova Solicitacao de Reserva', $html);
        $this->assertStringContainsString('01310-100', $html);
        $this->assertStringContainsString('Avenida Paulista', $html);
        $this->assertStringContainsString('Conjunto 401', $html);
    }

    public function test_success_page_receives_booking_reference_and_settings(): void
    {
        SiteSetting::query()->create([
            'key' => 'whatsapp_number',
            'value' => '5511999998888',
            'type' => 'text',
            'group' => 'contact',
            'label' => 'WhatsApp',
        ]);

        $response = $this->withSession(['booking_reference' => '1234'])->get(route('booking.success'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Booking/Success')
            ->where('bookingReference', '1234')
            ->where('settings.whatsapp_number', '5511999998888')
        );
    }
}
