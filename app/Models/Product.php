<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'availability',
        'price_per_day',
        'is_active',
        'order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'gm_brand',
        'gm_gtin',
        'gm_mpn',
        'gm_condition',
        'gm_google_product_category',
        'gm_price',
        'gm_currency',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    protected function mainImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->images()?->where('is_primary', true)->first()?->url
                ?? $this->images()?->first()?->url,
        );
    }
}
