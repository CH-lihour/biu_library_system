<?php

namespace App\DataTables;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
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
            ->addColumn('title', fn($query) => ucfirst($query->title))
            ->addColumn('publisher', fn($query) => $query->publisher?->name ?? '-')
            ->addColumn('category', fn($query) => $query->category?->name ?? '-')
            ->addColumn('authors', fn($query) => $query->authors?->pluck('full_name')->join('<br>') ?? '-')
            ->addColumn('pages', fn($query) => format_number($query->pages))
            ->editColumn('cover_image_url', fn($book) => book_preview($book->cover_image_url, $book->title))
            ->editColumn("created_at", fn($query) => format_date($query->created_at))
            ->addColumn('action', function($query) {
                return edit_button(route('books.edit', $query->id)) . ' ' .
                    delete_button(route('books.destroy', $query->id));
            })
            ->rawColumns(['cover_image_url', 'action', 'authors']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Book>
     */
    public function query(Book $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['publisher', 'category', 'authors']);

        return $query;
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
            Column::make('authors')
                ->title('Authors')
                ->searchable(false),
            Column::make('publisher')
                ->title('Publisher')
                ->searchable(false),
            Column::make('category')
                ->title('Category')
                ->searchable(false),
            Column::make('publish_year')
                ->title('Publish Year'),
            Column::make('pages')
                ->title('Page')
                ->searchable(false),
            Column::make('language')
                ->title('Language'),
            Column::make('shelf_location')
                ->title('Shelf Location'),
            Column::make('created_at')
                ->searchable(false)
                ->exportable(false),
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
