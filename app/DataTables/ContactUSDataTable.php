<?php

namespace App\DataTables;

use App\User;
use Yajra\DataTables\Services\DataTable;
use App\Helper\GlobalHelper;

class ContactUSDataTable extends DataTable
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
        ->filterColumn('created_at', function($query, $keyword) {
          $sql = 'DATE_FORMAT(created_at,"%d-%M-%Y") like ?';
          $query->whereRaw($sql, ["%{$keyword}%"]);
        })
        ->editColumn('email', function ($data) {
            return '<a href="mailto:'.$data->email.'" > '.$data->email.'</a>';
        })
        ->rawColumns(['email']);
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

}
