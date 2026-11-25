<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<User>  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('role_name', fn ($user) => '<span class="badge bg-primary">'.e($user->role_name).'</span>')
            ->editColumn('position', fn ($user) => $user->position ?? '-')
            ->editColumn('staff_status', fn ($user) => status_label($user->staff_status ?? 'active'))
            ->editColumn('last_login', fn ($user) => format_date_time($user->last_login, 'd-M-Y H:i'))
            ->addColumn('action', fn ($user) => edit_button(route('users.edit', $user->id), 'users.edit').' '.
                ($user->id === auth()->id() ? '' : delete_button(route('users.destroy', $user->id), 'users.destroy')))
            ->rawColumns(['role_name', 'staff_status', 'action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('staff', 'staff.user_id', '=', 'users.id')
            ->select(
                'users.*',
                'roles.name as role_name',
                'staff.position',
                'staff.status as staff_status'
            );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
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
            Column::make('name')->name('users.name')->title('Name'),
            Column::make('email')->name('users.email')->title('Email'),
            Column::make('role_name')->name('roles.name')->title('Role'),
            Column::make('position')->name('staff.position')->title('Position'),
            Column::make('staff_status')->name('staff.status')->title('Status'),
            Column::make('last_login')->title('Last Login')->searchable(false),
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
        return 'User_'.date('YmdHis');
    }
}
