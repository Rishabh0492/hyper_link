<?php
    
namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use App\DataTables\RoleDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use App\PermissionRole;
    
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);

        Validator::extend('unique_role_name', function ($attribute, $value, $parameters) {
            $arr = explode("*_*",$parameters[0]);
            $name = $arr[0];
            $id = $arr[1];
            $userDetail = Role::where('id','<>',$id)->where('name',$name)->first();
            return $userDetail ? false : true;
        });
    }
    
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
            ['data' => 'created_at', 'name' => 'created_at','title' => 'Created On'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false,'title' => 'Action'],
        ])->parameters([
            'order' => [0,'desc'],
            'scrollX' => 'true',
            'stateSave' => true,
        ]);
        if(request()->ajax()) {
            $result = Role::where('auth_id','1')->select();
            return $dataTable->dataTable($result)->toJson();
        }
        return view('admin.roles.list', compact(['html']));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::select("category")->groupBy("category")->where('status','1')->get();
        return view('admin.roles.create',compact('permission'));
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
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        );
        $messages = [
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        } else {
            $role = Role::create(['name' => strtolower(str_replace(' ', '_', $request->input('name'))),'auth_id'=>'1']);
            $role->syncPermissions($request->input('permission'));
            return redirect()->route('roles.index') 
                            ->with('success','Role created successfully');
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
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        $permission = Permission::select("category")->groupBy("category")->where('status','1')->get();
        return view('admin.roles.view',compact('role','rolePermissions','permission'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::select("category")->groupBy("category")->where('status','1')->get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return view('admin.roles.edit',compact('role','permission','rolePermissions'));
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
            'name' => 'required|unique_role_name:'.$request['name'].'*_*'.$id,
            'permission' => 'required',
        );
        $messages = [
            'name.unique_role_name' => 'This role is already exist',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        } else {
            $role = Role::find($id);
            $role->name = strtolower(str_replace(' ', '_', $request->input('name')));
            $role->save();
            $role->syncPermissions($request->input('permission'));
            return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
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
        if($id==1)
            return "You can't delete admin role.";
        $record = Role::find($id);
        if($record->delete()){
            return true;
        }else{
            return 'Something went to wrong!';
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