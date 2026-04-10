<?php

namespace App\DataTables;

use App\Models\Member;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MemberDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Member> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('plan_name', function($query){
                return $query->plan_name . ' (' . ($query->loan_duration_days ?? '-') . ' days)';
            })
            ->editColumn('status', function($query){
                return status_label($query->status);
            })
            ->editColumn('address', function($query){
                return $query->address ?? '-';
            })
            ->editColumn('join_date', function($query){
                return $query->join_date ? format_date($query->join_date) : '';
            })
            ->editColumn('expiry_date', function($query){
                return $query->expiry_date ? format_date($query->expiry_date) : '';
            })
            ->editColumn('created_at', function($query){
                return $query->created_at ? format_date($query->created_at) : '';
            })
            ->addColumn('action', function($query){
                return edit_button(route('members.edit', $query->id)) . ' ' .
                    delete_button(route('members.destroy', $query->id));
            })
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Member>
     */
    public function query(Member $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->join('member_plans', 'members.plan_id', '=', 'member_plans.id')
            ->select(
                'members.*',
                'member_plans.name as plan_name',
                'member_plans.loan_duration_days',
                'member_plans.fine_per_day',
                'member_plans.discount_fee',
            );

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('member-table')
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
                ->width(40)
                ->addClass('text-center'),
            Column::make('member_code')->title('Member Code'),
            Column::make('name')->title('Name'),
            Column::make('plan_name')->title('Plan Name'),
            Column::make('status')->title('Status'),
            Column::make('join_date')->title('Join Date'),
            Column::make('expiry_date')->title('Expiry Date'),
            Column::make('email')->title('Email'),
            Column::make('phone')->title('Phone'),
            Column::make('address')->title('Address'),
            Column::make('created_at')->title('Created At'),
            Column::make('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Member_' . date('YmdHis');
    }
}
