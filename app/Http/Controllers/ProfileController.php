<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Validator;
use Session;
use App\User;
use Zxing\QrReader;
class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = Auth::user();

        return view('admin.profile')->with(compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        // Update Password
        if(isset($request['old_password']) && $request['old_password']!='')
        {
            $rules = array(
                'old_password'  => 'required',
                'new_password'  => 'required',
                'confirm_password'  => 'required|same:new_password',
            );
            $messages = [];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            } else {
                $user = User::find($id);
                if (Hash::check($request['old_password'], $user->password)) {
                    $user->password = bcrypt($request['new_password']);
                    $user->save();
                    //Auth::logout();
                    Session::flash('message', 'Password updated successfully!!');
                    Session::flash('alert-class', 'success');
                    return redirect('admin/profile');
                } else {
                    Session::flash('message', 'Oops !! current password is wrong, please try again.');
                    Session::flash('alert-class', 'error');
                    return redirect('admin/profile');
                }
            }
        }
        else {
        // update user details
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'profile_image' => 'nullable|image|max:500000',
                'user_mobile' => 'required|numeric|unique:users,user_mobile,'.$id.',id',
            );
            $messages = [];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
            } else {
                $editUser = User::find($id);
                $editUser->first_name = $request['first_name'];
                $editUser->last_name = $request['last_name'];
                $editUser->user_mobile = $request['user_mobile'];
                $editUser->updated_at = date("Y-m-d H:i:s");
                if(!empty($request['profile_image']) || $request['profile_image'] != null){
                    if($editUser->getOriginal('profile_image') && file_exists(base_path().'/resources/uploads/profile/'.$editUser->getOriginal('profile_image'))){
                        unlink(base_path().'/resources/uploads/profile/'.$editUser->getOriginal('profile_image')); // correct
                    }
                    $file = $request->file('profile_image');
                    $file->getClientOriginalName();   // Get File Name
                    $fileExtension = $file->getClientOriginalExtension();  // Get File Extension
                    $file->getRealPath(); // Get File Real Path
                    $file->getSize();   // Get File Size
                    $file->getMimeType();  // Get File Mime Type
                    $fileName = md5(microtime() . $file->getClientOriginalName()) . "." . $fileExtension; // Rename file name
                    $destinationPath = base_path().'/resources/uploads/profile/';
                    $file->move($destinationPath, $fileName);
                    $editUser->profile_image = $fileName;
                }
                if(!empty($request['profile_image_snapshot']) || $request['profile_image_snapshot'] != null){
                    $destinationPath = base_path().'/resources/uploads/profile/';
                    $img = $request['profile_image_snapshot'];
                    $image_parts = explode(";base64,", $img);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = md5(microtime()) . ".png";
                    $file = $destinationPath . $fileName;
                    file_put_contents($file, $image_base64);
                    $editUser->profile_image = $fileName;
                }

                if ($editUser->save()) {
                    Session::flash('message', 'Profile information updated !!');
                    Session::flash('alert-class', 'success');
                    return redirect('admin/profile');
                } else {
                    Session::flash('message', 'Oops !! Something went wrong please try again after some times');
                    Session::flash('alert-class', 'error');
                    return redirect('admin/profile');
                }
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
        //
    }

    public function qrcodeScan(Request $request)
    {
      if(!empty($request['qrcode_image']) || $request['qrcode_image'] != null){
          $file = $request->file('qrcode_image');
          $file->getClientOriginalName();   // Get File Name
          $fileExtension = $file->getClientOriginalExtension();  // Get File Extension
          $file->getRealPath(); // Get File Real Path
          $file->getSize();   // Get File Size
          $file->getMimeType();  // Get File Mime Type
          $fileName = md5(microtime() . $file->getClientOriginalName()) . "." . $fileExtension; // Rename file name
          $destinationPath = base_path().'/resources/uploads/qrcode/';
          if(!file_exists($destinationPath)) {
              File::makeDirectory($destinationPath, 0777, true);
              chmod($destinationPath,0777);
          }
          $file->move($destinationPath, $fileName);
          chmod($destinationPath.$fileName,0777);
          $file = $destinationPath.$fileName;
          $qrcode = new QrReader(url('resources/uploads/qrcode/').'/'.$fileName);
          $text = $qrcode->text();
          return $text;
      } else {
        return "";
      }
    }
}
