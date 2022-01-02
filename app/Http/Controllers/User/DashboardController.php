<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CampaignGroup;
use App\Models\CampaignGroupUser;

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
        foreach($campaignGroupUsers as $campaignGroupUser){
            $campaignGroup = $campaignGroupUser->campaignGroup;
            $group = [
                'id' => $campaignGroup->id, 
                'name' => $campaignGroup->name,
                'campaigns' => [],
                'total' => [],
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
                        'revenue' => $stats['cost'],
                        'epc' => $stats['cpv'],
                    ]
                ];
                $apiResponsesTotals[] = $stats;
            }
            
            $group['campaigns'] = $apiResponses;
            $total = trackerCampaignStatSum($apiResponsesTotals);
            $group['total'] = [
                'clicks' => $total['visits'],
                'revenue' => $total['cost'],
                'epc' => $total['cpv'],
            ];
            $campaignGroupTotalStats[] = $total;
            $campaignGroupStats[$campaignGroup->id] = $group;
        }
        $allTotals = trackerCampaignStatSum($campaignGroupTotalStats);
        $campaignGroupTotalStats = [
            'clicks' => $allTotals['visits'],
            'revenue' => $allTotals['cost'],
            'epc' => $allTotals['cpv'],
        ];
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
                            'revenue' => $item['cost'],
                            'epc' => $item['cpv'],
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
                                'revenue' => $cost,
                                'epc' => $cpv,
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
}
