<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
