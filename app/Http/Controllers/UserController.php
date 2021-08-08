<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\UserDataTable;
use Yajra\DataTables\Html\Builder;
use App\Helper\GlobalHelper;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use Str;
use Session;
use Validator;
use App\User;
use DataTables;
use App\Chat;
use App\Country;
use App\State;
use App\City;
use Carbon;

class UserController extends Controller
{   
    function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete|user-status-change|user-view', ['only' => ['index','store']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        $this->middleware('permission:user-status-change', ['only' => ['changeStatus']]);
        $this->middleware('permission:user-view', ['only' => ['show']]);
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Builder $builder, UserDataTable $dataTable)
    {
        $html = $builder->columns([
            ['data' => 'id', 'name' => 'id','title' => 'ID'],
            ['data' => 'first_name', 'name' => 'first_name','title' => 'First Name'],
            ['data' => 'last_name', 'name' => 'last_name','title' => 'Last Name'],
            ['data' => 'email', 'name' => 'email','title' => 'Email'],
            ['data' => 'user_mobile', 'name' => 'user_mobile','title' => 'Mobile Number'],
            ['data' => 'registeredUsing', 'name' => 'registeredUsing','title' => 'RegisteredUsing'],
            ['data' => 'status', 'name' => 'status','title' => 'Status'],
            ['data' => 'created_at', 'name' => 'created_at','title' => 'Scaned At'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false,'title' => 'Action'],
        ]);
        $users = User::where('id','<>',Auth::id())->get();
       // dd($users);
        if(request()->ajax()) {
            return $dataTable->dataTable($users)->toJson();
        }
        return view('admin.users.list',compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::where("status","1")->get();

        return View('admin.users.create',compact("countries"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dt = new Carbon\Carbon();
        $before = $dt->subYears(13)->format('Y-m-d');

        $rules = array(
            'last_name' => 'required',
            'user_mobile' => 'required|numeric|unique:users,id',
            'email' => 'required|email|unique:users,id',
            'password' => 'required',
            'confirm_password' => 'required',
            'dob' => 'required|date|before:' . $before
        );
        $messages = [
            'first_name.required' => 'Please enter first name.',
            'last_name.required' => 'Please enter Last name.',
            'user_mobile.required' => 'Please enter mobile number.',
            'user_mobile.numeric' =>'Please enter at least 10 -15 digits.' ,
            'user_mobile.unique' =>'Please enter another mobile number.',
            'email.required' =>'Please enter email.',
            'email.unique' => 'Please enter valid email.',
            'password.required' => 'Password should contain number,characters,special character and atleast one capital letter.',
            'confirm_password.required' => 'Please enter same as password',
            'dob.required'=>'Please select country.',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        //dd($validator->fails());
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        } else {
            $adminUser = new User();
            $adminUser->first_name = $request['first_name'];
            $adminUser->last_name = $request['last_name'];
            $adminUser->email = $request['email'];
            $adminUser->user_mobile = $request['user_mobile'];
            $adminUser->country_code = $request['country'];
            $adminUser->dob = date('Y-m-d', strtotime($request['dob']));
            $adminUser->password = bcrypt($request['password']);
            $adminUser->user_status = '1';

            }
            if(!empty($request->main_image) || $request->main_image != ''){
                $data = explode(';', $request->main_image);
                $part = explode("/", $data[0]);
                $image = $request->main_image;  // your base64 encoded
                $image = str_replace('data:image/'.$part[1].';base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $fileName = md5(microtime().Str::random(10)) .'.'.$part[1];
                $destinationPath = base_path().'/resources/uploads/profile/';
                \File::put(base_path().'/resources/uploads/profile/' .$fileName, base64_decode($image));
                chmod($destinationPath.$fileName,0777);
                $adminUser->profile_image = $fileName;
                $image = url('/').'/resources/uploads/profile/'.$fileName;

            }else{
                $image='';
            }

            if ($adminUser->save()) {

                $adminUser->assignRole(2);
                Session::flash('message', 'User Added Succesfully !');
                Session::flash('alert-class', 'success');
                return redirect('admin/users');

            } else {
                Session::flash('message', 'Oops !! Something went wrong!');
                Session::flash('alert-class', 'error');
                return redirect('admin/users');
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('countyData','stateData','cityData')->find($id);
        if(!empty($user)){
            
            return view('admin.users.view')->with(compact('user'));
        }
        else{
            Session::flash('message', 'User not found!');
            Session::flash('alert-class', 'error');
            return redirect('admin/users');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        if(!empty($user)){
             $getCountry = Country::where("status","1")->get();
            $getCity = City::where("state_id",$user->state)->get();
            $getState = State::where("country_id",$user->country)->get();
            return view('admin.users.edit')->with(compact('user','getCountry','getState','getCity'));
        }
        else{
            Session::flash('message', 'User not found!');
            Session::flash('alert-class', 'error');
            return redirect('admin/users');
        }
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
                //dd($id);
         $rules = array(
            //'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'user_mobile' => 'required|numeric|unique:users,user_mobile,'.$id,
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'sometimes',
            'confirm_password' => 'sometimes',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            );
                $messages = [
                    'first_name.required' => 'Please enter first name.',
                    'last_name.required' => 'Please enter Last name.',
                    'user_mobile.required' => 'Please enter mobile number.',
                    'user_mobile.numeric' =>'Please enter at least 10 -15 digits.' ,
                    'user_mobile.unique' =>'Please enter another mobile number.',
                    'email.required' =>'Please enter email.',
                    'email.unique' => 'Please enter valid email.',
                    'country.required'=>'Please select country.',
                    'state.required'=>'Please select state.',
                    'city.required'=>'Please select city.',
                ];


        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        } else {

            $adminUser = User::find($id);
           $adminUser->first_name = $request['first_name'];
            $adminUser->last_name = $request['last_name'];
            $adminUser->email = $request['email'];
            $adminUser->user_mobile = $request['user_mobile'];
            $adminUser->city = $request['city'];
            $adminUser->state = $request['state'];
            $adminUser->country = $request['country'];
            $adminUser->dob = date('Y-m-d', strtotime($request['dob']));
            if($request['password']!= null || !empty($request['password'])){
                $adminUser->password = bcrypt($request['password']);
            }

            if(!empty($request->main_image) || $request->main_image != ''){
                if($adminUser->profile_image && file_exists(base_path().'/resources/uploads/profile/'.$adminUser->profile_image)){
                  unlink(base_path().'/resources/uploads/profile/'.$adminUser->profile_image); // correct
                }
                $data = explode(';', $request->main_image);
                $part = explode("/", $data[0]);
                $image = $request->main_image;  // your base64 encoded
                $image = str_replace('data:image/'.$part[1].';base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $fileName = md5(microtime().Str::random(10)) .'.'.$part[1];
                $destinationPath = base_path().'/resources/uploads/profile/';
                \File::put(base_path().'/resources/uploads/profile/' .$fileName, base64_decode($image));
                chmod($destinationPath.$fileName,0777);
                $adminUser->profile_image = $fileName;
                $image = url('/').'/resources/uploads/profile/'.$fileName;

            }else{
                $image='';
            }

            $adminUser->updated_at = date("Y-m-d H:i:s");
            if ($adminUser->save()) {

                Session::flash('message', 'User Updated Succesfully !');
                Session::flash('alert-class', 'success');
                return redirect('admin/users');
            } else {
                Session::flash('message', 'Oops !! Something went wrong!');
                Session::flash('alert-class', 'error');
                return redirect('admin/users');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(isset($id)){
            // $user = User::find($id);
            if($id==1)
                return "You can't delete Super Admin";
            $user = User::with('roles')->find($id);
            $role = (isset($user->roles)&& count($user->roles))?$user->roles[0]->id:'';
            if($user->delete())
            {
                if($role){
                    $user->removeRole($role);
                }
                 return true;
            }
             else
                return 'Something went to wrong';

        }
    }
    public function changeStatus(Request $request)
    {
        return $this->UpdateStatus($request->id,User::class,'user_status');
        
    }

    public function emailExsist(Request $request){
        if(isset($request->type) && $request->type == '1'){
            // for update
            $user = User::where('id','<>',$request->id)->where('email','=',$request->email)->where('user_status','!=','-1')->first();
            if(!empty($user)){
                echo "false";
            }else{
                echo "true";
            }
        }else{
            $user = User::where('email','=',$request->email)->where('user_status','!=','-1')->first();
            if(!empty($user)){
                echo "false";
            }else{
                echo "true";
            }
        }
    }
    public function mobilenumberExsist(Request $request){
        $user = User::where('user_mobile','=',$request->user_mobile)->first();
        if(!empty($user)){
            echo "false";
        }else{
            echo "true";
        }
    }
    public function confirmEmail(Request $request){
        $str = $request->get('data');
        $user = User::where('confirmation_code','=',$str)->first();
        if($user){
            $user->user_status = '1';
            $user->confirmation_code = null;
            $user->email_verified_at = date("y-m-d h:i:s");
            $user->email_verified = "1";
            $user->save();
            if(isset($_SERVER['HTTP_USER_AGENT']) and !empty($_SERVER['HTTP_USER_AGENT'])){
                $user_ag = $_SERVER['HTTP_USER_AGENT'];
                if(preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis',$user_ag)){
                    header("Status: 301 Moved Permanently");
                    header(env( 'MOBILE_APP_CALLBACKURL' ));
                }else{
                    Session::flash('message', 'Your email has been verified successfully.');
                    Session::flash('alert-class', 'success');
                    return redirect('/login');
                }
            }else{
                Session::flash('message', 'Your email has been verified successfully.');
                Session::flash('alert-class', 'success');
                return redirect('/login');
            }
        }else{
            Session::flash('message', 'Oops !! Link is expired.');
            Session::flash('alert-class', 'danger');
            return redirect('/login');
        }
    }

    public function usersChat($id)
    {
        $userRole = User::where('id',$id)
                        ->first();
        if($userRole){
            $result = User::where('id',$id)->first();
            if($result){
                $receptorUser = User::where('id', '=', $id)->first();
                if($receptorUser == null) {
                    abort(404);
                    // return view('common.chat.nousernamefinded', compact('userName'));
                }else {
                    // $users = User::where('id', '!=', Auth::user()->id)->take(10)->get();
                    $users = Chat::where(function ($query) {
                                $query->where('user_id1', Auth::user()->id);
                            })
                            ->orWhere(function ($query) {
                                $query->where('user_id2', Auth::user()->id);
                            })->with('userOneData','userTwoData')
                            ->orderBy('updated_at','desc')
                            ->get();
                    $chat = $this->hasChatWith($receptorUser->id);
                    return view('admin.users.chat', compact('receptorUser', 'chat', 'users'));
                }
            }else{
                return redirect()->back();
            }
        }else{
            abort(404);
        }
    }

    public function hasChatWith($userId)
    {
        $chat = Chat::where('user_id1', Auth::user()->id)
            ->where('user_id2', $userId)
            ->orWhere('user_id1', $userId)
            ->where('user_id2', Auth::user()->id)
            ->get();
        if(!$chat->isEmpty()){
          $updateChat = Chat::find($chat[0]['id']);
          if(Auth::user()->id == $updateChat->user_id1){
            $updateChat->user_id1_read="2";
            $updateChat->user_id1_unread_count = '0';
          }
          if(Auth::user()->id == $updateChat->user_id2){
            $updateChat->user_id2_read="2";
            $updateChat->user_id2_unread_count = '0';
          }
          $updateChat->save();
          return $chat->first();
        }else{
            return $this->createChat(Auth::user()->id, $userId);;
        }
    }

    public function adminUsersChat($id){
        
        $receptorUser = User::where('id', '=', $id)->first();
        if($receptorUser == null) {
            return view('common.chat.chat', compact('userName'));
        }else {
            $users = User::where('id', '!=', Auth::user()->id)->take(10)->get();
            $chat = $this->hasChatWith($receptorUser->id);
            return view('common.chat.chat', compact('receptorUser', 'chat', 'users'));
        }
    }

    public function createChat($userId1, $userId2)
    {
        $chat = Chat::create([
            'user_id1' => $userId1,
            'user_id2' => $userId2
        ]);
        return $chat;
    }

    public function updateMessageMysql()
    {
        $apidata = request();
        $updateChat = Chat::find($apidata['chatId']);
        $updateChat->message=$apidata['textmessage']?$apidata['textmessage']:NULL;
        if($apidata['userId'] == $updateChat->user_id1){
          $updateChat->user_id1_read= "2";
          $updateChat->user_id2_read= "1";
          $updateChat->user_id2_unread_count = ($updateChat['user_id2_unread_count'] + 1);
        }else{
          $updateChat->user_id1_read="1";
          $updateChat->user_id2_read="2";
          $updateChat->user_id1_unread_count = ($updateChat['user_id1_unread_count'] + 1);
        }
        $updateChat->updated_at=date('Y-m-d H:i:s');
        $updateChat->save();

        // $user_id1 = User::where('id',$updateChat->user_id1)->first();
        // $user_id2 = User::where('id',$updateChat->user_id2)->whereNotNull('device_token')->first();
        // if($user_id1 && $user_id2){
        //     $title = $user_id1->full_name;
        //     $message = $updateChat->message?$updateChat->message:NULL;
        //     if($user_id2->device_token){
        //         $notification_type = '1';
        //         $chatDetail = Chat::where('id',$updateChat->id)->with('doctorDetail')->first();
        //         if($user_id2->device_type == 1){ // IOS
        //             $app_type = $user_id2->device_app_type?$user_id2->device_app_type:env('APP_TYPE');
        //             GlobalHelper::sendGCM($title, $message,$user_id2->device_token,$app_type,$notification_type,$chatDetail);
        //         }elseif($user_id2->device_type == 2){ // Android
        //             GlobalHelper::sendFCM($title, $message,$user_id2->device_token,$notification_type,$chatDetail);
        //         }
        //     }
        // }
    }
}
 