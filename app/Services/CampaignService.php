<?php

namespace App\Services;

use App\Jobs\CampaignGroupStatJob;
use App\Jobs\GenerateInvoiceJob;
use App\Models\User;
use App\Models\Role;
use App\Models\CampaignGroup;
use App\Models\CampaignGroupReport;
use App\Models\CampaignGroupUser;
use App\Models\Invoice;
use App\Models\Credit;

class CampaignService
{
    public function __construct()
    {
        // $this->date = date('Y-m-d',strtotime("-1 days"));
    }

    public function getAllCampaignGroupStats($groupId)
    {
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
                if(empty($checkExists)){
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
        $groups = CampaignGroup::all();
        if(!empty($groups)){
            foreach($groups as $group){
                // $this->getAllCampaignGroupStats($group->id);
                // dispatch job for group stat
                CampaignGroupStatJob::dispatch($group->id);
            }
        }
        // $this->generateInvoice();
        GenerateInvoiceJob::dispatch();
    }

    public function generateInvoice()
    {
        
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
			exit();
		}
        

        // $dateFrom = '2022-01-03';
        // $dateTo = '2022-01-05';

        $roleUsers = Role::find(3);
        $users = $roleUsers->users()->get();
        $data = [];
        
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
                            $campaigns = $campaignGroupUser->campaignGroup->campaigns()->get();
                            $credits = $campaignGroupUser->campaignGroup->credits()->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->where('used', false)->get();
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
                                        foreach($reports as $report){
                                            $invoiceData['amount'] += $report->cost;
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

}