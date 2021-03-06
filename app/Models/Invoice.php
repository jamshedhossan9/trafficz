<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $casts = [
        'splits' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
