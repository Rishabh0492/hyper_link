<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{

   use HasApiTokens, Notifiable,HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'first_name', 'last_name', 'email','user_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
   public function getProfileImageAttribute($value){
        if($value != '' && $value != null){
            if(isset($value)) {
                return (url('/resources/uploads/profile/').'/').$value;
            }else{
                return '';
            }
        }else {
            return '';
        }
    }

    public static function getAllUsers(){
        $user = User::whereHas('roles', function($q){$q->where('name','user');})->where('user_status','=','1')->get();
        return $user;
    }
}
