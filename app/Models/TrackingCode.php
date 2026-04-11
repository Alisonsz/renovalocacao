<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingCode extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'position', 'is_active', 'pages'];

    protected $casts = [
        'is_active' => 'boolean',
        'pages' => 'array',
    ];

    public function appliesToPage(string $page): bool
    {
        if (empty($this->pages)) {
            return true;
        }
        return in_array($page, $this->pages) || in_array('all', $this->pages);
    }
}
