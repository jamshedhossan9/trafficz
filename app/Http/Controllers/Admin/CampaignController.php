<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CampaignGroup;
use App\Models\CampaignGroupUser;
use App\Models\TrackerAuth;
use App\Models\User;
use App\Models\Tag;
use App\Models\CampaignTag;
use App\Models\Credit;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->campaignGroups = CampaignGroup::where('user_id', $this->user->id)->with(['users', 'campaigns.trackerAuth.trackerUser.tracker','credit'])->orderBy('id', 'desc')->get();
        // dd($this->campaignGroups);
        $this->trackerAuths = TrackerAuth::where('user_id', $this->user->id)->with(['trackerUser', 'trackerUser.tracker'])->get();
        $this->campaignTags = Tag::where('user_id', $this->user->id)->orderBy('name', 'asc')->get();
        return view('admin.campaigns', $this->data);
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
        $request->validate([
            'name' => 'required|max:100',
        ]);

        $output = $this->ajaxRes(true);

        $name = trim($request->input('name'));
        $checkEsxist = CampaignGroup::where('name', $name)->where('user_id', $this->user->id)->first();
        if(empty($checkEsxist)){
            $group = new CampaignGroup();
            $group->user_id = $this->user->id;
            $group->name = $name;
            $group->save();

            $output->msg->text = 'Group Created Successfully';
            $output->msg->type = 'success';
            $output->msg->title = 'Successful';
            $output->status = true;
            $output->data['group'] = $group;
        }
        else{
            $output->msg->text = 'A group is already exists with same name';
        }
        return response()->json($output);
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

    public function deleteCampaign($id)
    {
        $output = $this->ajaxRes(true);
        $id = intval($id);
        Campaign::destroy($id);
        $output->status = true;
        $output->msg->text = 'Deleted Campaign';
        $output->msg->title = 'Successful';
        $output->msg->type = 'success';
        return response()->json($output);
    }

    public function addCampaign(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'campaign_id' => 'required|max:200',
            'campaign_group_id' => 'required',
            'tracker_auth_id' => 'required',
        ],[
            'campaign_group_id.required' => 'Group not found'
        ]);

        $output = $this->ajaxRes(true);

        $campaignGroupId = trim($request->input('campaign_group_id'));
        $name = trim($request->input('name'));
        $campaignId = trim($request->input('campaign_id'));
        $trackerAuthId = trim($request->input('tracker_auth_id'));
        $campaignGroupId = intval($campaignGroupId);
        $trackerAuthId = intval($trackerAuthId);
        $checkEsxist = Campaign::where('campaign_group_id', $campaignGroupId)->where('tracker_auth_id', $trackerAuthId)->where('camp_id', $campaignId)->first();
        if(empty($checkEsxist)){

            $campaign = new Campaign();
            $campaign->campaign_group_id = $campaignGroupId;
            $campaign->tracker_auth_id = $trackerAuthId;
            $campaign->name = $name;
            $campaign->camp_id = $campaignId;
            $campaignSave = $campaign->save();

            if($campaignSave){
                if($request->has('campaign_tag_id')){
                    $tagIds = $request->campaign_tag_id;
                    if(!empty($tagIds)){
                        foreach($tagIds as $tagId){
                            $tagId = trim($tagId);
                            if(is_numeric($tagId)){
                                $tagId = intval($tagId);
                                $tag = Tag::where('id', $tagId)->where('user_id' , $this->user->id)->first();
                                if(!empty($tag)){
                                    $tag->campaigns()->attach($campaign->id);
                                }
                            }
                            else{
                                $tagSlug = Str::slug($tagId);
                                $checkTagExists = Tag::where('user_id' , $this->user->id)->where('slug', $tagSlug)->first();
                                if(empty($checkTagExists)){
                                    $tag = new Tag();
                                    $tag->user_id = $this->user->id;
                                    $tag->name = $tagId;
                                    $tag->slug = $tagSlug;
                                    $tag->save();
                                    $tag->campaigns()->attach($campaign->id);
                                }
                                else{
                                    $checkTagExists->campaigns()->attach($campaign->id);
                                }
                            }
                        }
                    }
                }
            }

            $output->msg->text = 'Campaign added to Group Successfully';
            $output->msg->type = 'success';
            $output->msg->title = 'Successful';
            $output->status = true;
            $output->data['group'] = CampaignGroup::where('id', $campaignGroupId)->with(['users', 'campaigns.trackerAuth.trackerUser.tracker'])->first();
        }
        else{
            $output->msg->text = 'This campaign is already added to this group';
        }
        return response()->json($output);
    }
    
    public function addUser(Request $request)
    {
        $request->validate([
            'campaign_group_id' => 'required',
        ],[
            'campaign_group_id.required' => 'Group not found'
        ]);

        $output = $this->ajaxRes(true);

        $campaignGroupId = trim($request->input('campaign_group_id'));
        $campaignGroupId = intval($campaignGroupId);
        $users = $request->users;
        if(empty($users)) $users = [];
        CampaignGroupUser::where('campaign_group_id', $campaignGroupId)->delete();
        
        $addedUserIds = [];
        foreach($users as $user){
            $user = intval($user);
            if($user){
                $addedUserIds[] = $user;
            }
        }
        if(!empty($addedUserIds)){
            foreach($addedUserIds as $id){
                $groupUser = new CampaignGroupUser();
                $groupUser->user_id = $id;
                $groupUser->campaign_group_id = $campaignGroupId;
                $groupUser->save();
            }
            $output->msg->text = 'Campaign Group updated Successfully';
            $output->msg->type = 'success';
            $output->msg->title = 'Successful';
            $output->status = true;
            
        }
        else{
            $output->msg->text = 'Campaign Group updated. No user is added for now';
            $output->msg->type = 'info';
            $output->msg->title = 'Successful';
            $output->status = true;
        }
        $output->data['group'] = CampaignGroup::where('id', $campaignGroupId)->with(['users', 'campaigns.trackerAuth.trackerUser.tracker'])->first();

        return response()->json($output);
    }

    public function addedUsersToGroup($id)
    {
        $campaignGroupId = intval($id);
        $output = $this->ajaxRes();
        $group = CampaignGroup::where('id', $campaignGroupId)->with(['users'])->first();
        $users = User::with('campaignGroups')->where('parent_id', $this->user->id)->orderBy('id', 'desc')->get();
        
        $userIdsInGroup = [];
        $allUsers = [];
        foreach($group->users as $user){
            $userIdsInGroup[] = $user->id;
        }
        foreach($users as $user){
            $temp = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'using_group' => false
            ];
            if(in_array($user->id, $userIdsInGroup)){
                $temp['using_group'] = true;
            }
            $allUsers[] = $temp;
        }
        if(empty($allUsers)){
            $output->status = false;    
            $output->msg->show = true;    
            $output->msg->text = 'Users not found';    
        }
        else{
            $output->status = true;
        }
        $output->data['users'] = $allUsers;
        
        return response()->json($output);
    }

    public function addCredit(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'amount' => 'required',
            'campaign_group_id' => 'required',
        ],[
            'campaign_group_id.required' => 'Group not found'
        ]);

        $output = $this->ajaxRes(true);

        $date = $request->date;
        $amount = $request->amount;
        $groupId = $request->campaign_group_id;
        $groupId = intval($groupId);
        $amount = floatval($amount);

        $checkExists = Credit::where('campaign_group_id', $groupId)->where('date', $date)->first();
        if(empty($checkExists)){
            $credit = new Credit();
            $credit->campaign_group_id = $groupId;
            $credit->date = $date;
            $credit->amount = $amount;
            $credit->save();

            $output->status = true;
            $output->msg->text = 'Credit added Successfully';
            $output->msg->title = 'Successful';
            $output->msg->type = 'success';
            $group = CampaignGroup::with('credit')->find($groupId);
            $output->data['group'] = $group;
            $output->data['credit'] = $credit;
        }
        else{
            $output->msg->text = 'Credit already added for this date';
        }

        return response()->json($output);
    }
    
    public function deleteCredit($id)
    {
        $output = $this->ajaxRes(true);
        $id = intval($id);

        $credit = Credit::with('campaignGroup')->find($id);
        if($credit && !$credit->used && !is_null($credit->campaignGroup) && $credit->campaignGroup->user_id == $this->user->id){
            $groupId = $credit->campaign_group_id;
            $credit->delete();    
            $output->status = true;
            $output->msg->text = 'Credit Delete Successfully';
            $output->msg->title = 'Successful';
            $output->msg->type = 'success';
            $group = CampaignGroup::with('credit')->find($groupId);
            $output->data['group'] = $group;
        }
        else{
            $output->msg->text = 'Credit not found';
        }

        return response()->json($output);
    }

    public function listCredit($id)
    {
        $output = $this->ajaxRes();
        $id = intval($id);

        $credits = Credit::where('campaign_group_id', $id)->orderBy('date', 'desc')->limit(15)->get();
        if(!empty($credits)){
            $output->status = true;
            $output->data['credits'] = $credits;
            $output->msg->text = 'Credits list';
        }
        else{
            $output->msg->text = 'Credits not found';
        }

        return response()->json($output);
    }
}
