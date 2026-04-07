<?php

namespace App\DataTables;

use App\Models\BookCopy;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BookCopyDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<BookCopy> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('title', function ($query) {
                return $query->book ? $query->title : '-';
            })
            ->editColumn("created_at", fn($query) => format_date($query->created_at))
            ->editColumn('status', function ($query) {
                return status_label($query->status);
            })
            ->addColumn('action', function($query) {
                return view('book_copies.actions', compact('query'));
            })
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<BookCopy>
     */
    public function query(BookCopy $model): QueryBuilder
    {
        $query = $model->newQuery()
                ->join('books', 'book_copies.book_id', '=', 'books.id')
                ->select('book_copies.*', 'books.title');

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('bookcopy-table')
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
            Column::make('title')->title('Book Title'),
            Column::make('barcode')->title('Barcode'),
            Column::make('status')->title('Status'),
            Column::make('created_at'),
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
        return 'BookCopy_' . date('YmdHis');
    }
}
