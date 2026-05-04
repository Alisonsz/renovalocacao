<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        return $this->actingAs(User::factory()->create());
    }

    public function test_index_renders_bookings_page(): void
    {
        $booking = Booking::factory()->pending()->create();

        $response = $this->actingAsAdmin()->get(route('admin.bookings.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Bookings/Index')
            ->has('bookings', 1)
            ->where('bookings.0.customer_name', $booking->customer_name)
            ->where('bookings.0.status', 'pending')
            ->where('bookings.0.status_label', 'Pendente')
        );
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('admin.bookings.index'));

        $response->assertRedirectToRoute('login');
    }

    public function test_update_changes_status_and_notes(): void
    {
        $booking = Booking::factory()->pending()->create();

        $response = $this->actingAsAdmin()->put(route('admin.bookings.update', $booking), [
            'status' => 'confirmed',
            'admin_notes' => 'Cliente confirmado por telefone.',
        ]);

        $response->assertRedirectToRoute('admin.bookings.index');
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
            'admin_notes' => 'Cliente confirmado por telefone.',
        ]);
    }

    public function test_update_validates_status(): void
    {
        $booking = Booking::factory()->pending()->create();

        $response = $this->actingAsAdmin()->put(route('admin.bookings.update', $booking), [
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_update_blocks_confirmation_when_dates_conflict_with_other_confirmed_booking(): void
    {
        $confirmed = Booking::factory()->confirmed()->create([
            'start_date' => now()->addDays(20)->toDateString(),
            'end_date' => now()->addDays(22)->toDateString(),
        ]);

        $pending = Booking::factory()->pending()->create([
            'product_id' => $confirmed->product_id,
            'start_date' => now()->addDays(21)->toDateString(),
            'end_date' => now()->addDays(23)->toDateString(),
        ]);

        $response = $this->actingAsAdmin()
            ->from(route('admin.bookings.index'))
            ->put(route('admin.bookings.update', $pending), [
                'status' => 'confirmed',
                'admin_notes' => 'Tentativa de confirmar conflito',
            ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('bookings', [
            'id' => $pending->id,
            'status' => 'pending',
        ]);
    }

    public function test_destroy_deletes_booking(): void
    {
        $booking = Booking::factory()->create();

        $response = $this->actingAsAdmin()->delete(route('admin.bookings.destroy', $booking));

        $response->assertRedirectToRoute('admin.bookings.index');
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    public function test_status_label_accessor_returns_correct_labels(): void
    {
        $labels = [
            'pending' => 'Pendente',
            'contacted' => 'Contactado',
            'confirmed' => 'Confirmado',
            'cancelled' => 'Cancelado',
        ];

        foreach ($labels as $status => $expectedLabel) {
            $booking = Booking::factory()->create(['status' => $status]);
            $this->assertSame($expectedLabel, $booking->status_label);
        }
    }
}
