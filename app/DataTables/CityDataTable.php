<?php

namespace App\DataTables;

use App\City;
use Yajra\DataTables\Services\DataTable;
use App\Helper\GlobalHelper;

class CityDataTable extends DataTable
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
       ->addColumn('status', function ($city) {
            $status = $city->status;
            $id = $city->city_id;
            $class='text-danger';
            $label='Deactive';
            if($status==1)
            {
                $class='text-green';
                $label='Active';
            }
          return  '<a class="'.$class.' actStatus" id = "cat'.$id.'" data-sid="'.$id.'">'.$label.'</a>';
        })
        ->addColumn('action', function ($city) {
        $id = $city->city_id;

          return '<a class="label label-success" href="' . url('admin/city/'.$id.'/edit') . '" title="Update"><i class="fa fa-edit"></i>&nbsp</a>
          <a class="label label-danger" href="javascript:;"  title="Delete" onclick="deleteConfirm('.$id.')"><i class="fa fa-trash"></i>&nbsp</a>';
        })
        ->addColumn('state_id', function ($city) {  
          return $city->state->name;
        })

        ->rawColumns(['action','status']);
    }
    
    /**
     * Get query source of dataTable.
     *
     * @param \App\City $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(City $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('city-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
            Column::make('id'),
            Column::make('add your columns'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'City_' . date('YmdHis');
    }
}
