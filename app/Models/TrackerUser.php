<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrackerUser extends Model
{
    use HasFactory;

    public function tracker()
    {
        return $this->hasOne(Tracker::class, 'id', 'tracker_id');
    }
}
