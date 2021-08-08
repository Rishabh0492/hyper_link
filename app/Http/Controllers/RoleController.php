<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Html\Builder;
use App\DataTables\RoleDataTable;
use App\DataTables\RoleUserDataTable;
use App\User;
use Session;
use Validator;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Builder $builder, RoleDataTable $dataTable)
    {
        $html = $builder->columns([
            ['data' => 'id', 'name' => 'id','title' => 'ID'],
            ['data' => 'name', 'name' => 'name','title' => 'Name'],
            ['data' => 'guard_name', 'name' => 'guard_name','title' => 'Guard Name'],
            ['data' => 'created_at', 'name' => 'created_at','title' => 'Scaned At'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false,'title' => 'Action'],
        ]);

        if(request()->ajax()) {
        	$roles = Role::where('auth_id','1')->get();
            //dd($users);
            return $dataTable->dataTable($roles)->toJson();
        }

        return view('admin.roles.list', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $action = 'Create';
        return view('admin.roles.edit', compact('action'));
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
            //'name' => 'required|unique:roles,name,NULL,id,auth_id,'.\Auth::id().'',
            'name' => 'required|unique:roles,name,NULL,id,auth_id,1',
        );
        $messages = [
            'name.required'=>'Please enter role name.',
            'name.unique'=>'Role Must be Unique.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
            //dd($validator->fails());
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        } else {
            $role = Role::create(['name' => $request['name'],'auth_id'=>'1']);
            if ($role) {
                Session::flash('message', 'Role Added Succesfully !');
                Session::flash('alert-class', 'success');
                return redirect('admin/roles');

            } else {
                Session::flash('message', 'Oops !! Something went wrong!');
                Session::flash('alert-class', 'error');
                return redirect('admin/roles');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Builder $builder, RoleUserDataTable $dataTable)
    {
    		/*$roles =  User::role($id)->first();

    	print_r($roles->getRoleNames()->toArray());exit;*/
        $html = $builder->columns([
            ['data' => 'user_id', 'name' => 'user_id','title' => 'User ID'],
            ['data' => 'email', 'name' => 'email','title' => 'User Email'],
            ['data' => 'role_id', 'name' => 'role_id','title' => 'Role Name'],
            ['data' => 'status', 'name' => 'status','title' => 'User Status'],
            ['data' => 'action', 'name' => 'action','title' => 'Action'],
           
        ]);
        if(request()->ajax()) {
    		$roles =  User::role($id)->get();
        	return $dataTable->dataTable($roles)->toJson();
        }
        return view('admin.roleuser.list',compact('html'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::where(['auth_id'=>'1','id'=>$id])->first();
        if(!empty($role)){
            $action = 'Update';

            return view('admin.roles.edit')->with(compact('role','action'));
        }
        else{
            Session::flash('message', 'Role not found');
            Session::flash('alert-class', 'error');
            return redirect('admin/roles');

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
            //'name' => 'required|unique:roles,name,'.$id.',id,auth_id,'.\Auth::id().'',
            'name' => 'required|unique:roles,name,'.$id.',id,auth_id,1',
        );
        $messages = [
            'name.required'=>'Please enter role name.',
            'name.unique'=>'Role Must be Unique.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        //dd($validator->fails());
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        } else {

            $role = Role::find($id);
            $guard_name = ($request['guard_name'] !=null) ? $request['guard_name'] : 'web';
            $role->name = $request['name'];
            $role->guard_name = $guard_name;

            if ($role->save()) {
                Session::flash('message', 'Role Updated Succesfully !');
                Session::flash('alert-class', 'success');

                return redirect('admin/roles');
            } else {
                Session::flash('message', 'Oops !! Something went wrong!');
                Session::flash('alert-class', 'error');
                return redirect('admin/roles');
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
        	$role = Role::where('id',$id)->first();
	        $users = User::whereHas('roles', function ($query) use($role){
	           $query->where('name', '=', $role->name);
	         })->get();
	         if ($users->count() > 0) {
	           foreach ($users as $key => $user) {
	             $user->delete();
	           }
	         }
            if($role->delete()) { 
                 return true;
             }
             else
                return 'Something went to wrong';

        }
    }
}
