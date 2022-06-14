<?php

namespace App\Services;

use App\Jobs\CampaignGroupStatJob;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\StoreAllCampaignStatsXDaysJob;
use App\Jobs\GenerateYesterdayStatsAndInvoiceJob;
use App\Jobs\CampaignStatJob;
use App\Models\User;
use App\Models\Role;
use App\Models\Campaign;
use App\Models\CampaignGroup;
use App\Models\CampaignGroupReport;
use App\Models\CampaignGroupUser;
use App\Models\CampaignTag;
use App\Models\Invoice;
use App\Models\Credit;
use App\Models\MyLog;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;
use Carbon\Carbon;
use Exception;

class CampaignService
{
    public function __construct()
    {
        // $this->date = date('Y-m-d',strtotime("-1 days"));
    }

    public function getAllCampaignGroupStats($groupId)
    {
        return;
        $groupId = intval($groupId);
        $dateFrom = date('Y-m-d',strtotime("-1 days")); //$this->date;
        $dateTo = $dateFrom; //$this->date;

        $campaignGroup = CampaignGroup::with('campaigns.trackerAuth.trackerUser.tracker')->find($groupId);
        // dd($campaignGroupUsers);
        // echo json_encode($campaignGroupUsers);
        // exit();
        $log = [
            'campaigns' => []
        ];
        if(!empty($campaignGroup)){
            foreach($campaignGroup->campaigns as $campaign){
                $log['campaigns'][$campaign->id][$dateFrom] = ['existing' => null, 'new' => null];
                $checkExists = $campaign->reportByDate($dateFrom)->first();
                if(empty($checkExists) && $campaign->pull){
                    $tracker = $campaign->trackerAuth->trackerUser->tracker->slug;
                    $auth = $campaign->trackerAuth->auth;
                    
                    $stats = getTrackerCampaignStat($tracker, $auth, $dateFrom, $dateTo, $campaign->camp_id);
                    $report = new CampaignGroupReport();
                    $report->campaign_group_id = $groupId;
                    $report->campaign_id = $campaign->id;
                    $report->date = $dateFrom;
                    $report->conversions = $stats['conversions'];
                    $report->cost = $stats['cost'];
                    $report->revenue = $stats['revenue'];
                    $report->profit = $stats['profit'];
                    $report->impressions = $stats['impressions'];
                    $report->visits = $stats['visits'];
                    $report->clicks = $stats['clicks'];
                    $report->epc = $stats['epc'];
                    $report->cpc = $stats['cpc'];
                    $report->epv = $stats['epv'];
                    $report->cpv = $stats['cpv'];
                    $report->roi = $stats['roi'];
                    $report->ctr = $stats['ctr'];
                    $report->ictr = $stats['ictr'];
                    $report->cr = $stats['cr'];

                    $report->save();

                    $log['campaigns'][$campaign->id][$dateFrom]['new'] = $stats;
                }
                else{
                    $log['campaigns'][$campaign->id][$dateFrom]['existing'] = $checkExists;
                }
            }
        }

    }

    public function getAllCampaignStats()
    {
        return;
        $groups = CampaignGroup::all();
        $campaignGroupStatJobs = [];
        if(!empty($groups)){
            foreach($groups as $group){
                // $this->getAllCampaignGroupStats($group->id);
                // dispatch job for group stat
                // CampaignGroupStatJob::dispatch($group->id);
                $campaignGroupStatJobs[] = new CampaignGroupStatJob($group->id);
            }


            // $this->generateInvoice();
            //dispatch all jobs in the batch
            $this->dipatchBatchJobs($campaignGroupStatJobs);
            
        }
        return;
    }

