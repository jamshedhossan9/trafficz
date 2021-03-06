<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tracker extends Model
{
    use HasFactory;

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tracker_users', 'tracker_id',  'user_id');
    }
}
