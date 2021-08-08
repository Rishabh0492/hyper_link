<?php

namespace App\DataTables;

use App\Role;
use App\Helper\GlobalHelper;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
        // ->addColumn('action', function ($role) {
        //     $id= $role->id;
        //     return '<a class="label label-primary" href="' . url('admin/roles/'.$id) . '"  title="View"><i class="fa fa-eye"></i>&nbsp</a>
        //     <a class="label label-success" href="' . url('admin/roles/'.$id.'/edit') . '"  title="Edit"><i class="fa fa-edit"></i>&nbsp</a>
        //     <a class="label label-danger" href="javascript:;"  title="Delete" onclick="deleteConfirm('.$id.')"><i class="fa fa-trash"></i>&nbsp</a>';
        // })
        ->addColumn('action', function ($role) {
            $id= $role->id;
            return '<a class="label label-primary" href="' . route('roles.show',$id) . '"  title="View"><i class="fa fa-eye"></i>&nbsp</a>
            <a class="label label-success" href="' . route('roles.edit',$id) . '"  title="Edit"><i class="fa fa-edit"></i>&nbsp</a>
            <a class="label label-danger" href="javascript:;"  title="Delete" onclick="deleteConfirm('.$id.')"><i class="fa fa-trash"></i>&nbsp</a>';
        })
       /* ->addColumn('status',  function($role) {
            $id= $role->id;
            $status = $role->status;
            $class='text-danger';
            $label='Deactive';
            if($status==1)
            {
                $class='text-green';
                $label='Active';
            }
          return  '<a class="'.$class.' actStatus" id = "cat'.$id.'" data-sid="'.$id.'">'.$label.'</a>';
        })*/
        ->editColumn('created_at', function($role) {
            return GlobalHelper::getFormattedDate($role->created_at);
        })
        ->rawColumns(['status','action']);//->toJson();
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Role $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model)
    {
        return $model->newQuery()->select('id', 'guard_name','name', 'created_at', 'updated_at');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                   ->addAction(['width' => '80px']);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return ['id', 'guard_name','name', 'created_at', 'updated_at'];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Role_' . date('YmdHis');
    }
}
