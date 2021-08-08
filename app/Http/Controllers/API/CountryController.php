<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Response\APIResponse;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\User;
//use Lcobucci\JWT\Parser;
use App\Helper\GlobalHelper;
use Hash;
use DB;
use Mail;
use App\Notifications\UserRegistration;
use App\Notifications\ForgotPassword;
use App\Country;
use App\State;
use App\City;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->APIResponse = new APIResponse();
    }

    /**
    * Developed By : Adhiya Kaushal
    * Description  : Get Country
    * Date         : 09-05-2019
    */
    public function country(Request $request){
        try {
            $country = Country::where('status','1')->get()->toArray();
            $country = GlobalHelper::removeNullMultiArray($country);
            if($country != 0){
                return $this->APIResponse->respondWithMessageAndPayload($country);
            }else{
                return $this->APIResponse->respondNotFound(__('No Record Found'));
            }
        }catch (\Exception $e) {
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

    /**
    * Developed By : Adhiya Kaushal
    * Description  : Get state
    * Date         : 09-05-2019
    */
    public function state(Request $request){
        $data = $request->json()->get('data');
        try{
            if(empty($data)) {
                return $this->APIResponse->respondNotFound(__('Data key not found or Empty'));
            }else {
                $rules = array(
                    'country_id'=>'required'
                );
                $messages = [
                    'country_id.required' => 'Please enter Country id',
                ];
                $validator = Validator::make($data, $rules,$messages);
                if($validator->fails()) {
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    $state = State::where('country_id', $data['country_id'])->where('status','1')->get()->toArray();
                    if(empty($state)){
                        return $this->APIResponse->respondNotFound(__('Oops No Record found with given ID'));
                    }else{
                        $state = GlobalHelper::removeNullMultiArray($state);
                        return $this->APIResponse->respondWithMessageAndPayload($state);
                    }
                }
            }
        }catch (\Exception $e) {
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

    /**
    * Developed By : Adhiya Kaushal
    * Description  : Get city
    * Date         : 09-05-2019
    */
    public function city(Request $request){
        $data = $request->json()->get('data');
        try{
            if(empty($data)) {
                return $this->APIResponse->respondNotFound(__('Data key not found or Empty'));
            }else {
                $rules = array(
                    'state_id'=>'required|numeric'
                );
                $messages = [
                    'state_id.required' => 'Please enter State id',
                ];
                $validator = Validator::make($data, $rules,$messages);
                if($validator->fails()) {
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    $city = City::where('state_id', $data['state_id'])->where('status','1')->get()->toArray();
                    if(empty($city)){
                        return $this->APIResponse->respondNotFound(__('Oops No Record found with given ID'));
                    }else{
                        $city = GlobalHelper::removeNullMultiArray($city);
                        return $this->APIResponse->respondWithMessageAndPayload($city);
                    }
                }
            }
        }catch (\Exception $e) {
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

}