    public function generateInvoice()
    {
        return;
        $days = array('sunday', 'monday', 'tuesday', 'wednesday','thursday','friday', 'saturday');
		$currentDate = date('Y-m-d');
		$dayNo = date('w', strtotime($currentDate));
		$dayWeek = $days[$dayNo];
		$dateFrom = null;
		$dateTo = null;
		if($dayWeek == 'monday'){ // thursday - sunday
			$dateFrom = date('Y-m-d', strtotime('-4 days', strtotime($currentDate)));
			$dateTo = date('Y-m-d', strtotime('-1 days', strtotime($currentDate)));
		}
		else if($dayWeek == 'thursday'){ // monday - wednesday
			$dateFrom = date('Y-m-d', strtotime('-3 days', strtotime($currentDate)));
			$dateTo = date('Y-m-d', strtotime('-1 days', strtotime($currentDate)));
		}
		else{
			return;
		}
        

        // $dateFrom = '2022-01-03';
        // $dateTo = '2022-01-05';

        $roleUsers = Role::find(3);
        $users = $roleUsers->users()->get();
        $data = [];
        $splitByTrackers = [];
        
        if(!empty($users)){
            foreach($users as $user){
                // $campaignGroupUsers = CampaignGroupUser::with(['campaignGroup.campaigns.reports' => function ($query) use ($dateFrom, $dateTo) {
                //     $query->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo);
                // }, 'campaignGroup.credits' => function($query) use($dateFrom, $dateTo){
                //     $query->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo);
                // }])->where('user_id', $user->id)->get();
                // $data[$user->id] = $campaignGroupUsers;

                $checkExists = Invoice::where('user_id', $user->id)->where('end_date', $dateTo)->first();
                if(empty($checkExists)){
                    $invoiceData = ['amount' => 0, 'credit' => 0];
                    $campaignGroupUsers = CampaignGroupUser::with(['campaignGroup'])->where('user_id', $user->id)->get();
                    $allCreditIds = [];
                    if(!empty($campaignGroupUsers)){
                        foreach($campaignGroupUsers as $campaignGroupUser){
                            $campaigns = $campaignGroupUser->campaignGroup->campaigns()->with('trackerAuth')->get();
                            $credits = $campaignGroupUser->campaignGroup->credits()->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->get();
                            if(!empty($credits)){
                                foreach($credits as $credit){
                                    $invoiceData['credit'] += $credit->amount;
                                    $allCreditIds[] = $credit->id;
                                }
                            }
                            if(!empty($campaigns)){
                                foreach($campaigns as $campaign){
                                    $reports = $campaign->reports()->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->get();
                                    if(!empty($reports)){
                                        if(empty($splitByTrackers[$campaign->tracker_auth_id])){
                                            $splitByTrackers[$campaign->tracker_auth_id] = [
                                                'name' => $campaign->trackerAuth->name,
                                                'amount' => 0,
                                            ];
                                        }
                                        foreach($reports as $report){
                                            $invoiceData['amount'] += $report->cost;
                                            $splitByTrackers[$campaign->tracker_auth_id]['amount'] += $report->cost;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $invoice = new Invoice();
                    $invoice->user_id = $user->id;
                    $invoice->start_date = $dateFrom;
                    $invoice->end_date = $dateTo;
                    $invoice->description = 'Traffic revenue';
                    $invoice->amount = $invoiceData['amount'];
                    $invoice->credit = $invoiceData['credit'];
                    $invoice->total = $invoiceData['credit'] + $invoiceData['amount'];
                    $invoice->splits = $splitByTrackers;
                    $invoice->save();
                    if(!empty($allCreditIds)){
                        Credit::whereIn('id', $allCreditIds)->update(['used' => true]);
                    }
                }
            }
        }
        // $data = json_decode(json_encode($data), true);
        // dd($data);
    }

    public function generateInvoices()
    {
        sleep(10);
        $days = array('sunday', 'monday', 'tuesday', 'wednesday','thursday','friday', 'saturday');
		$currentDate = date('Y-m-d');
		$dayNo = date('w', strtotime($currentDate));
		$dayWeek = $days[$dayNo];
		$dateFrom = null;
		$dateTo = null;
		if($dayWeek == 'monday'){ // thursday - sunday //monday
			$dateFrom = date('Y-m-d', strtotime('-4 days', strtotime($currentDate)));
			$dateTo = date('Y-m-d', strtotime('-1 days', strtotime($currentDate)));
		}
		else if($dayWeek == 'thursday'){ // monday - wednesday
			$dateFrom = date('Y-m-d', strtotime('-3 days', strtotime($currentDate)));
			$dateTo = date('Y-m-d', strtotime('-1 days', strtotime($currentDate)));
		}
		else{
            $this->updateStatsPullingLock(false);
			exit();
		}

        $this->makeInvoices($dateFrom, $dateTo);
    }

    public function updateStatsPullingLock($set = true){
        $output = false;
        $user = User::find(1);
        if(!empty($user)){
            if($set){
                if($user->stats_pull_running){
                    $output = false;
                }
                else{
                    $user->stats_pull_running = true;
                    $user->save();
                    $output = true;
                }
            }
            else{
                if($user->stats_pull_running){
                    $user->stats_pull_running = false;
                    $user->save();
                }
                $output = true;
            }
        }
        return $output;
    }

    public function makeInvoices($dateFrom, $dateTo)
    {
        $roleUsers = Role::find(3);
        $users = $roleUsers->users()->get();
        $data = [];
        $splitByTrackers = [];
        if(!empty($users)){
            foreach($users as $user){
                $this->makeInvoice($user->id, $dateFrom, $dateTo);
            }
        }
    }

    public function makeInvoice($userId, $dateFrom, $dateTo)
    {
        try{
            $splitByTrackers = [];
            $user = User::find($userId);
            $dateFrom = date('Y-m-d', strtotime($dateFrom));
            $dateTo = date('Y-m-d', strtotime($dateTo));
            if(!empty($user)){
                $invoiceData = ['amount' => 0, 'credit' => 0];
                $campaignGroupUsers = CampaignGroupUser::with(['campaignGroup'])->where('user_id', $user->id)->get();
                $allCreditIds = [];
                if(!empty($campaignGroupUsers)){
                    foreach($campaignGroupUsers as $campaignGroupUser){
                        $campaigns = $campaignGroupUser->campaignGroup->campaigns()->with('trackerAuth')->get();
                        $credits = $campaignGroupUser->campaignGroup->credits()->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->get();
                        if(!empty($credits)){
                            foreach($credits as $credit){
                                $invoiceData['credit'] += $credit->amount;
                                $allCreditIds[] = $credit->id;
                            }
                        }
                        if(!empty($campaigns)){
                            foreach($campaigns as $campaign){
                                $reports = $campaign->reports()->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->get();
                                if(!empty($reports)){
                                    if(empty($splitByTrackers[$campaign->tracker_auth_id])){
                                        $splitByTrackers[$campaign->tracker_auth_id] = [
                                            'name' => $campaign->trackerAuth->name,
                                            'amount' => 0,
                                        ];
                                    }
                                    foreach($reports as $report){
                                        $invoiceData['amount'] += $report->cost;
                                        $splitByTrackers[$campaign->tracker_auth_id]['amount'] += $report->cost;
                                    }
                                }
                            }
                        }
                    }
                }
                $invoice = Invoice::where('user_id', $user->id)->where('end_date', $dateTo)->first();
                if(empty($invoice)){
                    $invoice = new Invoice();
                    $invoice->user_id = $user->id;
                }
                $invoice->start_date = $dateFrom;
                $invoice->end_date = $dateTo;
                $invoice->description = 'Traffic revenue';
                $invoice->amount = $invoiceData['amount'];
                $invoice->credit = $invoiceData['credit'];
                $invoice->total = $invoiceData['credit'] + $invoiceData['amount'];
                $invoice->splits = $splitByTrackers;
                $invoice->save();
                if(!empty($allCreditIds)){
                    Credit::whereIn('id', $allCreditIds)->update(['used' => true]);
                }
            }
            $this->updateStatsPullingLock(false);
        }
        catch(Exception $e){
            $this->updateStatsPullingLock(false);
        }
    }

    public function storeAllCampaignStatsYerterday()
    {
        $date = date('Y-m-d',strtotime("-1 days"));
        $this->storeAllCampaignStats($date);
    }

    public function storeAllCampaignStats($date)
    {
        $campaignGroupStatJobs = [];
        $campaigns = Campaign::all();
        if(!empty($campaigns)){
            for($i = 0; $i < 3; $i++){
                foreach($campaigns as $campaign){
                    // $this->storeCampaignStats($campaign->id, $date);
                    $campaignGroupStatJobs[] = new CampaignStatJob($campaign->id, $date);
                }
            }
            $campaignGroupStatJobs[] = new GenerateInvoiceJob();
            $this->dipatchBatchJobs($campaignGroupStatJobs);
        }
        
    }

    public function storeCampaignStatsForce($campaignId, $date, $save = true)
    {
        
        $campaignId = intval($campaignId);
        $date = date('Y-m-d', strtotime($date));
        $dateFrom = $date;
        $dateTo = $date;
        $campaign = Campaign::with('trackerAuth.trackerUser.tracker')->find($campaignId);
        if(!empty($campaign)){
            $identifier = $dateFrom.'_'.$campaign->campaign_group_id.'_'.$campaign->id;

            $tracker = $campaign->trackerAuth->trackerUser->tracker->slug;
            $auth = $campaign->trackerAuth->auth;
            
            $stats = getTrackerCampaignStat($tracker, $auth, $dateFrom, $dateTo, $campaign->camp_id);
            if(!$save){
                return $stats;
            }
            try {
        
                $report = new CampaignGroupReport();
                $report->campaign_group_id = $campaign->campaign_group_id;
                $report->campaign_id = $campaign->id;
                $report->date = $dateFrom;
                $report->identifier = $identifier;
                $report->save();
                
            } catch (Exception $e) {
                if ($e->getCode() == 23000) {
                    $report = CampaignGroupReport::where('identifier', $identifier)->first();
                }
            }
            if(!empty($report)){
                $report->conversions = $stats['conversions'];
                $report->cost = $stats['cost'];
                $report->revenue = $stats['revenue'];
                $report->profit = $stats['profit'];
                $report->impressions = $stats['impressions'];
                $report->visits = $stats['visits'];
                $report->clicks = $stats['clicks'];
                $report->epc = $stats['epc'];
                $report->cpc = $stats['cpc'];
                $report->epv = $stats['epv'];
                $report->cpv = $stats['cpv'];
                $report->roi = $stats['roi'];
                $report->ctr = $stats['ctr'];
                $report->ictr = $stats['ictr'];
                $report->cr = $stats['cr'];
                
                $report->save();
            }
            
        }
    }

    public function storeCampaignStatsInstant($campaignId, $date, $save = true)
    {
        try{
            $campaignId = intval($campaignId);
            $date = date('Y-m-d', strtotime($date));
            $dateFrom = $date;
            $dateTo = $date;
            $campaign = Campaign::with('trackerAuth.trackerUser.tracker')->find($campaignId);
            if(!empty($campaign)){
                $identifier = $dateFrom.'_'.$campaign->campaign_group_id.'_'.$campaign->id;

                $tracker = $campaign->trackerAuth->trackerUser->tracker->slug;
                $auth = $campaign->trackerAuth->auth;
                
                $stats = getTrackerCampaignStat($tracker, $auth, $dateFrom, $dateTo, $campaign->camp_id);
                if(!$save){
                    return $stats;
                }
                try {
            
                    $report = new CampaignGroupReport();
                    $report->campaign_group_id = $campaign->campaign_group_id;
                    $report->campaign_id = $campaign->id;
                    $report->date = $dateFrom;
                    $report->identifier = $identifier;
                    $report->save();
                    
                } catch (Exception $e) {
                    if ($e->getCode() == 23000) {
                        $report = CampaignGroupReport::where('identifier', $identifier)->first();
                    }
                }
                if(!empty($report)){
                    $report->conversions = $stats['conversions'];
                    $report->cost = $stats['cost'];
                    $report->revenue = $stats['revenue'];
                    $report->profit = $stats['profit'];
                    $report->impressions = $stats['impressions'];
                    $report->visits = $stats['visits'];
                    $report->clicks = $stats['clicks'];
                    $report->epc = $stats['epc'];
                    $report->cpc = $stats['cpc'];
                    $report->epv = $stats['epv'];
                    $report->cpv = $stats['cpv'];
                    $report->roi = $stats['roi'];
                    $report->ctr = $stats['ctr'];
                    $report->ictr = $stats['ictr'];
                    $report->cr = $stats['cr'];

                    if($stats['visits'] > 0){
                        $report->save();
                    }
                }
            }
        }
        catch(Exception $e){

        }
    }

    public function storeCampaignStats($campaignId, $date, $save = true)
    {
        try{
            $pull = false;
            $campaignId = intval($campaignId);
            $date = date('Y-m-d', strtotime($date));
            $dateFrom = $date;
            $campaign = Campaign::with('trackerAuth.trackerUser.tracker')->find($campaignId);
            if(!empty($campaign) && $campaign->pull){
                $pull = true;
            }
            if($pull){
                sleep(10);
                $this->storeCampaignStatsInstant($campaignId, $date, $save);
            }
        }
        catch(Exception $e){
        }
    }

    public function generateYesterdayStatsAndInvoice()
    {
        $this->storeAllCampaignStatsYerterday();
        return;
    }

    public function generateYesterdayStatsAndInvoiceRun()
    {
        usleep(rand(0, 3000000));
        $lock = $this->updateStatsPullingLock(true);
        if($lock){
            $this->generateYesterdayStatsAndInvoice();
        }
        // GenerateYesterdayStatsAndInvoiceJob::dispatch();
    }

    public function storeAllCampaignStatsXDays($day)
    {
        $day = intval($day);
        $start = Carbon::now();
        if($day){
            $pullDate = date('Y-m-d', strtotime('-1 days'));
            for($i = 0; $i < $day; $i++){
                $campaigns = Campaign::all();
                if(!empty($campaigns)){
                    foreach($campaigns as $campaign){
                        // $this->storeCampaignStats($campaign->id, $pullDate);
                        echo $start->format('Y-m-d H:i:s');
                        echo '<br>';
                        CampaignStatJob::dispatch($campaign->id, $pullDate);//->delay($start->addSeconds(20));
                    }
                }
                $pullDate = date('Y-m-d', strtotime('-1 days', strtotime($pullDate)));
            }
        }
        return;
    }

    public function storeAllCampaignStatsXDaysRun($day)
    {
        $this->storeAllCampaignStatsXDays($day);
        // StoreAllCampaignStatsXDaysJob::dispatch($day);
    }

    public function everyMinCron()
    {
        // $data = new MyLog();
        // $data->type = "every min cron check foreign";
        // $data->data = [
        //     'date' => date("Y-m-d H:i:s"),
        //     'source' => 'foreign hit'
        // ];
        // $data->save();

        $time = date('H:i');
        if($time == '01:00'){
            $data = new MyLog();
            $data->type = "daily 01:00am cron check foreign";
            $data->data = [
                'date' => date("Y-m-d H:i:s"),
                'source' => 'foreign hit'
            ];
            $data->save();
            $this->generateYesterdayStatsAndInvoiceRun();
        }
    }

    public function getCampaignStats($campaignId, $dateFrom, $dateTo)
    {
        $reports = [];
        $dateFrom = date('Y-m-d', strtotime($dateFrom));
        $dateTo = date('Y-m-d', strtotime($dateTo));
        $pullDate = $dateFrom;
        while(true){
            $reports[] = $this->storeCampaignStats($campaignId, $pullDate, false);
            if($pullDate == $dateTo) break;
            $pullDate = date('Y-m-d', strtotime('+1 days', strtotime($pullDate)));
        }
        $total = trackerCampaignStatSum($reports);
        dd($reports, $total);
    }

    /**
     * dipatchBatchJobs
     * dispatching all jobs in a batch using Bus
     */
    public function dipatchBatchJobs($jobs = []){
        if(!empty($jobs)){
            Bus::chain($jobs)->dispatch();
            // $batch = Bus::batch($jobs)->then( function (Batch $bat){
            //     // All jobs completed successfully...
            //     GenerateInvoiceJob::dispatch();
            // })->catch(function (Batch $batch, Throwable $e) {
            //     // First batch job failure detected...
            //     \Log::info("Batch failed: ". json_encode($batch));
            // })->finally(function (Batch $batch) {
            //     // The batch has finished executing...
            // })->name('Campaign Group Stat')->dispatch();

            // return $batch->id;
        }

        return;
    }
}