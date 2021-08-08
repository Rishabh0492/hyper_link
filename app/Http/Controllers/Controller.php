<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

     public function UpdateStatus($id,$table,$field_name)
    {
        if(isset(($id))){
        	//DB::enableQueryLog();
                $record = $table::find($id);
               // dd(DB::getQueryLog());
               // dd($record);
                $result = "";
                
                if(isset($record)){
                    if($record->$field_name == 0){
                        $record->$field_name= '1';
                        $result ="Active";
                    }
                    else {
                        $record->$field_name= '0';
                        $result ="Deactive";
                    }
                    $record->save();
                    return $result;
                }
                else{
                    return 'error';
                }
        }
    }
}
