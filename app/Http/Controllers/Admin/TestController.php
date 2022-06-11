<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\CampaignGroupReport;
use Exception;

class TestController extends Controller
{
    public function unniqueTest(){
        $date = date('Y-m-12');
        $campaign = Campaign::find(1);
        $identifier = $date.'_'.$campaign->campaign_group_id.'_'.$campaign->id;
        try {
        
            $report = new CampaignGroupReport();
            $report->campaign_group_id = 1;
            $report->campaign_id = 1;
            $report->date = $date;
            $report->identifier = $identifier;
            $report->save();
            
        } catch (Exception $e) { // It's actually a QueryException but this works too
            if ($e->getCode() == 23000) {
                $report = CampaignGroupReport::where('identifier', $identifier)->first();
                // dd($report);
            }
        }
        if(!empty($report)){
            $report->conversions = 0;
            $report->cost = 14;
            $report->revenue = 0;
            $report->profit = 0;
            $report->impressions = 0;
            $report->visits = 0;
            $report->clicks = 0;
            $report->epc = 0;
            $report->cpc = 0;
            $report->epv = 0;
            $report->cpv = 0;
            $report->roi = 0;
            $report->ctr = 0;
            $report->ictr = 0;
            $report->cr = 0;

            $report->save();
        }
    }
}
