<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignGroupUser extends Model
{
    use HasFactory;

    
    public function campaignGroup()
    {
        return $this->belongsTo(CampaignGroup::class);
    }
}
