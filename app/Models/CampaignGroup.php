<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CampaignGroup extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'campaign_group_users', 'campaign_group_id',  'user_id')->withPivot('id');
    }
}
