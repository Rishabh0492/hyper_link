<?php

namespace App\DataTables;

use App\User;
use Yajra\DataTables\Services\DataTable;
use App\Helper\GlobalHelper;

class NotificationDataTable extends DataTable
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
        ->editColumn('created_at', function($data) {
            return GlobalHelper::getFormattedDate($data->created_at);
        })
        ->editColumn('full_name', function($data) {
            if($data['userDetail']){
                return $data['userDetail']->first_name .' '.$data['userDetail']->last_name;
               // return '<a href="'. route('users.show',$data['userDetail']->id).'">'.$data['userDetail']->first_name .' '.$data['userDetail']->last_name.'</a>';
            }else{
                return '-';
            }
        })
        ->filterColumn('created_at', function($query, $keyword) {
            $sql = 'DATE_FORMAT(created_at,"%d-%M-%Y") like ?';
            $query->whereRaw($sql, ["%{$keyword}%"]);
        })
        ->rawColumns(['created_at','full_name']);
    }
    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery()->select();
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
