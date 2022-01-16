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
use App\Models\User;
use App\Models\MyLog;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId = 0)
    {
        $this->isAdmin = false;
        if(isAdmin()){
            if($userId != 0){
                $user = User::whereId($userId)->whereParentId($this->user->id)->first();
                if(!empty($user)){
                    $this->user = $user;
                    $this->isAdmin = true;
                }
                else{
                    abort(403);    
                }
            }
            else{
                abort(403);
            }
        }
        $this->campaignGroupUsers = CampaignGroupUser::where('user_id', $this->user->id)->with('campaignGroup.campaigns.tags')->orderBy('id', 'desc')->get();
        $this->pendingInvoiceAmount = $this->getPendingInvoiceAmount(false);
        // dd($this->pendingInvoiceAmount);
        
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

    public function getAllCampaignGroupStats(Request $request, $userId = 0)
    {
        $output = $this->ajaxRes();
        $isAdmin = false;
        if(isAdmin()){
            if($userId != 0){
                $user = User::whereId($userId)->whereParentId($this->user->id)->first();
                if(!empty($user)){
                    $this->user = $user;
                    $isAdmin = true;
                }
                else{
                    abort(403);    
                }
            }
            else{
                abort(403);
            }
        }

        $groupId = 'all';
        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d');
        $todayDate = date('Y-m-d');
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
        $todayAmount = 0;
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
                
                $campaigntSats = $this->getTrackerCampaignStat($tracker, $auth, $dateFrom, $dateTo, $campaign->camp_id, $campaign->id);
                $stats = $campaigntSats['all'];
                $todayAmount += $campaigntSats['today']['cost'];
                if($isAdmin || $stats['visits'] > 0){
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
                }
                $apiResponsesTotals[] = $stats;
            }

            $credits = $campaignGroup->credits()->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->get();
            if(!empty($credits)){
                foreach($credits as $credit){
                    $group['credit'] += $credit->amount;
                    if($credit->date == $todayDate){
                        $todayAmount += $credit->amount;
                    }
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

        $campaignGroupTotalStats['revenueOrg'] = $campaignGroupTotalStats['revenue'];
        $campaignGroupTotalStats['revenue'] = number_format($campaignGroupTotalStats['revenue'], 2);
        $campaignGroupTotalStats['clicks'] = number_format($campaignGroupTotalStats['clicks']);

        $output->data['groupStats'] = $campaignGroupStats;
        $output->data['totals'] = $campaignGroupTotalStats;
        $output->data['dates'] = ['from' => $dateFrom, 'to' => $dateTo];
        if($groupId == 'all' && $dateTo == $todayDate){
            $output->data['today_amount'] = $todayAmount;
        }
        
        $output->status = true;

        return response()->json($output);
    }

    public function getCampaignHourlyStats(Request $request, $userId = 0)
    {
        $output = $this->ajaxRes();

        if(isAdmin()){
            if($userId != 0){
                $user = User::whereId($userId)->whereParentId($this->user->id)->first();
                if(!empty($user)){
                    $this->user = $user;
                }
                else{
                    abort(403);    
                }
            }
            else{
                abort(403);
            }
        }

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
            $output->msg->show = true;
            $output->msg->text = 'Campaign not found';
        }
        return response()->json($output);
    }
    
    public function getAllCampaignHourlyStats(Request $request, $userId = 0)
    {
        $output = $this->ajaxRes();

        if(isAdmin()){
            if($userId != 0){
                $user = User::whereId($userId)->whereParentId($this->user->id)->first();
                if(!empty($user)){
                    $this->user = $user;
                }
                else{
                    abort(403);    
                }
            }
            else{
                abort(403);
            }
        }

        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d');

        if($request->has('dateFrom') && $request->has('dateTo')){
            $dateFrom = $request->dateFrom;
            $dateFrom = date('Y-m-d', strtotime($dateFrom));
            $dateTo = $request->dateTo;
            $dateTo = date('Y-m-d', strtotime($dateTo));
        }
        $campaignGroupUsers = CampaignGroupUser::where('user_id', $this->user->id)->with('campaignGroup')->orderBy('id', 'desc')->get();
        $hourlyData = [];
        $tempHourlyData = [];
        for($i = 0; $i < 24; $i++){
            $tempHourlyData[$i] = [
                'clicks' => 0,
                'revenue' => 0,
                'epc' => 0,
            ];
        }
        if(!empty($campaignGroupUsers)){
            foreach($campaignGroupUsers as $campaignGroupUser){
                if(!empty($campaignGroupUser->campaignGroup)){
                    $campaigns = $campaignGroupUser->campaignGroup->campaigns()->with('trackerAuth.trackerUser.tracker')->get();
                    if(!empty($campaigns)){
                        foreach($campaigns as $campaign){
                            $tracker = $campaign->trackerAuth->trackerUser->tracker->slug;
                            $result = null;
                            if($tracker == 'voluum'){
                                $result = getVoluumCampaignStatByHour($campaign->trackerAuth->auth, $dateFrom, $dateTo, $campaign->camp_id);
                                if(!empty($result) && !empty($result['rows'])){
                                    foreach($result['rows'] as $item){
                                        $hour = intval($item['hourOfDay']);
                                        // $temp = [
                                        //     'clicks' => floatval($item['visits']),
                                        //     'revenue' => floatval($item['cost']),
                                        //     'epc' => 0,
                                        // ];
                                        // $tempHourlyData[$hour][] = $temp;
                                        $tempHourlyData[$hour]['clicks'] += floatval($item['visits']);
                                        $tempHourlyData[$hour]['revenue'] += floatval($item['cost']);
                                    }
                                }
                            }
                            elseif($tracker == 'binom'){
                                $result = getBinomCampaignStatByHour($campaign->trackerAuth->auth, $dateFrom, $dateTo, $campaign->camp_id);
                                if(!empty($result)){
                                    foreach($result as $item){
                                        if($item['level'] == 1){
                                            $hour = intval($item['name']);
                                            // $visits = floatval($item['clicks']);
                                            // $cost = floatval($item['cost']);
                                            // $temp = [
                                            //     'clicks' => $visits,
                                            //     'revenue' => $cost,
                                            //     'epc' => 0,
                                            // ];
                                            // $tempHourlyData[$hour][] = $temp;
                                            $tempHourlyData[$hour]['clicks'] += floatval($item['clicks']);
                                            $tempHourlyData[$hour]['revenue'] += floatval($item['cost']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach($tempHourlyData as $key => $item){
                if($item['clicks']){
                    $item['epc'] = $item['revenue'] / $item['clicks'];
                }
                $item['clicks'] = number_format($item['clicks']);
                $item['revenue'] = number_format($item['revenue'], 2);
                $item['epc'] = number_format($item['epc'], 2);
                $tempHourlyData[$key] = $item;
            }
            $output->status = true;
            $output->data['hourly_data'] = $tempHourlyData;
            
        }
        else{
            $output->msg->show = true;
            $output->msg->text = 'Campaign not found';
        }
        
        return response()->json($output);
    }

    public function getTrackerCampaignStat($tracker, $auth, $dateFrom, $dateTo, $trackerCampId, $campaignId)
    {
        $output = [
            'all' => null,
            'today' => trackerCampaignStatDefaults(),
        ];
        $todayDate = date('Y-m-d');
        $reports = [];
        $reportDB = CampaignGroupReport::where('campaign_id', $campaignId)->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->orderBy('date', 'asc')->get();
        if(!empty($reportDB)){
            $reports = $reportDB->toArray();
        }
        if($dateTo == $todayDate){
            $searchTodayInApi = true;
            if(!empty($reports)){
                $lastReportDate = end($reports)['date'];
                if($lastReportDate == $dateTo){
                    $searchTodayInApi = false;
                }
            }
            if($searchTodayInApi){
                $todayStats = getTrackerCampaignStat($tracker, $auth, $todayDate, $todayDate, $trackerCampId);
                $reports[] = $todayStats;
                $output['today'] = $todayStats;
            }
        }
        $totals = trackerCampaignStatSum($reports);
        $output['all'] = $totals;
        return $output;
    }

    public function getAllCampaignGroupStatsV2($params)
    {
        $todayDateFrom = date('Y-m-d');
        $todayDateTo = date('Y-m-d');
        $getTodayStats = false;
        $groupId = 'all';
        if(!empty($params['from']) && !empty($params['to'])){
            $dateFrom = $params['from'];
            $dateFrom = date('Y-m-d', strtotime($dateFrom));
            $dateTo = $params['to'];
            $dateTo = date('Y-m-d', strtotime($dateTo));
        }
        else{
            $dateFrom = $todayDateFrom;
            $dateTo = $todayDateTo;
        }
        if(!empty($params['groupId']) || $params['groupId'] != 'all'){
            $groupId = $params['groupId'];
        }
        if($dateTo == date('Y-m-d')){
            $getTodayStats = true;
            $dateTo = date("Y-m-d", strtotime("-1 day", strtotime($dateTo)));
        }
        $campaignGroupStats = [];

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
    }

    public function getPendingInvoiceAmount($includingToday)
    {
        $output = [
            'from' => '',
            'to' => '',
            'amount' => 0,
        ];
        $amount = 0;
        $dateFrom = null;
        $lastInvoice = Invoice::whereUserId($this->user->id)->whereHandled(true)->orderBy('id', 'desc')->first();
        if(!empty($lastInvoice)){
            $dateFrom = date("Y-m-d", strtotime("+1 day", strtotime($lastInvoice->end_date)));
        }
        else if($this->user->created_at != null){
            $dateFrom = date("Y-m-d", strtotime($this->user->created_at));
        }

        
        if($includingToday){
            $dateTo = date("Y-m-d");
        }
        else{
            $dateTo = date("Y-m-d", strtotime("-1 day"));
        }
        // dd($dateFrom, $dateTo);
        $output['from'] = $dateFrom;
        $output['to'] = $dateTo;
        if($dateFrom != null && strtotime($dateTo) >= strtotime($dateFrom)){
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
                            $trackerAuth = $campaign->trackerAuth()->with('trackerUser.tracker')->first();
                            $tracker = $trackerAuth->trackerUser->tracker->slug;
                            $auth = $trackerAuth->auth;
                            
                            $reports = $this->getTrackerCampaignStat($tracker, $auth, $dateFrom, $dateTo, $campaign->camp_id, $campaign->id);
                            // dd($reports);
                            // $reports = $campaign->reports()->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->get();
                            if(!empty($reports['all'])){
                                // foreach($reports as $report){
                                //     $invoiceData['amount'] += $report['cost'];
                                // }
                                $invoiceData['amount'] += $reports['all']['cost'];
                            }
                        }
                    }
                }
            }
            $amount = $invoiceData['amount'] + $invoiceData['credit'];
        }
        
        $output['amount'] = $amount;
        
        return $output;
    }

    public function invoices()
    {
        $this->invoices = Invoice::whereUserId($this->user->id)->orderBy('id', 'desc')->get();
        return view('user.invoice', $this->data);
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
        /*
        $dateFrom = '2022-01-05'; 
        $dateTo = '2022-01-10'; 
        $campaignId = 8;
        $reports = [];
        $reportDB = CampaignGroupReport::where('campaign_id', $campaignId)->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)->orderBy('date', 'asc')->get();
        if(!empty($reportDB)){
            $reports = $reportDB->toArray();
        }
        if($dateTo == date('Y-m-d')){
            $searchTodayInApi = true;
            if(!empty($reports)){
                $lastReportDate = end($reports)['date'];
                if($lastReportDate == $dateTo){
                    $searchTodayInApi = false;
                }
            }
            if($searchTodayInApi){

            }
        }
        $totals = trackerCampaignStatSum($reports);
        dd($reports, $totals);
        */
        $campaignService->getAllCampaignStats();
    }

    public function checkCronStatus()
    {
        $data = new MyLog();
        $data->type = "daily 1am cron check";
        $data->data = ['date' => date("Y-m-d H:i:s")];
        $data->save();
    }
}
