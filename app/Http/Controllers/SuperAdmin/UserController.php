<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tracker;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->trackers = Tracker::all();
        $roleId = 2;
        $this->users = User::with(['roles', 'trackers'])->whereHas('roles', function($query) use ($roleId){
            $query->where('roles.id', '=', $roleId);
        })->orderBy('id', 'desc')->get();
        return view('super-admin.users', $this->data);
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
            'email' => 'required|email:strict',
            'password' => 'required|min:8|max:20',
        ]);

        $output = $this->ajaxRes(true);

        $name = trim($request->input('name'));
        $email = trim($request->input('email'));
        $password = trim($request->input('password'));
        $trackers = $request->input('trackers');

        $email = strtolower($email);

        if(empty($trackers)){
            $trackers = [];
        }
        $trackerIds = [];
        foreach($trackers as $tracker){
            $tracker = intval(trim($tracker));
            if($tracker){
                $trackerIds[] = $tracker;
            }
        }

        $checkEsxist = User::where('email', $email)->first();
        if(empty($checkEsxist)){
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->parent_id = $this->user->id;
            $user->status = 1;
            $user->save();
            $user->roles()->attach(2);
            foreach($trackerIds as $trackerId){
                $user->trackers()->attach($trackerId);
            }
            $output->msg->text = 'User Created Successfully';
            $output->msg->type = 'success';
            $output->msg->title = 'Successful';
            $output->status = true;
            $output->data['user'] = User::with(['roles', 'trackers'])->find($user->id);
        }
        else{
            $output->msg->text = 'User already exists with this Email';
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
