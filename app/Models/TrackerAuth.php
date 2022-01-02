<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackerAuth extends Model
{
    use HasFactory;

    protected $casts = [
        'auth' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trackerUser()
    {
        return $this->belongsTo(TrackerUser::class);
    }
}
