<?php

namespace App\DataTables;

use App\SubCategory;
use Yajra\DataTables\Services\DataTable;
use App\Helper\GlobalHelper;
use Auth;

class SubCategoryDataTable extends DataTable
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
        ->addColumn('action', function ($sub_category) {
            if(Auth::user()->can('sub-category-edit')){
                $edit = '<a class="label label-success" href="' . url('admin/sub-categories/'.$sub_category->sub_category_id.'/edit') . '"  title="View"><i class="fa fa-edit"></i>&nbsp</a>';
            }else{
                $edit = '';
            }
            if(Auth::user()->can('sub-category-delete')){
                $delete = '<a class="label label-danger" href="javascript:;"  title="Delete" onclick="deleteConfirm('.$sub_category->sub_category_id.')"><i class="fa fa-trash"></i>&nbsp</a>';
            }else{
                $delete = '';
            }
            if(Auth::user()->can('sub-category-view')){
                $view = '';
            }else{
                $view = '';
            }
            return $view.' '.$edit.' '.$delete.' ';
        })
         ->addColumn('status',  function($sub_category) {
            $status = $sub_category->status;
            $id= $sub_category->sub_category_id;
            $class='text-danger';
            $label='Deactive';
            if($status==1)
            {
                $class='text-green';
                $label='Active';
            }
            if(Auth::user()->can('sub-category-status-change')){
                return  '<a class="'.$class.' actStatus" id = "subcat'.$id.'" data-sid="'.$id.'">'.$label.'</a>';
            }else{
                return  '<a class="'.$class.'">'.$label.'</a>';
            }
        })
        ->editColumn('created_at', function($sub_category) {
            return GlobalHelper::getFormattedDate($sub_category->created_at);
        })
        ->addColumn('category', function($sub_category) {
            return $sub_category->category->name;
        })
        ->rawColumns(['status','action', 'category']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\SubCategory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SubCategory $model)
    {
        return $model->newQuery()->select('sub_category_id', 'category_id', 'name', 'status', 'created_at', 'updated_at');
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
        return [
            'sub_category_id',
            'name',
            'status',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'SubCategory_' . date('YmdHis');
    }
}
