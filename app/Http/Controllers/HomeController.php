<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // dd($this->data);
        // dd(auth()->user()->trackers);
        // return view('home', $this->data);
        if(isSuperAdmin()){
            return redirect(route('superAdmin.users.index'));
        }
        else if(isAdmin()){
            return redirect(route('admin.users.index'));
        }
        else{
            return redirect(route('user.dashboard.index'));
        }
    }
}
