<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use DB;
use Auth;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;
    protected $table = 'banners';
    protected $primaryKey = 'banner_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array

     */
    protected $fillable = ['banner_id', 'title', 'link', 'banner_description', 'image', 'status', 'created_at', 'updated_at'];

    public function getImageAttribute($value){
        if($value != '' && $value != null){
            if(isset($value)) {
                return url('/resources/uploads/banner').'/'.$value;
              }else{
                  return url('/resources/assets/img/default.png');
              }
          }else {
              return url('/resources/assets/img/default.png');
          }
    }
}
