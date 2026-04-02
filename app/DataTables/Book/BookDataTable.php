<?php

namespace App\DataTables\Book;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BookDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Book> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('cover_image_url', function ($book) {
                if ($book->cover_image_url) {
                    return '<img src="' . asset('storage/' . $book->cover_image_url) . '"
                                alt="' . $book->title . '"
                                style="width: 50px; height: 60px; object-fit: cover; border-radius: 4px;">';
                }
                return '<img src="' . asset('assets/img/books/no-cover.jpg') . '"
                            alt="No Cover"
                            style="width: 50px; height: 60px; object-fit: cover; border-radius: 4px;">';
            })
            ->editColumn("created_at", fn($query) => $query->created_at->format("d-M-Y"))
            ->addColumn('action', function($query) {
                return view('books.actions', compact('query'));
            })
            ->rawColumns(['cover_image_url', 'action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Book>
     */
    public function query(Book $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('book-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->parameters([
                        'responsive' => true,
                        'autoWidth'  => false,
                        'dom'        => 'Brftip',
                        'columnDefs' => [
                            [
                                'orderable' => false,
                                'targets'   => [1],
                            ],
                        ],
                    ])
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel')
                            ->text('<i class="fa fa-file-excel"></i>')
                            ->addClass('btn btn-light btn-sm'),

                        Button::make('pdf')
                            ->text('<i class="fa fa-file-pdf"></i>')
                            ->addClass('btn btn-light btn-sm'),
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('No')
                ->width(20)
                ->addClass('text-center'),
            Column::make('cover_image_url')
                ->title('Cover Image')
                ->addClass('text-center')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false),
            Column::make('title')
                ->title('title'),
            Column::make('isbn')
                ->title('Is Bn'),
            Column::make('publish_year')
                ->title('Publish Year'),
            Column::make('pages')
                ->title('Page')
                ->searchable(false),
            Column::make('language')
                ->title('Language'),
            Column::make('created_at')
                ->searchable(false),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Book_' . date('YmdHis');
    }
}
