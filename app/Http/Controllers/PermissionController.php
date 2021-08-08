<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Support\Facades\Input;
use App\Helper\GlobalHelper;
use Auth;
use Validator;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;
use App\PermissionRole;

class PermissionController extends Controller
{
    public function index()
    {
        $roles = Role::where("auth_id","1")->get();
        $permissions = Permission::select("category")->groupBy("category")->get();
        // dd($permissions);
        return view('admin.permission.index', compact('roles', 'permissions'));
    }
    public function store(Request $request)
    {
        if(isset($request->permissions)){
          $previous_permissions = PermissionRole::where('role_id',$request->roles)->delete();
          foreach ($request->permissions as $key => $value) {
            $new_permission = new PermissionRole();
            $new_permission->permission_id = $value;
            $new_permission->role_id = $request->roles;
            $new_permission->save();
          }
          Session::flash('message', 'Permission updated !!');
          Session::flash('alert-class', 'success');
          return redirect()->back();
        }else{
          $previous_permissions = PermissionRole::where('role_id',$request->roles)->delete();
          Session::flash('message', 'Permission updated !!');
          Session::flash('alert-class', 'success');
          return redirect()->back();
        }
    }
    public function getPermissions(Request $request)
    {
        $getPermissions = PermissionRole::where("role_id",$request->id)->get()->pluck('permission_id');
        if($getPermissions){
            echo $getPermissions;
        }else{
            echo "0";
        }
    }

}
