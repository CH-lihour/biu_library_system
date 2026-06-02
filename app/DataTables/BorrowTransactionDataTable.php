<?php

namespace App\DataTables;

use App\Models\BorrowTransaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BorrowTransactionDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<BorrowTransaction> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('book_cover_image', function($row){
                return book_preview($row->book_cover_image);
            })
            ->editColumn('borrow_date', function($row){
                return status_badge(format_date($row->borrow_date), true);
            })
            ->editColumn('due_date', function($row){
                return status_badge(format_date($row->due_date), false);
            })
            ->editColumn('return_date', function($row){
                return $row->return_date ? status_badge(format_date($row->return_date), true) : status_badge('Not Yet Returned', false);
            })
            ->editColumn('created_at', function($row){
                return format_date($row->created_at);
            })
            ->addColumn('action', function($row){
                return view('borrows.actions', compact('row'));
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'book_cover_image', 'borrow_date', 'due_date', 'return_date']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<BorrowTransaction>
     */
    public function query(BorrowTransaction $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->join('members', 'borrow_transactions.member_id', '=', 'members.id')
            ->join('book_copies', 'borrow_transactions.book_copy_id', '=', 'book_copies.id')
            ->join('books', 'book_copies.book_id', '=', 'books.id')
            ->join('users', 'borrow_transactions.staff_id', '=', 'users.id')
            ->select(
                'borrow_transactions.*',
                'members.name as member_name',
                'books.title as book_title',
                'users.name as handle_by',
                'books.cover_image_url as book_cover_image',
                'book_copies.barcode as book_barcode'
            );

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('booktransaction-table')
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
            Column::make('book_cover_image')
                ->title('Cover Image')
                ->addClass('text-center'),
            Column::make('member_name')->title('Member Name'),
            Column::make('book_title')->title('Book Title'),
            Column::make('book_barcode')->title('Book Barcode'),
            Column::make('borrow_date')->title('Borrow Date'),
            Column::make('due_date')->title('Due Date'),
            Column::make('return_date')->title('Returned Date'),
            Column::make('handle_by')->title('Handled By'),
            Column::make('created_at')->title('Created At'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BookTransaction_' . date('YmdHis');
    }
}
