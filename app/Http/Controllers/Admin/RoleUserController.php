<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;
use App\DataTables\RoleUserDataTable;
use App\Role;
use App\User;
use Validator;
use Session;
use App\Http\Controllers\Controller;

class RoleUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Builder $builder, RoleUserDataTable $dataTable)
    {
        
      // DB::enableQueryLog(); dd(DB::getQueryLog());// Enable query log
        $html = $builder->columns([
            ['data' => 'user_id', 'name' => 'user_id','title' => 'User ID'],
            ['data' => 'email', 'name' => 'email','title' => 'User Email'],
            ['data' => 'role_id', 'name' => 'role_id','title' => 'Role Name'],
            ['data' => 'status', 'name' => 'status','title' => 'User Status'],
            ['data' => 'action', 'name' => 'action','title' => 'Action'],
           
        ]);
        if(request()->ajax()) {
            $all_users_with_all_their_roles  = User::whereHas('roles', function ($query){
                $query->where('auth_id', '=', '1');
            })->get();
            return $dataTable->dataTable($all_users_with_all_their_roles)->toJson();
        }
        return view('admin.roleuser.list',compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $action = 'Create';
        $roles = Role::where('auth_id','1')->get();
        return view('admin.roleuser.edit', compact(['action','roles']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $rules = array(
            'email' => 'nullable|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required',
        );
        $messages = [
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
     //   dd($validator->fails());
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        } else {
            $adminUser = new User();
            $adminUser->first_name = $request['first_name'];
            $adminUser->last_name = $request['last_name'];
            $adminUser->email = $request['email'];
            $adminUser->password = bcrypt($request['password']);
            $adminUser->user_status = '1';
           // $adminUser->mobile_verified = '1';
            $adminUser->email_verified = '1';
            //$adminUser->mobile_verified_at = date('Y-m-d H:i:s');

            if ($adminUser->save()) {
                $adminUser->assignRole($request['name']);
                Session::flash('message', 'User Added Succesfully !');
                Session::flash('alert-class', 'success');
                return redirect('admin/roleuser');

            } else {
                Session::flash('message', 'Oops !! Something went wrong!');
                Session::flash('alert-class', 'error');
                return redirect('admin/roleuser');
            }
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
        $action = 'Update';
        $roleuser = User::find($id);

        if(!empty($roleuser)){
            $role_id=$roleuser->roles->first()->id;
            $roles = Role::where('auth_id','1')->get();
            $user = User::find($id);
            $userRole = $user->roles->pluck('name','name')->all();
            return view('admin.roleuser.edit', compact(['action','roles','roleuser','role_id','user','userRole']));
        }
        else{
            abort(404);
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
        $rules = array(
            'email' => 'nullable|email|unique:users,email,'.$id.',id',
            'password' => 'sometimes',
            'confirm_password' => 'sometimes',
        );
        $messages = [
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
       // dd($validator->fails());
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        } else {
            $adminUser = User::find($id);
            $adminUser->first_name = $request['first_name'];
            $adminUser->last_name = $request['last_name'];
            $adminUser->email = $request['email'];
            if($request['password']!= null || !empty($request['password'])){
                $adminUser->password = bcrypt($request['password']);
            }
            
            $adminUser->updated_at = date("Y-m-d H:i:s");
            $role = $adminUser->roles->toArray();
            if($role[0]['id']!=$request['name']){
                    $adminUser->removeRole($role[0]['id']);
                    $adminUser->assignRole($request['name']);
                }
          //  $role = Role::find($adminUser->roles);      
           // $role->role_id = $request['name'];
            if ($adminUser->save()) {
                Session::flash('message', 'User Updated Succesfully !');
                Session::flash('alert-class', 'success');

                return redirect('admin/roleuser');
            } else {
                Session::flash('message', 'Oops !! Something went wrong!');
                Session::flash('alert-class', 'error');
                return redirect('admin/roleuser');
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
        if($id==1)
                return "You can't delete Super Admin";
            $user = User::with('roles')->find($id);
            $role = $user->roles[0]->id;
            if($user->delete())
            {
                $user->removeRole($role);
                 return true;
            }
             else
                return 'Something went to wrong';

        }
    }
}
