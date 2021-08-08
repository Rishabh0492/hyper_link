<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Session;
use Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated($request, $user)
    { 
        //$userRoles= Auth::user()->roles()->pluck('name');//->pluck('user_status');
        $userStatus= User::find($request->user()->id)->first()->user_status;
        if($userStatus==1)
        {
            if (AUth::user()->hasRole('admin')) {
                return redirect('admin/dashboard');
            }
            else
                return redirect('/');
        }
        return redirect()->intended('/');
    }
    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        // Load user from database
        $user = \App\User::where($this->username(), $request->{$this->username()})->first();
        if(!empty($user)){
        // Check if user was successfully loaded, that the password matches
        // and active is not 1. If so, override the default error message.
        if($user->user_status==-1)
           $errors = [$this->username() => "Your account  doesn't exist"];
       else{
            if ($user && \Hash::check($request->password, $user->password) && $user->user_status != 1) {
                $errors = [$this->username() => 'Your account has been deactivated please contact us for reactivation'];
            }elseif($user && !\Hash::check($request->password, $user->password)){
                $errors = ['password' => 'Please enter valid password.'];
            }elseif(!$user){
                $errors = ['email' => 'Please enter valid email.'];
            }
        }
    }
        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }
    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['user_status'] = '1';
        return $credentials;
    }

    public function login(Request $request) {
     $this->validateLogin($request);
    // If the class is using the ThrottlesLogins trait, we can automatically throttle
    // the login attempts for this application. We'll key this by the username and
    // the IP address of the client making these requests into this application.
    if ($this->hasTooManyLoginAttempts($request)) {
        $this->fireLockoutEvent($request);
        return $this->sendLockoutResponse($request);
    }

    // This section is the only change
    if ($this->guard()->validate($this->credentials($request))) {
        $user = $this->guard()->getLastAttempted();
        $remember = $request->input('remember');

        // Make sure the user is active
        if ($user->user_status && $this->attemptLogin($request)) {
          
            // Send the normal successful login response
            return $this->sendLoginResponse($request);
        } else {
            // Increment the failed login attempts and redirect back to the
            // login form with an error message.
            $this->incrementLoginAttempts($request);
            return redirect()
                ->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors(['error' => 'You must be active to login.']);
        }
    }

    // If the login attempt was unsuccessful we will increment the number of attempts
    // to login and redirect the user back to the login form. Of course, when this
    // user surpasses their maximum number of attempts they will get locked out.
    $this->incrementLoginAttempts($request);

    return $this->sendFailedLoginResponse($request);
    }

    /**
    * Show the application's login form.
    *
    * @return \Illuminate\Http\Response
    */
    public function showLoginForm()
    {
        // Get URLs
        $urlPrevious = url()->previous();
        $urlBase = url()->to('/');

        // Set the previous url that we came from to redirect to after successful login but only if is internal
        if(($urlPrevious != $urlBase . '/login') && (substr($urlPrevious, 0, strlen($urlBase)) === $urlBase)) {
            session()->put('url.intended', $urlPrevious);
    }

     return view('auth.login');
    }

    // Google social login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        if ($user->email != "") {
            $authUser = User::where('google_id', $user->id)
                            ->orWhere('social_provider_id', $user->id)
                            ->first();
            //if user already registerd
            if ($authUser) {
                Auth::login($authUser, true);
                Session::flash('message', 'Login success');
                Session::flash('alert-class', 'success');
                return redirect()->intended('/');
            }else {
                //if user not registerd
                $emailExsist = User::where('email', $user->email)->first();
                if ($emailExsist) {
                    if($emailExsist->user_status == '0'){
                        Session::flash('message', 'Your Google registered email already exist. Your account is deactivated please contact admin to reactivate.');
                        Session::flash('alert-class', 'error');
                        return redirect('/login');
                    }else{
                        $emailExsist->social_provider_id = $user->id;
                        $emailExsist->google_id = $user->id;
                        $emailExsist->social_provider = 'google';
                        if($emailExsist->save()){
                            Auth::login($emailExsist, true);
                            Session::flash('message', 'Login success');
                            Session::flash('alert-class', 'success');
                            return redirect()->intended('/');
                        }else{
                            Session::flash('message', 'Oops !! Something went wrong!');
                            Session::flash('alert-class', 'error');
                            return redirect('/login');
                        }
                    }
                } else {
                    $newUser = new User();
                    $newUser->first_name = $user->user['given_name'];
                    $newUser->last_name = $user->user['family_name'];
                    $newUser->email = $user->email;
                    $newUser->user_status = '1';
                    $newUser->social_provider_id = $user->id;
                    $newUser->google_id = $user->id;
                    $newUser->social_provider = 'google';
                    $newUser->password = Hash::make(Str::random(8));
                    if ($newUser->save()) {
                        $newUser->roles()->attach(2);
                        Auth::login($newUser, true);
                        Session::flash('message', 'Login success');
                        Session::flash('alert-class', 'success');
                        return redirect()->intended('/');
                    } else {
                        Session::flash('message', 'Oops !! Something went wrong!');
                        Session::flash('alert-class', 'error');
                        return redirect('/login');
                    }
                }
            }
        } else {
            Session::flash('message', 'Oops !! Something went wrong. Please try again later');
            Session::flash('alert-class', 'error');
            return redirect('/login');
        }
    }

    // facebook social login
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $user = Socialite::driver('facebook')->user();
        $authUser = User::where('facebook_id', $user->user['id'])
                        ->orWhere('social_provider_id', $user->user['id'])
                        ->first();
        //if user already registerd
        if ($authUser) {
            Auth::login($authUser, true);
            Session::flash('message', 'Login success');
            Session::flash('alert-class', 'success');
            return redirect()->intended('/');
        }else {
            //if user not registerd
            if((isset($user->user['email'])) && ($user->user['email'] != null)){
                $emailExsist = User::where('email', $user->user['email'])->first();
                if ($emailExsist) {
                    if($emailExsist->user_status == '0'){
                        Session::flash('message', 'Your Facebook registered email already exist. Your account is deactivated please contact admin to reactivate.');
                        Session::flash('alert-class', 'error');
                        return redirect('/login');
                    }else{
                        $emailExsist->social_provider_id = $user->user['id'];
                        $emailExsist->facebook_id = $user->user['id'];
                        $emailExsist->social_provider = 'facebook';
                        if($emailExsist->save()){
                            Auth::login($emailExsist, true);
                            Session::flash('message', 'Login success');
                            Session::flash('alert-class', 'success');
                            return redirect()->intended('/');
                        }else{
                            Session::flash('message', 'Oops !! Something went wrong!');
                            Session::flash('alert-class', 'error');
                            return redirect('/login');
                        }
                    }
                } else {
                    $pieces = explode(" ", $user->name);
                    $newUser = new User();
                    $newUser->first_name = $pieces[0];
                    $newUser->last_name = $pieces[1]?$pieces[1]:NULL;
                    $newUser->email = $user->user['email'];
                    $newUser->user_status = '1';
                    $newUser->social_provider_id = $user->user['id'];
                    $newUser->facebook_id = $user->user['id'];
                    $newUser->social_provider = 'facebook';
                    $newUser->password = Hash::make(Str::random(8));
                    if ($newUser->save()) {
                        $newUser->roles()->attach(2);
                        Auth::login($newUser, true);
                        Session::flash('message', 'Login success');
                        Session::flash('alert-class', 'success');
                        return redirect()->intended('/');
                    } else {
                        Session::flash('message', 'Oops !! Something went wrong!');
                        Session::flash('alert-class', 'error');
                        return redirect('/login');
                    }
                }
            }else{
                $emailExsist = User::where('facebook_id', $user->user['id'])
                                    ->orWhere('social_provider_id', $user->user['id'])
                                    ->first();
                if ($emailExsist) {
                    Session::flash('message', 'Your Facebook registered email already exist. Please try to login with same email address.');
                    Session::flash('alert-class', 'error');
                    return redirect('/login');
                } else {
                    $pieces = explode(" ", $user->name);
                    $newUser = new User();
                    $newUser->first_name = $pieces[0];
                    $newUser->last_name = $pieces[1]?$pieces[1]:NULL;
                    $newUser->email = isset($user->user['email'])?$user->user['email']:NULL;
                    $newUser->user_status = '1';
                    $newUser->social_provider_id = $user->user['id'];
                    $newUser->facebook_id = $user->user['id'];
                    $newUser->social_provider = 'facebook';
                    $newUser->password = Hash::make(Str::random(8));
                    if ($newUser->save()) {
                        $newUser->roles()->attach(2);
                        Auth::login($newUser, true);
                        Session::flash('message', 'Login success');
                        Session::flash('alert-class', 'success');
                        return redirect()->intended('/');
                    } else {
                        Session::flash('message', 'Oops !! Something went wrong!');
                        Session::flash('alert-class', 'error');
                        return redirect('/login');
                    }
                }
            }
        }
    }

    // twitter social login
    public function redirectToTwitter()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function handleTwitterCallback()
    {
        $user = Socialite::driver('twitter')->user();
        if ($user->email != "") {
            $authUser = User::where('twitter_id', $user->id)
                            ->orWhere('social_provider_id', $user->id)
                            ->first();
            if ($authUser) {
                Auth::login($authUser, true);
                Session::flash('message', 'Login success');
                Session::flash('alert-class', 'success');
                return redirect()->intended('/');
            }else {
                $emailExsist = User::where('email', $user->email)->first();
                if ($emailExsist) {
                    if($emailExsist->user_status == '0'){
                        Session::flash('message', 'Your Twitter registered email already exist. Your account is deactivated please contact admin to reactivate.');
                        Session::flash('alert-class', 'error');
                        return redirect('/login');
                    }else{
                        $emailExsist->social_provider_id = $user->id;
                        $emailExsist->twitter_id = $user->id;
                        $emailExsist->social_provider = 'twitter';
                        if($emailExsist->save()){
                            Auth::login($emailExsist, true);
                            Session::flash('message', 'Login success');
                            Session::flash('alert-class', 'success');
                            return redirect()->intended('/');
                        }else{
                            Session::flash('message', 'Oops !! Something went wrong!');
                            Session::flash('alert-class', 'error');
                            return redirect('/login');
                        }
                    }
                } else {
                    $pieces = explode(" ", $user->name);
                    $newUser = new User();
                    $newUser->first_name = $pieces[0];
                    $newUser->last_name = $pieces[1]?$pieces[1]:NULL;
                    $newUser->email = $user->email;
                    $newUser->user_status = '1';
                    $newUser->social_provider_id = $user->id;
                    $newUser->twitter_id = $user->id;
                    $newUser->social_provider = 'twitter';
                    $newUser->password = Hash::make(Str::random(8));
                    if ($newUser->save()) {
                        $newUser->roles()->attach(2);
                        Auth::login($newUser, true);
                        Session::flash('message', 'Login success');
                        Session::flash('alert-class', 'success');
                        return redirect()->intended('/');
                    } else {
                        Session::flash('message', 'Oops !! Something went wrong!');
                        Session::flash('alert-class', 'error');
                        return redirect('/login');
                    }
                }
            }
        } else {
            Session::flash('message', 'Oops !! Something went wrong. Please try again later');
            Session::flash('alert-class', 'error');
            return redirect('/login');
        }
    }
}
