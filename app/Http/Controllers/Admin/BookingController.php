<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Airlines;
use App\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $booking = Booking::with(['airline','user'])->get();
        return view('admin.booking.list',compact('booking'));
    }

    public function create()
    {
        $users = User::where('id','!=',Auth::id())->get();
        $airline = Airlines::all();
        return view('admin.booking.create',compact('users','airline'));
    }

    public function getFair(Request $request)
    {
        $airline = Airlines::where('id',$request->id)->first();
        if($airline){
          return $airline->charge;
        }
        return 0;
    }

    public function store(Request $request)
    {
        $booking = new Booking;
        $booking->user_id =$request->name;
        $booking->flight_id =$request->airline;
        $booking->travell_date =$request->travell_date;
        $booking->no_of_guest =$request->no_of_guest;
        $booking->save();
        return redirect('/admin/booking');
    }
}
