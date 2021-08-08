<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class DashboardController extends Controller
{
   public function __invoke(Request $request)
    {
     $totalUser = User::whereHas("roles", function($q){ $q->where("name","!=","admin"); })->where('user_status','!=','-1')->count();                
        return view('admin.dashboard',['totalUser' => $totalUser]);//' "Welcome to our homepage";
    }
    public function dashboardFilterData(Request $request){

    $start_date = date("Y-m-d H:i:s", strtotime($request->start_date));
    $end_date = date("Y-m-d 23:59:59", strtotime($request->end_date));

    $totalUser =User::whereHas("roles", function($q){ $q->where("name", "user"); })
                      ->where('users.created_at','>=',$start_date)
                      ->where('users.created_at','<=',$end_date)
                      //->where('role_user.role_id','=','2')
                      //->where('users.user_status','=','1')
                      ->count();

     
    return $totalUser;
  }
}
