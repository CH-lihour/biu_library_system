<?php

namespace App\DataTables;

use App\Models\Author;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AuthorDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Author> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action' , function($query) {
                    return edit_button(route('authors.edit', $query->id)) . ' ' .
                    delete_button(route('authors.destroy', $query->id));;
                }
            )
            ->addColumn('fullname', function($query) {
                return ucfirst($query->firstname) . ' ' . ucfirst($query->lastname);
            })
            ->filterColumn('fullname', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('firstname', 'like', "%{$keyword}%")
                        ->orWhere('lastname', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('fullname', function ($query, $order) {
                $query->orderBy('firstname', $order)
                    ->orderBy('lastname', $order);
            })
            ->editColumn("bio", fn($query) => $query->bio ?? '-')
            ->editColumn("created_at", fn($query) => format_date($query->created_at))
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Author>
     */
    public function query(Author $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('author-table')
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
            Column::make('fullname')->title('Full Name'),
            Column::make('email')->title('Email'),
            Column::make('bio')->title('Bio'),
            Column::make('created_at')->title('Created At'),
            Column::computed('action')
                ->title('Action')
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
        return 'Author_' . date('YmdHis');
    }
}
