<?php

namespace App\DataTables;

use App\FAQ;
use App\Helper\GlobalHelper;
use Yajra\DataTables\Services\DataTable;
use DataTables;

class FAQDataTable extends DataTable
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
			->addColumn('action', function ($faq) {
				$id=$faq->faq_id;
				return '
				<a class="label label-success" href="' . url('admin/faq/'.$id.'/edit') . '"  title="View"><i class="fa fa-edit"></i>&nbsp</a>
				<a class="label label-danger" href="javascript:;"  title="Delete" onclick="deleteConfirm('.$id.')"><i class="fa fa-trash"></i>&nbsp</a>';
			})
			/*->addColumn('status',  function($faq) {
				$id=$faq->faq_id;
				$status = $faq->status;
				$class='text-danger';
				$label='Deactive';
				if($status==1)
				{
					$class='text-green';
					$label='Active';
				}
			  return  '<a class="'.$class.' actStatus" id = "faq'.$id.'" data-sid="'.$id.'">'.$label.'</a>';
			})*/
			->editColumn('created_at', function($faq) {
				return GlobalHelper::getFormattedDate($faq->created_at);
			})
			->rawColumns(['status','action']);//->toJson();
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\FAQ $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(FAQ $model)
    {
        return $model->newQuery()->select('faq_id', 'question', 'answer', 'created_at', 'updated_at');
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
        return ['faq_id', 'question', 'answer', 'created_at', 'updated_at'];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'FAQ_' . date('YmdHis');
    }
}
