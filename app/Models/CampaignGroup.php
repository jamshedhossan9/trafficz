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

    public function credit()
    {
        return $this->hasOne(Credit::class)->where('date', date('Y-m-d'));
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function reports()
    {
        return $this->hasMany(CampaignGroupReport::class);
    }
    
    public function reportsByDate($date)
    {
        return $this->reports()->where('date', $date);
    }

    public function reportsByDateRange($dateFrom, $dateTo)
    {
        return $this->reports()->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo);
    }
}
