<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CampaignGroup;
use App\Models\CampaignGroupReport;
use App\Models\CampaignGroupUser;
use App\Services\CampaignService;
use App\Models\Role;
use App\Models\Invoice;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->user;
        $this->campaignGroupUsers = CampaignGroupUser::where('user_id', $this->user->id)->with('campaignGroup.campaigns.tags')->orderBy('id', 'desc')->get();
        
        // dd($this->campaignGroupUsers);
        return view('user.dashboard', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAllCampaignGroupStats(Request $request)
    {
        $output = $this->ajaxRes();

        $groupId = 'all';
        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d');
        $tagIds = [];

        if($request->has('group')){
            $groupId = trim($request->group);
            if($groupId != 'all'){
                $groupId = intval($groupId);
            }
        }
        if($request->has('dateFrom') && $request->has('dateTo')){
            $dateFrom = $request->dateFrom;
            $dateFrom = date('Y-m-d', strtotime($dateFrom));
            $dateTo = $request->dateTo;
            $dateTo = date('Y-m-d', strtotime($dateTo));
        }
        if($request->has('tags')){
            $tags = $request->tags;
            if(empty($tags)) $tags = [];
            foreach($tags as $tag){
                $tag = intval($tag);
                if($tag){
                    $tagIds[] = $tag;
                }
            }
        }
        $query = CampaignGroupUser::where('user_id', $this->user->id);
        $relations = [];
        $relations[] = 'campaignGroup.campaigns.trackerAuth.trackerUser.tracker';
        $relations[] = 'campaignGroup.campaigns.tags';
        if($groupId != 'all'){
            $query->where('campaign_group_id', $groupId);
        }
        
        if(!empty($relations)){
            $query->with($relations);
        }
        // \Log::info(json_encode($query));
        $campaignGroupUsers = $query->get();
        // dd($campaignGroupUsers);
        // echo json_encode($campaignGroupUsers);
        // exit();
        $campaignGroupStats = [];
        $campaignGroupTotalStats = [];
        $allCredits = 0;
        foreach($campaignGroupUsers as $campaignGroupUser){
            $campaignGroup = $campaignGroupUser->campaignGroup;
            $group = [
                'id' => $campaignGroup->id, 
                'name' => $campaignGroup->name,
                'campaigns' => [],
                'total' => [],
                'credit' => 0,
            ];
            $apiResponses = [];
            $apiResponsesTotals = [];

            foreach($campaignGroup->campaigns as $campaign){
                $campaignTagIds = $campaign->tag_ids;
                if(!empty($tagIds)){
                    $intersect = array_intersect($tagIds, $campaignTagIds->toArray());
                    if(empty($intersect)){
                        continue;
                    }
                }
                $tracker = $campaign->trackerAuth->trackerUser->tracker->slug;
                $auth = $campaign->trackerAuth->auth;
                
                $stats = getTrackerCampaignStat($tracker, $auth, $dateFrom, $dateTo, $campaign->camp_id);
                $apiResponses[] = [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'tags' => $campaign->tags,
                    'stats' => [
                        'clicks' => $stats['visits'],
                        'revenue' => number_format($stats['cost'], 2),
                        'epc' => number_format($stats['cpv'], 2),
                    ]
                ];
                $apiResponsesTotals[] = $stats;
            }

            $credits = $campaignGroup->credits()->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->get();
            if(!empty($credits)){
                foreach($credits as $credit){
                    $group['credit'] += $credit->amount;
                    $allCredits += $credit->amount;
                }
            }
            
            $group['campaigns'] = $apiResponses;
            $total = trackerCampaignStatSum($apiResponsesTotals);
            $group['total'] = [
                'clicks' => $total['visits'],
                'revenue' => $total['cost'] + $group['credit'],
                'epc' => number_format($total['cpv'], 2),
            ];
            $group['total']['revenue'] = number_format($group['total']['revenue'], 2);

            $campaignGroupTotalStats[] = $total;
            $campaignGroupStats[$campaignGroup->id] = $group;
        }
        $allTotals = trackerCampaignStatSum($campaignGroupTotalStats);
        $campaignGroupTotalStats = [
            'clicks' => $allTotals['visits'],
            'revenue' => $allTotals['cost'] + $allCredits,
            'epc' => number_format($allTotals['cpv'], 2),
        ];

        $output->data['pending_amount'] = 0;
        if($groupId == 'all'){
            $output->data['pending_amount'] = $campaignGroupTotalStats['revenue'] + $this->getPendingInvoiceAmount($dateFrom);
        }

        $output->data['pending_amount'] = number_format($output->data['pending_amount'], 2);
        $campaignGroupTotalStats['revenue'] = number_format($campaignGroupTotalStats['revenue'], 2);

        $output->data['groupStats'] = $campaignGroupStats;
        $output->data['totals'] = $campaignGroupTotalStats;
        
        $output->status = true;

        return response()->json($output);
    }

    public function getCampaignHourlyStats(Request $request)
    {
        $output = $this->ajaxRes();

        $groupId = 'all';
        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d');
        $tagIds = [];

        $campaignId = intval($request->campaignId);
        if($request->has('dateFrom') && $request->has('dateTo')){
            $dateFrom = $request->dateFrom;
            $dateFrom = date('Y-m-d', strtotime($dateFrom));
            $dateTo = $request->dateTo;
            $dateTo = date('Y-m-d', strtotime($dateTo));
        }
        $campaign = Campaign::with('trackerAuth.trackerUser.tracker')->find($campaignId);
        $hourlyData = [];
        if(!empty($campaign)){
            $tracker = $campaign->trackerAuth->trackerUser->tracker->slug;
            $result = null;
            if($tracker == 'voluum'){
                $result = getVoluumCampaignStatByHour($campaign->trackerAuth->auth, $dateFrom, $dateTo, $campaign->camp_id);
                if(!empty($result) && !empty($result['rows'])){
                    foreach($result['rows'] as $item){
                        $temp = [
                            'name' => $item['hourOfDay'].':00 - '.($item['hourOfDay']+1).':00',
                            'clicks' => $item['visits'],
                            'revenue' => number_format($item['cost'], 2),
                            'epc' => number_format($item['cpv'], 2),
                        ];
                        $hourlyData[] = $temp;
                    }
                }
            }
            elseif($tracker == 'binom'){
                $result = getBinomCampaignStatByHour($campaign->trackerAuth->auth, $dateFrom, $dateTo, $campaign->camp_id);
                if(!empty($result)){
                    foreach($result as $item){
                        if($item['level'] == 1){
                            $visits = floatval($item['clicks']);
                            $cost = floatval($item['cost']);
                            $cpv = $visits == 0 ? 0 : $cost / $visits;
                            $temp = [
                                'name' => $item['name'],
                                'clicks' => $visits,
                                'revenue' => number_format($cost, 2),
                                'epc' => number_format($cpv, 2),
                            ];
                            $hourlyData[] = $temp;
                        }
                    }
                }
            }
            /*
             'clicks' => $stats['visits'],
                        'revenue' => $stats['cost'],
                        'epc' => $stats['cpv'],
             */
            if(!empty($hourlyData)){
                $output->status = true;
                $output->data['hourly_data'] = $hourlyData;
            }
            else{
                $output->msg->show = true;
                $output->msg->text = 'No Hourly data found';
            }
        }
        else{
            $output->msg->text = 'Campaign not found';
        }
        return response()->json($output);
    }

    public function getPendingInvoiceAmount($dateTo)
    {
        $amount = 0;

        $lastInvoice = Invoice::whereUserId($this->user->id)->orderBy('id', 'desc')->first();
        if(!empty($lastInvoice)){
            $dateFrom = date("Y-m-d", strtotime("+1 day", strtotime($lastInvoice->end_date)));
            $dateTo = date("Y-m-d", strtotime("-1 day", strtotime($dateTo)));
            if(strtotime($dateTo) >= strtotime($dateFrom)){
                $invoiceData = ['amount' => 0, 'credit' => 0];
                $campaignGroupUsers = CampaignGroupUser::with(['campaignGroup'])->where('user_id', $this->user->id)->get();
                $allCreditIds = [];
                if(!empty($campaignGroupUsers)){
                    foreach($campaignGroupUsers as $campaignGroupUser){
                        $campaigns = $campaignGroupUser->campaignGroup->campaigns()->get();
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
                                    foreach($reports as $report){
                                        $invoiceData['amount'] += $report->cost;
                                    }
                                }
                            }
                        }
                    }
                }
                $amount = $invoiceData['amount'] + $invoiceData['credit'];
            }
        }
        
        return $amount;
    }

    public function campaignService()
    {
        $campaignService = new CampaignService();
        // $campaignService->getAllCampaignGroupStats(7);
        // dd();
        // $report = Campaign::find(8);
        // dd($report->reportsByDateRange('2022-01-03', '2022-01-04')->get());

        // $roleUsers = Role::find(3);
        // $users = $roleUsers->users()->get();
        // dd($users);

        $campaignService->generateInvoice();
    }
}
