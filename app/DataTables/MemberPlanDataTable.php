<?php

namespace App\DataTables;

use App\Models\MemberPlan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MemberPlanDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<MemberPlan> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('loan_duration_days', function($query){
                return $query->loan_duration_days . ' days';
            })
            ->editColumn('fine_per_day', function($query){
                return format_currency($query->fine_per_day);
            })
            ->editColumn('discount_fee', function($query){
                return format_currency($query->discount_fee);
            })
            ->editColumn('description', function($query){
                return $query->description ?? '-';
            })
            ->editColumn('created_at', function($query){
                return format_date($query->created_at);
            })
            ->addColumn('action', function($query){
                return edit_button(route('member_plans.edit', $query->id)) . ' ' .
                    delete_button(route('member_plans.destroy', $query->id));
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<MemberPlan>
     */
    public function query(MemberPlan $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('memberplan-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('No')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('name')->title('Plan Name'),
            Column::make('loan_duration_days')->title('Loan Duration (Days)'),
            Column::make('fine_per_day')->title('Fine Per Day'),
            Column::make('discount_fee')->title('Discount Fee'),
            Column::make('description')->title('Description'),
            Column::make('created_at')->title('Created At'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'MemberPlan_' . date('YmdHis');
    }
}
