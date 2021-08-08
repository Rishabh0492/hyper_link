<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\User;
use Illuminate\Http\Request;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userRoles= Auth::user()->roles()->pluck('name');
        $userStatus= User::select('user_status')->where('id', Auth::Id())->get();
        if($userStatus[0]->user_status == 1)
        {
           if (!$userRoles->contains('admin')) {
               return redirect('/user');
            }
        }
        else
        {
            Auth::logout();
            return redirect()->intended('/');
        }
        return $next($request);
    }
}
