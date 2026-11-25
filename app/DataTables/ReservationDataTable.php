<?php

namespace App\DataTables;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ReservationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Reservation>  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('status', fn ($reservation) => status_label($reservation->displayStatus()))
            ->editColumn('created_at', fn ($reservation) => format_date_time($reservation->created_at, 'd-M-Y H:i'))
            ->editColumn('expires_at', fn ($reservation) => format_date_time($reservation->expires_at, 'd-M-Y H:i'))
            ->addColumn('action', fn ($reservation) => view('reservations.actions', compact('reservation')))
            ->rawColumns(['status', 'action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Reservation>
     */
    public function query(Reservation $model): QueryBuilder
    {
        return $model->newQuery()
            ->join('members', 'reservations.member_id', '=', 'members.id')
            ->join('books', 'reservations.book_id', '=', 'books.id')
            ->select('reservations.*', 'members.name as member_name', 'members.member_code', 'books.title as book_title', 'books.shelf_location');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('reservation-table')
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
            Column::make('created_at')->name('reservations.created_at')->title('Reserved At'),
            Column::make('member_code')->name('members.member_code')->title('Member Code'),
            Column::make('member_name')->name('members.name')->title('Member'),
            Column::make('book_title')->name('books.title')->title('Book'),
            Column::make('shelf_location')->name('books.shelf_location')->title('Shelf'),
            Column::make('status')->name('reservations.status')->title('Status'),
            Column::make('expires_at')->title('Hold Until')->searchable(false),
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
        return 'Reservation_'.date('YmdHis');
    }
}
