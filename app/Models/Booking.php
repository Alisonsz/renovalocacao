<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_company',
        'customer_zip_code',
        'customer_city',
        'customer_street',
        'customer_number',
        'customer_reference',
        'start_date',
        'end_date',
        'message',
        'status',
        'admin_notes',
        'notified_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'notified_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                'pending' => 'Pendente',
                'contacted' => 'Contactado',
                'confirmed' => 'Confirmado',
                'cancelled' => 'Cancelado',
                default => $this->status,
            },
        );
    }
}
