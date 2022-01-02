<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns() : BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_tags', 'tag_id', 'campaign_id');
    }

    public function getId()
    {
        return $this->id;
    }
}
