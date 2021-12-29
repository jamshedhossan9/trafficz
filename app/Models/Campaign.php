<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    public function campaignGroup()
    {
        return $this->belongsTo(CampaignGroup::class);
    }

    public function trackerAuth()
    {
        return $this->belongsTo(TrackerAuth::class);
    }

    public function campaignTags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'campaign_tags', 'campaign_id', 'tag_id');
    }
}
