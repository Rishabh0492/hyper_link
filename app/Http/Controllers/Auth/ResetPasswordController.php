<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Input;
use App\User;
use Session;
use DB;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
     /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);
        $user->save();
    }

    public function resetPasswords($password_reset_token)
    {
        $user = User::where('password_reset_token',$password_reset_token)->firstOrFail();
        if($user) {
            return view('auth.passwords.reset', ['user' => $user]);
        } else {
            Session::put('message', 'Hello, You are not registered with us.');
            return redirect('/login');
        }
    }

    public function updatePassword(Request $request)
    {
        $rules = array(
            'token' => 'required',
            'password' => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^.()_-])[A-Za-z\d@$!%*?&#^.()_-]{8,18}$/',
            'password_confirmation' => 'required|same:password',
        );

        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        } else {
            $user = User::where('password_reset_token', $request->token)->where('email',$request->email)->first();
            if($user){
                $user->password = bcrypt($request->password);
                $user->password_reset_token = null;
                if($user->save()) {
                    Session::put('message', "Hello, Your password has been successfully updated.");
                    return redirect('/login');
                } else {
                    Session::put('message', "Hello, Some Error occured on Updating your password. Please Request again to Generate Reset Password Link. Thank you!");
                    return redirect('/login');
                }
            }else{
                Session::put('message', "Hello, Some Error occured on Updating your password. Please Request again to Generate Reset Password Link. Thank you!");
                return redirect('/login');
            }
            
        }
    }
}

