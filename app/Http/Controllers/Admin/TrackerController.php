<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TrackerAuth;

class TrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = User::with(['roles', 'trackerUsers', 'trackerUsers.tracker'])->find($this->user->id);
        $this->trackerAuths = TrackerAuth::where('user_id', $this->user->id)->with(['trackerUser', 'trackerUser.tracker'])->orderby('id','desc')->get();
        // dd($this->user);
        return view('admin.trackers', $this->data);
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
        $output = $this->ajaxRes(true);

        $trackerUserId = trim($request->input('tracker_user_id'));
        $trackerName = trim($request->input('tracker_slug'));
        $name = trim($request->input('name'));
        $auth = [];
        $passRules = true;
        $msg = '';
        if(empty($trackerUserId) || empty($trackerName)){
            $passRules = false;
            $msg = 'Tracker not found';
        }
        if($passRules){
            if(empty($name)){
                $passRules = false;
                $msg = 'Name is Required'; 
            }
        }
        if($passRules){
            if($trackerName == 'voluum'){
                $auth['access_key_id'] = trim($request->input('access_key_id'));
                $auth['access_key'] = trim($request->input('access_key'));
                if(empty($auth['access_key_id'])){
                    $passRules = false;
                    $msg = 'Access key ID is Required';
                }
                if($passRules){
                    if(empty($auth['access_key'])){
                        $passRules = false;
                        $msg = 'Access key is Required';
                    }   
                }
            }
            else if($trackerName == 'binom'){
                $auth['api_key'] = trim($request->input('api_key'));
                $auth['api_endpoint'] = trim($request->input('api_endpoint'));
                $auth['web_portal_url'] = trim($request->input('web_portal_url'));

                if(empty($auth['api_key'])){
                    $passRules = false;
                    $msg = 'API key is Required';
                }
                if($passRules){
                    if(empty($auth['api_endpoint'])){
                        $passRules = false;
                        $msg = 'API Endpoint is Required';
                    }   
                }
                if($passRules){
                    if(empty($auth['web_portal_url'])){
                        $passRules = false;
                        $msg = 'Web portal URL is Required';
                    }   
                }
            }
        }

        if($passRules){
            $trackerAuth = new TrackerAuth();
            $trackerAuth->name = $name;
            $trackerAuth->user_id = $this->user->id;
            $trackerAuth->tracker_user_id  = intval($trackerUserId);
            $trackerAuth->auth  = $auth;
            $trackerAuth->save();

            $output->msg->text = 'Auth Added Successfully';
            $output->msg->type = 'success';
            $output->msg->title = 'Successful';
            $output->status = true;
            $output->data['tracker'] = TrackerAuth::with(['trackerUser', 'trackerUser.tracker'])->find($trackerAuth->id);
        }
        else{
            $output->msg->text = $msg;
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
}
