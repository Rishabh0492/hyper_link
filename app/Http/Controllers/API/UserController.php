<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use App\Helper\GlobalHelper;
use App\Http\Response\APIResponse;
use App\Notifications\APIForgotPassword;
use App\Notifications\ForgotPassword;
use App\Notification;
use Lcobucci\JWT\Parser;
use Validator;
use Lang;
use Hash;
use DB;
use App\User;
use App\OauthAccessToken;
use Str;
use App\Jobs\UserRegistrationEmailJob;
use Storage;
use File;
use Mail;
use Carbon\Carbon;
use URL;

class UserController extends Controller
{
	 public function __construct()
    {
        $this->APIResponse = new APIResponse();
    }
    public function form_data()
    {
        $form_data = ['gender'=>['Male','Female'],
                      'martial_status'=> ['Single','Married','Living Together','Divorced','Common Law','Widowed'],
                      'no_of_dependents'=>['0','1','2','3','4+'],
                      'eduction'=>['Did Not Finish High School','High School','College',"Bachelor's Degree","Master's Degree",'Phd'],
                      'residence_type' =>['Apartment Building (less than 10 units)', 'Apartment Building (10 units or more)', 'Codominium', 'Single Detached House', 'Semi Detached House', 'Townhouse', 'Other'],
                      'address_lived_time'=>['6 Months or Less', '7 to 12 Months', '1 to 2 Years', '3 to 6 Years', '7+ Years'],
                      'home_status'=>['Rent', 'Own (With Mortgage)', 'Own (Without Mortgage)', 'Living with Family', 'Living with Roomates','Social Housing'],
                      'income_source'=>['Canada Pension', 'Child Tax Benefit', 'Employment Insurance', 'Old Age Pension', 'Ontario Disability Support Program', 'Veterans Disability'],
                      'time_revenue'=>['3 Months or Less', '4 to 6 Months', '7 to 12 Months', '1 to 2 Years', '3 to 6 Years', '7+'],
                      'form_of_payment'=>['Direct Deposit', 'Cheque', 'Cash', 'E-Transfer'],
                      'frequency_pay'=>['Weekly', 'Bi-Weekly', 'Monthly', 'Bi-Monthly'],
                      'account_creation_date'=>['3 Months', '4 to 6 Months', '7 to 12 Months', '1 to 2 Years', "3' to 6 Years", '7+'],
                      'e_transfers'=>['Yes I can receive e-transfers', 'No my bank prohibits e-transfers', 'No my email is blacklisted from the bank','No I prefer cash in store'],
                      'is_bankruptcy'=>['Yes','No'],
                      'number_of_bankruptcy'=>['0', '1','2','3','4+'],
                      'number_of_active_loan'=>['0', '1','2','3','4+'],
                      'loan_type'=>['Payday Loans', 'Personal Loans','Both','Other'],
                      'overdue_loan'=>['Yes','No'],
                     ];
    }
    /**
    * Developed By :
    * Description  : Registration
    * Date         :
    */
    public function register(Request $request){
        $data = $request->json()->get('data');
       // var_dump( $data );
        try{
            if (empty($data)) {
                return $this->APIResponse->respondNotFound(__('Data key not found or Empty'));
            } else {
                $rules = array(
                    'first_name'=>'required',
                    'last_name'=>'required',
                    'email' => 'required|string|email|max:255|unique:users',
                    // 'password' => 'required|string|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,18}$/',
                    'password' => 'required|string|min:6|max:50',
                    // 'password' => 'required|string|min:6|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                    // 'device_type' => 'required',
                    // 'device_token' => 'required',
                    // 'province' => 'required',
                    // 'about_us' => 'required',
                );
                $messages = [
                    'first_name.required' => 'Please enter first name',
                ];
                $validator = Validator::make($data, $rules,$messages);
                if($validator->fails()) {
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    //$confirmation_code = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,20);
                    $newuser = new User();
                    $newuser->first_name = $data['first_name'];
                    $newuser->last_name = $data['last_name'];
                    $newuser->email = $data['email'];
                    $newuser->password = bcrypt($data['password']);
                    // $newuser->device_type = $data['device_type'];
	                $newuser->social_provider = 'normal';
                    //$token = $newuser->createToken('accessToken')->accessToken;

                    if ($newuser->save()) {
                        // if(isset($data['device_token']) && ($data['device_token']) != null ){
                        //     $checkToken = new UserToken();
                        //     $checkToken->user_id = $newuser->id;
                        //     $checkToken->device_type = $data['device_type'];
                        //     $checkToken->device_token = $data['device_token'];
                        // }
                        // $newuser->user_name = str_slug($newUser->first_name.'-'.$newUser->last_name,"-")."-".($newUser->id);
                        // $newUser->save();
                        //$newuser->notify(new UserRegistration($newuser));
                       // $newuser->roles()->attach(2);
                        dispatch(new UserRegistrationEmailJob($newuser));
                        $newuser->assignRole(2);

                        // $newuser = GlobalHelper::removeNull($newuser);
                        return $this->APIResponse->respondWithMessageAndPayload($newuser,'Successfully Register');
                    }else{
                        return $this->APIResponse->respondInternalError(__(Lang::get('messages.registrationfailed')));
                    }
                }
            }
        }catch (\Exception $e) {
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

    /**
    * Developed By :
    * Description  : Login
    * Date         :
    */
    public function login(Request $request){
        $data = $request->json()->get('data');

		try{
        	if(empty($data)){
                return $this->APIResponse->respondNotFound(__('Data key not found or Empty'));
            }else{
                $rules = array(
                    'email' =>' required|email|max:255',
                    'password' => 'required',
                    // 'device_type' => 'required',
                    // 'device_token' => 'required',
                );
                $messages = [
                    'email.required' => 'Please enter Email',
                    'password.required' => 'Please enter password',
                    'device_type.required'=> 'Device type is required',
                    'device_token.required'=>'Device token is required',
                ];
                $validator = Validator::make($data,$rules,$messages);
                if($validator->fails()){
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    $checkUserExists = User::where('email',$data['email'])->first();
                    if(empty($checkUserExists)){
                        return $this->APIResponse->respondNotFound(Lang::get('messages.emailnotregistered'));
                    }else{
                        // account status`
                        if($checkUserExists->user_status == '0'){
                            return $this->APIResponse->respondUnauthorized(__(Lang::get('messages.accountblockbyadmin')));
                        }else{
                             if($checkUserExists->mobile_verified == '0'){
                                 return $this->APIResponse->respondUnauthorized(__(Lang::get('messages.pleaseverifyphone')));
                             }else{
                                if(Hash::check($data['password'],$checkUserExists->password)) {
                                    $token = null;
                                    try {
                                        if (!$token = $checkUserExists->createToken('Laravel')->accessToken) {
                                            return $this->APIResponse->respondUnauthorized(__('Invalid_email_or_password'));
                                        }
                                    } catch (Exception $e) {
                                        return $this->APIResponse->respondInternalError(__('failed_to_create_token'));
                                    }
                                    $checkUserExists->device_type = $data['device_type'];
                                    $checkUserExists->device_token = $data['device_token'];
                                    if($data['device_type'] == '1') {
                                      $checkUserExists->device_type = $data['device_type'];
                                    }
                                    $checkUserExists->save();
                                    $checkUserExists['token'] = $token;
                                    $checkUserExists = GlobalHelper::removeNull($checkUserExists->toArray());
                                    return $this->APIResponse->respondWithMessageAndPayload($checkUserExists,Lang::get('messages.loginsuccessfully'));
                                }else{
                                    return $this->APIResponse->respondUnauthorized(__(Lang::get('messages.credentialsdonotmatch')));
                                }
                            }
                        }
                    }
                }
            }
        }catch(\Exception $e){
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

    /**
    * Developed By :
    * Description  : Logout
    * Date         :
    */
    public function logout(Request $request){
      $value = $request->bearerToken();
      ///if(strlen($value) == '1072') {
      if($value) {
        //$id = (new Parser())->parse($value)->getHeader('jti');
        $id = (new Parser())->parse($value)->getClaim('jti');
        $data = OauthAccessToken::find($id);
        if($data) {
        	$data->delete();
          	User::where('id',$data->user_id)->update(['device_token' => NULL]);
    		return $this->APIResponse->respondWithMessage('You are logged out successfully!');
        }
        else
           return $this->APIResponse->respondNotFound(__('Data key not found or Empty'));
      }
    }

     /**
    * Developed By :
    * Description  : social registration
    * Date         :
    */

    public function socialRegister(Request $request){
		$data = $request->json()->get('data');
		try{
			if(empty($data)){
				return $this->APIResponse->respondNotFound(__(trans('messages.data.dataKey_notFound')));
			}else{
				$rules = array(
					"social_provider"=>'required|in:google,facebook,twitter',
					'social_provider_id' => 'required',
					// 'email' => 'email|required',
				);
				$messages = [

				];
				$validator = Validator::make($data,$rules,$messages);
				if($validator->fails()){
					return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
				}else{
                    if($data['social_provider'] == 'google'){
                        $checkSocialExists = User::where('google_id', $data['social_provider_id'])
                                                    ->orWhere('social_provider_id', $data['social_provider_id'])
                                                    ->first();
					}elseif($data['social_provider'] == 'facebook') {
						$checkSocialExists = User::where('facebook_id', $data['social_provider_id'])
                                                    ->orWhere('social_provider_id', $data['social_provider_id'])
                                                    ->first();
					}elseif($data['social_provider'] == 'twitter'){
                        $checkSocialExists = User::where('twitter_id', $data['social_provider_id'])
                                                    ->orWhere('social_provider_id', $data['social_provider_id'])
                                                    ->first();
                    }
                    if($checkSocialExists){
                        $token = null;
                        try {
                            if (!$token = $checkSocialExists->createToken('Laravel')->accessToken) {
                                return $this->APIResponse->respondUnauthorized(__(trans('messages.invalidEmailOrPassword')));
                            }
                        } catch (Exception $e) {
                            return $this->APIResponse->respondInternalError(__(trans('messages.failed_to_create_token')));
                        }
                        $checkSocialExists['token'] = $token;
                        return $this->APIResponse->respondWithMessageAndPayload($checkSocialExists,trans('messages.loginsuccessfully'));
                    }else{
                        if((isset($data['email'])) && ($data['email'] != null)){
                            $emailExsist = User::where('email', $data['email'])->first();
                            if ($emailExsist) {
                                if($emailExsist->user_status == '0'){
                                    return $this->APIResponse->respondUnauthorized(__(Lang::get('messages.accountblockbyadmin')));
                                }else{
                                    $emailExsist->social_provider_id = $data['social_provider_id'];
                                    if($data['social_provider'] == 'google'){
                                        $emailExsist->google_id = $data['social_provider_id'];
                                        $emailExsist->social_provider = 'google';
                                    }elseif($data['social_provider'] == 'facebook') {
                                        $emailExsist->facebook_id = $data['social_provider_id'];
                                        $emailExsist->social_provider = 'facebook';
                                    }elseif($data['social_provider'] == 'twitter'){
                                        $emailExsist->twitter_id = $data['social_provider_id'];
                                        $emailExsist->social_provider = 'twitter';
                                    }
                                    if($emailExsist->save()){
                                        $token = null;
                                        try {
                                            if (!$token = $emailExsist->createToken('Laravel')->accessToken) {
                                                return $this->APIResponse->respondUnauthorized(__(trans('messages.invalidEmailOrPassword')));
                                            }
                                        } catch (Exception $e) {
                                            return $this->APIResponse->respondInternalError(__(trans('messages.failed_to_create_token')));
                                        }
                                        $emailExsist['token'] = $token;
                                        return $this->APIResponse->respondWithMessageAndPayload($emailExsist,trans('messages.loginsuccessfully'));
                                    }else{
                                        return $this->APIResponse->respondInternalError(__(trans('messages.failed_to_create_token')));
                                    }
                                }
                            }else{
                                $socialnew = new User();
                                $socialnew->email = $data['email'];
                                $socialnew->first_name = $data['first_name'];
                                $socialnew->last_name = $data['last_name']?$data['last_name']:NULL;
                                if (isset($data['device_type'])) {
                                    $socialnew->device_type = $data['device_type'];
                                }
                                if (isset($data['device_token'])) {
                                    $socialnew->device_token = $data['device_token'];
                                }
                                if($data['social_provider'] == 'google'){
                                    $socialnew->google_id = $data['social_provider_id'];
                                }elseif($data['social_provider'] == 'facebook') {
                                    $socialnew->facebook_id = $data['social_provider_id'];
                                }elseif($data['social_provider'] == 'twitter'){
                                    $socialnew->twitter_id = $data['social_provider_id'];
                                }
                                $socialnew->social_provider = $data['social_provider'];
                                $socialnew->password = bcrypt('Qwerty@123');
                                $socialnew->user_status = '1';
                                if($socialnew->save()){
                                    $socialnew->assignRole(2);
                                    $token = null;
                                    try {
                                        if (!$token = $socialnew->createToken('Laravel')->accessToken) {
                                            return $this->APIResponse->respondUnauthorized(__(trans('messages.invalidEmailOrPassword')));
                                        }
                                    } catch (Exception $e) {
                                        return $this->APIResponse->respondInternalError(__(trans('messages.failed_to_create_token')));
                                    }
                                    $socialnew = GlobalHelper::removeNull($socialnew->toArray());
                                    $socialnew['token'] = $token;
                                    return $this->APIResponse->respondWithMessageAndPayload($socialnew,trans('messages.loginsuccessfully'));

                                }else{
                                    return $this->APIResponse->respondUnauthorized(__(trans('messages.registerFail')));
                                }
                            }
                        }else{
                            $socialnew = new User();
                            $socialnew->email = $data['email'];
                            $socialnew->first_name = $data['first_name'];
                            $socialnew->last_name = $data['last_name']?$data['last_name']:NULL;
                            if (isset($data['device_type'])) {
                                $socialnew->device_type = $data['device_type'];
                            }
                            if (isset($data['device_token'])) {
                                $socialnew->device_token = $data['device_token'];
                            }
                            if($data['social_provider'] == 'google'){
                                $socialnew->google_id = $data['social_provider_id'];
                            }elseif($data['social_provider'] == 'facebook') {
                                $socialnew->facebook_id = $data['social_provider_id'];
                            }elseif($data['social_provider'] == 'twitter'){
                                $socialnew->twitter_id = $data['social_provider_id'];
                            }
                            $socialnew->social_provider = $data['social_provider'];
                            $socialnew->password = bcrypt('Qwerty@123');
                            $socialnew->user_status = '1';
                            if($socialnew->save()){
                                $socialnew->assignRole(2);
                                $token = null;
                                try {
                                    if (!$token = $socialnew->createToken('Laravel')->accessToken) {
                                        return $this->APIResponse->respondUnauthorized(__(trans('messages.invalidEmailOrPassword')));
                                    }
                                } catch (Exception $e) {
                                    return $this->APIResponse->respondInternalError(__(trans('messages.failed_to_create_token')));
                                }
                                $socialnew = GlobalHelper::removeNull($socialnew->toArray());
                                $socialnew['token'] = $token;
                                return $this->APIResponse->respondWithMessageAndPayload($socialnew,trans('messages.loginsuccessfully'));

                            }else{
                                return $this->APIResponse->respondUnauthorized(__(trans('messages.registerFail')));
                            }
                        }
                    }
				}
			}
		}catch(\Exception $e){
			return $this->APIResponse->handleAndResponseException($e);
		}
	}

    public function socialRegister_BK(Request $request){

        $data = $request->json()->get('data');
        try{
            if(empty($data)){
                return $this->APIResponse->respondNotFound(__('Data key not found or Empty'));
            }else{
                $rules = array(
                    "social_provider"=>'required',
                    'device_type' => 'required',
                    'device_token' => 'required',
                    'social_provider_id' => 'nullable',
                    'email' => 'email',
                );
                $messages = [

                ];
                $validator = Validator::make($data,$rules,$messages);
                if($validator->fails()){
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    if (strtolower($data['social_provider']) !== 'google' && strtolower($data['social_provider']) !== 'facebook' && strtolower($data['social_provider']) !== 'twitter') {
                        return $this->APIResponse->respondValidationError('Please enter type google or facebook only.');
                    }else{
                        if (empty($data['social_provider_id'])) {
                            return $this->APIResponse->respondValidationError('Please enter social provider id to login.');
                        }else{
                            $checkSocialExists = User::where('social_provider_id', $data['social_provider_id'])->first();
                            if ($checkSocialExists){
                                // if (Hash::check($checkSocialExists->password, $checkSocialExists->password))
                                // {
                                    // $credentials = ['social_provider_id' => $data['social_provider_id'], 'password' => $checkSocialExists->password];
                                    $token = null;
                                    try{
                                        if (!$token = $checkSocialExists->createToken('LMW')->accessToken) {
                                            return $this->APIResponse->respondUnauthorized(__('invalid_parameters'));
                                        }
                                    }catch (Exception $e) {
                                        return $this->APIResponse->respondInternalError(__('failed_to_create_token'));
                                    }
                                    $checkSocialExists = GlobalHelper::removeNull($checkSocialExists);
                                    $checkSocialExists['token'] = $token;

                                    $social = User::find($checkSocialExists->id);
                                    $social->social_provider_id = $data['social_provider_id'];
                                    $social->social_provider = $data['social_provider'];
                                    $social->user_status = '1';
                                    if($social->save()){
                                        $social['token'] = $token;
                                        $url = parse_url($social->profile_image);
                                        if(isset($url['scheme'])) {
                                            if($url['scheme'] == 'https'){
                                                $social['profile_image'];
                                            }
                                        }else{
                                            $social['profile_image'] = $social->profile_image?url('/images/profile').'/'.$social->profile_image:'';
                                        }
                                        $social = GlobalHelper::removeNull($social);
                                        // $update = User::where('id', $social->id)->update(['last_active'=>date('Y-m-d H:i:s'),'is_active' => '1']);
                                        return $this->APIResponse->respondWithMessageAndPayload($social,'Login Success.');
                                    }else{
                                        return $this->APIResponse->respondUnauthorized(__('Oops Failed To Login'));
                                    }
                                // }
                            }else{
                                $checkExistsOrNot = User::where('email', '=', $data['email'])->where('social_provider',$data['social_provider'])->exists();
                                $checkExistsOrNotWithanother = User::where('email', '=', $data['email'])->where('social_provider','Normal')->exists();

                                //if($checkExistsOrNot == true){
                                if($checkExistsOrNot == true || $checkExistsOrNotWithanother){
                                    return $this->APIResponse->respondValidationError('Email already exists.');
                                }else{
                                    $password = Str::random(10);
                                    $socialnew = new User();
                                    $socialnew->email = $data['email'];
                                    $socialnew->first_name = isset($data['first_name']) ? $data['first_name'] : NULL;
                                    $socialnew->last_name = isset($data['last_name']) ? $data['last_name'] : NULL;
                                    $socialnew->device_type = $data['device_type'];
                                    $socialnew->device_token = $data['device_token'];
                                    $socialnew->social_provider_id = $data['social_provider_id'];
                                    $socialnew->social_provider = $data['social_provider'];
                                    // $socialnew->profile_image = $data['profile_image'];
                                    $socialnew->password = bcrypt($password);
                                    $socialnew->user_status = '1';
                                    if($socialnew->save()){
                                        //$update = User::where('id', $socialnew->id)->update(['user_name' => str_slug($socialnew->first_name.'-'.$socialnew->last_name,"-")."-".($socialnew->id)]);
                                        //$socialnew->roles()->attach(2);
                                         $socialnew->assignRole(2);

                                        // $credentials = ['password' => $password,'social_provider_id' => $socialnew->social_provider_id, 'social_provider_id' => $socialnew->social_provider_id];
                                        $token = null;
                                        try {
                                            if (!$token = $socialnew->createToken('LMW')->accessToken) {
                                            return $this->APIResponse->respondUnauthorized(__('invalid_parameters'));
                                            }
                                        } catch (Exception $e) {
                                            return $this->APIResponse->respondInternalError(__('failed_to_create_token'));
                                        }
                                        $socialnew = GlobalHelper::removeNull($socialnew);
                                        $socialnew['token'] = $token;
                                        $url = parse_url($socialnew->profile_image);
                                        if(isset($url['scheme'])) {
                                            if($url['scheme'] == 'https'){
                                                $socialnew['profile_image'];
                                            }
                                        }else{
                                            $socialnew['profile_image'] = $socialnew->profile_image?url('/images/profile').'/'.$socialnew->profile_image:'';
                                        }
                                        $socialnew = GlobalHelper::removeNull($socialnew);
                                        // $update = User::where('id', $socialnew->id)->update(['last_active'=>date('Y-m-d H:i:s'),'is_active' => '1']);
                                        return $this->APIResponse->respondWithMessageAndPayload($socialnew,'Registered Successfully.');
                                    }else{
                                        return $this->APIResponse->respondUnauthorized(__('Oops registration failed.'));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }catch(\Exception $e){
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

    /**
    * Developed By :
    * Description  : Forgot Password
    * Date         :
    */
    public function forgotPassword(Request $request){
        $data = $request->json()->get('data');
        try{
            if(empty($data)){
                return $this->APIResponse->respondNotFound(__('Data key not found or Empty.'));
            }else{
                $rules = array(
                    'email' => 'required|email|max:255'
                );
                $messages = [
                    'email.required' => 'Please enter Email'
                ];
                $validator = Validator::make($data,$rules,$messages);
                if($validator->fails()){
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    $checkUserExits = User::where('email',$data['email'])->first();
                    if(empty($checkUserExits)){
                        return $this->APIResponse->respondNotFound(__(Lang::get('messages.emailnotregistered')));
                    }else{

                        if(Password::sendResetLink(['email'=>$checkUserExits->email])){
                            return $this->APIResponse->respondWithMessage(Lang::get('messages.passwordrecoverylinksent'));
                        }
                        else
                        {
                            return $this->APIResponse->respondWithMessage(Lang::get('messages.pleasetryagin'));
                        }
                    }
                }
            }
        }catch(\Exception $e){
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

    /**
    * Developed By :
    * Description  : Change Password
    * Date         :
    */
    public function changePassword(Request $request){
        $data = $request->json()->get('data');
        try{
            if (empty($data)) {
                return $this->APIResponse->respondNotFound(__('Data key not found or Empty'));
            } else {
                $rules = array(
                    'old_password'=>'required',
                    'new_password'=>'required|min:5|max:15|',
                   // regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[@!\]\[\' #$%&\(\)\"<=>?\{\}_~\^\`+-\/\*])/',
                );
                $messages = [
                    'old_password.required' => 'Please enter old password',
                    'new_password.required' => 'Please enter new password',
                   // 'password.regex' => "Password must be 5-15 characters with minimum 1 Uppercase, 1 Lowercase, 1 Numeric and 1 Special character.",
                    'password.min' => "Password cannot have less than 5 characters.",
                    'password.max' => "Password cannot have more than 15 characters.",

                ];
                $validator = Validator::make($data, $rules,$messages);
                if($validator->fails()) {
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    $checkUserExits = User::where('id',$request->user()->id)->first();
                    if(empty($checkUserExits)){
                        return $this->APIResponse->respondNotFound(__(Lang::get('messages.emailnotregistered')));
                    }else{
                        if(Hash::check($data['old_password'],$checkUserExits->password)) {
                            $checkUserExits->password = bcrypt($data['new_password']);
                            if(Hash::check($data['old_password'],$checkUserExits->password)) {
                                return $this->APIResponse->respondNotFound(__("New password must not be the same as old password"));
                            }else{
                                if($checkUserExits->save()){
                                    return $this->APIResponse->respondWithMessageAndPayload($checkUserExits, Lang::get('messages.passwordchangesuccessfully'));
                                }else{
                                    return $this->APIResponse->respondInternalError(__(Lang::get('messages.pleasetryagin')));
                                }
                            }
                        } else {
                            return $this->APIResponse->respondNotFound(__("Old password doesnot match"));
                        }
                    }
                }
            }
        }catch (\Exception $e) {
          return $this->APIResponse->handleAndResponseException($e);
        }
    }

/**
	* Developed By :
	* Description  : Update device type and device token
	* Date         :
	*/
	public function updateDeviceToken(Request $request){
		$data = $request->json()->get('data');
		try{
			if(empty($data)){
				return $this->APIResponse->respondNotFound(__(trans('messages.dataKey_notFound')));
			}else {
				$rules = array(
					'device_type' => 'required|in:1,2',
                    'device_token' => 'required',
                    'device_app_type' =>'',
				);
				$messages = [
				];
				$validator = Validator::make($data, $rules,$messages);
				if($validator->fails()) {
					return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
				}else{
					$userDetail = User::where('id',Auth()->user()->id)->first();
					if(empty($userDetail)){
						return $this->APIResponse->respondNotFound(__(trans('messages.user_not_found')));
					}else{
						$userDetail->device_type = $data['device_type'];
						$userDetail->device_token = $data['device_token'];
						$userDetail->device_app_type = isset($data['device_app_type'])?$data['device_app_type']:env('APP_TYPE');
						if($userDetail->save()){
							return $this->APIResponse->respondWithMessage(trans('messages.device_token_updated'));
						}else{
							return $this->APIResponse->respondInternalError(__(trans('messages.failed_to_update')));
						}
					}
				}
			}
		}catch(\Exception $e){
			return $this->APIResponse->handleAndResponseException($e);
		}
    }

    /**
	* Developed By :
	* Description  : Update Profile
	* Date         :
	*/
	public function updateProfile(Request $request){
		// $data = $request->json()->get('data');
		$data = json_decode(preg_replace('/\s+/', ' ', trim($request->data)), true)['data'];
		try{
			$rules = array(
				"gender"=>"sometimes|nullable|in:m,f,o'",
			);
			$messages = [
			];
			$validator = Validator::make($data, $rules,$messages);
			if($validator->fails()) {
				return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
			}else{
				// get id from passport token
				$user_id = $request->user()->id;
				$user = User::find($user_id);
				if(empty($user)){
					return $this->APIResponse->respondNotFound(__(trans('messages.record_notFound_givenId')));
				}else{
					if(isset($data['first_name']) && $data['first_name'] != ''){
						$user->first_name = $data['first_name'];
                    }
                    if(isset($data['last_name']) && $data['last_name'] != ''){
						$user->last_name = $data['last_name'];
					}
					if(isset($data['dob']) && $data['dob'] != ''){
						$user->dob = date('Y-m-d', strtotime($data['dob']));
					}
					if(isset($data['user_mobile']) && $data['user_mobile'] != ''){
						$mobileNo = explode('-',$data['user_mobile']);
						$user->country_code = $data['user_mobile']?$mobileNo[0]?'+'.str_replace('+','',$mobileNo[0]):NULL:NULL;
						$user->user_mobile = $data['user_mobile']?$mobileNo[1]?str_replace(' ','',$mobileNo[1]):NULL:NULL;
					}
					if(isset($data['gender']) && $data['gender'] != ''){
						$user->gender = $data['gender'];
                    }
					if($request->hasFile('profile_image')) {
						if($user->getOriginal('profile_image') && file_exists((base_path().'/resources/uploads/profile/'.$user->getOriginal('profile_image')))){
							unlink(base_path() . '/resources/uploads/profile/'.$user->getOriginal('profile_image'));
						}
						$file = $request->file('profile_image');
						$file->getClientOriginalName();
						$fileExtension = $file->getClientOriginalExtension();
						$file->getRealPath();
						$file->getSize();
						$file->getMimeType();
						$fileName = md5(microtime(). $file->getClientOriginalName()).'.'.$fileExtension;
						$path = base_path() . '/resources/uploads/profile/';
						if(!file_exists($path)) {
							File::makeDirectory($path, 0777, true);
							chmod($path,0777);
						}
						$upload = $request->file('profile_image')->move(
							$path, $fileName
						);
						chmod($path.$fileName,0777);
						$user->profile_image = $fileName;
					}
					if ($user->save()) {
						return $this->APIResponse->respondWithMessageAndPayload($user,trans('messages.update_successfully'));
					}else{
						return $this->APIResponse->respondInternalError(__(trans('messages.failed_update_profile')));
					}
				}
			}
		}catch (\Exception $e) {
			return $this->APIResponse->handleAndResponseException($e);
		}
	}

}
