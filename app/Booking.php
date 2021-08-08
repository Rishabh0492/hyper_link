<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';


    public function user(){
        return $this->hasOne('App\User','id','user_id');
    }

    public function airline(){
        return $this->hasOne('App\Airlines','id','flight_id');
    }

}
