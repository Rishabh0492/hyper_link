<?php

namespace App\DataTables;

use App\User;
use Yajra\DataTables\Services\DataTable;
use App\Helper\GlobalHelper;
use Auth;

class BannerDataTable extends DataTable
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
        ->addColumn('action', function ($data) {
             $id = $data->banner_id;
            if(Auth::user()->can('banner-edit')){
                $edit = '<a class="label label-success" href="' . route('banner.edit',$id) . '"  title="Update"><i class="fa fa-edit"></i>&nbsp</a>';
            }else{
                $edit = '';
            }
            if(Auth::user()->can('banner-delete')){
                $delete = '<a class="label label-danger" href="javascript:;"  title="Delete" onclick="deleteConfirm('.$id.')"><i class="fa fa-trash"></i>&nbsp</a>';
            }else{
                $delete = '';
            }
            if(Auth::user()->can('banner-view')){
                $view = '<a class="label label-primary" href="'. route('banner.show',$id).'"  title="View"><i class="fa fa-eye"></i>&nbsp</a>';
            }else{
                $view = '';
            }
            return $view.' '.$edit.' '.$delete.' ';
            })
        ->addColumn('status',  function($data) {
            $id = $data->banner_id;
            $status = $data->status;
            $class='text-danger';
            $label='Deactive';
            if($status==1)
            {
                $class='text-green';
                $label='Active';
            }
            if(Auth::user()->can('banner-status-change')){
                return  '<a class="'.$class.' actStatus" id = "user'.$id.'" data-sid="'.$id.'">'.$label.'</a>';
            }else{
                return  '<a class="'.$class.'">'.$label.'</a>';
            }
        })
      
        ->editColumn('created_at', function($data) {
            return GlobalHelper::getFormattedDate($data->created_at);
        })
        ->rawColumns(['status','action']);//->toJson();
    }
    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery()->select('id', 'first_name', 'last_name', 'email','user_mobile','social_provider', 'user_status', 'created_at', 'updated_at');
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
                   ->addAction(['width' => '80px'])
                    ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return ['id', 'first_name', 'last_name', 'email','user_mobile','social_provider', 'user_status', 'created_at', 'updated_at'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'User_' . date('YmdHis');
    }
}
